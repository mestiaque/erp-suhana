<?php

namespace App\Services;

use App\Contracts\ApprovalHandlerInterface;
use App\Mail\ApprovalRequestMail;
use App\Models\Approval;
use App\Models\MailNotificationSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Single, centralized entry point for every module's approval flow.
 * See config/approval.php for how a module registers itself, and
 * App\Approvals\Handlers\ExampleApprovalHandler for a usage template.
 */
class ApprovalService
{
    /**
     * Raise a new approval request. Creates the record and emails whoever
     * the module's handler says should be notified.
     *
     * @param  array{
     *     module: string,
     *     title: string,
     *     approvable?: Model|null,
     *     description?: string|null,
     *     route_name?: string|null,
     *     route_params?: array,
     *     fallback_url?: string|null,
     *     requested_by?: int|null,
     *     meta?: array,
     * }  $data
     */
    public function request(array $data): Approval
    {
        $approvable = $data['approvable'] ?? null;

        $approval = Approval::create([
            'uuid'           => (string) Str::uuid(),
            'module'         => $data['module'],
            'approvable_type' => $approvable ? get_class($approvable) : null,
            'approvable_id'   => $approvable?->getKey(),
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'status'         => 'pending',
            'route_name'     => $data['route_name'] ?? null,
            'route_params'   => $data['route_params'] ?? null,
            'fallback_url'   => $data['fallback_url'] ?? null,
            'requested_by'   => $data['requested_by'] ?? Auth::id(),
            'meta'           => $data['meta'] ?? null,
        ]);

        $this->notify($approval, $approvable);

        return $approval;
    }

    public function approve(Approval $approval, ?User $approver = null, ?string $remarks = null): Approval
    {
        $approver ??= Auth::user();

        $approval->update([
            'status'      => 'approved',
            'approved_by' => $approver?->id,
            'approved_at' => now(),
            'remarks'     => $remarks,
        ]);

        $this->handler($approval->module)?->onApproved($approval->fresh());

        return $approval->fresh();
    }

    public function reject(Approval $approval, ?User $approver = null, ?string $remarks = null): Approval
    {
        $approver ??= Auth::user();

        $approval->update([
            'status'      => 'rejected',
            'approved_by' => $approver?->id,
            'approved_at' => now(),
            'remarks'     => $remarks,
        ]);

        $this->handler($approval->module)?->onRejected($approval->fresh());

        return $approval->fresh();
    }

    public function handler(string $module): ?ApprovalHandlerInterface
    {
        // Module keys themselves contain dots (e.g. "inventory.requisition"),
        // so a plain array lookup is used here instead of config()'s
        // dot-notation path, which would otherwise treat every dot as a
        // nested-array separator and never find the key.
        $class = config('approval.modules')[$module] ?? null;

        if (!$class || !class_exists($class)) {
            return null;
        }

        return app($class);
    }

    /** Minimum gap between two reminders for the same request. */
    public const REMIND_INTERVAL_MINUTES = 5;

    /**
     * Re-sends the approval email for a still-pending request (the bell 🔔
     * button next to every approvable document and on the Approvals page).
     * Records how many reminders went out and when in the request's meta.
     * Returns null on success, otherwise the reason it wasn't sent.
     */
    public function remind(Approval $approval, ?User $by = null): ?string
    {
        $by ??= Auth::user();

        if (!$approval->isPending()) {
            return 'This request has already been actioned — no reminder needed.';
        }

        $meta = $approval->meta ?? [];
        $lastAt = $meta['reminders']['last_at'] ?? null;
        if ($lastAt) {
            $minutes = (int) floor(abs(\Carbon\Carbon::parse($lastAt)->diffInMinutes(now())));
            if ($minutes < self::REMIND_INTERVAL_MINUTES) {
                return 'A reminder was already sent '.($minutes ? "{$minutes} minute(s)" : 'less than a minute').' ago — please wait '
                    .(self::REMIND_INTERVAL_MINUTES - $minutes).' more minute(s).';
            }
        }

        if (!$this->notify($approval, $approval->approvable, true)) {
            return 'Reminder could not be sent — no approver has a valid email, or the mail server failed (see log).';
        }

        $meta['reminders'] = [
            'count'   => ($meta['reminders']['count'] ?? 0) + 1,
            'last_at' => now()->toDateTimeString(),
            'last_by' => $by?->name,
        ];
        $approval->update(['meta' => $meta]);

        return null;
    }

    /** Emails the module's approvers. Returns whether an email actually went out. */
    protected function notify(Approval $approval, ?Model $approvable, bool $reminder = false): bool
    {
        $handler = $this->handler($approval->module);

        if (!$handler) {
            return false;
        }

        // Admin-controlled kill switch (Settings > Mail Notifications):
        // this action's mail is turned off entirely.
        if (!MailNotificationSetting::enabled($approval->module)) {
            return false;
        }

        $recipients = collect($handler->recipients($approvable, $approval))
            ->map(fn ($recipient) => $recipient instanceof User ? $recipient->email : $recipient)
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
            ->unique()
            ->values();

        // Admin-picked recipients override the handler's default list, if set.
        if ($override = MailNotificationSetting::recipientEmailOverride($approval->module)) {
            $recipients = collect($override);
        }

        // Testing: every approval email goes only to the test address(es).
        if ($testRecipients = config('approval.test_recipients', [])) {
            $recipients = collect($testRecipients);
        }

        if ($recipients->isEmpty()) {
            return false;
        }

        $content = method_exists($handler, 'mailContent') ? (array) $handler->mailContent($approvable, $approval) : [];

        try {
            Mail::to($recipients->all())->send(new ApprovalRequestMail($approval, $content, $reminder));
        } catch (\Throwable $e) {
            report($e);

            return false;
        }

        return true;
    }
}
