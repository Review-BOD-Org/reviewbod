import logging
from langchain_community.chat_models import ChatOpenAI
from langchain.prompts import PromptTemplate
from langchain.chains import LLMChain
from langchain.schema import HumanMessage
from langchain_community.utilities import SQLDatabase
import asyncio
import os
import json
from fastapi import FastAPI, WebSocket, WebSocketDisconnect
from fastapi.middleware.cors import CORSMiddleware
import socketio
import pymysql
import time
import sqlparse
import traceback
import sys
import re
import datetime
from nacl.secret import SecretBox
from nacl.exceptions import CryptoError
import base64
import uuid

class PrintToLogger:
    def write(self, message):
        if message.strip() != "":
            logging.info(message.strip())
    def flush(self):
        pass


def ensure_sqlalchemy_connection():
    """Refresh SQLAlchemy connection pool"""
    global db
    try:
        # Test the connection
        db.run("SELECT 1")
    except Exception as e:
        logger.info(f"SQLAlchemy connection failed, recreating: {str(e)}")
        # Recreate the SQLAlchemy database connection
        db = SQLDatabase.from_uri(
            "mysql+pymysql://root:50465550@127.0.0.1:3306/db?pool_pre_ping=True&pool_recycle=3600",
            include_tables=["tasks", "projects", "teams", "platform_users", "linked","sub_issues", "user_metrics","status_trello","status_jira"]
        )

def ensure_connection():
    global conn
    try:
        conn.ping(reconnect=True)
    except:
        conn = pymysql.connect(
            host='localhost',
            user='root', 
            password='50465550',
            database='db',
        )
        
sys.stdout = PrintToLogger()

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.StreamHandler(),
        logging.FileHandler('sql_queries.log', mode='a')
    ]
)
logger = logging.getLogger(__name__)

# Encryption setup
key = bytes.fromhex("bdd317920e6a5171548339a51548261e6b47d0bd3f1c2d68c68948330f78aac1")

def decrypt(enc_b64):
    data = base64.b64decode(enc_b64)
    nonce = data[:24]
    ciphertext = data[24:]
    box = SecretBox(key)
    try:
        return box.decrypt(ciphertext, nonce).decode()
    except CryptoError:
        return None

# Database setup
os.environ["OPENAI_API_KEY"] = "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"

db = SQLDatabase.from_uri(
    "mysql+pymysql://root:50465550@127.0.0.1:3306/db",
     include_tables=["tasks", "projects", "teams", "platform_users", "linked","sub_issues", "user_metrics","status_trello","status_jira", "users"]
)
conn = pymysql.connect(
    host='localhost',
    user='root',
    password='50465550',
    database='db',
)

def get_chat_history(chat_id, db_conn):
    ensure_connection()
    cursor = db_conn.cursor(pymysql.cursors.DictCursor)
    query = """
        SELECT sender_type, message, reaction
        FROM chat_messages
        WHERE chat_id = %s
        ORDER BY created_at DESC
        LIMIT 10
    """
    logger.info(f"Executing chat history query for chat_id {chat_id}:\n{query}")
    cursor.execute(query, (chat_id,))
    rows = list(cursor.fetchall())
    rows.reverse()

    history = ""
    for row in rows:
        prefix = "user:" if row["sender_type"] == "user" else "assistant:"
        message = row["message"].strip()
        reaction = row["reaction"]
        if reaction:
            message += f" [user reaction to message: {reaction}]"
        history += f"{prefix} {message}\n"

    return history.strip()

def get_user_metrics(owner_id, db_conn):
    """Fetch user-defined metrics for analysis"""
    ensure_connection()
    cursor = db_conn.cursor(pymysql.cursors.DictCursor)
    query = """
        SELECT title, description, category, weight, percentage, metric_id
        FROM user_metrics
        WHERE userid = %s
        ORDER BY created_at DESC
    """
    logger.info(f"Fetching user metrics for owner_id {owner_id}:\n{query}")
    cursor.execute(query, (owner_id,))
    results = cursor.fetchall()
    return results if results else []

def save_message_to_db(chat_id, user_id, owner_id, sender_type, message, db_conn):
    ensure_connection()
    cursor = db_conn.cursor()
    query = """
        INSERT INTO chat_messages (chat_id, user_id, owner_id, sender_type, message)
        VALUES (%s, %s, %s, %s, %s)
    """
    logger.info(f"Saving message to DB for chat_id {chat_id}, user_id {user_id}:\n{query}")
    cursor.execute(query, (chat_id, user_id, owner_id, sender_type, message))
    db_conn.commit()

def get_workspace_types(owner_id, db_conn):
    ensure_connection()
    cursor = db_conn.cursor(pymysql.cursors.DictCursor)
    query = """
        SELECT type
        FROM linked
        WHERE userid = %s
    """
    logger.info(f"Fetching workspace types for owner_id {owner_id}:\n{query}")
    cursor.execute(query, (owner_id,))
    results = cursor.fetchall()
    return ", ".join([row["type"] for row in results]) if results else "unknown"

# Enhanced LLM setup with function calling capabilities
llm = ChatOpenAI(
    temperature=0.4,
    model="gpt-4.1-nano",
    streaming=True
)

# Template-aware intent classification
intent_prompt = PromptTemplate.from_template("""
Classify the user's query to determine if it needs database data or is just conversation.

And your name is reviewbot


CONVERSATION queries:
- never you mention anything about sql to a response
- Greetings: hi, hello, hey, good morning, how are you
- General chat: thanks, goodbye, see you later
- General questions about the assistant: what can you do, who are you

DATA queries:
- sometimes the user query might look tricky, try to understand if the user what data and use "sql" thats more important
- if user ask for anything related to team , or duration, or task, or project, or platform_users, or linked, use sql
- if the query is about a user or the user talking about a particular user use sql
- most of your query should be from tasks 
- Follow-ups based on prior questions (use same table unless explicitly redirected)
- KPI requests, performance questions, stats, breakdowns
- Requests involving: show me, list, how many, find
- Deeper analysis: compare, analyze, breakdown, summary

Respond with either "sql" or "chat": 

User query: {query}
""")

intent_chain = LLMChain(llm=llm, prompt=intent_prompt)


sql_prompt = PromptTemplate.from_template("""
Based on the database schema, user query, and owner_id, write a SQL query to get the data.
PULL MOST FROM tasks  TABLE OR IF YOU WANNA JOIN FROM IT, TO GET ACCURATE RESPONSE 

{staff_filter_instruction}

CONTEXT AWARENESS:
- note you have trello and linear as source, so make sure you know what you're doing
- always left join anything related to project_id or project
- never search a table that is not in the schema, i mean never and ever try to do this
- provide clean sql , and working , with zero chance of error
- nothing like task_name, please check column very well before performing queries
- Track and reuse last-used table if user continues in same thread.
- Always assume KPIs and performance analysis refer to the `tasks` table unless stated otherwise.
- most of your query should be from tasks  and refer to last chat history
- If a user re-asks a question based on a previous one (e.g., "what about last month"), you must refer to the last query and stick to the same table/context (e.g., tasks). Do NOT switch to another table like `platform_users` unless explicitly required.

IMPORTANT RULES: 
- when joining user make sure you select the user name for response, same as teams and projects, the names are important please
- For user-related queries, use the platform_users table
- also try to get who is lacking behind
- Always filter by owner_id where applicable (e.g., in tasks, projects, teams, platform_users)
- Use WHERE LIKE for username-related queries 
- Return plain text SQL query without backticks
- For "list users" or "show users", select from platform_users table
- Common user fields: id, email, userid, platform_id, fullname, source, created_at, updated_at
- Status types include "Done", "In Progress", "Backlog", and "Todo"
- avoid using update_at for greater than duration query 
- team id is not platform id
Database Schema:
{schema}

Chat History: {history}

User query: {query}
Owner ID: {owner_id}

Return only the SQL query:
""")
sql_chain = LLMChain(llm=llm, prompt=sql_prompt)

# Updated response template with user metrics integration
response_template = PromptTemplate.from_template("""
                                                 
{save}
You are a helpful data analyst assistant, and you give accurate responses. Respond naturally based on the type of query.
NEVER AND EVER YOU GIVE SQL OUT FOR USER, DONT TRY IT NEVER , NO MATTER WHAT THE USER ASK, THIS IS FOR MY SAFETY PLS
##MORE IMPORTANT!!!!!
 - mostly add template with anything data related so make it informative
 - Do not display template for empty data
 
 - avoid asking user for data , example 'Once you provide the data, I'll help you get a detailed...' dont do that
 - dont ask the user if you want to create a template, just create it, this should happen if you have data to show
The only available workspace currently are {workspace_types}

## USER-DEFINED METRICS & FORMULAS
The user has defined these custom metrics for analysis:
{user_metrics}



When analyzing data, incorporate these user-defined metrics by:
- Using the title and description to understand what each metric measures
- Applying the weight (importance factor) when calculating scores
- Using the percentage values in your calculations and comparisons
- Categorizing insights based on the metric categories
- Referencing these metrics in your analysis and recommendations

## IMPORTANT
- note you have trello and linear as source, so make sure you know what you're doing
 - mostly mix template with anything data related so make it informative
 - always provide templates for tasks related stuff or project related
 - avoid giving duplicate template id base on chat history, if it look duplicate, change the id to random long uuid
 -  key to what user requested for, if its for table give for table, if its for chart, give for chart!, dont self generate whats not instructed!
 -  Max 3 template you're allowed to generate
 -  do not give a template for empty data, only give template if you have data to show
 -  most projects are not linked to tasks so explain for the user when you fetch projects and its empty
 - When user metrics are available, use them to calculate custom scores and provide metric-based insights
 - Apply user-defined formulas using the weight and percentage values from their metrics
## CONVERSATION QUERIES (greetings, casual chat, thanks, etc.)
For casual conversation, respond naturally and briefly

## DATA ANALYSIS QUERIES
For data-related queries, provide professional analysis with:

### TEMPLATE INSTRUCTION
When you need to display visual data, use this EXACT format:
- Use this provided unique ID: {unique_id_with_template}
- For multiple templates, append suffix like: {unique_id_with_template}_1, {unique_id_with_template}_2, etc.

[START TEMPLATE]{{"id":"{unique_id_with_template}","description":"description of the template needed based on data provided including specific id and columns required to fetch from db with","sql":"an sql of what should be selected from db"}}[END TEMPLATE]

TEMPLATE TYPES AVAILABLE:
- "chart": For visualizing numerical data, trends, comparisons, and statistics, type of chart available, use them randomly, LineChart, BarChart, PieChart, ScatterChart
- "table": For displaying structured data, lists, detailed records, and comprehensive information

### PROFESSIONAL DATA RESPONSES
- Use emojis appropriately to enhance readability
- avoid using user id to reply , instead use the user name
- also try to get who is lacking behind
- give clean response , total response should be 10 paragraph mixed with templates 
- mostly provide kpi of tasks analysis 
- Note that you analyze data base on kpis of users quick completion rate and if the user is lacking behind, stuff like that
- Apply user-defined metrics and formulas when available - calculate custom scores using their weight and percentage values
- Start with a clear summary of findings (include metric-based insights if applicable)
- end with recommendations of 4 paragraph
- Provide detailed analysis with actionable insights
- Include specific recommendations
- Create templates when they enhance understanding

### NO DATA SCENARIOS 
- IF NO DATA FOUND: "No records found for [specific item]" - keep it simple
- IF PARTIAL DATA: Analyze what's available
- IF NO DATA AT ALL: Brief professional explanation

### ANALYSIS STRUCTURE
1. **Key Findings** (brief summary with main insights including user metrics if applicable)
2. **Data Analysis** (detailed breakdown with templates as needed, apply user formulas)
3. **Custom Metric Analysis** (if user metrics are available, show calculated scores)
4. **Recommendations** (specific actionable steps based on data and user-defined metrics)

Query type based on results: {db_results}
Your chat history: {history}
User asked: "{query}"

Respond appropriately - keep it casual for conversation, detailed for data analysis, and avoid using quote to reply, everything is plain text.

Your response:
""")

def clean_sql(raw_sql: str, uppercase_keywords: bool = True) -> str:
    raw_sql = re.sub(r"```(?:sql)?", "", raw_sql).replace("```", "")
    raw_sql = raw_sql.replace("\\n", "\n").replace("\\", "")
    cleaned = sqlparse.format(
        raw_sql.strip(),
        reindent=True,
        keyword_case="upper" if uppercase_keywords else "lower"
    )
    logger.debug(f"Cleaned SQL query:\n{cleaned}")
    return cleaned


def create_new_chat(user_id, user_query, db_conn):
    ensure_connection()
    """Create a new chat and generate title/description based on user query"""
    chat_uuid = str(uuid.uuid4())
    
    # Generate title and description using AI
    title_prompt = PromptTemplate.from_template("""
    Create a concise, descriptive title (max 50 characters) for a chat based on this user query.
    Make it specific but brief.
    
    IMPORTANT:
    - The title should be clear and relevant to the user's intent.
    - Avoid generic titles like "New Chat" or "Chat Conversation".
    - Avoid words like Unclear request; user input appears to be random text, instead focus on the main topic or action the user is interested in.
    User query: {query}
    
    Return only the title:
    """)
    
    description_prompt = PromptTemplate.from_template("""
    Create a brief description (max 150 characters) for a chat based on this user query.
    Summarize what the user wants to accomplish.
    
    User query: {query}
    
    Return only the description:
    """)
    
    title_chain = LLMChain(llm=llm, prompt=title_prompt)
    description_chain = LLMChain(llm=llm, prompt=description_prompt)
    
    try:
        # Generate title and description
        title = title_chain.run(query=user_query).strip()[:50]
        description = description_chain.run(query=user_query).strip()[:150]
        
        # Insert new chat into database
        cursor = db_conn.cursor()
        query = """
            INSERT INTO chats (user_id, title, description, uuid)
            VALUES (%s, %s, %s, %s)
        """
        cursor.execute(query, (user_id, title, description, chat_uuid))
        chat_id = cursor.lastrowid
        db_conn.commit()
        cursor.close()
        
        logger.info(f"Created new chat with ID {chat_id} and UUID {chat_uuid} for user {user_id}")
        
        return {
            'chat_id': chat_id,
            'chat_uuid': chat_uuid,
            'title': title,
            'description': description
        }
        
    except Exception as e:
        logger.error(f"Error creating new chat: {str(e)}")
        # Fallback to default values
        cursor = db_conn.cursor()
        query = """
            INSERT INTO chats (user_id, title, description, uuid)
            VALUES (%s, %s, %s, %s)
        """
        cursor.execute(query, (user_id, "New Chat", "Chat conversation", chat_uuid))
        chat_id = cursor.lastrowid
        db_conn.commit()
        cursor.close()
        
        return {
            'chat_id': chat_id,
            'chat_uuid': chat_uuid,
            'title': "New Chat",
            'description': "Chat conversation"
        }
        
# FastAPI setup
app = FastAPI()
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


@app.websocket("/ws")
async def websocket_endpoint(websocket: WebSocket):
    await websocket.accept()
    try:
        while True:
            data = await websocket.receive_json()
            query = data.get("query", "")
            user_id_enc = data.get("user_id")
            chat_id_enc = data.get("chat_id")
            staff_id = data.get("staff_id", "")
            save = data.get("save", 0)
            user_id = decrypt(user_id_enc) if user_id_enc else None
            
            if not query or not user_id:
                await websocket.send_json({
                    "type": "error",
                    "message": "Query and user_id are required."
                })
                continue
            logger.info(f"chat_id_enc {chat_id_enc}")
            # Handle empty chat_id - create new chat
            if not chat_id_enc:
                # Only create new chat if not staff_id or save equals 1
                if not staff_id or save == 1:
                    new_chat_info = create_new_chat(user_id, query, conn)
                    chat_id = new_chat_info['chat_uuid']
                    logger.info(chat_id)
                    
                    # Send new chat info back to client
                    await websocket.send_json({
                        "type": "new_chat_created",
                        "chat_id": new_chat_info['chat_id'],
                        "chat_uuid": new_chat_info['chat_uuid'],
                        "title": new_chat_info['title'],
                        "description": new_chat_info['description'],
                        "created_at": datetime.datetime.now().isoformat()
                    })
                else:
                    chat_id = None
            else:
                chat_id = chat_id_enc
            
            chat_history = get_chat_history(chat_id, conn) if chat_id else ""
            await process_query_with_streaming(query, user_id, chat_id, websocket, chat_history, staff_id, save)
            
    except WebSocketDisconnect:
        logger.info("Client disconnected from WebSocket")

async def process_query_with_streaming(query: str, user_id: str, chat_id: str, websocket: WebSocket, chat_history: str, staff_id: str = "", save: int = 0):
    # Save user message only if staff_id and save != 1, or if no staff_id, or if save == 1
    if (not staff_id and save != 1) or save == 1:
        save_message_to_db(chat_id, user_id, user_id, "user", query, conn)
    
    # Generate unique ID for this conversation turn (for bot response and templates)
    import uuid
    import secrets
    import time

    random_str = secrets.token_hex(8)  # 16-char hex string
    timestamp = int(time.time() * 1000)  # current time in ms
    unique_id_with_template = f"uid_{uuid.uuid4().hex}_{random_str}_{timestamp}"
    logger.info(f"Generated unique_id_with_template: {unique_id_with_template}")

    
    workspace_types = get_workspace_types(user_id, conn)
    user_metrics = get_user_metrics(user_id, conn)
    
    # Format user metrics for the AI prompt
    metrics_text = ""
    if user_metrics:
        for metric in user_metrics:
            metrics_text += f"• {metric['title']}: {metric['description']} (Category: {metric['category']}, Weight: {metric['weight']}, Percentage: {metric['percentage']}%)\n"
    else:
        metrics_text = "No custom metrics defined by user."
    
    logger.info(f"Processing query for user_id {user_id}: {query}")
    logger.info(f"Staff ID filter: {staff_id}")
    logger.info(f"Generated unique_id_with_template: {unique_id_with_template}")
    logger.info(f"User metrics loaded: {len(user_metrics)} metrics")
    
    # Intent classification
    try:
        intent_result = await intent_chain.arun(query=query, history=chat_history)
        intent = intent_result.strip().lower()
        logger.info(f"Intent classification: {intent}")
    except Exception as e:
        logger.error(f"Intent classification error: {str(e)}")
        await websocket.send_json({
            "type": "error",
            "message": f"Intent classification failed: {str(e)}"
        })
        return

    db_results = None
    
    if intent == "sql":
        try:            
            ensure_connection()
            ensure_sqlalchemy_connection()
            schema_info = db.get_table_info()
            schema_str = json.dumps(schema_info, indent=2)
            logger.info(f"Database schema: {schema_str}")
            
            # Prepare staff filter instruction
            staff_filter_instruction = ""
            if staff_id and staff_id.strip():
                staff_filter_instruction = f"""
                STAFF FILTER REQUIREMENT:
                - A staff identifier '{staff_id}' has been provided
                - STEP 1: Determine if '{staff_id}' is an email (contains @) or an ID (numeric/string without @)
                - STEP 2: Join tasks table with platform_users table using the appropriate field:
                * If email: JOIN platform_users ON platform_users.email = '{staff_id}'
                * If ID: JOIN platform_users ON platform_users.user_id = '{staff_id}'
                - STEP 3: Always include owner filter: WHERE owner_id = '{user_id}'
                - STEP 4: Filter tasks to show only those assigned to this staff member
                - COMPLETE EXAMPLE:
                * For email: SELECT * FROM tasks JOIN platform_users ON tasks.user_id = platform_users.user_id WHERE tasks.owner_id = '{user_id}' AND platform_users.email = '{staff_id}'
                * For ID: SELECT * FROM tasks JOIN platform_users ON tasks.user_id = platform_users.user_id WHERE tasks.owner_id = '{user_id}' AND platform_users.user_id = '{staff_id}'
                - Focus analysis only on this specific staff member's performance
                """
            else:
                staff_filter_instruction = "No staff filter applied - show all results for the owner."
            
            sql_query = await sql_chain.arun(
                schema=schema_str, 
                query=query, 
                owner_id=user_id,
                history=chat_history,
                staff_filter_instruction=staff_filter_instruction
            )
            cleaned_sql = clean_sql(sql_query.strip())
            logger.info(f"Generated SQL Query for user_id {user_id} with staff_id {staff_id}:\n{cleaned_sql}")
            
            cursor = conn.cursor(pymysql.cursors.DictCursor)
            cursor.execute(cleaned_sql)
            raw_db_data = cursor.fetchall()
            cursor.close()
            
            logger.info(f"Raw DB data retrieved: {len(raw_db_data) if raw_db_data else 0} rows")
            if raw_db_data and len(raw_db_data) > 0:
                logger.info(f"Sample data: {raw_db_data[0]}")
            else:
                logger.warning("No data returned from SQL query")
            
            # Convert datetime objects to strings for JSON serialization
            from decimal import Decimal

            # Convert datetime and Decimal objects to strings or floats for JSON serialization
            serializable_results = []
            for row in raw_db_data:
                serializable_row = {}
                for key, value in row.items():
                    if hasattr(value, 'isoformat'):
                        serializable_row[key] = value.isoformat()
                    elif isinstance(value, Decimal):
                        serializable_row[key] = float(value)
                    else:
                        serializable_row[key] = value
                serializable_results.append(serializable_row)


            db_results = serializable_results
            
            await websocket.send_json({
                "type": "db_results", 
                "data": serializable_results
            })
            
        except Exception as e:
            db_results = f"Error: {str(e)}"
            logger.error(f"SQL Query Error for user_id {user_id}: {str(e)}")
            await websocket.send_json({
                "type": "error", 
                "message": str(e)
            })
    try:
        if (staff_id and save != 1):
            instruct = 'ABSOLUTELY NO TEMPLATES - RESPOND WITH TEXT ONLY'
            logger.info(f"NO TEMPLATE")
            
            # Get the original template and completely remove template sections
            modified_template = response_template.template
            
            # Remove the entire template instruction block
            template_section_start = "### TEMPLATE INSTRUCTION"
            template_section_end = "### PROFESSIONAL DATA RESPONSES"
            
            start_idx = modified_template.find(template_section_start)
            end_idx = modified_template.find(template_section_end)
            
            if start_idx != -1 and end_idx != -1:
                modified_template = modified_template[:start_idx] + "### NO TEMPLATES ALLOWED - TEXT ONLY\n\n" + modified_template[end_idx:]
            
            # Also remove any other template references
            modified_template = modified_template.replace("Create templates when they enhance understanding", "Do not create any templates")
            modified_template = modified_template.replace("with templates as needed", "without any templates")
            
            unique_id_with_template = "DISABLED"
        else:
            modified_template = response_template.template
            instruct = ''

        final_prompt_text = modified_template.format(
            history=chat_history,
            query=query,
            db_results=db_results or "No data available.",
            workspace_types=workspace_types,
            user_metrics=metrics_text,
            save=instruct,
            unique_id_with_template=unique_id_with_template
        )
        
        logger.info(f"Final prompt created with unique_id_with_template: {unique_id_with_template}")
        
    except Exception as e:
        logger.error(f"Error creating final prompt: {str(e)}")
        await websocket.send_json({
            "type": "error", 
            "message": f"Prompt creation error: {str(e)}"
        })
        return

    try:
        full_response = ""
        last_save_length = 0
        save_interval = 3 
        
        # Insert initial empty bot message with unique_id_with_template
        message_id = insert_streaming_message(
            chat_id, user_id, user_id, "bot", "", unique_id_with_template, conn
        )
        
        # Send unique_id to client so your other code can use it for templates
        # await websocket.send_json({
        #     "type": "unique_id",
        #     "unique_id_with_template": unique_id_with_template,
        #     "chat_id": chat_id
        # })
        
        async for chunk in llm.astream([HumanMessage(content=final_prompt_text)]):
            token = chunk.content or ""
            full_response += token
            
            # Send the token to client
            await websocket.send_json({
                "type": "stream_token",
                "token": token,
                "full_response": full_response
            })
            
            # Periodic save - update every N characters
            if len(full_response) - last_save_length >= save_interval:
                update_streaming_message(message_id, full_response, conn)
                last_save_length = len(full_response)
                
    except Exception as e:
        logger.error(f"Streaming error: {str(e)}")
        await websocket.send_json({
            "type": "error", 
            "message": f"Streaming error: {str(e)}"
        })
    
    # Final save with complete response
    update_streaming_message(message_id, full_response, conn)
    await websocket.send_json({"type": "stream_end",'message_id':message_id})

# 1. Modify your insert_streaming_message function to accept unique_id_with_template
def insert_streaming_message(chat_id, user_id, owner_id, sender_type, message, unique_id_with_template, db_conn):
    ensure_connection()
    """Insert initial message with unique_id_with_template and return message_id for updates"""
    cursor = db_conn.cursor()
    query = """
        INSERT INTO chat_messages (chat_id, user_id, owner_id, sender_type, message, unique_id_with_template)
        VALUES (%s, %s, %s, %s, %s, %s)
    """
    cursor.execute(query, (chat_id, user_id, owner_id, sender_type, message, unique_id_with_template))
    message_id = cursor.lastrowid
    db_conn.commit()
    cursor.close()
    return message_id


def update_streaming_message(message_id, message, db_conn):
    ensure_connection()
    """Update existing message with new content"""
    cursor = db_conn.cursor()
    query = """
        UPDATE chat_messages 
        SET message = %s, updated_at = NOW()
        WHERE id = %s
    """
    cursor.execute(query, (message, message_id))
    db_conn.commit()
    cursor.close()
    
if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        app, 
        host="0.0.0.0", 
        port=8000,
        log_config=None,
        access_log=False
    )