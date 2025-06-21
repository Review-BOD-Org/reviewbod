import requests
import random
from datetime import datetime, timedelta
import time

class LinearIssueBackdater:
    def __init__(self, linear_api_key: str):
        self.linear_api_key = linear_api_key
        self.linear_url = "https://api.linear.app/graphql"
        self.headers = {
            "Authorization": f"{linear_api_key}",
            "Content-Type": "application/json"
        }
    
    def generate_random_past_date(self, days_back_min=30, days_back_max=365):
        """Generate a random date in the past"""
        days_back = random.randint(days_back_min, days_back_max)
        past_date = datetime.now() - timedelta(days=days_back)
        return past_date.isoformat()
    
    def get_all_issues(self):
        """Get ALL issues in the workspace using pagination"""
        all_issues = []
        has_next_page = True
        after_cursor = None
        
        while has_next_page:
            if after_cursor:
                query = f"""
                query {{
                    issues(first: 100, after: "{after_cursor}") {{
                        nodes {{
                            id
                            identifier
                            title
                            createdAt
                            updatedAt
                            dueDate
                            startedAt
                            completedAt
                            team {{
                                id
                                name
                            }}
                            project {{
                                id
                                name
                            }}
                            assignee {{
                                id
                                name
                            }}
                            state {{
                                name
                                type
                            }}
                        }}
                        pageInfo {{
                            hasNextPage
                            endCursor
                        }}
                    }}
                }}
                """
            else:
                query = """
                query {
                    issues(first: 100) {
                        nodes {
                            id
                            identifier
                            title
                            createdAt
                            updatedAt
                            dueDate
                            startedAt
                            completedAt
                            team {
                                id
                                name
                            }
                            project {
                                id
                                name
                            }
                            assignee {
                                id
                                name
                            }
                            state {
                                name
                                type
                            }
                        }
                        pageInfo {
                            hasNextPage
                            endCursor
                        }
                    }
                }
                """
            
            response = requests.post(
                self.linear_url,
                headers=self.headers,
                json={"query": query}
            )
            
            if response.status_code == 200:
                data = response.json()
                if "errors" in data:
                    print(f"Error fetching issues: {data['errors']}")
                    break
                    
                issues_data = data.get("data", {}).get("issues", {})
                batch_issues = issues_data.get("nodes", [])
                all_issues.extend(batch_issues)
                
                page_info = issues_data.get("pageInfo", {})
                has_next_page = page_info.get("hasNextPage", False)
                after_cursor = page_info.get("endCursor")
                
                print(f"Fetched {len(batch_issues)} issues (total: {len(all_issues)})")
            else:
                print(f"HTTP Error: {response.status_code}")
                break
        
        print(f"Found {len(all_issues)} total issues to backdate")
        return all_issues
    
    def backdate_issue_dates(self, issue_id: str, new_due_date=None, new_started_date=None):
        """Backdate an issue's dates"""
        # Build the input object dynamically
        input_fields = []
        
        if new_due_date:
            input_fields.append(f'dueDate: "{new_due_date}"')
        
        if new_started_date:
            input_fields.append(f'startedAt: "{new_started_date}"')
        
        if not input_fields:
            return False
        
        input_string = ", ".join(input_fields)
        
        mutation = f"""
        mutation {{
            issueUpdate(id: "{issue_id}", input: {{ {input_string} }}) {{
                success
                issue {{
                    id
                    identifier
                    dueDate
                    startedAt
                }}
            }}
        }}
        """
        
        try:
            response = requests.post(
                self.linear_url,
                headers=self.headers,
                json={"query": mutation},
                timeout=10
            )
            
            if response.status_code == 200:
                data = response.json()
                if "errors" in data:
                    print(f"GraphQL Error: {data['errors']}")
                    return False
                if data.get("data", {}).get("issueUpdate", {}).get("success"):
                    return True
            else:
                print(f"HTTP Error: {response.status_code} - {response.text}")
                return False
        except Exception as e:
            print(f"Request Error: {e}")
            return False
        
        return False
    
    def backdate_all_issues(self, days_back_min=30, days_back_max=365):
        """Backdate all issues with random past dates"""
        print("🕐 Starting issue backdating process...")
        
        issues = self.get_all_issues()
        
        if not issues:
            print("❌ No issues found!")
            return
        
        success_count = 0
        
        for issue in issues:
            # Generate random past dates
            new_due_date = self.generate_random_past_date(days_back_min, days_back_max)
            new_started_date = self.generate_random_past_date(days_back_min + 10, days_back_max + 10)
            
            team_name = issue.get('team', {}).get('name', 'No Team') if issue.get('team') else 'No Team'
            project_name = issue.get('project', {}).get('name', 'No Project') if issue.get('project') else 'No Project'
            assignee_name = issue.get('assignee', {}).get('name', 'Unassigned') if issue.get('assignee') else 'Unassigned'
            
            print(f"\n📅 {issue['identifier']} [{team_name}] [{project_name}]")
            print(f"   Title: {issue['title'][:50]}...")
            print(f"   Assignee: {assignee_name}")
            print(f"   OLD Due Date: {issue.get('dueDate', 'None')}")
            print(f"   OLD Started: {issue.get('startedAt', 'None')}")
            print(f"   NEW Due Date: {new_due_date}")
            print(f"   NEW Started: {new_started_date}")
            
            if self.backdate_issue_dates(issue['id'], new_due_date, new_started_date):
                success_count += 1
                print(f"   ✅ Successfully backdated!")
            else:
                print(f"   ❌ Failed to backdate!")
            
            # Add delay to avoid rate limiting
            time.sleep(1)
        
        print(f"\n🎉 Backdating completed!")
        print(f"📊 Successfully backdated {success_count}/{len(issues)} issues")
    
    def backdate_to_specific_date_range(self, start_date: str, end_date: str):
        """Backdate all issues to a specific date range"""
        print(f"🕐 Backdating all issues to date range: {start_date} to {end_date}")
        
        issues = self.get_all_issues()
        
        if not issues:
            print("❌ No issues found!")
            return
        
        # Parse dates
        start_dt = datetime.fromisoformat(start_date.replace('Z', '+00:00'))
        end_dt = datetime.fromisoformat(end_date.replace('Z', '+00:00'))
        
        success_count = 0
        
        for issue in issues:
            # Generate random date within the specified range
            time_diff = end_dt - start_dt
            random_days = random.randint(0, time_diff.days)
            random_date = start_dt + timedelta(days=random_days)
            backdated_date = random_date.isoformat()
            
            team_name = issue.get('team', {}).get('name', 'No Team') if issue.get('team') else 'No Team'
            project_name = issue.get('project', {}).get('name', 'No Project') if issue.get('project') else 'No Project'
            
            print(f"\n📅 {issue['identifier']} [{team_name}] [{project_name}]")
            print(f"   Title: {issue['title'][:50]}...")
            print(f"   Backdating to: {backdated_date}")
            
            if self.backdate_issue_dates(issue['id'], backdated_date, backdated_date):
                success_count += 1
                print(f"   ✅ Success!")
            else:
                print(f"   ❌ Failed!")
            
            time.sleep(1)
        
        print(f"\n🎉 Completed! Successfully backdated {success_count}/{len(issues)} issues")

# Usage Examples
if __name__ == "__main__":
    LINEAR_API_KEY = 'lin_api_RjdqHPeUYu6GgwMCfEdALxg6bco4ROlM0uFInLFD'
    
    backdater = LinearIssueBackdater(LINEAR_API_KEY)
    
    # Option 1: Backdate to random dates in the past (30-365 days ago)
    backdater.backdate_all_issues(days_back_min=30, days_back_max=365)
    
    # Option 2: Backdate to a specific date range
    # backdater.backdate_to_specific_date_range(
    #     start_date="2023-01-01T00:00:00Z",
    #     end_date="2023-12-31T23:59:59Z"
    # )