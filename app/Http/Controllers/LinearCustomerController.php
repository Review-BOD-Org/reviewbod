<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Mail;
class LinearCustomerController extends Controller
{
    private $linearApiUrl = 'https://api.linear.app/graphql';

    /**
     * Show the form
     */
    public function index()
    {
        return view('dash.customers.index');
    }

    /**
     * Store user in Linear and custom fields/managers locally
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'custom_fields' => 'nullable|array',
                'custom_fields.*.name' => 'required|string|max:255',
                'custom_fields.*.value' => 'nullable|string',
                'managers' => 'nullable|array',
                'managers.*.name' => 'required|string|max:255',
                'managers.*.email' => 'required|email',
                'managers.*.phone' => 'nullable|string|max:20',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process customer: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    
        $linearUserId = $request->id;
    
        try {
            DB::beginTransaction();
    
            // Use updateOrInsert to avoid duplicate entry error
            DB::table('custom_details')->updateOrInsert(
                ['linear_user_id' => $linearUserId],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
    
            DB::table("managers")->where(["linear_user_id" => $linearUserId])->delete();
            DB::table("custom_fields")->where(["linear_user_id" => $linearUserId])->delete();
    
            // Store custom fields
            if ($request->has('custom_fields')) {
                foreach ($request->custom_fields as $field) {
                    DB::table('custom_fields')->insert([
                        'linear_user_id' => $linearUserId,
                        'field_name' => $field['name'],
                        'field_value' => $field['value'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
    
            // Store managers
            if ($request->has('managers')) {
                foreach ($request->managers as $manager) {
                    DB::table('managers')->insert([
                        'linear_user_id' => $linearUserId,
                        'name' => $manager['name'],
                        'email' => $manager['email'],
                        'phone' => $manager['phone'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
    
            DB::commit();
            return response()->json(['message' => 'User updated successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process customer: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show custom fields and managers for a user
     */
    public function show($linearUserId)
    {
        $details = DB::table('custom_details')
            ->where('linear_user_id', $linearUserId)
            ->first();

        $customFields = DB::table('custom_fields')
            ->where('linear_user_id', $linearUserId)
            ->get();

        $managers = DB::table('managers')
            ->where('linear_user_id', $linearUserId)
            ->get();

        return response()->json(compact('details', 'customFields', 'managers'));
    }

    /**
     * Create user in Linear via GraphQL API
     */
    private function createLinearUser($name, $email, $accessToken)
    {
        try {
            // Using the correct customerCreate mutation from the documentation
            $query = <<<'GRAPHQL'
            mutation CustomerCreate($input: CustomerCreateInput!) {
                customerCreate(input: $input) {
                    success
                    customer {
                        id
                    }
                }
            }
            GRAPHQL;
    
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->linearApiUrl, [
                'query' => $query,
                'variables' => [
                    'input' => [
                        'name' => $name,
                        // Optional: Add domain if it's not from a public email provider
                        'domains' => [$email],
                    ]
                ],
            ]);
    
            // Log the complete response for debugging
            Log::info('Linear API response', ['response' => $response->json()]);
            
            $data = $response->json();
    
            // Check for errors in the response
            if (isset($data['errors'])) {
                Log::warning('Linear API returned errors', ['errors' => $data['errors']]);
                return $data['errors'];
            }
    
            // Check for success and return customer ID if available
            if ($data['data']['customerCreate']['success'] ?? false) {
                return $data['data']['customerCreate']['customer']['id'] ?? null;
            }
    
            Log::warning('Linear API customer creation failed', ['response' => $data]);
            return null;
        } catch (\Exception $e) {
            Log::error('Linear API error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return null;
        }
    }

    public function send_invite(Request $request)
    {
         

        $check = DB::table('linear_users')->where('email', $request->email)->first();
        $check2 = DB::table('linear_users')->where('email', $request->email)->where("password","!=",null)->first();
        if ($check2) {
            return response()->json(['message' => 'User already exists'], 409);
        }
        if (!$check) {
  // Store the user ID in the database
  DB::table('linear_users')->insert([
    'invite_id' => $request->id,
    'name' => $request->name,
    'email' => $request->email, 
    'userid'=>Auth::id()
]);
        }
 
       

        Mail::send('mail.invite', ['name' => $request->name,"id"=>$request->id], function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Inivitation  to ReviewBod - Linear');
        });
        return response()->json(['message' => 'User created successfully'], 201);
    }

    public function invite($id){
        $user = DB::table('linear_users')->where('invite_id', $id)->where("password",null)->first();
        if ($user) {
            return view('dash.invite', ['user' => $user]);
        } else {
            return redirect('/')->with('error', 'User not found');
        }
    }

    public function reports(){
        return view("dash.reports");
    }
}