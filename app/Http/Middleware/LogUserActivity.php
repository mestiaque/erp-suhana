<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use Illuminate\Support\Str;

class LogUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $user = Auth::user();
        if ($user) {
            ActivityLog::create([
                'uuid' => Str::uuid()->toString(),
                'event' => 'user_active',
                'title' => "User Active: {$user->name}",
                'user_type' => get_class($user),
                'user_id' => $user->id,
                'loggable_type' => get_class($user),
                'loggable_id' => $user->id,
                'data' => json_encode([
                    'last_active_at' => now()->toDateTimeString(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                ]),
            ]);
        }

        return $response;
    }
}
