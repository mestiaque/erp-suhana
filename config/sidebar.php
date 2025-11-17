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

    // Accounts Management
    [
        'group_title' => 'ACCOUNTS MANAGEMENT',
        [
            'title'      => 'Account Setup',
            'icon'       => 'fa-solid fa-cogs',
            'icon_color' => 'text-primary',
            'permission' => '',
            'children'   => [
                [
                    'title'       => 'Payment Method',
                    'icon'        => 'fa-solid fa-credit-card',
                    'route'       => '/admin/accounts/payment-methods',
                    'icon_color'  => 'text-primary',
                    'permission'  => ''
                ],
                [
                    'title'       => 'Account List',
                    'icon'        => 'fa-solid fa-list',
                    'route'       => '/admin/accounts/list',
                    'icon_color'  => 'text-primary',
                    'permission'  => ''
                ],
            ]
        ],
        [
            'title'      => 'Expenses',
            'icon'       => 'fa-solid fa-file-invoice-dollar',
            'icon_color' => 'text-warning',
            'permission' => '',
            'children'   => [
                [
                    'title'       => 'Expenses List',
                    'icon'        => 'fa-solid fa-list',
                    'route'       => '/admin/expenses',
                    'icon_color'  => 'text-warning',
                    'permission'  => ''
                ],
                [
                    'title'       => 'Expense Head',
                    'icon'        => 'fa-solid fa-layer-group',
                    'route'       => '/admin/expenses/types',
                    'icon_color'  => 'text-warning',
                    'permission'  => ''
                ],
                [
                    'title'       => 'Expense Reports',
                    'icon'        => 'fa-solid fa-layer-group',
                    'route'       => '/admin/expenses/reports',
                    'icon_color'  => 'text-warning',
                    'permission'  => ''
                ],
            ]
        ]
    ],

    // HR / User Management
    [
        'group_title' => 'HR / USER MANAGEMENT',
        [
            'title'      => 'User Management',
            'icon'       => 'fa-solid fa-users',
            'icon_color' => 'text-success',
            'permission' => '',
            'children'   => [
                [
                    'title'       => 'Admin List',
                    'icon'        => 'fa-solid fa-user-shield',
                    'route'       => '/admin/users/admin',
                    'icon_color'  => 'text-success',
                    'permission'  => ''
                ],
                [
                    'title'       => 'Staff List',
                    'icon'        => 'fa-solid fa-user-tie',
                    'route'       => '/admin/users/staff',
                    'icon_color'  => 'text-success',
                    'permission'  => ''
                ],
                [
                    'title'       => 'Employee List',
                    'icon'        => 'fa-solid fa-id-badge',
                    'route'       => '/admin/users/admin',
                    'icon_color'  => 'text-success',
                    'permission'  => ''
                ],
                [
                    'title'       => 'Roles Setup',
                    'icon'        => 'fa-solid fa-user-gear',
                    'route'       => '/admin/users/roles',
                    'icon_color'  => 'text-success',
                    'permission'  => ''
                ],
            ]
        ],
        [
            'title'      => 'HR Setup',
            'icon'       => 'fa-solid fa-briefcase',
            'icon_color' => 'text-info',
            'permission' => '',
            'children'   => [
                [
                    'title'       => 'Branch/Factory',
                    'icon'        => 'fa-solid fa-building',
                    'route'       => '/admin/hr/branchs',
                    'icon_color'  => 'text-info',
                    'permission'  => ''
                ],
                [
                    'title'       => 'Department',
                    'icon'        => 'fa-solid fa-sitemap',
                    'route'       => '/admin/hr/department',
                    'icon_color'  => 'text-info',
                    'permission'  => ''
                ],
                [
                    'title'       => 'Designation',
                    'icon'        => 'fa-solid fa-id-card-clip',
                    'route'       => '/admin/hr/designations',
                    'icon_color'  => 'text-info',
                    'permission'  => ''
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
                    'permission'  => ''
                ],
                [
                    'title'       => 'Mail Setting',
                    'icon'        => 'fa-solid fa-envelope',
                    'route'       => '/admin/setting/mail',
                    'icon_color'  => 'text-secondary',
                    'permission'  => ''
                ],
                [
                    'title'       => 'SMS Setting',
                    'icon'        => 'fa-solid fa-sms',
                    'route'       => '/admin/setting/sms',
                    'icon_color'  => 'text-secondary',
                    'permission'  => ''
                ],
            ]
        ],
    ],

];
