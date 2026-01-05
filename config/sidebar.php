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
                    'title'       => 'Creditor Ledgers',
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

    // Merchandising Management
    [
        'group_title' => '',
        [
            'title'      => 'Merchandising',
            'icon'       => 'fa-solid fa-cart-shopping',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                [
                    'title'       => 'Sample',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/samples',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'samples'
                ],
                [
                    'title'      => 'Order Details',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/order-details',
                    'icon_color' => 'text-warning',
                    'permission' => 'order_details'
                ],
                [
                    'title'      => 'Proforma Invoice (PI)',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/proforma-invoice',
                    'icon_color' => 'text-warning',
                    'permission' => 'proforma_invoice'
                ],
                [
                    'title'      => 'Budget',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/budget',
                    'icon_color' => 'text-warning',
                    'permission' => 'budget'
                ],
                [
                    'title'       => 'Reference Data',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'icon_color'  => 'text-warning',
                    'permission'  => '',
                    'children'    => [
                        [
                            'title'       => 'Fabrications',
                            'icon'        => 'fa-solid fa-arrow-right',
                            'route'       => '/admin/fabrications',
                            'icon_color'  => 'text-warning',
                            'permission'  => 'fabrications'
                        ],
                        [
                            'title'       => 'Compositions',
                            'icon'        => 'fa-solid fa-arrow-right',
                            'route'       => '/admin/compositions',
                            'icon_color'  => 'text-warning',
                            'permission'  => 'compositions'
                        ],
                        [
                            'title'      => 'Buyer List',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/buyers',
                            'icon_color' => 'text-warning',
                            'permission' => 'buyers'
                        ],
                    ]
                ],
            ]
        ],
    ],
    // Merchandising Management
    [
        'group_title' => '',
        [
            'title'      => 'Procurement',
            'icon'       => 'fa-solid fa-layer-group',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                [
                    'title'       => 'Booking',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'icon_color'  => 'text-warning',
                    'permission'  => '',
                    'children'    => [
                        [
                            'title'       => 'Yarn',
                            'icon'        => 'fa-solid fa-arrow-right',
                            'route'       => '/admin/procurement/yarn-booking',
                            'icon_color'  => 'text-warning',
                            'permission'  => 'yarn_booking'
                        ],
                        [
                            'title'       => 'knitting',
                            'icon'        => 'fa-solid fa-arrow-right',
                            'route'       => '/admin/procurement/knitting-booking',
                            'icon_color'  => 'text-warning',
                            'permission'  => 'knitting_booking'
                        ],
                        [
                            'title'       => 'Dyeing',
                            'icon'        => 'fa-solid fa-arrow-right',
                            'route'       => '/admin/procurement/dyeing-booking',
                            'icon_color'  => 'text-warning',
                            'permission'  => 'dyeing_booking'
                        ],
                    ]
                ],
                [
                    'title'       => 'Receiving',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'icon_color'  => 'text-warning',
                    'permission'  => '',
                    'children'    => [
                        [
                            'title'       => 'Yarn',
                            'icon'        => 'fa-solid fa-arrow-right',
                            'route'       => '/admin/procurement/yarn-receive',
                            'icon_color'  => 'text-warning',
                            'permission'  => 'yarn_booking'
                        ],
                        [
                            'title'       => 'knitting',
                            'icon'        => 'fa-solid fa-arrow-right',
                            'route'       => '/admin/procurement/knitting-receive',
                            'icon_color'  => 'text-warning',
                            'permission'  => 'knitting_booking'
                        ],
                        [
                            'title'       => 'Dyeing',
                            'icon'        => 'fa-solid fa-arrow-right',
                            'route'       => '/admin/procurement/dyeing-receive',
                            'icon_color'  => 'text-warning',
                            'permission'  => 'dyeing_booking'
                        ],
                    ]
                ],
                [
                    'title'      => 'Status',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/procurement/pi-wise-fabric-status',
                    'icon_color' => '',
                    'permission' => 'pi_wise_fabric_status'
                ],

            ]
        ],
    ],

    [
        'group_title' => '',
        [
            'title'      => 'Production',
            'icon'       => 'fa-solid fa-layer-group',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                [
                    'title'       => 'Master Plan',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/production-planning',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'production_planning'
                ],
                [
                    'title'      => 'Floor Plan',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/floor-planning',
                    'icon_color' => 'text-warning',
                    'permission' => 'floor_planning'
                ],
                [
                    'title'      => 'Cutting',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/cutting',
                    'icon_color' => 'text-warning',
                    'permission' => 'cutting'
                ],
                [
                    'title'      => 'Sweing',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/daily-production',
                    'icon_color' => 'text-warning',
                    'permission' => 'sweing'
                ],


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
                    'title'       => 'Creditor List',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/suppliers',
                    'icon_color'  => 'text-primary',
                    'permission'  => 'creditor'
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
                    'title'       => 'Fund Received',
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
                    'title'      => 'Merchandiser List',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/merchandisers',
                    'icon_color' => 'text-warning',
                    'permission' => 'merchandisers'
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
                [
                    'title'       => 'Floor/Lines',
                    'icon'        => 'fa-solid fa-building',
                    'route'       => '/admin/hr/floor-lines',
                    'icon_color'  => 'text-info',
                    'permission'  => 'designations'
                ],
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
