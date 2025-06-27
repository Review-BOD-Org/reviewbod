<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
class FetchData extends Controller
{
    public function StoreData($type)
    {
        if ($type === 'linear') {
            return $this->fetchLinearData();
        } elseif ($type === 'trello') {
            return $this->fetchTrelloData();
        } elseif($type == "jira"){
             return $this->fetchJiraData();
        }

        return response()->json(['message' => 'Unsupported service: ' . $type], 400);
    }

private function fetchJiraData()
{
   $data = DB::table("linked")->where([
       "userid" => Auth::id(),
       "type" => "jira"
   ])->first();

   if (!$data) {
       return response()->json(['message' => 'No Jira token found'], 404);
   }

   $token = $data->token;
   $cloudId = $data->cloud_id;
   $baseUrl = "https://api.atlassian.com/ex/jira/{$cloudId}/rest/api/3";

   $headers = [
       'Authorization' => 'Bearer ' . $token,
       'Accept' => 'application/json',
   ];

   // Get users
   $usersResponse = Http::withHeaders($headers)->get("{$baseUrl}/users/search");
   $users = $usersResponse->json();

   // Get projects
   $projectsResponse = Http::withHeaders($headers)->get("{$baseUrl}/project");
   $projects = $projectsResponse->json();

   // Get issues

$issuesResponse = Http::withHeaders($headers)->get("{$baseUrl}/search?jql=updated >= -30d&maxResults=1000");
   $issuesData = $issuesResponse->json();
   $issues = $issuesData['issues'] ?? [];

   // Get project roles (teams equivalent)
//    $rolesResponse = Http::withHeaders($headers)->get("{$baseUrl}/role");
//    $roles = $rolesResponse->json();
// dd($roles);
   // Store users
   foreach ($users as $u) {
       $exists = DB::table('platform_users')
           ->where('user_id', $u['accountId'])
           ->where('platform_id', $data->id)
           ->where('owner_id', $data->userid)
           ->exists();

       if (!$exists) {
           DB::table('platform_users')->insert([
               'user_id' => $u['accountId'],
               'platform_id' => $data->id,
               'owner_id' => $data->userid,
               'name' => $u['displayName'] ?? '',
               'full_name' => $u['displayName'] ?? '',
               'email' => $u['emailAddress'] ?? '',
               'source' => "jira",
               'created_at' => now(),
               'updated_at' => now(),
           ]);
       }
   }

  

   // Store projects
   foreach ($projects as $p) {
       $exists = DB::table('projects')
           ->where('user_id', Auth::id())
           ->where('platform_id', $data->id)
           ->where('project_key', $p['id'])
           ->where('owner_id', $data->userid)
           ->exists();

       if (!$exists) {
           DB::table('projects')->insert([
               "user_id" => Auth::id(),
               "platform_id" => $data->id,
               "project_key" => $p['id'],
               'owner_id' => $data->userid,
               "creator" => $p['lead']['accountId'] ?? null,
               "name" => $p['name'],
               "description" => $p['description'] ?? '',
               "source" => "jira",
               "start_date" => now(),
               "end_date" => null,
               "state" => "active",
               "last_synced_at" => now(),
               "created_at" => now(),
               "updated_at" => now()
           ]);
       }
   }

   // Store issues
   foreach ($issues as $i) {
       $dueDate = isset($i['fields']['duedate']) ? Carbon::parse($i['fields']['duedate'])->format('Y-m-d H:i:s') : null;
       $createdAt = Carbon::parse($i['fields']['created'])->format('Y-m-d H:i:s');
       $assignedUserId = $i['fields']['assignee']['accountId'] ?? null;

       $exists = DB::table('tasks')
           ->where('platform_id', $data->id)
           ->where('parent_id', $i['id'])
           ->where('owner_id', $data->userid)
           ->exists();

       if (!$exists) {
           DB::table('tasks')->insert([
               "user_id" => $assignedUserId,
               "project_id" => $i['fields']['project']['id'] ?? null,
               "team_id" => "-",
               "platform_id" => $data->id,
               "parent_id" => $i['id'],
               "title" => $i['fields']['summary'],
               "description" => $i['fields']['description'] ?? '',
               "status" => $i['fields']['status']['name'] ?? null,
               "priority" => $i['fields']['priority']['name'] ?? null,
               "labels" => json_encode($i['fields']['labels'] ?? []),
               "estimate" => $i['fields']['timeoriginalestimate'] ?? null,
               "due_date" => $dueDate,
               'owner_id' => $data->userid,
               "source" => "jira",
               "last_synced_at" => now(),
               "is_deleted" => null,
               "created_at" => $createdAt,
               "updated_at" => now(),
           ]);
       }

       // Store subtasks
       if (isset($i['fields']['subtasks'])) {
           foreach ($i['fields']['subtasks'] as $subtask) {
               $exists = DB::table('sub_issues')
                   ->where('sub_task_id', $subtask['id'])
                   ->where('platform_id', $data->id)
                   ->where('task_id', $i['id'])
                   ->where('owner_id', $data->userid)
                   ->exists();

               if (!$exists) {
                   DB::table('sub_issues')->insert([
                       "title" => $subtask['fields']['summary'],
                       "sub_task_id" => $subtask['id'],
                       "status" => $subtask['fields']['status']['name'] ?? null,
                       "user_id" => $assignedUserId,
                       "platform_id" => $data->id,
                       'owner_id' => $data->userid,
                       "task_id" => $i['id'],
                       "created_at" => now(),
                       "updated_at" => now(),
                   ]);
               }
           }
       }
   }

   return response()->json(['message' => 'Jira data synced successfully']);
}

   private function fetchTrelloData()
{
    $data = DB::table("linked")->where([
        "userid" => Auth::id(),
        "type" => "trello"
    ])->first();

    if (!$data) {
        return response()->json(['message' => 'No Trello token found'], 404);
    }

    $token = $data->token;
    $trelloKey = 'e39869487a72d56e6758bd57b67fca4f';

    // Helper function to ensure user exists in platform_users table
    $ensureUserExists = function($userId, $userData = null) use ($data) {
        if (!$userId) return;
        
        $userExists = DB::table('platform_users')
            ->where('user_id', $userId)
            ->where('platform_id', $data->id)
            ->where('owner_id', $data->userid)
            ->exists();
            
        if (!$userExists && $userData) {
            $createdAt = now()->format('Y-m-d H:i:s');
            DB::table('platform_users')->insert([
                'user_id' => $userId,
                'owner_id' => $data->userid,
                'platform_id' => $data->id,
                'name' => $userData['fullName'] ?? $userData['username'] ?? '',
                'full_name' => $userData['fullName'] ?? '',
                'email' => $userData['email'] ?? '',
                'source' => "trello",
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    };

    // Get boards
    $boardsResponse = Http::get("https://api.trello.com/1/members/me/boards", [
        'key' => $trelloKey,
        'token' => $token,
        'fields' => 'id,name,desc,dateLastActivity,dateLastView,idOrganization,url',
        'actions' => 'createBoard',
        'actions_limit' => 1,
        'action_fields' => 'date,memberCreator',
        'action_memberCreator_fields' => 'id,fullName,username'
    ]);

    // Get organizations (teams)
    $orgsResponse = Http::get("https://api.trello.com/1/members/me/organizations", [
        'key' => $trelloKey,
        'token' => $token,
        'fields' => 'id,name,desc,dateLastActivity'
    ]);

    $boards = $boardsResponse->json();
    $organizations = $orgsResponse->json();

    // Helper function for MongoDB ID to date conversion
    function getCreationDateFromId($mongoId) {
        $timestamp = hexdec(substr($mongoId, 0, 8));
        return date('Y-m-d H:i:s', $timestamp);
    }

    // Get detailed data for each board
    foreach ($boards as &$board) {
        // Get lists (columns)
        $listsResponse = Http::get("https://api.trello.com/1/boards/{$board['id']}/lists", [
            'key' => $trelloKey,
            'token' => $token,
            'fields' => 'id,name,pos'
        ]);

        // Get cards with checklists
        $cardsResponse = Http::get("https://api.trello.com/1/boards/{$board['id']}/cards", [
            'key' => $trelloKey,
            'token' => $token,
            'fields' => 'id,name,desc,due,dateLastActivity,idList,idMembers,labels,idChecklists',
            'checklists' => 'all'
        ]);

        // Get board creation action
        $actionsResponse = Http::get("https://api.trello.com/1/boards/{$board['id']}/actions", [
            'key' => $trelloKey,
            'token' => $token,
            'filter' => 'createBoard',
            'limit' => 1,
            'fields' => 'date,memberCreator',
            'memberCreator_fields' => 'id,fullName,username'
        ]);

        $board['lists'] = $listsResponse->json();
        $board['cards'] = $cardsResponse->json();
        $board['creation'] = $actionsResponse->json();

        // Get detailed checklist data for each card
        foreach ($board['cards'] as &$card) {
            if (!empty($card['idChecklists'])) {
                $card['checklists'] = [];
                foreach ($card['idChecklists'] as $checklistId) {
                    $checklistResponse = Http::get("https://api.trello.com/1/checklists/{$checklistId}", [
                        'key' => $trelloKey,
                        'token' => $token,
                        'fields' => 'id,name,pos',
                        'checkItems' => 'all',
                        'checkItem_fields' => 'id,name,state,pos,due'
                    ]);
                    
                    $checklist = $checklistResponse->json();
                    if ($checklist) {
                        $card['checklists'][] = $checklist;
                    }
                }
            } else {
                $card['checklists'] = [];
            }
        }
    }

    // Process organizations (teams)
    foreach ($organizations as $team) {
        $exists = DB::table('teams')
            ->where('user_id', Auth::id())
            ->where('platform_id', $data->id)
            ->where('owner_id', $data->userid)
            ->where('team_key', $team['id'])
            ->exists();

        if (!$exists) {
            $teamUpdatedAt = isset($team['dateLastActivity']) ? Carbon::parse($team['dateLastActivity'])->format('Y-m-d H:i:s') : now();
            
            DB::table('teams')->insert([
                "user_id" => Auth::id(),
                "platform_id" => $data->id,
                'owner_id' => $data->userid,
                "name" => $team['name'],
                "team_key" => $team['id'],
                "description" => $team['desc'] ?? "",
                "source" => "trello",
                "last_synced_at" => now(),
                "created_at" => null,
                "updated_at" => $teamUpdatedAt
            ]);
        }
    }

    // Process boards (projects)
    foreach ($boards as $project) {
        // Ensure board creator exists in platform_users
        if (!empty($project['creation'][0]['memberCreator'])) {
            $ensureUserExists($project['creation'][0]['memberCreator']['id'], $project['creation'][0]['memberCreator']);
        }

        $exists = DB::table('projects')
            ->where('user_id', Auth::id())
            ->where('platform_id', $data->id)
            ->where('project_key', $project['id'])
            ->where('owner_id', $data->userid)
            ->exists();

        if (!$exists) {
            $createdAt = !empty($project['creation'][0]['date']) 
                ? Carbon::parse($project['creation'][0]['date'])->format('Y-m-d H:i:s')
                : Carbon::parse(getCreationDateFromId($project['id']))->format('Y-m-d H:i:s');
            $updateAt = Carbon::parse($project['dateLastView'])->format('Y-m-d H:i:s');
            
            DB::table('projects')->insert([
                "user_id" => Auth::id(),
                "platform_id" => $data->id,
                "project_key" => $project['id'],
                'owner_id' => $data->userid,
                "creator" => $project['creation'][0]['memberCreator']['id'] ?? null,
                "name" => $project['name'],
                "description" => $project['desc'],
                "source" => "trello",
                "start_date" => $createdAt,
                "end_date" => null,
                "state" => "none",
                "last_synced_at" => now(),
                "created_at" => $createdAt,
                "updated_at" => $updateAt
            ]);
        }

        // Process lists (status) for this board
        foreach ($project['lists'] as $status) {
            $exists = DB::table('status_trello')
                ->where('user_id', Auth::id())
                ->where('platform_id', $data->id)
                ->where('status_key', $status['id'])
                ->where('owner_id', $data->userid)
                ->exists();

            if (!$exists) {
                DB::table('status_trello')->insert([
                    "user_id" => Auth::id(),
                    "platform_id" => $data->id,
                    "status_key" => $status['id'],
                    'owner_id' => $data->userid,
                    "type" => $status['name'],
                ]);
            }
        }

        // Create list lookup for status names
        $listLookup = [];
        foreach ($project['lists'] as $list) {
            $listLookup[$list['id']] = $list['name'];
        }

        // Process cards (tasks) for this board
        foreach ($project['cards'] as $card) {
            $dueDate = isset($card['due']) ? Carbon::parse($card['due'])->format('Y-m-d H:i:s') : null;
            $createdAt = Carbon::parse(getCreationDateFromId($card['id']))->format('Y-m-d H:i:s');
            $estimate = $dueDate ? now()->diffInDays(Carbon::parse($dueDate), false) : null;
            $updatedAt = Carbon::parse($card['dateLastActivity'])->format('Y-m-d H:i:s');
            $statusName = $listLookup[$card['idList']] ?? 'Unknown';

            // Handle cards with no assigned members
            $members = !empty($card['idMembers']) ? $card['idMembers'] : [Auth::id()];

            // Get member details for platform_users
            if (!empty($card['idMembers'])) {
                foreach ($card['idMembers'] as $memberId) {
                    // Get member details from Trello API
                    $memberResponse = Http::get("https://api.trello.com/1/members/{$memberId}", [
                        'key' => $trelloKey,
                        'token' => $token,
                        'fields' => 'id,fullName,username,email'
                    ]);
                    
                    $memberData = $memberResponse->json();
                    if ($memberData) {
                        $ensureUserExists($memberId, $memberData);
                    }
                }
            }

            foreach ($members as $memberId) {
                $exists = DB::table('tasks')
                    ->where('user_id', $memberId)
                    ->where('platform_id', $data->id)
                    ->where('parent_id', $card['id'])
                    ->where('owner_id', $data->userid)
                    ->exists();

                if (!$exists) {
                    $taskId = DB::table('tasks')->insertGetId([
                        "user_id" => $memberId,
                        "project_id" => $project['id'],
                        "team_id" => "-",
                        "platform_id" => $data->id,
                        "parent_id" => $card['id'],
                        "title" => $card['name'],
                        "description" => $card['desc'],
                        "status" => $statusName,
                        "priority" => null,
                        "labels" => json_encode($card['labels'] ?? []),
                        "estimate" => $estimate,
                        "due_date" => $dueDate,
                        "checklists" => json_encode($card['checklists'] ?? []),
                        'owner_id' => $data->userid,
                        "source" => "trello",
                        "last_synced_at" => now(),
                        "is_deleted" => null,
                        "created_at" => $createdAt,
                        "updated_at" => $updatedAt,
                    ]);

                    // Process checklist items as sub issues
                    foreach ($card['checklists'] ?? [] as $checklist) {
                        foreach ($checklist['checkItems'] ?? [] as $checkItem) {
                            $subTaskId = $checkItem['id'];
                            $checkItemCreatedAt = getCreationDateFromId($checkItem['id']);
                            $checkItemStatus = $checkItem['state'] === 'complete' ? 'completed' : 'incomplete';

                            $subExists = DB::table('sub_issues')
                                ->where('sub_task_id', $subTaskId)
                                ->where('platform_id', $data->id)
                                ->where('task_id', $card['id'])
                                ->where('owner_id', $data->userid)
                                ->exists();

                            if (!$subExists) {
                                DB::table('sub_issues')->insert([
                                    "title" => $checkItem['name'],
                                    "sub_task_id" => $subTaskId,
                                    "status" => $checkItemStatus,
                                    "user_id" => $memberId,
                                    "platform_id" => $data->id,
                                    'owner_id' => $data->userid,
                                    "task_id" => $card['id'],
                                    "created_at" => $checkItemCreatedAt,
                                    "updated_at" => $checkItemCreatedAt,
                                ]);
                            }
                        }
                    }
                } else {
                    // If task exists, still process checklist items as sub issues
                    foreach ($card['checklists'] ?? [] as $checklist) {
                        foreach ($checklist['checkItems'] ?? [] as $checkItem) {
                            $subTaskId = $checkItem['id'];
                            $checkItemCreatedAt = getCreationDateFromId($checkItem['id']);
                            $checkItemStatus = $checkItem['state'] === 'complete' ? 'completed' : 'incomplete';

                            $subExists = DB::table('sub_issues')
                                ->where('sub_task_id', $subTaskId)
                                ->where('platform_id', $data->id)
                                ->where('task_id', $card['id'])
                                ->where('owner_id', $data->userid)
                                ->exists();

                            if (!$subExists) {
                                DB::table('sub_issues')->insert([
                                    "title" => $checkItem['name'],
                                    "sub_task_id" => $subTaskId,
                                    "status" => $checkItemStatus,
                                    "user_id" => $memberId,
                                    "platform_id" => $data->id,
                                    'owner_id' => $data->userid,
                                    "task_id" => $card['id'],
                                    "created_at" => $checkItemCreatedAt,
                                    "updated_at" => $checkItemCreatedAt,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    return response()->json(['message' => 'Trello data synced successfully']);
}
    private function fetchLinearData()
    {
        $data = DB::table("linked")->where([
            "userid" => Auth::id(),
            "type" => "linear"
        ])->first();

        if (!$data) {
            return response()->json(['message' => 'No Linear token found'], 404);
        }

        $token = $data->token;

        $query = <<<GQL
    {
        teams {
            nodes {
                id
                name
                createdAt
            }
        }
        issues {
            nodes {
                id
                title
                description
                dueDate
                createdAt
                priority
                estimate
                state {
                    name
                }
                project {
                    id
                    name
                }
                team {
                    id
                    name
                }
                assignee {
                    id
                    name
                    email
                }
                labels {
                    nodes {
                        id
                        name
                        color
                    }
                }
                children {
                    nodes {
                        id
                        title                
                        createdAt
                        updatedAt
                        state {
                            name
                        }
                    }
                }
            }
        }
        users {
            nodes {
                id
                name
                email 
                active
                createdAt 
            }
        }
        projects {
            nodes {
                id
                name
                description
                createdAt
                targetDate
                state
                creator {
                    id
                    name
                    email
                }
            }
        }
    }
    GQL;


        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post('https://api.linear.app/graphql', [
                    'query' => $query,
                ]);

        $json = $response->json();
        // dd($json);

        if (isset($json['errors'])) {
            return response()->json([
                'message' => 'Linear API error',
                'errors' => $json['errors']
            ], 400);
        }

        $teams = $json['data']['teams']['nodes'];
        $issues = $json['data']['issues']['nodes'];
        $projects = $json['data']['projects']['nodes'];
        $users = $json['data']['users']['nodes'];

        foreach ($users as $u) {
            $exists = DB::table('platform_users')
                ->where('user_id', $u['id'])
                ->where('platform_id', $data->id)
                ->where('owner_id', $data->userid)
                ->exists();

            if (!$exists) {
                $createdAt = Carbon::parse($u['createdAt'])->format('Y-m-d H:i:s');
                DB::table('platform_users')->insert([
                    'user_id' => $u['id'],
                    'platform_id' => $data->id,
                    'owner_id' => $data->userid,
                    'name' => $u['name'] ?? '',
                    'full_name' => $u['name'] ?? '',
                    'email' => $u['email'] ?? '',
                    'source' => "linear",
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        foreach ($teams as $t) {
            $exists = DB::table('teams')
                ->where('user_id', Auth::id())
                ->where('platform_id', $data->id)
                ->where('owner_id', $data->userid)
                ->where('team_key', $t['id'])
                ->exists();

            if (!$exists) {
                $createdAt = Carbon::parse($t['createdAt'])->format('Y-m-d H:i:s');
                DB::table('teams')->insert([
                    "user_id" => Auth::id(),
                    "platform_id" => $data->id,
                    'owner_id' => $data->userid,

                    "name" => $t['name'],
                    "team_key" => $t['id'],
                    "description" => "",
                    "source" => "linear",
                    "last_synced_at" => now(),
                    "created_at" => $createdAt,
                    "updated_at" => $createdAt
                ]);
            }
        }

        foreach ($projects as $p) {
            $exists = DB::table('projects')
                ->where('user_id', Auth::id())
                ->where('platform_id', $data->id)
                ->where('project_key', $p['id'])
                ->where('owner_id', $data->userid)
                ->exists();

            if (!$exists) {
                $createdAt = Carbon::parse($p['createdAt'])->format('Y-m-d H:i:s');
                $targetDate = isset($p['targetDate']) ? Carbon::parse($p['targetDate'])->format('Y-m-d H:i:s') : null;
                DB::table('projects')->insert([
                    "user_id" => Auth::id(),
                    "platform_id" => $data->id,
                    "project_key" => $p['id'],
                    'owner_id' => $data->userid,
                    "creator" => $p['creator']['id'] ?? null,
                    "name" => $p['name'],
                    "description" => $p['description'],
                    "source" => "linear",
                    "start_date" => $createdAt,
                    "end_date" => $targetDate,
                    "state" => $p['state'],
                    "last_synced_at" => now(),
                    "created_at" => $createdAt,
                    "updated_at" => $createdAt
                ]);
            }
        }

        foreach ($issues as $i) {
            $dueDate = isset($i['dueDate']) ? Carbon::parse($i['dueDate'])->format('Y-m-d H:i:s') : null;
            $createdAt = Carbon::parse($i['createdAt'])->format('Y-m-d H:i:s');
            $estimate = $dueDate ? now()->diffInDays(Carbon::parse($dueDate), false) : null;
            $assignedUserId = $i['assignee']['id'] ?? null;

            $exists = DB::table('tasks')
                ->where('user_id', $assignedUserId)
                ->where('platform_id', $data->id)
                ->where('parent_id', $i['id'])
                ->where('owner_id', $data->userid)
                ->exists();

            if (!$exists) {
                DB::table('tasks')->insert([
                    "user_id" => $assignedUserId,
                    "project_id" => $i['project']['id'] ?? null,
                    "team_id" => $i['team']['id'] ?? "-",
                    "platform_id" => $data->id,
                    "parent_id" => $i['id'],
                    "title" => $i['title'],
                    "description" => $i['description'],
                    "status" => $i['state']['name'] ?? null,
                    "priority" => $i['priority'] ?? null,
                    "labels" => json_encode($i['labels']['nodes'] ?? []),
                    "estimate" => $i['estimate'] ?? $estimate,
                    "due_date" => $dueDate,
                    'owner_id' => $data->userid,
                    "source" => "linear",
                    "last_synced_at" => now(),
                    "is_deleted" => null,
                    "created_at" => $createdAt,
                    "updated_at" => $createdAt,
                ]);
            }

            // Save sub-issues (children)
            foreach ($i['children']['nodes'] ?? [] as $child) {
                $childCreatedAt = Carbon::parse($child['createdAt'])->format('Y-m-d H:i:s');
                $childUpdatedAt = Carbon::parse($child['updatedAt'])->format('Y-m-d H:i:s');
                $subTaskId = $child['id'];

                $exists = DB::table('sub_issues')
                    ->where('sub_task_id', $subTaskId)
                    ->where('platform_id', $data->id)
                    ->where('task_id', $i['id'])
                    ->where('owner_id', $data->userid)
                    ->exists();

                if (!$exists) {
                    DB::table('sub_issues')->insert([
                        "title" => $child['title'],
                        "sub_task_id" => $subTaskId,
                        "status" => $child['state']['name'] ?? null,
                        "user_id" => $assignedUserId,
                        "platform_id" => $data->id,
                        'owner_id' => $data->userid,
                        "task_id" => $i['id'],
                        "created_at" => $childCreatedAt,
                        "updated_at" => $childUpdatedAt,
                    ]);
                }
            }
        }


        return response()->json(['message' => 'Linear data synced successfully']);
    }


}
