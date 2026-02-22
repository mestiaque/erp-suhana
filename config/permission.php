<?php

return [
    'modules' => [

        'Purchases Management' =>[
            'purchases_orders' => [
                'label'       => 'Purchases Orders',
                'permissions' => [
                    'list'   => 'List',
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
                    'list'    => 'List',
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
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'purchases_items_units' => [
                'label'       => 'Goods Items (Unit)',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create/Edit',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'purchases_items_categories' => [
                'label'       => 'Goods Items (Categories)',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create/Edit',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'purchases_requisitions' => [
                'label'       => 'Purchases Requisition',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'purchases_received' => [
                'label'       => 'Purchases Received',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'purchases_damage_returns' => [
                'label'       => 'Purchases Damage Returns',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'suppliers_ladgers' => [
                'label'       => 'Suppliers ladgers',
                'permissions' => [
                    'list' => 'List',
                    'all'  => 'All',
                ],
            ],
            'purchases_reports' => [
                'label'       => 'Purchases Reports',
                'permissions' => [
                    'list' => 'List',
                    'all'  => 'All',
                ],
            ],
            'purchases_stocks' => [
                'label'       => 'Purchases Stock',
                'permissions' => [
                    'list' => 'List',
                    'all'  => 'All',
                ],
            ],
        ],

        'Merchandising Management' =>[
            'samples' => [
                'label'       => 'Sample',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'order_details' => [
                'label'       => 'Order Details',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'proforma_invoice' => [
                'label'       => 'Proforma Invoice',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'fabrications' => [
                'label'       => 'Fabrications',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'compositions' => [
                'label'       => 'Compositions',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'buyers' => [
                'label'       => 'Buyer',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'budget' => [
                'label'       => 'Budget',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],

        ],

        'Production Management' =>[
            'production_planning' => [
                'label'       => 'Master Planning',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'approve' => 'Approve',
                    'all'    => 'All',
                ],
            ],
            'floor_planning' => [
                'label'       => 'Floor Planning',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'cutting' => [
                'label'       => 'Cutting List',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'finishing' => [
                'label'       => 'Finishing',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'iron' => [
                'label'       => 'Iron',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'poly' => [
                'label'       => 'Poly',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'sweing' => [
                'label'       => 'Sweing',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
        ],

        'Procurement Management' =>[
            'yarn_booking' => [
                'label'       => 'Yarn Booking',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'knitting_booking' => [
                'label'       => 'Knitting Booking',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'dyeing_booking' => [
                'label'       => 'Dyeing Booking',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'yarn_receive' => [
                'label'       => 'Yarn Receive',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'knitting_receive' => [
                'label'       => 'Knitting Receive',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'dyeing_receive' => [
                'label'       => 'Dyeing Receive',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'pi_wise_fabric_status' => [
                'label'       => 'PI Wise Fabric Status',
                'permissions' => [
                    'list' => 'List',
                    'view' => 'View',
                    'all'  => 'All',
                ],
            ],
        ],


        'Accounts Management' =>[
            'expenses' => [
                'label'       => 'Expenses',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'audit'  => 'Audit',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'expenses_type' => [
                'label'       => 'Expenses Head',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'expenses_report' => [
                'label'       => 'Expenses Report',
                'permissions' => [
                    'list'  => 'List',
                    'all'   => 'All',
                ],
            ],
            'iou' => [
                'label'       => 'I.O.U',
                'permissions' => [
                    'list'   => 'List',
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
                    'list' => 'List',
                    'all'  => 'All',
                ],
            ],
            'payment_methods' => [
                'label'       => 'Payment Methods',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'accounts' => [
                'label'       => 'Accounts',
                'permissions' => [
                    'list'   => 'List',
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
                    'list'   => 'List',
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
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'deposits' => [
                'label'       => 'Fund Received',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'delete' => 'Delete',
                    'approve' => 'Approve',
                    'all'    => 'All',
                ],
            ],
            'withdrawal' => [
                'label'       => 'Withdrawal',
                'permissions' => [
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'statement' => [
                'label'       => 'Statement',
                'permissions' => [
                    'list' => 'List',
                    'view' => 'View',
                    'all'  => 'All',
                ],
            ],

        ],

        'HR/Users Management' =>[
            'employee' => [
                'label'       => 'Employee',
                'permissions' => [
                    'list'   => 'List',
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
                    'list'   => 'List',
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
                    'list'   => 'List',
                    'add'    => 'Create',
                    'edit'   => 'Edit',
                    'view'   => 'View',
                    'delete' => 'Delete',
                    'all'    => 'All',
                ],
            ],
            'merchandisers' => [
                'label'       => 'Merchandisers',
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
            'branchs' => [
                'label'       => 'Branchs',
                'permissions' => [
                    'list'   => 'List',
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
                    'list'   => 'List',
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
                    'list'   => 'List',
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
