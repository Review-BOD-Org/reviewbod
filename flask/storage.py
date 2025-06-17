import os
import json
from uuid import uuid4
from typing import Dict, List, Any, Optional, Union
from datetime import datetime
import faiss
import numpy as np

from langchain_core.documents import Document
from langchain_community.docstore.in_memory import InMemoryDocstore
from langchain_community.vectorstores import FAISS

# Updated import to fix deprecation warning
try:
    from langchain_huggingface import HuggingFaceEmbeddings
except ImportError:
    from langchain_community.embeddings import HuggingFaceEmbeddings

class TaskPlatformStorage:
    def __init__(self, user_id: str, persist_path: str = "/tmp/task_platform_index"):
        """
        Initialize TaskPlatformStorage with a global user ID
        
        Args:
            user_id: Global user identifier that all data will be tied to
            persist_path: Path to persist the vector store
        """
        if not user_id or not user_id.strip():
            raise ValueError("user_id is required and cannot be empty")
            
        self.user_id = user_id.strip()
        self.persist_path = f"{persist_path}_{self.user_id}"  # User-specific storage path
        self.embeddings = HuggingFaceEmbeddings(model_name="all-MiniLM-L6-v2")
        self.vector_store = self._initialize_vector_store()
        
        print(f"TaskPlatformStorage initialized for user: {self.user_id}")
    
    def _initialize_vector_store(self):
        """Initialize or load the vector store"""
        sample_embedding = self.embeddings.embed_documents(["sample text"])[0]
        embedding_dim = len(sample_embedding)
        
        if os.path.exists(self.persist_path):
            try:
                vector_store = FAISS.load_local(
                    folder_path=self.persist_path,
                    embeddings=self.embeddings,
                    allow_dangerous_deserialization=True
                )
                
                if vector_store.index.d != embedding_dim:
                    print(f"Dimension mismatch! Creating new index...")
                    index = faiss.IndexFlatL2(embedding_dim)
                    vector_store = FAISS(
                        embedding_function=self.embeddings,
                        index=index,
                        docstore=InMemoryDocstore(),
                        index_to_docstore_id={},
                    )
                else:
                    print(f"Loaded existing index with {vector_store.index.ntotal} documents for user {self.user_id}")
                    
            except Exception as e:
                print(f"Error loading index: {e}")
                index = faiss.IndexFlatL2(embedding_dim)
                vector_store = FAISS(
                    embedding_function=self.embeddings,
                    index=index,
                    docstore=InMemoryDocstore(),
                    index_to_docstore_id={},
                )
        else:
            print(f"Creating new index for user {self.user_id}...")
            index = faiss.IndexFlatL2(embedding_dim)
            vector_store = FAISS(
                embedding_function=self.embeddings,
                index=index,
                docstore=InMemoryDocstore(),
                index_to_docstore_id={},
            )
        
        return vector_store
    
    def _clean_data(self, data: Any) -> Any:
        """Convert null/None values and clean data recursively"""
        if data is None or data == "null":
            return None
        elif isinstance(data, dict):
            return {key: self._clean_data(value) for key, value in data.items() if value is not None and value != "null"}
        elif isinstance(data, list):
            return [self._clean_data(item) for item in data if item is not None and item != "null"]
        elif isinstance(data, str):
            # Clean empty strings
            return data.strip() if data.strip() else None
        else:
            return data
    
    def _generate_searchable_text(self, data: Dict[str, Any], content_type: str) -> str:
        """Generate searchable text content from structured data"""
        text_parts = []
        
        # Always include user context
        text_parts.append(f"User {self.user_id}")
        
        if content_type == "user_profile":
            name = data.get("name", "")
            email = data.get("email", "")
            text_parts.append(f"Profile: {name} ({email})")
            
            # Add team information
            teams = data.get("teams", [])
            if teams:
                team_names = [team.get("name", "") for team in teams if team.get("name")]
                if team_names:
                    text_parts.append(f"Teams: {', '.join(team_names)}")
            
            # Add role/position if available
            if data.get("role"):
                text_parts.append(f"Role: {data['role']}")
                
        elif content_type == "task":
            title = data.get("title", "")
            status = data.get("status", data.get("state", {}).get("name", ""))
            text_parts.append(f"Task: {title}")
            
            if status:
                text_parts.append(f"Status: {status}")
            
            # Add description if available
            description = data.get("description", "")
            if description:
                text_parts.append(f"Description: {description}")
            
            # Add project information
            project = data.get("project", {})
            if project and project.get("name"):
                text_parts.append(f"Project: {project['name']}")
            
            # Add assignee information
            assignee = data.get("assignee", {})
            if assignee and assignee.get("name"):
                text_parts.append(f"Assigned to: {assignee['name']}")
            
            # Add labels/tags
            labels = data.get("labels", data.get("tags", []))
            if labels:
                label_names = [label.get("name", str(label)) for label in labels if label]
                if label_names:
                    text_parts.append(f"Labels: {', '.join(label_names)}")
                    
        elif content_type == "project":
            name = data.get("name", "")
            text_parts.append(f"Project: {name}")
            
            description = data.get("description", "")
            if description:
                text_parts.append(f"Description: {description}")
            
            status = data.get("status", data.get("state", ""))
            if status:
                text_parts.append(f"Status: {status}")
                
        elif content_type == "team":
            name = data.get("name", "")
            text_parts.append(f"Team: {name}")
            
            description = data.get("description", "")
            if description:
                text_parts.append(f"Description: {description}")
            
            # Add member count if available
            members = data.get("members", [])
            if members:
                text_parts.append(f"Members: {len(members)} people")
                
        elif content_type == "note":
            title = data.get("title", "")
            content = data.get("content", data.get("text", ""))
            if title:
                text_parts.append(f"Note: {title}")
            if content:
                text_parts.append(f"Content: {content}")
                
        elif content_type == "meeting":
            title = data.get("title", "")
            date = data.get("date", data.get("scheduled_date", ""))
            text_parts.append(f"Meeting: {title}")
            if date:
                text_parts.append(f"Date: {date}")
            
            # Add participants
            participants = data.get("participants", data.get("attendees", []))
            if participants:
                participant_names = [p.get("name", str(p)) for p in participants if p]
                if participant_names:
                    text_parts.append(f"Participants: {', '.join(participant_names)}")
        
        return " | ".join(text_parts)
    
    def store_data(self, 
                   data: Dict[str, Any], 
                   content_type: str,
                   additional_metadata: Optional[Dict[str, Any]] = None) -> str:
        """
        Store data in the vector store (automatically tied to the global user ID)
        
        Args:
            data: The data to store (dict)
            content_type: Type of content ('user_profile', 'task', 'project', 'team', 'note', 'meeting', etc.)
            additional_metadata: Additional metadata to include
            
        Returns:
            Document ID
        """
        # Clean the data
        cleaned_data = self._clean_data(data)
        
        # Generate searchable text
        searchable_text = self._generate_searchable_text(cleaned_data, content_type)
        
        if not searchable_text.strip():
            raise ValueError("Generated searchable text is empty")
        
        # Prepare metadata - ALWAYS include global user_id
        metadata = {
            "global_user_id": self.user_id,  # Global user ID for all data
            "content_type": content_type,
            "timestamp": datetime.now().isoformat(),
            "data_id": cleaned_data.get("id", str(uuid4())),
        }
        
        # Add specific metadata based on content type
        if content_type == "user_profile":
            metadata.update({
                "profile_id": cleaned_data.get("id"),
                "profile_name": cleaned_data.get("name"),
                "profile_email": cleaned_data.get("email"),
                "teams": [team.get("name") for team in cleaned_data.get("teams", []) if team.get("name")],
                "role": cleaned_data.get("role"),
            })
            
        elif content_type == "task":
            metadata.update({
                "task_id": cleaned_data.get("id"),
                "task_title": cleaned_data.get("title"),
                "task_status": cleaned_data.get("status", cleaned_data.get("state", {}).get("name")),
                "project_id": cleaned_data.get("project", {}).get("id") if cleaned_data.get("project") else None,
                "project_name": cleaned_data.get("project", {}).get("name") if cleaned_data.get("project") else None,
                "assignee_id": cleaned_data.get("assignee", {}).get("id") if cleaned_data.get("assignee") else None,
                "assignee_name": cleaned_data.get("assignee", {}).get("name") if cleaned_data.get("assignee") else None,
                "priority": cleaned_data.get("priority"),
                "due_date": cleaned_data.get("due_date", cleaned_data.get("dueDate")),
            })
            
        elif content_type == "project":
            metadata.update({
                "project_id": cleaned_data.get("id"),
                "project_name": cleaned_data.get("name"),
                "project_status": cleaned_data.get("status", cleaned_data.get("state")),
                "team_id": cleaned_data.get("team", {}).get("id") if cleaned_data.get("team") else None,
                "team_name": cleaned_data.get("team", {}).get("name") if cleaned_data.get("team") else None,
            })
            
        elif content_type == "team":
            metadata.update({
                "team_id": cleaned_data.get("id"),
                "team_name": cleaned_data.get("name"),
                "member_count": len(cleaned_data.get("members", [])),
            })
            
        elif content_type == "note":
            metadata.update({
                "note_id": cleaned_data.get("id"),
                "note_title": cleaned_data.get("title"),
                "note_category": cleaned_data.get("category"),
            })
            
        elif content_type == "meeting":
            metadata.update({
                "meeting_id": cleaned_data.get("id"),
                "meeting_title": cleaned_data.get("title"),
                "meeting_date": cleaned_data.get("date", cleaned_data.get("scheduled_date")),
                "meeting_type": cleaned_data.get("type"),
            })
        
        # Add additional metadata if provided
        if additional_metadata:
            cleaned_additional = self._clean_data(additional_metadata)
            metadata.update(cleaned_additional)
        
        # Remove None values from metadata
        metadata = {k: v for k, v in metadata.items() if v is not None}
        
        # Create document
        doc = Document(page_content=searchable_text, metadata=metadata)
        doc_id = str(uuid4())
        
        # Add to vector store
        self.vector_store.add_documents([doc], ids=[doc_id])
        self.vector_store.save_local(self.persist_path)
        
        print(f"Stored {content_type} data for user {self.user_id}: {doc_id}")
        
        return doc_id
    
    def bulk_store_data(self, data_list: List[Dict[str, Any]], 
                       content_type: str,
                       additional_metadata: Optional[Dict[str, Any]] = None) -> List[str]:
        """
        Store multiple data items at once (all tied to the global user ID)
        
        Args:
            data_list: List of data dictionaries to store
            content_type: Type of content
            additional_metadata: Additional metadata for all items
            
        Returns:
            List of document IDs
        """
        doc_ids = []
        
        for data in data_list:
            try:
                doc_id = self.store_data(data, content_type, additional_metadata)
                doc_ids.append(doc_id)
            except Exception as e:
                print(f"Error storing data item: {e}")
                continue
        
        print(f"Successfully stored {len(doc_ids)} out of {len(data_list)} {content_type} items for user {self.user_id}")
        return doc_ids
    
    def search(self, 
               query: str = "",
               filters: Optional[Dict[str, Any]] = None,
               content_type: Optional[str] = None,
               k: int = 5,
               search_type: str = "similarity",
               similarity_threshold: float = 0.5) -> List[Document]:
        """
        Search stored data (automatically filtered to current user's data)
        
        Args:
            query: Search query (can be empty for filter-only search)
            filters: Dictionary of metadata filters
            content_type: Filter by content type
            k: Number of results to return
            search_type: Search type ('similarity' or 'mmr')
            similarity_threshold: Minimum similarity score (0.0 to 1.0, lower is more similar for L2 distance)
            
        Returns:
            List of matching documents (only for current user)
        """
        if not query.strip():
            # If no query provided, return filtered results without semantic search
            return self._get_filtered_documents(filters, content_type, k)
        
        # First, get all user documents for filtering
        all_user_docs = self._get_all_user_documents()
        
        if not all_user_docs:
            return []
        
        # Apply content type and other filters first
        filtered_docs = []
        for doc in all_user_docs:
            # Check content type filter
            if content_type and doc.metadata.get("content_type") != content_type:
                continue
            
            # Check other filters
            if filters:
                match = True
                for key, value in filters.items():
                    doc_value = doc.metadata.get(key)
                    
                    # Handle list matching (e.g., teams)
                    if isinstance(doc_value, list) and not isinstance(value, list):
                        if value not in doc_value:
                            match = False
                            break
                    elif isinstance(value, list) and isinstance(doc_value, list):
                        # Check if any values in the filter list match doc values
                        if not any(v in doc_value for v in value):
                            match = False
                            break
                    elif doc_value != value:
                        match = False
                        break
                
                if not match:
                    continue
            
            filtered_docs.append(doc)
        
        if not filtered_docs:
            return []
        
        # If we have filtered documents, perform similarity search on them
        return self._semantic_search_on_documents(query, filtered_docs, k, similarity_threshold)
    
    def _get_all_user_documents(self) -> List[Document]:
        """Get all documents for the current user"""
        try:
            total_docs = self.vector_store.index.ntotal
            if total_docs == 0:
                return []
            
            # Get all documents
            all_docs = self.vector_store.similarity_search("", k=total_docs)
            
            # Filter to current user only
            user_docs = [doc for doc in all_docs if doc.metadata.get("global_user_id") == self.user_id]
            return user_docs
        except Exception as e:
            print(f"Error getting user documents: {e}")
            return []
    
    def _get_filtered_documents(self, filters: Optional[Dict[str, Any]] = None,
                              content_type: Optional[str] = None, k: int = 5) -> List[Document]:
        """Get filtered documents without semantic search"""
        all_user_docs = self._get_all_user_documents()
        
        filtered_results = []
        for doc in all_user_docs:
            # Check content type filter
            if content_type and doc.metadata.get("content_type") != content_type:
                continue
            
            # Check other filters
            if filters:
                match = True
                for key, value in filters.items():
                    doc_value = doc.metadata.get(key)
                    
                    if isinstance(doc_value, list) and not isinstance(value, list):
                        if value not in doc_value:
                            match = False
                            break
                    elif isinstance(value, list) and isinstance(doc_value, list):
                        if not any(v in doc_value for v in value):
                            match = False
                            break
                    elif doc_value != value:
                        match = False
                        break
                
                if not match:
                    continue
            
            filtered_results.append(doc)
            
            if len(filtered_results) >= k:
                break
        
        return filtered_results
    
    def _semantic_search_on_documents(self, query: str, documents: List[Document], 
                                    k: int, similarity_threshold: float) -> List[Document]:
        """Perform semantic search on a specific set of documents"""
        if not documents:
            return []
        
        try:
            # Get query embedding
            query_embedding = self.embeddings.embed_query(query)
            
            # Get embeddings for all documents
            doc_texts = [doc.page_content for doc in documents]
            doc_embeddings = self.embeddings.embed_documents(doc_texts)
            
            # Calculate similarities (using cosine similarity)
            query_embedding = np.array(query_embedding)
            doc_embeddings = np.array(doc_embeddings)
            
            # Normalize embeddings for cosine similarity
            query_norm = query_embedding / np.linalg.norm(query_embedding)
            doc_norms = doc_embeddings / np.linalg.norm(doc_embeddings, axis=1, keepdims=True)
            
            # Calculate cosine similarities
            similarities = np.dot(doc_norms, query_norm)
            
            # Create list of (document, similarity) pairs
            doc_similarity_pairs = list(zip(documents, similarities))
            
            # Filter by similarity threshold and sort by similarity (highest first)
            filtered_pairs = [(doc, sim) for doc, sim in doc_similarity_pairs if sim >= similarity_threshold]
            filtered_pairs.sort(key=lambda x: x[1], reverse=True)
            
            # Return top k documents
            result_docs = [doc for doc, sim in filtered_pairs[:k]]
            
            print(f"Semantic search for '{query}': {len(result_docs)} results above threshold {similarity_threshold}")
            for i, (doc, sim) in enumerate(filtered_pairs[:k]):
                print(f"  {i+1}. Similarity: {sim:.3f} - {doc.page_content[:100]}...")
            
            return result_docs
            
        except Exception as e:
            print(f"Error in semantic search: {e}")
            # Fallback to simple text matching
            return self._simple_text_search(query, documents, k)
    
    def _simple_text_search(self, query: str, documents: List[Document], k: int) -> List[Document]:
        """Simple text-based search as fallback"""
        query_lower = query.lower()
        matches = []
        
        for doc in documents:
            content_lower = doc.page_content.lower()
            if query_lower in content_lower:
                # Simple scoring based on frequency and position
                score = content_lower.count(query_lower)
                if content_lower.startswith(query_lower):
                    score += 2
                matches.append((doc, score))
        
        # Sort by score (highest first) and return top k
        matches.sort(key=lambda x: x[1], reverse=True)
        return [doc for doc, score in matches[:k]]
    
    def search_tasks(self, query: str = "", status: str = None, project_id: str = None, k: int = 5) -> List[Document]:
        """Search user's tasks with optional filters"""
        filters = {}
        if status:
            filters["task_status"] = status
        if project_id:
            filters["project_id"] = project_id
            
        return self.search(
            query=query,
            content_type="task",
            filters=filters,
            k=k
        )
    
    def search_projects(self, query: str = "", status: str = None, k: int = 5) -> List[Document]:
        """Search user's projects with optional filters"""
        filters = {}
        if status:
            filters["project_status"] = status
            
        return self.search(
            query=query,
            content_type="project",
            filters=filters,
            k=k
        )
    
    def search_notes(self, query: str = "", category: str = None, k: int = 5) -> List[Document]:
        """Search user's notes with optional filters"""
        filters = {}
        if category:
            filters["note_category"] = category
            
        return self.search(
            query=query,
            content_type="note",
            filters=filters,
            k=k
        )
    
    def get_user_stats(self) -> Dict[str, Any]:
        """Get storage statistics for the current user"""
        total_docs = self.vector_store.index.ntotal
        
        # Count by content type for this user
        all_docs = self.vector_store.similarity_search("", k=total_docs) if total_docs > 0 else []
        user_docs = [doc for doc in all_docs if doc.metadata.get("global_user_id") == self.user_id]
        
        content_type_counts = {}
        for doc in user_docs:
            content_type = doc.metadata.get("content_type", "unknown")
            content_type_counts[content_type] = content_type_counts.get(content_type, 0) + 1
        
        return {
            "user_id": self.user_id,
            "total_user_documents": len(user_docs),
            "content_type_breakdown": content_type_counts,
            "storage_path": self.persist_path,
            "index_dimension": self.vector_store.index.d if total_docs > 0 else 0
        }
    
    def get_all_user_data(self, content_type: str = None) -> List[Document]:
        """Get all data for the current user, optionally filtered by content type"""
        return self.search(
            query="",
            content_type=content_type,
            k=1000  # Get a large number to retrieve all data
        )