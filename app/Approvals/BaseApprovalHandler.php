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

    /**
     * What the approval email shows for this module — every module's email
     * uses the one shared template (resources/views/emails/approval-request).
     * Return any of:
     *   'badge'   => 'PURCHASE REQUISITION',          // label top-right
     *   'number'  => 'PREQ-000016', 'date' => '28.09.2026',
     *   'meta'    => ['Requested by' => 'Name', ...], // label => value
     *   'columns' => [['label' => 'Item'], ['label' => 'Qty', 'align' => 'right'], ...],
     *   'rows'    => [['Item text' or ['text' => .., 'sub' => ..], '10', ...], ...],
     *   'total'   => ['label' => 'Total', 'value' => 4255.0, 'money' => true],
     *   'notes'   => ['Justification' => '...'],
     * Empty = just the request's title and description.
     */
    public function mailContent(?Model $approvable, Approval $approval): array
    {
        return [];
    }
}
