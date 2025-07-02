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
   
        DB::table("managers")->where(["id" => auth("managers")->id()])->update([
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
}
