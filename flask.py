from flask import Flask, request, jsonify
from app import TaskPlatformStorage  # Assuming your code is in task_platform_storage.py

app = Flask(__name__)

@app.route('/api/store-data', methods=['POST'])
def store_data():
    try:
        # Get JSON data from request body
        data = request.get_json()
        
        # Validate required fields
        user_id = data.get('user_id')
        data_list = data.get('data')
        content_type = data.get('content_type')
        additional_metadata = data.get('additional_metadata', {})

        if not user_id or not user_id.strip():
            return jsonify({
                'error': 'user_id is required and cannot be empty'
            }), 400
            
        if not data_list or not isinstance(data_list, list):
            return jsonify({
                'error': 'data must be a non-empty list of dictionaries'
            }), 400
            
        if not content_type or not content_type.strip():
            return jsonify({
                'error': 'content_type is required and cannot be empty'
            }), 400

        # Initialize storage with user_id
        storage = TaskPlatformStorage(user_id=user_id)
        
        # Store the data using bulk_store_data
        doc_ids = storage.bulk_store_data(data_list, content_type, additional_metadata)
        
        return jsonify({
            'user_id': user_id,
            'content_type': content_type,
            'stored_documents': len(doc_ids),
            'document_ids': doc_ids
        }), 200
        
    except Exception as e:
        return jsonify({
            'error': str(e)
        }), 500

@app.route('/api/user-data', methods=['POST'])
def get_all_user_data():
    try:
        # Get JSON data from request body
        data = request.get_json()
        user_id = data.get('user_id')
        
        if not user_id or not user_id.strip():
            return jsonify({
                'error': 'user_id is required and cannot be empty'
            }), 400
            
        # Initialize storage with user_id
        storage = TaskPlatformStorage(user_id=user_id)
        
        # Optional content_type filter
        content_type = data.get('content_type')
        
        # Get all user data
        documents = storage.get_all_user_data(content_type)
        
        # Format response
        results = []
        for doc in documents:
            results.append({
                'content': doc.page_content,
                'metadata': doc.metadata
            })
            
        return jsonify({
            'user_id': user_id,
            'total_results': len(results),
            'data': results
        }), 200
        
    except Exception as e:
        return jsonify({
            'error': str(e)
        }), 500

if __name__ == '__main__':
    app.run(debug=True)