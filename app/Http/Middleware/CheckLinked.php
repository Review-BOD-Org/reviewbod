<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
class CheckLinked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Auth("linear_user")->check()){
            return redirect("/invited/login");
        }

        if(!Auth("linear_user")->user()->space){
            return redirect("/invited/space");
        }
        return $next($request);
    }
}
