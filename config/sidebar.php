<?php

return [

    // Dashboard & My Profile (single items)
    [
        'group_title' => 'MAIN',
        [
            'title'      => 'Dashboard',
            'icon'       => 'fa-solid fa-gauge-high',
            'route'      => '/admin/dashboard',
            'permission' => '',
        ],
        [
            'title'      => 'My Profile',
            'icon'       => 'fa-solid fa-user',
            'route'      => '/admin/my-profile',
            'permission' => '',
        ],
    ],

    // Purchases Management
    [
        'group_title' => '',
        [
            'title'      => 'Purchases Management',
            'icon'       => 'fa-solid fa-store',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                [
                    'title'       => 'Purchases',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/purchases-orders',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'purchases_orders'
                ],
                [
                    'title'       => 'Creditor List',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/suppliers',
                    'icon_color'  => 'text-primary',
                    'permission'  => 'creditor'
                ],
                [
                    'title'       => 'Goods Items',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/purchases-items',
                    'icon_color'  => 'text-primary',
                    'permission'  => 'purchases_items'
                ],

                [
                    'title'       => 'Requisitions',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/purchases-requisitions',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'purchases_requisitions'
                ],

                [
                    'title'       => 'Goods Receive (GRN)',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/purchases-received',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'purchases_received'
                ],
                [
                    'title'       => 'Damages / Returns',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/purchases-damage-returns',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'purchases_damage_returns'
                ],
                [
                    'title'       => 'Supplier Ledgers',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/suppliers-ladgers',
                    'icon_color'  => 'text-primary',
                    'permission'  => 'suppliers_ladgers'
                ],
                [
                    'title'       => 'Purchase Reports',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/purchases-reports',
                    'icon_color'  => 'text-primary',
                    'permission'  => 'purchases_reports'
                ],
                [
                    'title'       => 'Purchase Stock',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/purchases-stocks',
                    'icon_color'  => 'text-primary',
                    'permission'  => 'purchases_stocks'
                ]
            ]
        ],
    ],

    // Accounts Management
    [
        'group_title' => '',
        [
            'title'      => 'Accounts Management',
            'icon'       => 'fa-solid fa-cogs',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                [
                    'title'       => 'Expenses List',
                    'icon'        => 'fa-solid fa-list',
                    'route'       => '/admin/expenses',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'expenses'
                ],
                [
                    'title'       => 'Expense Head',
                    'icon'        => 'fa-solid fa-layer-group',
                    'route'       => '/admin/expenses/types',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'expenses_type'
                ],
                [
                    'title'       => 'Expense Reports',
                    'icon'        => 'fa-solid fa-layer-group',
                    'route'       => '/admin/expenses/reports',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'expenses_report'
                ],
                [
                    'title'       => 'I.O.U List',
                    'icon'        => 'fa-solid fa-layer-group',
                    'route'       => '/admin/expenses/iou',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'iou'
                ],
                [
                    'title'       => 'I.O.U Reports',
                    'icon'        => 'fa-solid fa-layer-group',
                    'route'       => '/admin/expenses/iou-reports',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'iou_report'
                ],
                [
                    'title'       => 'Payment Method',
                    'icon'        => 'fa-solid fa-credit-card',
                    'route'       => '/admin/accounts/payment-methods',
                    'icon_color'  => 'text-primary',
                    'permission'  => 'payment_methods'
                ],
                [
                    'title'       => 'Account List',
                    'icon'        => 'fa-solid fa-list',
                    'route'       => '/admin/accounts/list',
                    'icon_color'  => 'text-primary',
                    'permission'  => 'accounts'
                ],
                [
                    'title'       => 'Creditor Payment',
                    'icon'        => 'fas fa-credit-card',
                    'route'       => '/admin/bill-payments',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'bill_payments'
                ],
                [
                    'title'       => 'Bill Collection',
                    'icon'        => 'fas fa-wallet',
                    'route'       => '/admin/bill-collections',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'bill_collections'
                ],
                [
                    'title'       => 'Fund Receiver',
                    'icon'        => 'fas fa-wallet',
                    'route'       => '/admin/accounts/deposits',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'deposits'
                ],
                [
                    'title'       => 'Withdrawal',
                    'icon'        => 'fas fa-wallet',
                    'route'       => '/admin/accounts/withdrawal',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'withdrawal'
                ],
                [
                    'title'       => 'Statement',
                    'icon'        => 'fas fa-wallet',
                    'route'       => '/admin/accounts/statement',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'statement'
                ],
            ]
        ],
    ],

    // HR / User Management
    [
        'group_title' => '',
        [
            'title'      => 'HR / User Management',
            'icon'       => 'fa-solid fa-users',
            'icon_color' => 'text-success',
            'permission' => '',
            'children'   => [
                [
                    'title'       => 'Employee List',
                    'icon'        => 'fa-solid fa-id-badge',
                    'route'       => '/admin/users/employee',
                    'icon_color'  => 'text-success',
                    'permission'  => 'employee'
                ],
                [
                    'title'       => 'Staff List',
                    'icon'        => 'fa-solid fa-user-tie',
                    'route'       => '/admin/users/staff',
                    'icon_color'  => 'text-success',
                    'permission'  => 'staff'
                ],
                [
                    'title'       => 'Admin List',
                    'icon'        => 'fa-solid fa-user-shield',
                    'route'       => '/admin/users/admin',
                    'icon_color'  => 'text-success',
                    'permission'  => 'admin'
                ],
                [
                    'title'       => 'Roles Setup',
                    'icon'        => 'fa-solid fa-user-gear',
                    'route'       => '/admin/users/roles',
                    'icon_color'  => 'text-success',
                    'permission'  => 'roles'
                ],
                [
                    'title'       => 'Branch/Factory',
                    'icon'        => 'fa-solid fa-building',
                    'route'       => '/admin/hr/branchs',
                    'icon_color'  => 'text-info',
                    'permission'  => 'branchs'
                ],
                [
                    'title'       => 'Departments',
                    'icon'        => 'fa-solid fa-sitemap',
                    'route'       => '/admin/hr/departments',
                    'icon_color'  => 'text-info',
                    'permission'  => 'departments'
                ],
                [
                    'title'       => 'Designation',
                    'icon'        => 'fa-solid fa-id-card-clip',
                    'route'       => '/admin/hr/designations',
                    'icon_color'  => 'text-info',
                    'permission'  => 'designations'
                ],
            ]
        ],
    ],



    [
        'group_title' => 'PRODUCTION WORKFLOW',
        [
            'title'      => 'Order Management',
            'icon'       => 'fa-solid fa-shopping-cart',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                ['title' => 'Buyer List', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/buyers', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Order Entry', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/orders/create', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Order Status', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/orders/status', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Order History', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/orders/history', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Order Approval', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/orders/approval', 'icon_color' => 'text-warning', 'permission' => ''],
            ]
        ],
        [
            'title'      => 'Merchandising',
            'icon'       => 'fa-solid fa-tags',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                ['title' => 'Style / Product Entry', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/styles', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'BOM (Bill of Materials)', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/bom', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Costing & Quotation', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/costing', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Sample Approval', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/samples/approval', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Fabric & Trim Requirement', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/fabrics/requirement', 'icon_color' => 'text-warning', 'permission' => ''],
            ]
        ],
        [
            'title'      => 'Planning',
            'icon'       => 'fa-solid fa-calendar',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                ['title' => 'Production Plan', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/planning/production', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Cutting Plan', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/planning/cutting', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Line Assignment', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/planning/line-assignment', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Capacity Planning', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/planning/capacity', 'icon_color' => 'text-warning', 'permission' => ''],
            ]
        ],
        [
            'title'      => 'Cutting',
            'icon'       => 'fa-solid fa-cut',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                ['title' => 'Cutting Plan', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/cutting/plan', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Cut Panel / Bundle Entry', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/cutting/panel', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Cutting Output', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/cutting/output', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Fabric Consumption Report', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/cutting/report', 'icon_color' => 'text-warning', 'permission' => ''],
            ]
        ],
        [
            'title'      => 'Sewing / Production',
            'icon'       => 'fa-solid fa-industry',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                ['title' => 'Line / Operator Assignment', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/production/line-assignment', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Daily Target vs Output', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/production/daily-target', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Defect Entry', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/production/defect-entry', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Rework Entry', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/production/rework-entry', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Bundle / Cartoon Tracking', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/production/bundle-tracking', 'icon_color' => 'text-warning', 'permission' => ''],
            ]
        ],
        [
            'title'      => 'Finishing & Packing',
            'icon'       => 'fa-solid fa-archive',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                ['title' => 'Quality Check (QC)', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/finishing/qc', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Ironing / Pressing', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/finishing/ironing', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Packing Entry', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/finishing/packing', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Shipment Ready Status', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/finishing/shipment', 'icon_color' => 'text-warning', 'permission' => ''],
            ]
        ],
        [
            'title'      => 'Shipping',
            'icon'       => 'fa-solid fa-ship',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                ['title' => 'Shipment Schedule', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/shipping/schedule', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Shipment Tracking', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/shipping/tracking', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Invoice / Packing List', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/shipping/invoice', 'icon_color' => 'text-warning', 'permission' => ''],
            ]
        ],
        [
            'title'      => 'Machines / Lines',
            'icon'       => 'fa-solid fa-cogs',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                ['title' => 'Machine List', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/machines', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Line List', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/lines', 'icon_color' => 'text-warning', 'permission' => ''],
            ]
        ],
        [
            'title'      => 'Units / Sizes / Colors',
            'icon'       => 'fa-solid fa-palette',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                ['title' => 'Unit List', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/units', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Size List', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/sizes', 'icon_color' => 'text-warning', 'permission' => ''],
                ['title' => 'Color List', 'icon' => 'fa-solid fa-arrow-right', 'route' => '/admin/colors', 'icon_color' => 'text-warning', 'permission' => ''],
            ]
        ],
    ],


    // App Settings
    [
        'group_title' => 'APP SETTING',
        [
            'title'      => 'Setting',
            'icon'       => 'fa-solid fa-sliders-h',
            'icon_color' => 'text-secondary',
            'permission' => '',
            'children'   => [
                [
                    'title'       => 'General Setting',
                    'icon'        => 'fa-solid fa-cog',
                    'route'       => '/admin/setting/general',
                    'icon_color'  => 'text-secondary',
                    'permission'  => 'general'
                ],
                [
                    'title'       => 'Mail Setting',
                    'icon'        => 'fa-solid fa-envelope',
                    'route'       => '/admin/setting/mail',
                    'icon_color'  => 'text-secondary',
                    'permission'  => 'mail'
                ],
                [
                    'title'       => 'SMS Setting',
                    'icon'        => 'fa-solid fa-sms',
                    'route'       => '/admin/setting/sms',
                    'icon_color'  => 'text-secondary',
                    'permission'  => 'sms'
                ],
            ]
        ],
    ],

];
