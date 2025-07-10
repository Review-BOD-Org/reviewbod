<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaskAnalyzerController extends Controller
{
    public function streamChat(Request $request)
    {
        $messages = $request->input('messages', []);
        $userId = "1231";
        $systemPrompts = $request->input('system_prompts', [
            "You are a helpful assistant with access to the user's personal knowledge base.",
            "When you need to find information from the user's notes, research, or documentation, use the search_knowledge_base function.",
            "Always respond in a friendly and professional tone.",
        ]);

        if (empty($messages)) {
            return response()->json(['message' => 'Messages required'], 422);
        }

        if (empty($userId)) {
            return response()->json(['message' => 'User ID required'], 422);
        }

        // Add system prompts
        foreach (array_reverse($systemPrompts) as $prompt) {
            array_unshift($messages, [
                'role' => 'system',
                'content' => $prompt,
            ]);
        }

        $apiKey = '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';

        // Define the function/tool that OpenAI can call
        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_knowledge_base',
                    'description' => 'Search the user\'s personal knowledge base including notes, research, and documentation',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'The search query to find relevant information'
                            ]
                        ],
                        'required' => ['query']
                    ]
                ]
            ]
        ];

        $response = new StreamedResponse(function () use ($messages, $apiKey, $tools, $userId) {
            $this->handleStreamingWithFunctions($messages, $apiKey, $tools, $userId);
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    private function handleStreamingWithFunctions($messages, $apiKey, $tools, $userId)
    {
        $conversationMessages = $messages;

        while (true) {
            $ch = curl_init();
            $responseData = '';
            $functionCall = null;

            curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) use (&$responseData, &$functionCall) {
                $responseData .= $data;
                
                // Parse streaming data to detect function calls
                $lines = explode("\n", $data);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (strpos($line, 'data: ') === 0) {
                        $json = substr($line, 6);
                        if ($json !== '[DONE]') {
                            $decoded = json_decode($json, true);
                            if (isset($decoded['choices'][0]['delta']['tool_calls'])) {
                                $functionCall = $decoded['choices'][0]['delta']['tool_calls'][0];
                                return strlen($data); // Stop processing for function call
                            }
                        }
                    }
                }

                // Only echo if it's not a function call
                if (!$functionCall) {
                    echo $data;
                    ob_flush();
                    flush();
                }
                
                return strlen($data);
            });

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer $apiKey",
            ]);

            $postData = json_encode([
                "model" => "gpt-4o-mini",
                "messages" => $conversationMessages,
                "tools" => $tools,
                "tool_choice" => "auto",
                "stream" => true,
            ]);

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

            curl_exec($ch);
            curl_close($ch);

            // If no function call was made, we're done
            if (!$functionCall) {
                break;
            }

            // Handle the function call
            $functionResult = $this->executeFunctionCall($functionCall, $userId);
            
            // Add the assistant's function call message
            $conversationMessages[] = [
                'role' => 'assistant',
                'tool_calls' => [[
                    'id' => $functionCall['id'] ?? 'call_' . uniqid(),
                    'type' => 'function',
                    'function' => [
                        'name' => $functionCall['function']['name'],
                        'arguments' => $functionCall['function']['arguments']
                    ]
                ]]
            ];

            // Add the function result
            $conversationMessages[] = [
                'role' => 'tool',
                'tool_call_id' => $functionCall['id'] ?? 'call_' . uniqid(),
                'content' => json_encode($functionResult)
            ];

            // Echo function call info to the stream
            echo "data: " . json_encode([
                'type' => 'function_call',
                'function' => $functionCall['function']['name'],
                'arguments' => json_decode($functionCall['function']['arguments'], true),
                'result' => $functionResult
            ]) . "\n\n";
            ob_flush();
            flush();

            // Continue the conversation with the function result
        }
    }

    private function executeFunctionCall($functionCall, $userId)
    {
        $functionName = $functionCall['function']['name'];
        $arguments = json_decode($functionCall['function']['arguments'], true);

        switch ($functionName) {
            case 'search_knowledge_base':
                return $this->searchKnowledgeBase($arguments['query'], $userId);
            default:
                return ['error' => 'Unknown function: ' . $functionName];
        }
    }

    private function searchKnowledgeBase($query, $userId)
    {
        try {
            $response = Http::timeout(10)->post('https://api.reviewbod.com/api/similarity-search', [
                'user_id' => $userId,
                'search' => $query,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Format the results for the AI
                if (empty($data['results'])) {
                    return [
                        'success' => true,
                        'message' => 'No relevant information found in knowledge base.',
                        'results' => []
                    ];
                }

                $formattedResults = [];
                foreach ($data['results'] as $result) {
                    $formattedResults[] = [
                        'content' => $result['content'],
                        'title' => $result['metadata']['note_title'] ?? 'Untitled',
                        'category' => $result['metadata']['note_category'] ?? 'general',
                        'platform' => $result['metadata']['platform'] ?? 'unknown',
                        'timestamp' => $result['metadata']['timestamp'] ?? null
                    ];
                }

                return [
                    'success' => true,
                    'message' => sprintf('Found %d relevant results', count($formattedResults)),
                    'results' => $formattedResults,
                    'total_results' => $data['total_results']
                ];
            }

            Log::warning('Knowledge base search failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'Search service unavailable',
                'message' => 'Unable to search knowledge base at this time.'
            ];

        } catch (\Exception $e) {
            Log::error('Knowledge base search error', [
                'error' => $e->getMessage(),
                'query' => $query,
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'error' => 'Search failed',
                'message' => 'An error occurred while searching the knowledge base.'
            ];
        }
    }

    // Alternative: Non-streaming version for easier testing
    public function chatWithFunctions(Request $request)
    {
        $messages = $request->input('messages', []);
        $userId = $request->input('user_id');
        
        if (empty($messages) || empty($userId)) {
            return response()->json(['message' => 'Messages and user_id required'], 422);
        }

        $apiKey = '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';

        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_knowledge_base',
                    'description' => 'Search the user\'s personal knowledge base',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Search query'
                            ]
                        ],
                        'required' => ['query']
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => $messages,
            'tools' => $tools,
            'tool_choice' => 'auto',
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'OpenAI API call failed'], 500);
        }

        $data = $response->json();
        $choice = $data['choices'][0];

        // Check if the AI wants to call a function
        if (isset($choice['message']['tool_calls'])) {
            $toolCall = $choice['message']['tool_calls'][0];
            $functionResult = $this->executeFunctionCall($toolCall, $userId);
            
            // Make another call with the function result
            $messages[] = $choice['message'];
            $messages[] = [
                'role' => 'tool',
                'tool_call_id' => $toolCall['id'],
                'content' => json_encode($functionResult)
            ];

            $secondResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
                'tools' => $tools,
                'tool_choice' => 'auto',
            ]);

            return $secondResponse->json();
        }

        return $data;
    }
}