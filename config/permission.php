<?php

return [
    'modules' => [
        'company' => [
            'label' => 'Customers Management',
            'permissions' => [
                'add' => 'Create/Update',
                'view' => 'View',
                'delete' => 'Delete',
                'export' => 'Export',
                'sales' => 'Sales',
                'duecollect' => 'Due Collect',
                'service' => 'Services',
                'all' => 'All',
            ],
        ],
        'engineers' => [
            'label' => 'Engineers Management',
            'permissions' => [
                'add' => 'Create/Update',
                'view' => 'View',
                'delete' => 'Delete',
                'export' => 'Export',
                'all' => 'All',
            ],
        ],
        'leads' => [
            'label' => 'Leads Management',
            'permissions' => [
                'add' => 'Create/Update',
                'view' => 'View',
                'delete' => 'Delete',
                'all' => 'All',
            ],
        ],
        'tasks' => [
            'label' => 'Task Management',
            'permissions' => [
                'add' => 'Create/Update',
                'view' => 'View',
                'delete' => 'Delete',
                'all' => 'All',
            ],
        ],
        'meetings' => [
            'label' => 'Meeting Management',
            'permissions' => [
                'add' => 'Create/Update',
                'view' => 'View',
                'delete' => 'Delete',
                'all' => 'All',
            ],
        ],
        'visits' => [
            'label' => 'Visit Management',
            'permissions' => [
                'add' => 'Create/Update',
                'view' => 'View',
                'delete' => 'Delete',
                'all' => 'All',
            ],
        ],
        'sales' => [
            'label' => 'Sales Invoice',
            'permissions' => [
                'add' => 'Create/Update',
                'view' => 'View',
                'delete' => 'Delete',
                'all' => 'All',
            ],
        ],
        'quotation' => [
            'label' => 'Quotation',
            'permissions' => [
                'add' => 'Create/Update',
                'view' => 'View',
                'delete' => 'Delete',
                'all' => 'All',
            ],
        ],
        'expenses' => [
            'label' => 'expenses',
            'permissions' => [
                'add' => 'Create/Update',
                'view' => 'View',
                'delete' => 'Delete',
                'type' =>  'Type',
                'all' => 'All',
            ],
        ],
        'accounts' => [
            'label' => 'Accounts',
            'permissions' => [
                'add' => 'Create/Update',
                'view' => 'View',
                'delete' => 'Delete',
                'type' =>  'Type',
                'all' => 'All',
            ],
        ],
        'paymentMethod' => [
            'label' => 'Payment Method',
            'permissions' => [
                'add' => 'Create/Update',
                'view' => 'View',
                'delete' => 'Delete',
                'type' =>  'Type',
                'all' => 'All',
            ],
        ],
    ],
];
