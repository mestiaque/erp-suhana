@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{websiteTitle('Road Map')}}</title>
@endsection


@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>ERP Road Map</h3>

            <div class="d-flex flex-wrap gap-3 align-items-center">
                <span class="fw-bold mr-4">Filter by Status:</span>

                <div class="form-check mr-3">
                    <input class="form-check-input status-radio" type="radio" name="statusFilter" id="statusAll" value="all">
                    <label class="form-check-label" for="statusAll">All</label>
                </div>

                <div class="form-check mr-3">
                    <input class="form-check-input status-radio" type="radio" name="statusFilter" id="statusCompleted" value="completed">
                    <label class="form-check-label completed-bg">Completed</label>
                </div>

                <div class="form-check mr-3">
                    <input class="form-check-input status-radio" type="radio" name="statusFilter" id="statusWorking" value="working">
                    <label class="form-check-label working-bg">Working</label>
                </div>

                <div class="form-check mr-3">
                    <input class="form-check-input status-radio" type="radio" name="statusFilter" id="statusPending" value="pending">
                    <label class="form-check-label pending-bg">Pending</label>
                </div>

                <div class="form-check mr-3">
                    <input class="form-check-input status-radio" type="radio" name="statusFilter" id="statusHold" value="hold">
                    <label class="form-check-label hold-bg">Hold</label>
                </div>

                <div class="form-check mr-3">
                    <input class="form-check-input status-radio" type="radio" name="statusFilter" id="statusRedev" value="redev">
                    <label class="form-check-label redev-bg">Redevelopment</label>
                </div>

                <div class="form-check mr-3">
                    <input class="form-check-input status-radio" type="radio" name="statusFilter" id="statusBug" value="bug">
                    <label class="form-check-label bug-bg">Bug</label>
                </div>
            </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            @php

            $maps = [

                // Dashboard & My Profile
                [
                    'title' => 'Dashboard',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'My Profile',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],

                // Purchases Management
                [
                    'title' => 'Purchases Orders',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Goods Items',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Requisitions',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Goods Receive (GRN)',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Damages / Returns',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Creditor Ledgers',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Purchase Reports',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Purchase Stock',
                    'start' => '',
                    'end'   => '',
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
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Proforma Invoice (PI)',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Budget',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Fabrications',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Compositions',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Buyer List',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],

                // Procurement
                [
                    'title' => 'Yarn Booking',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Knitting Booking',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Dyeing Booking',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Yarn Receiving',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Knitting Receiving',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Dyeing Receiving',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Fabric Status (PI-wise)',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],

                // Production
                [
                    'title' => 'Master Plan',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Floor Plan',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Cutting',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Sewing',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                    // 'focus' => true,
                ],

                // Accounts Management
                [
                    'title' => 'Expenses List',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Expense Head',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Expense Reports',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'I.O.U List',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'I.O.U Reports',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Payment Methods',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Account List',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Creditor List',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Creditor Payment',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Bill Collection',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Fund Received',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Withdrawal',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Statement',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],

                // HR / User Management
                [
                    'title' => 'Employee List',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Staff List',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Admin List',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Merchandiser List',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Roles Setup',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Branch / Factory',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Departments',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Designation',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Floor / Lines',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],

                // App Settings
                [
                    'title' => 'General Setting',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'Mail Setting',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],
                [
                    'title' => 'SMS Setting',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'completed',
                ],

                // Commercial
                [
                    'title' => 'Bank BTB LC',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Export LC / Sales Contract',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Purchase Order (PO)',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Proforma Invoice (PI)',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Pricing List',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Commercial Invoice',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Packing List',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Shipping Bill / Docs',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Export Realization',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Commercial Reports',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],

                // Payroll
                [
                    'title' => 'Salary Setup',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Attendance Summary',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Generate Payslip',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Bonus & Allowance',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Deductions & Loan',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Overtime (OT) Entry',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Salary Disbursement',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Payroll Reports',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],

                // Extras / Common ERP Garments Features
                [
                    'title' => 'Inventory Reports',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Supplier Management',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Quality Control / Inspection',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Production Analytics / KPIs',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Material Requirement Planning (MRP)',
                    'start' => '',
                    'end'   => '',
                    'status'=> 'pending',
                ],
                [
                    'title' => 'Shipment Tracking',
                    'start' => '',
                    'end'   => '26-10-01',
                    'status'=> 'pending',
                ],

            ];

            // Filter from session (previously selected via JS)
            $selectedStatus = session('roadmapStatusFilter', 'all');

            // Filter $maps array so that only items existing in DOM are rendered
            $maps = array_filter($maps, function($item) use ($selectedStatus) {
                // Agar "all" selected, sob allow
                if ($selectedStatus === 'all') return true;

                // Check if item status matches selected filter
                return isset($item['status']) && $item['status'] === $selectedStatus;
            });

            @endphp


            <div class="container roadmap-container">

                <div class="road-line"></div>



                @foreach ($maps as $item)

                    @php
                        // status class
                        $statusClass = match($item['status']) {
                            'completed'    => 'completed',
                            'working', 'in-progress' => 'working',
                            'pending'      => 'pending',

                            'redev'   => 'redev',
                            'bug'     => 'bug',
                            'testing' => 'testing',
                            'review'  => 'review',
                            'deploy'  => 'deploy',
                            'hold'    => 'hold',
                            default   => 'pending'
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

    /* Status label colors matching card bg */
    .completed-bg { color: rgba(60, 180, 75, 0.8); }
    .working-bg   { color: rgba(255, 174, 0, 0.8); }
    .pending-bg   { color: rgba(230, 57, 70, 0.8); }
    .hold-bg      { color: rgba(108, 117, 125, 0.8); }
    .redev-bg     { color: rgba(0, 123, 255, 0.8); }
    .bug-bg       { color: rgba(217, 83, 79, 0.8); }








</style>

@endpush
@push('js')
<script>
$(document).ready(function() {
    // Scroll to focus item
    let $focusItem = $('#focus-item');
    if ($focusItem.length) {
        $focusItem[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    const $roadItems = $('.road-item');
    const $roadLine = $('.road-line'); // Container of the items
    const storageKey = 'roadmapStatusFilter';

    // Load saved filter
    let savedStatus = sessionStorage.getItem(storageKey) || 'all';
    $('input.status-radio[value="' + savedStatus + '"]').prop('checked', true);
    filterRoadmap(savedStatus);

    // On radio change
    $('input.status-radio').change(function() {
        let status = $(this).val();
        sessionStorage.setItem(storageKey, status);
        filterRoadmap(status);
    });

    function filterRoadmap(status) {
        $roadItems.each(function() {
            let item = $(this);
            let itemStatus = item.attr('class').match(/(completed|working|pending|hold|redev|bug)-/);
            let itemStatusKey = itemStatus ? itemStatus[1] : '';

            if (status === 'all' || itemStatusKey === status) {
                item.show();
            } else {
                item.hide();
            }
        });

        // Check visible items count
        let visibleCount = $roadItems.filter(':visible').length;

        if (visibleCount === 0) {
            $roadLine.addClass('d-none'); // Hide container if none visible
        } else {
            $roadLine.removeClass('d-none'); // Show container if at least one visible
        }
    }
});


</script>

@endpush
@endsection





