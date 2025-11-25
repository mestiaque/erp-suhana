<?php

return [
    'modules' => [

        'Purchases Management' =>[
            'purchases_orders' => [
                'label'       => 'Purchases Orders',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'creditor' => [
                'label'       => 'Creditor',
                'permissions' => [
                    'add'     => 'Create',
                    'edit'    => 'Edit',
                    'view'    => 'View',
                    'delete'  => 'Delete',
                    'payment' => 'Payment',
                    'all'     => 'All',
                ],
            ],
            'purchases_items' => [
                'label'       => 'Goods Items',
                'permissions' => [
                    'add'     => 'Create',
                    'edit'    => 'Edit',
                    'view'    => 'View',
                    'delete'  => 'Delete',
                    'all'     => 'All',
                ],
            ],
            'purchases_items_units' => [
                'label'       => 'Goods Items (Unit)',
                'permissions' => [
                    'add'     => 'Create',
                    'edit'    => 'Edit',
                    'view'    => 'View',
                    'delete'  => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'purchases_items_categories' => [
                'label'       => 'Goods Items (Categories)',
                'permissions' => [
                    'add'     => 'Create',
                    'edit'    => 'Edit',
                    'view'    => 'View',
                    'delete'  => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'purchases_requisitions' => [
                'label'       => 'Purchases Requisition',
                'permissions' => [
                    'add'     => 'Create',
                    'edit'    => 'Edit',
                    'view'    => 'View',
                    'delete'  => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'purchases_received' => [
                'label'       => 'Purchases Received',
                'permissions' => [
                    'add'     => 'Create',
                    'edit'    => 'Edit',
                    'view'    => 'View',
                    'delete'  => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'purchases_damage_returns' => [
                'label'       => 'Purchases Damage Returns',
                'permissions' => [
                    'add'     => 'Create',
                    'edit'    => 'Edit',
                    'view'    => 'View',
                    'delete'  => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'suppliers_ladgers' => [
                'label'       => 'Suppliers ladgers',
                'permissions' => [
                    'view'    => 'View',
                    'all'    => 'All',
                ],
            ],
            'purchases_reports' => [
                'label'       => 'Purchases Reports',
                'permissions' => [
                    'view'    => 'View',
                    'all'    => 'All',
                ],
            ],
            'purchases_stocks' => [
                'label'       => 'Purchases Stock',
                'permissions' => [
                    'view'    => 'View',
                    'all'    => 'All',
                ],
            ],
        ],

        'Accounts Management' =>[
            'expenses' => [
                'label'       => 'Expenses',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'expenses_type' => [
                'label'       => 'Expenses Head',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'expenses_report' => [
                'label'       => 'Expenses Report',
                'permissions' => [
                    'view'  => 'View',
                    'all'   => 'All',
                ],
            ],
            'iou' => [
                'label'       => 'I.O.U',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'iou_report' => [
                'label'       => 'I.O.U Report',
                'permissions' => [
                    'view'  => 'View',
                    'all'   => 'All',
                ],
            ],
            'payment_methods' => [
                'label'       => 'Payment Methods',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'accounts' => [
                'label'       => 'Accounts',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'bill_payments' => [
                'label'       => 'Bill Payments',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'bill_collections' => [
                'label'       => 'Bill Collections',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'deposits' => [
                'label'       => 'Deposits',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'withdrawal' => [
                'label'       => 'Withdrawal',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'statement' => [
                'label'       => 'Statement',
                'permissions' => [
                    'view'  => 'View',
                    'all'    => 'All',
                ],
            ],

        ],

        'HR/Users Management' =>[
            'employee' => [
                'label'       => 'Employee',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'staff' => [
                'label'       => 'Staff',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'admin' => [
                'label'       => 'Admin',
                'permissions' => [
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
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'branchs' => [
                'label'       => 'Branchs',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'departments' => [
                'label'       => 'Departments',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'designations' => [
                'label'       => 'Designations',
                'permissions' => [
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
        ],

        'Setting' =>[
            'general' => [
                'label'       => 'General',
                'permissions' => [
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'mail' => [
                'label'       => 'Mail',
                'permissions' => [
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'all'    => 'All',
                ],
            ],
            'sms' => [
                'label'       => 'SMS',
                'permissions' => [
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'all'    => 'All',
                ],
            ],

        ],
    ],
];
