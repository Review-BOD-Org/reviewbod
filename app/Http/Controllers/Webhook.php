<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Crypt;
use Carbon;
use Log;

class Webhook extends Controller
{
public function saveTrello($request)
{
    $action = $request['action'];
    $type = $action['type'];
    $data = $action['data'];
    $userid = Crypt::decryptString($request->user);

    $linked = DB::table("linked")->where([
        "type" => "trello",
        "userid" => $userid
    ])->first();

    if (!$linked) {
        return response()->json(['status' => 'no_linked_found'], 404);
    }

    $platformid = $linked->id;

    // Helper function to get creation date from MongoDB-style ID
    $getCreationDateFromId = function($mongoId) {
        $timestamp = hexdec(substr($mongoId, 0, 8));
        return date('Y-m-d H:i:s', $timestamp);
    };

    // Helper function to ensure user exists in platform_users table
    $ensureUserExists = function($userId, $userData = null) use ($platformid, $userid) {
        if (!$userId) return;
        
        $userExists = DB::table('platform_users')
            ->where('user_id', $userId)
            ->where('platform_id', $platformid)
            ->where('owner_id', $userid)
            ->exists();
            
        if (!$userExists && $userData) {
            $createdAt = now()->format('Y-m-d H:i:s');
            DB::table('platform_users')->insert([
                'user_id' => $userId,
                'owner_id' => $userid,
                'platform_id' => $platformid,
                'name' => $userData['fullName'] ?? $userData['username'] ?? '',
                'full_name' => $userData['fullName'] ?? '',
                'email' => $userData['email'] ?? '',
                'source' => "trello",
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    };

    // Handle Board Actions
    if ($type === "createBoard") {
        $board = $data['board'];
        $creator = $action['memberCreator'] ?? null;
        
        if ($creator) {
            $ensureUserExists($creator['id'], $creator);
        }

        $exists = DB::table('projects')
            ->where('user_id', $userid)
            ->where('platform_id', $platformid)
            ->where('project_key', $board['id'])
            ->where('owner_id', $userid)
            ->exists();

        if (!$exists) {
            $createdAt = $getCreationDateFromId($board['id']);
            DB::table('projects')->insert([
                "user_id" => $userid,
                "platform_id" => $platformid,
                "project_key" => $board['id'],
                'owner_id' => $userid,
                "creator" => $creator['id'] ?? null,
                "name" => $board['name'],
                "description" => $board['desc'] ?? '',
                "source" => "trello",
                "start_date" => $createdAt,
                "end_date" => null,
                "state" => "active",
                "last_synced_at" => now(),
                "created_at" => $createdAt,
                "updated_at" => $createdAt
            ]);
        }
    }

    // Handle List Actions (Status)
    if ($type === "createList") {
        $list = $data['list'];
        
        $exists = DB::table('status_trello')
            ->where('user_id', $userid)
            ->where('platform_id', $platformid)
            ->where('status_key', $list['id'])
            ->where('owner_id', $userid)
            ->exists();

        if (!$exists) {
            DB::table('status_trello')->insert([
                "user_id" => $userid,
                "platform_id" => $platformid,
                "status_key" => $list['id'],
                'owner_id' => $userid,
                "type" => $list['name'],
            ]);
        }
    }

    if ($type === "updateList") {
        $list = $data['list'];
        
        DB::table('status_trello')
            ->where('status_key', $list['id'])
            ->where('platform_id', $platformid)
            ->where('owner_id', $userid)
            ->update([
                "status" => $list['name'],
            ]);
    }

    // Handle Card Actions (Tasks)
    if ($type === "createCard") {
        $card = $data['card'];
        $list = $data['list'];
        $creator = $action['memberCreator'] ?? null;
        
        if ($creator) {
            $ensureUserExists($creator['id'], $creator);
        }

        $createdAt = $getCreationDateFromId($card['id']);
        $dueDate = isset($card['due']) ? Carbon\Carbon::parse($card['due'])->format('Y-m-d H:i:s') : null;
        $estimate = $dueDate ? now()->diffInDays(Carbon\Carbon::parse($dueDate), false) : null;

        $exists = DB::table('tasks')
            ->where('platform_id', $platformid)
            ->where('parent_id', $card['id'])
            ->where('owner_id', $userid)
            ->exists();

        if (!$exists) {
            DB::table('tasks')->insert([
                "user_id" => $creator['id'] ?? $userid,
                "project_id" => $data['board']['id'],
                "team_id" => "-",
                "platform_id" => $platformid,
                "parent_id" => $card['id'],
                "title" => $card['name'],
                "description" => $card['desc'] ?? '',
                "status" => $list['name'],
                "priority" => null,
                "labels" => json_encode([]),
                "estimate" => $estimate,
                "due_date" => $dueDate,
                "checklists" => json_encode([]),
                'owner_id' => $userid,
                "source" => "trello",
                "last_synced_at" => now(),
                "is_deleted" => null,
                "created_at" => $createdAt,
                "updated_at" => $createdAt,
            ]);
        }
    }

    if ($type === "updateCard") {
        $card = $data['card'];
        $listAfter = $data['listAfter'] ?? null;
        $listBefore = $data['listBefore'] ?? null;
        
        $updateData = [
            'title' => $card['name'],
            'description' => $card['desc'] ?? '',
            'last_synced_at' => now(),
            'updated_at' => now(),
        ];

        // If card moved between lists, update status
        if ($listAfter && $listBefore && $listAfter['id'] !== $listBefore['id']) {
            $updateData['status'] = $listAfter['name'];
        }

        // Handle due date changes
        if (isset($card['due'])) {
            $dueDate = $card['due'] ? Carbon\Carbon::parse($card['due'])->format('Y-m-d H:i:s') : null;
            $updateData['due_date'] = $dueDate;
            $updateData['estimate'] = $dueDate ? now()->diffInDays(Carbon\Carbon::parse($dueDate), false) : null;
        }

        DB::table('tasks')
            ->where('parent_id', $card['id'])
            ->where('platform_id', $platformid)
            ->where('owner_id', $userid)
            ->update($updateData);
    }

    if ($type === "deleteCard") {
        $card = $data['card'];
        
        DB::table('tasks')
            ->where('parent_id', $card['id'])
            ->where('platform_id', $platformid)
            ->where('owner_id', $userid)
            ->update(["is_deleted" => 1]);
    }

    // Handle Checklist Actions
    if ($type === "createChecklist") {
        $checklist = $data['checklist'];
        $card = $data['card'];
        
        // Update the card's checklists field
        $task = DB::table('tasks')
            ->where('parent_id', $card['id'])
            ->where('platform_id', $platformid)
            ->where('owner_id', $userid)
            ->first();

        if ($task) {
            $existingChecklists = json_decode($task->checklists, true) ?: [];
            $existingChecklists[] = [
                'id' => $checklist['id'],
                'name' => $checklist['name'],
                'checkItems' => []
            ];
            
            DB::table('tasks')
                ->where('parent_id', $card['id'])
                ->where('platform_id', $platformid)
                ->where('owner_id', $userid)
                ->update([
                    'checklists' => json_encode($existingChecklists),
                    'updated_at' => now()
                ]);
        }
    }

    // Handle Checklist Item Actions (Sub Issues)
    if ($type === "createCheckItem") {
        $checkItem = $data['checkItem'];
        $card = $data['card'];
        $checklist = $data['checklist'];
        
        $createdAt = $getCreationDateFromId($checkItem['id']);
        
        $exists = DB::table('sub_issues')
            ->where('sub_task_id', $checkItem['id'])
            ->where('platform_id', $platformid)
            ->where('task_id', $card['id'])
            ->where('owner_id', $userid)
            ->exists();

        if (!$exists) {
            DB::table('sub_issues')->insert([
                "title" => $checkItem['name'],
                "sub_task_id" => $checkItem['id'],
                "status" => $checkItem['state'] === 'complete' ? 'completed' : 'incomplete',
                "user_id" => $userid,
                "platform_id" => $platformid,
                'owner_id' => $userid,
                "task_id" => $card['id'],
                "created_at" => $createdAt,
                "updated_at" => $createdAt,
            ]);
        }
    }

    if ($type === "updateCheckItemStateOnCard") {
        $checkItem = $data['checkItem'];
        $card = $data['card'];
        
        DB::table('sub_issues')
            ->where('sub_task_id', $checkItem['id'])
            ->where('platform_id', $platformid)
            ->where('task_id', $card['id'])
            ->where('owner_id', $userid)
            ->update([
                'status' => $checkItem['state'] === 'complete' ? 'completed' : 'incomplete',
                'updated_at' => now(),
            ]);
    }

    if ($type === "deleteCheckItem") {
        $checkItem = $data['checkItem'];
        $card = $data['card'];
        
        DB::table('sub_issues')
            ->where('sub_task_id', $checkItem['id'])
            ->where('platform_id', $platformid)
            ->where('task_id', $card['id'])
            ->where('owner_id', $userid)
            ->update(["is_deleted" => 1]);
    }

    // Handle Member Actions
    if ($type === "addMemberToCard") {
        $member = $data['member'];
        $card = $data['card'];
        
        $ensureUserExists($member['id'], $member);
        
        // Update task assignment
        DB::table('tasks')
            ->where('parent_id', $card['id'])
            ->where('platform_id', $platformid)
            ->where('owner_id', $userid)
            ->update([
                'user_id' => $member['id'],
                'updated_at' => now(),
            ]);
    }

    if ($type === "removeMemberFromCard") {
        $card = $data['card'];
        
        // Remove assignment (set back to owner)
        DB::table('tasks')
            ->where('parent_id', $card['id'])
            ->where('platform_id', $platformid)
            ->where('owner_id', $userid)
            ->update([
                'user_id' => $userid,
                'updated_at' => now(),
            ]);
    }

    return response()->json(['status' => 'success'], 200);
}

public function saveJira($request)
{
    $webhookEvent = $request['webhookEvent'];
    $issue = $request['issue'] ?? null;
    $project = $request['project'] ?? null;
    $user = $request['user'] ?? null;
    $changelog = $request['changelog'] ?? null;
    $userid = Crypt::decryptString($request->user);

    $linked = DB::table("linked")->where([
        "type" => "jira",
        "userid" => $userid
    ])->first();

    if (!$linked) {
        return response()->json(['status' => 'no_linked_found'], 404);
    }

    $platformid = $linked->id;

    // Helper function to ensure user exists in platform_users table
    $ensureUserExists = function($userKey, $userData = null) use ($platformid, $userid) {
        if (!$userKey) return;
        
        $userExists = DB::table('platform_users')
            ->where('user_id', $userKey)
            ->where('platform_id', $platformid)
            ->where('owner_id', $userid)
            ->exists();
            
        if (!$userExists && $userData) {
            $createdAt = now()->format('Y-m-d H:i:s');
            DB::table('platform_users')->insert([
                'user_id' => $userKey,
                'owner_id' => $userid,
                'platform_id' => $platformid,
                'name' => $userData['displayName'] ?? $userData['name'] ?? '',
                'full_name' => $userData['displayName'] ?? '',
                'email' => $userData['emailAddress'] ?? '',
                'source' => "jira",
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    };

    // Helper function to parse Jira date format
    $parseJiraDate = function($dateString) {
        if (!$dateString) return null;
        try {
            return Carbon\Carbon::parse($dateString)->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return null;
        }
    };

    // Handle Project Events
    if ($webhookEvent === "project_created" && $project) {
        $creator = $project['lead'] ?? $user;
        
        if ($creator) {
            $ensureUserExists($creator['key'] ?? $creator['accountId'], $creator);
        }

        $exists = DB::table('projects')
            ->where('user_id', $userid)
            ->where('platform_id', $platformid)
            ->where('project_key', $project['key'])
            ->where('owner_id', $userid)
            ->exists();

        if (!$exists) {
            DB::table('projects')->insert([
                "user_id" => $userid,
                "platform_id" => $platformid,
                "project_key" => $project['key'],
                'owner_id' => $userid,
                "creator" => $creator['key'] ?? $creator['accountId'] ?? null,
                "name" => $project['name'],
                "description" => $project['description'] ?? '',
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

    // Handle Issue Events
    if (in_array($webhookEvent, ["jira:issue_created", "issue_created"]) && $issue) {
        $fields = $issue['fields'];
        $creator = $fields['creator'] ?? $user;
        $assignee = $fields['assignee'] ?? null;
        
        if ($creator) {
            $ensureUserExists($creator['key'] ?? $creator['accountId'], $creator);
        }
        
        if ($assignee) {
            $ensureUserExists($assignee['key'] ?? $assignee['accountId'], $assignee);
        }

        $createdAt = $parseJiraDate($fields['created']);
        $dueDate = $parseJiraDate($fields['duedate']);
        $estimate = null;
        
        if ($dueDate) {
            $estimate = now()->diffInDays(Carbon\Carbon::parse($dueDate), false);
        }

        // Handle story points or time estimates
        $storyPoints = $fields['customfield_10016'] ?? $fields['story_points'] ?? null;
        if ($storyPoints) {
            $estimate = $storyPoints;
        }

        $exists = DB::table('tasks')
            ->where('platform_id', $platformid)
            ->where('parent_id', $issue['key'])
            ->where('owner_id', $userid)
            ->exists();

        if (!$exists) {
            DB::table('tasks')->insert([
                "user_id" => $assignee['key'] ?? $assignee['accountId'] ?? $userid,
                "project_id" => $fields['project']['key'],
                "team_id" => "-",
                "platform_id" => $platformid,
                "parent_id" => $issue['key'],
                "title" => $fields['summary'],
                "description" => $fields['description'] ?? '',
                "status" => $fields['status']['name'],
                "priority" => $fields['priority']['name'] ?? null,
                "labels" => json_encode($fields['labels'] ?? []),
                "estimate" => $estimate,
                "due_date" => $dueDate,
                "checklists" => json_encode([]),
                'owner_id' => $userid,
                "source" => "jira",
                "last_synced_at" => now(),
                "is_deleted" => null,
                "created_at" => $createdAt ?: now(),
                "updated_at" => $createdAt ?: now(),
            ]);
        }
    }

    // Handle Issue Updates
    if (in_array($webhookEvent, ["jira:issue_updated", "issue_updated"]) && $issue) {
        $fields = $issue['fields'];
        
        $updateData = [
            'title' => $fields['summary'],
            'description' => $fields['description'] ?? '',
            'status' => $fields['status']['name'],
            'priority' => $fields['priority']['name'] ?? null,
            'labels' => json_encode($fields['labels'] ?? []),
            'last_synced_at' => now(),
            'updated_at' => now(),
        ];

        // Handle assignee changes
        if (isset($fields['assignee'])) {
            $assignee = $fields['assignee'];
            if ($assignee) {
                $ensureUserExists($assignee['key'] ?? $assignee['accountId'], $assignee);
                $updateData['user_id'] = $assignee['key'] ?? $assignee['accountId'];
            } else {
                $updateData['user_id'] = $userid; // Unassigned
            }
        }

        // Handle due date changes
        if (isset($fields['duedate'])) {
            $dueDate = $parseJiraDate($fields['duedate']);
            $updateData['due_date'] = $dueDate;
            $updateData['estimate'] = $dueDate ? now()->diffInDays(Carbon\Carbon::parse($dueDate), false) : null;
        }

        // Handle story points
        $storyPoints = $fields['customfield_10016'] ?? $fields['story_points'] ?? null;
        if ($storyPoints !== null) {
            $updateData['estimate'] = $storyPoints;
        }

        DB::table('tasks')
            ->where('parent_id', $issue['key'])
            ->where('platform_id', $platformid)
            ->where('owner_id', $userid)
            ->update($updateData);
    }

    // Handle Issue Deletion
    if (in_array($webhookEvent, ["jira:issue_deleted", "issue_deleted"]) && $issue) {
        DB::table('tasks')
            ->where('parent_id', $issue['key'])
            ->where('platform_id', $platformid)
            ->where('owner_id', $userid)
            ->update(["is_deleted" => 1]);
    }

    // Handle Subtask Creation (if issue type is subtask)
    if (in_array($webhookEvent, ["jira:issue_created", "issue_created"]) && $issue) {
        $fields = $issue['fields'];
        $issueType = $fields['issuetype'];
        $parent = $fields['parent'] ?? null;
        
        if ($issueType['subtask'] && $parent) {
            $createdAt = $parseJiraDate($fields['created']);
            
            $exists = DB::table('sub_issues')
                ->where('sub_task_id', $issue['key'])
                ->where('platform_id', $platformid)
                ->where('task_id', $parent['key'])
                ->where('owner_id', $userid)
                ->exists();

            if (!$exists) {
                DB::table('sub_issues')->insert([
                    "title" => $fields['summary'],
                    "sub_task_id" => $issue['key'],
                    "status" => $fields['status']['name'] === 'Done' ? 'completed' : 'incomplete',
                    "user_id" => $userid,
                    "platform_id" => $platformid,
                    'owner_id' => $userid,
                    "task_id" => $parent['key'],
                    "created_at" => $createdAt ?: now(),
                    "updated_at" => $createdAt ?: now(),
                ]);
            }
        }
    }

    // Handle Subtask Updates
    if (in_array($webhookEvent, ["jira:issue_updated", "issue_updated"]) && $issue) {
        $fields = $issue['fields'];
        $issueType = $fields['issuetype'];
        $parent = $fields['parent'] ?? null;
        
        if ($issueType['subtask'] && $parent) {
            DB::table('sub_issues')
                ->where('sub_task_id', $issue['key'])
                ->where('platform_id', $platformid)
                ->where('task_id', $parent['key'])
                ->where('owner_id', $userid)
                ->update([
                    'title' => $fields['summary'],
                    'status' => $fields['status']['name'] === 'Done' ? 'completed' : 'incomplete',
                    'updated_at' => now(),
                ]);
        }
    }

    // Handle Status Changes (if you want to track status types)
    if ($changelog && in_array($webhookEvent, ["jira:issue_updated", "issue_updated"])) {
        foreach ($changelog['items'] as $item) {
            if ($item['field'] === 'status') {
                // You can add logic here to handle status changes
                // For example, create status entries in status_jira table if needed
                $newStatus = $item['toString'];
                
                // Check if status exists in your status tracking table
                $statusExists = DB::table('status_jira')
                    ->where('user_id', $userid)
                    ->where('platform_id', $platformid)
                    ->where('type', $newStatus)
                    ->where('owner_id', $userid)
                    ->exists();

                if (!$statusExists) {
                    DB::table('status_jira')->insert([
                        "user_id" => $userid,
                        "platform_id" => $platformid,
                        "status_key" => strtolower(str_replace(' ', '_', $newStatus)),
                        'owner_id' => $userid,
                        "type" => $newStatus,
                    ]);
                }
            }
        }
    }

    return response()->json(['status' => 'success'], 200);
}
    public function webhook(Request $request)
    {
        $type = $request['type'];
        $action = $request['action'];
        $request_type = $request->platform_type;
        if($request_type == "jira"){
               $userid = $request->user;
        }else{
            $userid = Crypt::decryptString($request->user);

        }
     
       Log::info($request->all());
        $linked = DB::table("linked")->where([
            "type" => $request_type,
            "userid" => $userid
        ])->first();
        if($linked && $linked->type == "trello"){
                $this->saveTrello($request);
                return;
        }

          if($linked && $linked->type == "jira"){
            // return;
                // $this->saveJira($request);
                return;
        }

        if (!$linked) {
            return response()->json(['status' => 'no_linked_found'], 404);
        }



        $platformid = $linked->id;
        
        $data = $request['data'];
        $isSub = !empty($data['parentId']);
        $recordId = $data['id'];

        // Helper function to ensure user exists in platform_users table
        $ensureUserExists = function($userId, $userData = null) use ($platformid, $userid) {
            if (!$userId) return;
            
            $userExists = DB::table('platform_users')
                ->where('user_id', $userId)
                ->where('platform_id', $platformid)
                ->where('owner_id', $userid)
                ->exists();
                
            if (!$userExists && $userData) {
                $createdAt = now()->format('Y-m-d H:i:s');
                DB::table('platform_users')->insert([
                    'user_id' => $userId,
                    'owner_id' => $userid,
                    'platform_id' => $platformid,
                    'name' => $userData['name'] ?? '',
                    'full_name' => $userData['name'] ?? '',
                    'email' => $userData['email'] ?? '',
                    'source' => "linear",
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        };

        if ($type === "Issue") {
            if ($action === 'create') {
                if ($isSub) {
                    // Check and insert assignee user if exists
                    if (!empty($data['assigneeId'])) {
                        $assigneeData = $data['assignee'] ?? null;
                        $ensureUserExists($data['assigneeId'], $assigneeData);
                    }

                    DB::table('sub_issues')->insert([
                        'title'        => $data['title'],
                        'sub_task_id'  => $recordId,
                        'status'       => $data['state']['name'] ?? null,
                        'user_id'      => $data['assigneeId'] ?? null,
                        'platform_id'  => $platformid,
                        'owner_id'     => $userid,
                        'task_id'      => $data['parentId'],
                        'created_at'   => Carbon\Carbon::parse($data['createdAt'])->format('Y-m-d H:i:s'),
                        'updated_at'   => Carbon\Carbon::parse($data['updatedAt'])->format('Y-m-d H:i:s'),
                    ]);
                } else {
                    $dueDate = isset($data['dueDate']) ? Carbon\Carbon::parse($data['dueDate'])->format('Y-m-d H:i:s') : null;
                    $createdAt = Carbon\Carbon::parse($data['createdAt'])->format('Y-m-d H:i:s');
                    $estimate = $dueDate ? now()->diffInDays(Carbon\Carbon::parse($dueDate), false) : null;
                    $assignedUserId = $data['assignee']['id'] ?? null;

                    // Check and insert assignee user if exists
                    if ($assignedUserId) {
                        $ensureUserExists($assignedUserId, $data['assignee']);
                    }

                    $exists = DB::table('tasks')
                        ->where('user_id', $assignedUserId)
                        ->where('platform_id', $platformid)
                        ->where('owner_id', $userid)
                        ->where('parent_id', $recordId)
                        ->exists();

                    if (!$exists) {
                        DB::table('tasks')->insert([
                            "user_id"        => $assignedUserId,
                            "project_id"     => $data['project']['id'] ?? null,
                            "team_id"        => $data['team']['id'] ?? "-",
                            "platform_id"    => $platformid,
                            "owner_id"       => $userid,
                            "parent_id"      => $recordId,
                            "title"          => $data['title'],
                            "description"    => $data['description'],
                            "status"         => $data['state']['name'] ?? null,
                            "priority"       => $data['priority'] ?? null,
                            "labels"         => json_encode($data['labels'] ?? []),
                            "estimate"       => $data['estimate']   ?? $estimate,
                            "due_date"       => $dueDate,
                            "source"         => "linear",
                            "last_synced_at" => now(),
                            "is_deleted"     => null,
                            "created_at"     => $createdAt,
                            "updated_at"     => $createdAt,
                        ]);
                    }
                }
            }

            if ($action === 'update') {
                $updatedAt = Carbon\Carbon::parse($data['updatedAt'])->format('Y-m-d H:i:s');
                
                if ($isSub) {
                    // Check and insert assignee user if exists
                    if (!empty($data['assigneeId'])) {
                        $assigneeData = $data['assignee'] ?? null;
                        $ensureUserExists($data['assigneeId'], $assigneeData);
                    }

                    DB::table('sub_issues')
                        ->where('sub_task_id', $recordId)
                        ->where('platform_id', $platformid)
                        ->where('owner_id', $userid)
                        ->update([
                            'title'      => $data['title'],
                            'status'     => $data['state']['name'] ?? null,
                            'user_id'    => $data['assigneeId'] ?? null,
                            'updated_at' => $updatedAt,
                        ]);
                } else {
                    $dueDate = isset($data['dueDate']) ? Carbon\Carbon::parse($data['dueDate'])->format('Y-m-d H:i:s') : null;
                    $estimate = $dueDate ? now()->diffInDays(Carbon\Carbon::parse($dueDate), false) : null;
                    $assignedUserId = $data['assignee']['id'] ?? null;

                    // Check and insert assignee user if exists
                    if ($assignedUserId) {
                        $ensureUserExists($assignedUserId, $data['assignee']);
                    }

                    DB::table('tasks')
                        ->where('parent_id', $recordId)
                        ->where('platform_id', $platformid)
                        ->where('owner_id', $userid)
                        ->update([
                            'title'          => $data['title'],
                            'description'    => $data['description'],
                            'status'         => $data['state']['name'] ?? null,
                            'priority'       => $data['priority'] ?? null,
                            'labels'         => json_encode($data['labels'] ?? []),
                        "estimate"       => $data['estimate']   ?? $estimate,
                            'due_date'       => $dueDate,
                            'user_id'        => $assignedUserId,
                            'last_synced_at' => now(),
                            'updated_at'     => $updatedAt,
                        ]);
                }
            }

            if ($action === 'remove') {
                if ($isSub) {
                    DB::table('sub_issues')
                        ->where('sub_task_id', $recordId)
                        ->where('platform_id', $platformid)
                        ->where('owner_id', $userid)
                        ->update(["is_deleted" => 1]);
                } else {
                    DB::table('tasks')
                        ->where('parent_id', $recordId)
                        ->where('platform_id', $platformid)
                        ->where('owner_id', $userid)
                        ->update(["is_deleted" => 1]);
                }
            }

            if ($action === 'restore') {
                $updatedAt = Carbon\Carbon::parse($data['updatedAt'])->format('Y-m-d H:i:s');
                
                if ($isSub) {
                    DB::table('sub_issues')
                        ->where('sub_task_id', $recordId)
                        ->where('platform_id', $platformid)
                        ->where('owner_id', $userid)
                        ->update([
                            'is_deleted' => null,
                            'updated_at' => $updatedAt
                        ]);
                } else {
                    DB::table('tasks')
                        ->where('parent_id', $recordId)
                        ->where('platform_id', $platformid)
                        ->where('owner_id', $userid)
                        ->update([
                            'is_deleted' => null,
                            'updated_at' => $updatedAt
                        ]);
                }
            }
        }

        return response()->json(['status' => $action], 200);
    }
}