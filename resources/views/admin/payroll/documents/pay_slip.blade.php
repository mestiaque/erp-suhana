@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Pay Slip') }}</title>
@endsection

@push('css')
<style>
    .payslip-container { max-width: 700px; margin: 0 auto; background: white; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border: 2px solid #333; }
    .payslip-header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 20px; margin: -30px -30px 20px -30px; text-align: center; }
    .payslip-table { width: 100%; border-collapse: collapse; }
    .payslip-table td, .payslip-table th { padding: 10px; border: 1px solid #ddd; }
    .payslip-table th { background: #f8f9fa; text-align: left; }
    .payslip-footer { background: #e8f5e9; padding: 15px; margin: 20px -30px -30px -30px; text-align: center; }

    @media print {
        /* Hide all UI elements */
        .no-print, .breadcrumb-area, .doc-card, form, button, .btn,
        .sidebar, .sidenav, .main-menu, .header-navbar, .footer,
        .sidebar-wrapper, .app-sidebar, .app-header, .app-footer,
        .sidemenu-area, .sidemenu-header, .sidemenu-body,
        .navbar, .top-navbar, .footer-area,
        nav, header, .navbar, .footer-area, .main-menu, .side-menu, .side-nav, .page-header, .page-footer, .layout-header, .layout-footer, .layout-sidebar, .sidebar-menu, .sidebar-footer, .sidebar-header, .sidebar-content, .sidebar, .footer, .header, .sidemenu, .sidemenu-area, .sidemenu-header, .sidemenu-body { display: none !important; }

        /* Show only document content */
        body { margin: 0 !important; padding: 5mm !important; background: white !important; }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .flex-grow-1 { display: block !important; }
        .payslip-container {
            max-width: 100%;
            border: 1px solid #333;
            page-break-inside: avoid;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .payslip-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Remove shadows for clean print */
        * { box-shadow: none !important; border: none !important; }
    }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area no-print">
    <h1>Generate Pay Slip</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Documents</li>
        <li class="item">Pay Slip</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="no-print" style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <form action="{{ route('admin.documents.paySlip') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <label>Select Employee</label>
                <select name="employee_id" class="form-control" required>
                    <option value="">Choose Employee</option>
                    @foreach($employees ?? [] as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->employee_id }} - {{ $emp->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label>Month</label>
                <input type="month" name="month" value="{{ request('month', date('Y-m')) }}" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-file-blank"></i> Generate</button>
            </div>
        </form>
    </div>

    @if($employee ?? false)
    @php
        $month = request('month', date('Y-m'));
        $monthName = date('F Y', strtotime($month . '-01'));
        $year = date('Y', strtotime($month . '-01'));
        $monthNum = date('m', strtotime($month . '-01'));

        // Get salary sheet data if exists
        $salarySheet = \App\Models\SalarySheet::where('user_id', $employee->id)
            ->where('year', $year)
            ->where('month', $monthNum)
            ->first();

        // Basic salary components
        $basicSalary = $salarySheet->basic_salary ?? $employee->basic_salary ?? 0;
        $houseRent = $salarySheet->house_rent ?? $employee->house_rent ?? 0;
        $medical = $salarySheet->medical_allowance ?? $employee->medical_allowance ?? 0;
        $transport = $salarySheet->transport_allowance ?? $employee->transport_allowance ?? 0;
        $food = $salarySheet->food_allowance ?? $employee->food_allowance ?? 0;
        $conveyance = $salarySheet->conveyance_allowance ?? $employee->conveyance_allowance ?? 0;
        $otherAllowance = $salarySheet->other_allowance ?? $employee->other_allowance ?? 0;
        $grossSalary = $salarySheet->gross_salary ?? ($basicSalary + $houseRent + $medical + $transport + $food + $conveyance + $otherAllowance);

        // Extra Earnings
        $overtime = $salarySheet->overtime_amount ?? 0;
        $specialOvertime = $salarySheet->special_overtime_amount ?? 0;
        $grassTime = $salarySheet->grass_time_amount ?? 0;
        $attendanceBonus = $salarySheet->attendance_bonus ?? 0;
        $bonus = $salarySheet->bonus_amount ?? 0;
        $otherBonus = $salarySheet->other_bonus ?? 0;

        $totalEarnings = $grossSalary + $overtime + $specialOvertime + $grassTime + $attendanceBonus + $bonus + $otherBonus;

        // Deductions
        $absentDeduction = $salarySheet->absent_deduction ?? 0;
        $lateDeduction = $salarySheet->late_deduction ?? 0;
        $taxDeduction = $salarySheet->tax_deduction ?? 0;
        $pfDeduction = $salarySheet->provident_fund_deduction ?? $employee->provident_fund ?? 0;
        $loanDeduction = $salarySheet->loan_deduction ?? 0;
        $advanceDeduction = $salarySheet->salary_advance_deduction ?? 0;
        $otherDeductions = $salarySheet->deduction ?? 0;
        $stampCharge = $salarySheet->stamp_charge ?? 0;

        $totalDeductions = $absentDeduction + $lateDeduction + $taxDeduction + $pfDeduction + $loanDeduction + $advanceDeduction + $otherDeductions + $stampCharge;
        $netSalary = $salarySheet->net_salary ?? ($totalEarnings - $totalDeductions);

        // Attendance Info
        $workingDays = $salarySheet->working_days ?? 0;
        $presentDays = $salarySheet->present_days ?? 0;
        $absentDays = $salarySheet->absent_days ?? 0;
        $leaveDays = $salarySheet->leave_days ?? 0;
        $holidayDays = $salarySheet->holiday_days ?? 0;
        $lateDays = $salarySheet->late_count ?? 0;
        $overtimeHours = $salarySheet->overtime_hours ?? 0;
    @endphp

    <div class="payslip-container">
        <div class="payslip-header">
            <h3>{{ general()->title }}</h3>
            <p class="mb-1">{{ general()->address_one ?? '' }}</p>
            <p class="mb-0">SALARY SLIP - {{ strtoupper($monthName) }}</p>
        </div>

        <table class="payslip-table mb-3">
            <tr>
                <th>Employee Name:</th>
                <td><strong>{{ $employee->name }}</strong></td>
                <th>Employee ID:</th>
                <td>{{ $employee->employee_id ?? 'N/A' }}</td>
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
        </table>

        {{-- Attendance Summary --}}
        <h6 class="mb-2" style="background: #e3f2fd; padding: 8px;">ATTENDANCE SUMMARY</h6>
        <table class="payslip-table mb-3">
            <tr>
                <th style="width: 16%;">Working Days</th>
                <td class="text-center" style="width: 17%;">{{ $workingDays }}</td>
                <th style="width: 16%;">Present Days</th>
                <td class="text-center text-success" style="width: 17%;"><strong>{{ $presentDays }}</strong></td>
                <th style="width: 16%;">Absent Days</th>
                <td class="text-center text-danger" style="width: 18%;"><strong>{{ $absentDays }}</strong></td>
            </tr>
            <tr>
                <th>Leave Days</th>
                <td class="text-center text-info">{{ $leaveDays }}</td>
                <th>Holiday/Weekend</th>
                <td class="text-center text-secondary"><strong>{{ $holidayDays }}</strong></td>
                <th>Overtime Hours</th>
                <td class="text-center text-primary">{{ $overtimeHours }}</td>
            </tr>
            <tr>
                <th>Total Days</th>
                <td class="text-center">{{ $workingDays + $holidayDays }}</td>
                <th>Attendance Rate</th>
                <td class="text-center">
                    @php
                        $attendanceRate = $workingDays > 0 ? round(($presentDays / $workingDays) * 100, 1) : 0;
                    @endphp
                    <span class="{{ $attendanceRate >= 80 ? 'text-success' : 'text-danger' }}"><strong>{{ $attendanceRate }}%</strong></span>
                </td>
                <th>Late Days</th>
                <td class="text-center text-warning">{{ $lateDays }}</td>
            </tr>
            @php
                $totalCalendarDays = $workingDays + $holidayDays;
                $perDay = $totalCalendarDays > 0 ? $grossSalary / $totalCalendarDays : 0;
            @endphp
            <tr style="background: #fff3cd;">
                <td colspan="6" class="text-center">
                    <small>
                        <strong>Per Day:</strong> ৳{{ number_format($perDay, 2) }} |
                        <strong>Absent Deduction:</strong> ৳{{ number_format($absentDeduction, 0) }} |
                        <strong>Late Deduction:</strong> ৳{{ number_format($lateDeduction, 0) }}
                    </small>
                </td>
            </tr>
        </table>

        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-2" style="background: #e8f5e9; padding: 8px;">EARNINGS</h6>
                <table class="payslip-table">
                    <tr>
                        <td>Basic Salary</td>
                        <td class="text-end">৳{{ number_format($basicSalary, 2) }}</td>
                    </tr>
                    <tr>
                        <td>House Rent</td>
                        <td class="text-end">৳{{ number_format($houseRent, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Medical Allowance</td>
                        <td class="text-end">৳{{ number_format($medical, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Transport Allowance</td>
                        <td class="text-end">৳{{ number_format($transport, 2) }}</td>
                    </tr>
                    @if($food > 0)
                    <tr>
                        <td>Food Allowance</td>
                        <td class="text-end">৳{{ number_format($food, 2) }}</td>
                    </tr>
                    @endif
                    @if($conveyance > 0)
                    <tr>
                        <td>Conveyance</td>
                        <td class="text-end">৳{{ number_format($conveyance, 2) }}</td>
                    </tr>
                    @endif
                    @if($otherAllowance > 0)
                    <tr>
                        <td>Other Allowance</td>
                        <td class="text-end">৳{{ number_format($otherAllowance, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="background: #d4edda;">
                        <td><strong>Gross Salary</strong></td>
                        <td class="text-end"><strong>৳{{ number_format($grossSalary, 2) }}</strong></td>
                    </tr>
                    @if($overtime > 0)
                    <tr>
                        <td>Overtime</td>
                        <td class="text-end">৳{{ number_format($overtime, 2) }}</td>
                    </tr>
                    @endif
                    @if($specialOvertime > 0)
                    <tr>
                        <td>Special Overtime</td>
                        <td class="text-end">৳{{ number_format($specialOvertime, 2) }}</td>
                    </tr>
                    @endif
                    @if($grassTime > 0)
                    <tr>
                        <td>Grass Time</td>
                        <td class="text-end">৳{{ number_format($grassTime, 2) }}</td>
                    </tr>
                    @endif
                    @if($attendanceBonus > 0)
                    <tr>
                        <td>Attendance Bonus</td>
                        <td class="text-end">৳{{ number_format($attendanceBonus, 2) }}</td>
                    </tr>
                    @endif
                    @if($bonus > 0)
                    <tr>
                        <td>Bonus</td>
                        <td class="text-end">৳{{ number_format($bonus, 2) }}</td>
                    </tr>
                    @endif
                    @if($otherBonus > 0)
                    <tr>
                        <td>Other Bonus</td>
                        <td class="text-end">৳{{ number_format($otherBonus, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="background: #fffae6;">
                        <td><strong>Total Earnings</strong></td>
                        <td class="text-end"><strong>৳{{ number_format($totalEarnings, 2) }}</strong></td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <h6 class="mb-2" style="background: #ffe8e8; padding: 8px;">DEDUCTIONS</h6>
                <table class="payslip-table">
                    @if($absentDeduction > 0)
                    <tr>
                        <td>Absent Deduction</td>
                        <td class="text-end">৳{{ number_format($absentDeduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($lateDeduction > 0)
                    <tr>
                        <td>Late Deduction</td>
                        <td class="text-end">৳{{ number_format($lateDeduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($taxDeduction > 0)
                    <tr>
                        <td>Tax Deduction</td>
                        <td class="text-end">৳{{ number_format($taxDeduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($pfDeduction > 0)
                    <tr>
                        <td>Provident Fund</td>
                        <td class="text-end">৳{{ number_format($pfDeduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($loanDeduction > 0)
                    <tr>
                        <td>Loan Deduction</td>
                        <td class="text-end">৳{{ number_format($loanDeduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($advanceDeduction > 0)
                    <tr>
                        <td>Salary Advance</td>
                        <td class="text-end">৳{{ number_format($advanceDeduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($otherDeductions > 0)
                    <tr>
                        <td>Other Deductions</td>
                        <td class="text-end">৳{{ number_format($otherDeductions, 2) }}</td>
                    </tr>
                    @endif
                    @if($stampCharge > 0)
                    <tr>
                        <td>Stamp Charge</td>
                        <td class="text-end">৳{{ number_format($stampCharge, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="background: #ffe8e8;">
                        <td><strong>Total Deductions</strong></td>
                        <td class="text-end"><strong>৳{{ number_format($totalDeductions, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="payslip-table mt-3" style="background: #e3f2fd;">
            <tr>
                <td style="font-size: 16px;"><strong>NET SALARY PAYABLE</strong></td>
                <td class="text-end" style="font-size: 18px;"><strong>৳{{ number_format($netSalary, 2) }}</strong></td>
            </tr>
        </table>

        <div class="mt-4">
            <small><strong>In Words:</strong> {{ numberToWords($netSalary) }} Taka Only</small>
        </div>

        <div class="payslip-footer">
            <p class="mb-0"><small>This is a computer-generated pay slip and does not require a signature.<br>
            Generated on: {{ date('d F Y, h:i A') }}</small></p>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success btn-lg"><i class="bx bx-printer"></i> Print Pay Slip</button>
        <a href="{{ route('admin.documents.paySlip') }}" class="btn btn-secondary btn-lg">Generate Another</a>
    </div>
    @endif

</div>

@endsection
