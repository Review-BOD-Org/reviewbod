from flask import Flask, request, jsonify
from langchain_community.chat_models import ChatOpenAI
from langchain.prompts import PromptTemplate
from langchain.chains import LLMChain
from langchain_community.utilities import SQLDatabase
import pymysql
from pymysql.err import OperationalError, InterfaceError
import logging
import os
import json
from nacl.secret import SecretBox
from nacl.exceptions import CryptoError
import base64
import sqlparse
import re
from decimal import Decimal
from datetime import datetime, date
import time
from contextlib import contextmanager


class DecimalEncoder(json.JSONEncoder):
    def default(self, obj):
        if isinstance(obj, Decimal):
            return float(obj)
        return super(DecimalEncoder, self).default(obj)
    
def convert_decimal_to_float(obj):
    """Recursively convert Decimal objects to float in nested structures"""
    if isinstance(obj, Decimal):
        return float(obj)
    elif isinstance(obj, dict):
        return {key: convert_decimal_to_float(value) for key, value in obj.items()}
    elif isinstance(obj, list):
        return [convert_decimal_to_float(item) for item in obj]
    else:
        return obj
app = Flask(__name__)

# Setup logging
logging.basicConfig(level=logging.INFO)
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

# Database configuration
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '50465550',
    'database': 'db',
    'charset': 'utf8mb4',
    'autocommit': True,
    'connect_timeout': 60,
    'read_timeout': 60,
    'write_timeout': 60
}

class DatabaseManager:
    def __init__(self, config):
        self.config = config
        self._connection = None
        
    def get_connection(self):
        """Get a database connection with automatic reconnection"""
        if self._connection is None or not self._is_connection_alive():
            self._connect()
        return self._connection
    
    def _connect(self):
        """Create a new database connection"""
        try:
            self._connection = pymysql.connect(**self.config)
            logger.info("Database connection established")
        except Exception as e:
            logger.error(f"Failed to connect to database: {str(e)}")
            raise
    
    def _is_connection_alive(self):
        """Check if the current connection is still alive"""
        if self._connection is None:
            return False
        try:
            self._connection.ping(reconnect=True)
            return True
        except:
            return False
    
    def _consume_all_results(self, cursor):
        """Consume all remaining results to prevent 'Command Out of Sync' errors"""
        try:
            while cursor.nextset():
                cursor.fetchall()
        except Exception:
            # No more result sets
            pass
    
    def execute_query(self, query, params=None, fetch=True):
        """Execute a query with automatic reconnection and proper result handling"""
        max_retries = 3
        for attempt in range(max_retries):
            cursor = None
            try:
                conn = self.get_connection()
                cursor = conn.cursor(pymysql.cursors.DictCursor)
                cursor.execute(query, params)
                
                if fetch:
                    result = cursor.fetchall()
                    # Consume any remaining results to prevent sync issues
                    self._consume_all_results(cursor)
                    cursor.close()
                    return result
                else:
                    # Consume any remaining results before committing
                    self._consume_all_results(cursor)
                    conn.commit()
                    cursor.close()
                    return True
                    
            except (OperationalError, InterfaceError) as e:
                logger.warning(f"Database connection error (attempt {attempt + 1}): {str(e)}")
                if cursor:
                    try:
                        cursor.close()
                    except:
                        pass
                self._connection = None  # Force reconnection
                if attempt == max_retries - 1:
                    raise
                time.sleep(1)  # Wait before retry
            except Exception as e:
                logger.error(f"Database query error: {str(e)}")
                if cursor:
                    try:
                        # Try to consume remaining results even on error
                        self._consume_all_results(cursor)
                        cursor.close()
                    except:
                        pass
                raise

    def execute_stored_procedure(self, proc_name, params=None):
        """Execute a stored procedure with proper result handling"""
        max_retries = 3
        for attempt in range(max_retries):
            cursor = None
            try:
                conn = self.get_connection()
                cursor = conn.cursor(pymysql.cursors.DictCursor)
                
                # Execute stored procedure
                cursor.callproc(proc_name, params or [])
                
                # Fetch all result sets
                results = []
                result = cursor.fetchall()
                if result:
                    results.append(result)
                
                # Handle multiple result sets from stored procedure
                while cursor.nextset():
                    result = cursor.fetchall()
                    if result:
                        results.append(result)
                
                cursor.close()
                conn.commit()
                
                # Return first result set if only one, otherwise return all
                return results[0] if len(results) == 1 else results
                
            except (OperationalError, InterfaceError) as e:
                logger.warning(f"Database connection error (attempt {attempt + 1}): {str(e)}")
                if cursor:
                    try:
                        cursor.close()
                    except:
                        pass
                self._connection = None  # Force reconnection
                if attempt == max_retries - 1:
                    raise
                time.sleep(1)  # Wait before retry
            except Exception as e:
                logger.error(f"Stored procedure error: {str(e)}")
                if cursor:
                    try:
                        # Consume any remaining results
                        while cursor.nextset():
                            cursor.fetchall()
                        cursor.close()
                    except:
                        pass
                raise

    def close_connection(self):
        """Explicitly close the database connection"""
        if self._connection:
            try:
                self._connection.close()
                self._connection = None
                logger.info("Database connection closed")
            except Exception as e:
                logger.error(f"Error closing database connection: {str(e)}")

# Initialize database manager
db_manager = DatabaseManager(DB_CONFIG)

# Database setup for LangChain
os.environ["OPENAI_API_KEY"] = "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"

# Create SQLDatabase - LangChain SQLDatabase doesn't support pooling parameters directly
db = SQLDatabase.from_uri(
    "mysql+pymysql://root:50465550@localhost:3306/db?charset=utf8mb4&autocommit=true",
      include_tables=["tasks", "projects", "teams", "platform_users", "linked","sub_issues", "user_metrics","status_trello","status_jira", "users"],
       engine_args={
        'pool_pre_ping': True,
        'pool_recycle': 3600,
        'pool_size': 10,
        'max_overflow': 20
    }
)

# Enhanced LLM setup
llm = ChatOpenAI(
    temperature=0.5,
    model="gpt-4o",
)


def clean_sql(raw_sql: str) -> str:
    """Enhanced SQL cleaning with validation"""
    try:
        # Remove markdown formatting
        raw_sql = re.sub(r"```(?:sql)?", "", raw_sql).replace("```", "")
        raw_sql = raw_sql.replace("\\n", "\n").replace("\\", "")
        
        # Remove any text before the first SELECT
        sql_match = re.search(r'(SELECT.*)', raw_sql, re.IGNORECASE | re.DOTALL)
        if sql_match:
            raw_sql = sql_match.group(1)
        
        # Clean up common issues
        raw_sql = raw_sql.strip()
        
        # Remove any trailing explanatory text after semicolon
        if ';' in raw_sql:
            raw_sql = raw_sql.split(';')[0] + ';'
        
        # Parse and validate SQL syntax
        try:
            parsed = sqlparse.parse(raw_sql)
            if not parsed or not parsed[0].tokens:
                raise ValueError("Empty or invalid SQL")
            
            # Check if it's a valid SELECT statement
            first_token = str(parsed[0].tokens[0]).strip().upper()
            if not first_token.startswith('SELECT'):
                raise ValueError("SQL must start with SELECT")
                
        except Exception as parse_error:
            logger.error(f"SQL parsing error: {parse_error}")
            raise ValueError(f"Invalid SQL syntax: {parse_error}")
        
        # Format the SQL
        cleaned = sqlparse.format(raw_sql.strip(), reindent=True, keyword_case="upper")
        
        # Final validation checks
        if not cleaned.strip().upper().startswith('SELECT'):
            raise ValueError("Cleaned SQL doesn't start with SELECT")
            
        return cleaned
        
    except Exception as e:
        logger.error(f"SQL cleaning error: {str(e)}")
        raise ValueError(f"Failed to clean SQL: {str(e)}")


def create_safe_template_structure(processed_data, column_types, description, template_type="auto"):
    """Create template structure with guaranteed consistency"""
    try:
        if not processed_data or not column_types:
            return {
                "template_type": "table",
                "structure": {
                    "message": "No data available",
                    "columns": [],
                    "data": []
                }
            }
        
        columns = list(column_types.keys())
        string_cols = [col for col, dtype in column_types.items() if dtype == 'string']
        number_cols = [col for col, dtype in column_types.items() if dtype == 'number']
        date_cols = [col for col, dtype in column_types.items() if dtype == 'date']
        
        # Always create table structure as fallback
        table_structure = {
            "template_type": "table",
            "structure": {
                "columns": [{"data": col, "title": col.replace('_', ' ').title()} for col in columns],
                "data": processed_data
            }
        }
        
        # Try to create chart if conditions are met
        can_create_chart = False
        chart_structure = None
        
        if len(processed_data) > 1 and len(columns) >= 2:
            chart_columns = []
            chart_type = "BarChart"  # Safe default
            
            # Determine best chart setup
            if date_cols and number_cols:
                # Time series
                chart_type = "LineChart"
                chart_columns = [
                    {"type": "date", "label": date_cols[0].replace('_', ' ').title(), "data": date_cols[0]},
                    {"type": "number", "label": number_cols[0].replace('_', ' ').title(), "data": number_cols[0]}
                ]
                can_create_chart = True
                
            elif string_cols and number_cols:
                # Categorical data
                chart_type = "PieChart" if len(processed_data) <= 10 else "BarChart"
                chart_columns = [
                    {"type": "string", "label": string_cols[0].replace('_', ' ').title(), "data": string_cols[0]},
                    {"type": "number", "label": number_cols[0].replace('_', ' ').title(), "data": number_cols[0]}
                ]
                can_create_chart = True
                
            elif len(number_cols) >= 2:
                # Scatter plot
                chart_type = "ScatterChart"
                chart_columns = [
                    {"type": "number", "label": number_cols[0].replace('_', ' ').title(), "data": number_cols[0]},
                    {"type": "number", "label": number_cols[1].replace('_', ' ').title(), "data": number_cols[1]}
                ]
                can_create_chart = True
            
            if can_create_chart and chart_columns:
                chart_structure = {
                    "template_type": "chart",
                    "structure": {
                        "chartType": chart_type,
                        "options": {
                            "title": description,
                            "legend": {"position": "bottom"},
                            "animation": {
                                "startup": True,
                                "duration": 1000,
                                "easing": "out"
                            },
                            "width": "100%",
                            "height": 400,
                            "backgroundColor": "transparent"
                        },
                        "columns": chart_columns,
                        "chart_data": processed_data,
                        "column_types": column_types
                    }
                }
                
                # Add axis labels for applicable chart types
                if chart_type in ["BarChart", "LineChart", "ScatterChart"]:
                    chart_structure["structure"]["options"]["hAxis"] = {"title": chart_columns[0]["label"]}
                    chart_structure["structure"]["options"]["vAxis"] = {"title": chart_columns[1]["label"]}
        
        # Return chart if possible, otherwise table
        return chart_structure if chart_structure else table_structure
        
    except Exception as e:
        logger.error(f"Error creating template structure: {str(e)}")
        # Ultimate fallback
        return {
            "template_type": "table",
            "structure": {
                "message": "Data structure error - showing raw data",
                "columns": [{"data": "error", "title": "Error"}],
                "data": [{"error": str(e)}]
            }
        }
        
# Add this function to fix chart template generation
def create_chart_template_structure(processed_data, column_types, description, suggested_chart_type):
    """Enhanced chart template creation with better column handling"""
    try:
        if not processed_data or not column_types:
            return None
            
        columns = list(column_types.keys())
        string_cols = [col for col, dtype in column_types.items() if dtype == 'string']
        number_cols = [col for col, dtype in column_types.items() if dtype == 'number']
        date_cols = [col for col, dtype in column_types.items() if dtype == 'date']
        
        # Enhanced column selection logic
        chart_columns = []
        
        if date_cols and number_cols:
            # Time series: date + number
            x_col = date_cols[0]
            y_col = number_cols[0]
            chart_columns = [
                {"type": "date", "label": x_col.replace('_', ' ').title(), "data": x_col},
                {"type": "number", "label": y_col.replace('_', ' ').title(), "data": y_col}
            ]
            
        elif string_cols and number_cols:
            # Categorical: string + number
            x_col = string_cols[0]
            y_col = number_cols[0]
            chart_columns = [
                {"type": "string", "label": x_col.replace('_', ' ').title(), "data": x_col},
                {"type": "number", "label": y_col.replace('_', ' ').title(), "data": y_col}
            ]
            
        elif len(number_cols) >= 2:
            # Scatter plot: number + number
            chart_columns = [
                {"type": "number", "label": number_cols[0].replace('_', ' ').title(), "data": number_cols[0]},
                {"type": "number", "label": number_cols[1].replace('_', ' ').title(), "data": number_cols[1]}
            ]
            
        elif string_cols and not number_cols:
            # Count chart: string + count
            x_col = string_cols[0]
            from collections import Counter
            counts = Counter(str(row[x_col]) for row in processed_data if row[x_col] is not None)
            processed_data = [{"category": k, "count": v} for k, v in counts.items()]
            column_types = {"category": "string", "count": "number"}
            
            chart_columns = [
                {"type": "string", "label": x_col.replace('_', ' ').title(), "data": "category"},
                {"type": "number", "label": "Count", "data": "count"}
            ]
            
        elif number_cols and not string_cols:
            # Index chart: add index as category
            y_col = number_cols[0]
            for i, row in enumerate(processed_data):
                row['index'] = f"Item {i+1}"
            column_types['index'] = 'string'
            
            chart_columns = [
                {"type": "string", "label": "Item", "data": "index"},
                {"type": "number", "label": y_col.replace('_', ' ').title(), "data": y_col}
            ]
            
        else:
            return None  # Unable to create chart structure
        
        # Create chart options based on chart type
        chart_options = {
            "title": description,
            "legend": {"position": "bottom"},
            "animation": {
                "startup": True,
                "duration": 1000,
                "easing": "out"
            },
            "width": "100%",
            "height": 400,
            "backgroundColor": "transparent"
        }
        
        # Add axis labels based on chart type
        if suggested_chart_type in ["BarChart", "LineChart", "ScatterChart"]:
            chart_options["hAxis"] = {"title": chart_columns[0]["label"]}
            chart_options["vAxis"] = {"title": chart_columns[1]["label"]}
        
        # Special options for specific chart types
        if suggested_chart_type == "PieChart":
            chart_options["is3D"] = False
            chart_options["pieHole"] = 0.3  # Donut style
        elif suggested_chart_type == "LineChart":
            chart_options["curveType"] = "function"
            chart_options["pointSize"] = 5
        
        chart_structure = {
            "chartType": suggested_chart_type,
            "options": chart_options,
            "columns": chart_columns,
            "chart_data": processed_data,
            "column_types": column_types
        }
        
        return chart_structure
        
    except Exception as e:
        logger.error(f"Error creating chart structure: {str(e)}")
        return None
    
def get_chat_history(chat_id):
    """Get chat history using the database manager"""
    query = """
        SELECT sender_type, message, reaction
        FROM chat_messages
        WHERE chat_id = %s
        ORDER BY created_at DESC
        LIMIT 5
    """
    try:
        rows = db_manager.execute_query(query, (chat_id,))
        rows.reverse()
        history = ""
        for row in rows:
            prefix = "user:" if row["sender_type"] == "user" else "assistant:"
            history += f"{prefix} {row['message'].strip()}\n"
        return history.strip()
    except Exception as e:
        logger.error(f"Error getting chat history: {str(e)}")
        return ""

def save_template_to_db(template_data, template_id, chat_id):
    """Save the template data to the templates table using database manager"""
    try:
        # Remove _<digits> at the end
        template_id_ = re.sub(r'_\d+$', '', template_id)

        template_json = json.dumps(template_data, cls=DecimalEncoder)


        query = """
            INSERT INTO templates (template_id, text, chat_id, unique_id_with_template)
            VALUES (%s, %s, %s, %s)
            ON DUPLICATE KEY UPDATE
                text = VALUES(text),
                chat_id = VALUES(chat_id),
                unique_id_with_template = VALUES(unique_id_with_template)
        """

        db_manager.execute_query(
            query, 
            (template_id, template_json, chat_id, template_id_),
            fetch=False
        )
        logger.info(f"Template {template_id} saved to database successfully")
        return True

    except Exception as e:
        logger.error(f"Error saving template to database: {str(e)}")
        return False

# NEW: Enhanced data preprocessing function
def preprocess_data_for_google_charts(data):
    """Enhanced data preprocessing with bulletproof error handling"""
    if not data:
        return [], {}
    
    # Ensure data is a list of dictionaries
    if not isinstance(data, list):
        return [], {}
    
    # Filter out non-dict items and ensure we have valid data
    valid_data = []
    for item in data:
        if isinstance(item, dict) and item:  # Non-empty dict
            valid_data.append(item)
    
    if not valid_data:
        return [], {}
    
    # Get all possible columns from all rows
    all_columns = set()
    for row in valid_data:
        all_columns.update(row.keys())
    
    if not all_columns:
        return [], {}
    
    # Initialize column type detection
    column_types = {}
    for col in all_columns:
        column_types[col] = {'types': set(), 'samples': []}
    
    # Analyze column types with safer access
    for row in valid_data:
        for col in all_columns:
            # Safely get value with default None
            value = row.get(col, None)
            column_types[col]['samples'].append(value)
            
            if value is None:
                continue
            elif isinstance(value, (int, float, Decimal)):
                column_types[col]['types'].add('number')
            elif isinstance(value, (datetime, date)):
                column_types[col]['types'].add('date')
            elif isinstance(value, str):
                value_clean = value.strip()
                if not value_clean:
                    column_types[col]['types'].add('string')
                    continue
                
                # Check for date strings
                date_patterns = [
                    r'^\d{4}-\d{2}-\d{2}',
                    r'^\d{2}/\d{2}/\d{4}',
                    r'^\d{2}-\d{2}-\d{4}',
                ]
                
                is_date = any(re.match(pattern, value_clean, re.IGNORECASE) for pattern in date_patterns)
                if is_date:
                    column_types[col]['types'].add('date')
                    continue
                
                # Check if string represents a number
                try:
                    clean_val = re.sub(r'[,$%\s]', '', value_clean)
                    if clean_val:  # Not empty after cleaning
                        float(clean_val)
                        column_types[col]['types'].add('number')
                    else:
                        column_types[col]['types'].add('string')
                except (ValueError, AttributeError):
                    column_types[col]['types'].add('string')
            else:
                column_types[col]['types'].add('string')
    
    # Determine final types with safer logic
    final_column_types = {}
    for column, info in column_types.items():
        types = info['types']
        samples = [s for s in info['samples'] if s is not None]
        
        if not samples:  # All values are None
            final_column_types[column] = 'string'
            continue
        
        # Prioritize: date > number > string
        if 'date' in types:
            date_count = sum(1 for s in samples if isinstance(s, (datetime, date)) or 
                           (isinstance(s, str) and s.strip() and 
                            any(re.match(p, str(s).strip(), re.IGNORECASE) 
                                for p in [r'^\d{4}-\d{2}-\d{2}', r'^\d{2}/\d{2}/\d{4}'])))
            if len(samples) > 0 and date_count / len(samples) > 0.7:
                final_column_types[column] = 'date'
            else:
                final_column_types[column] = 'string'
        elif 'number' in types and 'string' not in types:
            final_column_types[column] = 'number'
        elif 'number' in types and 'string' in types:
            number_count = sum(1 for s in samples if isinstance(s, (int, float, Decimal)) or
                             (isinstance(s, str) and s.strip() and 
                              re.match(r'^[\d,.$%\s-]+$', s.strip())))
            if len(samples) > 0 and number_count / len(samples) > 0.8:
                final_column_types[column] = 'number'
            else:
                final_column_types[column] = 'string'
        else:
            final_column_types[column] = 'string'
    
    # Process all data with consistent structure
    processed_data = []
    for row in valid_data:
        processed_row = {}
        # Ensure all columns exist in every row
        for col in all_columns:
            value = row.get(col, None)  # Safe access
            target_type = final_column_types.get(col, 'string')
            
            if value is None:
                processed_row[col] = None if target_type == 'string' else (0 if target_type == 'number' else None)
            elif target_type == 'string':
                processed_row[col] = str(value)
            elif target_type == 'number':
                try:
                    if isinstance(value, (Decimal, int, float)):
                        processed_row[col] = float(value)
                    elif isinstance(value, str):
                        clean_val = re.sub(r'[,$%\s]', '', value.strip())
                        processed_row[col] = float(clean_val) if clean_val else 0.0
                    else:
                        processed_row[col] = 0.0
                except (ValueError, AttributeError):
                    processed_row[col] = 0.0
            elif target_type == 'date':
                if isinstance(value, datetime):
                    processed_row[col] = f"Date({value.year}, {value.month-1}, {value.day})"
                elif isinstance(value, date):
                    processed_row[col] = f"Date({value.year}, {value.month-1}, {value.day})"
                elif isinstance(value, str):
                    try:
                        from dateutil import parser
                        parsed_date = parser.parse(value)
                        processed_row[col] = f"Date({parsed_date.year}, {parsed_date.month-1}, {parsed_date.day})"
                    except:
                        processed_row[col] = str(value)
                        final_column_types[col] = 'string'
                else:
                    processed_row[col] = str(value)
            else:
                processed_row[col] = str(value) if value is not None else ''
        
        processed_data.append(processed_row)
    
    # Final safety conversion
    processed_data = convert_decimal_to_float(processed_data)
    final_column_types = convert_decimal_to_float(final_column_types)
    
    return processed_data, final_column_types

# NEW: Smart chart type selection based on data
def determine_optimal_chart_type(data, column_types):
    """Enhanced chart type selection with better logic"""
    if not data or not column_types:
        return "BarChart"
    
    string_cols = [col for col, dtype in column_types.items() if dtype == 'string']
    number_cols = [col for col, dtype in column_types.items() if dtype == 'number']
    date_cols = [col for col, dtype in column_types.items() if dtype == 'date']
    
    total_cols = len(column_types)
    data_count = len(data)
    
    # Enhanced decision logic
    
    # Time series data (dates + numbers)
    if date_cols and number_cols:
        return "LineChart"
    
    # Single categorical with single numeric - good for pie charts (small datasets)
    if len(string_cols) == 1 and len(number_cols) == 1 and data_count <= 12:
        # Check if the string column has reasonable categories (not too many unique values)
        unique_categories = len(set(str(row[string_cols[0]]) for row in data))
        if unique_categories <= 8:
            return "PieChart"
        else:
            return "BarChart"
    
    # Multiple numbers, no strings - scatter plot for correlation analysis
    if len(number_cols) >= 2 and len(string_cols) == 0:
        return "ScatterChart"
    
    # Categorical data with numbers - bar charts are best
    if string_cols and number_cols:
        # For many categories or large datasets, use BarChart
        if data_count > 12:
            return "BarChart"
        
        # Check category distribution
        if string_cols:
            unique_categories = len(set(str(row[string_cols[0]]) for row in data))
            if unique_categories > 8:
                return "BarChart"
            elif unique_categories <= 5 and data_count <= 10:
                return "PieChart"
            else:
                return "BarChart"
    
    # Only strings - create a count chart
    if string_cols and not number_cols:
        return "BarChart"
    
    # Only numbers - not ideal for charts, but use bar chart with indices
    if number_cols and not string_cols:
        return "BarChart"
    
    # Default fallback
    return "BarChart"

# SQL Optimization Prompt

# Enhanced SQL optimization prompt with better error prevention
sql_optimization_prompt = PromptTemplate.from_template("""
Based on the database schema, description, sample SQL query, and owner_id, write a SINGLE, VALID SQL query.

CRITICAL SQL REQUIREMENTS: 
- on the users table i dont have anything like full_name only name
- use only the schema provided avoid suggesting please, thats more important
- always state the platform the data is from, either trello or linear or jira
- note you have trello and linear as source, so make sure you know what you're doing, so your response should be accurate across these platform
- include trello and linear when fetching , make give repsonse labeled by platform either linear or trello
- mostly limit data if its too long
- please your labels should be meaningful and descriptive, i mean what you're fetching or querying should be meaningful
- do not generate column name on your own , only use the one from schema!!!!
- Return ONLY ONE complete SELECT statement
- Start with SELECT, end with semicolon
- NO explanatory text before or after the SQL
- NO multiple queries or statements
- Must be syntactically correct MySQL


##CRITICAL JOIN RULES:
- This is correct JOIN platform_users ON platform_users.user_id = tasks.user_id, this is not correct JOIN platform_users ON platform_users.user_id = users.id, always note that
- NEVER join platform_users directly on email
- CORRECT task joins: tasks -> users (via owner_id) -> platform_users (via user_id)
- EXAMPLE: FROM tasks 
           JOIN users ON users.id = tasks.owner_id 
           JOIN platform_users ON platform_users.user_id = tasks.user_id
- please pass distinct to your fetching of tasks as most of the tasks are duplicare
- Always filter by: WHERE platform_users.email = '{user_email}' AND users.workspace = '{workspace}'
- Always add: WHERE tasks.is_deleted IS NULL (to exclude deleted tasks)


CURRENT DATE CONTEXT:
- Today's date is: {current_date}
- Current year is: {current_year}
- Use DYNAMIC date functions, NEVER hardcoded years

DATE FILTERING EXAMPLES:
- This year: WHERE YEAR(created_at) = YEAR(CURDATE())
- Last month: WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
- This month: WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())
- Last 30 days: WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)

SQL SYNTAX RULES:
- use only column given in the schema, do not generate or form new columns on your own!!!!
- Always use proper JOIN syntax: LEFT JOIN table ON condition
- Group by all non-aggregate columns in SELECT
- Use proper column aliases
- Ensure all WHERE conditions are valid
- End with semicolon

CONTEXT AWARENESS:
- Status types: "Done", "In Progress", "Backlog", "Todo"
- Return full data, don't count unless specifically asked
- Always LEFT JOIN projects (most IDs are null)
- Always filter by owner_id where applicable
- Include user/team/project names in results when joining

FORBIDDEN:
- Hardcoded years (2023, 2024, etc.)
- Multiple SQL statements
- Explanatory text
- Invalid syntax
- Columns not in schema

Database Schema:
{schema}

Chat History: {history}

Description: {description}
Sample SQL Query: {sample_sql}
Owner ID: {owner_id}

Return ONLY a single, valid SQL query:
""")

sql_optimization_chain = LLMChain(llm=llm, prompt=sql_optimization_prompt)

# UPDATED: Template analyzer prompt with proper variable handling
# Replace your template_analyzer_prompt with this enhanced version:

template_analyzer_prompt = PromptTemplate.from_template("""
You are a JSON generator that creates Google Charts configuration templates and you give 100% accurate responses. You must return ONLY valid JSON with no extra text, explanations, or formatting.


IMPORTANT: Analyze the data structure and content to determine the BEST chart type automatically.
 - always label the platform the data is from, either trello or linear or jira
 - note you have trello and linear as source, so make sure you know what you're doing, so your response should be splitted across these platform
 - include trello and linear when fetching , make give repsonse labeled by platform either linear or trello
 -  Max 3 template you're allowed to generate
 - mostly limit data if its too long
 - please your labels should be meaningful and descriptive
CHART TYPE SELECTION RULES:
- LineChart: For time series data (dates/timestamps with numeric values), trends over time
- BarChart: For categorical comparisons, counts, rankings, comparing discrete categories  
- PieChart: For proportional data with few categories (2-8), showing parts of a whole
- ScatterChart: For correlation analysis between two numeric variables
- ColumnChart: Alternative to BarChart for vertical display
- AreaChart: For cumulative data or filled area under trends

ANALYZE THE DATA:
- Look at column types and actual values
- Consider data relationships and what story it tells
- Choose the chart type that best represents the data meaning
- Don't default to BarChart - be intelligent about selection

For CHART template, return this EXACT structure:
{{
  "template_type": "chart",
  "structure": {{
    "chartType": "CHOOSE_BEST_TYPE_BASED_ON_DATA",
    "options": {{
      "title": "{description}",
      "hAxis": {{"title": "Smart X Axis Label Based On Data"}},
      "vAxis": {{"title": "Smart Y Axis Label Based On Data"}},
      "legend": {{"position": "bottom"}},
      "animation": {{
        "startup": true,
        "duration": 1000,
        "easing": "out"
      }},
      "width": "100%",
      "height": 400,
      "backgroundColor": "transparent"
    }},
    "columns": [
      {{"type": "DETECT_FROM_DATA", "label": "GENERATE_MEANINGFUL_LABEL", "data": "actual_column_name"}},
      {{"type": "DETECT_FROM_DATA", "label": "GENERATE_MEANINGFUL_LABEL", "data": "actual_column_name"}}
    ],
    "chart_data": [],
    "column_types": {{}}
  }}
}}

For TABLE template:
{{
  "template_type": "table", 
  "structure": {{
    "columns": [
      {{"data": "column_name", "title": "Display Name"}}
    ],
    "data": []
  }}
}}

DECISION LOGIC:
- Analyze the ACTUAL data content, not just types
- If data shows clear trends, relationships, or patterns: use appropriate chart
- If data is complex, has many columns, or mixed types: use table
- Consider what the user is asking for in the description

Description: {description}
Sample Data: {sample_data}
Data Count: {data_count}
Column Types: {column_types}

Return ONLY the JSON - no markdown, no explanations:
""")


template_chain = LLMChain(llm=llm, prompt=template_analyzer_prompt)

def getTableTemplate(temp_data, owner_id):
    """Function to generate table-only template with proper data processing"""
    try:
        description = temp_data['description']
        template_id = temp_data['id']
        sample_sql_query = temp_data['sql']
        chat_id = temp_data.get('chat_id')
        
        logger.info(f"Generating TABLE template for owner_id {owner_id}, template_id {template_id}")
        
        # Get chat history for context
        chat_history = get_chat_history(chat_id) if chat_id else ""
        
        # Get database schema
        schema_info = db.get_table_info()
        schema_str = json.dumps(schema_info, indent=2)
        
        # Optimize the SQL query using AI
        try:
            logger.info(f"All schema {schema_str}:\n{schema_str}")
            optimized_sql = sql_optimization_chain.run(
                schema=schema_str,
                description=description,
                sample_sql=sample_sql_query,
                owner_id=owner_id,
                history=chat_history
            )
            optimized_sql = clean_sql(optimized_sql.strip())
            logger.info(f"AI-Optimized SQL Query for owner_id {owner_id}:\n{optimized_sql}")
            
        except Exception as e:
            logger.error(f"SQL optimization error: {str(e)}")
            # Fallback to sample SQL with owner_id filter added
            optimized_sql = clean_sql(sample_sql_query.strip())
            if "WHERE" in optimized_sql.upper():
                optimized_sql = optimized_sql.replace("WHERE", f"WHERE owner_id = '{owner_id}' AND")
            else:
                optimized_sql += f" WHERE owner_id = '{owner_id}'"
        
        # Execute the optimized SQL to get data using database manager
        all_data = db_manager.execute_query(optimized_sql)
        
        logger.info(f"Retrieved {len(all_data)} rows from optimized query")
        
        if not all_data:
            return {
                "template_type": "table",
                "id": template_id,
                "data_count": 0,
                "sql_query": optimized_sql,
                "structure": {
                    "message": "No data available for this owner",
                    "columns": [],
                    "data": []
                }
            }
        
        # NEW: Process data for consistency
        processed_data, column_types = preprocess_data_for_google_charts(all_data)
        
        # Get columns from processed data
        columns = list(column_types.keys()) if column_types else []
        
        # Return simple table structure
        return {
            "template_type": "table",
            "id": template_id,
            "data_count": len(processed_data),
            "sql_query": optimized_sql,
            "owner_id": owner_id,
            "structure": {
                "columns": [{"data": col, "title": col.replace('_', ' ').title()} for col in columns],
                "data": processed_data
            }
        }
        
    except Exception as e:
        logger.error(f"Error generating table template: {str(e)}")
        return {"error": str(e)}

@app.route('/generate-template', methods=['POST'])
def generate_template():
    try:
        data = request.get_json()
        
        # Validate input data structure
        if not isinstance(data, dict):
            return jsonify({"error": "Invalid request format"}), 400
            
        from datetime import date
        current_date = date.today()
        current_year = current_date.year
        
        # Get the type parameter from query string
        request_type = request.args.get('type', 'default')
        
        description = str(data['description'])
        template_id = str(data['id'])
        sample_sql_query = str(data['sql'])
        chat_id_enc = str(data['chat_id'])
        
        # Handle different request types
        if request_type == 'invited':
            # For invited type, use staff_id (encrypted email)
            staff_id_enc = str(data.get('staff_id', ''))
            if not staff_id_enc:
                return jsonify({"error": "Missing staff_id for invited type"}), 400
            
            # Decrypt staff_id to get email
            try:
                staff_email = decrypt(staff_id_enc)
                if not staff_email:
                    return jsonify({"error": "Invalid encrypted staff_id"}), 400
            except Exception as e:
                return jsonify({"error": "Staff ID decryption failed"}), 400
            
            # For invited type, we don't need owner_id
            owner_id = None
            user_email = staff_email
            
        else:
            # For default type, use owner_id
            owner_id_enc = str(data.get('owner_id', ''))
            if not owner_id_enc:
                return jsonify({"error": "Missing owner_id for default type"}), 400
            
            # Decrypt owner_id
            try:
                owner_id = decrypt(owner_id_enc)
                if not owner_id:
                    return jsonify({"error": "Invalid encrypted owner_id"}), 400
            except Exception as e:
                return jsonify({"error": "Owner ID decryption failed"}), 400
            
            user_email = None  # Will be set from database if needed
        
        chat_id = chat_id_enc
        
        logger.info(f"Generating template for type: {request_type}, template_id: {template_id}")
        
        # Get chat history safely
        chat_history = ""
        try:
            chat_history = get_chat_history(chat_id)
        except Exception as e:
            logger.warning(f"Could not get chat history: {str(e)}")
        
        # Get database schema safely
        schema_str = "{}"
        try:
            schema_info = db.get_table_info()
            schema_str = json.dumps(schema_info, indent=2) if schema_info else "{}"
        except Exception as e:
            logger.warning(f"Could not get schema: {str(e)}")
        
        # Create appropriate optimization prompt based on type
        if request_type == 'invited':
            # Use the invited-specific SQL prompt
            invited_sql_prompt = PromptTemplate.from_template("""
Based on the database schema, user query, and staff email, write a SQL query to get the data for invited users.

PULL MOST FROM tasks TABLE OR IF YOU WANNA JOIN FROM IT, TO GET ACCURATE RESPONSE

CONTEXT AWARENESS:
- on the users table i dont have anything like full_name only name
- use only the schema provided avoid suggesting please, thats more important
- note you have trello and linear as source, so make sure you know what you're doing
- always specify where a data is from either linear or trello
- always left join anything related to project_id or project
- never search a table that is not in the schema, i mean never and ever try to do this
- provide clean sql , and working , with zero chance of error
- nothing like task_name, please check column very well before performing queries
- Track and reuse last-used table if user continues in same thread.
- Always assume KPIs and performance analysis refer to the `tasks` table unless stated otherwise.
- most of your query should be from tasks and refer to last chat history

##CRITICAL JOIN RULES FOR INVITED TYPE:
- This is correct JOIN platform_users ON platform_users.user_id = tasks.user_id
- NEVER join platform_users directly on email in tasks context
- CORRECT task joins for invited users: tasks -> platform_users (via user_id)
- EXAMPLE: FROM tasks JOIN platform_users ON platform_users.user_id = tasks.user_id
- please pass distinct to your fetching of tasks as most of the tasks are duplicate
- Always filter by: WHERE platform_users.email = '{staff_email}'
- Always add: WHERE tasks.is_deleted IS NULL (to exclude deleted tasks)
- You can also use platform_users.owner_id for additional joins if needed

IMPORTANT RULES:
- when joining user make sure you select the user name for response, same as teams and projects, the names are important please
- For invited user queries, use the platform_users table as primary filter
- also try to get who is lacking behind
- Use WHERE LIKE for username-related queries
- Return plain text SQL query without backticks
- For "list users" or "show users", select from platform_users table
- Common user fields: id, email, userid, platform_id, fullname, source, created_at, updated_at
- Status types include "Done", "In Progress", "Backlog", and "Todo"
- avoid using update_at for greater than duration query
- team id is not platform id

CURRENT DATE CONTEXT:
- Today's date is: {current_date}
- Current year is: {current_year}
- Use DYNAMIC date functions, NEVER hardcoded years

Database Schema: {schema}
Chat History: {history}
User query: {description}
Staff Email: {staff_email}

Return only the SQL query:
""")
            
            invited_sql_chain = LLMChain(llm=llm, prompt=invited_sql_prompt)
            
            # Optimize SQL for invited type
            try:
                optimized_sql = invited_sql_chain.run(
                    schema=schema_str,
                    description=description,
                    history=chat_history,
                    staff_email=user_email,
                    current_date=current_date.strftime('%Y-%m-%d'),
                    current_year=current_year
                )
                optimized_sql = clean_sql(optimized_sql.strip())
                logger.info(f"AI-Optimized SQL Query for invited type:\n{optimized_sql}")
                
            except Exception as e:
                logger.error(f"SQL optimization error for invited type: {str(e)}")
                # Safe fallback for invited type
                try:
                    optimized_sql = clean_sql(sample_sql_query.strip())
                    if "WHERE" in optimized_sql.upper():
                        optimized_sql = optimized_sql.replace("WHERE", f"WHERE platform_users.email = '{user_email}' AND")
                    else:
                        optimized_sql += f" WHERE platform_users.email = '{user_email}'"
                except Exception as fallback_error:
                    logger.error(f"Fallback SQL error: {str(fallback_error)}")
                    return jsonify({"error": "SQL processing failed"}), 500
        
        else:
            # Use the original optimization for default type
            try:
                optimized_sql = sql_optimization_chain.run(
                    schema=schema_str,
                    description=description,
                    sample_sql=sample_sql_query,
                    owner_id=owner_id,
                    history=chat_history,
                    current_date=current_date.strftime('%Y-%m-%d'),
                    current_year=current_year
                )
                optimized_sql = clean_sql(optimized_sql.strip())
                logger.info(f"AI-Optimized SQL Query for default type:\n{optimized_sql}")
                
            except Exception as e:
                logger.error(f"SQL optimization error: {str(e)}")
                # Safe fallback for default type
                try:
                    optimized_sql = clean_sql(sample_sql_query.strip())
                    if "WHERE" in optimized_sql.upper():
                        optimized_sql = optimized_sql.replace("WHERE", f"WHERE owner_id = '{owner_id}' AND")
                    else:
                        optimized_sql += f" WHERE owner_id = '{owner_id}'"
                except Exception as fallback_error:
                    logger.error(f"Fallback SQL error: {str(fallback_error)}")
                    return jsonify({"error": "SQL processing failed"}), 500
        
        # Execute SQL safely
        all_data = []
        try:
            all_data = db_manager.execute_query(optimized_sql)
            if not isinstance(all_data, list):
                all_data = []
        except Exception as e:
            logger.error(f"Database query error: {str(e)}")
            return jsonify({"error": "Database query failed"}), 500
        
        logger.info(f"Retrieved {len(all_data)} rows from query")
        
        # Handle empty data
        if not all_data:
            template_config = {
                "template_type": "table",
                "id": template_id,
                "data_count": 0,
                "sql_query": optimized_sql,
                "request_type": request_type,
                "structure": {
                    "message": f"No data available for this {'staff member' if request_type == 'invited' else 'owner'}",
                    "columns": [],
                    "data": []
                }
            }
            save_template_to_db(template_config, template_id, chat_id)
            return jsonify(template_config)
        
        # Process data safely
        processed_data = []
        column_types = {}
        try:
            processed_data, column_types = preprocess_data_for_google_charts(all_data)
        except Exception as e:
            logger.error(f"Data processing error: {str(e)}")
            # Create safe fallback structure
            if all_data and isinstance(all_data[0], dict):
                processed_data = all_data
                column_types = {key: 'string' for key in all_data[0].keys()}
            else:
                processed_data = []
                column_types = {}
        
        # Create template structure safely
        try:
            template_config = create_safe_template_structure(processed_data, column_types, description)
            
            # Add metadata
            template_config['id'] = template_id
            template_config['data_count'] = len(processed_data)
            template_config['sql_query'] = optimized_sql
            template_config['request_type'] = request_type
            if request_type == 'invited':
                template_config['staff_email'] = user_email
            else:
                template_config['owner_id'] = owner_id
            
            # Save to database
            save_template_to_db(template_config, template_id, chat_id)
            
            logger.info(f"Successfully generated template for template_id {template_id}, type: {request_type}")
            return jsonify(template_config)
            
        except Exception as e:
            logger.error(f"Template creation error: {str(e)}")
            # Ultimate fallback
            fallback_config = {
                "template_type": "table",
                "id": template_id,
                "data_count": len(processed_data) if processed_data else 0,
                "sql_query": optimized_sql,
                "request_type": request_type,
                "structure": {
                    "message": "Template generation error - showing data as table",
                    "columns": [{"data": key, "title": key.replace('_', ' ').title()} 
                              for key in (column_types.keys() if column_types else [])],
                    "data": processed_data if processed_data else []
                }
            }
            save_template_to_db(fallback_config, template_id, chat_id)
            return jsonify(fallback_config)
        
    except Exception as e:
        logger.error(f"Critical error in generate_template: {str(e)}")
        return jsonify({
            "error": "Internal server error",
            "template_type": "table",
            "structure": {
                "message": "Service temporarily unavailable",
                "columns": [],
                "data": []
            }
        }), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)