<?php

return [
    'modules' => [
        'HR/Users Management' =>[
            'admin' => [
                'label'       => 'Admin',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'roles' => [
                'label'       => 'Roles',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
        ],

        'Setting' =>[
            'general' => [
                'label'       => 'General',
                'permissions' => [
                    'list'   => 'List',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'mail' => [
                'label'       => 'Mail',
                'permissions' => [
                    'list' => 'List',
                    'edit' => 'Edit',
                    'view' => 'View',
                    'all'  => 'All',
                ],
            ],
            'sms' => [
                'label'       => 'SMS',
                'permissions' => [
                    'list' => 'List',
                    'edit' => 'Edit',
                    'view' => 'View',
                    'all'  => 'All',
                ],
            ],

        ],
        'Login History' =>[
            'login_history' => [
                'label'       => 'Login History',
                'permissions' => [
                    'all'    => 'All',
                    'list'   => 'List',
                    'view'   => 'View',
                    'dashboard' => 'Dashboard',
                ],
            ],
        ],
    ],
];
