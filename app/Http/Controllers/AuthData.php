<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Http;
use DB;
use Auth;

use Cache;
use Socialite;

use League\OAuth1\Client\Server\Trello;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Str;
use Illuminate\Support\Facades\Crypt;


class AuthData extends Controller
{
    //


    public function redirectToLinear()
    {
        $query = http_build_query([
            'client_id'     => env('LINEAR_CLIENT_ID'),
            'redirect_uri'  => env('LINEAR_REDIRECT_URI'),
            'response_type' => 'code',
            'scope'         => 'read,write,admin', // Changed from 'read' to 'read,write'
            'state'         => csrf_token(),
        ]);
    
        return redirect("https://linear.app/oauth/authorize?$query");
    }
public function registerLinearWebhook($userId) {
    $tokenRecord = DB::table('linked')
        ->where(['userid' => $userId, 'type' => 'linear'])
        ->first();

    if (!$tokenRecord) {
        return ['error' => 'Linear token not found for user'];
    }

    $token = $tokenRecord->token;
    $encryptedUserId = Crypt::encryptString($userId);

    $callbackUrl = url("/api/callback?platform_type=linear&user={$encryptedUserId}");

    $mutation = <<<GQL
    mutation {
      webhookCreate(input: {
        url: "$callbackUrl",
        allPublicTeams: true,
        resourceTypes: [
          "Issue", 
          "Customer", 
          "Cycle",
          "Initiative",
          "Comment"
        ]
      }) {
        success
        webhook {
          id
          enabled
          url
        }
      }
    }
    GQL;

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json',
    ])->post('https://api.linear.app/graphql', [
        'query' => $mutation,
    ]);
// dd($response->json());
    return $response->json();
}



public function handleLinearCallback(Request $request)
{

    if(!Auth::check()){
        return redirect("/auth/login");
    }

    // dd($res);
      
    if ($request->has('error')) {
        return response()->json(['message' => 'Authorization failed'], 403);
    }

    $response = Http::asForm()->post('https://api.linear.app/oauth/token', [
        'client_id'     => env('LINEAR_CLIENT_ID'),
        'client_secret' => env('LINEAR_CLIENT_SECRET'),
        'redirect_uri'  => env('LINEAR_REDIRECT_URI'),
        'code'          => $request->code,
        'grant_type'    => 'authorization_code',
    ]);

    if ($response->failed()) {
        return redirect("/auth/login");
    }

    $tokenData = $response->json();

    $linked = DB::table("linked")->where(["userid"=>Auth::id(),"type"=>"linear"])->exists();
    if(!$linked){
        DB::table("linked")->insert(["userid"=>Auth::id(),"type"=>"linear","token"=>$tokenData['access_token'],"json"=>json_encode($tokenData)]);

    }else{
        DB::table("linked")->where(["userid"=>Auth::id(),"type"=>"linear"])->update(["token"=>$tokenData['access_token'],"json"=>json_encode($tokenData)]);
    }

    DB::table("users")->where(["id"=>Auth::user()->id])->update([
        'service' => "linear"
    ]);
        $res = new FetchData();
      
    $res = $res->StoreData("linear");
   
  $this->registerLinearWebhook(Auth::id());
    //  dd($res);
  if(!$res){
    //    dd($res);
  }
    // Store the access token in session or database
    // session(['linear_access_token' => $tokenData['access_token']]);

    return redirect("/auth/pricing");
}


public function optimize()
{

    $channelId = 'C08KPL2QFMM'; // Replace with the actual channel ID

    $response = Http::withToken('xoxp-8672873888886-8697069936225-8708487347042-0fc2c97102ac7f69ae2d67ff3bc0573c')
        ->get('https://slack.com/api/conversations.history', [
            'channel' => $channelId, // Specify the channel ID
            'limit' => 50, // Get up to 50 messages
        ]);
    
    $data = $response->json();
    
    if ($data['ok']) {
        return $data['messages']; // Returns the list of messages
    } else {
        return $data; // Return the error response from Slack
    }

    return;
    $response = Http::withToken('xoxp-8681766382996-8681766383396-8664151772647-ea863a1bf30b04a73c95b77c712612ba')->get('https://slack.com/api/conversations.list', [
        'limit' => 100, // Fetch up to 100 channels at once
    ]);

    $data = $response->json();

    if ($data['ok']) {
        return $data['channels']; // Return channel list
    }

    return ['error' => $data['error']]; // Handle API errors

    $response = Http::withToken('xoxp-8681766382996-8681766383396-8664151772647-ea863a1bf30b04a73c95b77c712612ba')->get('https://slack.com/api/conversations.history', [
        'channel' => $channelId,
        'limit' => 10, // Get the last 10 messages
    ]);

    $data = $response->json();

    if ($data['ok']) {
        return $data['messages']; // Return message list
    }
    return;
    $linearData = $this->getLinearUser()->getData(true); // Fetch the data

    if (isset($linearData['message'])) {
        return response()->json(['message' => 'Failed to retrieve Linear data'], 400);
    }

    $prompt = "i want to design a dashbooard that analyze data from linear and co even on slack.. list only what i need  on the dashboard";

    $openaiResponse = Http::withHeaders([
        'Authorization' => 'Bearer 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',
        'Content-Type'  => 'application/json',
    ])->post('https://api.openai.com/v1/chat/completions', [
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'system', 'content' => 'You are an expert at analyzing project management data. here'.json_encode($linearData, JSON_PRETTY_PRINT)],
            ['role' => 'user', 'content' => $prompt],
        ],
        'max_tokens' => 500,
    ]);

    return response()->json($openaiResponse->json());
}

public function getLinearUser()
{
    $token = "lin_oauth_d0504ff6e23ebf55e3541fdde07788e96dfbfd3e0e3d6c44ed355b8f3046b221";

    if (!$token) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $query = '
    query {
        users(first: 50) {
            nodes {
                id
                name
                email
                teams(first: 5) {
                    nodes {
                        id
                        name
                    }
                }
                assignedIssues(first: 5) {
                    nodes {
                        id
                        title
                        state {
                            name
                        }
                        project {
                            id
                            name
                        }
                    }
                }
            }
        }
        teams(first: 50) {
            nodes {
                id
                name
            }
        }
        projects(first: 50) {
            nodes {
                id
                name
            }
        }
    }
    ';

    $response = Http::withToken($token)->post('https://api.linear.app/graphql', [
        'query' => $query
    ]);

    $data = $response->json();

    if (isset($data['errors'])) {
        return response()->json(['message' => 'GraphQL error', 'errors' => $data['errors']], 400);
    }

    // Restructuring the response
    $users = [];
    $teams = [];
    $projects = [];

    foreach ($data['data']['users']['nodes'] as $user) {
        $users[] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'teams' => array_map(fn($team) => ['id' => $team['id'], 'name' => $team['name']], $user['teams']['nodes']),
            'assigned_issues' => array_map(fn($issue) => [
                'id' => $issue['id'],
                'title' => $issue['title'],
                'state' => $issue['state']['name'],
                'project' => $issue['project'] ? ['id' => $issue['project']['id'], 'name' => $issue['project']['name']] : null,
            ], $user['assignedIssues']['nodes'])
        ];
    }

    foreach ($data['data']['teams']['nodes'] as $team) {
        $teams[] = [
            'id' => $team['id'],
            'name' => $team['name'],
        ];
    }

    foreach ($data['data']['projects']['nodes'] as $project) {
        $projects[] = [
            'id' => $project['id'],
            'name' => $project['name'],
        ];
    }

    return response()->json([
        'users' => $users,
        'teams' => $teams,
        'projects' => $projects,
    ]);
}

 

public function getSubIssues($issueId = "38d32ddc-7061-4743-a1c1-755f082a0729")
{
    $token = "lin_oauth_d0504ff6e23ebf55e3541fdde07788e96dfbfd3e0e3d6c44ed355b8f3046b221";

    if (!$token) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $query = '
    query($issueId: String!) {
        issue(id: $issueId) {
            id
            title
            children(first: 10) {  # Fetch sub-issues (children)
                nodes {
                    id
                    title
                    state {
                        name
                    }
                    assignee {
                        id
                        name
                    }
                }
            }
        }
    }
    ';

    $variables = ['issueId' => $issueId];

    $response = Http::withToken($token)->post('https://api.linear.app/graphql', [
        'query' => $query,
        'variables' => $variables
    ]);

    $data = $response->json();

    if (isset($data['errors'])) {
        return response()->json(['message' => 'GraphQL error', 'errors' => $data['errors']], 400);
    }

    // Restructure response
    $issue = $data['data']['issue'];

    $formattedIssue = [
        'id' => $issue['id'],
        'title' => $issue['title'],
        'sub_issues' => array_map(fn($subIssue) => [
            'id' => $subIssue['id'],
            'title' => $subIssue['title'],
            'state' => $subIssue['state']['name'],
            'assignee' => $subIssue['assignee'] ? [
                'id' => $subIssue['assignee']['id'],
                'name' => $subIssue['assignee']['name']
            ] : null
        ], $issue['children']['nodes'])
    ];

    return response()->json($formattedIssue);
}



public function slack_auth() {
    $scopes = [
        'chat:write',       // Send messages
        'channels:read',    // Read public channels
        'channels:join',    // Join public channels
        'groups:read',      // Read private channels
        'commands',         // Enable slash commands
    ];
    
    $query = http_build_query([
        'client_id' => config('services.slack.client_id'),
        'scope' => implode(',', $scopes),
        'redirect_uri' => config('services.slack.redirect'),
        'user_scope' => '', // No user scopes
    ]);

    return redirect("https://slack.com/oauth/v2/authorize?$query");
}


public function slack_callback(Request $request){
    try {
        $response = Http::asForm()->post('https://slack.com/api/oauth.v2.access', [
            'client_id' => config('services.slack.client_id'),
            'client_secret' => config('services.slack.client_secret'),
            'code' => $request->code,
            'redirect_uri' => config('services.slack.redirect'),
        ]);

        $data = $response->json();

        if (!data_get($data, 'ok')) {
            return response()->json(['error' => $data['error']], 400);
        }

        $botToken = $data['access_token']; // xoxb-...
        $teamId = $data['team']['id'] ?? null;
        $botUserId = $data['bot_user_id'] ?? null;

        // Save or update link
        $linked = DB::table("linked")->where(["userid"=>Auth::id(),"type"=>"slack"])->exists();
        if(!$linked){
            DB::table("linked")->insert([
                "userid"=>Auth::id(),
                "type"=>"slack",
                "token"=>$botToken,
                "bot"=>$botUserId,
                "json"=>json_encode($data)
            ]);
        }else{
            DB::table("linked")->where(["userid"=>Auth::id(),"type"=>"slack"])->update([
                "token"=>$botToken,
                "bot"=>$botUserId,
                "json"=>json_encode($data)
            ]);
        }

        // ✅ Auto-join all public channels
        $channelsResponse = Http::withToken($botToken)->get('https://slack.com/api/conversations.list', [
            'types' => 'public_channel',
            'limit' => 1000
        ]);

        $channels = $channelsResponse->json();
        if (data_get($channels, 'ok')) {
            foreach ($channels['channels'] as $channel) {
                Http::withToken($botToken)->post('https://slack.com/api/conversations.join', [
                    'channel' => $channel['id'],
                ]);
            }
        }

        return redirect()->route('dashboard');
    } catch (\Exception $e) {
        return redirect('/auth/choose')->with('error', 'Something went wrong!');
    }
}


public function showChannelList()
{
    $slack = DB::table("linked")->where(["type"=>"slack","userid"=>Auth::id()])->first();
    if (!$slack) {
        return redirect('/slack/auth')->with('error', 'Slack account not linked.');
    }
    $token = $slack->token; // Get token from session after OAuth
    $userId = Auth::id();
    
    // Get channels with caching
    $channelsCacheKey = 'slack_channels_' . $userId;
    $channels = Cache::remember($channelsCacheKey, 60, function () use ($token) {
        $response = Http::withToken($token)
            ->get('https://slack.com/api/conversations.list', [
                'limit' => 100,
                'exclude_archived' => true,
            ]);
        
        $data = $response->json();
        
        if ($data['ok']) {
            // Return only the necessary channel info to reduce memory usage
            return collect($data['channels'])->map(function ($channel) {
                return [
                    'id' => $channel['id'],
                    'name' => $channel['name'],
                    'topic' => $channel['topic']['value'] ?? '',
                    'member_count' => $channel['num_members'] ?? 0
                ];
            })->toArray();
        }
        
        return [];
    });

    $id = $channels[0]['id'] ?? null;
    $url = "https://slack.com/api/chat.postMessage";

    $response = Http::withToken($token)->post($url, [
        'channel' => $id,
        'text' => "test",
    ]);


    $botToken = $token; // pass via POST or hidden form
    $channel = $id; // e.g., C12345678

    $response = Http::withToken($botToken)->post('https://slack.com/api/chat.postMessage', [
        'channel' => $channel,
        'text' => 'This ia reviewbod AI !',
    ]);

    // dd($response->json());
    
    // Return view with channels for selection
    return view('auth.channel_list', ['channels' => $channels]);
}


// Controller method (in your controller)
public function saveSelectedChannel(Request $request)
{
    $request->validate([
        'channel' => 'required|string'
    ]);
    
    try {
        DB::table('linked')->where(["userid" => Auth::id(),"type"=>"slack"])->update([
            "slack_channel" => $request->channel
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Channel linked successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error linking channel: ' . $e->getMessage()
        ], 500);
    }
}

public function redirectToTrello() {
    $server = new Trello([
        'identifier'    => 'e39869487a72d56e6758bd57b67fca4f',
        'secret'        => '8c42ae7ad0587f23a578ee71ceec3d6df4f887cb3867169ef1661361400d01fe',
        'callback_uri'  => 'https://reviewbod.com/trello/callback',
    ]);

    // Step 1: Get temporary credentials
    $tempCredentials = $server->getTemporaryCredentials();
    
    // Store temp credentials in session
    session(['oauth.temp_credentials' => serialize($tempCredentials)]);
    
    // Generate authorization URL with explicit expiration=never
    $authUrl = $server->getAuthorizationUrl($tempCredentials);
    
    // Parse the URL to ensure no duplicate expiration parameters
    $parsedUrl = parse_url($authUrl);
    parse_str($parsedUrl['query'] ?? '', $queryParams);
    
    // Set expiration to never and remove any conflicting expiration
    $queryParams['expiration'] = 'never';
    
    // Rebuild the URL
    $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];
    $authUrl = $baseUrl . '?' . http_build_query($queryParams);
    
    return redirect($authUrl);
}

 

public function handleTrelloCallback(Request $request)
{
    $server = new Trello([
        'identifier' => 'e39869487a72d56e6758bd57b67fca4f',
        'secret' => '8c42ae7ad0587f23a578ee71ceec3d6df4f887cb3867169ef1661361400d01fe',
        'callback_uri' => 'https://reviewbod.com/trello/callback',
    ]);

    // Get stored temp credentials from session
    $tempCredentials = session('oauth.temp_credentials');

    if (!$tempCredentials) {
        return response()->json(['message' => 'Missing temporary credentials'], 400);
    }
    
    $tempCredentials = unserialize($tempCredentials); // 🛠 Deserialize it here
    

    // Step 2: Exchange for token
    $tokenCredentials = $server->getTokenCredentials(
        $tempCredentials,
        $request->query('oauth_token'),
        $request->query('oauth_verifier')
    );

    // Step 3: You can fetch user details if needed
    $user = $server->getUserDetails($tokenCredentials);
    // dd($user);
    // Store token like you do for "linear"
    $tokenData = [
        'access_token' => $tokenCredentials->getIdentifier(),
        'access_secret' => $tokenCredentials->getSecret(),
        'user_nickname' => $user->nickname,
        'user_uid' => $user->uid,
    ];

    $linked = DB::table("linked")->where([
        "userid" => Auth::id(),
        "type" => "trello"
    ])->exists();

    if (!$linked) {
        DB::table("linked")->insert([
            "userid" => Auth::id(),
            "type" => "trello",
            "token" => $tokenData['access_token'],
            "json" => json_encode($tokenData),
        ]);
    } else {
        DB::table("linked")->where([
            "userid" => Auth::id(),
            "type" => "trello"
        ])->update([
            "token" => $tokenData['access_token'],
            "json" => json_encode($tokenData),
        ]);
    }

    DB::table("users")->where("id", Auth::id())->update([
        'service' => 'trello'
    ]);

    Cache::flush();         
    return redirect('/auth/pricing')->with('success', 'Trello connected successfully.');
}


public function get_data(){ 

    $token = DB::table("linked")->where(["userid"=>Auth::id(),"type"=>"linear"])->value("token");
// dd($token);
    if (!$token) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $query = '
    query {
        users(first: 50) {
            nodes {
                id
                name
                email
                teams(first: 5) {
                    nodes {
                        id
                        name
                    }
                }
                assignedIssues(first: 5) {
                    nodes {
                        id
                        title
                        state {
                            name
                        }
                        project {
                            id
                            name
                        }
                    }
                }
            }
        }
      teams(first: 50) {
        nodes {
            id
            name
            issues(first: 5) {
            nodes {
                id
                title
                state {
                name
                }
                project {
                id
                name
                }
                children {
                nodes {
                    id
                    title
                    state {
                    name
                    }
                    assignee {
                    id
                    name
                    }
                }
                }
            }
            }
        }
        }

        projects(first: 50) {
            nodes {
                id
                name
            }
        }
    }
    ';

    $response = Http::withToken($token)->post('https://api.linear.app/graphql', [
        'query' => $query
    ]);

    $data = $response->json();
    return $data;
}



public function streamChat(Request $request)
{
    $messages = $request->input('messages', []);
    $systemPrompts = $request->input('system_prompts', [
        "You are a helpful assistant.",
        "Answer concisely and professionally.",
        "Always respond in a friendly tone.",
    ]);

    if (empty($messages)) {
        return response()->json(['message' => 'Messages required'], 422);
    }

    $functions = [
        [
            'name' => 'callSimilaritySearch',
            'description' => 'Calls similarity search API based on user query',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'user_id' => ['type' => 'string', 'description' => 'User ID'],
                    'search' => ['type' => 'string', 'description' => 'Search query'],
                ],
                'required' => ['user_id', 'search'],
            ],
        ],
    ];

    foreach (array_reverse($systemPrompts) as $prompt) {
        array_unshift($messages, ['role' => 'system', 'content' => $prompt]);
    }

    $apiKey = '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';

    $response = new StreamedResponse(function () use ($messages, $functions, $apiKey) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);

        $functionCallBuffer = '';
        $collectingFunctionCall = false;

        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) use (&$functionCallBuffer, &$collectingFunctionCall) {
            echo $data;
            ob_flush();
            flush();

            $lines = explode("\n", $data);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!Str::startsWith($line, 'data:')) continue;

                $payload = trim(substr($line, 5));
                if ($payload === '[DONE]') break;

                $json = json_decode($payload, true);
                if (!$json) continue;

                $delta = $json['choices'][0]['delta'] ?? [];

                // If function_call starts
                if (isset($delta['function_call'])) {
                    $collectingFunctionCall = true;

                    if (isset($delta['function_call']['name'])) {
                        $functionCallBuffer = ''; // Reset buffer
                    }

                    if (isset($delta['function_call']['arguments'])) {
                        $functionCallBuffer .= $delta['function_call']['arguments'];
                    }
                }

                // Function call complete
                if (($json['choices'][0]['finish_reason'] ?? '') === 'function_call' && $collectingFunctionCall) {
                    $collectingFunctionCall = false;

                    $functionName = $json['choices'][0]['delta']['function_call']['name'] ?? 'callSimilaritySearch';
                    $args = json_decode($functionCallBuffer, true);

                    if (json_last_error() === JSON_ERROR_NONE && $functionName === 'callSimilaritySearch') {
                        $result = app()->call([$this, 'callSimilaritySearch'], $args);

                        echo "data: " . json_encode([
                            'id' => Str::uuid()->toString(),
                            'object' => 'chat.completion.chunk',
                            'choices' => [
                                [
                                    'delta' => [
                                        'content' => "\n\n" . json_encode($result, JSON_PRETTY_PRINT),
                                    ],
                                    'index' => 0,
                                ]
                            ]
                        ]) . "\n\n";
                        ob_flush();
                        flush();
                    }
                }
            }

            return strlen($data);
        });

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json",
        ]);

        $postData = json_encode([
            "model" => "gpt-4o-mini",
            "messages" => $messages,
            "stream" => true,
            "functions" => $functions,
            "function_call" => "auto",
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_exec($ch);
        curl_close($ch);
    });

    $response->headers->set('Content-Type', 'text/event-stream');
    $response->headers->set('Cache-Control', 'no-cache');
    $response->headers->set('Connection', 'keep-alive');

    return $response;
}



public function callSimilaritySearch($user_id, $search)
{
    $client = new \GuzzleHttp\Client();

    $response = $client->post('https://api.reviewbod.com/api/similarity-search', [
        'json' => [
            'user_id' => $user_id,
            'search' => $search,
        ]
    ]);

    return json_decode($response->getBody()->getContents(), true);
}


}
