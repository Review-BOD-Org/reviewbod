<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use OpenAI\Laravel\Facades\OpenAI;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    protected $token;

    public function __construct()
    {
        // Token will be set in each method
    }

    /**
     * Show the reports view
     */
    public function reports()
    {
        return view("dash.reports");
    }

    private function getLinearToken()
    {
        $data = DB::table("linked")->where(["userid" => auth()->id(), "type" => "linear"])->first();
        return $data->token ?? null;
    }

    public function getReportData(Request $request)
    {
        $this->token = $this->getLinearToken();
        if (!$this->token) {
            return response()->json(['error' => 'Linear account not connected'], 400);
        }

        try {
            // Parse date range
            $range = $request->input('range', '7days');
            $dates = $this->getDateRange($range, $request);

            // Fetch data from Linear API
            $users = $this->fetchUsers();
            $teams = $this->fetchTeams();
            $projects = $this->fetchProjects();
            $issues = $this->fetchIssues($dates['start'], $dates['end']);

            // Calculate stats
            $userStats = $this->calculateUserStats($users);
            $projectStats = $this->calculateProjectStats($projects);
            $teamStats = $this->calculateTeamStats($teams);

            // Calculate KPIs
            $kpi = $this->calculateKPIs($issues, $projects, $users);

            // Get activities
            $activities = $this->getRecentActivities($issues, $users, $teams, $projects);

            // Prepare chart data
            $chartData = $this->prepareChartData($issues, $teams, $projects, $users);

            return response()->json([
                'stats' => [
                    'users' => $userStats,
                    'projects' => $projectStats,
                    'teams' => $teamStats
                ],
                'kpi' => $kpi,
                'activities' => $activities,
                'charts' => $chartData
            ]);
        } catch (\Exception $e) {
            Log::error('Linear API error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch data from Linear API'], 500);
        }
    }

    public function getAnalysis(Request $request)
    {
        $this->token = $this->getLinearToken();
        if (!$this->token) {
            return response()->json(['error' => 'Linear account not connected'], 400);
        }

        try {
            // Parse date range
            $range = $request->input('range', '7days');
            $dates = $this->getDateRange($range, $request);

            // Fetch necessary data for analysis
            $users = $this->fetchUsers();
            $teams = $this->fetchTeams();
            $projects = $this->fetchProjects();
            $issues = $this->fetchIssues($dates['start'], $dates['end']);

            // Calculate metrics for OpenAI context
            $metrics = $this->prepareMetricsForAI($issues, $projects, $users, $teams);

            // Get AI analysis using OpenAI
            $analysis = $this->generateAIAnalysis($metrics);
            $recommendations = $this->generateRecommendations($metrics);

            return response()->json([
                'analysis' => $analysis,
                'recommendations' => $recommendations
            ]);
        } catch (\Exception $e) {
            Log::error('AI analysis error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate AI analysis'], 500);
        }
    }

    public function exportReport(Request $request)
    {
        $this->token = $this->getLinearToken();
        if (!$this->token) {
            return response()->json(['error' => 'Linear account not connected'], 400);
        }

        try {
            // Parse date range
            $range = $request->input('range', '7days');
            $dates = $this->getDateRange($range, $request);

            // Fetch data
            $users = $this->fetchUsers();
            $teams = $this->fetchTeams();
            $projects = $this->fetchProjects();
            $issues = $this->fetchIssues($dates['start'], $dates['end']);

            // Prepare export data
            $exportData = $this->prepareExportData($issues, $projects, $users, $teams, $dates);

            // Generate CSV
            $filename = 'linear_report_' . date('Y-m-d') . '.csv';

            return new StreamedResponse(function() use ($exportData) {
                $handle = fopen('php://output', 'w');

                // Add headers
                fputcsv($handle, array_keys($exportData['headers']));

                // Add summary stats
                foreach ($exportData['summary'] as $row) {
                    fputcsv($handle, $row);
                }

                // Add spacer
                fputcsv($handle, ['']);

                // Add activities
                fputcsv($handle, ['Activity Type', 'Description', 'Related To', 'Date']);
                foreach ($exportData['activities'] as $activity) {
                    fputcsv($handle, [
                        $activity['type'],
                        $activity['description'],
                        $activity['relatedTo'],
                        $activity['date']
                    ]);
                }

                fclose($handle);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Report export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export report');
        }
    }

    private function getDateRange($range, Request $request)
    {
        $end = Carbon::now();

        switch ($range) {
            case '7days':
                $start = Carbon::now()->subDays(7);
                break;
            case '30days':
                $start = Carbon::now()->subDays(30);
                break;
            case '90days':
                $start = Carbon::now()->subDays(90);
                break;
            case 'custom':
                $start = Carbon::parse($request->input('startDate'));
                $end = Carbon::parse($request->input('endDate'));
                break;
            default:
                $start = Carbon::now()->subDays(7);
        }

        return [
            'start' => $start->toIso8601String(),
            'end' => $end->toIso8601String()
        ];
    }

    private function fetchUsers()
    {
        $query = '
            query {
                users {
                    nodes {
                        id
                        name
                        email
                        createdAt
                        active
                    }
                }
            }
        ';

        $response = Http::withToken($this->token)
            ->post('https://api.linear.app/graphql', [
                'query' => $query
            ]);

        return $response->json()['data']['users']['nodes'] ?? [];
    }

    private function fetchTeams()
    {
        $query = '
            query {
                teams {
                    nodes {
                        id
                        name
                        key
                        description
                        createdAt
                        members {
                            nodes {
                                id
                            }
                        }
                    }
                }
            }
        ';

        $response = Http::withToken($this->token)
            ->post('https://api.linear.app/graphql', [
                'query' => $query
            ]);

        return $response->json()['data']['teams']['nodes'] ?? [];
    }

    private function fetchProjects()
    {
        $query = '
            query {
                projects {
                    nodes {
                        id
                        name
                        description
                        state
                        createdAt
                        completedAt
                        progress
                    }
                }
            }
        ';

        $response = Http::withToken($this->token)
            ->post('https://api.linear.app/graphql', [
                'query' => $query
            ]);

        return $response->json()['data']['projects']['nodes'] ?? [];
    }

    private function fetchIssues($startDate, $endDate)
    {
        $query = '
            query($startDate: DateTimeOrDuration!, $endDate: DateTimeOrDuration!) {
                issues(
                    filter: {
                        createdAt: { gte: $startDate, lte: $endDate }
                    }
                ) {
                    nodes {
                        id
                        title
                        description
                        state {
                            name
                            type
                        }
                        createdAt
                        completedAt
                        assignee {
                            id
                            name
                        }
                        team {
                            id
                            name
                        }
                        project {
                            id
                            name
                        }
                    }
                }
            }
        ';
    
        $response = Http::withToken($this->token)
            ->post('https://api.linear.app/graphql', [
                'query' => $query,
                'variables' => [
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]
            ]);
    
        // Log response for debugging (optional)
        Log::info('Linear API fetchIssues response', ['response' => $response->json()]);
    
        return $response->json()['data']['issues']['nodes'] ?? [];
    }

    private function calculateUserStats($users)
    {
        $total = count(array_filter($users, function($user) {
            return $user['active'] === true;
        }));

        $growth = rand(-5, 15); // Mock growth, replace with real data if available

        return [
            'total' => $total,
            'growth' => $growth
        ];
    }

    private function calculateProjectStats($projects)
    {
        $activeProjects = count(array_filter($projects, function($project) {
            return $project['completedAt'] === null;
        }));

        $growth = rand(-5, 15); // Mock growth

        return [
            'total' => $activeProjects,
            'growth' => $growth
        ];
    }

    private function calculateTeamStats($teams)
    {
        $total = count($teams);

        $growth = rand(-5, 15); // Mock growth

        return [
            'total' => $total,
            'growth' => $growth
        ];
    }

    private function calculateKPIs($issues, $projects, $users)
    {
        $completed = count(array_filter($issues, function($issue) {
            return $issue['state']['type'] === 'completed';
        }));

        $teamEfficiency = $issues ? round(($completed / count($issues)) * 100) : 0;

        $completedProjects = count(array_filter($projects, function($project) {
            return $project['completedAt'] !== null;
        }));

        $projectCompletion = $projects ? round(($completedProjects / count($projects)) * 100) : 0;
        // dd($completedProjects);
        $usersWithIssues = [];
        foreach ($issues as $issue) {
            if (isset($issue['assignee']['id'])) {
                $usersWithIssues[$issue['assignee']['id']] = true;
            }
        }

        $activeUsers = count(array_filter($users, function($user) {
            return $user['active'] === true;
        }));

        $userEngagement = $activeUsers ? round((count($usersWithIssues) / $activeUsers) * 100) : 0;

        return [
            'teamEfficiency' => $teamEfficiency,
            'projectCompletion' => $projectCompletion,
            'userEngagement' => $userEngagement
        ];
    }

    private function getRecentActivities($issues, $users, $teams, $projects)
    {
        $activities = [];

        foreach (array_slice($issues, 0, 10) as $issue) {
            $activities[] = [
                'type' => 'projects',
                'description' => 'Issue ' . $issue['title'] . ' ' . ($issue['state']['type'] === 'completed' ? 'completed' : 'created'),
                'relatedTo' => isset($issue['project']['name']) ? $issue['project']['name'] : 'General',
                'date' => $issue['state']['type'] === 'completed' ? $issue['completedAt'] : $issue['createdAt']
            ];
        }

        foreach (array_slice($projects, 0, 5) as $project) {
            $activities[] = [
                'type' => 'projects',
                'description' => 'Project ' . $project['name'] . ' ' . ($project['completedAt'] ? 'completed' : 'in progress'),
                'relatedTo' => $project['name'],
                'date' => $project['createdAt']
            ];
        }

        foreach (array_slice($users, 0, 5) as $user) {
            $activities[] = [
                'type' => 'users',
                'description' => 'User ' . $user['name'] . ' ' . ($user['active'] ? 'active' : 'inactive'),
                'relatedTo' => $user['name'],
                'date' => $user['createdAt']
            ];
        }

        foreach (array_slice($teams, 0, 5) as $team) {
            $activities[] = [
                'type' => 'teams',
                'description' => 'Team ' . $team['name'] . ' created',
                'relatedTo' => $team['name'],
                'date' => $team['createdAt']
            ];
        }

        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, 20);
    }

    private function prepareChartData($issues, $teams, $projects, $users)
    {
        // Team Activity: Issues updated or completed per team per week over last 4 weeks
        $teamActivity = [
            'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            'datasets' => []
        ];
    
        $weeks = [
            [Carbon::now()->subWeeks(3)->startOfWeek(), Carbon::now()->subWeeks(3)->endOfWeek()],
            [Carbon::now()->subWeeks(2)->startOfWeek(), Carbon::now()->subWeeks(2)->endOfWeek()],
            [Carbon::now()->subWeeks(1)->startOfWeek(), Carbon::now()->subWeeks(1)->endOfWeek()],
            [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
        ];
    
        foreach (array_slice($teams, 0, 3) as $index => $team) {
            $teamData = [0, 0, 0, 0];
        
            foreach ($issues as $issue) {
             
                if ($issue['team']['id'] === $team['id'] && (isset($issue['completedAt']) || isset($issue['createdAt']))) {
                    $activityDate = Carbon::parse($issue['completedAt'] ?? $issue['createdAt']);
                    foreach ($weeks as $weekIndex => $week) {
                        if ($activityDate->between($week[0], $week[1])) {
                            $teamData[$weekIndex]++;
                        }
                    }
                }
            }
            $teamActivity['datasets'][] = [
                'label' => $team['name'],
                'data' => $teamData,
                'borderColor' => $this->getChartColor($index),
                'backgroundColor' => $this->getChartColorAlpha($index),
                'tension' => 0.4
            ];
        }

        // Project Progress: Completed vs Remaining
        $projectLabels = [];
        $completedData = [];
        $remainingData = [];

        foreach (array_slice($projects, 0, 5) as $project) {
            $projectLabels[] = $project['name'];
            $progress = $project['progress'] ?? 0;
            $completedData[] = $progress;
            $remainingData[] = 100 - $progress;
        }

        $projectProgress = [
            'labels' => $projectLabels,
            'datasets' => [
                [
                    'label' => 'Completed',
                    'data' => $completedData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.7)'
                ],
                [
                    'label' => 'Remaining',
                    'data' => $remainingData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.7)'
                ]
            ]
        ];

        // User Performance: Metrics based on user activity
        $userPerformance = [
            'labels' => ['Tasks Completed', 'Response Time', 'Communication', 'Team Collaboration', 'Innovation'],
            'datasets' => []
        ];

        foreach (array_slice($users, 0, 3) as $index => $user) {
            if ($user['active']) {
                // Tasks Completed: Count of completed issues
                $completedTasks = count(array_filter($issues, function($issue) use ($user) {
                    return $issue['state']['type'] === 'completed' && isset($issue['assignee']['id']) && $issue['assignee']['id'] === $user['id'];
                }));

                // Response Time: Average days to complete issues
                $completedIssues = array_filter($issues, function($issue) use ($user) {
                    return $issue['state']['type'] === 'completed' && isset($issue['assignee']['id']) && $issue['assignee']['id'] === $user['id'] && isset($issue['createdAt']) && isset($issue['completedAt']);
                });
                $responseTimes = array_map(function($issue) {
                    return Carbon::parse($issue['createdAt'])->diffInDays(Carbon::parse($issue['completedAt']));
                }, $completedIssues);
                $avgResponseTime = $responseTimes ? array_sum($responseTimes) / count($responseTimes) : 0;
                $responseScore = $avgResponseTime > 0 ? max(0, 100 - ($avgResponseTime * 5)) : 100; // Lower is better, scale to 0-100

                // Communication: Number of assigned issues (proxy)
                $assignedIssues = count(array_filter($issues, function($issue) use ($user) {
                    return isset($issue['assignee']['id']) && $issue['assignee']['id'] === $user['id'];
                }));
                $communicationScore = min(100, $assignedIssues * 10); // Normalize to 0-100

                // Team Collaboration: Number of teams user is a member of
                $teamCount = count(array_filter($teams, function($team) use ($user) {
                    return in_array($user['id'], array_column($team['members']['nodes'], 'id'));
                }));
                $collaborationScore = min(100, $teamCount * 20); // Normalize to 0-100

                // Innovation: Number of assigned issues (proxy, as creator data not fetched)
                $innovationScore = min(100, $assignedIssues * 10); // Normalize to 0-100

                $userPerformance['datasets'][] = [
                    'label' => $user['name'],
                    'data' => [
                        min(100, $completedTasks * 10), // Normalize to 0-100
                        $responseScore,
                        $communicationScore,
                        $collaborationScore,
                        $innovationScore
                    ],
                    'borderColor' => $this->getChartColor($index),
                    'backgroundColor' => $this->getChartColorAlpha($index)
                ];
            }
        }

        return [
            'teamActivity' => $teamActivity,
            'projectProgress' => $projectProgress,
            'userPerformance' => $userPerformance
        ];
    }

    private function getChartColor($index)
    {
        $colors = [
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)'
        ];

        return $colors[$index % count($colors)];
    }

    private function getChartColorAlpha($index)
    {
        $colors = [
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)'
        ];

        return $colors[$index % count($colors)];
    }

    private function prepareMetricsForAI($issues, $projects, $users, $teams)
    {
        $metrics = [
            'issueCount' => count($issues),
            'completedIssues' => count(array_filter($issues, function($issue) {
                return $issue['state']['type'] === 'completed';
            })),
            'activeProjects' => count(array_filter($projects, function($project) {
                return $project['completedAt'] === null;
            })),
            'completedProjects' => count(array_filter($projects, function($project) {
                return $project['completedAt'] !== null;
            })),
            'activeUsers' => count(array_filter($users, function($user) {
                return $user['active'] === true;
            })),
            'teamCount' => count($teams),
            'averageProjectProgress' => 0
        ];

        $totalProgress = 0;
        $projectCount = count($projects);

        foreach ($projects as $project) {
            $totalProgress += $project['progress'] ?? 0;
        }

        $metrics['averageProjectProgress'] = $projectCount ? round($totalProgress / $projectCount) : 0;

        return $metrics;
    }

    private function generateAIAnalysis($metrics)
    {
        try {
            $prompt = "Generate a concise data analysis for a team dashboard based on these metrics:\n";
            $prompt .= "Total Issues: {$metrics['issueCount']}\n";
            $prompt .= "Completed Issues: {$metrics['completedIssues']}\n";
            $prompt .= "Active Projects: {$metrics['activeProjects']}\n";
            $prompt .= "Completed Projects: {$metrics['completedProjects']}\n";
            $prompt .= "Active Users: {$metrics['activeUsers']}\n";
            $prompt .= "Number of Teams: {$metrics['teamCount']}\n";
            $prompt .= "Average Project Progress: {$metrics['averageProjectProgress']}%\n\n";
            $prompt .= "Provide 2-3 concise paragraphs of professional analysis about team performance, project status and overall progress. Focus on trends, efficiency, and areas worth noting. Write in a professional, data-driven style suitable for a management dashboard.";

            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 350,
                'temperature' => 0.7
            ]);

            $analysis = $result->choices[0]->message->content;

            return explode("\n\n", trim($analysis));
        } catch (\Exception $e) {
            Log::error('OpenAI error: ' . $e->getMessage());
            return ['Unable to generate AI analysis at this time.'];
        }
    }

    private function generateRecommendations($metrics)
    {
        try {
            $prompt = "Based on these project metrics:\n";
            $prompt .= "Total Issues: {$metrics['issueCount']}\n";
            $prompt .= "Completed Issues: {$metrics['completedIssues']}\n";
            $prompt .= "Active Projects: {$metrics['activeProjects']}\n";
            $prompt .= "Completed Projects: {$metrics['completedProjects']}\n";
            $prompt .= "Active Users: {$metrics['activeUsers']}\n";
            $prompt .= "Number of Teams: {$metrics['teamCount']}\n";
            $prompt .= "Average Project Progress: {$metrics['averageProjectProgress']}%\n\n";
            $prompt .= "Generate 5 specific, actionable recommendations to improve team performance, project completion rates, and efficiency. Make them concise, practical and directly related to the metrics above. Format your response as a simple list with each recommendation on a new line with no numbering.";

            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 250,
                'temperature' => 0.7
            ]);

            $analysis = $result->choices[0]->message->content;

            $recommendations = preg_replace('/^\d+\.\s+/m', '', $analysis);
            $recommendationsArray = array_values(array_filter(array_map('trim', explode("\n", $recommendations)), function($item) {
                return !empty($item);
            }));

            if (empty($recommendationsArray)) {
                return $this->getMockRecommendations($metrics);
            }

            return $recommendationsArray;
        } catch (\Exception $e) {
            Log::error('OpenAI error: ' . $e->getMessage());
            return ['Unable to generate recommendations at this time.'];
        }
    }

    private function getMockAnalysis($metrics)
    {
        $completion = $metrics['completedIssues'] / max(1, $metrics['issueCount']) * 100;
        $completionRate = round($completion);

        return [
            "Your team has completed {$metrics['completedIssues']} out of {$metrics['issueCount']} issues, achieving a {$completionRate}% completion rate. This efficiency level is " . ($completionRate > 70 ? "strong and indicates good task management" : "below target and suggests potential workflow issues") . ". With {$metrics['activeProjects']} active projects running concurrently, resource allocation appears " . ($metrics['activeProjects'] > 5 ? "stretched" : "manageable") . ".",
            "Project progress metrics show an average completion rate of {$metrics['averageProjectProgress']}%, which is " . ($metrics['averageProjectProgress'] > 60 ? "on track with quarterly objectives" : "falling behind expected timelines") . ". The {$metrics['teamCount']} teams are coordinating across multiple workstreams, with team utilization rates suggesting " . ($metrics['teamCount'] > 3 ? "effective collaboration structures" : "potential resource constraints") . ".",
            "User engagement metrics indicate that all {$metrics['activeUsers']} active users are contributing to project advancement. The distribution of tasks across team members appears " . (($metrics['issueCount'] / max(1, $metrics['activeUsers'])) > 5 ? "heavy, with potential bottlenecks forming" : "well-balanced, promoting steady progress") . ". Focus should be maintained on " . ($completionRate < 70 ? "increasing completion rates" : "maintaining the current momentum") . " while ensuring quality standards remain high."
        ];
    }

    private function getMockRecommendations($metrics)
    {
        $completion = $metrics['completedIssues'] / max(1, $metrics['issueCount']) * 100;
        $completionRate = round($completion);

        $recommendations = [
            "Implement weekly sprint reviews to improve the current {$completionRate}% task completion rate to a target of 85%.",
            "Redistribute workload across teams to optimize resource allocation for {$metrics['activeProjects']} active projects.",
            "Establish cross-team collaboration sessions to share best practices and increase overall project velocity.",
            "Focus on reducing bottlenecks in projects below 50% completion rate to raise the average progress above 70%.",
            "Create specialized task forces for critical path items to accelerate completion of high-priority deliverables."
        ];

        if ($completionRate < 60) {
            $recommendations[] = "Conduct project prioritization workshop to focus team efforts on most impactful initiatives.";
        }

        if ($metrics['activeProjects'] > 5) {
            $recommendations[] = "Consider temporarily freezing new project initiations until current completion rates improve.";
        }

        return array_slice($recommendations, 0, 5);
    }

    private function prepareExportData($issues, $projects, $users, $teams, $dates)
    {
        $completion = count($issues) > 0
            ? round((count(array_filter($issues, function($i) { return $i['state']['type'] === 'completed'; })) / count($issues)) * 100)
            : 0;

        $avgProgress = count($projects) > 0
            ? round(array_sum(array_column(array_map(function($p) { return ['progress' => $p['progress'] ?? 0]; }, $projects), 'progress')) / count($projects))
            : 0;

        return [
            'headers' => [
                'Report Generated' => date('Y-m-d H:i:s'),
                'Period' => Carbon::parse($dates['start'])->format('M d, Y') . ' to ' . Carbon::parse($dates['end'])->format('M d, Y')
            ],
            'summary' => [
                ['Total Issues', count($issues)],
                ['Completed Issues', count(array_filter($issues, function($i) { return $i['state']['type'] === 'completed'; }))],
                ['Completion Rate', $completion . '%'],
                ['Active Projects', count(array_filter($projects, function($p) { return $p['completedAt'] === null; }))],
                ['Average Project Progress', $avgProgress . '%'],
                ['Active Teams', count($teams)],
                ['Active Users', count(array_filter($users, function($u) { return $u['active'] === true; }))]
            ],
            'activities' => $this->getRecentActivities($issues, $users, $teams, $projects)
        ];
    }
}
?>