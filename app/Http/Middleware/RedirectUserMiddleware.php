<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // current path
        $path = trim($request->path(), '/');

        // Admin routes
        if (str_starts_with($path, 'admin')) {
            if ($user->admin == 1) {
                return $next($request);
            }
            abort(403, 'Unauthorized');
        }

        // Business routes
        if (str_starts_with($path, 'staff')) {
            if ($user->staff == 1) {
                return $next($request);
            }
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
