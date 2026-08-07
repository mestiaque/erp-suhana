<?php

return [
    'modules' => [
        'Users Management' =>[
            'user_dashboard' => [
                'label' => 'User Dashboard',
                'permissions' => ['view' => 'View', 'all' => 'All'],
            ],
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
            'login_history' => [
                'label'       => 'Login History',
                'permissions' => [
                    'all'    => 'All',
                    'list'   => 'List',
                    'view'   => 'View',
                    'dashboard' => 'Dashboard',
                ],
            ],
            'data_change_log' => [
                'label'       => 'Data Change Log',
                'permissions' => [
                    'all'    => 'All',
                    'list'   => 'List',
                    'view'   => 'View',
                ],
            ],
            'approvals' => [
                'label'       => 'Approvals',
                'permissions' => [
                    'all'     => 'All',
                    'list'    => 'List',
                    'approve' => 'Approve',
                    'reject'  => 'Reject',
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
    ],
];
