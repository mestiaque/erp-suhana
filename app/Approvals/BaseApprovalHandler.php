<?php

namespace App\Approvals;

use App\Contracts\ApprovalHandlerInterface;
use App\Models\Approval;
use Illuminate\Database\Eloquent\Model;

/**
 * Convenience base class — extend this in a module and only override what
 * you need. See config/approval.php for how to register the handler and
 * App\Approvals\Handlers\ExampleApprovalHandler for a full example.
 */
abstract class BaseApprovalHandler implements ApprovalHandlerInterface
{
    public function recipients(?Model $approvable, Approval $approval): array
    {
        return [];
    }

    public function onApproved(Approval $approval): void
    {
        //
    }

    public function onRejected(Approval $approval): void
    {
        //
    }
}
