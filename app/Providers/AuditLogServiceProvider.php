<?php

namespace App\Providers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

/**
 * Registers wildcard Eloquent listeners so every model's create/update/delete
 * (across all app + package models) is written to the audit log automatically,
 * with no per-model changes required.
 */
class AuditLogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen('eloquent.created: *', function (string $eventName, array $data) {
            $this->logChange('create', $data[0] ?? null);
        });

        Event::listen('eloquent.updated: *', function (string $eventName, array $data) {
            $this->logChange('update', $data[0] ?? null);
        });

        Event::listen('eloquent.deleted: *', function (string $eventName, array $data) {
            $this->logChange('delete', $data[0] ?? null);
        });
    }

    protected function logChange(string $event, $model): void
    {
        if (!$model instanceof Model) {
            return;
        }

        if (in_array(get_class($model), config('audit.excluded_models', []), true)) {
            return;
        }

        if ($model->getKey() === null) {
            return;
        }

        $old = null;
        $new = null;

        if ($event === 'create') {
            $new = $model->getAttributes();
        } elseif ($event === 'update') {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if (empty($changes)) {
                return;
            }

            $original = $model->getOriginal();
            $old = [];
            $new = [];
            foreach ($changes as $key => $value) {
                $old[$key] = $original[$key] ?? null;
                $new[$key] = $value;
            }
        } else {
            $old = $model->getAttributes();
        }

        $user = Auth::user();
        $request = app()->bound('request') && !app()->runningInConsole() ? request() : null;

        try {
            ActivityLog::create([
                'uuid'          => (string) Str::uuid(),
                'event'         => $event,
                'title'         => class_basename($model).' '.ucfirst($event).' (#'.$model->getKey().')',
                'user_type'     => $user ? get_class($user) : null,
                'user_id'       => $user ? $user->id : null,
                'loggable_type' => get_class($model),
                'loggable_id'   => $model->getKey(),
                'data'          => ['old' => $old, 'new' => $new],
                'ip_address'    => $request ? clientPublicIp($request) ?? $request->ip() : null,
                'user_agent'    => $request ? $request->userAgent() : null,
                'url'           => $request ? $request->fullUrl() : null,
                'method'        => $request ? $request->method() : null,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
