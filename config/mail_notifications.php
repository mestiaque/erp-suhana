<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail Notification Actions
    |--------------------------------------------------------------------------
    |
    | Every mail-sending action the admin can toggle on/off (and, for
    | approval actions, hand-pick recipients for) from Settings > Mail
    | Notifications. Keyed by the same "action_key" used in
    | App\Models\MailNotificationSetting.
    |
    | 'approval' => true marks an action whose key matches a module in
    | config/approval.php — its default recipient pool comes from that
    | module's handler at send time; the admin picker here just lets you
    | override which of the active users actually receive it.
    |
    */

    'actions' => [

        'inventory.purchase_requisition' => [
            'title' => 'Approval: Purchase Requisition',
            'group' => 'Approval',
            'approval' => true,
        ],
        'inventory.requisition' => [
            'title' => 'Approval: Requisition',
            'group' => 'Approval',
            'approval' => true,
        ],
        'inventory.grn_receive' => [
            'title' => 'Approval: GRN Receive',
            'group' => 'Approval',
            'approval' => true,
        ],
        'accounts.expense' => [
            'title' => 'Approval: Expense',
            'group' => 'Approval',
            'approval' => true,
        ],
        'accounts.balance_receive' => [
            'title' => 'Approval: Balance Receive',
            'group' => 'Approval',
            'approval' => true,
        ],

        'daily_summary_report' => [
            'title' => 'Daily Summary Report',
            'group' => 'Reports',
            'approval' => false,
        ],

    ],

];
