import requests
import json
import random
from openai import OpenAI
import time
from datetime import datetime, timedelta
from typing import List, Dict, Optional

class BackdatedLinearIssueCreator:
    def __init__(self, linear_api_key: str, openai_api_key: str):
        self.linear_api_key = linear_api_key
        self.linear_url = "https://api.linear.app/graphql"
        self.headers = {
            "Authorization": f"{linear_api_key}",
            "Content-Type": "application/json"
        }
        self.openai_client = OpenAI(api_key=openai_api_key)
    
    def get_team_states_and_cycles(self):
        """Get first team, its workflow states, cycles, and team members"""
        query = """
        query {
            teams(first: 1) {
                nodes {
                    id
                    name
                    states {
                        nodes {
                            id
                            name
                            type
                        }
                    }
                    cycles {
                        nodes {
                            id
                            name
                            number
                            startsAt
                            endsAt
                        }
                    }
                    members {
                        nodes {
                            id
                            name
                            displayName
                            email
                        }
                    }
                }
            }
        }
        """
        
        try:
            response = requests.post(
                self.linear_url,
                headers=self.headers,
                json={"query": query},
                timeout=30
            )
            
            if response.status_code == 200:
                data = response.json()
                if "errors" in data:
                    print(f"GraphQL errors: {data['errors']}")
                    return None, [], [], []
                
                teams = data.get("data", {}).get("teams", {}).get("nodes", [])
                if not teams:
                    print("No teams found")
                    return None, [], [], []
                    
                team = teams[0]
                cycles = team.get("cycles", {}).get("nodes", [])
                members = team.get("members", {}).get("nodes", [])
                states = team.get("states", {}).get("nodes", [])
                
                return team["id"], states, cycles, members
            else:
                print(f"Error getting team data: {response.status_code}")
                print(f"Response: {response.text}")
                return None, [], [], []
                
        except requests.exceptions.RequestException as e:
            print(f"Request error: {e}")
            return None, [], [], []
    
    def create_cycles_if_none(self, team_id: str, num_cycles: int = 4, days_back: int = 90) -> List[Dict]:
        """Create cycles in Linear if none exist"""
        print(f"📅 No cycles found. Creating {num_cycles} cycles...")
        
        cycles = []
        end_date = datetime.now()
        cycle_duration = days_back // num_cycles
        
        for i in range(num_cycles):
            cycle_start = end_date - timedelta(days=days_back - (i * cycle_duration))
            cycle_end = end_date - timedelta(days=days_back - ((i + 1) * cycle_duration))
            
            # Create cycle in Linear
            cycle_name = f"Sprint {i + 1}"
            cycle_id = self.create_linear_cycle(team_id, cycle_name, cycle_start, cycle_end)
            
            if cycle_id:
                cycles.append({
                    'id': cycle_id,
                    'name': cycle_name,
                    'number': i + 1,
                    'startsAt': cycle_start.isoformat() + "Z",
                    'endsAt': cycle_end.isoformat() + "Z"
                })
                print(f"✅ Created cycle: {cycle_name}")
            else:
                print(f"❌ Failed to create cycle: {cycle_name}")
        
        return cycles
    
    def create_linear_cycle(self, team_id: str, name: str, starts_at: datetime, ends_at: datetime) -> Optional[str]:
        """Create a cycle in Linear"""
        mutation = """
        mutation CycleCreate($input: CycleCreateInput!) {
            cycleCreate(input: $input) {
                success
                cycle {
                    id
                    name
                }
            }
        }
        """
        
        variables = {
            "input": {
                "teamId": team_id,
                "name": name,
                "startsAt": starts_at.isoformat() + "Z",
                "endsAt": ends_at.isoformat() + "Z"
            }
        }
        
        try:
            response = requests.post(
                self.linear_url,
                headers=self.headers,
                json={"query": mutation, "variables": variables},
                timeout=30
            )
            
            if response.status_code == 200:
                data = response.json()
                
                if "errors" in data:
                    print(f"❌ GraphQL Error creating cycle: {data['errors']}")
                    return None
                
                if data["data"]["cycleCreate"]["success"]:
                    return data["data"]["cycleCreate"]["cycle"]["id"]
            
            return None
                
        except Exception as e:
            print(f"❌ Error creating cycle: {e}")
            return None
    
    def get_random_date_in_cycle(self, cycle: Dict) -> datetime:
        """Get a random datetime within a specific cycle"""
        start_time = datetime.fromisoformat(cycle['startsAt'].replace('Z', '+00:00'))
        end_time = datetime.fromisoformat(cycle['endsAt'].replace('Z', '+00:00'))
        
        start_timestamp = start_time.timestamp()
        end_timestamp = end_time.timestamp()
        random_timestamp = random.uniform(start_timestamp, end_timestamp)
        return datetime.fromtimestamp(random_timestamp)
    
    def generate_random_issue(self, cycle_name: str = ""):
        """Generate random issue using OpenAI with cycle context"""
        topics = [
            "bug fix", "feature request", "UI improvement", "performance issue",
            "API endpoint", "database optimization", "security vulnerability", 
            "user authentication", "payment integration", "mobile responsive",
            "code refactoring", "unit testing", "documentation update", "deployment issue",
            "user experience", "accessibility", "monitoring", "analytics integration"
        ]
        
        topic = random.choice(topics)
        cycle_context = f" for {cycle_name}" if cycle_name else ""
        
        prompt = f"""Create a software development issue about {topic}{cycle_context}.
        Return ONLY a JSON object with 'title' and 'description' keys.
        Title should be under 60 characters.
        Description should be 1-2 sentences explaining the issue.
        
        Example format:
        {{"title": "Fix login validation", "description": "Users are experiencing login failures due to incorrect validation logic."}}"""
        
        try:
            response = self.openai_client.chat.completions.create(
                model="gpt-3.5-turbo",
                messages=[{"role": "user", "content": prompt}],
                temperature=0.7,
                max_tokens=150
            )
            
            content = response.choices[0].message.content.strip()
            # Remove any markdown code blocks if present
            if content.startswith("```"):
                content = content.split("```")[1]
                if content.startswith("json"):
                    content = content[4:]
            
            return json.loads(content)
            
        except Exception as e:
            print(f"OpenAI error: {e}")
            return {
                "title": f"Fix {topic}{cycle_context}",
                "description": f"Need to address {topic} in the system{cycle_context}."
            }
    
    def create_backdated_issue(self, team_id: str, cycle_id: str, state_id: str, 
                             assignee_id: str, title: str, description: str, created_at: datetime):
        """Create issue with specific cycle, assignee, and backdated timestamp"""
        created_at_iso = created_at.isoformat() + "Z"
        
        mutation = """
        mutation IssueCreate($input: IssueCreateInput!) {
            issueCreate(input: $input) {
                success
                issue {
                    id
                    identifier
                    title
                    createdAt
                    state {
                        name
                    }
                    cycle {
                        name
                    }
                    assignee {
                        name
                    }
                }
            }
        }
        """
        
        variables = {
            "input": {
                "teamId": team_id,
                "cycleId": cycle_id,
                "stateId": state_id,
                "assigneeId": assignee_id,
                "title": title,
                "description": description,
                "priority": random.randint(1, 4),
                "createdAt": created_at_iso
            }
        }
        
        try:
            response = requests.post(
                self.linear_url,
                headers=self.headers,
                json={"query": mutation, "variables": variables},
                timeout=30
            )
            
            if response.status_code == 200:
                data = response.json()
                
                if "errors" in data:
                    print(f"❌ GraphQL Error: {data['errors']}")
                    return False
                
                if data["data"]["issueCreate"]["success"]:
                    issue = data["data"]["issueCreate"]["issue"]
                    created_date = datetime.fromisoformat(issue['createdAt'].replace('Z', '+00:00')).strftime('%Y-%m-%d %H:%M')
                    cycle_name = issue['cycle']['name'] if issue['cycle'] else 'No Cycle'
                    assignee_name = issue['assignee']['name'] if issue['assignee'] else 'Unassigned'
                    
                    print(f"✅ Created: {issue['identifier']} - {issue['title']}")
                    print(f"   📅 Cycle: {cycle_name} | 👤 Assignee: {assignee_name} | 📊 State: {issue['state']['name']} | ⏰ Created: {created_date}")
                    return True
                else:
                    print(f"❌ Issue Creation Failed")
                    return False
            else:
                print(f"❌ HTTP Error: {response.status_code}")
                return False
                
        except Exception as e:
            print(f"❌ Error: {e}")
            return False
    
    def create_backdated_issues_by_cycles(self, issues_per_cycle: int = 25, 
                                        days_back: int = 90, num_cycles: int = 4):
        """Create backdated issues distributed across Linear cycles with random assignees"""
        print("🚀 Getting team data (states, cycles, and members)...")
        
        team_id, states, cycles, members = self.get_team_states_and_cycles()
        
        if not team_id:
            print("❌ Could not get team data")
            return
        
        print(f"📊 Team ID: {team_id}")
        print(f"📋 Available states: {[s['name'] for s in states]}")
        print(f"👥 Team members: {[m['name'] or m['displayName'] for m in members]}")
        
        # Create cycles if none exist
        if not cycles:
            cycles = self.create_cycles_if_none(team_id, num_cycles, days_back)
        
        if not cycles:
            print("❌ No cycles available and couldn't create new ones")
            return
        
        # Sort cycles by start date to use them chronologically
        cycles.sort(key=lambda x: x['startsAt'])
        
        print(f"\n🔄 Found/Created {len(cycles)} cycles:")
        for cycle in cycles:
            start_date = datetime.fromisoformat(cycle['startsAt'].replace('Z', '+00:00')).strftime('%Y-%m-%d')
            end_date = datetime.fromisoformat(cycle['endsAt'].replace('Z', '+00:00')).strftime('%Y-%m-%d')
            print(f"  🎯 {cycle['name']}: {start_date} to {end_date}")
        
        if not members:
            print("⚠️  No team members found - issues will be unassigned")
        
        total_success = 0
        total_issues = len(cycles) * issues_per_cycle
        
        for cycle_idx, cycle in enumerate(cycles):
            start_date = datetime.fromisoformat(cycle['startsAt'].replace('Z', '+00:00')).strftime('%Y-%m-%d')
            end_date = datetime.fromisoformat(cycle['endsAt'].replace('Z', '+00:00')).strftime('%Y-%m-%d')
            print(f"\n🔄 Creating issues for {cycle['name']} ({start_date} - {end_date})...")
            cycle_success = 0
            
            for i in range(issues_per_cycle):
                print(f"📝 Creating issue {i+1}/{issues_per_cycle} for {cycle['name']}...")
                
                # Generate random issue content
                issue_data = self.generate_random_issue(cycle['name'])
                
                # Pick random state
                state = random.choice(states)
                
                # Pick random assignee (or None if no members)
                assignee_id = random.choice(members)['id'] if members else None
                
                # Get random date within this cycle
                created_at = self.get_random_date_in_cycle(cycle)
                
                # Create the backdated issue
                success = self.create_backdated_issue(
                    team_id=team_id,
                    cycle_id=cycle['id'],
                    state_id=state["id"],
                    assignee_id=assignee_id,
                    title=issue_data["title"],
                    description=issue_data["description"],
                    created_at=created_at
                )
                
                if success:
                    cycle_success += 1
                    total_success += 1
                    time.sleep(0.5)  # Rate limiting
                else:
                    time.sleep(1)  # Longer wait on failure
            
            print(f"✨ {cycle['name']} completed: {cycle_success}/{issues_per_cycle} issues created")
        
        print(f"\n🎉 All cycles completed! Created {total_success}/{total_issues} backdated issues successfully!")
        print(f"📈 Success rate: {(total_success/total_issues)*100:.1f}%")
        print(f"🎯 Issues distributed across {len(cycles)} Linear cycles with random assignees")

# Usage example
if __name__ == "__main__":
    # SECURITY WARNING: Never hardcode API keys in production code!
    # Use environment variables instead:
    # import os
    # OPENAI_API_KEY = os.getenv('OPENAI_API_KEY')
    # LINEAR_API_KEY = os.getenv('LINEAR_API_KEY')
    
    OPENAI_API_KEY = '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'
    LINEAR_API_KEY = 'lin_api_RjdqHPeUYu6GgwMCfEdALxg6bco4ROlM0uFInLFD'
    
    creator = BackdatedLinearIssueCreator(LINEAR_API_KEY, OPENAI_API_KEY)
    
    # Create 100 issues across Linear cycles over the last 90 days (25 issues per cycle)
    creator.create_backdated_issues_by_cycles(
        issues_per_cycle=25,  # 25 issues per cycle
        days_back=90,         # Go back 90 days
        num_cycles=4          # Create 4 cycles if none exist
    )