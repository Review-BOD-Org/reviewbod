<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use OpenAI\Laravel\Facades\OpenAI;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TrelloReportController extends Controller
{
    protected $token;
    protected $key;

    public function __construct()
    {
        $this->key = 'e39869487a72d56e6758bd57b67fca4f'; // Consider storing in .env
    }

    /**
     * Show the reports view
     */
    public function reports()
    {
        return view("dash.reports");
    }

    private function getTrelloToken()
    {
        $data = DB::table("linked")->where(["userid" => auth()->id(), "type" => "trello"])->first();
        return $data->token ?? null;
    }

    public function getReportData(Request $request)
    {
        $this->token = $this->getTrelloToken();
        if (!$this->token) {
            return response()->json(['error' => 'Trello account not connected'], 400);
        }

        try {
            // Parse date range
            $range = $request->input('range', '7days');
            $dates = $this->getDateRange($range, $request);

            // Fetch data from Trello API
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
            Log::error('Trello API error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch data from Trello API'], 500);
        }
    }

    public function getAnalysis(Request $request)
    {
        $this->token = $this->getTrelloToken();
        if (!$this->token) {
            return response()->json(['error' => 'Trello account not connected'], 400);
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
        $this->token = $this->getTrelloToken();
        if (!$this->token) {
            return response()->json(['error' => 'Trello account not connected'], 400);
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
            $filename = 'trello_report_' . date('Y-m-d') . '.csv';

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
        $response = Http::get("https://api.trello.com/1/members/me", [
            'key' => $this->key,
            'token' => $this->token,
            'boards' => 'all',
            'fields' => 'fullName,username,email,avatarUrl,idBoards'
        ]);

        $memberData = $response->json();
        Log::info('Trello members/me response', ['memberData' => $memberData]);

        if (!isset($memberData['idBoards'])) {
            Log::error('Trello API: idBoards missing in members/me response', ['memberData' => $memberData]);
            return [];
        }

        $allMembers = [];
        foreach ($memberData['idBoards'] as $boardId) {
            $boardMembers = Http::get("https://api.trello.com/1/boards/{$boardId}/members", [
                'key' => $this->key,
                'token' => $this->token,
                'fields' => 'fullName,username,email,avatarUrl'
            ])->json();

            foreach ($boardMembers as $member) {
                $allMembers[$member['id']] = [
                    'id' => $member['id'],
                    'name' => $member['fullName'] ?? $member['username'],
                    'email' => $member['email'] ?? null,
                    'createdAt' => null, // Trello doesn't provide creation date
                    'active' => true // Assume active unless specified
                ];
            }
        }

        return array_values($allMembers);
    }

    private function fetchTeams()
    {
        $response = Http::get("https://api.trello.com/1/members/me", [
            'key' => $this->key,
            'token' => $this->token,
            'boards' => 'all',
            'fields' => 'idBoards'
        ]);

        $memberData = $response->json();
        Log::info('Trello members/me response for teams', ['memberData' => $memberData]);

        if (!isset($memberData['idBoards'])) {
            Log::error('Trello API: idBoards missing in members/me response for teams', ['memberData' => $memberData]);
            return [];
        }

        $teams = [];
        foreach ($memberData['idBoards'] as $boardId) {
            $boardData = Http::get("https://api.trello.com/1/boards/{$boardId}", [
                'key' => $this->key,
                'token' => $this->token,
                'fields' => 'name,desc,closed,dateLastActivity',
                'members' => 'all'
            ])->json();

            $boardMembers = Http::get("https://api.trello.com/1/boards/{$boardId}/members", [
                'key' => $this->key,
                'token' => $this->token,
                'fields' => 'id'
            ])->json();

            $teams[] = [
                'id' => $boardData['id'],
                'name' => $boardData['name'] ?? 'Unknown Board',
                'key' => $boardData['shortLink'] ?? '',
                'description' => $boardData['desc'] ?? '',
                'createdAt' => null, // Trello doesn't provide creation date
                'members' => [
                    'nodes' => array_map(fn($member) => ['id' => $member['id']], $boardMembers)
                ]
            ];
        }

        return $teams;
    }

    private function fetchProjects()
    {
        $response = Http::get("https://api.trello.com/1/members/me", [
            'key' => $this->key,
            'token' => $this->token,
            'boards' => 'all',
            'fields' => 'idBoards'
        ]);

        $memberData = $response->json();
        Log::info('Trello members/me /

response for projects', ['memberData' => $memberData]);

        if (!isset($memberData['idBoards'])) {
            Log::error('Trello API: idBoards missing in members/me response for projects', ['memberData' => $memberData]);
            return [];
        }

        $projects = [];
        foreach ($memberData['idBoards'] as $boardId) {
            $boardData = Http::get("https://api.trello.com/1/boards/{$boardId}", [
                'key' => $this->key,
                'token' => $this->token,
                'fields' => 'name,desc,closed,dateLastActivity',
                'cards' => 'all',
                'lists' => 'open'
            ])->json();

            $cards = $boardData['cards'] ?? [];
            $completedCards = count(array_filter($cards, function($card) use ($boardData) {
                $listId = $card['idList'] ?? '';
                foreach ($boardData['lists'] ?? [] as $list) {
                    if ($list['id'] === $listId && strpos(strtolower($list['name']), 'done') !== false) {
                        return true;
                    }
                }
                return false;
            }));

            $totalCards = count($cards);
            $progress = $totalCards > 0 ? round(($completedCards / $totalCards) * 100) : 0;

            $projects[] = [
                'id' => $boardData['id'],
                'name' => $boardData['name'] ?? 'Unknown Board',
                'description' => $boardData['desc'] ?? '',
                'state' => $boardData['closed'] ? 'completed' : 'active',
                'createdAt' => null, // Trello doesn't provide creation date
                'completedAt' => $boardData['closed'] ? $boardData['dateLastActivity'] : null,
                'progress' => $progress
            ];
        }

        return $projects;
    }

    private function fetchIssues($startDate, $endDate)
    {
        $response = Http::get("https://api.trello.com/1/members/me", [
            'key' => $this->key,
            'token' => $this->token,
            'boards' => 'all',
            'fields' => 'idBoards'
        ]);

        $memberData = $response->json();
        Log::info('Trello members/me response for issues', ['memberData' => $memberData]);

        if (!isset($memberData['idBoards'])) {
            Log::error('Trello API: idBoards missing in members/me response for issues', ['memberData' => $memberData]);
            return [];
        }

        $issues = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        foreach ($memberData['idBoards'] as $boardId) {
            $boardData = Http::get("https://api.trello.com/1/boards/{$boardId}", [
                'key' => $this->key,
                'token' => $this->token,
                'fields' => 'name',
                'cards' => 'all',
                'lists' => 'open',
                'members' => 'all'
            ])->json();

            $boardMembers = array_column($boardData['members'] ?? [], 'id');

            foreach ($boardData['cards'] ?? [] as $card) {
                $activityDate = Carbon::parse($card['dateLastActivity'] ?? null);
                if ($activityDate->between($start, $end)) {
                    $listId = $card['idList'] ?? '';
                    $listName = '';
                    $stateType = 'to_do';
                    foreach ($boardData['lists'] ?? [] as $list) {
                        if ($list['id'] === $listId) {
                            $listName = $list['name'];
                            $listNameLower = strtolower($listName);
                            if (strpos($listNameLower, 'done') !== false) {
                                $stateType = 'completed';
                            } elseif (strpos($listNameLower, 'doing') !== false) {
                                $stateType = 'started';
                            }
                            break;
                        }
                    }

                    $assignee = null;
                    if (!empty($card['idMembers']) && in_array($card['idMembers'][0], $boardMembers)) {
                        $memberData = Http::get("https://api.trello.com/1/members/{$card['idMembers'][0]}", [
                            'key' => $this->key,
                            'token' => $this->token,
                            'fields' => 'fullName'
                        ])->json();
                        $assignee = [
                            'id' => $card['idMembers'][0],
                            'name' => $memberData['fullName'] ?? $memberData['username'] ?? 'Unknown'
                        ];
                    }

                    $issues[] = [
                        'id' => $card['id'],
                        'title' => $card['name'] ?? 'Untitled Card',
                        'description' => $card['desc'] ?? '',
                        'state' => [
                            'name' => $listName ?: 'Unknown',
                            'type' => $stateType
                        ],
                        'createdAt' => null, // Trello doesn't provide creation date
                        'completedAt' => $stateType === 'completed' ? $card['dateLastActivity'] : null,
                        'assignee' => $assignee,
                        'team' => [
                            'id' => $boardData['id'],
                            'name' => $boardData['name'] ?? 'Unknown Board'
                        ],
                        'project' => [
                            'id' => $boardData['id'],
                            'name' => $boardData['name'] ?? 'Unknown Board'
                        ]
                    ];
                }
            }
        }

        return $issues;
    }

    private function calculateUserStats($users)
    {
        $total = count($users);
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

        $usersWithIssues = [];
        foreach ($issues as $issue) {
            if (isset($issue['assignee']['id'])) {
                $usersWithIssues[$issue['assignee']['id']] = true;
            }
        }
        $activeUsers = count($users);
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
                'description' => 'Card ' . $issue['title'] . ' ' . ($issue['state']['type'] === 'completed' ? 'completed' : 'updated'),
                'relatedTo' => $issue['project']['name'] ?? 'General',
                'date' => $issue['completedAt'] ?? $issue['createdAt'] ?? Carbon::now()->toIso8601String()
            ];
        }

        foreach (array_slice($projects, 0, 5) as $project) {
            $activities[] = [
                'type' => 'projects',
                'description' => 'Board ' . $project['name'] . ' ' . ($project['completedAt'] ? 'closed' : 'active'),
                'relatedTo' => $project['name'],
                'date' => $project['completedAt'] ?? Carbon::now()->toIso8601String()
            ];
        }

        foreach (array_slice($users, 0, 5) as $user) {
            $activities[] = [
                'type' => 'users',
                'description' => 'User ' . $user['name'] . ' active',
                'relatedTo' => $user['name'],
                'date' => $user['createdAt'] ?? Carbon::now()->toIso8601String()
            ];
        }

        foreach (array_slice($teams, 0, 5) as $team) {
            $activities[] = [
                'type' => 'teams',
                'description' => 'Board ' . $team['name'] . ' active',
                'relatedTo' => $team['name'],
                'date' => $team['createdAt'] ?? Carbon::now()->toIso8601String()
            ];
        }

        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, 20);
    }

    private function prepareChartData($issues, $teams, $projects, $users)
    {
        // Team Activity: Card updates per team per week over last 4 weeks
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
                if ($issue['team']['id'] === $team['id'] && isset($issue['completedAt'])) {
                    $activityDate = Carbon::parse($issue['completedAt']);
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
            // Tasks Completed: Count of completed cards
            $completedTasks = count(array_filter($issues, function($issue) use ($user) {
                return $issue['state']['type'] === 'completed' && isset($issue['assignee']['id']) && $issue['assignee']['id'] === $user['id'];
            }));

            // Response Time: Average days to complete tasks (simplified)
            $completedIssues = array_filter($issues, function($issue) use ($user) {
                return $issue['state']['type'] === 'completed' && isset($issue['assignee']['id']) && $issue['assignee']['id'] === $user['id'] && isset($issue['completedAt']);
            });
            $responseTimes = array_map(function($issue) {
                // Approximate as days since activity (Trello doesn't provide creation date)
                return Carbon::parse($issue['completedAt'])->diffInDays(Carbon::now());
            }, $completedIssues);
            $avgResponseTime = $responseTimes ? array_sum($responseTimes) / count($responseTimes) : 0;
            $responseScore = $avgResponseTime > 0 ? max(0, 100 - ($avgResponseTime * 10)) : 100; // Normalize (lower is better)

            // Communication: Number of boards user is active on (proxy)
            $activeBoards = count(array_unique(array_map(function($issue) {
                return $issue['team']['id'];
            }, array_filter($issues, function($issue) use ($user) {
                return isset($issue['assignee']['id']) && $issue['assignee']['id'] === $user['id'];
            }))));
            $communicationScore = min(100, $activeBoards * 20); // Normalize to 0-100

            // Team Collaboration: Number of boards user is a member of
            $teamBoards = count(array_filter($teams, function($team) use ($user) {
                return in_array($user['id'], array_column($team['members']['nodes'], 'id'));
            }));
            $collaborationScore = min(100, $teamBoards * 20); // Normalize to 0-100

            // Innovation: Number of cards assigned to user (proxy)
            $totalCards = count(array_filter($issues, function($issue) use ($user) {
                return isset($issue['assignee']['id']) && $issue['assignee']['id'] === $user['id'];
            }));
            $innovationScore = min(100, $totalCards * 10); // Normalize to 0-100

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
            'activeUsers' => count($users),
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
            $prompt .= "Total Cards: {$metrics['issueCount']}\n";
            $prompt .= "Completed Cards: {$metrics['completedIssues']}\n";
            $prompt .= "Active Boards: {$metrics['activeProjects']}\n";
            $prompt .= "Closed Boards: {$metrics['completedProjects']}\n";
            $prompt .= "Active Users: {$metrics['activeUsers']}\n";
            $prompt .= "Number of Boards: {$metrics['teamCount']}\n";
            $prompt .= "Average Board Progress: {$metrics['averageProjectProgress']}%\n\n";
            $prompt .= "Provide 2-3 concise paragraphs of professional analysis about team performance, board status, and overall progress. Focus on trends, efficiency, and areas worth noting. Write in a professional, data-driven style suitable for a management dashboard.";

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
            $prompt = "Based on these Trello metrics:\n";
            $prompt .= "Total Cards: {$metrics['issueCount']}\n";
            $prompt .= "Completed Cards: {$metrics['completedIssues']}\n";
            $prompt .= "Active Boards: {$metrics['activeProjects']}\n";
            $prompt .= "Closed Boards: {$metrics['completedProjects']}\n";
            $prompt .= "Active Users: {$metrics['activeUsers']}\n";
            $prompt .= "Number of Boards: {$metrics['teamCount']}\n";
            $prompt .= "Average Board Progress: {$metrics['averageProjectProgress']}%\n\n";
            $prompt .= "Generate 5 specific, actionable recommendations to improve team performance, board completion rates, and efficiency. Make them concise, practical, and directly related to the metrics above. Format your response as a simple list with each recommendation on a new line with no numbering.";

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
            "Your team has completed {$metrics['completedIssues']} out of {$metrics['issueCount']} cards, achieving a {$completionRate}% completion rate. This efficiency level is " . ($completionRate > 70 ? "strong and indicates effective task management" : "below target and suggests potential workflow issues") . ". With {$metrics['activeProjects']} active boards, resource allocation appears " . ($metrics['activeProjects'] > 5 ? "stretched" : "manageable") . ".",
            "Board progress metrics show an average completion rate of {$metrics['averageProjectProgress']}%, which is " . ($metrics['averageProjectProgress'] > 60 ? "on track with objectives" : "falling behind expected timelines") . ". The {$metrics['teamCount']} boards are coordinating multiple workstreams, with board utilization rates suggesting " . ($metrics['teamCount'] > 3 ? "effective collaboration structures" : "potential resource constraints") . ".",
            "User engagement metrics indicate that {$metrics['activeUsers']} active users are contributing to board advancement. The distribution of tasks across team members appears " . (($metrics['issueCount'] / max(1, $metrics['activeUsers'])) > 5 ? "heavy, with potential bottlenecks" : "well-balanced, promoting steady progress") . ". Focus should be maintained on " . ($completionRate < 70 ? "increasing completion rates" : "maintaining current momentum") . "."
        ];
    }

    private function getMockRecommendations($metrics)
    {
        $completion = $metrics['completedIssues'] / max(1, $metrics['issueCount']) * 100;
        $completionRate = round($completion);

        $recommendations = [
            "Implement weekly board reviews to improve the current {$completionRate}% card completion rate to a target of 85%.",
            "Redistribute workload across boards to optimize resource allocation for {$metrics['activeProjects']} active boards.",
            "Establish cross-board collaboration sessions to share best practices and increase overall task velocity.",
            "Focus on moving cards in 'To Do' and 'Doing' lists to 'Done' to raise average board progress above 70%.",
            "Create prioritized card lists for critical tasks to accelerate completion of high-priority deliverables."
        ];

        if ($completionRate < 60) {
            $recommendations[] = "Conduct board prioritization workshops to focus team efforts on high-impact tasks.";
        }

        if ($metrics['activeProjects'] > 5) {
            $recommendations[] = "Consider archiving low-priority boards until current completion rates improve.";
        }

        return array_slice($recommendations, 0, 5);
    }

    private function prepareExportData($issues, $projects, $users, $teams, $dates)
    {
        $completion = count($issues) > 0
            ? round((count(array_filter($issues, function($i) { return $i['state']['type'] === 'completed'; })) / count($issues)) * 100)
            : 0;

        $avgProgress = count($projects) > 0
            ? round(array_sum(array_column($projects, 'progress')) / count($projects))
            : 0;

        return [
            'headers' => [
                'Report Generated' => date('Y-m-d H:i:s'),
                'Period' => Carbon::parse($dates['start'])->format('M d, Y') . ' to ' . Carbon::parse($dates['end'])->format('M d, Y')
            ],
            'summary' => [
                ['Total Cards', count($issues)],
                ['Completed Cards', count(array_filter($issues, function($i) { return $i['state']['type'] === 'completed'; }))],
                ['Completion Rate', $completion . '%'],
                ['Active Boards', count(array_filter($projects, function($p) { return $p['completedAt'] === null; }))],
                ['Average Board Progress', $avgProgress . '%'],
                ['Active Boards (Teams)', count($teams)],
                ['Active Users', count($users)]
            ],
            'activities' => $this->getRecentActivities($issues, $users, $teams, $projects)
        ];
    }
}
?>