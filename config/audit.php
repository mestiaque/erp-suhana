<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Excluded Models
    |--------------------------------------------------------------------------
    |
    | Eloquent models listed here will never be written to the audit log
    | (activity_logs table). Keep the log model itself excluded to avoid
    | infinite loops, plus any high-frequency / noisy models.
    |
    */
    'excluded_models' => [
        \App\Models\ActivityLog::class,
        \Illuminate\Notifications\DatabaseNotification::class,
    ],

];
