import re
import json
from datetime import datetime, timedelta
from collections import defaultdict

class ChatHistorySummarizer:
    def __init__(self):
        # Initialize with lightweight summarization methods
        self.use_transformers = False
        self.summarizer = None
        
        # Try to initialize transformers if available
        try:
            from transformers import pipeline
            self.summarizer = pipeline(
                "summarization", 
                model="sshleifer/distilbart-cnn-12-6",
                device=-1,  # CPU
                max_length=100,
                min_length=20,
                do_sample=False
            )
            self.use_transformers = True
            print("✅ Using DistilBART for summarization")
        except ImportError:
            print("⚠️  Transformers not available, using extractive summarization")
    
    def preprocess_chat_messages(self, chat_history):
        """Clean and prepare chat messages for summarization"""
        processed_messages = []
        
        for msg in chat_history:
            content = msg.get('message', '')
            sender = msg.get('sender', 'user')
            timestamp = msg.get('timestamp', '')
            
            # Skip very short messages
            if len(content.strip()) < 10:
                continue
                
            # Clean the content
            content = self.clean_message_content(content)
            
            if content:
                processed_messages.append({
                    'content': content,
                    'sender': sender,
                    'timestamp': timestamp
                })
        
        return processed_messages
    
    def clean_message_content(self, content):
        """Clean message content for better summarization"""
        # Handle JSON responses
        if isinstance(content, dict):
            if 'content' in content:
                content = content['content']
            elif 'title' in content:
                content = content['title']
            else:
                content = str(content)
        
        # Basic text cleaning
        content = str(content).strip()
        content = re.sub(r'\s+', ' ', content)  # Normalize whitespace
        content = re.sub(r'[^\w\s.,!?-]', '', content)  # Remove special chars
        
        return content
    
    def extractive_summarization(self, text, max_sentences=3):
        """Simple extractive summarization using sentence scoring"""
        sentences = re.split(r'[.!?]+', text)
        sentences = [s.strip() for s in sentences if len(s.strip()) > 10]
        
        if len(sentences) <= max_sentences:
            return text
        
        # Score sentences by word frequency
        word_freq = defaultdict(int)
        words = re.findall(r'\b\w+\b', text.lower())
        
        for word in words:
            if len(word) > 3:  # Skip short words
                word_freq[word] += 1
        
        # Score sentences
        sentence_scores = []
        for sentence in sentences:
            score = 0
            words_in_sentence = re.findall(r'\b\w+\b', sentence.lower())
            
            for word in words_in_sentence:
                score += word_freq.get(word, 0)
            
            if len(words_in_sentence) > 0:
                score = score / len(words_in_sentence)
            
            sentence_scores.append((sentence, score))
        
        # Get top sentences
        sentence_scores.sort(key=lambda x: x[1], reverse=True)
        top_sentences = [s[0] for s in sentence_scores[:max_sentences]]
        
        return '. '.join(top_sentences) + '.'
    
    def summarize_conversation(self, chat_history, max_length=200):
        """Main method to summarize chat conversation"""
        if not chat_history:
            return "No conversation history available."
        
        # Preprocess messages
        processed_messages = self.preprocess_chat_messages(chat_history)
        
        if not processed_messages:
            return "No meaningful conversation content found."
        
        # Combine all messages into conversation flow
        conversation_text = self.build_conversation_context(processed_messages)
        
        # Summarize based on available method
        if self.use_transformers and len(conversation_text) > 100:
            try:
                summary = self.summarizer(
                    conversation_text, 
                    max_length=max_length,
                    min_length=50,
                    do_sample=False
                )
                return summary[0]['summary_text']
            except Exception as e:
                print(f"Transformers summarization failed: {e}")
                return self.extractive_summarization(conversation_text)
        else:
            return self.extractive_summarization(conversation_text)
    
    def build_conversation_context(self, messages):
        """Build readable conversation context"""
        context_parts = []
        
        # Group by conversation topics
        current_topic = []
        
        for msg in messages:
            content = msg['content']
            sender = msg['sender']
            
            # Add sender context
            if sender == 'user':
                current_topic.append(f"User asked: {content}")
            else:
                current_topic.append(f"Assistant responded: {content}")
        
        # Join all parts
        conversation_text = ' '.join(current_topic)
        
        # Limit length to prevent processing issues
        if len(conversation_text) > 2000:
            conversation_text = conversation_text[:2000] + "..."
        
        return conversation_text
    
    def get_conversation_insights(self, chat_history):
        """Get key insights from conversation"""
        processed_messages = self.preprocess_chat_messages(chat_history)
        
        if not processed_messages:
            return {
                'summary': "No conversation data",
                'key_topics': [],
                'user_queries': 0,
                'bot_responses': 0
            }
        
        # Count message types
        user_messages = [msg for msg in processed_messages if msg['sender'] == 'user']
        bot_messages = [msg for msg in processed_messages if msg['sender'] == 'bot']
        
        # Extract key topics (simple keyword extraction)
        all_text = ' '.join([msg['content'] for msg in processed_messages])
        key_topics = self.extract_key_topics(all_text)
        
        # Generate summary
        summary = self.summarize_conversation(chat_history)
        
        return {
            'summary': summary,
            'key_topics': key_topics,
            'user_queries': len(user_messages),
            'bot_responses': len(bot_messages),
            'conversation_length': len(processed_messages)
        }
    
    def extract_key_topics(self, text, max_topics=5):
        """Extract key topics from conversation"""
        # Simple keyword extraction
        words = re.findall(r'\b\w+\b', text.lower())
        
        # Filter out common words
        stop_words = {'the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 
                     'of', 'with', 'by', 'from', 'up', 'about', 'into', 'through', 
                     'during', 'before', 'after', 'above', 'below', 'between',
                     'user', 'bot', 'assistant', 'asked', 'responded', 'said'}
        
        word_freq = defaultdict(int)
        for word in words:
            if len(word) > 3 and word not in stop_words:
                word_freq[word] += 1
        
        # Get top topics
        top_words = sorted(word_freq.items(), key=lambda x: x[1], reverse=True)
        return [word for word, freq in top_words[:max_topics] if freq > 1]

# Usage in your Flask app
def get_chat_summary(user_id, chat_id, platform_id=None, limit=50):
    """Enhanced chat history function with summarization"""
    try:
        # Get chat history (your existing function)
        chat_history = get_chat_history(user_id, chat_id, platform_id, limit)
        
        if not chat_history:
            return {
                'summary': 'No chat history found',
                'insights': {},
                'messages': []
            }
        
        # Initialize summarizer
        summarizer = ChatHistorySummarizer()
        
        # Get insights
        insights = summarizer.get_conversation_insights(chat_history)
        
        return {
            'summary': insights['summary'],
            'insights': {
                'key_topics': insights['key_topics'],
                'user_queries': insights['user_queries'],
                'bot_responses': insights['bot_responses'],
                'conversation_length': insights['conversation_length']
            },
            'messages': chat_history[-10:]  # Last 10 messages for context
        }
        
    except Exception as e:
        print(f"Error generating chat summary: {e}")
        return {
            'summary': 'Error generating summary',
            'insights': {},
            'messages': chat_history[-5:] if chat_history else []
        }