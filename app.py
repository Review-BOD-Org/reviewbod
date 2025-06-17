# Quick test to verify your Linear API key
import requests

LINEAR_API_KEY = "lin_api_RjdqHPeUYu6GgwMCfEdALxg6bco4ROlM0uFInLFD"  # Replace with your actual key

headers = {
    "Authorization": f"{LINEAR_API_KEY}",
    "Content-Type": "application/json"
}

# Simple test query
query = """
query {
    viewer {
        id
        name
        email
    }
}
"""

response = requests.post(
    "https://api.linear.app/graphql",
    headers=headers,
    json={"query": query}
)

print(f"Status: {response.status_code}")
print(f"Response: {response.text}")