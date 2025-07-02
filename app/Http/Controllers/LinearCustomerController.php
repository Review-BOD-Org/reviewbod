<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Mail;
use Hash;
use Carbon\Carbon;
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
         

        $check = DB::table('linked_users')->where("workspace",Auth::user()->workspace)->where('email', $request->email)->first();
        $check2 = DB::table('linked_users')->where("workspace",Auth::user()->workspace)->where('email', $request->email)->where("password","!=",null)->first();
        if ($check2) {
            return response()->json(['message' => 'User already exists'], 409);
        }
        if (!$check) {
  // Store the user ID in the database
  DB::table('linked_users')->insert([
    'invite_id' => rand(1111111,9999999),
    'name' => $request->name,
    'email' => $request->email, 
    "workspace"=>Auth::user()->workspace,
    'userid'=>Auth::id()
]);
        }else{
            DB::table('linked_users')->where("workspace",Auth::user()->workspace)->where('email', $request->email)->update(["status"=>"pending"]);
        }
 
               $check = DB::table('linked_users')->where("workspace",Auth::user()->workspace)->where('email', $request->email)->first();


        Mail::send('mail.invitation', ['name' => $request->name,"id"=>$check->invite_id,"workspace"=>Auth::user()->workspace], function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Inivitation  to ReviewBod - Linear');
        });
        return response()->json(['message' => 'User created successfully'], 201);
    }

    public function invite($workspace,$id){
        $user = DB::table('linked_users')->where('invite_id', $id)->where("workspace",$workspace)->first();
        if ($user) {
            $invite = DB::table("users")->where(["id"=>$user->userid])->where("workspace",$workspace)->first();
            return view('dash.invite', ['user' => $user,"invite"=>$invite]);
        } else {
            return redirect('/')->with('error', 'User not found');
        }
    }

    public function reports(){
        return view("dash.reports");
    }

    public function setstatus(Request $request){
        DB::table('linked_users')->where(["id"=>$request->id,'userid'=>Auth::id()])->update(["status"=>$request->status]);
        return response()->json(["message"=>"User status updated to $request->status"]);
    }

    public function bulk_block_users(Request $request){
         $users = $request->input('users');
    $userIds = array_column($users, 'id');
    
    // Update user status to blocked
      DB::table('linked_users')->whereIn('id', $userIds)->where(['userid'=>Auth::id()])->update(['status' => 'blocked']);
    
    return response()->json(['message' => 'Users blocked successfully']);
    }

     public function bulk_send_invites(Request $request)
    {
         
          $users = $request->input('users');
    
    foreach ($users as $userData) {

        $check2 = DB::table('linked_users')->where('email', $userData['email'])->where("password","!=",null)->first();
        if ($check2) {
            return response()->json(['message' => 'User already exists'], 409);
        }
                $check = DB::table('linked_users')->where('email', $userData['email'])->first();

        if (!$check) {
  // Store the user ID in the database
  DB::table('linked_users')->insert([
    'invite_id' => rand(1111111,9999999),
    'name' => $userData['name'],
    'email' => $userData['email'], 
    'userid'=>Auth::id()
]);

        }
 
         $check = DB::table('linked_users')->where('email', $userData['email'])->first();

       

        Mail::send('mail.invitation', ['name' => $userData['name'],"id"=>$check->invite_id], function ($message) use ($request,$userData) {
            $message->to($userData['email'])
                ->subject('Inivitation  to ReviewBod - Linear');
        });
               }
        return response()->json(['message' => 'User created successfully'], 201);
    }


    public function login(){
        return view("dash.invite.login");
    }


     public function plogin(Request $request){

        try{
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth('linear_user')->attempt($credentials)) {
            // $request->session()->regenerate();

          
            if(Auth('linear_user')->user()->status == "blocked"){
                     return response()->json([
                'message' => 'Account Restricted', 
            ],400);
            }
       
            return response()->json([
                'message' => 'Authenticated successfully',
                'redirect'=> '/dashboard'
            ]);
     
        }

        return response()->json([
            'message' => 'The provided credentials do not match our records.',
        ],400);

    }catch(\Exception $e){
        return  response()->json([
            'message' => $e->getMessage(),
        ],400);
    }
    }

    public function dash(){
          return view("dash.invite.dash");
    }

        public function sidebar_chats(Request $request)
    {
        try {
            // Get user's chats without messages
            $chats = DB::table('chats')
                ->where('staff_id', Auth('linear_user')->id())
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

    public function update_status(Request $request){
        DB::table("linked_users")->where(["workspace"=>$request->workspace,"invite_id"=>$request->id])->update(["status"=>$request->action == "accept" ? "active" : "decline"]);
           return response()->json([
                'success' => true,
                'message'=>"Invitation $request->status"
            ]);
    }

    public function space(Request $request){
        $spaces =  DB::table("linked_users")->where(["email"=>Auth('linear_user')->user()->email])->get();
            return view("dash.invite.space",compact("spaces"));
    }

    public function choose(Request $request){
        $check = DB::table("users")->where(["workspace"=>$request->space])->first();
        if(!$check){
                          return response()->json(["message"=>"Workspace not found, please contact admin!"],400);

        }
        if($check->status == "expired"){
              return response()->json(["message"=>"Workspace expired please contact admin!"],400);
        }
        DB::table("linked_users")->where(["id"=>Auth('linear_user')->user()->id])->where('password','!=',null)->update(['space'=>$request->space]);
        return response()->json(["message"=>"Authenticated!"]);
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

      public function chat_data(Request $request)
    {
        $userId = Auth('linear_user')->user()->id;
        $perPage = 10;

        // Get user's chats (most recently updated first)
        $chats = DB::table('chats')
            ->where('staff_id', $userId)
            ->where(["uuid" => $request->chat_id])
            ->orderBy('updated_at', 'desc')
            ->first();

        // Get messages for selected chat or first chat
        $chatId = $chats->uuid;

        if ($chatId) {
            // Get messages in reverse order (newest first) for pagination
            $messages = DB::table('chat_messages')
                ->where('chat_id', $chatId)
                ->where('staff_id', $userId)
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


    public function deleteChat(Request $request)
    {
        try {
            $request->validate([
                'chat_id' => 'required|string|exists:chats,uuid'
            ]);

              $userId = Auth('linear_user')->user()->id;
            $chatId = $request->input('chat_id');

            // Verify the chat belongs to the authenticated user
            $chat = DB::table('chats')
                ->where('uuid', $chatId)
                ->where('staff_id', $userId)
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
                    ->where('staff_id', $userId)
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

     $userId = Auth('linear_user')->user()->id;
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

    public function logout(){
           DB::table( "linked_users")->where(["id"=>Auth('linear_user')->user()->id])->where('password','!=',null)->update(['space'=>null]);

        Auth('linear_user')->logout();
        return redirect("/invited/login");
    }


    public function user(){

     $id = Auth('linear_user')->user()->id;
          $data = DB::table("linked_users")->where(["id" => $id,"space"=>Auth('linear_user')->user()->space])->first();
      

          $sources = DB::table("platform_users")
          ->join("users","users.id","platform_users.owner_id")
          ->where(["users.workspace"=>Auth('linear_user')->user()->space])
          ->where(["platform_users.email"=>Auth('linear_user')->user()->email])->select("source")->distinct()->get();
        // Get tasks with more detailed information
$tasks = DB::table("tasks")
    ->join("users", "users.id", "=", "tasks.owner_id")
    ->join("platform_users", "platform_users.user_id", "=", "tasks.user_id")
    ->where("platform_users.email", $data->email)
    ->where("users.workspace", Auth('linear_user')->user()->space)
    ->whereNull("tasks.is_deleted")
    ->select('tasks.id', 'platform_users.email', 'title', 'description', 'tasks.status', 'priority', 'due_date', 'tasks.created_at', 'tasks.updated_at','tasks.source')
    ->distinct() // ensures unique rows
    ->orderBy('tasks.updated_at', 'desc');

$totaltasks = DB::table("tasks")
    ->join("users", "users.id", "=", "tasks.owner_id")
    ->join("platform_users", "platform_users.user_id", "=", "tasks.user_id")
    ->where("platform_users.email", $data->email)
    ->where("users.workspace", Auth('linear_user')->user()->space)
    ->whereNull("tasks.is_deleted")
    ->select('tasks.id', 'platform_users.email', 'title', 'description', 'tasks.status', 'priority', 'due_date', 'tasks.created_at', 'tasks.updated_at','tasks.source')
    ->distinct() // ensures unique rows
    ->orderBy('tasks.updated_at', 'desc')->get();
 
//  dd($totaltasks);
    // dd($tasks->created_at);
            // dd($tasks);
$totalProjects = DB::table("projects")
    ->join("users", "users.id", "=", "projects.owner_id")
    ->join("platform_users", "platform_users.owner_id", "=", "users.id")
    ->where("platform_users.email",  $data->email)
    ->where("users.workspace", Auth('linear_user')->user()->space)
    ->select("projects.project_key","projects.*","platform_users.email")
    ->distinct()
    ->get()
    ->unique("project_key")->count();



            // return;

        // Convert to array for JavaScript
        $tasksArray = $tasks->get()->toArray();
      

        return view("dash.invite.user", [
            'data' => $data,
            'totaltasks'=>$totaltasks,
            "sources"=>$sources,
            "totalProjects"=>$totalProjects,
            "tasks" => $tasks,
            "tasksJson" => json_encode($tasksArray) // Pass JSON for JavaScript
        ]);
    }

 public function getTaskPerformance(Request $request)
    {



        $days = $request->input('days', 7);
        $userId =    Auth('linear_user')->user()->email;

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
     $firstTaskDate = DB::table("tasks")
    ->where(["platform_users.email" => $userId])
    ->join("users","users.id","tasks.owner_id")
    ->join("platform_users","platform_users.user_id","=","tasks.user_id")
    ->select('tasks.id','platform_users.email', 'title', 'description', 'tasks.status', 'priority', 'due_date', 'tasks.created_at', 'tasks.updated_at')
    ->orderBy('tasks.updated_at', 'desc')
    ->whereNull('is_deleted')
    ->orderBy('created_at', 'asc') 
    ->distinct()
    ->where(["users.workspace" => Auth('linear_user')->user()->space])
    ->first();

    // dd(count($firstTaskDate));
      
         $firstTaskDate = $firstTaskDate->created_at;
        //  dd($firstTaskDate);

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
        DB::raw('DATE(tasks.created_at) as date'),
        DB::raw('COUNT(DISTINCT tasks.id) as total_assigned'),
        DB::raw('SUM(CASE WHEN tasks.status IN ("Done") THEN 1 ELSE 0 END) as total_completed')
    ])
   
    ->join("users", "users.id", "tasks.owner_id")
    ->join("platform_users", "platform_users.user_id", "=", "tasks.user_id")
    ->where("platform_users.email", $userId)
    ->whereNull('is_deleted')
    ->whereBetween('tasks.created_at', [$startDate, $endDate])
    ->groupBy(DB::raw('DATE(tasks.created_at)'))
    ->orderBy('date', 'asc')
  ->where(["users.workspace" => Auth('linear_user')->user()->space])
    ->get();
//   dd($taskStats);

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


      public function get_template(Request $request)
    {
        $des = $request->des;
        $id = $request->id;
        $sql = $request->sql;
        $owner_id = Auth("linear_user")->user()->id;
        $chat_id = $request->chat_id;


        $key = hex2bin(env('SODIUM_KEY')); // 32-byte key from env
        $nonceUser = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $encryptedUser = sodium_crypto_secretbox(Auth("linear_user")->user()->email, $nonceUser, $key);

        // combine nonce + ciphertext then base64 encode for safe JS embedding
        $user_id_encrypted = base64_encode($nonceUser . $encryptedUser);


        $res = Http::post('https://temp.reviewbod.com/generate-template?type=invited', [
            "description" => $des,
            "id" => $id,
            "sql" => $sql,
            "staff_id" => $user_id_encrypted,
            "chat_id" => $chat_id
        ])->json();


        return response()->json($res);

    }
  


    public function settings(){
        $user = DB::table("users")->where(["workspace"=>Auth("linear_user")->user()->space])->first();
        $id = $user->id;
        return view("dash.invite.settings",compact("id"));
    }


    public function update_user_profile(Request $request){
       $userId = auth("linear_user")->id();
        $name = $request->input("name");
        $email = $request->input("email");  

        // Check if email exists for another user
        $emailExists = DB::table("linked_users")
            ->where("email", $email)
            ->where("id", "!=", $userId)
            ->exists();

        if ($emailExists) {
            return response()->json(["message" => "Email already in use"], 400);
        }

        

        DB::table("linked_users")->where("id", $userId)->update([
            "name" => $name,
            "email" => $email, 
        ]);

        return response()->json(["message" => "Updated"]);
    }

    public function update_password(Request $request){

                $password = request()->input("password");
        $confirm_password = request()->input("password_confirmation");
        if ($password != $confirm_password) {
            return response()->json(["message" => "Password not matched"], 422);
        }
        DB::table("users")->where(["id" => auth("linear_user")->id()])->update([
            "password" => Hash::make($password)
        ]);
        return response()->json(["message" => "Updated"]);

    }



 
   

}