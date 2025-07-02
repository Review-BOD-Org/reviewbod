<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Hash;
use Http;
use Auth;
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
}
