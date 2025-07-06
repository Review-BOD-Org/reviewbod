<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Hash;
use Http;
use Auth;
use Carbon\Carbon;
class Manager extends Controller
{
    //

        public function invite($workspace,$id){
        $user = DB::table('managers')->where('manager_id', $id)->where("workspace",$workspace)->first();
        // dd($id);
        if ($user) {
            $invite = DB::table("users")->where(["id"=>$user->userid])->where("workspace",$workspace)->first();
            return view('dash.managers.invite', ['user' => $user,"invite"=>$invite]);
        } else {
            return redirect('/')->with('error', 'User not found');
        }
    }


    public function update_password(Request $request){

                $password = request()->input("password");
        $confirm_password = request()->input("password_confirmation");
   
        DB::table("managers")->where(["manager_id" =>$request->id])->update([
            "password" => Hash::make($password)
        ]);
        return response()->json(["message" => "Updated"]);

    }

    public function update_status(Request $request){
        DB::table("managers")->where(["workspace"=>$request->workspace,"manager_id"=>$request->id])->update(["status"=>$request->action == "accept" ? "active" : "decline"]);
           return response()->json([
                'success' => true,
                'message'=>"Invitation $request->status"
            ]);
    }
    

    public function login(){
        return view("dash.managers.login");
    }


      public function plogin(Request $request){

        try{
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth('managers')->attempt($credentials)) {
            // $request->session()->regenerate();

          
            if(Auth('managers')->user()->status == "blocked"){
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
        return view("dash.managers.dash");
    }


       public function chat_data(Request $request)
    {
        $userId = Auth('managers')->user()->id;
        $perPage = 10;

        // Get user's chats (most recently updated first)
        $chats = DB::table('chats')
            ->where('manager_id', $userId)
            ->where(["uuid" => $request->chat_id])
            ->orderBy('updated_at', 'desc')
            ->first();

        // Get messages for selected chat or first chat
        $chatId = $chats->uuid;

        if ($chatId) {
            // Get messages in reverse order (newest first) for pagination
            $messages = DB::table('chat_messages')
                ->where('chat_id', $chatId)
                ->where('manager_id', $userId)
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

              $userId = Auth('managers')->user()->id;
            $chatId = $request->input('chat_id');

            // Verify the chat belongs to the authenticated user
            $chat = DB::table('chats')
                ->where('uuid', $chatId)
                ->where('manager_id', $userId)
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
                    ->where('manager_id', $userId)
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
                'user_id' => Auth('managers')->user()->id,
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

     $userId = Auth('managers')->user()->id;
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

          public function sidebar_chats(Request $request)
    {
        try {
            // Get user's chats without messages
            $chats = DB::table('chats')
                ->where('manager_id', Auth('managers')->id())
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

        public function get_template(Request $request)
    {
        $des = $request->des;
        $id = $request->id;
        $sql = $request->sql;
        $owner_id = Auth("managers")->user()->id;
        $chat_id = $request->chat_id;


        $key = hex2bin(env('SODIUM_KEY')); // 32-byte key from env
        $nonceUser = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $encryptedUser = sodium_crypto_secretbox(Auth("managers")->user()->id, $nonceUser, $key);

        // combine nonce + ciphertext then base64 encode for safe JS embedding
        $user_id_encrypted = base64_encode($nonceUser . $encryptedUser);


        $res = Http::post('https://temp.reviewbod.com/generate-template?type=manager', [
            "description" => $des,
            "id" => $id,
            "sql" => $sql,
            "manager_id" => $user_id_encrypted,
            "chat_id" => $chat_id
        ])->json();


        return response()->json($res);

    }
  
        public function users(Request $request)
    {
        $data = DB::table("platform_users")
            ->leftJoin('linked_users', DB::raw("CONVERT(linked_users.email USING utf8mb4) COLLATE utf8mb4_unicode_ci"), '=', 'platform_users.email')
            ->select('platform_users.*', 'linked_users.status as user_status', 'linked_users.id as iid')
            ->where(["platform_users.manager_id" => Auth("managers")->user()->id]);

        if ($request->type) {
            $data->where(["platform_users.source" => $request->type]);
        }
        $data = $data->distinct()->get();
        return view("dash.managers.members", compact("data"));
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
     
       return view("dash.managers.user", [
            'data' => $data,
            'teams'=>$teams,
            "manager"=>$manager,
            "tasks" => $tasks,
            "tasksJson" => json_encode($tasksArray) // Pass JSON for JavaScript
        ]);
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



    public function logout(){
        Auth("managers")->logout();
        return redirect("/manager/login");
    }

      public function settings()
    {
           $user = DB::table("users")->where(["id"=>Auth("managers")->user()->userid])->first();
        // $users = DB::table("linked_users")->where(["userid" => auth()->id()])->get(); 
                $slack = DB::table("linked")->where(["userid" => $user->id, "type" => "slack"])->first();

        return view("dash.managers.settings",compact("slack"));
    }


        public function update_user(Request $request)
    {
        $userId = auth("managers")->id();
        $name = $request->input("name");
        $email = $request->input("email");
        $phone = $request->input("phone");
        // $company_name = $request->input("company_name");

        // Check if email exists for another user
        $emailExists = DB::table("managers")
            ->where("email", $email)
            ->where("id", "!=", $userId)
            ->exists();

        if ($emailExists) {
            return response()->json(["message" => "Email already in use"], 400);
        }

        // Check if phone exists for another user
        $phoneExists = DB::table("managers")
            ->where("phone", $phone)
            ->where("id", "!=", $userId)
            ->exists();

        if ($phoneExists) {
            return response()->json(["message" => "Phone number already in use"], 400);
        }

        DB::table("managers")->where("id", $userId)->update([
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            // "company_name" => $company_name
        ]);

        return response()->json(["message" => "Updated"]);
    }

      public function _update_password()
    {
        $password = request()->input("password");
        $confirm_password = request()->input("password_confirmation");
        if ($password != $confirm_password) {
            return response()->json(["message" => "Password not matched"], 422);
        }
        DB::table("managers")->where(["id" => auth("managers")->id()])->update([
            "password" => Hash::make($password)
        ]);
        return response()->json(["message" => "Updated"]);

    }

    public function save_notification(Request $request){
     
              // Handle email notifications
        if ($request->email_notifications != "0") {
            // Check if the record already exists to avoid duplicates
            $exists = DB::table("notification_channel_manager")
                ->where("channel", "email")
                ->where("userid", Auth("managers")->user()->id)
                ->exists();

            if (!$exists) {
                DB::table("notification_channel_manager")->insert([
                    "channel" => "email",
                    "userid" => Auth("managers")->user()->id,
                    "created_at" => now(),
                ]);
            }
        } else {
            // Remove email notification preference
            DB::table("notification_channel_manager")
                ->where("channel", "email")
                ->where("userid", Auth("managers")->user()->id)
                ->delete();
        }

        // Handle slack notifications
        if ($request->slack_notifications != "0") {
            // Check if the record already exists to avoid duplicates
            $exists = DB::table("notification_channel_manager")
                ->where("channel", "slack")
                ->where("userid", Auth("managers")->user()->id)
                ->exists();

            if (!$exists) {
                DB::table("notification_channel_manager")->insert([
                    "channel" => "slack",
                    "userid" => Auth("managers")->user()->id,
                    "created_at" => now(),
                ]);
            }
        } else {
            // Remove slack notification preference
            DB::table("notification_channel_manager")
                ->where("channel", "slack")
                ->where("userid", Auth("managers")->user()->id)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully'
        ]);
        
    }


}
