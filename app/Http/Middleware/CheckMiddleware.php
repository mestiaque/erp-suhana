<?php

namespace App\Http\Middleware;

use Session;
use App;
use Auth;
use Closure;
use Illuminate\Http\Request;

class CheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        if (Session::has('lang')) {
            App::setLocale(Session::get('lang'));
        }else{
            App::setLocale('en');
        }

        if(general()->maintenance_mode){
            
            if($request->is('admin*')  || $request->is('/') || url('login')==url()->current()){
                return $next($request);
            }else{
                
                return redirect()->route('index');
            }

        }else{
            return $next($request);
        }
        
    }
}
