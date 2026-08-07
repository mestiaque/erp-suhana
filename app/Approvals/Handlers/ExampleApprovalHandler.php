<?php

namespace App\Approvals\Handlers;

use App\Approvals\BaseApprovalHandler;
use App\Models\Approval;
use Illuminate\Database\Eloquent\Model;

/**
 * TEMPLATE — copy this into your own module and adjust the three methods
 * below, then register it in config/approval.php under 'modules'.
 *
 * Example registration:
 *   'hr.leave_request' => \ME\Hr\Approvals\LeaveRequestApprovalHandler::class,
 *
 * Example of raising a request from anywhere in that module:
 *
 *   app(\App\Services\ApprovalService::class)->request([
 *       'module'      => 'hr.leave_request',
 *       'approvable'  => $leaveRequest,               // the actual record (optional)
 *       'title'       => "Leave Request - {$employee->name}",
 *       'description' => "{$employee->name} requested leave from {$from} to {$to}.",
 *       'route_name'  => 'admin.hr.leaveRequests.edit', // page the approver lands on
 *       'route_params'=> ['leaveRequest' => $leaveRequest->id],
 *   ]);
 */
class ExampleApprovalHandler extends BaseApprovalHandler
{
    /**
     * Who gets the "approval needed" email. Return email strings and/or
     * App\Models\User instances — resolve this however the module needs
     * (a fixed role, the requester's line manager, a department head, etc).
     */
    public function recipients(?Model $approvable, Approval $approval): array
    {
        // This project keeps roles in the `permissions` table (User::permission()),
        // not a separate roles package — join through that relation to target a role.
        return \App\Models\User::whereHas('permission', function ($q) {
            $q->where('name', 'Admin');
        })->pluck('email')->filter()->all();
    }

    /**
     * Apply the real business effect once approved.
     */
    public function onApproved(Approval $approval): void
    {
        // $approval->approvable?->update(['status' => 'approved']);
    }

    /**
     * Apply the real business effect once rejected.
     */
    public function onRejected(Approval $approval): void
    {
        // $approval->approvable?->update(['status' => 'rejected', 'reject_reason' => $approval->remarks]);
    }
}
