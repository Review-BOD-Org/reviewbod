<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Http;
use Log;
class Optimizer extends Controller
{

    public function get_data_from_linear()
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer YOUR_LINEAR_API_TOKEN',
        'Content-Type' => 'application/json',
    ])->get('https://api.linear.app/graphql', [
        // Add your query parameters here if needed
    ]);

    if ($response->failed()) {
        return response()->json(['message' => 'Failed to fetch data'], 500);
    }

    return response()->json($response->json());
}


public function linear_callback(){
    // lin_wh_cO3iygOw6QdYFBpOZHTZuXplhxdWQ4yWo53wPK8TIfMP
    Log::info(request()->all());
}

public function linear_callback2(){
    // lin_wh_cO3iygOw6QdYFBpOZHTZuXplhxdWQ4yWo53wPK8TIfMP
    Log::info(request()->all());
}


}
