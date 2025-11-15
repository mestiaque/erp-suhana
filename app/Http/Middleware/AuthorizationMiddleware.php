<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (Auth::guest()) {
            return redirect()->route('login');
        }

        if (!$request->user()->hasPermission($permission)) {
            // abort(403, 'Unauthorized action.');
            return response(view('auth.unauthorize1'), 403);
        }

        return $next($request);
    }
}
