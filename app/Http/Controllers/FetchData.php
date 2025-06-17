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
            return response()->json(['message' => 'Trello support not implemented yet'], 501);
        }

        return response()->json(['message' => 'Unsupported service: ' . $type], 400);
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
                    "estimate" => $estimate,
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
