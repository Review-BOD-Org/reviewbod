<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
class TestRoute extends Controller
{
    //

    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the user
        if ($token = auth("api")->attempt($request->only('email', 'password'))) {
            // Authentication passed, return user data
            return $this->respondWithToken($token);
        }

        // Authentication failed, return error response
        return response()->json(['error' => 'Unauthorized'], 401);
    }

       protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth("api")->factory()->getTTL() * 60
        ]);
    }

    public function user(){
        $user = auth("api")->user();
        return response()->json($user);
    }

    public function services(){
        $data = DB::table("linked")->where(["userid"=>auth("api")->user()->id])->get();
        return response()->json($data);
    }

    public function datas(){
        $res = new FetchData();
        Auth::loginusingId(auth("api")->user()->id);
        $users = $res->getData("users");
        $teams = $res->getData("teams");
        $data = $res->getData("dash");
        $ai = $res->getData("ai");
        $res = [
            "users"=>$users,
            "teams"=>$teams,
            "ai"=>$ai,
            "data"=>$data
        ];
        return response()->json($res);
    }
}
