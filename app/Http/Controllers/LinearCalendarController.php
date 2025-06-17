<?php
// This is the endpoint that will fetch and return Linear data for the calendar
// You can place this in a new file like linear-calendar-api.php

namespace App\Http\Controllers;
use DB;
use Auth; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
class LinearCalendarController extends Controller
{
    // Get calendar data from Linear
    public function getCalendarData(Request $request)
    {
        $data = DB::table("linked")->where(["userid" => auth()->id(),"type"=>"linear"])->first();

        // Get access token and user ID from authenticated user or session
        $accessToken = $data->token;
        $userId = $request->linear_user_id;
        
        if (!$accessToken || !$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Linear access token or user ID not found'
            ], 400);
        }
        
        // Get user data including issues with due dates
        $userData = $this->getFullLinearUserData($accessToken, $userId);
        
        if (!$userData['success']) {
            return response()->json($userData, 400);
        }
        
        // Get upcoming milestones
        $milestones = $this->getLinearMilestones($accessToken);
        
        // Get issue activity (for history)
        $issueActivity = $this->getIssueActivity($accessToken, $userId);
        
        return response()->json([
            'success' => true,
            'user' => $userData['user'],
            'milestones' => $milestones['success'] ? $milestones['data'] : [],
            'activity' => $issueActivity['success'] ? $issueActivity['data'] : []
        ]);
    }
    
    // Your existing function
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
    
                assignedIssues {
                    nodes {
                        id
                        title
                        identifier
                        description
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
                        children {  # Added subissues
                            nodes {
                                id
                                title
                                identifier
                                description
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
    
    // New function to get milestones
    public function getLinearMilestones($accessToken)
    {
        $query = <<<GQL
        query GetMilestones {
            milestones {
                nodes {
                    id
                    name
                    description
                    targetDate
                    state
                    projects {
                        nodes {
                            id
                            name
                        }
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
            'data' => $data['data']['milestones']['nodes'] ?? []
        ];
    }
    
    // New function to get issue activity
    public function getIssueActivity($accessToken, $userId)
    {
        $query = <<<GQL
        query GetIssueActivity($userId: String!) {
            issueHistory(
                filter: {
                    actor: { id: { eq: $userId } }
                }
                first: 50
            ) {
                nodes {
                    id
                    createdAt
                    issue {
                        id
                        title
                        identifier
                    }
                    fromState {
                        id
                        name
                    }
                    toState {
                        id
                        name
                    }
                }
            }
        }
        GQL;

        $response = Http::withToken($accessToken)
            ->post('https://api.linear.app/graphql', [
                'query' => $query,
                'variables' => [
                    'userId' => $userId
                ]
            ]);

        if ($response->failed()) {
            return [
                'success' => false,
                'message' => 'Linear API request failed.',
                'status' => $response->status(),
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
            'data' => $data['data']['issueHistory']['nodes'] ?? []
        ];
    }
    

    public function getDashboardData(Request $request)
    {
        $linearData = DB::table("linked")->where([
            "userid" => auth()->id(),
            "type" => "linear"
        ])->first();

        if (!$linearData) {
            return response()->json([
                'success' => false,
                'message' => 'Linear account not linked.'
            ], 400);
        }

        $accessToken = $linearData->token;
        $userId = $request->id;

        $timeRange = $request->input('time_range', 'last_week');
        $endDate = Carbon::now();
        $startDate = null;

        switch ($timeRange) {
            case 'last_week':
                $startDate = Carbon::now()->subDays(7);
                break;
            case 'last_month':
                $startDate = Carbon::now()->subDays(30);
                break;
            case 'last_quarter':
                $startDate = Carbon::now()->subDays(90);
                break;
                default:
                $startDate = Carbon::createFromTimestamp(0);
                
        }

        $analyticsData = $this->fetchLinearAnalytics($accessToken, $userId, $startDate, $endDate);

        if (!$analyticsData['success']) {
            return response()->json([
                'success' => false,
                'message' => $analyticsData['message'] ?? 'Failed to fetch analytics data.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'timeRange' => $timeRange,
            'tasksCompleted' => $analyticsData['tasksCompleted'],
            'dailyStats' => $analyticsData['dailyStats'],
            'userRetention' => $analyticsData['userRetention'] // Added retention data
        ]);
    }


    private function fetchLinearAnalytics($accessToken, $userId, $startDate, $endDate)
    {
        $completedTasks = $this->getCompletedTasksAndSubtasks($accessToken, $userId, $startDate, $endDate);
        $dailyStats = $this->getDailyStats($accessToken, $userId, $startDate, $endDate);
        $userRetention = $this->getUserRetention($accessToken, $userId);

        return [
            'success' => true,
            'tasksCompleted' => $completedTasks,
            'dailyStats' => $dailyStats,
            'userRetention' => $userRetention
        ];
    }


    private function getCompletedTasksAndSubtasks($accessToken, $userId, $startDate, $endDate)
    {
        // Modified GraphQL query to explicitly request completedAt and state for subtasks
        $query = <<<GQL
        query GetCompletedIssues(\$userId: ID!, \$startDate: DateTimeOrDuration!, \$endDate: DateTimeOrDuration!) {
            issues(
                filter: {
                    assignee: { id: { eq: \$userId } },
                    completedAt: { gte: \$startDate, lte: \$endDate },
                    state: { type: { eq: "completed" } }
                }
                first: 100
            ) {
                nodes {
                    id
                    title
                    identifier
                    createdAt
                    completedAt
                    project {
                        id
                        name
                    }
                    children {
                        nodes {
                            id
                            title
                            identifier
                            createdAt
                            completedAt
                            state {
                                id
                                name
                                type
                            }
                        }
                    }
                }
            }
        }
        GQL;
    
        $response = Http::withToken($accessToken)
            ->post('https://api.linear.app/graphql', [
                'query' => $query,
                'variables' => [
                    'userId' => $userId,
                    'startDate' => $startDate->toIso8601String(),
                    'endDate' => $endDate->toIso8601String()
                ]
            ]);
    
        if ($response->failed() || isset($response->json()['errors'])) {
            return [
                'totalTasks' => 0,
                'totalSubtasks' => 0,
                'avgTaskDuration' => 0,
                'avgSubtaskDuration' => 0,
                'byDay' => []
            ];
        }
    
        $issues = $response->json()['data']['issues']['nodes'] ?? [];
    
        $totalTasks = 0;
        $totalSubtasks = 0;
        $taskDurations = [];
        $subtaskDurations = [];
        $byDayTasks = [];
        $byDaySubtasks = [];
    
        // For debugging
        $debug = [
            'completedSubtasks' => [],
            'incompleteSubtasks' => []
        ];
    
        foreach ($issues as $issue) {
            // Process parent task
            if (isset($issue['completedAt']) && $issue['completedAt']) {
                $totalTasks++;
                $completedDay = Carbon::parse($issue['completedAt'])->format('Y-m-d');
                $byDayTasks[$completedDay] = ($byDayTasks[$completedDay] ?? 0) + 1;
    
                // Calculate duration
                $created = Carbon::parse($issue['createdAt']);
                $completed = Carbon::parse($issue['completedAt']);
                $durationHours = $created->diffInHours($completed);
                $taskDurations[] = $durationHours;
            }
    
            // Process subtasks
            if (isset($issue['children']['nodes']) && is_array($issue['children']['nodes'])) {
                foreach ($issue['children']['nodes'] as $subissue) {
                    // Check if the subtask is completed within the date range
                    if (isset($subissue['completedAt']) && $subissue['completedAt']) {
                        $subCompletedAt = Carbon::parse($subissue['completedAt']);
                        
                        // Check if completion date is within our range
                        if ($subCompletedAt >= $startDate && $subCompletedAt <= $endDate) {
                            // Check if state type is completed (if available)
                            $isCompleted = isset($subissue['state']['type']) && $subissue['state']['type'] === 'completed';
                            
                            if ($isCompleted) {
                                $totalSubtasks++;
                                $subCompletedDay = $subCompletedAt->format('Y-m-d');
                                $byDaySubtasks[$subCompletedDay] = ($byDaySubtasks[$subCompletedDay] ?? 0) + 1;
    
                                // For debugging
                                $debug['completedSubtasks'][] = [
                                    'id' => $subissue['id'],
                                    'title' => $subissue['title'],
                                    'completedAt' => $subissue['completedAt'],
                                    'day' => $subCompletedDay
                                ];
    
                                // Calculate duration if createdAt is available
                                if (isset($subissue['createdAt'])) {
                                    $subCreated = Carbon::parse($subissue['createdAt']);
                                    $subDurationHours = $subCreated->diffInHours($subCompletedAt);
                                    $subtaskDurations[] = $subDurationHours;
                                }
                            } else {
                                // For debugging
                                $debug['incompleteSubtasks'][] = [
                                    'id' => $subissue['id'],
                                    'title' => $subissue['title'],
                                    'completedAt' => $subissue['completedAt'],
                                    'state' => $subissue['state'] ?? 'unknown'
                                ];
                            }
                        }
                    }
                }
            }
        }
    
        // Format byDay for chart
        $byDayFormatted = [];
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dayKey = $currentDate->format('Y-m-d');       // For your internal arrays
            $displayKey = $currentDate->format('M d');     // For display
            // \Log::info($dayKey);
            $byDayFormatted[] = [
                'date' => $displayKey,
                'tasks' => $byDayTasks[$dayKey] ?? 0,
                'subtasks' => $byDaySubtasks[$dayKey] ?? 0  // Using dayKey format for consistency
            ];
            $currentDate->addDay();
        }
        
        // Calculate average durations
        $avgTaskDuration = !empty($taskDurations) ? round(array_sum($taskDurations) / count($taskDurations), 1) : 0;
        $avgSubtaskDuration = !empty($subtaskDurations) ? round(array_sum($subtaskDurations) / count($subtaskDurations), 1) : 0;
    
        // Add debug info for development - remove in production
        $debug['byDaySubtasks'] = $byDaySubtasks;
        // \Log::info($byDaySubtasks);
        return [
            'totalTasks' => $totalTasks,
            'totalSubtasks' => $totalSubtasks,
            'avgTaskDuration' => $avgTaskDuration, // in hours
            'avgSubtaskDuration' => $avgSubtaskDuration, // in hours
            'byDay' => $byDayFormatted,
            'debug' => $debug // Remove this in production
        ];
    }

    private function getDailyStats($accessToken, $userId, $startDate, $endDate)
    {
        // Modified GraphQL query to ensure we get all necessary data for subtasks
        $query = <<<GQL
        query GetDailyStats(\$userId: ID!, \$startDate: DateTimeOrDuration!, \$endDate: DateTimeOrDuration!) {
            completedIssues: issues(
                filter: {
                    assignee: { id: { eq: \$userId } },
                    completedAt: { gte: \$startDate, lte: \$endDate },
                    state: { type: { eq: "completed" } }
                }
                first: 100
            ) {
                nodes {
                    id
                    completedAt
                    children {
                        nodes {
                            id
                            completedAt
                            state {
                                id
                                name
                                type
                            }
                        }
                    }
                }
            }
        }
        GQL;

        $response = Http::withToken($accessToken)
            ->post('https://api.linear.app/graphql', [
                'query' => $query,
                'variables' => [
                    'userId' => $userId,
                    'startDate' => $startDate->toIso8601String(),
                    'endDate' => $endDate->toIso8601String()
                ]
            ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return [];
        }

        $issues = $response->json()['data']['completedIssues']['nodes'] ?? [];

        $dailyStats = [];
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            $day = $currentDate->format('Y-m-d');
            $dayFormatted = $currentDate->format('M d');

            $dayTasks = 0;
            $daySubtasks = 0;

            foreach ($issues as $issue) {
                // Count parent task if completedAt exists
                if (isset($issue['completedAt']) && $issue['completedAt'] && 
                    Carbon::parse($issue['completedAt'])->format('Y-m-d') === $day) {
                    $dayTasks++;
                }

                // Count subtasks with proper null checks
                if (isset($issue['children']['nodes']) && is_array($issue['children']['nodes'])) {
                    foreach ($issue['children']['nodes'] as $subissue) {
                        if (isset($subissue['completedAt']) && $subissue['completedAt'] && 
                            isset($subissue['state']['type']) && $subissue['state']['type'] === 'completed' && 
                            Carbon::parse($subissue['completedAt'])->format('Y-m-d') === $day) {
                            $daySubtasks++;
                        }
                    }
                }
            }

            $dailyStats[] = [
                'date' => $dayFormatted,
                'tasks' => $dayTasks,
                'subtasks' => $daySubtasks
            ];

            $currentDate->addDay();
        }

        return $dailyStats;
    }
 
    // Helper method to get completed tasks
    private function getCompletedTasks($accessToken, $userId, $startDate, $endDate) 
    {
        $query = <<<GQL
            query GetCompletedIssues(\$userId: ID!, \$startDate: DateTimeOrDuration!, \$endDate: DateTimeOrDuration!) {
            issues(
                filter: {
                    assignee: { id: { eq: \$userId } },
                    completedAt: { gte: \$startDate, lte: \$endDate },
                    state: { type: { eq: "completed" } }
                }
                first: 100
            ) {
                nodes {
                    id
                    title
                    identifier
                    completedAt
                    project {
                        id
                        name
                    }
                }
            }
        }
        GQL;

        $response = Http::withToken($accessToken)
            ->post('https://api.linear.app/graphql', [
                'query' => $query,
                'variables' => [
                    'userId' => $userId,
                    'startDate' => $startDate->toIso8601String(),
                    'endDate' => $endDate->toIso8601String()
                ]
            ]);

           
        if ($response->failed() || isset($response->json()['errors'])) {
            return [
                'total' => 0,
                'byDay' => [],
                'byProject' => []
            ];
        }

        $issues = $response->json()['data']['issues']['nodes'] ?? [];
      
        // Count total completed tasks
        $total = count($issues);
        
        // Group tasks by day
        $byDay = [];
        foreach ($issues as $issue) {
            if (isset($issue['completedAt'])) {
                $day = Carbon::parse($issue['completedAt'])->format('Y-m-d');
                if (!isset($byDay[$day])) {
                    $byDay[$day] = 0;
                }
                $byDay[$day]++;
            }
        }
        
        // Convert to format for chart
        $byDayFormatted = [];
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dayKey = $currentDate->format('Y-m-d');
            $byDayFormatted[] = [
                'date' => $currentDate->format('M d'),
                'count' => $byDay[$dayKey] ?? 0
            ];
            $currentDate->addDay();
        }
        
        // Group tasks by project
        $byProject = [];
        foreach ($issues as $issue) {
            $projectName = isset($issue['project']['name']) ? $issue['project']['name'] : 'Unassigned';
            if (!isset($byProject[$projectName])) {
                $byProject[$projectName] = 0;
            }
            $byProject[$projectName]++;
        }
        
        return [
            'total' => $total,
            'byDay' => $byDayFormatted,
            'byProject' => $byProject
        ];
    }
    
    // Helper method to get user activity
    private function getUserActivity($accessToken, $userId, $startDate, $endDate)
    {
        // In reality, this would come from the Linear API
        // Here we're simulating some data as Linear doesn't have built-in login tracking
        
        // Query issue history for user activity simulation
        $query = <<<GQL
        query GetUserActivity(\$userId: ID!, \$startDate: DateTimeOrDuration!, \$endDate: DateTimeOrDuration!) {
            issueHistory(
                filter: {
                    actor: { id: { eq: \$userId } },
                    createdAt: { gte: \$startDate, lte: \$endDate }
                }
                first: 100
            ) {
                nodes {
                    id
                    createdAt
                }
            }
        }
        GQL;

        $response = Http::withToken($accessToken)
            ->post('https://api.linear.app/graphql', [
                'query' => $query,
                'variables' => [
                    'userId' => $userId,
                    'startDate' => $startDate->toIso8601String(),
                    'endDate' => $endDate->toIso8601String()
                ]
            ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return [
                'activeUsers' => 0,
                'totalSessions' => 0,
                'byDay' => []
            ];
        }

        $activities = $response->json()['data']['issueHistory']['nodes'] ?? [];
        
        // Group activities by day to simulate sessions
        $byDay = [];
        foreach ($activities as $activity) {
            if (isset($activity['createdAt'])) {
                $day = Carbon::parse($activity['createdAt'])->format('Y-m-d');
                if (!isset($byDay[$day])) {
                    $byDay[$day] = [];
                }
                $byDay[$day][] = $activity;
            }
        }
        
        // Calculate sessions (we consider activities within 30 minutes as one session)
        $sessions = 0;
        $sessionsByDay = [];
        
        foreach ($byDay as $day => $dayActivities) {
            $daySessions = 0;
            $lastActivity = null;
            
            // Sort activities by time
            usort($dayActivities, function($a, $b) {
                return Carbon::parse($a['createdAt'])->timestamp - Carbon::parse($b['createdAt'])->timestamp;
            });
            
            foreach ($dayActivities as $activity) {
                $activityTime = Carbon::parse($activity['createdAt']);
                
                if ($lastActivity === null || $activityTime->diffInMinutes(Carbon::parse($lastActivity['createdAt'])) > 30) {
                    $daySessions++;
                }
                
                $lastActivity = $activity;
            }
            
            $sessionsByDay[$day] = $daySessions;
            $sessions += $daySessions;
        }
        
        // Format for chart
        $byDayFormatted = [];
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dayKey = $currentDate->format('Y-m-d');
            $byDayFormatted[] = [
                'date' => $currentDate->format('M d'),
                'activeUsers' => isset($byDay[$dayKey]) ? 1 : 0, // Only 1 user in this case
                'sessions' => $sessionsByDay[$dayKey] ?? 0
            ];
            $currentDate->addDay();
        }
        
        return [
            'activeUsers' => count($byDay) > 0 ? 1 : 0, // Only tracking current user
            'totalSessions' => $sessions,
            'byDay' => $byDayFormatted
        ];
    }
    
    // Helper method to get engagement metrics
    private function getEngagementMetrics($accessToken, $userId, $startDate, $endDate)
    {
        // This would also come from Linear API
        // Here we'll use a combination of comments and issue updates to represent engagement
        
        $query = <<<GQL
        query GetEngagementData(\$userId: ID!, \$startDate: DateTimeOrDuration!, \$endDate: DateTimeOrDuration!) {
            issueHistory(
                filter: {
                    actor: { id: { eq: \$userId } },
                    createdAt: { gte: \$startDate, lte: \$endDate }
                }
                first: 100
            ) {
                nodes {
                    id
                    createdAt
                }
            }
            comments(
                filter: {
                    user: { id: { eq: \$userId } },
                    createdAt: { gte: \$startDate, lte: \$endDate }
                }
                first: 100
            ) {
                nodes {
                    id
                    createdAt
                }
            }
        }
        GQL;

        $response = Http::withToken($accessToken)
            ->post('https://api.linear.app/graphql', [
                'query' => $query,
                'variables' => [
                    'userId' => $userId,
                    'startDate' => $startDate->toIso8601String(),
                    'endDate' => $endDate->toIso8601String()
                ]
            ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return [
                'avgTimeMinutes' => 0,
                'totalInteractions' => 0,
                'byDay' => []
            ];
        }

        $history = $response->json()['data']['issueHistory']['nodes'] ?? [];
        $comments = $response->json()['data']['comments']['nodes'] ?? [];
        
        // Combine all interactions
        $interactions = array_merge($history, $comments);
        
        // Group by day
        $byDay = [];
        foreach ($interactions as $interaction) {
            if (isset($interaction['createdAt'])) {
                $day = Carbon::parse($interaction['createdAt'])->format('Y-m-d');
                if (!isset($byDay[$day])) {
                    $byDay[$day] = [];
                }
                $byDay[$day][] = $interaction;
            }
        }
        
        // Calculate average time (estimate based on number of interactions)
        $totalInteractions = count($interactions);
        $avgTimeMinutes = $totalInteractions > 0 ? round($totalInteractions * 2.5) : 0; // Assuming each interaction takes average 2.5 minutes
        
        // Format for chart
        $byDayFormatted = [];
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dayKey = $currentDate->format('Y-m-d');
            $dayInteractions = count($byDay[$dayKey] ?? []);
            $byDayFormatted[] = [
                'date' => $currentDate->format('M d'),
                'timeSpent' => $dayInteractions > 0 ? $dayInteractions * 2.5 : 0, // Estimated time in minutes
                'interactions' => $dayInteractions
            ];
            $currentDate->addDay();
        }
        
        return [
            'avgTimeMinutes' => $avgTimeMinutes,
            'totalInteractions' => $totalInteractions,
            'byDay' => $byDayFormatted
        ];
    }
    
    // Helper method to get user retention data
    private function getUserRetention($accessToken, $userId)
    {
        // Get user data to determine type
        $userData = $this->getFullLinearUserData($accessToken, $userId);
        
        if (!$userData['success']) {
            return [
                [
                    'cohort' => 'Unknown',
                    'userType' => 'Unknown',
                    'avgSessions' => '0',
                    'status' => 'Unknown'
                ]
            ];
        }
        
        $user = $userData['user'];
        if (!isset($user['createdAt'])) {
            return [
                [
                    'cohort' => 'Unknown',
                    'userType' => 'Unknown',
                    'avgSessions' => '0',
                    'status' => 'Unknown'
                ]
            ];
        }
        
        $createdAt = Carbon::parse($user['createdAt']);
        $now = Carbon::now();
        $monthsActive = $createdAt->diffInMonths($now);
        
        // Determine cohort based on join date
        $cohort = '';
        if ($monthsActive < 1) {
            $cohort = 'New Users';
        } elseif ($monthsActive < 3) {
            $cohort = 'Recent Users';
        } elseif ($monthsActive < 6) {
            $cohort = 'Established Users';
        } else {
            $cohort = 'Long-term Users';
        }
        
        // Determine type based on admin status
        $userType = isset($user['admin']) && $user['admin'] ? 'Admin' : 'Standard';
        
        // Calculate average sessions (placeholder - in real app would be from activity logs)
        $avgSessions = ($monthsActive > 0) ? "-" . '' : 'N/A';
        
        // Determine status based on lastSeen
        $lastSeen = isset($user['lastSeen']) ? Carbon::parse($user['lastSeen']) : null;
        $status = 'Inactive';
        
        if ($lastSeen && $lastSeen->diffInDays($now) < 7) {
            $status = 'Active';
        } elseif ($lastSeen && $lastSeen->diffInDays($now) < 30) {
            $status = 'Occasional';
        }
        
        return [
            [
                'cohort' => $cohort,
                'userType' => $userType,
                'avgSessions' => $avgSessions,
                'status' => $status
            ]
        ];
    }
    
 
}