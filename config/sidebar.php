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
            'order'      => 1,
        ],
        [
            'title'      => 'My Profile',
            'icon'       => 'fa-solid fa-user',
            'route'      => '/admin/my-profile',
            'permission' => '',
            'order'      => 2,
        ],
        [
            'title'      => 'Login History',
            'icon'       => 'fa-solid fa-clock-rotate-left',
            'route'      => '/admin/login-history',
            'permission' => 'login_history',
            'order'      => 3,
        ],
    ],



    // HR / User Management
    [
        'group_title' => '',
        [
            'title'      => 'User Management',
            'icon'       => 'fa-solid fa-users',
            'icon_color' => 'text-success',
            'permission' => '',
            'order'      => 9,
            'children'   => [

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
            'order'      => 999,
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


