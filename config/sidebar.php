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

    // Production Management
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
                    'title'      => 'Line Loading Plan',
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
                    'title'      => 'Finishing',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/finishing',
                    'icon_color' => 'text-warning',
                    'permission' => 'finishing'
                ],
                [
                    'title'      => 'Iron',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/iron',
                    'icon_color' => 'text-warning',
                    'permission' => 'iron'
                ],
                [
                    'title'      => 'Poly',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/poly',
                    'icon_color' => 'text-warning',
                    'permission' => 'poly'
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

    // Commercial Management
    [
        'group_title' => '',
        [
            'title'      => 'Commercial',
            'icon'       => 'fa-solid fa-file-invoice-dollar',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                [
                    'title'       => 'Bank BTB LC',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/commercial/btb-lc',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'general'
                ],
                [
                    'title'      => 'Export LC/Sales Contact',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/commercial/export-lc',
                    'icon_color' => 'text-warning',
                    'permission' => 'general'
                ],
                [
                    'title'      => 'Purchase Order (PO)',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/commercial/purchase-orders',
                    'icon_color' => 'text-warning',
                    'permission' => 'general'
                ],
                [
                    'title'      => 'Proforma Invoice (PI)',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/commercial/proforma-invoice',
                    'icon_color' => 'text-warning',
                    'permission' => 'general'
                ],
                [
                    'title'       => 'Pricing List',
                    'icon'        => 'fa-solid fa-arrow-right',
                    'route'       => '/admin/commercial/pricing-list',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'general'
                ],
                [
                    'title'      => 'Commercial Invoice',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/commercial/invoices',
                    'icon_color' => 'text-warning',
                    'permission' => 'general'
                ],
                [
                    'title'      => 'Packing List',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/commercial/packing-list',
                    'icon_color' => 'text-warning',
                    'permission' => 'general'
                ],
                [
                    'title'      => 'Shipping Bill/Docs',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/commercial/shipping-docs',
                    'icon_color' => 'text-warning',
                    'permission' => 'general'
                ],
                [
                    'title'      => 'Export Realization',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/commercial/export-realization',
                    'icon_color' => 'text-warning',
                    'permission' => 'general'
                ],
                // [
                //     'title'      => 'Commercial Reports',
                //     'icon'       => 'fa-solid fa-arrow-right',
                //     'route'      => '/admin/commercial/reports',
                //     'icon_color' => 'text-warning',
                //     'permission' => 'general'
                // ],
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
                    'title'       => 'Completed I.O.U List',
                    'icon'        => 'fa-solid fa-layer-group',
                    'route'       => '/admin/expenses/completed-iou',
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
                    'title'       => 'All Creditor Transactions',
                    'icon'        => 'fas fa-credit-card',
                    'route'       => '/admin/bill-payments',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'bill_payments'
                ],
                [
                    'title'       => 'Fund Received',
                    'icon'        => 'fas fa-wallet',
                    'route'       => '/admin/accounts/deposits',
                    'icon_color'  => 'text-warning',
                    'permission'  => 'deposits'
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

    // Payroll & HR Management
    [
        'group_title' => 'PAYROLL & HR MANAGEMENT',
        // Employee
        [
            'title' => 'Employee Management',
            'icon' => 'fa fa-user-circle',
            'permission' => 'dev',
            'children' => [
                [
                    'title' => 'Employees',
                    'icon' => 'fa fa-user-circle',
                    'route' => 'admin/users/employee',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Employee Report',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/reports/employees',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Employee Performance',
                    'icon' => 'fa fa-chart-line',
                    'route' => 'admin/performance',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Probation',
                    'icon' => 'fa fa-user-clock',
                    'route' => 'admin/probations',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Termination',
                    'icon' => 'fa fa-user-times',
                    'route' => 'admin/terminations',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Retirement',
                    'icon' => 'fa fa-user-clock',
                    'route' => 'admin/retirement',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Interviews',
                    'icon' => 'fa fa-user-tie',
                    'route' => 'admin/interviews',
                    'permission' => 'dev',
                ],
            ]
        ],

        // Employee Structure
        [
            'title' => 'Employee Structure',
            'icon' => 'fa fa-sitemap',
            'permission' => 'dev',
            'children' => [
                [
                    'title' => 'Employee Type',
                    'icon' => 'fa fa-id-badge',
                    'route' => 'admin/hr/employee-types',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Department',
                    'icon' => 'fa fa-building',
                    'route' => 'admin/hr/departments',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Designation',
                    'icon' => 'fa fa-briefcase',
                    'route' => 'admin/hr/designations',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Division',
                    'icon' => 'fa fa-sitemap',
                    'route' => 'admin/hr/divisions',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Grade',
                    'icon' => 'fa fa-graduation-cap',
                    'route' => 'admin/hr/grades',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Line Number',
                    'icon' => 'fa fa-list-ol',
                    'route' => 'admin/hr/line-numbers',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Section',
                    'icon' => 'fa fa-th-large',
                    'route' => 'admin/hr/sections',
                    'permission' => 'dev',
                ],
            ]
        ],

        // Payroll
        [
            'title' => 'Payroll Management',
            'icon' => 'fa fa-users',
            'permission' => 'dev',
            'children' => [
                [
                    'title' => 'Salary Sheet',
                    'icon' => 'fa fa-file-invoice-dollar',
                    'route' => 'admin/payroll/salary-sheet',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Salary Summary',
                    'icon' => 'fa fa-file-invoice-dollar',
                    'route' => 'admin/payroll/salary-summary',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Payroll Report',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/reports/payroll',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Salary Advance',
                    'icon' => 'fa fa-money-bill-wave',
                    'route' => 'admin/salary-advance',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Deductions',
                    'icon' => 'fa fa-minus-circle',
                    'route' => 'admin/deductions',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Bonus',
                    'icon' => 'fa fa-gift',
                    'route' => 'admin/bonus',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Tax',
                    'icon' => 'fa fa-percentage',
                    'route' => 'admin/tax',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Loan',
                    'icon' => 'fa fa-hand-holding-usd',
                    'route' => 'admin/loan',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Provident Fund',
                    'icon' => 'fa fa-piggy-bank',
                    'route' => 'admin/provident-fund',
                    'permission' => 'dev',
                ],
            ]
        ],

        // Attendance
        [
            'title' => 'Attendance Management',
            'icon' => 'fa fa-calendar-minus',
            'permission' => 'dev',
            'children' => [
                [
                    'title' => 'Attendance',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/daily-attendance',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Department Attendance',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/daily-attendance-department-summary',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Individual Report',
                    'icon' => 'fa fa-user',
                    'route' => 'admin/attendance/individual-report',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Monthly Summary',
                    'icon' => 'fa fa-calendar',
                    'route' => 'admin/attendance/monthly-summary',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Manual Attendance',
                    'icon' => 'fa fa-calendar-plus',
                    'route' => 'admin/attendance/manual',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Roaster Management',
                    'icon' => 'fa fa-clock',
                    'route' => 'admin/attendance/roaster',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Working Hours',
                    'icon' => 'fa fa-hourglass-half',
                    'route' => 'admin/working-hours',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Attendance Approval',
                    'icon' => 'fa fa-check-circle',
                    'route' => 'admin/attendance-approval',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Live Location',
                    'icon' => 'fa fa-map-marker',
                    'route' => 'admin/live-location-tracking',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Overtime',
                    'icon' => 'fa fa-business-time',
                    'route' => 'admin/overtimes',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Attendance Machine Logs',
                    'icon' => 'fa fa-fingerprint',
                    'route' => 'admin/attendance/machine-log',
                    'permission' => 'dev',
                ],
            ]
        ],

        // Leave
        [
            'title' => 'Leave Management',
            'icon' => 'fa fa-calendar-minus',
            'permission' => 'dev',
            'children' => [
                [
                    'title' => 'Leave Applications',
                    'icon' => 'fa fa-calendar-minus',
                    'route' => 'admin/leaves',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Leave Types',
                    'icon' => 'fa fa-calendar-check',
                    'route' => 'admin/leaves/types',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Leave Report',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/leaves/report',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Leave Summary',
                    'icon' => 'fa fa-bar-chart',
                    'route' => 'admin/leaves/summary',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Holiday',
                    'icon' => 'fa fa-calendar-day',
                    'route' => 'admin/holidays',
                    'permission' => 'dev',
                ],
            ]
        ],

        // User Management
        [
            'title' => 'User Management',
            'icon' => 'fa fa-user',
            'permission' => 'dev',
            'children' => [
                [
                    'title' => 'Admins',
                    'icon' => 'fa fa-user-shield',
                    'route' => 'admin/users/admin',
                    'permission' => 'users',
                ],
                [
                    'title' => 'Roles',
                    'icon' => 'fa fa-lock',
                    'route' => 'admin/users/roles',
                    'permission' => 'roles',
                ],
            ]
        ],

        // Documents
        [
            'title' => 'Document Management',
            'icon' => 'fa fa-folder',
            'permission' => 'dev',
            'children' => [
                [
                    'title' => 'ID Card',
                    'icon' => 'fa fa-id-card',
                    'route' => 'admin/idcard',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Pay Slip',
                    'icon' => 'fa fa-receipt',
                    'route' => 'admin/documents/pay-slip',
                    'permission' => 'pay_slip',
                ],
                [
                    'title' => 'Personal Info',
                    'icon' => 'fa fa-user',
                    'route' => 'admin/documents/personal-info',
                    'permission' => 'personal_info',
                ],
                [
                    'title' => 'Job Card',
                    'icon' => 'fa fa-id-card',
                    'route' => 'jobcard.index',
                    'permission' => 'dev',
                ]
            ]
        ],

        // Letters
        [
            'title' => 'Letters Management',
            'icon' => 'fa fa-envelope-open',
            'permission' => 'dev',
            'children' => [
                [
                    'title' => 'Appointment Letters',
                    'icon' => 'fa fa-envelope',
                    'route' => 'admin/letters/appointment',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Joining Letters',
                    'icon' => 'fa fa-sign-in',
                    'route' => 'admin/letters/joining',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Confirmation Letters',
                    'icon' => 'fa fa-check-circle',
                    'route' => 'admin/letters/confirmation',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Salary Increments',
                    'icon' => 'fa fa-arrow-up',
                    'route' => 'admin/letters/increment',
                    'permission' => 'dev',
                ],
            ]
        ],

        // Assets
        [
            'title' => 'Assets',
            'icon' => 'fa fa-laptop',
            'permission' => 'dev',
            'children' => [
                [
                    'title' => 'Asset List',
                    'icon' => 'fa fa-list',
                    'route' => 'admin/assetss',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Distribution',
                    'icon' => 'fa fa-share-alt',
                    'route' => 'admin/assets/distribution',
                    'permission' => 'dev',
                ],
            ]
        ],

        // Policies & Requests
        [
            'title' => 'Policies & Requests',
            'icon' => 'fa fa-folder-open',
            'permission' => 'dev',
            'children' => [
                [
                    'title' => 'Policy',
                    'icon' => 'fa fa-balance-scale',
                    'route' => 'admin/policy',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Convenience Request',
                    'icon' => 'fa fa-file-signature',
                    'route' => 'admin/convenience',
                    'permission' => 'dev',
                ]
            ]
        ],

        // Notice
        [
            'title' => 'Notice Board',
            'icon' => 'fa fa-bullhorn',
            'permission' => 'dev',
            'children' => [
                [
                    'title' => 'Notices',
                    'icon' => 'fa fa-bullhorn',
                    'route' => 'admin/notices',
                    'permission' => 'dev',
                ],
            ]
        ],

        // System / Other
        [
            'title' => 'ZKTeco Integration',
            'icon' => 'fa-brands fa-accusoft',
            'permission' => 'dev',
            'children' => [
                [
                    'title' => 'Data Import',
                    'icon' => 'fa fa-file-import',
                    'route' => 'admin/zkteco-data-import',
                    'permission' => 'dev',
                ],
                [
                    'title' => 'Software Integration',
                    'icon' => 'fa fa-download',
                    'route' => 'admin/zkteco-software-integration',
                    'permission' => 'dev',
                ]
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
                // [
                //     'title'       => 'Staff List',
                //     'icon'        => 'fa-solid fa-user-tie',
                //     'route'       => '/admin/users/staff',
                //     'icon_color'  => 'text-success',
                //     'permission'  => 'staff'
                // ],
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
                    'title' => 'Employee Type',
                    'icon' => 'fa fa-id-badge',
                    'route' => 'admin/hr/employee-types',
                    'permission' => 'departments',
                ],
                [
                    'title'       => 'Departments',
                    'icon'        => 'fa-solid fa-sitemap',
                    'route'       => '/admin/hr/departments',
                    'permission'  => 'departments'
                ],
                [
                    'title' => 'Division',
                    'icon' => 'fa fa-sitemap',
                    'route' => 'admin/hr/divisions',
                    'permission' => 'departments',
                ],
                [
                    'title' => 'Grade',
                    'icon' => 'fa fa-graduation-cap',
                    'route' => 'admin/hr/grades',
                    'permission' => 'departments',
                ],
                [
                    'title' => 'Section',
                    'icon' => 'fa fa-th-large',
                    'route' => 'admin/hr/sections',
                    'permission' => 'departments',
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
                [
                    'title'       => 'Shift',
                    'icon'        => 'fa-solid fa-clock',
                    'route'       => '/admin/hr/shifts',
                    'icon_color'  => 'text-info',
                    'permission'  => 'designations'
                ]
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
                [
                    'title'       => 'Roadmap',
                    'icon'        => 'fa-solid fa-route',
                    'route'       => '/admin/roadmap',
                    'icon_color'  => 'text-secondary',
                    'permission'  => 'sms'
                ],
            ]
        ],
    ],
];
