<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Approval extends Model
{
    protected $fillable = [
        'uuid',
        'module',
        'approvable_type',
        'approvable_id',
        'title',
        'description',
        'status',
        'route_name',
        'route_params',
        'fallback_url',
        'requested_by',
        'approved_by',
        'approved_at',
        'remarks',
        'meta',
    ];

    protected $casts = [
        'route_params' => 'array',
        'meta'         => 'array',
        'approved_at'  => 'datetime',
    ];

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * The direct link to the module's own page for this approval.
     * Prefers a named route (survives domain/path changes) and falls back
     * to the stored absolute URL.
     */
    public function getUrlAttribute(): string
    {
        if ($this->route_name) {
            try {
                return route($this->route_name, $this->route_params ?? []);
            } catch (\Throwable $e) {
                // route may no longer exist (renamed/removed) — fall through to fallback_url
            }
        }

        return $this->fallback_url ?? '#';
    }
}
