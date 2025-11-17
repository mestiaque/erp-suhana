<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if(Auth::check()  && Auth::user()->$role==true){

            if( Auth::user()->status==1){
                return $next($request);
            }else{
              Auth::logout();
            return redirect()->route('login')->with('status','You are suspend User. Pleace contact Authorieze.');
            }

        }else{
            return redirect()->route('index');
        }
    }
}
