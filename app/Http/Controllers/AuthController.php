<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Mail;
use Hash;
use Cache;
class AuthController extends Controller
{
    //

    public function login(){
        return view("auth.login");
    }

    public function register(){
        return view("auth.register");
    }


    public function workspace(){
        return view("auth.workspace");
}


    public function verification(){
        return view("auth.otp_verify");
    }

    public function choose(){
        return view("auth.choose");
    }



    // server side

    public function plogin(Request $request){

        try{
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // $request->session()->regenerate();

            if(Auth()->user()->verified != 1){
             $this->sendMail(Auth()->user()->token,Auth()->user()->email);
             return response()->json([
                'message' => 'Authenticated successfully',
                'redirect'=> '/auth/verification'
            ]);
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

    public function pregister(Request $request){
        try{
       
        $checkemail = DB::table('users')->where('email',$request->email)->first();
        $checkphone = DB::table('users')->where('phone',$request->phone)->first();

        if($checkemail){
            return response()->json([
                'message' => 'Email already exists',
            ],400);
        }

        if($checkphone){
            return response()->json([
                'message' => 'Phone already exists',
            ],400);
        }

        $token = rand(111111,999999);

        $recipient = $request->email;
      
        $this->sendMail($token,$recipient);

        DB::table("users")->insert([
            'name'=>$request->fullname,
            'company_name' => $request->company,
            'email' => $request->email,
            'phone' => $request->phone,
            'token'=>$token,
            'password' => Hash::make($request->password),
        ]);

        Auth::attempt($request->only("email","password"));

        return response()->json([
            'message' => 'User created successfully',
        ],200);
    }catch(\Exception $e){
        return  response()->json([
            'message' => $e->getMessage(),
        ],400);
    }
    }

    public function sendMail($token,$recipient){
   
        $subject = 'ReviewBOD Account Verification';

        Mail::send("mail.otp", ["otp" => $token], function ($mail) use ($recipient, $subject) {
            $mail->to($recipient)
                ->subject($subject);
        });

    }


    public function pverification(){
        $checkotp = DB::table("users")->where("token",request()->otp)->where("email",Auth::user()->email)->first();
        if($checkotp){
            DB::table("users")->where("token",request()->otp)->where("email",Auth::user()->email)->update([
                'verified' => 1
            ]);
            return response()->json([
                'message' => 'User verified successfully',
            ],200);
        }else{
            return response()->json([
                'message' => 'Invalid OTP',
            ],400);
        }
    }


    public function pchoose(Request $request){
        $service  = $request->service;
        if($service == "linear"){
            $link = "/linear/auth";
        }
        if($service == "slack"){
            $link = "/slack/auth";
        }
        if($service == "trello"){
            $link = "/trello/auth";
        }
        if($service == "jira"){
        $link = "/jira/auth";
        }
        return response()->json([
            'message' => 'Service selected successfully',
            'link'=>$link
        ],200);
    }
    public function resend_otp(){
        $token = rand(111111,999999);
        $recipient = Auth::user()->email;
        $this->sendMail($token,$recipient);
        DB::table("users")->where("email",Auth::user()->email)->update([
            'token' => $token
        ]);
        return response()->json([
            'message' => 'OTP sent successfully',
        ],200);
    }


    public function pricing(){
        return view("auth.sub");
    }

        public function waiting(){
        return view("auth.waiting");
    }

    public function update_password(Request $request){
        
        $checkuser = DB::table("linked_users")->where("invite_id",$request->id)->first();
        if(!$checkuser){
            return response()->json([
                'message' => 'User not found',
            ],400);
        }

        DB::table("linked_users")->where("invite_id",$request->id)->update([
            'password' => Hash::make($request->password),
            "status" => "active",
        ]);
   

        return response()->json([
            'message' => 'Password updated successfully',
        ],200);
    }

    public function create(Request $request){
        $check = DB::table("users")->where(["workspace"=>$request->name])->exists();
        if($check){
              return response()->json([
                'message' => 'This workspace name already exists, please choose another name!',
            ],400);
        }

         DB::table("users")->where(["id"=>Auth::id()])->update(["workspace"=>$request->name]);
       return response()->json([
            'message' => 'Workspace created!',
        ],200);
    }
}
