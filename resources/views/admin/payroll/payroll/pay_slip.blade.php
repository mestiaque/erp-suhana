@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Pay Slip') }}</title>
@endsection

@push('css')
<style>
    .payslip-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border: 2px solid #333;
    }
    .payslip-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 25px;
        margin: -30px -30px 25px -30px;
        text-align: center;
    }
    .payslip-table {
        width: 100%;
        border-collapse: collapse;
    }
    .payslip-table td, .payslip-table th {
        padding: 10px;
        border: 1px solid #ddd;
    }
    .payslip-table th {
        background: #f8f9fa;
        text-align: left;
    }
    .payslip-footer {
        background: #e8f5e9;
        padding: 15px;
        margin: 25px -30px -30px -30px;
        text-align: center;
    }
    .earnings-section { border-right: 2px solid #dee2e6; }
    .net-pay-section { background: #e3f2fd; }
    .employee-photo {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
    }
    @media print {
        .no-print, .breadcrumb-area, .doc-card, form, button, .btn,
        .sidebar, .sidenav, .main-menu, .header-navbar, .footer,
        .sidebar-wrapper, .app-sidebar, .app-header, .app-footer,
        .sidemenu-area, .sidemenu-header, .sidemenu-body,
        nav, header, .navbar, .footer-area,
        nav, header, .navbar, .footer-area, .main-menu, .side-menu,
        .side-nav, .page-header, .page-footer, .layout-header,
        .layout-footer, .layout-sidebar, .sidebar-menu, .sidebar-footer,
        .sidebar-header, .sidebar-content, .sidebar, .footer, .header,
        .sidemenu, .sidemenu-area, .sidemenu-header, .sidemenu-body,
        .flex-grow-1 > .breadcrumb-area, .flex-grow-1 > .alert {
            display: none !important;
        }

        body {
            margin: 0 !important;
            padding: 5mm !important;
            background: white !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .flex-grow-1 {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .payslip-container {
            max-width: 100%;
            border: 2px solid #333;
            page-break-inside: avoid;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-shadow: none;
        }
        .payslip-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        * {
            box-shadow: none !important;
        }
    }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area no-print">
    <h1>Pay Slip</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{route('admin.payroll.index')}}">Payroll</a></li>
        <li class="item">Pay Slip</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="text-center mb-3 no-print">
        <a href="{{route('admin.payroll.paySlip', $salarySheet->id)}}" target="_blank" class="btn btn-primary">
            <i class="bx bx-printer"></i> Print
        </a>
        <a href="{{route('admin.payroll.salarySheet', ['month' => $salarySheet->month, 'year' => $salarySheet->year])}}" class="btn btn-secondary">
            <i class="bx bx-arrow-back"></i> Back
        </a>
    </div>

    @php
        $monthName = date('F Y', mktime(0, 0, 0, $salarySheet->month, 1, $salarySheet->year));
        $employee = $salarySheet->user;
    @endphp

    <div class="payslip-container">
        <div class="payslip-header">
            <h3>{{ general()->title ?? 'Company Name' }}</h3>
            <p class="mb-1">{{ general()->address_one ?? '' }}</p>
            <p class="mb-0"><strong>SALARY SLIP - {{ strtoupper($monthName) }}</strong></p>
        </div>

        <!-- Employee Info -->
        <table class="payslip-table mb-3">
            <tr>
                <th style="width: 15%;">Employee Name:</th>
                <td style="width: 35%;"><strong>{{ $employee->name }}</strong></td>
                <th style="width: 20%;">Employee ID:</th>
                <td style="width: 30%;">{{ $employee->employee_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Department:</th>
                <td>{{ $employee->department->name ?? 'N/A' }}</td>
                <th>Designation:</th>
                <td>{{ $employee->designation->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Father's Name:</th>
                <td>{{ $employee->father_name ?? 'N/A' }}</td>
                <th>Card No:</th>
                <td>{{ $employee->card_number ?? ($employee->employeeInfo->card_no ?? 'N/A') }}</td>
            </tr>
            <tr>
                <th>Date of Joining:</th>
                <td>{{ $employee->joining_date ? date('d M Y', strtotime($employee->joining_date)) : 'N/A' }}</td>
                <th>Pay Period:</th>
                <td>{{ $monthName }}</td>
            </tr>
            <tr>
                <th>Payment Method:</th>
                <td>
                    @if($salarySheet->payment_method == 'bank')
                        <span class="badge bg-info">Bank Transfer</span>
                    @elseif($salarySheet->payment_method == 'cash')
                        <span class="badge bg-warning">Cash</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($salarySheet->payment_method) }}</span>
                    @endif
                </td>
                <th>Payment Status:</th>
                <td>
                    @if($salarySheet->payment_status == 'paid')
                        <span class="badge bg-success">Paid</span>
                    @elseif($salarySheet->payment_status == 'held')
                        <span class="badge bg-danger">Held</span>
                    @else
                        <span class="badge bg-warning">Pending</span>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Attendance Info -->
        <table class="payslip-table mb-3" style="background: #f8f9fa;">
            <tr>
                <th colspan="6" class="text-center" style="background: #e9ecef;">ATTENDANCE SUMMARY</th>
            </tr>
            <tr>
                <th style="width: 16%;">Working Days:</th>
                <td class="text-center" style="width: 17%;">{{ $salarySheet->working_days ?? 0 }}</td>
                <th style="width: 16%;">Present Days:</th>
                <td class="text-center text-success" style="width: 17%;"><strong>{{ $salarySheet->present_days ?? 0 }}</strong></td>
                <th style="width: 16%;">Absent Days:</th>
                <td class="text-center text-danger" style="width: 18%;"><strong>{{ $salarySheet->absent_days ?? 0 }}</strong></td>
            </tr>
            <tr>
                <th>Leave Days:</th>
                <td class="text-center text-info">{{ $salarySheet->leave_days ?? 0 }}</td>
                <th>Holiday/Weekend:</th>
                <td class="text-center text-secondary"><strong>{{ $salarySheet->holiday_days ?? 0 }}</strong></td>
                <th>Overtime Hours:</th>
                <td class="text-center text-primary">{{ $salarySheet->overtime_hours ?? 0 }}</td>
            </tr>
            <tr>
                <th>Salary Type:</th>
                <td class="text-center">
                    @if($salarySheet->salary_type == 'new_employee')
                        <span class="badge bg-info">New Employee</span>
                    @elseif($salarySheet->salary_type == 'retired_employee')
                        <span class="badge bg-warning">Retired</span>
                    @else
                        <span class="badge bg-primary">Regular</span>
                    @endif
                </td>
                <th>Total Days:</th>
                <td class="text-center">{{ ($salarySheet->working_days ?? 0) + ($salarySheet->holiday_days ?? 0) }}</td>
                <th>Attendance Rate:</th>
                <td class="text-center">
                    @php
                        $totalWorkDays = $salarySheet->working_days ?? 1;
                        $presentDays = $salarySheet->present_days ?? 0;
                        $attendanceRate = $totalWorkDays > 0 ? round(($presentDays / $totalWorkDays) * 100, 1) : 0;
                    @endphp
                    <span class="{{ $attendanceRate >= 80 ? 'text-success' : 'text-danger' }}"><strong>{{ $attendanceRate }}%</strong></span>
                </td>
            </tr>
            @php
                $totalCalendarDays = ($salarySheet->working_days ?? 0) + ($salarySheet->holiday_days ?? 0);
                $perDay = $totalCalendarDays > 0 ? ($salarySheet->gross_salary ?? 0) / $totalCalendarDays : 0;
                $earnedAmount = $perDay * (($salarySheet->present_days ?? 0) + ($salarySheet->leave_days ?? 0) + ($salarySheet->holiday_days ?? 0));
            @endphp
            <tr style="background: #fff3cd;">
                <td colspan="6" class="text-center">
                    <small>
                        <strong>Per Day Calculation:</strong>
                        Gross (৳{{ number_format($salarySheet->gross_salary ?? 0, 0) }}) ÷ Total Days ({{ $totalCalendarDays }}) =
                        <strong>৳{{ number_format($perDay, 2) }}/day</strong> |
                        <strong>Earned:</strong> ৳{{ number_format($earnedAmount, 0) }} |
                        <strong>Absent Deduction:</strong> ৳{{ number_format($salarySheet->absent_deduction ?? 0, 0) }}
                    </small>
                </td>
            </tr>
        </table>

        <div class="row">
            <!-- Earnings Section -->
            <div class="col-md-6 earnings-section">
                <h6 class="mb-2" style="background: #e8f5e9; padding: 10px; border-radius: 4px;">
                    <i class="bx bx-plus-circle"></i> EARNINGS
                </h6>
                <table class="payslip-table">
                    <tr>
                        <td>Basic Salary</td>
                        <td class="text-end">৳{{ number_format($salarySheet->basic_salary ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>House Rent Allowance</td>
                        <td class="text-end">৳{{ number_format($salarySheet->house_rent ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Medical Allowance</td>
                        <td class="text-end">৳{{ number_format($salarySheet->medical_allowance ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Transport Allowance</td>
                        <td class="text-end">৳{{ number_format($salarySheet->transport_allowance ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Other Allowance (Food/Conveyance)</td>
                        <td class="text-end">৳{{ number_format($salarySheet->other_allowance ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background: #f0f0f0;">
                        <td><strong>Gross Salary</strong></td>
                        <td class="text-end"><strong>৳{{ number_format($salarySheet->gross_salary ?? 0, 2) }}</strong></td>
                    </tr>
                    @if(($salarySheet->overtime_amount ?? 0) > 0)
                    <tr>
                        <td>Overtime ({{ $salarySheet->overtime_hours ?? 0 }} hrs)</td>
                        <td class="text-end text-success">৳{{ number_format($salarySheet->overtime_amount ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salarySheet->special_overtime_amount ?? 0) > 0)
                    <tr>
                        <td>Special Overtime</td>
                        <td class="text-end text-success">৳{{ number_format($salarySheet->special_overtime_amount ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salarySheet->grass_time_amount ?? 0) > 0)
                    <tr>
                        <td>Grass Time Amount</td>
                        <td class="text-end text-success">৳{{ number_format($salarySheet->grass_time_amount ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salarySheet->bonus ?? 0) > 0)
                    <tr>
                        <td>Attendance Bonus</td>
                        <td class="text-end text-success">৳{{ number_format($salarySheet->bonus ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salarySheet->other_bonus ?? 0) > 0)
                    <tr>
                        <td>Other Bonus (Festival/Performance)</td>
                        <td class="text-end text-success">৳{{ number_format($salarySheet->other_bonus ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="background: #c8e6c9; font-weight: bold;">
                        <td><strong>Total Earnings</strong></td>
                        <td class="text-end"><strong>৳{{ number_format($salarySheet->total_earning ?? 0, 2) }}</strong></td>
                    </tr>
                </table>
            </div>

            <!-- Deductions Section -->
            <div class="col-md-6">
                <h6 class="mb-2" style="background: #ffe8e8; padding: 10px; border-radius: 4px;">
                    <i class="bx bx-minus-circle"></i> DEDUCTIONS
                </h6>
                <table class="payslip-table">
                    @if(($salarySheet->absent_deduction ?? 0) > 0)
                    <tr>
                        <td>Absent Deduction ({{ $salarySheet->absent_days ?? 0 }} days)</td>
                        <td class="text-end text-danger">৳{{ number_format($salarySheet->absent_deduction ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salarySheet->late_deduction ?? 0) > 0)
                    <tr>
                        <td>Late Deduction</td>
                        <td class="text-end text-danger">৳{{ number_format($salarySheet->late_deduction ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salarySheet->tax ?? 0) > 0)
                    <tr>
                        <td>Income Tax</td>
                        <td class="text-end text-danger">৳{{ number_format($salarySheet->tax ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salarySheet->provident_fund ?? 0) > 0)
                    <tr>
                        <td>Provident Fund (PF)</td>
                        <td class="text-end text-danger">৳{{ number_format($salarySheet->provident_fund ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salarySheet->loan_deduction ?? 0) > 0)
                    <tr>
                        <td>Loan Installment</td>
                        <td class="text-end text-danger">৳{{ number_format($salarySheet->loan_deduction ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salarySheet->salary_advance_deduction ?? 0) > 0)
                    <tr>
                        <td>Salary Advance Deduction</td>
                        <td class="text-end text-danger">৳{{ number_format($salarySheet->salary_advance_deduction ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salarySheet->deduction ?? 0) > 0)
                    <tr>
                        <td>Other Deduction</td>
                        <td class="text-end text-danger">৳{{ number_format($salarySheet->deduction ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salarySheet->other_deduction ?? 0) > 0)
                    <tr>
                        <td>Stamp Charge</td>
                        <td class="text-end text-danger">৳{{ number_format($salarySheet->other_deduction ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salarySheet->total_deduction ?? 0) == 0)
                    <tr>
                        <td colspan="2" class="text-center text-muted">No deductions</td>
                    </tr>
                    @endif
                    <tr style="background: #ffcdd2; font-weight: bold;">
                        <td><strong>Total Deductions</strong></td>
                        <td class="text-end"><strong>৳{{ number_format($salarySheet->total_deduction ?? 0, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Net Salary -->
        <table class="payslip-table mt-3 net-pay-section">
            <tr>
                <td style="width: 70%; font-size: 18px; padding: 15px;">
                    <strong>NET SALARY PAYABLE</strong>
                </td>
                <td style="width: 30%; text-align: right; font-size: 22px; padding: 15px;">
                    <strong>৳{{ number_format($salarySheet->net_salary ?? 0, 2) }}</strong>
                </td>
            </tr>
        </table>

        <div class="mt-3">
            <small><strong>In Words:</strong> {{ numberToWords($salarySheet->net_salary ?? 0) }} Taka Only</small>
        </div>

        <!-- Bank Info -->
        @php
            $bankInfo = $employee->employeeBankInfo()->where('is_primary', 'yes')->first();
        @endphp
        @if($bankInfo)
        <table class="payslip-table mt-3" style="background: #f8f9fa;">
            <tr>
                <th colspan="2" class="text-center" style="background: #e9ecef;">BANK DETAILS</th>
            </tr>
            <tr>
                <th style="width: 50%;">Bank Name:</th>
                <td>{{ $bankInfo->bank_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Branch Name:</th>
                <td>{{ $bankInfo->branch_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Account Number:</th>
                <td>{{ $bankInfo->account_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Account Holder Name:</th>
                <td>{{ $bankInfo->account_holder_name ?? 'N/A' }}</td>
            </tr>
        </table>
        @endif

        <div class="payslip-footer">
            <p class="mb-0">
                <small>
                    This is a computer-generated pay slip and does not require a signature.<br>
                    Generated on: {{ date('d F Y, h:i A') }} |
                    Salary Sheet ID: {{ $salarySheet->id }}
                </small>
            </p>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success btn-lg">
            <i class="bx bx-printer"></i> Print Pay Slip
        </button>
    </div>

</div>

@endsection
