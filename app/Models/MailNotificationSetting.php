<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailNotificationSetting extends Model
{
    protected $fillable = [
        'action_key',
        'title',
        'is_enabled',
        'recipient_user_ids',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'recipient_user_ids' => 'array',
    ];

    /** Off by default only when a row exists and is explicitly disabled. */
    public static function enabled(string $actionKey): bool
    {
        return static::where('action_key', $actionKey)->value('is_enabled') ?? true;
    }

    /**
     * Admin-picked recipient emails for this action, or null when not
     * configured (meaning: use the action's own default recipients).
     * An explicitly saved-but-empty selection also falls back to the
     * default, so clearing the picker by accident can't go silent.
     */
    public static function recipientEmailOverride(string $actionKey): ?array
    {
        $ids = static::where('action_key', $actionKey)->value('recipient_user_ids');
        $ids = $ids ? json_decode($ids, true) : null;

        if (empty($ids)) {
            return null;
        }

        $emails = User::whereIn('id', $ids)->pluck('email')->filter()->values()->all();

        return $emails ?: null;
    }
}
