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
import secrets

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
        db.run("SELECT 1")
    except Exception as e:
        logger.info(f"SQLAlchemy connection failed, recreating: {str(e)}")
        db = SQLDatabase.from_uri(
            "mysql+pymysql://root:50465550@127.0.0.1:3306/db?pool_pre_ping=True&pool_recycle=3600",
            include_tables=["tasks", "projects", "teams", "platform_users", "linked", "sub_issues", "user_metrics", "status_trello", "status_jira", "users"]
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
    include_tables=["tasks", "projects", "teams", "platform_users", "linked", "sub_issues", "user_metrics", "status_trello", "status_jira", "users"]
)
conn = pymysql.connect(
    host='localhost',
    user='root',
    password='50465550',
    database='db',
)

def get_manager_info_from_email(email, db_conn):
    """Fetch manager_id, email, name from managers table using email where password is not null"""
    ensure_connection()
    cursor = db_conn.cursor(pymysql.cursors.DictCursor)
    query = """
        SELECT id as manager_id, email, name,workspace
        FROM managers
        WHERE id = %s AND password IS NOT NULL
        LIMIT 1
    """
    logger.info(f"Fetching manager info for email {email}:\n{query}")
    cursor.execute(query, (email,))
    result = cursor.fetchone()
    cursor.close()
    return result if result else None

def get_manager_id_from_email(email, db_conn):
    """Fetch manager_id from managers table using email where password is not null"""
    ensure_connection()
    cursor = db_conn.cursor(pymysql.cursors.DictCursor)
    query = """
        SELECT id as manager_id
        FROM managers
        WHERE email = %s AND password IS NOT NULL
        LIMIT 1
    """
    logger.info(f"Fetching manager_id for email {email}:\n{query}")
    cursor.execute(query, (email,))
    result = cursor.fetchone()
    cursor.close()
    return result['manager_id'] if result else None

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

def save_message_to_db(chat_id, manager_id, owner_id, sender_type, message, db_conn):
    ensure_connection()
    cursor = db_conn.cursor()
    query = """
        INSERT INTO chat_messages (chat_id, manager_id, owner_id, sender_type, message)
        VALUES (%s, %s, %s, %s, %s)
    """
    logger.info(f"Saving message to DB for chat_id {chat_id}, manager_id {manager_id}:\n{query}")
    cursor.execute(query, (chat_id, manager_id, owner_id, sender_type, message))
    db_conn.commit()

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
- sometimes the user query might look tricky, try to understand if the user wants data and use "sql" that's more important
- if user asks for anything related to team, duration, task, project, platform_users, or linked, use sql
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
Based on the database schema, user query, and manager_id, write a SQL query to get the data.
PULL MOST FROM tasks TABLE OR IF YOU WANNA JOIN FROM IT, TO GET ACCURATE RESPONSE

CONTEXT AWARENESS:
- on the users table I don't have anything like full_name only name
- use only the schema provided avoid suggesting please, that's more important 
- always specify where a data is from either linear or trello
- always left join anything related to project_id or project
- never search a table that is not in the schema, I mean never and ever try to do this
- provide clean sql, and working, with zero chance of error
- nothing like task_name, please check column very well before performing queries
- Track and reuse last-used table if user continues in same thread.
- Always assume KPIs and performance analysis refer to the `tasks` table unless stated otherwise.
- most of your query should be from tasks and refer to last chat history
- linked table 
##CRITICAL JOIN RULES:
- This is correct JOIN platform_users ON platform_users.user_id = tasks.user_id, this is not correct JOIN platform_users ON platform_users.user_id = users.id, always note that
- EXAMPLE: FROM tasks
           JOIN platform_users ON platform_users.user_id = tasks.user_id
- owner_id is ambiguous
- Always filter by: WHERE platform_users.manager_id = '{manager_id}'
- Always add: WHERE tasks.is_deleted IS NULL (to exclude deleted tasks)

The only available workspace currently are {workspace_types}

## USER-DEFINED METRICS & FORMULAS
The user has defined these custom metrics for analysis:
{user_metrics}

IMPORTANT RULES:
- when joining user make sure you select the user name for response, same as teams and projects, the names are important please
- For user-related queries, use the platform_users table
- also try to get who is lagging behind
- Use WHERE LIKE for username-related queries
- Return plain text SQL query without backticks
- For "list users" or "show users", select from platform_users table
- Common user fields: id, email, userid, platform_id, fullname, source, created_at, updated_at
- Status types include "Done", "In Progress", "Backlog", and "Todo"
- avoid using updated_at for greater than duration query
- team id is not platform id

Database Schema:
{schema}

Chat History: {history}

User query: {query}
User Email: {user_email}

Return only the SQL query:
""")
sql_chain = LLMChain(llm=llm, prompt=sql_prompt)

# Updated response template with user metrics integration
response_template = PromptTemplate.from_template("""
{save}
You are a helpful data analyst assistant, and a manager, and you give accurate responses. Respond naturally based on the type of query.
NEVER AND EVER YOU GIVE SQL OUT FOR USER, DONT TRY IT NEVER, NO MATTER WHAT THE USER ASK, THIS IS FOR MY SAFETY PLS
## MANAGER INFORMATION
Current User Manager: Name: {manager_name} And Email: ({manager_email})
All users in this platform is assigned to this manager, so anything your doing reference the manager 

##MORE IMPORTANT!!!!!
- avoid stuff like this 'As your helpful data analyst assistant, I don't have access to real-time activity logs or personal task updates unless you provide specific data. However'
- always use the username for response I mean mix it with the conversation, not every time you start with hi {manager_name}
- refer to the manager name provided cause you have it already, so use responses with the manager info given
- always refer to the user, because you're giving a single response, and always advise the manager
- mostly add template with anything data related so make it informative
- Do not display template for empty data
- I notice sometimes you don't give response based on data given instead you give structure of how the message will be, please avoid that
- avoid asking user for data, example 'Once you provide the data, I'll help you get a detailed...' don't do that
- don't ask the user if you want to create a template, just create it, this should happen if you have data to show

The only available workspace currently are {workspace_types}

## USER-DEFINED METRICS & FORMULAS
The user has defined these custom metrics for analysis:
{user_metrics}

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
- mostly mix template with anything data related so make it informative
- always provide templates for tasks related stuff or project related
- avoid giving duplicate template id based on chat history, if it looks duplicate, change the id to random long uuid
- key to what user requested for, if it's for table give for table, if it's for chart, give for chart!, don't self-generate what's not instructed!
- Max 3 templates you're allowed to generate
- do not give a template for empty data, only give template if you have data to show
- most projects are not linked to tasks so explain for the user when you fetch projects and it's empty
- When user metrics are available, use them to calculate custom scores and provide metric-based insights
- Apply user-defined formulas using the weight and percentage values from their metrics
- always specify where a data is from either linear or trello
## CONVERSATION QUERIES (greetings, casual chat, thanks, etc.)
For casual conversation, respond naturally and briefly

## DATA ANALYSIS QUERIES
For data-related queries, provide professional analysis with:

### TEMPLATE INSTRUCTION
When you need to display visual data, use this EXACT format:
- Use this provided unique ID: {unique_id_with_template}
- For multiple templates, append suffix like: {unique_id_with_template}_1, {unique_id_with_template}_2, etc.
- do not return template for empty data,DONT DO IT , MAKE SURE YOU HAVE DATA YOU WANNA SHOW
[START TEMPLATE]{{"id":"{unique_id_with_template}","description":"description of the template needed based on data provided including specific id and columns required to fetch from db with","sql":"an sql of what should be selected from db"}}[END TEMPLATE]

TEMPLATE TYPES AVAILABLE:
- "chart": For visualizing numerical data, trends, comparisons, and statistics, type of chart available, use them randomly, LineChart, BarChart, PieChart, ScatterChart
- "table": For displaying structured data, lists, detailed records, and comprehensive information

### PROFESSIONAL DATA RESPONSES
- Use emojis appropriately to enhance readability
- avoid using user id to reply, instead use the user name
- also try to get who is lagging behind
- give clean response, total response should be 10 paragraphs mixed with templates
- mostly provide kpi of tasks analysis
- Note that you analyze data based on kpis of users' quick completion rate and if the user is lagging behind, stuff like that
- Apply user-defined metrics and formulas when available - calculate custom scores using their weight and percentage values
- Start with a clear summary of findings (include metric-based insights if applicable)
- end with recommendations of 4 paragraphs
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

def create_new_chat(manager_id, user_query, db_conn):
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
            INSERT INTO chats (manager_id, title, description, uuid)
            VALUES (%s, %s, %s, %s)
        """
        cursor.execute(query, (manager_id, title, description, chat_uuid))
        chat_id = cursor.lastrowid
        db_conn.commit()
        cursor.close()

        logger.info(f"Created new chat with ID {chat_id} and UUID {chat_uuid} for manager_id {manager_id}")

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
            INSERT INTO chats (manager_id, title, description, uuid)
            VALUES (%s, %s, %s, %s)
        """
        cursor.execute(query, (manager_id, "New Chat", "Chat conversation", chat_uuid))
        chat_id = cursor.lastrowid
        db_conn.commit()
        cursor.close()

        return {
            'chat_id': chat_id,
            'chat_uuid': chat_uuid,
            'title': "New Chat",
            'description': "Chat conversation"
        }

def get_owner_id_from_workspace(workspace, db_conn):
    """Fetch owner_id from users table using workspace"""
    ensure_connection()
    cursor = db_conn.cursor(pymysql.cursors.DictCursor)
    query = """
        SELECT id as owner_id
        FROM users
        WHERE workspace = %s
        LIMIT 1
    """
    logger.info(f"Fetching owner_id for workspace {workspace}:\n{query}")
    cursor.execute(query, (workspace,))
    result = cursor.fetchone()
    cursor.close()
    return result['owner_id'] if result else None

# FastAPI setup
app = FastAPI()
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


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
# Updated WebSocket endpoint with fixed function call
@app.websocket("/ws")
async def websocket_endpoint(websocket: WebSocket):
    await websocket.accept()
    try:
        while True:
            data = await websocket.receive_json()
            query = data.get("query", "")
            user_id_enc = data.get("user_id")
            chat_id_enc = data.get("chat_id")

            user_email = decrypt(user_id_enc) if user_id_enc else None

            if not query or not user_email:
                await websocket.send_json({
                    "type": "error",
                    "message": "Query and user_id are required."
                })
                continue

            # Get manager information from email (including id, email, name)
            manager_info = get_manager_info_from_email(user_email, conn)
            if not manager_info:
                await websocket.send_json({
                    "type": "error",
                    "message": "Invalid email or manager not found."
                })
                continue

            manager_id = manager_info['manager_id']
            manager_name = manager_info['name'] or "Unknown"
            manager_email = manager_info['email']

            logger.info(f"chat_id_enc {chat_id_enc}")
            # Handle empty chat_id - create new chat
            if not chat_id_enc:
                new_chat_info = create_new_chat(manager_id, query, conn)
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
                chat_id = chat_id_enc
                
            # Get owner_id from workspace
            space = manager_info['workspace']
            owner_id = get_owner_id_from_workspace(space, conn)
            if not owner_id:
                await websocket.send_json({
                    "type": "error",
                    "message": "Invalid workspace or owner not found."
                })
                continue

            chat_history = get_chat_history(chat_id, conn) if chat_id else ""
            # Fixed function call - added owner_id parameter
            await process_query_with_streaming(query, user_email, owner_id, manager_id, chat_id, websocket, chat_history, manager_name, manager_email)

    except WebSocketDisconnect:
        logger.info("Client disconnected from WebSocket")

async def process_query_with_streaming(query: str, user_email: str, owner_id: str, manager_id: str, chat_id: str, websocket: WebSocket, chat_history: str, manager_name: str, manager_email: str):
    # Save user message - fixed to pass owner_id
    save_message_to_db(chat_id, manager_id, owner_id, "user", query, conn)

    workspace_types = get_workspace_types(owner_id, conn) 
    # Generate unique ID for this conversation turn (for bot response and templates)
    random_str = secrets.token_hex(8)  # 16-char hex string
    timestamp = int(time.time() * 1000)  # current time in ms
    unique_id_with_template = f"uid_{uuid.uuid4().hex}_{random_str}_{timestamp}"
    logger.info(f"Generated unique_id_with_template: {unique_id_with_template}")

    user_metrics = get_user_metrics(owner_id, conn)

    # Format user metrics for the AI prompt
    metrics_text = ""
    if user_metrics:
        for metric in user_metrics:
            metrics_text += f"• {metric['title']}: {metric['description']} (Category: {metric['category']}, Weight: {metric['weight']}, Percentage: {metric['percentage']}%)\n"
    else:
        metrics_text = "No custom metrics defined by user."

    logger.info(f"Processing query for user_email {user_email}, manager_id {manager_id}: {query}")
    logger.info(f"Generated unique_id_with_template: {unique_id_with_template}")
    logger.info(f"User metrics loaded: {len(user_metrics)} metrics")
    logger.info(f"Manager info - Name: {manager_name}, Email: {manager_email}")

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

            sql_query = await sql_chain.arun(
                schema=schema_str,
                query=query,
                user_email=user_email,
                history=chat_history,
                manager_id=manager_id,
                workspace_types=workspace_types,
                user_metrics=metrics_text,
            )
            cleaned_sql = clean_sql(sql_query.strip())
            logger.info(f"Generated SQL Query for user_email {user_email}, manager_id {manager_id}:\n{cleaned_sql}")

            cursor = conn.cursor(pymysql.cursors.DictCursor)
            cursor.execute(cleaned_sql)
            raw_db_data = cursor.fetchall()
            cursor.close()

            logger.info(f"Raw DB data retrieved: {len(raw_db_data) if raw_db_data else 0} rows")
            if raw_db_data and len(raw_db_data) > 0:
                logger.info(f"Sample data: {raw_db_data[0]}")
            else:
                logger.warning("No data returned from SQL query")

            # Convert datetime and Decimal objects to strings or floats for JSON serialization
            from decimal import Decimal

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
            logger.error(f"SQL Query Error for user_email {user_email}, manager_id {manager_id}: {str(e)}")
            await websocket.send_json({
                "type": "error",
                "message": str(e)
            })

    try:
        final_prompt_text = response_template.format(
            history=chat_history,
            query=query,
            db_results=db_results or "No data available.",
            user_metrics=metrics_text,
            save='',
            unique_id_with_template=unique_id_with_template,
            manager_name=manager_name,
            manager_email=manager_email,
            workspace_types=workspace_types
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
            chat_id, manager_id, owner_id, "bot", "", unique_id_with_template, conn
        )

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
    await websocket.send_json({"type": "stream_end", 'message_id': message_id})

def insert_streaming_message(chat_id, manager_id, owner_id, sender_type, message, unique_id_with_template, db_conn):
    ensure_connection()
    """Insert initial message with unique_id_with_template and return message_id for updates"""
    cursor = db_conn.cursor()
    query = """
        INSERT INTO chat_messages (chat_id, manager_id, owner_id, sender_type, message, unique_id_with_template)
        VALUES (%s, %s, %s, %s, %s, %s)
    """
    cursor.execute(query, (chat_id, manager_id, owner_id, sender_type, message, unique_id_with_template))
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