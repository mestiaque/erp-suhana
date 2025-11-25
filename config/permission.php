<?php

return [
    'modules' => [
        'Accounts Management' =>[
            'expenses' => [
                'label' => 'Expenses',
                'permissions' => [
                    'add' => 'Create',
                    'edit' => 'Edit',
                    'view' => 'View',
                    'delete' => 'Delete',
                    'all' => 'All',
                ],
            ],
            'expenses_type' => [
                'label' => 'Expenses Head',
                'permissions' => [
                    'add' => 'Create',
                    'edit' => 'Edit',
                    'delete' => 'Delete',
                    'all' => 'All',
                ],
            ],
            'expenses_report' => [
                'label' => 'Expenses Report',
                'permissions' => [
                    'view' => 'View',
                    'print' =>  'Print',
                    'all' => 'All',
                ],
            ],
            'iou' => [
                'label' => 'I.O.U',
                'permissions' => [
                    'add' => 'Create',
                    'edit' => 'Edit',
                    'view' => 'View',
                    'print' =>  'Print',
                    'delete' => 'Delete',
                    'all' => 'All',
                ],
            ],
            'iou_report' => [
                'label' => 'I.O.U Report',
                'permissions' => [
                    'view' => 'View',
                    'print' =>  'Print',
                    'all' => 'All',
                ],
            ],
            'payment_methods' => [
                'label' => 'Payment Methods',
                'permissions' => [
                    'add' => 'Create',
                    'edit' => 'Edit',
                    'view' => 'View',
                    'print' =>  'Print',
                    'delete' => 'Delete',
                    'all' => 'All',
                ],
            ],
            'accounts' => [
                'label' => 'Accounts',
                'permissions' => [
                    'add' => 'Create',
                    'edit' => 'Edit',
                    'view' => 'View',
                    'print' =>  'Print',
                    'delete' => 'Delete',
                    'all' => 'All',
                ],
            ],
            'bill_payments' => [
                'label' => 'Bill Payments',
                'permissions' => [
                    'add' => 'Create',
                    'edit' => 'Edit',
                    'view' => 'View',
                    'print' =>  'Print',
                    'delete' => 'Delete',
                    'all' => 'All',
                ],
            ],
            'bill_collections' => [
                'label' => 'Bill Collections',
                'permissions' => [
                    'add' => 'Create',
                    'edit' => 'Edit',
                    'view' => 'View',
                    'print' =>  'Print',
                    'delete' => 'Delete',
                    'all' => 'All',
                ],
            ],
            'deposits' => [
                'label' => 'Deposits',
                'permissions' => [
                    'add' => 'Create',
                    'edit' => 'Edit',
                    'delete' => 'Delete',
                    'all' => 'All',
                ],
            ],
            'withdrawal' => [
                'label' => 'Withdrawal',
                'permissions' => [
                    'add' => 'Create',
                    'edit' => 'Edit',
                    'delete' => 'Delete',
                    'all' => 'All',
                ],
            ],
            'statement' => [
                'label' => 'Statement',
                'permissions' => [
                    'view' => 'View',
                    'print' =>  'Print',
                ],
            ],

        ],

        // 'HR / User Management' =>[
        //     'expenses' => [
        //         'label' => 'Expenses',
        //         'permissions' => [
        //             'add' => 'Create',
        //             'edit' => 'Edit',
        //             'view' => 'View',
        //             'print' =>  'Print',
        //             'delete' => 'Delete',
        //             'all' => 'All',
        //         ],
        //     ],
        //     'expenses_type' => [
        //         'label' => 'Expenses Type',
        //         'permissions' => [
        //             'add' => 'Create',
        //             'edit' => 'Edit',
        //             'delete' => 'Delete',
        //             'all' => 'All',
        //         ],
        //     ],
        //     'expenses_report' => [
        //         'label' => 'Expenses Report',
        //         'permissions' => [
        //             'view' => 'View',
        //             'print' =>  'Print',
        //             'all' => 'All',
        //         ],
        //     ],
        //     'iou' => [
        //         'label' => 'I.O.U',
        //         'permissions' => [
        //             'add' => 'Create',
        //             'edit' => 'Edit',
        //             'view' => 'View',
        //             'print' =>  'Print',
        //             'delete' => 'Delete',
        //             'all' => 'All',
        //         ],
        //     ],
        //     'iou_report' => [
        //         'label' => 'I.O.U Report',
        //         'permissions' => [
        //             'view' => 'View',
        //             'print' =>  'Print',
        //             'all' => 'All',
        //         ],
        //     ],
        //     'payment_methods' => [
        //         'label' => 'Payment Methods',
        //         'permissions' => [
        //             'add' => 'Create',
        //             'edit' => 'Edit',
        //             'view' => 'View',
        //             'print' =>  'Print',
        //             'delete' => 'Delete',
        //             'all' => 'All',
        //         ],
        //     ],
        //     'accounts' => [
        //         'label' => 'Accounts',
        //         'permissions' => [
        //             'add' => 'Create',
        //             'edit' => 'Edit',
        //             'view' => 'View',
        //             'print' =>  'Print',
        //             'delete' => 'Delete',
        //             'all' => 'All',
        //         ],
        //     ],
        //     'bill_payments' => [
        //         'label' => 'Bill Payments',
        //         'permissions' => [
        //             'add' => 'Create',
        //             'edit' => 'Edit',
        //             'view' => 'View',
        //             'print' =>  'Print',
        //             'delete' => 'Delete',
        //             'all' => 'All',
        //         ],
        //     ],
        //     'bill_collections' => [
        //         'label' => 'Bill Collections',
        //         'permissions' => [
        //             'add' => 'Create',
        //             'edit' => 'Edit',
        //             'view' => 'View',
        //             'print' =>  'Print',
        //             'delete' => 'Delete',
        //             'all' => 'All',
        //         ],
        //     ],
        //     'deposits' => [
        //         'label' => 'Deposits',
        //         'permissions' => [
        //             'add' => 'Create',
        //             'edit' => 'Edit',
        //             'delete' => 'Delete',
        //             'all' => 'All',
        //         ],
        //     ],
        //     'withdrawal' => [
        //         'label' => 'Withdrawal',
        //         'permissions' => [
        //             'add' => 'Create',
        //             'edit' => 'Edit',
        //             'delete' => 'Delete',
        //             'all' => 'All',
        //         ],
        //     ],
        //     'statement' => [
        //         'label' => 'Statement',
        //         'permissions' => [
        //             'view' => 'View',
        //             'print' =>  'Print',
        //         ],
        //     ],

        // ]
    ],
];
