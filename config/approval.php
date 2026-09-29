<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Approval Modules
    |--------------------------------------------------------------------------
    |
    | Every feature across every package (HR, Commercial, Inventory,
    | Production, Accounts, ...) that needs an approval flow registers
    | itself here, keyed by a unique module name, mapped to a handler
    | class implementing App\Contracts\ApprovalHandlerInterface (usually by
    | extending App\Approvals\BaseApprovalHandler).
    |
    | The handler decides WHO gets emailed for that module, and WHAT
    | happens when a request is approved/rejected. Nothing else needs to
    | change centrally when a new module adopts approvals.
    |
    | Class strings only (no closures) — this file must stay safe for
    | `php artisan config:cache`.
    |
    | Example:
    |   'hr.leave_request' => \App\Approvals\Handlers\LeaveRequestApprovalHandler::class,
    |
    | See App\Approvals\Handlers\ExampleApprovalHandler for a full template
    | and App\Services\ApprovalService for how to raise a request.
    |
    */
    'modules' => [

        // 'hr.leave_request'        => \App\Approvals\Handlers\ExampleApprovalHandler::class,
        // 'commercial.master_lc'    => \App\Approvals\Handlers\ExampleApprovalHandler::class,

        'inventory.purchase_requisition' => \ME\SflInventory\Approvals\InvPurchaseRequisitionApprovalHandler::class,
        'inventory.requisition' => \ME\SflInventory\Approvals\InvRequisitionApprovalHandler::class,
        'inventory.grn_receive' => \ME\SflInventory\Approvals\InvGrnApprovalHandler::class,
        'accounts.expense' => \ME\AccSfl\Approvals\ExpenseApprovalHandler::class,
        'accounts.balance_receive' => \ME\AccSfl\Approvals\BalanceReceiveApprovalHandler::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Mail
    |--------------------------------------------------------------------------
    |
    | Leave these null to fall back to the app's default mail "from"
    | address/name (config/mail.php, populated from Settings > General).
    |
    */
    'mail_from_address' => null,
    'mail_from_name'    => null,

    /*
    |--------------------------------------------------------------------------
    | Test Recipients
    |--------------------------------------------------------------------------
    |
    | Comma-separated addresses (APPROVAL_TEST_RECIPIENTS in .env). While set,
    | EVERY email the app sends — approval emails and any other mail, from
    | any module — goes only to these addresses (see AppServiceProvider::
    | redirectAllMailInTestMode()). Leave empty in production.
    |
    */
    'test_recipients' => array_values(array_filter(array_map('trim', explode(',', (string) env('APPROVAL_TEST_RECIPIENTS', ''))))),

];
