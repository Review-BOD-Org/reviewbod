<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Cache;
use Http;
use Log;
use Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Hash;
use Ratchet\Client\WebSocket;
use Ratchet\Client\Connector;
use Exception;
use WebSocket\Client;
use Mail;
class Dash extends Controller
{
    //


    public function getLinearUser()
    {
        // Check if cached data exists
        $cacheKey = 'linear_data_' . auth()->id();
        if (Cache::has($cacheKey)) {
            // Return the cached data directly as an array, not wrapped in a response
            return Cache::get($cacheKey);
        }

        // Check if we've hit the rate limit
        $rateLimitKey = 'linear_api_' . auth()->id();
        $requestCount = Cache::get($rateLimitKey, 0);

        // Rate limit: 100 requests per hour
        // if ($requestCount >= 100) {
        //     return response()->json(['message' => 'Rate limit exceeded. Please try again later.'], 429);
        // }

        // Get token from database
        $data = DB::table("linked")->where(["userid" => auth()->id(), "type" => "linear"])->first();

        $token = $data->token ?? null;

        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            // Increment the request counter for each API call
            $requestIncrement = 0;

            // Fetch data in smaller chunks
            $usersData = $this->fetchLinearData($token, $this->getUsersQuery(), $rateLimitKey, $requestCount, $requestIncrement);
            $requestIncrement++;

            $teamsData = $this->fetchLinearData($token, $this->getTeamsQuery(), $rateLimitKey, $requestCount, $requestIncrement);
            $requestIncrement++;

            $projectsData = $this->fetchLinearData($token, $this->getProjectsQuery(), $rateLimitKey, $requestCount, $requestIncrement);

            // Update the rate limit counter
            Cache::put($rateLimitKey, $requestCount + $requestIncrement, now()->addHour());

            // Combine and transform the data
            $combinedData = [
                'users' => $usersData['users'] ?? ['nodes' => []],
                'teams' => $teamsData['teams'] ?? ['nodes' => []],
                'projects' => $projectsData['projects'] ?? ['nodes' => []]
            ];

            $transformedData = $this->transformLinearData($combinedData);

            // Cache the result for 5 minutes to improve performance
            Cache::put($cacheKey, $transformedData, now()->addMinutes(1));

            // Return the transformed data directly as an array
            return $transformedData;
        } catch (\Exception $e) {
            Log::error('Linear API Exception', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch data from Linear: ' . $e->getMessage()], 500);
        }
    }

    private function fetchLinearData($token, $query, $rateLimitKey, $baseCount, $increment)
    {
        try {
            $response = Http::withToken($token)
                ->timeout(15)
                ->retry(3, 1000)
                ->post('https://api.linear.app/graphql', [
                    'query' => $query
                ]);

            $data = $response->json();

            if (isset($data['errors'])) {
                Log::error('Linear API Error', ['errors' => $data['errors'], 'query' => $query]);
                throw new \Exception('GraphQL error: ' . json_encode($data['errors']));
            }

            return $data['data'] ?? [];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function getUsersQuery()
    {
        return '
        query {
            users(first: 20) {
                nodes {
                    id
                    name
                    displayName
                    email
                    avatarUrl
                    teams(first: 3) {
                        nodes {
                            id
                            name
                        }
                    }
                    assignedIssues(first: 20, orderBy: updatedAt) {
                        nodes {
                            id
                            title
                            state {
                                id
                                name
                                type
                            }
                            createdAt
                            updatedAt
                            completedAt
                        }
                    }
                }
            }
        }
        ';
    }

    private function getTeamsQuery()
    {
        return '
            query {
                teams(first: 20) {
                    nodes {
                        id
                        name
                        createdAt
                        members {
                            nodes {
                                id
                            }
                        }
                        states(first: 10) {
                            nodes {
                                id
                                name
                                color
                                type
                            }
                        }
                        issues {
                            nodes {
                                id
                                title
                                state {
                                    name
                                    type
                                }
                                createdAt
                                completedAt
                            }
                        }
                    }
                }
            }
        ';
    }


    private function getProjectsQuery()
    {
        return '
        query {
            projects(first: 20) {
                nodes {
                    id
                    name
                    state
                    progress
                    startDate
                    targetDate
                    issues(first: 30) {
                        nodes {
                            id
                            title
                            state {
                                name
                                type
                            }
                        }
                    }
                }
            }
        }
        ';
    }
    private function transformLinearData($data)
    {

        $users = [];
        $teams = [];
        $projects = [];
        $tasksByState = [
            'completed' => 0,
            'in_progress' => 0,
            'backlog' => 0,
            'blocked' => 0
        ];
        $taskCompletionTrend = [];

        // Process users and their issues
        foreach ($data['users']['nodes'] ?? [] as $user) {
            $assignedIssues = $user['assignedIssues']['nodes'] ?? [];
            $completedTasks = 0;
            $totalTasks = count($assignedIssues);
            $onTimeCompletions = 0;
            $avgCompletionTime = 0;
            $totalCompletionTime = 0;
            $completedIssuesCount = 0;

            foreach ($assignedIssues as $issue) {
                $stateType = $issue['state']['type'] ?? '';
                $stateName = strtolower($issue['state']['name'] ?? '');

                // Count issues by state for the pie chart
                if ($stateType === 'completed' || strpos($stateName, 'done') !== false || strpos($stateName, 'complete') !== false) {
                    $tasksByState['completed']++;
                    $completedTasks++;
                    $completedIssuesCount++;

                    if (isset($issue['completedAt']) && isset($issue['createdAt'])) {
                        $created = new \DateTime($issue['createdAt']);
                        $completed = new \DateTime($issue['completedAt']);
                        $completionTime = $created->diff($completed)->days;
                        $totalCompletionTime += $completionTime;

                        // Check if completed on time (arbitrary threshold of 7 days)
                        if ($completionTime <= 7) {
                            $onTimeCompletions++;
                        }
                    }
                } elseif ($stateType === 'started' || strpos($stateName, 'progress') !== false || strpos($stateName, 'working') !== false) {
                    $tasksByState['in_progress']++;
                } elseif ($stateType === 'unstarted' || strpos($stateName, 'backlog') !== false || strpos($stateName, 'todo') !== false) {
                    $tasksByState['backlog']++;
                } elseif ($stateType === 'canceled' || strpos($stateName, 'block') !== false) {
                    $tasksByState['blocked']++;
                }
            }

            // Calculate average completion time
            if ($completedIssuesCount > 0) {
                $avgCompletionTime = $totalCompletionTime / $completedIssuesCount;
            }

            $onTimePercentage = $totalTasks > 0 ? ($onTimeCompletions / $totalTasks) * 100 : 0;
            $slack = DB::table("linked")->where(["type" => "slack", "userid" => Auth::id()])->first();

            $users[] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'] ?? '',
                'avatar' => $user['avatarUrl'] ?? '',
                'teams' => array_map(fn($team) => [
                    'id' => $team['id'],
                    'name' => $team['name']
                ], $user['teams']['nodes'] ?? []),
                'tasks_completed' => $completedTasks,
                'total_tasks' => $totalTasks,
                'avg_completion_time' => round($avgCompletionTime, 1),
                'on_time_percentage' => round($onTimePercentage),
                // 'slack_activity' => $slack ? $this->getUnreadMessagesCount($slack->token) : 0, // Placeholder - Linear API doesn't provide Slack data
                'assigned_issues' => array_map(fn($issue) => [
                    'id' => $issue['id'],
                    'title' => $issue['title'],
                    'state' => $issue['state']['name'] ?? '',
                    'state_type' => $issue['state']['type'] ?? '',
                    'state_color' => $issue['state']['color'] ?? '',
                    'created_at' => $issue['createdAt'] ?? null,
                    'updated_at' => $issue['updatedAt'] ?? null,
                    'completed_at' => $issue['completedAt'] ?? null,
                ], $assignedIssues)
            ];
        }

        $allCompletedTasks = [];

        foreach ($data['teams']['nodes'] ?? [] as $team) {
            $teamIssues = $team['issues']['nodes'] ?? [];

            // Process all tasks individually for trend data instead of grouping by week

            foreach ($teamIssues as $issue) {

                if (!empty($issue['completedAt'])) {
                    $completedAt = new \DateTime($issue['completedAt']);
                    $allCompletedTasks[] = [
                        'date' => $completedAt->format('Y-m-d'),
                        'title' => $issue['title'] ?? 'Unknown Task',
                        'team' => $team['name'],
                        'id' => $issue['id'],
                        'status' => $issue['state']['name']
                    ];
                }
            }



            // [rest of team processing remains the same]
            $teams[] = [
                'id' => $team['id'],
                'total_users' => count($team['users']['nodes'] ?? []),
                'name' => $team['name'],
                'states' => array_map(fn($state) => [
                    'id' => $state['id'],
                    'name' => $state['name'],
                    'color' => $state['color'] ?? '',
                    'type' => $state['type'] ?? ''
                ], $team['states']['nodes'] ?? []),
                'issues_count' => count($teamIssues),
                'completed_count' => count(array_filter($teamIssues, fn($issue) => !empty($issue['completedAt'])))
            ];
        }

        // Sort all completed tasks by date
        usort($allCompletedTasks, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        // Format all completed tasks for the trend chart
        $taskCompletionTrend = $allCompletedTasks;


        // Process projects
        foreach ($data['projects']['nodes'] ?? [] as $project) {
            $projectIssues = $project['issues']['nodes'] ?? [];
            $completedCount = 0;
            $inProgressCount = 0;
            $backlogCount = 0;

            foreach ($projectIssues as $issue) {
                $stateType = $issue['state']['type'] ?? '';

                if ($stateType === 'completed') {
                    $completedCount++;
                } elseif ($stateType === 'started') {
                    $inProgressCount++;
                } else {
                    $backlogCount++;
                }
            }

            $projects[] = [
                'id' => $project['id'],
                'name' => $project['name'],
                'state' => $project['state'] ?? '',
                'progress' => $project['progress'] ?? 0,
                'start_date' => $project['startDate'] ?? null,
                'target_date' => $project['targetDate'] ?? null,
                'completed_issues' => $completedCount,
                'in_progress_issues' => $inProgressCount,
                'backlog_issues' => $backlogCount,
                'total_issues' => count($projectIssues)
            ];
        }

        // Generate dashboard data with fallbacks for empty data
        // dd($tasksByState);
        $arr = [
            'users' => $users,
            'teams' => $teams,
            'projects' => $projects,
            'data' => $data,
            'dashboard_data' => [
                'stats' => [
                    'tasks_completed' => $tasksByState['completed'],
                    'tasks_uncompleted' => $tasksByState['in_progress'],
                    'avg_completion_time' => count($users) ? array_sum(array_column($users, 'avg_completion_time')) / count($users) : 0,
                    'slack_messages' => $slack ? $this->getUnreadMessagesCount($slack->token) : 0,
                    'on_time_completion' => count($users) ? array_sum(array_column($users, 'on_time_percentage')) / count($users) : 0
                ],
                'task_completion_trend' => $taskCompletionTrend ?: $this->generatePlaceholderTrend(),
                'task_distribution' => [
                    ['label' => 'Completed', 'value' => $tasksByState['completed']],
                    ['label' => 'In Progress', 'value' => $tasksByState['in_progress']],
                    ['label' => 'Backlog', 'value' => $tasksByState['backlog']],
                    ['label' => 'Blocked', 'value' => $tasksByState['blocked']]
                ],
                'project_status' => array_map(fn($project) => [
                    'name' => $project['name'],
                    'completed' => $project['completed_issues'],
                    'in_progress' => $project['in_progress_issues'],
                    'backlog' => $project['backlog_issues']
                ], array_slice($projects, 0, 4)), // First 4 projects
                'slack_activity' => [
                    ['channel' => '#general', 'messages' => rand(300, 500)],
                    ['channel' => '#engineering', 'messages' => rand(500, 700)],
                    ['channel' => '#design', 'messages' => rand(300, 400)],
                    ['channel' => '#product', 'messages' => rand(400, 500)],
                    ['channel' => '#random', 'messages' => rand(200, 300)]
                ],
                'response_time' => [
                    'Mon' => rand(15, 50),
                    'Tue' => rand(15, 45),
                    'Wed' => rand(10, 30),
                    'Thu' => rand(15, 40),
                    'Fri' => rand(20, 45),
                    'Sat' => rand(10, 25),
                    'Sun' => rand(15, 30)
                ]
            ]
        ];

        return $arr;
    }

    // Helper function to generate placeholder trend data if no data is available
    private function generatePlaceholderTrend()
    {
        $trend = [];
        $currentWeek = (int) date('W');

        for ($i = 5; $i >= 0; $i--) {
            $weekNum = $currentWeek - $i;
            if ($weekNum <= 0) {
                $weekNum += 52; // Adjust for year boundary
            }

            $trend[] = [
                'week' => 'Week ' . $weekNum,
                'created' => 0,
                'completed' => 0
            ];
        }

        return $trend;
    }

    public function getUnreadMessagesCount($oauthToken)
    {
        $cacheKey = 'slack_unread_messages_' . md5($oauthToken);

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($oauthToken) {
            $response = Http::withToken($oauthToken)->get('https://slack.com/api/conversations.list', [
                'types' => 'im,mpim' // Fetch Direct Messages & Multi-Party IMs
            ]);

            if ($response->failed()) {
                return 0; // Return 0 if API request fails
            }

            $channels = $response->json()['channels'] ?? [];
            $unreadCount = 0;

            foreach ($channels as $channel) {
                if (!empty($channel['id'])) {
                    $channelResponse = Http::withToken($oauthToken)->get('https://slack.com/api/conversations.info', [
                        'channel' => $channel['id']
                    ]);

                    if ($channelResponse->failed()) {
                        continue;
                    }

                    $channelData = $channelResponse->json()['channel'] ?? [];
                    $unreadCount += $channelData['unread_count_display'] ?? 0;
                }
            }

            return $unreadCount;
        });
    }
    public function dash(Request $request)
    {
        // if($request->type){
        //     DB::table("users")->where(["id"=>Auth::id()])->update(["service"=>$request->type]);
        //     return redirect("/dashboard");
        // }
        // $res = new FetchData();
        // $res = $res->getData("dash");
        // // dd($res);
        // // dd($res['teams']);

        // // $res = $data->getData();
        // // dd($res);
        // // dd($res);
        // // Handle different response types
        // if ($res instanceof \Illuminate\Http\JsonResponse) {
        //     // Extract data from JsonResponse
        //     $responseData = $res->getData('dash');

        //     // Check if it's an error response
        //     if (isset($responseData['message']) && ($res->getStatusCode() != 200)) {
        //         // Handle error
        //         $error =  $responseData['message'];
        //         Log::info("Error Dataaa: $error");
        //         // return view("dash.error", ['error' => $responseData['message']]);
        //     }

        //     $res = $responseData;
        // }

        // $slack = DB::table("linked")->where(["type"=>"slack","userid"=>Auth::id()])->first();
        // $count_ = DB::table("linked")->where(["userid"=>Auth::id()])->count();

        // $message_count = 0;

        // if($slack && $this->getSlackData() != null){
        //     $message_count = count($this->getSlackData());
        // }
        // if(!isset($res['projects']['dashboard_data']) &&  $count_ != 0){
        //     return view("dash.error");

        // }
        // dd($res['projects']['dashboard_data']);
        $service = Auth::user()->service;
        $data = DB::table("linked")->where(["userid" => auth()->id(), "type" => "$service"])->first();
        $id = 1;
        return view("dash.index", compact("data", "id"));
    }

    public function getTaskCompletionTrend(Request $request)
    {
        $res = $this->getLinearUser();
        if ($res instanceof \Illuminate\Http\JsonResponse) {
            $responseData = $res->getData(true);
            $res = $responseData;
        }

        $timeframe = $request->input('timeframe', 'monthly');
        $users = $res['users'] ?? []; // Use users array directly

        $today = Carbon::today();
        $chartData = [];

        // Set time range
        $startDate = match ($timeframe) {
            'daily' => $today->copy()->subDays(1),
            'weekly' => $today->copy()->subDays(7),
            'monthly' => $today->copy()->subDays(30),
            default => $today->copy()->subDays(30),
        };

        // Initialize chart data
        $currentDate = $startDate->copy();
        while ($currentDate <= $today) {
            $key = match ($timeframe) {
                'daily' => $currentDate->format('M d'),
                'weekly' => 'Week ' . $currentDate->weekOfYear,
                'monthly' => $currentDate->format('M Y'),
                default => $currentDate->format('M d'),
            };
            if (!isset($chartData[$key])) {
                $chartData[$key] = ['week' => $key, 'completed' => 0, 'created' => 0];
            }
            $currentDate->add(match ($timeframe) {
                'daily' => '1 day',
                'weekly' => '7 days',
                'monthly' => '1 month',
                default => '1 day',
            })->startOfDay();
        }

        // Process user-assigned issues
        foreach ($users as $user) {
            if (isset($user['assigned_issues'])) {
                foreach ($user['assigned_issues'] as $issue) {
                    $createdAt = isset($issue['created_at']) ? Carbon::parse($issue['created_at']) : null;
                    $completedAt = isset($issue['completed_at']) ? Carbon::parse($issue['completed_at']) : null;

                    if (!$createdAt) {
                        \Log::info("Skipping issue due to missing created_at: " . json_encode($issue));
                        continue;
                    }

                    if ($createdAt->gte($startDate)) {
                        $key = match ($timeframe) {
                            'daily' => $createdAt->format('M d'),
                            'weekly' => 'Week ' . $createdAt->weekOfYear,
                            'monthly' => $createdAt->format('M Y'),
                            default => $createdAt->format('M d'),
                        };
                        if (!isset($chartData[$key])) {
                            $chartData[$key] = ['week' => $key, 'completed' => 0, 'created' => 0];
                        }
                        $chartData[$key]['created']++;
                    }

                    if ($completedAt && $completedAt->gte($startDate) && isset($issue['state_type']) && $issue['state_type'] === 'completed') {
                        $key = match ($timeframe) {
                            'daily' => $completedAt->format('M d'),
                            'weekly' => 'Week ' . $completedAt->weekOfYear,
                            'monthly' => $completedAt->format('M Y'),
                            default => $completedAt->format('M d'),
                        };
                        if (!isset($chartData[$key])) {
                            $chartData[$key] = ['week' => $key, 'completed' => 0, 'created' => 0];
                        }
                        $chartData[$key]['completed']++;
                    }
                }
            }
        }

        $result = array_values($chartData);
        usort($result, fn($a, $b) => strtotime($a['week']) <=> strtotime($b['week']));

        \Log::info("Task Completion Trend Result: " . json_encode($result));
        return response()->json($result);
    }

    public function getTaskDistribution(Request $request)
    {
        $res = new FetchData();
        $res = $res->getData("dash");

        $service = Auth::user()->service;

        $users = $res['users'] ?? [];
        $teamFilter = $request->input('team');
        $withTeams = $request->boolean('with_teams');

        if ($service === 'trello') {
            $statusCounts = ['Done' => 0, 'Doing' => 0, 'To Do' => 0];
            $teams = [];



            foreach ($users as $user) {
                if (isset($user['assigned_issues'])) {
                    foreach ($user['assigned_issues'] as $issue) {
                        $issueTeams = array_column($user['teams'] ?? [], 'name');
                        if ($withTeams) {
                            foreach ($issueTeams as $team) {
                                if (!in_array($team, $teams)) {
                                    $teams[] = $team;
                                }
                            }
                        }

                        if ($teamFilter && !in_array($teamFilter, $issueTeams)) {
                            continue;
                        }

                        if (isset($issue['state_type'])) {
                            switch ($issue['state_type']) {
                                case 'done':
                                    $statusCounts['Done']++;
                                    break;
                                case 'doing':
                                    $statusCounts['Doing']++;
                                    break;
                                case 'to_do':
                                    $statusCounts['To Do']++;
                                    break;
                            }
                        }
                    }
                }
            }

            $chartData = [
                'labels' => array_keys($statusCounts),
                'datasets' => [
                    [
                        'data' => array_values($statusCounts),
                        'backgroundColor' => ['#22c55e', '#3b82f6', '#f59e0b'],
                        'borderWidth' => 0,
                        'hoverOffset' => 4,
                    ]
                ],
            ];
        } else {
            $statusCounts = ['Completed' => 0, 'In Progress' => 0, 'Backlog' => 0, 'Canceled' => 0];
            $teams = [];

            foreach ($users as $user) {
                if (isset($user['assigned_issues'])) {
                    foreach ($user['assigned_issues'] as $issue) {
                        $issueTeams = array_column($user['teams'] ?? [], 'name');
                        if ($withTeams) {
                            foreach ($issueTeams as $team) {
                                if (!in_array($team, $teams)) {
                                    $teams[] = $team;
                                }
                            }
                        }

                        if ($teamFilter && !in_array($teamFilter, $issueTeams)) {
                            continue;
                        }

                        if (isset($issue['state_type'])) {
                            switch ($issue['state_type']) {
                                case 'completed':
                                    $statusCounts['Completed']++;
                                    break;
                                case 'started':
                                    $statusCounts['In Progress']++;
                                    break;
                                case 'backlog':
                                    $statusCounts['Backlog']++;
                                    break;
                                case 'canceled':
                                    $statusCounts['Canceled']++;
                                    break;
                            }
                        }
                    }
                }
            }

            $chartData = [
                'labels' => array_keys($statusCounts),
                'datasets' => [
                    [
                        'data' => array_values($statusCounts),
                        'backgroundColor' => ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444'],
                        'borderWidth' => 0,
                        'hoverOffset' => 4,
                    ]
                ],
            ];
        }

        if ($withTeams) {
            $chartData['teams'] = $teams;
        }

        \Log::info("Task Distribution Result: " . json_encode($chartData));
        return response()->json($chartData);
    }
    public function getProjectStatus()
    {
        $res = $this->getLinearUser();
        if ($res instanceof \Illuminate\Http\JsonResponse) {
            $responseData = $res->getData(true);
            $res = $responseData;
        }

        $projects = $res['projects'] ?? []; // Use projects for labels
        $users = $res['users'] ?? []; // Use users for issue details
        $projectData = [];

        // Initialize project counts
        foreach ($projects as $project) {
            $projectName = $project['name'] ?? 'Unnamed Project';
            // Team name from first associated issue or project data
            $teamName = isset($project['issues']['nodes'][0]['team']) ? $project['issues']['nodes'][0]['team']['name'] : '';
            $label = $teamName ? "$projectName ($teamName)" : $projectName;
            $projectData[$label] = ['Completed' => 0, 'In Progress' => 0, 'Backlog' => 0];
        }

        // Count issues from users.assigned_issues, mapping to projects
        foreach ($users as $user) {
            if (isset($user['assigned_issues'])) {
                foreach ($user['assigned_issues'] as $issue) {
                    // Find matching project (simplified: match by title substring or ID if available)
                    foreach ($projects as $project) {
                        $projectName = $project['name'] ?? 'Unnamed Project';
                        $teamName = isset($user['teams'][0]['name']) ? $user['teams'][0]['name'] : '';
                        $label = $teamName ? "$projectName ($teamName)" : $projectName;

                        if (str_contains($projectName, explode(' ', $issue['title'])[0])) { // Rough match
                            if (isset($issue['state_type'])) {
                                switch ($issue['state_type']) {
                                    case 'completed':
                                        $projectData[$label]['Completed']++;
                                        break;
                                    case 'started':
                                        $projectData[$label]['In Progress']++;
                                        break;
                                    case 'backlog':
                                        $projectData[$label]['Backlog']++;
                                        break;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        $labels = array_keys($projectData);
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Completed', 'data' => array_column($projectData, 'Completed'), 'backgroundColor' => 'rgba(34, 197, 94, 0.7)', 'borderRadius' => 4, 'stack' => 'Stack 0'],
                ['label' => 'In Progress', 'data' => array_column($projectData, 'In Progress'), 'backgroundColor' => 'rgba(59, 130, 246, 0.7)', 'borderRadius' => 4, 'stack' => 'Stack 0'],
                ['label' => 'Backlog', 'data' => array_column($projectData, 'Backlog'), 'backgroundColor' => 'rgba(245, 158, 11, 0.7)', 'borderRadius' => 4, 'stack' => 'Stack 0'],
            ],
        ];

        \Log::info("Project Status Result: " . json_encode($chartData));
        return response()->json($chartData);
    }
    public function getSlackActivity($oauthToken, $userEmail)
    {
        // Step 1: Get user ID by email
        $userResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $oauthToken,
        ])->get('https://slack.com/api/users.lookupByEmail', [
                    'email' => $userEmail,
                ]);

        if (!$userResponse->successful()) {
            return response()->json(['error' => 'Failed to fetch user data from Slack'], 400);
        }

        $userData = $userResponse->json();

        if (!$userData['ok']) {
            return response()->json(['error' => 'Slack API error: ' . $userData['error']], 400);
        }

        $userId = $userData['user']['id']; // Get the user ID for further API calls

        // Step 2: Get user activity data (e.g., message count in channels)
        $activityResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $oauthToken,
        ])->get('https://slack.com/api/conversations.list', [
                    'user' => $userId,  // You can use this to get the channels user is part of
                ]);

        if (!$activityResponse->successful()) {
            return response()->json(['error' => 'Failed to fetch activity data from Slack'], 400);
        }

        $activityData = $activityResponse->json();

        if (!$activityData['ok']) {
            return response()->json(['error' => 'Slack API error: ' . $activityData['error']], 400);
        }

        // Calculate activity percentage based on message count in active channels
        $activeChannels = count($activityData['channels']);
        $messageCount = 0;

        foreach ($activityData['channels'] as $channel) {
            // Assume 'message_count' is a field returned in the channel data
            // Adjust the logic as per your actual response structure
            $messageCount += $channel['message_count'];
        }

        // If there are no active channels, assume no activity
        $activityPercentage = $activeChannels > 0 ? ($messageCount / $activeChannels) * 100 : 0;

        return round($activityPercentage, 2);
    }







    public function get_template(Request $request)
    {
        $des = $request->des;
        $id = $request->id;
        $sql = $request->sql;
        $owner_id = Auth::user()->id;
        $chat_id = $request->chat_id;


        $key = hex2bin(env('SODIUM_KEY')); // 32-byte key from env
        $nonceUser = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $encryptedUser = sodium_crypto_secretbox(Auth::id(), $nonceUser, $key);

        // combine nonce + ciphertext then base64 encode for safe JS embedding
        $user_id_encrypted = base64_encode($nonceUser . $encryptedUser);


        $res = Http::post('https://temp.reviewbod.com/generate-template', [
            "description" => $des,
            "id" => $id,
            "sql" => $sql,
            "owner_id" => $user_id_encrypted,
            "chat_id" => $chat_id
        ])->json();


        return response()->json($res);

    }
    public function chat(Request $request)
    {
        try {
            $client = new Client("wss://api.reviewbod.com/ws", [
                'timeout' => 30,  // Longer timeout
                'persistent' => true
            ]);

            echo "Connected!\n";

            $message = json_encode([
                'type' => 'test',
                'user_id' => 1,
                'chat_id' => 1,
                'message' => 'Hello from PHP'
            ]);

            $client->send($message);
            echo "Message sent: " . $message . "\n";

            // Add a small delay
            usleep(100000); // 100ms

            // Try to receive with a loop
            for ($i = 0; $i < 10; $i++) {
                try {
                    $response = $client->receive();
                    echo "Received: " . $response . "\n";
                    break;
                } catch (\WebSocket\TimeoutException $e) {
                    echo "Timeout waiting for response, attempt " . ($i + 1) . "\n";
                    usleep(500000); // Wait 500ms between attempts
                }
            }

            $client->close();

        } catch (\WebSocket\ConnectionException $e) {
            echo "Connection error: " . $e->getMessage() . "\n";
            echo "Error code: " . $e->getCode() . "\n";
        } catch (Exception $e) {
            echo "General error: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Extract only essential data from the user data
     */
    private function extractEssentialData($data)
    {
        // Example implementation - modify according to your actual data structure
        $essential = [];

        // Only include key information needed for responses
        if (isset($data['issues'])) {
            $essential['issues'] = array_map(function ($issue) {
                return [
                    'id' => $issue['id'] ?? null,
                    'title' => $issue['title'] ?? null,
                    'status' => $issue['status'] ?? null,
                    'priority' => $issue['priority'] ?? null,
                ];
            }, array_slice($data['issues'], 0, 10)); // Limit to 10 most recent issues
        }

        if (isset($data['projects'])) {
            $essential['projects'] = array_map(function ($project) {
                return [
                    'id' => $project['id'] ?? null,
                    'name' => $project['name'] ?? null,
                    'status' => $project['status'] ?? null,
                ];
            }, array_slice($data['projects'], 0, 5)); // Limit to 5 projects
        }

        return $essential;
    }

    /**
     * Store user message in the database
     */
    private function storeUserMessage(Request $request, $message)
    {
        $data = [
            'user_id' => auth()->id(),
            'sender_type' => 'user',
            'message' => $message,
            'created_at' => now(),
        ];

        if ($request->user_id) {
            $data['userid'] = $request->user_id;
        }

        DB::table('chat_messages')->insert($data);
    }

    /**
     * Prepare messages for the OpenAI API
     */
    private function prepareMessages(Request $request, $userData, $history, $message)
    {
        $userDataSummary = json_encode($userData);
        $name = $request->name ?? Auth::user()->name ?? 'user';
        $service = Auth::user()->service;

        $systemPrompt = "You are reviewBOT, a personal assistant for $name. ";
        $systemPrompt .= "You have access to the user's $service data summary: $userDataSummary. ";
        $systemPrompt .= "When asked about $service data, refer to this data and be specific. ";
        $systemPrompt .= "Respond in a friendly, conversational tone. ";
        $systemPrompt .= "If showing usage stats or counts, represent the actual names or keys from the data — do NOT use labels like 'Array 1', 'Array 2'. ";
        $systemPrompt .= "Use plain text Unicode bar charts like: 'Daniel █████ (5 messages)'. ";
        $systemPrompt .= "Each bar should visually represent the value. Show a total at the end. ";
        $systemPrompt .= "Use Markdown-style formatting. Only show images if relevant.";


        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Add conversation history
        foreach ($history as $entry) {
            // Only include the last 5 exchanges to save tokens
            if (count($messages) > 10)
                break;

            $messages[] = [
                'role' => $entry['role'],
                'content' => $entry['content']
            ];
        }

        // Add user's message
        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];

        return $messages;
    }

    /**
     * Estimate token count for a message array
     */
    private function estimateTokenCount($messages)
    {
        // Rough estimate: 4 characters ~= 1 token
        $json = json_encode($messages);
        return strlen($json) / 4;
    }

    /**
     * Truncate messages to reduce token count
     */
    private function truncateMessages($messages)
    {
        // Keep system message and last 3 exchanges
        $systemMessage = $messages[0];
        $recentMessages = array_slice($messages, -6);
        return array_merge([$systemMessage], $recentMessages);
    }

    /**
     * Make a request to the OpenAI API
     */
    private function makeOpenAIRequest($messages)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer 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',
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => $messages,
                    'max_tokens' => 1000,
                    'temperature' => 0.7,
                ]);

        if ($response->successful()) {
            $responseData = $response->json();
            return $responseData['choices'][0]['message']['content'] ?? 'I\'m not sure how to respond to that.';
        } else {
            Log::error('OpenAI API error: ' . $response->body());
            throw new \Exception('API request failed: ' . $response->status());
        }
    }

    /**
     * Store bot reply in the database
     */
    private function storeBotReply(Request $request, $botReply)
    {
        $data = [
            'user_id' => auth()->id(),
            'sender_type' => 'bot',
            'message' => $botReply,
            'created_at' => now(),
        ];

        if ($request->user_id) {
            $data['userid'] = $request->user_id;
        }

        DB::table('chat_messages')->insert($data);
    }
    /**
     * Determine possible actions based on the message and response.
     *
     * @param  string  $message
     * @param  string  $response
     * @return array
     */
    private function determineActions($message, $response)
    {
        $actions = [];

        // Simple keyword-based action suggestions
        // if (str_contains(strtolower($message), 'project') || 
        //     str_contains(strtolower($response), 'project')) {
        //     $actions[] = [
        //         'text' => 'Show me other options',
        //         'action' => 'sendQuickQuestion(\'Show me project options\')'
        //     ];
        // }

        // if (str_contains(strtolower($message), 'issue') || 
        //     str_contains(strtolower($response), 'issue')) {
        //     $actions[] = [
        //         'text' => 'View all issues',
        //         'action' => 'sendQuickQuestion(\'List all issues\')'
        //     ];
        // }

        return $actions;
    }



    public function getSlackData()
    {
        // Get user's Slack connection from DB
        $slack = DB::table("linked")->where(["type" => "slack", "userid" => Auth::id()])->first();

        if (!$slack || empty($slack->token)) {
            return ['error' => 'No Slack connection found'];
        }

        $token = $slack->token;
        $userId = Auth::id();
        $channelsCacheKey = 'slack_channels_' . $userId;

        // Get all channels
        $channels = Cache::remember($channelsCacheKey, 3600, function () use ($token) {
            $channels = [];
            $cursor = null;
            $attempts = 0;
            $maxAttempts = 5;

            do {
                try {
                    $params = [
                        'types' => 'public_channel,private_channel',
                        'limit' => 1000,
                    ];

                    if ($cursor) {
                        $params['cursor'] = $cursor;
                    }

                    $response = Http::withToken($token)
                        ->timeout(10)
                        ->retry(3, 100)
                        ->get('https://slack.com/api/conversations.list', $params);

                    $data = $response->json();

                    if (!$data['ok']) {
                        if (isset($data['error']) && $data['error'] === 'ratelimited') {
                            $attempts++;
                            if ($attempts >= $maxAttempts) {
                                break;
                            }
                            $retryAfter = $response->header('Retry-After') ?? (1 << $attempts);
                            sleep($retryAfter);
                            continue;
                        }
                        break;
                    }

                    $channels = array_merge($channels, $data['channels'] ?? []);
                    $cursor = $data['response_metadata']['next_cursor'] ?? null;

                } catch (\Exception $e) {
                    \Log::error('Slack channels fetch error: ' . $e->getMessage());
                    break;
                }

            } while ($cursor);

            return $channels;
        });

        if (empty($channels)) {
            return ['error' => 'No channels found or access denied'];
        }

        // Format channels for output
        $result = array_map(function ($channel) {
            return [
                'id' => $channel['id'],
                'name' => $channel['name'],
                'topic' => $channel['topic']['value'] ?? '',
                'member_count' => $channel['num_members'] ?? 0,
            ];
        }, $channels);

        return $result;
    }

    public function chatuser()
    {
        return view("dash.chat");
    }


    public function chats()
    {

        $messages = DB::table('chat_messages')
            ->where('user_id', auth()->id())
            ->where("userid", null)
            ->orderBy('created_at', 'asc')
            // ->limit(200)
            ->get();

        if (request()->user_id) {
            $messages = DB::table('chat_messages')
                ->where('user_id', auth()->id())
                ->where("userid", request()->user_id)
                ->orderBy('created_at', 'asc')
                // ->limit(200)
                ->get();
        }

        return response()->json(["data" => $messages]);
    }


    public function last_chat()
    {

        $message = DB::table('chat_messages')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json(["data" => $message]);
    }

    public function members(Request $request)
    {
        $res = new FetchData();
        $res = $res->getData("users");

        // Handle different response types
        if ($res instanceof \Illuminate\Http\JsonResponse) {
            // Extract data from JsonResponse
            $responseData = $res->getData("users");

            // Check if it's an error response
            if (isset($responseData['message']) && ($res->getStatusCode() != 200)) {
                // Handle error
                $error = $responseData['message'];
                Log::info("Error Data: $error");
                return view("dash.error", ['error' => $responseData['message']]);
            }

            $res = $responseData;
        }


        $service = Auth::user()->service;
        // dd($res);
        if ($service == "linear") {
            $allMembers = $res;

        } elseif ($service = "trello") {
            // dd($res);
            $allMembers = $res;


        }





        // Get linked user data
        $linkedUsers = DB::table('linked_users')->where(["userid" => Auth::id()])->select('email', 'blocked', 'verified')->get();
        $linkedUsersMap = [];

        foreach ($linkedUsers as $user) {
            $linkedUsersMap[$user->email] = [
                'blocked' => $user->blocked,
                'verified' => $user->verified
            ];
        }

        // Apply search filter if search query exists
        $search = $request->input('search');
        $status = $request->input('status');

        if ($search) {
            $allMembers = array_filter($allMembers, function ($member) use ($search) {
                $searchInName = stripos($member['name'] ?? '', $search) !== false;
                $searchInEmail = stripos($member['email'] ?? '', $search) !== false;

                if (Auth::user()->service == "trello") {
                    $searchInDisplayName = stripos($member['displayName'] ?? '', $search) !== false;
                    return $searchInEmail || $searchInDisplayName || $searchInName;
                }

                return $searchInEmail || $searchInName;
            });
        }

        // dd($allMembers);

        // Filter by status
        if ($status) {
            $allMembers = array_filter($allMembers, function ($member) use ($status, $linkedUsersMap) {
                $email = $member['email'] ?? '';

                // Check if we have this user in our linked_users table
                if (isset($linkedUsersMap[$email])) {
                    if ($status === 'blocked' && $linkedUsersMap[$email]['blocked'] == 1) {
                        return true;
                    }

                    if ($status === 'pending' && $linkedUsersMap[$email]['verified'] === null) {
                        return true;
                    }
                }



                // If status is not blocked or pending, use the original status filter
                return $status !== 'blocked' && $status !== 'pending' &&
                    stripos($member['status'] ?? '', $status) !== false;
            });



        }

        // Setup pagination
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $totalMembers = count($allMembers);

        // Create a LengthAwarePaginator instance
        $members = array_slice($allMembers, $offset, $perPage);

        $paginator = new LengthAwarePaginator(
            $members,
            $totalMembers,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Add status info to each member for display
        foreach ($members as &$member) {
            $email = $member['email'] ?? '';
            if (isset($linkedUsersMap[$email])) {
                $member['is_blocked'] = $linkedUsersMap[$email]['blocked'] == 1;
                $member['is_pending'] = $linkedUsersMap[$email]['verified'] === null;
            } else {
                $member['is_blocked'] = false;
                $member['is_pending'] = false;
            }
        }

        return view('dash.members', [
            'members' => $paginator,
            'totalMembers' => $totalMembers,
            'search' => $search,
            'status' => $status,
            'res' => $res,
        ]);

    }


    public function users(Request $request)
    {
        $data = DB::table("platform_users")
            ->leftJoin('linked_users', DB::raw("CONVERT(linked_users.email USING utf8mb4) COLLATE utf8mb4_unicode_ci"), '=', 'platform_users.email')
            ->select('platform_users.*', 'linked_users.status as user_status', 'linked_users.id as iid')
            ->where(["platform_users.owner_id" => Auth::id()]);

        if ($request->type) {
            $data->where(["platform_users.source" => $request->type]);
        }
        $data = $data->distinct()->get();
        return view("dash.members", compact("data"));
    }
    public function teams(Request $request)
    {
        $res = new FetchData();
        $res = $res->getData("teams");
        // dd($res);
        // Handle different response types
        if ($res instanceof \Illuminate\Http\JsonResponse) {
            // Extract data from JsonResponse
            $responseData = $res->getData("teams");

            // Check if it's an error response
            if (isset($responseData['message']) && ($res->getStatusCode() != 200)) {
                // Handle error
                $error = $responseData['message'];
                Log::info("Error Data: $error");
                return view("dash.error", ['error' => $responseData['message']]);
            }

            $res = $responseData;
        }

        // Access the nodes array from the response
        $allTeams = $res;
        // dd($allTeams);
        // Apply search filter if search query exists
        $search = $request->input('search');
        if ($search) {
            $allTeams = array_filter($allTeams, function ($team) use ($search) {
                return stripos($team['name'] ?? '', $search) !== false;
            });
        }

        // Setup pagination
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $totalTeams = count($allTeams);

        // Create a LengthAwarePaginator instance
        $teams = array_slice($allTeams, $offset, $perPage);
        $paginator = new LengthAwarePaginator(
            $teams,
            $totalTeams,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('dash.teams', [
            'members' => $paginator,
            'totalTeams' => $totalTeams,
            'search' => $search,
            'res' => $res,
        ]);
    }


    public function getUserLinear($accessToken)
    {
        $response = Http::withToken($accessToken)
            ->post('https://api.linear.app/graphql', [
                'query' => '
          

            query GetTeams {
                teams {
                    nodes {
                        id
                        name
                    }
                }
            }
        '
            ]);

        if ($response->failed()) {
            return [
                'success' => false,
                'message' => 'Linear API request failed.',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        $data = $response->json();
        dd($data);

    }
    public function getFullLinearUserData($accessToken, $userId)
    {
        $query = <<<GQL
 
    query GetUserDetails {
        user(id: "$userId") {
            id
            name
            displayName
            email
            avatarUrl 
            createdAt
            updatedAt
            active
            admin
            archivedAt
            statusEmoji
            description
            url
            timezone
            lastSeen
            isMe

            # Teams
            teamMemberships {
                nodes {
                    id 
                    createdAt
                    updatedAt
                    team {
                        id
                        name
                        key
                        color
                        createdAt
                    }
                }
            }

            # Assigned Issues
           assignedIssues {
            nodes {
                id
                title
                identifier
                state {
                    id
                    name
                    type
                }
                priority
                url
                dueDate
                createdAt
                updatedAt
                project {
                    id
                    name
                }
            }
        }

            # Created Issues
            createdIssues {
                nodes {
                    id
                    title
                    identifier
                    state {
                        name
                    }
                    createdAt
                    priority
                }
            }

         
         
        }

     
    }
    GQL;

        $response = Http::withToken($accessToken)
            ->post('https://api.linear.app/graphql', [
                'query' => $query,
            ]);

        if ($response->failed()) {
            return [
                'success' => false,
                'message' => 'Linear API request failed.',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        $data = $response->json();

        if (isset($data['errors'])) {
            return [
                'success' => false,
                'message' => 'Linear returned errors.',
                'errors' => $data['errors']
            ];
        }

        return [
            'success' => true,
            'user' => $data['data']['user'] ?? null
        ];
    }

    public function getFullTrelloUserData($apiKey, $token, $username)
    {
        // Trello uses REST API instead of GraphQL, so we'll make multiple requests to get similar data
        $baseUrl = 'https://api.trello.com/1';

        // Get user details - note the authentication parameters
        $userResponse = Http::get("{$baseUrl}/members/{$username}", [
            'key' => $apiKey,
            'token' => $token,
            'fields' => 'id,fullName,username,email,avatarUrl,idBoards,url,bio,initials,memberType,confirmed,status,dateCreated'
        ]);
        // dd($token);


        if ($userResponse->failed()) {
            return [
                'success' => false,
                'message' => 'Trello API user request failed.',
                'status' => $userResponse->status(),
                'body' => $userResponse->body(),
            ];
        }

        $userData = $userResponse->json();

        // Get boards (teams equivalent)
        $boardsResponse = Http::get("{$baseUrl}/members/{$username}/boards", [
            'key' => $apiKey,
            'token' => $token,
            'fields' => 'id,name,shortLink,dateLastActivity,url,prefs'
        ]);

        if ($boardsResponse->failed()) {
            return [
                'success' => false,
                'message' => 'Trello API boards request failed.',
                'status' => $boardsResponse->status(),
                'body' => $boardsResponse->body(),
            ];
        }

        $boards = $boardsResponse->json();

        // Get cards assigned to the user (issues equivalent)
        $cardsResponse = Http::get("{$baseUrl}/members/{$username}/cards", [
            'key' => $apiKey,
            'token' => $token,
            'fields' => 'id,name,idBoard,idList,url,due,dateLastActivity,desc,shortUrl',
            'members' => true,
            'member_fields' => 'id,fullName,username',
            'board' => true,
            'board_fields' => 'id,name',
            'list' => true,
            'list_fields' => 'id,name'
        ]);

        if ($cardsResponse->failed()) {
            return [
                'success' => false,
                'message' => 'Trello API cards request failed.',
                'status' => $cardsResponse->status(),
                'body' => $cardsResponse->body(),
            ];
        }

        $cards = $cardsResponse->json();

        // Get cards created by the user
        $createdCardsResponse = Http::get("{$baseUrl}/members/{$username}/actions", [
            'key' => $apiKey,
            'token' => $token,
            'filter' => 'createCard',
            'fields' => 'id,date,data',
            'limit' => 100
        ]);

        if ($createdCardsResponse->failed()) {
            return [
                'success' => false,
                'message' => 'Trello API created cards request failed.',
                'status' => $createdCardsResponse->status(),
                'body' => $createdCardsResponse->body(),
            ];
        }

        $createdCards = $createdCardsResponse->json();

        // Format response to match the Linear API response structure
        $formattedData = [
            'id' => $userData['id'],
            'name' => $userData['fullName'],
            'displayName' => $userData['username'],
            'email' => $userData['email'] ?? null,
            'avatarUrl' => $userData['avatarUrl'] ?? null,
            'createdAt' => $userData['dateCreated'] ?? null,
            'updatedAt' => $userData['dateLastActivity'] ?? null,
            'active' => $userData['confirmed'] ?? false,
            'admin' => $userData['memberType'] === 'admin',
            'archivedAt' => null,
            'statusEmoji' => null,
            'description' => $userData['bio'] ?? null,
            'url' => $userData['url'] ?? null,
            'timezone' => null,
            'lastSeen' => null,
            'isMe' => true,

            // Team memberships (boards in Trello)
            'teamMemberships' => [
                'nodes' => array_map(function ($board) {
                    return [
                        'id' => $board['id'],
                        'createdAt' => null, // Trello doesn't provide creation date for boards in this endpoint
                        'updatedAt' => $board['dateLastActivity'] ?? null,
                        'team' => [
                            'id' => $board['id'],
                            'name' => $board['name'],
                            'key' => $board['shortLink'] ?? null,
                            'color' => $board['prefs']['backgroundColor'] ?? null,
                            'createdAt' => null
                        ]
                    ];
                }, $boards)
            ],

            // Assigned issues (cards in Trello)
            'assignedIssues' => [
                'nodes' => array_map(function ($card) {
                    return [
                        'id' => $card['id'],
                        'title' => $card['name'],
                        'identifier' => $card['shortUrl'] ?? null,
                        'state' => [
                            'id' => $card['idList'],
                            'name' => $card['list']['name'] ?? 'Unknown',
                            'type' => null
                        ],
                        'priority' => null,
                        'url' => $card['url'] ?? null,
                        'dueDate' => $card['due'] ?? null,
                        'createdAt' => null, // Trello doesn't provide creation date in this endpoint
                        'updatedAt' => $card['dateLastActivity'] ?? null,
                        'project' => [
                            'id' => $card['idBoard'],
                            'name' => $card['board']['name'] ?? 'Unknown'
                        ]
                    ];
                }, $cards)
            ],

            // Created issues (cards created by user in Trello)
            'createdIssues' => [
                'nodes' => array_map(function ($action) {
                    return [
                        'id' => $action['data']['card']['id'] ?? null,
                        'title' => $action['data']['card']['name'] ?? null,
                        'identifier' => null,
                        'state' => [
                            'name' => null
                        ],
                        'createdAt' => $action['date'] ?? null,
                        'priority' => null
                    ];
                }, $createdCards)
            ]
        ];

        return [
            'success' => true,
            'user' => $formattedData
        ];
    }
    public function member($id, Request $request)
    {
        $data = DB::table("platform_users")->where(["id" => $id])->first();
        if (!$data) {
            return redirect()->back();
        }

        // Get tasks with more detailed information
        $tasks = DB::table("tasks")
            ->where(["user_id" => $data->user_id])
            ->select('id', 'title', 'description', 'status', 'priority', 'due_date', 'created_at', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->where('is_deleted', null)
            ->get();

        // Convert to array for JavaScript
        $tasksArray = $tasks->toArray();

        $teams = DB::table("teams")->join("tasks","tasks.team_id","teams.team_key")->where(["tasks.user_id"=>$data->user_id])->select("teams.name","teams.id")->distinct()->get();
       $manager = DB::table("managers")->where(["id"=>$data->manager_id])->first();
     
       return view("dash.user", [
            'data' => $data,
            'teams'=>$teams,
            "manager"=>$manager,
            "tasks" => $tasks,
            "tasksJson" => json_encode($tasksArray) // Pass JSON for JavaScript
        ]);
    }
    public function settings()
    {
        // $users = DB::table("linked_users")->where(["userid" => auth()->id()])->get();
        $slack = DB::table("linked")->where(["userid" => auth()->id(), "type" => "slack"])->first();
        $notifications = DB::table("notification_channel")->where(["userid" => auth()->id()])->get();
        $metrics_category = DB::table("metrics_category")->get();
        return view("dash.settings", compact("slack", "notifications", "metrics_category"));
    }

    public function new_user(Request $request)
    {
        return view("dash.add");
    }

    public function delete_linked(Request $request)
    {
        $id = $request->input("id");

        // Verify the link exists and belongs to the user
        $linked = DB::table("linked")
            ->where(["userid" => auth()->id(), "id" => $id])
            ->first();

        if (!$linked) {
            return response()->json(["message" => "Link not found"], 404);
        }

        // Delete the link
        DB::table("linked")->where(["userid" => auth()->id(), "id" => $id])->delete();

        // Get another linked entry (if any)
        $remaining = DB::table("linked")
            ->where("userid", auth()->id())
            ->first();

        // Update user's service based on remaining linked entry
        // $newService = ($remaining && $remaining->type == auth()->user()->service && $remaining->type != "slack")
        //     ? $remaining->type
        //     : null;

        if ($remaining && $remaining->type != "slack") {
            DB::table("users")->where("id", auth()->id())->update(["service" => $remaining->type]);

        }
        Cache::flush();
        return response()->json(["message" => "Deleted"]);
    }


    public function update_user(Request $request)
    {
        $userId = auth()->id();
        $name = $request->input("name");
        $email = $request->input("email");
        $phone = $request->input("phone");
        $company_name = $request->input("company_name");

        // Check if email exists for another user
        $emailExists = DB::table("users")
            ->where("email", $email)
            ->where("id", "!=", $userId)
            ->exists();

        if ($emailExists) {
            return response()->json(["message" => "Email already in use"], 400);
        }

        // Check if phone exists for another user
        $phoneExists = DB::table("users")
            ->where("phone", $phone)
            ->where("id", "!=", $userId)
            ->exists();

        if ($phoneExists) {
            return response()->json(["message" => "Phone number already in use"], 400);
        }

        DB::table("users")->where("id", $userId)->update([
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "company_name" => $company_name
        ]);

        return response()->json(["message" => "Updated"]);
    }

    public function update_password()
    {
        $password = request()->input("password");
        $confirm_password = request()->input("password_confirmation");
        if ($password != $confirm_password) {
            return response()->json(["message" => "Password not matched"], 422);
        }
        DB::table("users")->where(["id" => auth()->id()])->update([
            "password" => Hash::make($password)
        ]);
        return response()->json(["message" => "Updated"]);

    }

    public function delete_invitation(Request $request)
    {
        $id = $request->input("id");
        DB::table("linked_users")->where(["userid" => auth()->id(), "id" => $id])->delete();
        return response()->json(["message" => "Deleted"]);
    }

    public function save_notification(Request $request)
    {
        // Handle email notifications
        if ($request->email_notifications != "0") {
            // Check if the record already exists to avoid duplicates
            $exists = DB::table("notification_channel")
                ->where("channel", "email")
                ->where("userid", Auth::id())
                ->exists();

            if (!$exists) {
                DB::table("notification_channel")->insert([
                    "channel" => "email",
                    "userid" => Auth::id(),
                    "created_at" => now(),
                ]);
            }
        } else {
            // Remove email notification preference
            DB::table("notification_channel")
                ->where("channel", "email")
                ->where("userid", Auth::id())
                ->delete();
        }

        // Handle slack notifications
        if ($request->slack_notifications != "0") {
            // Check if the record already exists to avoid duplicates
            $exists = DB::table("notification_channel")
                ->where("channel", "slack")
                ->where("userid", Auth::id())
                ->exists();

            if (!$exists) {
                DB::table("notification_channel")->insert([
                    "channel" => "slack",
                    "userid" => Auth::id(),
                    "created_at" => now(),
                ]);
            }
        } else {
            // Remove slack notification preference
            DB::table("notification_channel")
                ->where("channel", "slack")
                ->where("userid", Auth::id())
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully'
        ]);
    }

    public function chat_data(Request $request)
    {
        $userId = Auth::id();
        $perPage = 10;

        // Get user's chats (most recently updated first)
        $chats = DB::table('chats')
            ->where('user_id', $userId)
            ->where(["uuid" => $request->chat_id])
            ->orderBy('updated_at', 'desc')
            ->first();

        // Get messages for selected chat or first chat
        $chatId = $chats->uuid;

        if ($chatId) {
            // Get messages in reverse order (newest first) for pagination
            $messages = DB::table('chat_messages')
                ->where('chat_id', $chatId)
                ->where('user_id', Auth::id())
                ->orderBy('id', 'desc')
                ->simplePaginate($perPage);

            foreach ($messages as $m) {
                $temp = DB::table("templates")->where(["chat_id" => $m->chat_id])->get();
                $m->template = $temp;
            }

            $messagesData = $messages->items();

            // Get pagination info
            $currentPage = $messages->currentPage();
            // Remove total() - not available with simplePaginate
            $hasMore = $messages->hasMorePages();
        } else {
            $messagesData = [];
            $currentPage = 1;
            $hasMore = false;
        }

        return response()->json([
            'messages' => [
                'data' => $messagesData,
                'current_page' => $currentPage,
                // 'total' => $total, // Remove this line
                'hasMore' => $hasMore
            ],
            'hasMore' => $hasMore
        ]);
    }

    public function getSidebarChats(Request $request)
    {
        try {
            // Get user's chats without messages
            $chats = DB::table('chats')
                ->where('user_id', Auth::id())
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'chats' => $chats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load chats'
            ], 500);
        }
    }

    public function getSidebarChatsLast(Request $request)
    {
        try {
            // Get user's chats without messages
            $chats = DB::table('chats')
                ->where('user_id', Auth::id())
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'chats' => $chats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load chats'
            ], 500);
        }
    }

    public function createChat(Request $request)
    {
        $context = $request->input('context', ''); // Get context from request

        // Generate title and description using OpenAI if context provided
        if (!empty($context)) {
            try {
                $client = new \OpenAI\Client(env('OPENAI_API_KEY'));

                $response = $client->chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are ReviewBot. Your task is to read the following input text and generate a JSON response with two keys: "title" (max 50 characters) and "description" (max 100 characters). ONLY use the actual content from the input. Do NOT include timestamps, roles, summaries, or anything that is not explicitly in the text. Do NOT invent or assume any context.'
                        ],

                        [
                            'role' => 'user',
                            'content' => $context
                        ]
                    ],
                    'max_tokens' => 100,
                    'temperature' => 0.7,
                ]);

                $result = json_decode($response->choices[0]->message->content, true);
                $title = $result['title'] ?? 'New Chat';
                $description = $result['description'] ?? '';

            } catch (\Exception $e) {
                $title = 'New Chat';
                $description = '';
            }
        } else {
            $title = 'New Chat';
            $description = '';
        }

        $chatId = DB::table('chats')->insertGetId([
            'user_id' => Auth::id(),
            'title' => $title,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $chat = DB::table('chats')->find($chatId);

        return response()->json([
            'success' => true,
            'chat' => $chat
        ]);
    }

    public function createChat2($title)
    {
        $context = $title; // Get context from request

        // Generate title and description using OpenAI if context provided
        if (!empty($context)) {
            try {
                $apiKey = env('OPENAI_API_KEY');

                $data = [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Youre a conversation context provider, Based on the following context, generate a concise title (max 50 characters) and brief description (max 100 characters). Return as JSON with "title" and "description" keys.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $context
                        ]
                    ],
                    'max_tokens' => 100,
                    'temperature' => 0.7,
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 && $response) {
                    $responseData = json_decode($response, true);
                    $result = json_decode($responseData['choices'][0]['message']['content'], true);
                    $title = $result['title'] ?? 'New Chat';
                    $description = $result['description'] ?? '';
                } else {
                    $title = 'New Chat';
                    $description = '';
                }

            } catch (\Exception $e) {
                $title = 'New Chat';
                $description = '';
            }
        } else {
            $title = 'New Chat';
            $description = '';
        }

        $chatId = DB::table('chats')->insertGetId([
            'user_id' => Auth::id(),
            'title' => $title,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $chat = DB::table('chats')->find($chatId);

        return $chatId;
    }

    // Add this method to re-update after 5 messages
    public function checkAndUpdateChatTitle($chatId)
    {
        $messageCount = DB::table('chat_messages')->where('chat_id', $chatId)->count();

        if ($messageCount == 5) {
            // Get first 5 messages for context
            $messages = DB::table('chat_messages')
                ->where('chat_id', $chatId)
                ->orderBy('created_at', 'asc')
                ->limit(5)
                ->get();

            $context = '';
            foreach ($messages as $message) {
                $role = $message->is_user ? 'User' : 'Assistant';
                $context .= "{$role}: {$message->content}\n";
            }

            try {
                $client = new \OpenAI\Client(env('OPENAI_API_KEY'));

                $response = $client->chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Based on this conversation, generate a better title (max 50 characters) and description (max 100 characters). Return as JSON with "title" and "description" keys.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $context
                        ]
                    ],
                    'max_tokens' => 100,
                    'temperature' => 0.7,
                ]);

                $result = json_decode($response->choices[0]->message->content, true);

                DB::table('chats')->where('uuid', $chatId)->update([
                    'title' => $result['title'] ?? 'New Chat',
                    'description' => $result['description'] ?? '',
                    'updated_at' => now()
                ]);

            } catch (\Exception $e) {
                // Silent fail, keep existing title
            }
        }
    }
    public function loadMoreMessages(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,uuid',
            'page' => 'integer|min:1'
        ]);

        $perPage = 10;
        $messages = DB::table('chat_messages')
            ->where('chat_id', $request->chat_id)
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $request->page);

        foreach ($messages as $m) {
            $temp = DB::table("templates")->where(["unique_id_with_template" => $m->unique_id_with_template])->get();
            $m->template = $temp;
        }
        return response()->json([
            'messages' => [
                'data' => $messages->items(),
                'current_page' => $messages->currentPage(),
                'total' => $messages->total(), // Add total message count
                'hasMore' => $messages->hasMorePages()
            ],
            'hasMore' => $messages->hasMorePages()
        ]);
    }

    public function deleteChat(Request $request)
    {
        try {
            $request->validate([
                'chat_id' => 'required|string|exists:chats,uuid'
            ]);

            $userId = Auth::id();
            $chatId = $request->input('chat_id');

            // Verify the chat belongs to the authenticated user
            $chat = DB::table('chats')
                ->where('uuid', $chatId)
                ->where('user_id', $userId)
                ->first();

            if (!$chat) {
                return response()->json([
                    'success' => false,
                    'error' => 'Chat not found or you do not have permission to delete this chat.'
                ], 404);
            }

            // Start a database transaction
            DB::beginTransaction();

            try {
                // Delete all messages associated with this chat
                DB::table('chat_messages')
                    ->where('chat_id', $chatId)
                    ->delete();

                // Delete the chat itself
                DB::table('chats')
                    ->where('uuid', $chatId)
                    ->where('user_id', $userId)
                    ->delete();

                // Commit the transaction
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Chat deleted successfully.'
                ]);

            } catch (\Exception $e) {
                // Rollback the transaction on error
                DB::rollback();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid input data.',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error deleting chat: ' . $e->getMessage(), [
                'chat_id' => $request->input('chat_id'),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while deleting the chat. Please try again.' . $e->getMessage()
            ], 500);
        }
    }

    public function reaction(Request $request)
    {

        $userId = Auth::id();
        $chatId = $request->input('id');
        $reaction = $request->input('type');


        // Insert or update the reaction
        DB::table('chat_messages')->where(["id" => $chatId])->update(
            ['reaction' => $reaction]
        );

        return response()->json([
            'success' => true,
            'message' => 'Reaction added successfully.'
        ]);
    }

    public function save_config_metrics(Request $request)
    {

        if ($request->update) {

            $json = json_decode($request->input("metrics"), true);
            foreach ($json as $j) {
                if (str_contains($j['id'], 'custom')) {
                    $number = filter_var($j['id'], FILTER_SANITIZE_NUMBER_INT);
                    $number = str_replace("-", "", $number);
                    DB::table("user_metrics")->where([
                        "userid" => Auth::id(),
                        "id" => (int) $number,
                    ])->update([
                                "weight" => $j['weight'],
                                "percentage" => $j['percentage']
                            ]);
                    $yes = "yes";
                } else {
                    $number = $j['id'];
                    if (
                        DB::table("user_metrics")->where([
                            "userid" => Auth::id(),
                            "metric_id" => $number,
                        ])->exists()
                    ) {
                        DB::table("user_metrics")->where([
                            "userid" => Auth::id(),
                            "metric_id" => $number,
                        ])->update([
                                    "weight" => $j['weight'],
                                    "percentage" => $j['percentage']
                                ]);
                    } else {
                        $data = DB::table("metrics_type")->where("id", $j['id'])->first();

                        DB::table("user_metrics")->insert([
                            "title" => $data->title,
                            "description" => $data->description,
                            "category" => $data->category,
                            "metric_id" => $j['id'],
                            "weight" => $j['weight'],
                            "percentage" => $j['percentage'],
                            'userid' => Auth::id(),
                        ]);
                    }
                    $yes = $number;

                }

                // var_dump($yes);

            }


            return response()->json([
                'success' => true,
                'message' => 'Metrics configuration saved successfully.'
            ]);
        }
        $data = DB::table("metrics_type")->where("id", $request->input("metric_id"))->first();
        $checkuser = DB::table("user_metrics")->where([
            "userid" => Auth::id(),
            "metric_id" => $request->metric_id
        ])->first();

        if ($checkuser) {
            DB::table("user_metrics")->where([
                "userid" => Auth::id(),
                "metric_id" => $request->metric_id
            ])->delete();
            return response()->json([
                'success' => true,
                'message' => 'Metrics configuration saved successfully.'
            ]);
        }

        if ($request->selected) {
            DB::table("user_metrics")->insert([
                "title" => $data->title,
                "description" => $data->description,
                "category" => $data->category,
                "metric_id" => $request->metric_id,
                'userid' => Auth::id(),
            ]);
        } else {
            DB::table("user_metrics")->where([
                "userid" => Auth::id(),
                "metric_id" => $request->metric_id
            ])->delete();
        }
        return response()->json([
            'success' => true,
            'message' => 'Metrics configuration saved successfully.'
        ]);
    }

    public function delete_account(Request $request)
    {
        DB::table("users")->where(["id" => Auth::id()])->delete();
        return response()->json(["message" => "Account Deleted!"]);
    }

    public function loadusers()
    {
        $data = DB::table("platform_users")
            ->join("linked", "linked.id", "platform_users.platform_id")
            ->leftJoin('linked_users', DB::raw("CONVERT(linked_users.email USING utf8mb4) COLLATE utf8mb4_unicode_ci"), '=', 'platform_users.email')
            ->select("platform_users.*", "linked.type", 'linked_users.status as linked_status')
            ->where(["platform_users.owner_id" => Auth::id()])->get();
        return response()->json(["users" => $data]);
    }

    public function getTaskPerformance(Request $request)
    {



        $days = $request->input('days', 7);
        $userId = $request->input('user_id');

        try {
            $data = $this->getOptimizedTaskData($userId, $days);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch task performance data'
            ], 500);
        }
    }

    private function getOptimizedTaskData($userId, $days)
    {
        if ($days == null) {
            // Get first created_at date for user
            $firstTaskDate = DB::table('tasks')
                ->where('user_id', $userId)
                ->whereNull('is_deleted')
                ->orderBy('created_at', 'asc')
                ->value('created_at');

            if (!$firstTaskDate) {
                // No tasks found
                return [
                    'labels' => [],
                    'assigned' => [],
                    'completed' => []
                ];
            }

            $startDate = Carbon::parse($firstTaskDate)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $days = $startDate->diffInDays($endDate) + 1;
        } else {
            $startDate = Carbon::now()->subDays($days - 1)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        }

        $taskStats = DB::table('tasks')
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_assigned'),
                DB::raw('SUM(CASE WHEN status IN ("Done") THEN 1 ELSE 0 END) as total_completed')
            ])
            ->where('user_id', $userId)
            ->whereNull('is_deleted')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // Generate date range and fill missing dates with zeros
        $dateRange = collect();
        for ($i = 0; $i < $days; $i++) {
            $dateRange->push(Carbon::parse($startDate)->addDays($i)->format('Y-m-d'));
        }

        $labels = [];
        $assignedData = [];
        $completedData = [];

        foreach ($dateRange as $date) {
            $dayStats = $taskStats->firstWhere('date', $date);

            $labels[] = Carbon::parse($date)->format('M j');
            $assignedData[] = $dayStats ? (int) $dayStats->total_assigned : 0;
            $completedData[] = $dayStats ? (int) $dayStats->total_completed : 0;
        }

        return [
            'labels' => $labels,
            'assigned' => $assignedData,
            'completed' => $completedData
        ];
    }

    public function loadusertasks(Request $request)
    {
        $userId = $request->input('user_id');
        $page = $request->input('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $tasks = DB::table('tasks')
            ->where('user_id', $userId)
            ->where('is_deleted', null)
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($perPage + 1) // Get one extra to check if there are more
            ->get(['id', 'title', 'description', 'status', 'due_date', 'created_at', 'user_id']);

        $hasMore = $tasks->count() > $perPage;
        if ($hasMore) {
            $tasks = $tasks->take($perPage);
        }

        return response()->json([
            'success' => true,
            'tasks' => $tasks,
            'has_more' => $hasMore,
            'current_page' => $page
        ]);
    }

    public function saveCustomMetric(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000'
            ]);

            // Check if custom metric with same name already exists for this user
            $existingMetric = DB::table('user_metrics')
                ->where('userid', auth()->user()->id)
                ->whereNull('metric_id')
                ->where('custom_name', $request->name)
                ->first();

            if ($existingMetric) {
                return response()->json([
                    'success' => false,
                    'message' => 'A custom metric with this name already exists.'
                ], 422);
            }

            // Insert custom metric
            DB::table('user_metrics')->insert([
                'userid' => auth()->user()->id,
                'metric_id' => null, // null indicates custom metric
                'custom_name' => $request->name,
                'custom_description' => $request->description,
                'weight' => 1,
                'percentage' => 0, // Will be calculated later
                'created_at' => now(),

            ]);

            return response()->json([
                'success' => true,
                'message' => 'Custom metric added successfully!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding custom metric: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a custom metric
     */
    public function deleteCustomMetric($id)
    {
        try {
            // Verify the custom metric belongs to the current user and is actually custom (metric_id is null)
            $customMetric = DB::table('user_metrics')
                ->where('id', $id)
                ->where('userid', auth()->user()->id)
                ->whereNull('metric_id')
                ->first();

            if (!$customMetric) {
                return response()->json([
                    'success' => false,
                    'message' => 'Custom metric not found or you do not have permission to delete it.'
                ], 404);
            }

            // Delete the custom metric
            DB::table('user_metrics')
                ->where('id', $id)
                ->where('userid', auth()->user()->id)
                ->whereNull('metric_id')
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Custom metric deleted successfully!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting custom metric: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUserAnalysis(Request $request)
    {
        $userId = $request->input('user_id');
        $query = $request->input('query', 'Give me an analysis of my data');

        if (!$userId) {
            return response()->json(['error' => 'User ID is required'], 400);
        }

        $cacheKey = 'user_analysis_' . $userId . '_' . md5($query);

        // Try to get from cache first (5 minutes)
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response()->json($cached);
        }

        try {
            // Pre-fetch all user data in parallel
            $userData = $this->getUserData($userId);

            // Single AI call with all context
            $analysisResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4-turbo-preview',
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => $this->buildAnalysisPrompt($userData, $query)
                            ]
                        ],
                        'temperature' => 0.4,
                        'max_tokens' => 1500
                    ]);

            $textAnalysis = $analysisResponse->json()['choices'][0]['message']['content'];

            $result = [
                'success' => true,
                'analysis' => $textAnalysis,
                'workspace_types' => $userData['workspace_types'],
                'has_data' => $userData['has_data']
            ];

            // Cache for 5 minutes
            Cache::put($cacheKey, $result, now()->addMinutes(5));

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getUserData($userId)
    {
        // Get all user data in efficient queries
        $workspaceTypes = DB::table('linked')
            ->where('userid', $userId)
            ->pluck('type')
            ->implode(', ') ?: 'unknown';

        $userMetrics = DB::table('user_metrics')
            ->where('userid', $userId)
            ->orderBy('created_at', 'desc')
            ->get(['title', 'description', 'category', 'weight', 'percentage']);

        // Get key business data
        $tasks = DB::table('tasks')
            ->where('owner_id', $userId)
            ->select(['title', 'status', 'priority', 'created_at', 'due_date'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $projects = DB::table('projects')
            ->where('owner_id', $userId)
            ->select(['name', 'created_at'])
            ->get();

        $taskStats = DB::table('tasks')
            ->where('owner_id', $userId)
            ->selectRaw('
                COUNT(*) as total_tasks,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_tasks,
                SUM(CASE WHEN due_date < NOW() AND status != "completed" THEN 1 ELSE 0 END) as overdue_tasks
            ')
            ->first();

        return [
            'workspace_types' => $workspaceTypes,
            'metrics' => $userMetrics,
            'tasks' => $tasks,
            'projects' => $projects,
            'task_stats' => $taskStats,
            'has_data' => $tasks->count() > 0 || $projects->count() > 0
        ];
    }

    private function buildAnalysisPrompt($userData, $query)
    {
        $metricsText = "";
        foreach ($userData['metrics'] as $metric) {
            $metricsText .= "• {$metric->title}: {$metric->description} (Category: {$metric->category}, Weight: {$metric->weight}, Percentage: {$metric->percentage}%)\n";
        }
        if (empty($metricsText)) {
            $metricsText = "No custom metrics defined by user.";
        }

        return "You are a data analyst. Provide a clear text analysis based on the user's query and their data.

USER CONTEXT:
- Workspace types: {$userData['workspace_types']}
- Custom metrics: {$metricsText}

USER DATA:
- Task Statistics: Total: {$userData['task_stats']->total_tasks}, Completed: {$userData['task_stats']->completed_tasks}, Pending: {$userData['task_stats']->pending_tasks}, Overdue: {$userData['task_stats']->overdue_tasks}
- Recent Tasks: " . json_encode($userData['tasks']->take(10)) . "
- Projects: " . json_encode($userData['projects']) . "
- Also your response should start with  e.g BAse on the report of the user... then continue.

- Give professional but friendly text response
- No charts, templates, or visual elements  
- Focus on key insights and actionable recommendations
- Use emojis appropriately for readability
- If no data, explain briefly and suggest alternatives
- Include performance insights and recommendations

User asked: \"{$query}\"

Provide a comprehensive text analysis:";
    }

    // Cache database schema (run this once and cache)
    private function getDatabaseSchema()
    {
        return Cache::remember('database_schema', 3600, function () {
            $tables = ['tasks', 'projects', 'teams', 'platform_users', 'linked', 'sub_issues', 'user_metrics'];
            $schema = [];

            foreach ($tables as $table) {
                $columns = DB::select("DESCRIBE {$table}");
                $schema[$table] = array_map(function ($col) {
                    return $col->Field . ' (' . $col->Type . ')';
                }, $columns);
            }

            return json_encode($schema);
        });
    }

    public function managers()
    {
        $managers = DB::table("managers")->where(["userid" => Auth::id()])->get();
        return view("dash.managers", compact("managers"));
    }

    public function upload_file(Request $request)
    {
        // $request->validate([
        //     'profilePicture' => 'required|file|image|max:2048',
        // ]);

        $file = $request->file('profilePicture');

        // Generate unique file name
        $filename = time() . '_' . $file->getClientOriginalName();

        // Move file to public/uploads folder (create if not exists)
        $destinationPath = public_path('uploads');

        // Make sure directory exists
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);

        // URL to access the file
        $url = url('uploads/' . $filename);

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'url' => $url,
            'message' => 'Profile picture uploaded successfully',
        ]);
    }

    public function add_manager(Request $request){

        $check = DB::table("managers")->where(["email"=>$request->email])->first();
        if($check){
            return response()->json(["message"=>"please use another email"],400);
        }
        $member = "RB-".rand();
        DB::table("managers")->insert([
                "name"=>$request->name,
                "email"=>$request->email,
                "department"=>$request->department,
                "workspace"=>Auth::user()->workspace,
                "phone"=>$request->phone,
                "status"=>"pending",
                "manager_id"=>$member,
                "userid"=>Auth::id(),
                "image"=>$request->image ? $request->image : "/profile.png",
                "note"=>$request->note
        ]);
             Mail::send('mail.invite_manager', ['name' => $request->name,"id"=>$member,"workspace"=>Auth::user()->workspace], function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Inivitation  to ReviewBod - manager');
        });

                    return response()->json(["message"=>"Manager Added"]);

    }

    public function delete_manager(Request $request){
        DB::table("managers")->where(["id"=>$request->id])->delete();
        return response()->json(["message"=>"Delete Manager"]);
    }

public function edit_manager(Request $request)
{
    $check = DB::table("managers")
        ->where([
            'id' => $request->id,
            'workspace' => Auth::user()->workspace
        ])
        ->first();

    if (!$check) {
        return response()->json(["message" => "Something went wrong"], 400);
    }

    // Check if email exists for another manager
    $emailExists = DB::table("managers")
        ->where('workspace', Auth::user()->workspace)
        ->where('email', $request->email)
        ->where('id', '!=', $request->id)
        ->exists();

    if ($emailExists) {
        return response()->json(["message" => "Email already in use"], 409);
    }

    // Check if phone exists for another manager
    $phoneExists = DB::table("managers")
        ->where('workspace', Auth::user()->workspace)
        ->where('phone', $request->phone)
        ->where('id', '!=', $request->id)
        ->exists();

    if ($phoneExists) {
        return response()->json(["message" => "Phone already in use"], 409);
    }

    DB::table("managers")->where(["id" => $request->id])->update([
        "name"       => $request->name,
        "email"      => $request->email,
        "department" => $request->department, 
        "phone"      => $request->phone,
        "status"     => $request->status,  
        "image"      => $request->image,
        "note"       => $request->note
    ]);

    return response()->json(["message" => "Manager Updated"]);
}



public function manager_setstatus(Request $request){
            DB::table('managers')->where(["id"=>$request->id,'userid'=>Auth::id()])->update(["status"=>$request->status]);
        return response()->json(["message"=>"Manager status updated to $request->status"]);
}



    public function delete_bulk_managers(Request $request){
        $users = $request->users;
        // dd($users);
        foreach($users as $u){
            DB::table("managers")->where(["id"=>$u['id'],"userid"=>Auth::id()])->delete();
        }

                return response()->json(["message"=>"Managers deleted successfully"]);

    }


    public function bulk_block_managers(Request $request){
             $users = $request->users;
        // dd($users);
        foreach($users as $u){
            DB::table("managers")->where(["id"=>$u['id'],"userid"=>Auth::id()])->update(["status"=>"blocked"]);
        }

                return response()->json(["message"=>"Managers deleted successfully"]);
    }

    public function user_manager(Request $request){
        DB::table("platform_users")->where(["id"=>$request->id,"owner_id"=>Auth::id()])->update(["manager_id"=>$request->manager_id]);
                        return response()->json(["message"=>"Managers added successfully","success"=>true]);

    }

}