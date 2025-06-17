<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB;
use Auth;
class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(!Auth::check()){
            return redirect("/auth/login");
        }


        $data = DB::table("linked")->where("userid",Auth::user()->id)->first();
        if($data == null){
            return redirect("/auth/choose");
        }


        // $slack = DB::table("linked")->where("userid",Auth::user()->id)->where("type","slack")->first();
        // if($slack){
        //     $data = DB::table("linked")
        //     ->where("type", "slack")
        //     ->whereNotNull("token")
        //     ->whereNotNull("slack_channel")
        //     ->where("userid", auth()->user()->id)
        //     ->first();
        //     // dd($data);
        //     if($data == null){
        //         return redirect("/slack/channels");
        //     }
        // }

    
        return $next($request);
    }
}
