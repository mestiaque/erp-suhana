@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{websiteTitle('Road Map')}}</title>
@endsection


@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>ERP Road Map</h3>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            @php

            $maps = [

                // Dashboard & My Profile
                [
                    'title' => 'Dashboard',
                    'start' => '2026-01-15',
                    'end'   => '2026-01-16',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'My Profile',
                    'start' => '2026-01-15',
                    'end'   => '2026-01-16',
                    'status'=> 'completed',
                ],

                // Purchases Management
                [
                    'title' => 'Purchases Orders',
                    'start' => '2026-01-17',
                    'end'   => '2026-01-22',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Goods Items',
                    'start' => '2026-01-23',
                    'end'   => '2026-01-28',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Requisitions',
                    'start' => '2026-01-29',
                    'end'   => '2026-02-02',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Goods Receive (GRN)',
                    'start' => '2026-02-03',
                    'end'   => '2026-02-07',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Damages / Returns',
                    'start' => '2026-02-08',
                    'end'   => '2026-02-10',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Creditor Ledgers',
                    'start' => '2026-02-11',
                    'end'   => '2026-02-15',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Purchase Reports',
                    'start' => '2026-02-16',
                    'end'   => '2026-02-20',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Purchase Stock',
                    'start' => '2026-02-21',
                    'end'   => '2026-02-25',
                    'status'=> 'completed',
                ],

                // Merchandising
                [
                    'title' => 'Sample Management',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'hold',
                ],
                [
                    'title' => 'Order Details',
                    'start' => '2026-03-03',
                    'end'   => '2026-03-07',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Proforma Invoice (PI)',
                    'start' => '2026-03-08',
                    'end'   => '2026-03-12',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Budget',
                    'start' => '2026-03-13',
                    'end'   => '2026-03-17',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Fabrications',
                    'start' => '2026-03-18',
                    'end'   => '2026-03-20',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Compositions',
                    'start' => '2026-03-21',
                    'end'   => '2026-03-23',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Buyer List',
                    'start' => '2026-03-24',
                    'end'   => '2026-03-26',
                    'status'=> 'completed',
                ],

                // Procurement
                [
                    'title' => 'Yarn Booking',
                    'start' => '2026-03-27',
                    'end'   => '2026-03-30',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Knitting Booking',
                    'start' => '2026-03-31',
                    'end'   => '2026-04-02',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Dyeing Booking',
                    'start' => '2026-04-03',
                    'end'   => '2026-04-05',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Yarn Receiving',
                    'start' => '2026-04-06',
                    'end'   => '2026-04-08',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Knitting Receiving',
                    'start' => '2026-04-09',
                    'end'   => '2026-04-11',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Dyeing Receiving',
                    'start' => '2026-04-12',
                    'end'   => '2026-04-14',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Fabric Status (PI-wise)',
                    'start' => '2026-04-15',
                    'end'   => '2026-04-17',
                    'status'=> 'completed',
                ],

                // Production
                [
                    'title' => 'Master Plan',
                    'start' => '2026-04-18',
                    'end'   => '2026-04-22',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Floor Plan',
                    'start' => '2026-04-23',
                    'end'   => '2026-04-27',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Cutting',
                    'start' => '2026-04-28',
                    'end'   => '2026-05-02',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Sewing',
                    'start' => '2026-05-03',
                    'end'   => '2026-05-07',
                    'status'=> 'completed',
                    'focus' => true,
                ],

                // Accounts Management
                [
                    'title' => 'Expenses List',
                    'start' => '2026-05-08',
                    'end'   => '2026-05-12',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Expense Head',
                    'start' => '2026-05-13',
                    'end'   => '2026-05-15',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Expense Reports',
                    'start' => '2026-05-16',
                    'end'   => '2026-05-18',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'I.O.U List',
                    'start' => '2026-05-19',
                    'end'   => '2026-05-21',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'I.O.U Reports',
                    'start' => '2026-05-22',
                    'end'   => '2026-05-24',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Payment Methods',
                    'start' => '2026-05-25',
                    'end'   => '2026-05-27',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Account List',
                    'start' => '2026-05-28',
                    'end'   => '2026-05-30',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Creditor List',
                    'start' => '2026-05-31',
                    'end'   => '2026-06-02',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Creditor Payment',
                    'start' => '2026-06-03',
                    'end'   => '2026-06-05',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Bill Collection',
                    'start' => '2026-06-06',
                    'end'   => '2026-06-08',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Fund Received',
                    'start' => '2026-06-09',
                    'end'   => '2026-06-11',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Withdrawal',
                    'start' => '2026-06-12',
                    'end'   => '2026-06-14',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Statement',
                    'start' => '2026-06-15',
                    'end'   => '2026-06-17',
                    'status'=> 'completed',
                ],

                // HR / User Management
                [
                    'title' => 'Employee List',
                    'start' => '2026-06-18',
                    'end'   => '2026-06-20',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Staff List',
                    'start' => '2026-06-21',
                    'end'   => '2026-06-23',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Admin List',
                    'start' => '2026-06-24',
                    'end'   => '2026-06-26',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Merchandiser List',
                    'start' => '2026-06-27',
                    'end'   => '2026-06-29',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Roles Setup',
                    'start' => '2026-06-30',
                    'end'   => '2026-07-02',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Branch / Factory',
                    'start' => '2026-07-03',
                    'end'   => '2026-07-05',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Departments',
                    'start' => '2026-07-06',
                    'end'   => '2026-07-08',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Designation',
                    'start' => '2026-07-09',
                    'end'   => '2026-07-11',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Floor / Lines',
                    'start' => '2026-07-12',
                    'end'   => '2026-07-14',
                    'status'=> 'completed',
                ],

                // App Settings
                [
                    'title' => 'General Setting',
                    'start' => '2026-07-15',
                    'end'   => '2026-07-16',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Mail Setting',
                    'start' => '2026-07-17',
                    'end'   => '2026-07-18',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'SMS Setting',
                    'start' => '2026-07-19',
                    'end'   => '2026-07-20',
                    'status'=> 'completed',
                ],

                // Commercial
                [
                    'title' => 'Bank BTB LC',
                    'start' => '2026-07-21',
                    'end'   => '2026-07-23',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Export LC / Sales Contract',
                    'start' => '2026-07-24',
                    'end'   => '2026-07-26',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Purchase Order (PO)',
                    'start' => '2026-07-27',
                    'end'   => '2026-07-29',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Proforma Invoice (PI)',
                    'start' => '2026-07-30',
                    'end'   => '2026-08-01',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Pricing List',
                    'start' => '2026-08-02',
                    'end'   => '2026-08-04',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Commercial Invoice',
                    'start' => '2026-08-05',
                    'end'   => '2026-08-07',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Packing List',
                    'start' => '2026-08-08',
                    'end'   => '2026-08-10',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Shipping Bill / Docs',
                    'start' => '2026-08-11',
                    'end'   => '2026-08-13',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Export Realization',
                    'start' => '2026-08-14',
                    'end'   => '2026-08-16',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Commercial Reports',
                    'start' => '2026-08-17',
                    'end'   => '2026-08-19',
                    'status'=> 'pending',
                ],

                // Payroll
                [
                    'title' => 'Salary Setup',
                    'start' => '2026-08-20',
                    'end'   => '2026-08-22',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Attendance Summary',
                    'start' => '2026-08-23',
                    'end'   => '2026-08-25',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Generate Payslip',
                    'start' => '2026-08-26',
                    'end'   => '2026-08-28',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Bonus & Allowance',
                    'start' => '2026-08-29',
                    'end'   => '2026-08-31',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Deductions & Loan',
                    'start' => '2026-09-01',
                    'end'   => '2026-09-03',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Overtime (OT) Entry',
                    'start' => '2026-09-04',
                    'end'   => '2026-09-06',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Salary Disbursement',
                    'start' => '2026-09-07',
                    'end'   => '2026-09-09',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Payroll Reports',
                    'start' => '2026-09-10',
                    'end'   => '2026-09-12',
                    'status'=> 'pending',
                ],

                // Extras / Common ERP Garments Features
                [
                    'title' => 'Inventory Reports',
                    'start' => '2026-09-13',
                    'end'   => '2026-09-15',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Supplier Management',
                    'start' => '2026-09-16',
                    'end'   => '2026-09-18',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Quality Control / Inspection',
                    'start' => '2026-09-19',
                    'end'   => '2026-09-22',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Production Analytics / KPIs',
                    'start' => '2026-09-23',
                    'end'   => '2026-09-25',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Material Requirement Planning (MRP)',
                    'start' => '2026-09-26',
                    'end'   => '2026-09-28',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Shipment Tracking',
                    'start' => '2026-09-29',
                    'end'   => '2026-10-01',
                    'status'=> 'pending',
                ],

            ];

            @endphp


            <div class="container roadmap-container">

                <div class="road-line"></div>

                @foreach ($maps as $item)

                    @php
                        // status class
                        $statusClass = match($item['status']) {
                            'completed' => 'completed',
                            'working', 'in-progress' => 'working',
                            'pending' => 'pending',

                            'redev'       => 'redev',
                            'bug'         => 'bug',
                            'testing'     => 'testing',
                            'review'      => 'review',
                            'deploy'      => 'deploy',
                            'hold'        => 'hold',
                            default => 'pending'
                        };

                        // zigzag placement
                        $side = $loop->iteration % 2 == 1 ? 'left' : 'right';
                    @endphp

                    <div class="road-item {{ $side }} {{ $statusClass }}-{{ $side }} mb-1" @if (!empty($item['focus'])) id="focus-item" @endif>
                        {{-- <div class="connector"></div> --}}

                        <h5 class="fw-bold">{{ $item['title'] }}</h5>

                        @php
                            $start = !empty($item['start']) ? \Carbon\Carbon::parse($item['start'])->format('d M Y') : null;
                            $end   = !empty($item['end'])   ? \Carbon\Carbon::parse($item['end'])->format('d M Y') : null;
                        @endphp

                        <p class="mb-1 small text-muted">
                            @if ($start && $end)
                                {{ $start }} &rarr; {{ $end }}
                            @elseif ($start)
                                {{ $start }}
                            @elseif ($end)
                                {{ $end }}
                            @else
                                <span class="text-secondary">No Date</span>
                            @endif
                        </p>

                        <p class="mb-0 small">
                            Status: <strong>{{ ucfirst($item['status']) }}</strong>
                        </p>
                    </div>

                @endforeach
            </div>


        </div>
    </div>
</div>


@push('css')
<style>
    .roadmap-container {
        position: relative;
        padding: 40px 0;
        width: 63rem !important;
    }

    .road-line {
        position: absolute;
        top: 0;
        left: 50%;
        width: 2rem;
        height: 100%;
        transform: translateX(-50%);
        background: linear-gradient(to bottom, #ccc, #a3a3a3, #727272);
        border-radius: 10px;

    }


    .road-item {
        width: 30rem;
        padding: 15px 20px;
        border-radius: 20px;
        backdrop-filter: blur(14px);
        background: rgba(255,255,255,0.4);
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        position: relative;
        transition: 0.3s ease;
    }

    .road-item:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 15px 30px rgba(0,0,0,0.17);
    }

    .left {
        margin-left: 0;
        margin-right: auto;
    }

    .right {
        margin-right: 0;
        margin-left: auto;
    }

    .connector {
        position: absolute;
        top: 50%;
        width: 40px;
        height: 4px;
        background: #bbb;
    }

    .left .connector { right: -40px; }
    .right .connector { left: -40px; }

    /* Status Colors */
    .completed-right {
        border-right: 8px solid #28a745;
        background: rgba(60, 180, 75, 0.18) !important;
        text-align: right !important;
    }

    .pending-right {
        border-right: 8px solid #e63946;
        background: rgba(230, 57, 70, 0.18) !important;
        text-align: right !important;
    }

    .working-right {
        border-right: 8px solid #ffae00;
        background: rgba(255, 174, 0, 0.18) !important;
        box-shadow: 0 0 15px rgba(255, 174, 0, 0.5);
        text-align: right !important;
    }
    .completed-left {
        border-left: 8px solid #28a745;
        background: rgba(60, 180, 75, 0.18) !important;
    }

    .pending-left {
        border-left: 8px solid #e63946;
        background: rgba(230, 57, 70, 0.18) !important;
    }

    .working-left {
        border-left: 8px solid #ffae00;
        background: rgba(255, 174, 0, 0.18) !important;
        box-shadow: 0 0 15px rgba(255, 174, 0, 0.5);
    }
    .redev-left {
        border-left: 8px solid #007bff;
        background: rgba(0, 123, 255, 0.18) !important;
    }
    .redev-right {
        border-right: 8px solid #007bff;
        background: rgba(0, 123, 255, 0.18) !important;
        text-align: right !important;
    }
    .bug-left {
        border-left: 8px solid #d9534f;
        background: rgba(217, 83, 79, 0.18) !important;
    }
    .bug-right {
        border-right: 8px solid #d9534f;
        background: rgba(217, 83, 79, 0.18) !important;
        text-align: right !important;
    }
    .testing-left {
        border-left: 8px solid #6f42c1;
        background: rgba(111, 66, 193, 0.18) !important;
    }
    .testing-right {
        border-right: 8px solid #6f42c1;
        background: rgba(111, 66, 193, 0.18) !important;
        text-align: right !important;
    }
    .review-left {
        border-left: 8px solid #17a2b8;
        background: rgba(23, 162, 184, 0.18) !important;
    }
    .review-right {
        border-right: 8px solid #17a2b8;
        background: rgba(23, 162, 184, 0.18) !important;
        text-align: right !important;
    }
    .deploy-left {
        border-left: 8px solid #20c997;
        background: rgba(32, 201, 151, 0.18) !important;
    }
    .deploy-right {
        border-right: 8px solid #20c997;
        background: rgba(32, 201, 151, 0.18) !important;
        text-align: right !important;
    }
    .hold-left {
        border-left: 8px solid #6c757d;
        background: rgba(108, 117, 125, 0.18) !important;
    }
    .hold-right {
        border-right: 8px solid #6c757d;
        background: rgba(108, 117, 125, 0.18) !important;
        text-align: right !important;
    }







</style>

@endpush
@push('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let focusItem = document.getElementById("focus-item");

        if (focusItem) {
            focusItem.scrollIntoView({
                behavior: "smooth",
                block: "center"
            });
        }
    });
</script>

@endpush
@endsection





