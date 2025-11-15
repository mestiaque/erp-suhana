<?php

return [
    [
        'title'      => 'Administration & User Control',
        'icon'       => 'fa-solid fa-user-gear',
        'icon_color' => 'text-danger',
        'children'   => [
            ['title' => 'Multi-user & Multi-role Access', 'icon' => 'fa-solid fa-users-gear', 'route' => '#', 'icon_color' => 'text-danger'],
            ['title' => 'Department-wise Permissions', 'icon' => 'fa-solid fa-building-lock', 'route' => '#', 'icon_color' => 'text-danger'],
            ['title' => 'Role-based Dashboard', 'icon' => 'fa-solid fa-gauge-high', 'route' => '#', 'icon_color' => 'text-danger'],
            ['title' => 'Activity Log & Audit Trail', 'icon' => 'fa-solid fa-file-shield', 'route' => '#', 'icon_color' => 'text-danger'],
            ['title' => 'Branch/Factory Management', 'icon' => 'fa-solid fa-industry', 'route' => '#', 'icon_color' => 'text-danger'],
            ['title' => 'Data Backup & Restore', 'icon' => 'fa-solid fa-cloud-arrow-up', 'route' => '#', 'icon_color' => 'text-danger'],
        ]
    ],

    [
        'title'      => 'Merchandising Management',
        'icon'       => 'fa-solid fa-industry',
        'icon_color' => 'text-primary',
        'children'   => [
            ['title' => 'Buyer Profile', 'icon' => 'fa-solid fa-user-tie', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Style Creation & Management', 'icon' => 'fa-solid fa-shirt', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Sample Request & Approval', 'icon' => 'fa-solid fa-clipboard-check', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Tech Pack Upload', 'icon' => 'fa-solid fa-file-arrow-up', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Costing & Consumption', 'icon' => 'fa-solid fa-calculator', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Order Confirmation Tracking', 'icon' => 'fa-solid fa-receipt', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Pre-production Approval', 'icon' => 'fa-solid fa-circle-check', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Communication Log', 'icon' => 'fa-solid fa-comments', 'route' => '#', 'icon_color' => 'text-primary'],
        ]
    ],

    [
        'title'      => 'Production Planning & Control',
        'icon'       => 'fa-solid fa-gears',
        'icon_color' => 'text-warning',
        'children'   => [
            ['title' => 'Order to Production Planning', 'icon' => 'fa-solid fa-list-check', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Capacity Planning', 'icon' => 'fa-solid fa-chart-line', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Line Loading Plan', 'icon' => 'fa-solid fa-layer-group', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Cut-to-Pack Tracking', 'icon' => 'fa-solid fa-box-open', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Re-cut & Rework Management', 'icon' => 'fa-solid fa-arrow-rotate-right', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Production Progress Report', 'icon' => 'fa-solid fa-chart-bar', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Production Efficiency Report', 'icon' => 'fa-solid fa-gauge', 'route' => '#', 'icon_color' => 'text-warning'],
        ]
    ],

    [
        'title'      => 'Cutting Department',
        'icon'       => 'fa-solid fa-scissors',
        'icon_color' => 'text-info',
        'children'   => [
            ['title' => 'Fabric Requisition', 'icon' => 'fa-solid fa-border-all', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Cutting Plan & Lay Chart', 'icon' => 'fa-solid fa-table-cells-large', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Marker & Fabric Utilization', 'icon' => 'fa-solid fa-ruler-combined', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Bundle Creation & Numbering', 'icon' => 'fa-solid fa-boxes-stacked', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Cutting Status Tracking', 'icon' => 'fa-solid fa-bars-progress', 'route' => '#', 'icon_color' => 'text-info'],
        ]
    ],

    [
        'title'      => 'Sewing Department',
        'icon'       => 'fa-solid fa-needle',
        'icon_color' => 'text-primary',
        'children'   => [
            ['title' => 'Line Setup & Target', 'icon' => 'fa-solid fa-diagram-project', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Operation Bulletin', 'icon' => 'fa-solid fa-file-lines', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'WIP Tracking', 'icon' => 'fa-solid fa-spinner', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Hourly Production Input', 'icon' => 'fa-solid fa-clock', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Operator Performance', 'icon' => 'fa-solid fa-user-check', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Rejection & Alteration', 'icon' => 'fa-solid fa-triangle-exclamation', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Line Balancing Report', 'icon' => 'fa-solid fa-scale-balanced', 'route' => '#', 'icon_color' => 'text-primary'],
        ]
    ],

    [
        'title'      => 'Finishing & Packing',
        'icon'       => 'fa-solid fa-box',
        'icon_color' => 'text-success',
        'children'   => [
            ['title' => 'Finishing Input/Output Tracking', 'icon' => 'fa-solid fa-list-check', 'route' => '#', 'icon_color' => 'text-success'],
            ['title' => 'Iron & Quality Checking', 'icon' => 'fa-solid fa-thumbs-up', 'route' => '#', 'icon_color' => 'text-success'],
            ['title' => 'Packing List Generation', 'icon' => 'fa-solid fa-list', 'route' => '#', 'icon_color' => 'text-success'],
            ['title' => 'Carton Details & Barcode', 'icon' => 'fa-solid fa-barcode', 'route' => '#', 'icon_color' => 'text-success'],
            ['title' => 'Shipment Readiness Report', 'icon' => 'fa-solid fa-truck-fast', 'route' => '#', 'icon_color' => 'text-success'],
        ]
    ],

    [
        'title'      => 'Inventory & Store Management',
        'icon'       => 'fa-solid fa-warehouse',
        'icon_color' => 'text-warning',
        'children'   => [
            ['title' => 'Raw Material Inventory', 'icon' => 'fa-solid fa-cubes', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Goods Receive Note', 'icon' => 'fa-solid fa-file-invoice', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Material Issue & Return', 'icon' => 'fa-solid fa-right-left', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Stock Ledger & Valuation', 'icon' => 'fa-solid fa-book', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Minimum Stock Alert', 'icon' => 'fa-solid fa-bell', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Barcode-based Stock Management', 'icon' => 'fa-solid fa-barcode', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Store Requisition & Approval', 'icon' => 'fa-solid fa-file-signature', 'route' => '#', 'icon_color' => 'text-warning'],
        ]
    ],

    [
        'title'      => 'Purchase Management',
        'icon'       => 'fa-solid fa-cart-shopping',
        'icon_color' => 'text-primary',
        'children'   => [
            ['title' => 'Supplier List & Item Catalog', 'icon' => 'fa-solid fa-user-group', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Purchase Requisition (PR)', 'icon' => 'fa-solid fa-file-pen', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Purchase Order (PO)', 'icon' => 'fa-solid fa-file-invoice-dollar', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Supplier Quotation Comparison', 'icon' => 'fa-solid fa-scale-balanced', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Goods Receiving & QC', 'icon' => 'fa-solid fa-circle-check', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Supplier Payment Tracking', 'icon' => 'fa-solid fa-money-bill-transfer', 'route' => '#', 'icon_color' => 'text-primary'],
        ]
    ],

    [
        'title'      => 'Commercial (Import & Export)',
        'icon'       => 'fa-solid fa-file-export',
        'icon_color' => 'text-info',
        'children'   => [
            ['title' => 'L/C Management', 'icon' => 'fa-solid fa-file-contract', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Export Documentation', 'icon' => 'fa-solid fa-file-arrow-up', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Shipment Tracking', 'icon' => 'fa-solid fa-shipping-fast', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Bank Submission Record', 'icon' => 'fa-solid fa-building-columns', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Realization Statement', 'icon' => 'fa-solid fa-money-check-dollar', 'route' => '#', 'icon_color' => 'text-info'],
        ]
    ],

    [
        'title'      => 'Sample Department',
        'icon'       => 'fa-solid fa-flask',
        'icon_color' => 'text-secondary',
        'children'   => [
            ['title' => 'Sample Request', 'icon' => 'fa-solid fa-clipboard', 'route' => '#', 'icon_color' => 'text-secondary'],
            ['title' => 'Sample Production Tracking', 'icon' => 'fa-solid fa-timeline', 'route' => '#', 'icon_color' => 'text-secondary'],
            ['title' => 'Sample Approval Status', 'icon' => 'fa-solid fa-check', 'route' => '#', 'icon_color' => 'text-secondary'],
            ['title' => 'Sample Delivery Report', 'icon' => 'fa-solid fa-truck', 'route' => '#', 'icon_color' => 'text-secondary'],
        ]
    ],

    [
        'title'      => 'Accounts & Finance',
        'icon'       => 'fa-solid fa-money-bill-trend-up',
        'icon_color' => 'text-warning',
        'children'   => [
            ['title' => 'Chart of Accounts', 'icon' => 'fa-solid fa-book', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Voucher Entry', 'icon' => 'fa-solid fa-file-invoice', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Payment & Receipt', 'icon' => 'fa-solid fa-money-check-dollar', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Expense Tracking', 'icon' => 'fa-solid fa-file-invoice-dollar', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Profit & Loss', 'icon' => 'fa-solid fa-chart-line', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Balance Sheet', 'icon' => 'fa-solid fa-scale-balanced', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Bank Reconciliation', 'icon' => 'fa-solid fa-university', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Buyer & Supplier Ledger', 'icon' => 'fa-solid fa-book-open', 'route' => '#', 'icon_color' => 'text-warning'],
        ]
    ],

    [
        'title'      => 'HR & Payroll',
        'icon'       => 'fa-solid fa-users',
        'icon_color' => 'text-success',
        'children'   => [
            ['title' => 'Employee Profile & Attendance', 'icon' => 'fa-solid fa-id-card', 'route' => '#', 'icon_color' => 'text-success'],
            ['title' => 'Biometric / RFID', 'icon' => 'fa-solid fa-fingerprint', 'route' => '#', 'icon_color' => 'text-success'],
            ['title' => 'Leave & Holiday Management', 'icon' => 'fa-solid fa-calendar-days', 'route' => '#', 'icon_color' => 'text-success'],
            ['title' => 'Overtime Calculation', 'icon' => 'fa-solid fa-hourglass-half', 'route' => '#', 'icon_color' => 'text-success'],
            ['title' => 'Salary Structure & Payroll', 'icon' => 'fa-solid fa-sack-dollar', 'route' => '#', 'icon_color' => 'text-success'],
            ['title' => 'Bonus & Incentive', 'icon' => 'fa-solid fa-gift', 'route' => '#', 'icon_color' => 'text-success'],
            ['title' => 'PF / Gratuity', 'icon' => 'fa-solid fa-hand-holding-dollar', 'route' => '#', 'icon_color' => 'text-success'],
            ['title' => 'Payslip & Salary Sheet', 'icon' => 'fa-solid fa-file-invoice-dollar', 'route' => '#', 'icon_color' => 'text-success'],
        ]
    ],

    [
        'title'      => 'Costing & Budgeting',
        'icon'       => 'fa-solid fa-calculator',
        'icon_color' => 'text-info',
        'children'   => [
            ['title' => 'Pre-costing', 'icon' => 'fa-solid fa-coins', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Post-costing Comparison', 'icon' => 'fa-solid fa-scale-balanced', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Order Profit/Loss Analysis', 'icon' => 'fa-solid fa-chart-area', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Budget Control Reports', 'icon' => 'fa-solid fa-file-lines', 'route' => '#', 'icon_color' => 'text-info'],
        ]
    ],

    [
        'title'      => 'CRM',
        'icon'       => 'fa-solid fa-handshake',
        'icon_color' => 'text-primary',
        'children'   => [
            ['title' => 'Buyer & Supplier Communication', 'icon' => 'fa-solid fa-comments', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Inquiry & Follow-up', 'icon' => 'fa-solid fa-question', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Quotation & Offer', 'icon' => 'fa-solid fa-file-signature', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Order Feedback Tracking', 'icon' => 'fa-solid fa-file-contract', 'route' => '#', 'icon_color' => 'text-primary'],
        ]
    ],

    [
        'title'      => 'Quality Control',
        'icon'       => 'fa-solid fa-shield-check',
        'icon_color' => 'text-warning',
        'children'   => [
            ['title' => 'Inline & Final Inspection', 'icon' => 'fa-solid fa-magnifying-glass', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Defect Tracking', 'icon' => 'fa-solid fa-bug', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Rework & Repair', 'icon' => 'fa-solid fa-screwdriver-wrench', 'route' => '#', 'icon_color' => 'text-warning'],
            ['title' => 'Quality Performance Report', 'icon' => 'fa-solid fa-chart-column', 'route' => '#', 'icon_color' => 'text-warning'],
        ]
    ],

    [
        'title'      => 'Logistics & Delivery',
        'icon'       => 'fa-solid fa-truck-moving',
        'icon_color' => 'text-info',
        'children'   => [
            ['title' => 'Shipment Schedule', 'icon' => 'fa-solid fa-calendar-days', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Transport & Vehicle Tracking', 'icon' => 'fa-solid fa-truck', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Delivery Challan', 'icon' => 'fa-solid fa-file-lines', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Gate Pass & Delivery Confirmation', 'icon' => 'fa-solid fa-passport', 'route' => '#', 'icon_color' => 'text-info'],
        ]
    ],

    [
        'title'      => 'Reporting & Analytics',
        'icon'       => 'fa-solid fa-chart-pie',
        'icon_color' => 'text-primary',
        'children'   => [
            ['title' => 'Buyer-wise Order Report', 'icon' => 'fa-solid fa-file', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Production Progress Dashboard', 'icon' => 'fa-solid fa-gauge-high', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Line Efficiency Graph', 'icon' => 'fa-solid fa-chart-line', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Inventory Stock Summary', 'icon' => 'fa-solid fa-warehouse', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Financial Overview', 'icon' => 'fa-solid fa-sack-dollar', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Employee Attendance & Productivity', 'icon' => 'fa-solid fa-user-check', 'route' => '#', 'icon_color' => 'text-primary'],
            ['title' => 'Custom Report Generator', 'icon' => 'fa-solid fa-file-export', 'route' => '#', 'icon_color' => 'text-primary'],
        ]
    ],

    [
        'title'      => 'Security & Data Protection',
        'icon'       => 'fa-solid fa-lock',
        'icon_color' => 'text-danger',
        'children'   => [
            ['title' => 'SSL-secured Data', 'icon' => 'fa-solid fa-shield-halved', 'route' => '#', 'icon_color' => 'text-danger'],
            ['title' => 'IP-based Access Restriction', 'icon' => 'fa-solid fa-network-wired', 'route' => '#', 'icon_color' => 'text-danger'],
            ['title' => 'User Action Logs', 'icon' => 'fa-solid fa-file-lines', 'route' => '#', 'icon_color' => 'text-danger'],
            ['title' => 'Auto-backup', 'icon' => 'fa-solid fa-database', 'route' => '#', 'icon_color' => 'text-danger'],
            ['title' => 'Two-factor Authentication', 'icon' => 'fa-solid fa-key', 'route' => '#', 'icon_color' => 'text-danger'],
        ]
    ],

    [
        'title'      => 'Cloud Integration',
        'icon'       => 'fa-solid fa-cloud',
        'icon_color' => 'text-info',
        'children'   => [
            ['title' => 'Cloud Hosting', 'icon' => 'fa-solid fa-server', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Real-time Access', 'icon' => 'fa-solid fa-globe', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'API Integration', 'icon' => 'fa-solid fa-plug', 'route' => '#', 'icon_color' => 'text-info'],
            ['title' => 'Auto Software Update', 'icon' => 'fa-solid fa-rotate', 'route' => '#', 'icon_color' => 'text-info'],
        ]
    ],
];

