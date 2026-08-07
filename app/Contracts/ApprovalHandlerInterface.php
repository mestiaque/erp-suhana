<?php

namespace App\Contracts;

use App\Models\Approval;
use Illuminate\Database\Eloquent\Model;

/**
 * Every module that wants to use the central approval system registers a
 * class implementing this interface in config/approval.php, keyed by its
 * module name. See App\Approvals\BaseApprovalHandler for a convenient base
 * class, and config/approval.php for a full usage example.
 */
interface ApprovalHandlerInterface
{
    /**
     * Who should be emailed when a new approval request is raised for
     * this module. Return an array of email address strings and/or
     * App\Models\User instances (their ->email is used).
     */
    public function recipients(?Model $approvable, Approval $approval): array;

    /**
     * Called right after the request is approved. This is where the module
     * applies the actual business effect (e.g. mark a leave request as
     * approved, release a purchase order, etc).
     */
    public function onApproved(Approval $approval): void;

    /**
     * Called right after the request is rejected.
     */
    public function onRejected(Approval $approval): void;
}
