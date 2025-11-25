<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'event',
        'title',
        'user_type',
        'user_id',
        'loggable_type',
        'loggable_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Morph relationship to the user who performed the action.
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Morph relationship to the loggable entity.
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }
}
