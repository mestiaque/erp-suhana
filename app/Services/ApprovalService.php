<?php

namespace App\Services;

use App\Contracts\ApprovalHandlerInterface;
use App\Mail\ApprovalRequestMail;
use App\Models\Approval;
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

    protected function notify(Approval $approval, ?Model $approvable): void
    {
        $handler = $this->handler($approval->module);

        if (!$handler) {
            return;
        }

        $recipients = collect($handler->recipients($approvable, $approval))
            ->map(fn ($recipient) => $recipient instanceof User ? $recipient->email : $recipient)
            ->filter()
            ->unique()
            ->values();

        if ($recipients->isEmpty()) {
            return;
        }

        try {
            Mail::to($recipients->all())->send(new ApprovalRequestMail($approval));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
