<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Http;
use Log;
use Mail;
use Auth;
use Carbon\Carbon;

class BotSender extends Controller
{
    /**
     * Fetch Linear user data and their tasks
     */
    public function getLinearUser()
    {
        // Fetch all Linear OAuth tokens from the 'linked' table
        $data = DB::table("linked")
            ->where("type", "linear")
            ->get();

        if ($data->isEmpty()) {
            return [];
        }

        // GraphQL query to fetch users and their assigned issues with due dates
        $query = '
        query {
            users(first: 50) {
                nodes {
                    id
                    name
                    displayName
                    email
                    assignedIssues(first: 20) {
                        nodes {
                            id
                            title
                            state {
                                name
                            }
                            dueDate
                            project {
                                id
                                name
                            }
                        }
                    }
                }
            }
        }
        ';

        // Initialize task schedule
        $taskSchedule = [];
        $currentTime = now();
        $twoDaysFromNow = $currentTime->copy()->addHours(48);

        // Iterate through each OAuth token
        foreach ($data as $record) {
            $token = $record->token;

            if (!$token) {
                continue;
            }

            // Make GraphQL request
            $response = Http::withToken($token)->post('https://api.linear.app/graphql', [
                'query' => $query
            ]);

            $responseData = $response->json();

            if (isset($responseData['errors'])) {
                \Log::warning("GraphQL error for token: {$token}", ['errors' => $responseData['errors']]);
                continue;
            }

            // Fetch user email from users table based on userid
            $userRecord = DB::table('users')
                ->where('id', $record->userid)
                ->first();

            if (!$userRecord) {
                \Log::warning("No user found in users table for userid: {$record->userid}");
                continue;
            }

            // Process tasks
            foreach ($responseData['data']['users']['nodes'] as $user) {
                foreach ($user['assignedIssues']['nodes'] as $issue) {
                    $dueDate = null;
                    $dueStatus = null;

                    if (!empty($issue['dueDate'])) {
                        try {
                            $dueDate = Carbon::parse($issue['dueDate']);
                            if ($dueDate->lessThan($currentTime)) {
                                $dueStatus = '🚨 Task expired!';
                            } elseif ($dueDate->between($currentTime, $twoDaysFromNow)) {
                                $dueStatus = '⚠️ Task due within 48 hours!';
                            }
                        } catch (\Exception $e) {
                            \Log::error("Failed to parse dueDate for issue {$issue['id']}: {$issue['dueDate']}", ['error' => $e->getMessage()]);
                        }
                    }

                    $taskSchedule[] = [
                        'user' => $user['displayName'],
                        'email' => $userRecord->email,
                        'task_id' => $issue['id'],
                        'userid' => $record->userid,
                        'task_title' => $issue['title'],
                        'project' => $issue['project'] ? $issue['project']['name'] : 'No Project',
                        'state' => $issue['state']['name'],
                        'due_date' => $dueDate ? $dueDate->toDateString() : 'No Due Date',
                        'due_status' => $dueStatus,
                        'token_source' => $token,
                        'raw_due_date' => $issue['dueDate'],
                        'source' => 'Linear',
                        'url' => "https://linear.app/issue/{$issue['id']}" // Added URL for Linear tasks
                    ];
                }
            }
        }

        return $taskSchedule;
    }

    /**
     * Fetch Trello user data and their tasks (cards)
     */
    public function getTrelloUser()
    {
        // Fetch all Trello OAuth tokens from the 'linked' table
        $data = DB::table("linked")
            ->where("type", "trello")
            ->get();

        if ($data->isEmpty()) {
            return [];
        }

        // Initialize task schedule
        $taskSchedule = [];
        $currentTime = now();
        $twoDaysFromNow = $currentTime->copy()->addHours(48);

        // Iterate through each OAuth token
        foreach ($data as $record) {
            $accessToken = $record->token;
            $userId = $record->userid;

            // Fetch user email from users table
            $userRecord = DB::table('users')
                ->where('id', $userId)
                ->first();

            if (!$userRecord) {
                \Log::warning("No user found in users table for userid: {$userId}");
                continue;
            }

            // Make Trello API request to fetch cards for the authenticated user
            try {
                $response = Http::timeout(5)->get("https://api.trello.com/1/members/me/cards", [
                    'key' => env('TRELLO_API_KEY','e39869487a72d56e6758bd57b67fca4f'),
                    'token' => $accessToken,
                    'fields' => 'name,desc,due,idBoard,idList,labels,closed,pos,shortLink,url,dateLastActivity',
                    'filter' => 'open'
                ]);

                if ($response->failed()) {
                    \Log::warning("Trello API error for user {$userId}", [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'headers' => $response->headers()
                    ]);
                    continue;
                }

                $cards = $response->json();

                // Process cards (tasks)
                foreach ($cards as $card) {
                    $dueDate = null;
                    $dueStatus = null;

                    if (!empty($card['due'])) {
                        try {
                            $dueDate = Carbon::parse($card['due']);
                            if ($dueDate->lessThan($currentTime)) {
                                $dueStatus = '🚨 Task expired!';
                            } elseif ($dueDate->between($currentTime, $twoDaysFromNow)) {
                                $dueStatus = '⚠️ Task due within 48 hours!';
                            }
                        } catch (\Exception $e) {
                            \Log::error("Failed to parse dueDate for card {$card['id']}: {$card['due']}", ['error' => $e->getMessage()]);
                        }
                    }

                    $taskSchedule[] = [
                        'user' => $userRecord->name ?? 'Unknown User',
                        'email' => $userRecord->email,
                        'task_id' => $card['id'],
                        'userid' => $userId,
                        'task_title' => $card['name'],
                        'project' => $card['idBoard'],
                        'state' => 'N/A',
                        'due_date' => $dueDate ? $dueDate->toDateString() : 'No Due Date',
                        'due_status' => $dueStatus,
                        'token_source' => $accessToken,
                        'raw_due_date' => $card['due'] ?? null,
                        'source' => 'Trello',
                        'url' => $card['url'] ?? '#' // Added URL for Trello tasks
                    ];
                }

                // Add delay to avoid rate limits
                usleep(100000);

            } catch (\Exception $e) {
                \Log::error("Trello API request failed for user {$userId}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                continue;
            }
        }

        return $taskSchedule;
    }

    /**
     * Modified showChannelList to include Trello tasks
     */
    public function showChannelList()
    {
        try {
            // Get Linear tasks
            $linearTasks = $this->getLinearUser();

            // Get Trello tasks
            $trelloTasks = $this->getTrelloUser();

            // Combine tasks
            $tasks = array_merge($linearTasks, $trelloTasks);

            if (empty($tasks)) {
                return response()->json(['message' => 'No Linear or Trello tasks found'], 404);
            }

            $allChannels = [];
            $processedAccounts = 0;
            $processedChannelIds = [];
            $userTasks = [];

            // Group tasks by user email
            foreach ($tasks as $task) {
                if ($task['due_status'] != null) {
                    $userTasks[$task['email']][] = $task;
                }
            }

            // Process each user
            foreach ($userTasks as $email => $userTaskList) {
                // Find userid from the first task
                $userId = $userTaskList[0]['userid'];

                // Get notification preferences
                $userNotificationChannels = $this->getUserNotificationChannels($userId);
                $slackEnabled = in_array('slack', $userNotificationChannels);
                $emailEnabled = in_array('email', $userNotificationChannels);

                if (!$slackEnabled && !$emailEnabled) {
                    continue;
                }

                // Prepare consolidated notification message for all tasks
                $messages = [];
                foreach ($userTaskList as $task) {
                    $messages[] = $this->prepareNotificationMessage($task);
                }
                $consolidatedMessage = implode("\n\n---\n\n", $messages);

                // Send email notification if enabled
                if ($emailEnabled && !empty($email)) {
                    $this->sendEmailNotification($email, $consolidatedMessage, $userTaskList);
                }

                // Send Slack notification if enabled
                if ($slackEnabled) {
                    $slackAccount = DB::table("linked")
                        ->where("type", "slack")
                        ->where("userid", $userId)
                        ->first();

                    if ($slackAccount) {
                        $token = $slackAccount->token;
                        $channels = $this->getSlackChannels($token, $userId);

                        $allChannels = array_merge($allChannels, $channels);
                        $processedChannelIds = array_merge($processedChannelIds, array_column($channels, 'id'));
                        $processedChannelIds = array_unique($processedChannelIds);

                        foreach ($channels as $channel) {
                            $this->sendTestMessage($token, $channel['id'], $consolidatedMessage);
                        }

                        $processedAccounts++;
                    }
                }
            }

            $stats = [
                'total_unique_channels' => count($allChannels),
                'duplicate_channels_filtered' => count($processedChannelIds) - count(array_unique(array_column($allChannels, 'id'))),
                'account_count' => $processedAccounts
            ];

            return response()->json([
                'channels' => $allChannels,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Notification delivery error: ' . $e->getMessage());
            return response()->json(['error' => 'Error processing notifications: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get user notification channel preferences
     */
    protected function getUserNotificationChannels($userId)
    {
        $channels = DB::table("notification_channel")
            ->where("userid", $userId)
            ->pluck("channel")
            ->toArray();

        return $channels;
    }

    /**
     * Prepare notification message
     */
    protected function prepareNotificationMessage($task)
    {
        $message = "👤 User: @{$task['user']}\n";
        $message .= "🛠️ Source: {$task['source']}\n";
        $message .= "📌 Status: {$task['due_status']}\n";
        $message .= "📝 Task: {$task['task_title']}\n";

        if (!empty($task['raw_due_date'])) {
            $message .= "📅 Due Date: {$task['raw_due_date']}\n";
        }

        $message .= "🔄 Task State: {$task['state']}";

        return $message;
    }

    /**
     * Send email notification
     */
    protected function sendEmailNotification($recipient, $messageContent, $tasks)
    {
        try {
            $subject = "Task Notifications: Upcoming and Expired Tasks";

            Mail::send("mail.report", [
                "message" => $messageContent,
                "tasks" => $tasks,
            ], function ($mail) use ($recipient, $subject) {
                $mail->to($recipient)
                    ->subject($subject);
            });

            Log::info("Consolidated email notification sent to: " . $recipient);
            return true;
        } catch (\Exception $e) {
            Log::error("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Slack channels for user
     */
    protected function getSlackChannels($token, $userId)
    {
        $cursor = null;
        $accountChannels = [];

        do {
            $params = [
                'limit' => 200,
                'exclude_archived' => true
            ];

            if ($cursor) {
                $params['cursor'] = $cursor;
            }

            $response = Http::timeout(5)
                ->withToken($token)
                ->get('https://slack.com/api/conversations.list', $params);

            $data = $response->json();

            if ($data['ok']) {
                $channels = collect($data['channels'])->map(function ($channel) use ($userId) {
                    return [
                        'id' => $channel['id'],
                        'name' => $channel['name'],
                        'topic' => $channel['topic']['value'] ?? '',
                        'member_count' => $channel['num_members'] ?? 0,
                        'associated_user_id' => $userId
                    ];
                })->toArray();

                $accountChannels = array_merge($accountChannels, $channels);
                $cursor = $data['response_metadata']['next_cursor'] ?? null;
            } else {
                Log::error('Slack API error: ' . ($data['error'] ?? 'Unknown error'));
                $cursor = null;
            }

            if ($cursor) {
                usleep(100000);
            }
        } while ($cursor && !empty($cursor));

        return $accountChannels;
    }

    /**
     * Send a test message to a Slack channel
     */
    protected function sendTestMessage($token, $channelId, $message)
    {
        try {
            $response = Http::timeout(3)
                ->withToken($token)
                ->post('https://slack.com/api/chat.postMessage', [
                    'channel' => $channelId,
                    'text' => $message
                ]);

            if (!$response->json('ok')) {
                Log::warning('Failed to send Slack message: ' . ($response->json('error') ?? 'Unknown error'));
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Slack message error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send messages to multiple channels, avoiding duplicates
     */
    public function scheduleMessagesForChannels(array $channelIds, string $token, string $message)
    {
        $uniqueChannelIds = array_unique($channelIds);

        foreach ($uniqueChannelIds as $channelId) {
            try {
                $response = Http::timeout(3)
                    ->withToken($token)
                    ->post('https://slack.com/api/chat.postMessage', [
                        'channel' => $channelId,
                        'text' => $message
                    ]);

                if (!$response->json('ok')) {
                    Log::warning("Failed to send message to channel {$channelId}: " .
                        ($response->json('error') ?? 'Unknown error'));
                }

                usleep(300000);

            } catch (\Exception $e) {
                Log::error("Error sending message to channel {$channelId}: " . $e->getMessage());
                continue;
            }
        }

        return [
            'message_sent' => true,
            'unique_channels_processed' => count($uniqueChannelIds),
            'duplicate_channels_skipped' => count($channelIds) - count($uniqueChannelIds)
        ];
    }
}