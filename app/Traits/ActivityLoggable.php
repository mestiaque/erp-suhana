<?php
namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait ActivityLoggable
{
    public static function bootActivityLoggable()
    {
        static::created(function ($model) {
            self::logActivity('create', $model, [
                'old' => null,
                'new' => $model->toArray()
            ]);
        });

        static::updated(function ($model) {
            $changes = $model->getChanges(); // যা update হয়েছে
            $original = $model->getOriginal(); // পুরোনো data

            $changedOld = [];
            $changedNew = [];

            foreach ($changes as $key => $value) {
                $changedOld[$key] = $original[$key] ?? null;
                $changedNew[$key] = $value;
            }

            self::logActivity('update', $model, [
                'old' => $changedOld,
                'new' => $changedNew
            ]);
        });

        static::deleted(function ($model) {
            self::logActivity('delete', $model, [
                'old' => $model->toArray(),
                'new' => null
            ]);
        });
    }

    protected static function logActivity($event, $model, $data = [])
    {
        $user = Auth::user();

        ActivityLog::create([
            'uuid' => Str::uuid(),
            'event' => $event,
            'title' => class_basename($model) . " {$event}",
            'user_type' => $user ? get_class($user) : null,
            'user_id' => $user ? $user->id : null,
            'loggable_type' => get_class($model),
            'loggable_id' => $model->id,
            'data' => $data
        ]);

        if ($user) {
            self::logUserActivity($user);
        }
    }


    protected static function logUserActivity($user)
    {
        ActivityLog::create([
            'uuid' => Str::uuid(),
            'event' => 'user_active',
            'title' => "User Active: {$user->name}",
            'user_type' => get_class($user),
            'user_id' => $user->id,
            'loggable_type' => get_class($user),
            'loggable_id' => $user->id,
            'data' => [
                'last_active_at' => now()->toDateTimeString(),
                'ip' => request()->ip() ?? null,
                'user_agent' => request()->userAgent() ?? null,
            ]
        ]);
    }
}
