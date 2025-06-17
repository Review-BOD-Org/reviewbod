<?php
namespace App\Http\Controllers;
use DB;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Cache;

class TrelloCalendarController extends Controller
{
    public function getCalendarData(Request $request)
    {
        $data = DB::table("linked")->where(["userid" => auth()->id(), "type" => "trello"])->first();
        $accessToken = $data->token ?? null;
        $userId = $request->trello_user_id ?? $request->linear_user_id ?? null;

        if (!$accessToken || !$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Trello access token or user ID not found'
            ], 400);
        }

        $userData = $this->getFullTrelloUserData($accessToken, $userId);

        if (!$userData['success']) {
            return response()->json($userData, 400);
        }

        $milestones = $this->getTrelloMilestones($accessToken);
        $cardActivity = $this->getCardActivity($accessToken, $userId);

        return response()->json([
            'success' => true,
            'user' => $userData['user'],
            'milestones' => $milestones['success'] ? $milestones['data'] : [],
            'activity' => $cardActivity['success'] ? $cardActivity['data'] : []
        ]);
    }

    public function getFullTrelloUserData($accessToken, $userId)
    {
        $response = Http::get("https://api.trello.com/1/members/{$userId}", [
            'key' => env('TRELLO_API_KEY', 'e39869487a72d56e6758bd57b67fca4f'),
            'token' => $accessToken,
            'boards' => 'all',
            'board_fields' => 'name,desc,idOrganization',
            'cards' => 'all',
            'card_fields' => 'name,desc,due,idBoard,idList,labels,closed,pos,shortLink,url,dateLastActivity'
        ]);

        if ($response->failed()) {
            return [
                'success' => false,
                'message' => 'Trello API request failed.',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        $data = $response->json();
        $allCards = array_unique($data['cards'] ?? [], SORT_REGULAR);
        // dd($data);
        $user = [
            'id' => $data['id'] ?? null,
            'name' => $data['fullName'] ?? 'Unknown',
            'displayName' => $data['username'] ?? 'Unknown',
            'email' => $data['email'] ?? null,
            'avatarUrl' => $data['avatarUrl'] ?? null,
            'createdAt' => $data['dateCreated'] ?? null,
            'updatedAt' => $data['dateLastActive'] ?? null,
            'lastSeen' => $data['dateLastActive'] ?? null,
            'timezone' => null,
            'active' => isset($data['activityBlocked']) ? !$data['activityBlocked'] : true,
            'teams' => [
                'nodes' => array_map(function ($board) {
                    return [
                        'id' => $board['id'] ?? null,
                        'name' => $board['name'] ?? 'Unknown',
                        'key' => $board['shortLink'] ?? null,
                        'color' => null,
                        'createdAt' => $board['dateCreated'] ?? null
                    ];
                }, $data['boards'] ?? [])
            ],
            'assignedIssues' => [
                'nodes' => array_map(function ($card) {
                    $createdAt = $card['dateLastActivity'] ?? Carbon::now()->toIso8601String();
                    return [
                        'id' => $card['id'] ?? null,
                        'title' => $card['name'] ?? 'Untitled',
                        'identifier' => $card['shortLink'] ?? $card['id'],
                        'description' => $card['desc'] ?? '',
                        'state' => [
                            'name' => $card['closed'] ? 'Archived' : 'Active',
                            'type' => $card['closed'] ? 'completed' : 'started'
                        ],
                        'priority' => $card['pos'] ? min((int) ($card['pos'] / 10000), 4) : 0,
                        'url' => $card['url'] ?? "https://trello.com/c/{$card['shortLink']}",
                        'dueDate' => $card['due'] ?? null,
                        'createdAt' => $createdAt,
                        'children' => ['nodes' => []],
                        'project' => [
                            'id' => $card['idBoard'] ?? null,
                            'name' => $card['board']['name'] ?? 'Unknown'
                        ]
                    ];
                }, $allCards)
            ]
        ];

        return [
            'success' => true,
            'user' => $user
        ];
    }

    public function getTrelloMilestones($accessToken)
    {
        $response = Http::get('https://api.trello.com/1/search', [
            'key' => env('TRELLO_API_KEY', 'e39869487a72d56e6758bd57b67fca4f'),
            'token' => $accessToken,
            'query' => 'due:month',
            'card_fields' => 'name,desc,due,idBoard,closed',
            'modelTypes' => 'cards',
            'partial' => true
        ]);

        if ($response->failed()) {
            return [
                'success' => false,
                'message' => 'Trello API request failed.',
                'status' => $response->status(),
            ];
        }

        $data = $response->json();

        $milestones = array_map(function ($card) {
            return [
                'id' => $card['id'] ?? null,
                'name' => $card['name'] ?? 'Untitled',
                'description' => $card['desc'] ?? '',
                'targetDate' => $card['due'] ?? null,
                'state' => $card['closed'] ? 'completed' : 'active',
                'projects' => [
                    'nodes' => [
                        [
                            'id' => $card['idBoard'] ?? null,
                            'name' => $card['board']['name'] ?? 'Unknown'
                        ]
                    ]
                ]
            ];
        }, $data['cards'] ?? []);

        return [
            'success' => true,
            'data' => $milestones
        ];
    }

    public function getCardActivity($accessToken, $userId)
    {
        $response = Http::get("https://api.trello.com/1/members/{$userId}/actions", [
            'key' => env('TRELLO_API_KEY', 'e39869487a72d56e6758bd57b67fca4f'),
            'token' => $accessToken,
            'filter' => 'all',
            'limit' => 50
        ]);

        if ($response->failed()) {
            return [
                'success' => false,
                'message' => 'Trello API request failed.',
                'status' => $response->status(),
            ];
        }

        $data = $response->json();

        $activities = array_map(function ($action) {
            return [
                'id' => $action['id'] ?? null,
                'createdAt' => $action['date'] ?? null,
                'card' => [
                    'id' => $action['data']['card']['id'] ?? null,
                    'title' => $action['data']['card']['name'] ?? 'Unknown',
                    'identifier' => $action['data']['card']['shortLink'] ?? null
                ],
                'fromState' => [
                    'id' => $action['data']['old']['idList'] ?? null,
                    'name' => $action['data']['listBefore']['name'] ?? null
                ],
                'toState' => [
                    'id' => $action['data']['list']['id'] ?? null,
                    'name' => $action['data']['list']['name'] ?? null
                ]
            ];
        }, $data);

        return [
            'success' => true,
            'data' => $activities
        ];
    }

    public function getDashboardData(Request $request)
    {
        $trelloData = DB::table("linked")->where([
            "userid" => auth()->id(),
            "type" => "trello"
        ])->first();

        if (!$trelloData) {
            return response()->json([
                'success' => false,
                'message' => 'Trello account not linked.'
            ], 400);
        }

        $accessToken = $trelloData->token;
        $userId = $request->id ?? $request->trello_user_id ?? $request->user_id;

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Trello user ID not provided.'
            ], 400);
        }

        $timeRange = $request->input('time_range', 'all_time');
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
            case 'all_time':
                $startDate = Carbon::createFromTimestamp(0); // Epoch start
                break;
            default:
                $startDate = Carbon::createFromTimestamp(0);
        }

        $analyticsData = $this->fetchTrelloAnalytics($accessToken, $userId, $startDate, $endDate);

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
            'userRetention' => $analyticsData['userRetention']
        ]);
    }

    private function fetchTrelloAnalytics($accessToken, $userId, $startDate, $endDate)
    {
        $completedTasks = $this->getCompletedTasks($accessToken, $userId, $startDate, $endDate);
        $dailyStats = $this->getDailyStats($accessToken, $userId, $startDate, $endDate);
        $userRetention = $this->getUserRetention($accessToken, $userId);

        return [
            'success' => true,
            'tasksCompleted' => $completedTasks,
            'dailyStats' => $dailyStats,
            'userRetention' => $userRetention
        ];
    }

    private function getCompletedTasks($accessToken, $userId, $startDate, $endDate)
    {
        // Ensure dates are Carbon instances
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);
        
        // Add a day to end date to ensure we include the full end date
        $queryEndDate = (clone $endDate)->addDay();
        
        // Initialize empty data structure in case we need to return early
        $emptyData = $this->getEmptyTasksData();
        
        try {
            // Set up pagination parameters
            $limit = 1000; // Maximum allowed by Trello API
            $before = null;
            $allCards = [];
            $hasMoreData = true;
            
            // Use a cache key for this specific query
            $cacheKey = "trello_tasks_{$userId}_" . $startDate->timestamp . "_" . $endDate->timestamp;
            $cacheTtl = 3600; // Cache for 1 hour
            
            // Try to get data from cache first
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            
            // Fetch data in batches to handle large datasets
            while ($hasMoreData) {
                $params = [
                    'key' => config('services.trello.key'), // Use config instead of env directly
                    'token' => $accessToken,
                    'filter' => 'closed',
                    'fields' => 'name,dateLastActivity,idBoard',
                    'since' => $startDate->toIso8601String(),
                    'before' => $queryEndDate->toIso8601String(),
                    'limit' => $limit,
                    'boards' => true,
                    'board_fields' => 'name'
                ];
                
                // Add before parameter for pagination if we have it
                if ($before) {
                    $params['before'] = $before;
                }
                
                // Make request with timeout and retry logic
                $response = Http::timeout(30)
                    ->retry(3, 1000)
                    ->get("https://api.trello.com/1/members/{$userId}/cards", $params);
            
                if ($response->failed()) {
                    \Log::error('Trello API request failed', [
                        'status' => $response->status(),
                        'response' => $response->body(),
                        'user' => $userId
                    ]);
                    return $emptyData;
                }
                
                $cards = $response->json();
                
                if (!is_array($cards)) {
                    \Log::error('Invalid response from Trello API', [
                        'response' => $response->body(),
                        'user' => $userId
                    ]);
                    return $emptyData;
                }
                
                // If we got fewer cards than the limit, we've reached the end
                if (count($cards) < $limit) {
                    $hasMoreData = false;
                } else {
                    // Get the last activity date of the last card for pagination
                    $lastCard = end($cards);
                    $before = $lastCard['dateLastActivity'];
                }
                
                // Append these cards to our collection
                $allCards = array_merge($allCards, $cards);
                
                // Safety check: if we've already collected too many cards, stop to avoid memory issues
                if (count($allCards) > 100000) {
                    \Log::warning('Trello API returned too many cards, limiting results', [
                        'user' => $userId,
                        'card_count' => count($allCards)
                    ]);
                    $hasMoreData = false;
                }
            }
            
            // Fetch creation dates in a separate, optimized batch
            // This is an optimization point since we don't need to fetch actions for all cards at once
            $cardCreationDates = $this->fetchCardCreationDates($accessToken, $userId, $allCards);
            
            // Process the results
            $totalTasks = 0;
            $byDay = [];
            $byProject = [];
            $taskDurations = [];
        
            foreach ($allCards as $card) {
                // Skip cards without completion dates
                if (!isset($card['dateLastActivity'])) {
                    continue;
                }
                
                $completedDate = Carbon::parse($card['dateLastActivity']);
                
                // Only count cards completed within our date range
                if ($completedDate < $startDate || $completedDate > $endDate) {
                    continue;
                }
                
                $totalTasks++;
                
                // Use completion date for daily stats
                $day = $completedDate->format('Y-m-d');
                $byDay[$day] = ($byDay[$day] ?? 0) + 1;
                
                // Get proper board name
                $projectName = $card['board']['name'] ?? 'Unassigned';
                $byProject[$projectName] = ($byProject[$projectName] ?? 0) + 1;
        
                // Calculate task duration if we have the creation date
                $cardId = $card['id'];
                if (isset($cardCreationDates[$cardId])) {
                    $createdDate = $cardCreationDates[$cardId];
                    
                    if ($completedDate > $createdDate) {
                        // Only count reasonable durations (avoid negative or extremely large values)
                        $durationHours = $createdDate->diffInHours($completedDate);
                        if ($durationHours >= 0 && $durationHours < 8760) { // Max 1 year
                            $taskDurations[] = $durationHours;
                        }
                    }
                }
            }
        
            // Format daily data with zeros for days with no tasks
            $byDayFormatted = [];
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $dayKey = $currentDate->format('Y-m-d');
                $byDayFormatted[] = [
                    'date' => $currentDate->format('M d'),
                    'tasks' => $byDay[$dayKey] ?? 0,
                    'subtasks' => 0
                ];
                $currentDate->addDay();
            }
        
            // Calculate average duration
            $avgTaskDuration = count($taskDurations) > 0 
                ? round(array_sum($taskDurations) / count($taskDurations), 1) . ' hours' 
                : 'N/A';
        
            $result = [
                'totalTasks' => $totalTasks,
                'totalSubtasks' => 0, // Trello has no native subtasks
                'avgTaskDuration' => $avgTaskDuration,
                'avgSubtaskDuration' => 'N/A',
                'byDay' => $byDayFormatted,
                'byProject' => array_map(function($count) {
                    return ['count' => $count];
                }, $byProject)
            ];
            
            // Cache the results
            Cache::put($cacheKey, $result, $cacheTtl);
            
            return $result;
        } catch (\Exception $e) {
            \Log::error('Error fetching Trello tasks', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => $userId
            ]);
            return $emptyData;
        }
    }
    
    /**
     * Fetch creation dates for cards in batches to optimize API calls
     */
    private function fetchCardCreationDates($accessToken, $userId, $cards)
    {
        $creationDates = [];
        $batchSize = 100; // Process cards in smaller batches
        $cardBatches = array_chunk($cards, $batchSize);
        
        foreach ($cardBatches as $batch) {
            $cardIds = array_column($batch, 'id');
            
            // Skip empty batches
            if (empty($cardIds)) {
                continue;
            }
            
            // Create a comma-separated list of card IDs
            $cardIdList = implode(',', $cardIds);
            
            try {
                $response = Http::timeout(30)
                    ->retry(3, 1000)
                    ->get("https://api.trello.com/1/batch", [
                        'key' => config('services.trello.key'),
                        'token' => $accessToken,
                        'urls' => "/cards/{$cardIdList}/actions?filter=createCard&fields=date"
                    ]);
                    
                if ($response->successful()) {
                    $batchResults = $response->json();
                    
                    foreach ($batchResults as $index => $result) {
                        // Skip failed requests
                        if ($result['200'] ?? false) {
                            $actions = $result['200'];
                            $cardId = $cardIds[$index] ?? null;
                            
                            if ($cardId && !empty($actions)) {
                                foreach ($actions as $action) {
                                    if ($action['type'] === 'createCard') {
                                        $creationDates[$cardId] = Carbon::parse($action['date']);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Error fetching card creation dates', [
                    'message' => $e->getMessage(),
                    'user' => $userId
                ]);
                // Continue processing other batches
            }
            
            // Add a small delay to avoid rate limiting
            usleep(100000); // 100ms
        }
        
        return $creationDates;
    }
    
    /**
     * Return empty data structure when API call fails
     */
    private function getEmptyTasksData()
    {
        return [
            'totalTasks' => 0,
            'totalSubtasks' => 0,
            'avgTaskDuration' => 'N/A',
            'avgSubtaskDuration' => 'N/A',
            'byDay' => [],
            'byProject' => []
        ];
    }

    private function getDailyStats($accessToken, $userId, $startDate, $endDate)
    {
        $response = Http::get("https://api.trello.com/1/members/{$userId}/cards", [
            'key' => env('TRELLO_API_KEY', 'e39869487a72d56e6758bd57b67fca4f'),
            'token' => $accessToken,
            'filter' => 'closed',
            'fields' => 'dateLastActivity',
            'since' => $startDate->toIso8601String(),
            'before' => $endDate->toIso8601String()
        ]);

        if ($response->failed()) {
            return [];
        }

        $cards = $response->json();
        $dailyStats = [];
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            $day = $currentDate->format('Y-m-d');
            $dayFormatted = $currentDate->format('M d');

            $dayTasks = 0;
            foreach ($cards as $card) {
                if (Carbon::parse($card['dateLastActivity'] ?? now())->format('Y-m-d') === $day) {
                    $dayTasks++;
                }
            }

            $dailyStats[] = [
                'date' => $dayFormatted,
                'tasks' => $dayTasks,
                'subtasks' => 0
            ];

            $currentDate->addDay();
        }

        return $dailyStats;
    }

    private function getUserRetention($accessToken, $userId)
    {
        $response = Http::get("https://api.trello.com/1/members/{$userId}", [
            'key' => env('TRELLO_API_KEY', 'e39869487a72d56e6758bd57b67fca4f'),
            'token' => $accessToken,
            'fields' => 'dateCreated,dateLastActive'
        ]);

        if ($response->failed()) {
            return [
                [
                    'cohort' => 'Unknown',
                    'userType' => 'Unknown',
                    'avgSessions' => '0',
                    'status' => 'Unknown'
                ]
            ];
        }

        function getTrelloDateFromId($id) {
            $timestampHex = substr($id, 0, 8);
            $timestamp = hexdec($timestampHex);
            return date('Y-m-d H:i:s', $timestamp);
        }

        $user = $response->json();
        $user['dateCreated'] = getTrelloDateFromId($userId);
        $createdAt = Carbon::parse($user['dateCreated'] ?? now());
        $now = Carbon::now();
        $monthsActive = $createdAt->diffInMonths($now);
        // dd($monthsActive);
        $cohort = '';
        if ($monthsActive <= 1) {
            $cohort = 'New Users';
        } elseif ($monthsActive <= 3) {
            $cohort = 'Recent Users';
        } elseif ($monthsActive <= 6) {
            $cohort = 'Established Users';
        } else {
            $cohort = 'Long-term Users';
        }
     

        $userType = 'Standard';
        $avgSessions = ($monthsActive > 0) ? '-' : 'N/A';

        $lastSeen = $user['dateLastActive'] ? Carbon::parse($user['dateLastActive']) : null;
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