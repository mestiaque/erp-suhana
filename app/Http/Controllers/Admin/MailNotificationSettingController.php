<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailNotificationSetting;
use App\Models\User;
use Illuminate\Http\Request;

class MailNotificationSettingController extends Controller
{
    public function index()
    {
        $actions = collect(config('mail_notifications.actions', []))
            ->map(function ($action, $key) {
                $setting = MailNotificationSetting::where('action_key', $key)->first();

                return array_merge($action, [
                    'action_key' => $key,
                    'is_enabled' => $setting->is_enabled ?? true,
                    'recipient_user_ids' => $setting->recipient_user_ids ?? [],
                ]);
            })
            ->groupBy('group');

        $users = User::where('status', 1)
            ->orderBy('name')
            ->with('permission:id,name')
            ->get(['id', 'name', 'email', 'permission_id']);

        return view('admin.setting.mail-notifications', compact('actions', 'users'));
    }

    public function update(Request $r, string $actionKey)
    {
        if (! array_key_exists($actionKey, config('mail_notifications.actions', []))) {
            abort(404);
        }

        $r->validate([
            'is_enabled' => 'nullable|boolean',
            'recipient_user_ids' => 'nullable|array',
            'recipient_user_ids.*' => 'integer|exists:users,id',
        ]);

        MailNotificationSetting::updateOrCreate(
            ['action_key' => $actionKey],
            [
                'title' => config("mail_notifications.actions.{$actionKey}.title", $actionKey),
                'is_enabled' => $r->boolean('is_enabled'),
                'recipient_user_ids' => $r->input('recipient_user_ids', []),
            ]
        );

        Session()->flash('success', 'Mail notification setting updated successfully!');

        return redirect()->route('admin.setting.mailNotifications');
    }
}
