<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Crypt;
use Carbon;
use Log;

class Webhook extends Controller
{
    public function webhook(Request $request)
    {
        $type = $request['type'];
        $action = $request['action'];
        $request_type = $request->platform_type;
        $userid = Crypt::decryptString($request->user);

        $linked = DB::table("linked")->where([
            "type" => $request_type,
            "userid" => $userid
        ])->first();

        if (!$linked) {
            return response()->json(['status' => 'no_linked_found'], 404);
        }

        Log::info($request->all());

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