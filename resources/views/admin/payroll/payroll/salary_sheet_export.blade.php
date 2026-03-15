@extends('admin.layouts.print')
@section('title')
<title>Salary Sheet Export - {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</title>
@endsection

@section('contents')

<div class="print-area">
    <div class="text-center mb-4">
        <h4>Payroll Management System</h4>
        <h5>Monthly Salary Sheet - {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-sm" style="font-size: 9px;">
            <thead>
                <tr style="background: #333; color: #fff;">
                    <th rowspan="2">SL</th>
                    <th rowspan="2">Emp ID</th>
                    <th rowspan="2">Name</th>
                    <th rowspan="2">Department</th>
                    <th rowspan="2">Designation</th>
                    <th colspan="6" style="background: #28a745; color: #fff;">Salary Components</th>
                    <th colspan="5" style="background: #17a2b8; color: #fff;">Extra Earnings</th>
                    <th colspan="7" style="background: #dc3545; color: #fff;">Deductions</th>
                    <th colspan="4" style="background: #6c757d; color: #fff;">Attendance</th>
                    <th rowspan="2" style="background: #007bff; color: #fff;">Net Salary</th>
                    <th rowspan="2">Status</th>
                </tr>
                <tr>
                    {{-- Salary Components --}}
                    <th style="background: #d4edda;">Basic</th>
                    <th style="background: #d4edda;">House</th>
                    <th style="background: #d4edda;">Medical</th>
                    <th style="background: #d4edda;">Transport</th>
                    <th style="background: #d4edda;">Other</th>
                    <th style="background: #d4edda;">Gross</th>
                    {{-- Extra Earnings --}}
                    <th style="background: #d1ecf1;">OT</th>
                    <th style="background: #d1ecf1;">Special OT</th>
                    <th style="background: #d1ecf1;">Grass Time</th>
                    <th style="background: #d1ecf1;">Bonus</th>
                    <th style="background: #d1ecf1;">Total Earning</th>
                    {{-- Deductions --}}
                    <th style="background: #f8d7da;">Absent</th>
                    <th style="background: #f8d7da;">Late</th>
                    <th style="background: #f8d7da;">Tax</th>
                    <th style="background: #f8d7da;">PF</th>
                    <th style="background: #f8d7da;">Loan</th>
                    <th style="background: #f8d7da;">Advance</th>
                    <th style="background: #f8d7da;">Total Ded</th>
                    {{-- Attendance --}}
                    <th style="background: #e2e3e5;">Working</th>
                    <th style="background: #e2e3e5;">Present</th>
                    <th style="background: #e2e3e5;">Absent</th>
                    <th style="background: #e2e3e5;">Leave</th>
                    <th style="background: #e2e3e5;">Holiday</th>
                </tr>
            </thead>
            <tbody>
                @php
                $totals = [
                    'basic' => 0, 'house' => 0, 'medical' => 0, 'transport' => 0, 'other' => 0, 'gross' => 0,
                    'ot' => 0, 'special_ot' => 0, 'grass_time' => 0, 'bonus' => 0, 'total_earning' => 0,
                    'absent_ded' => 0, 'late_ded' => 0, 'tax' => 0, 'pf' => 0, 'loan' => 0, 'advance' => 0, 'total_ded' => 0,
                    'net' => 0
                ];
                @endphp

                @forelse($salaries as $key => $salary)
                @php
                $ot = $salary->overtime_amount ?? 0;
                $specialOt = $salary->special_overtime_amount ?? 0;
                $grassTime = $salary->grass_time_amount ?? 0;
                $bonus = $salary->bonus_amount ?? 0;
                $totalEarning = $salary->gross_salary + $ot + $specialOt + $grassTime + $bonus;

                $loanDed = $salary->loan_deduction ?? 0;
                $advanceDed = $salary->salary_advance_deduction ?? 0;
                $pfDed = $salary->provident_fund_deduction ?? 0;

                $totals['basic'] += $salary->basic_salary;
                $totals['house'] += $salary->house_rent;
                $totals['medical'] += $salary->medical_allowance;
                $totals['transport'] += $salary->transport_allowance;
                $totals['other'] += $salary->other_allowance;
                $totals['gross'] += $salary->gross_salary;
                $totals['ot'] += $ot;
                $totals['special_ot'] += $specialOt;
                $totals['grass_time'] += $grassTime;
                $totals['bonus'] += $bonus;
                $totals['total_earning'] += $totalEarning;
                $totals['absent_ded'] += $salary->absent_deduction;
                $totals['late_ded'] += $salary->late_deduction;
                $totals['tax'] += $salary->tax_deduction;
                $totals['pf'] += $pfDed;
                $totals['loan'] += $loanDed;
                $totals['advance'] += $advanceDed;
                $totals['total_ded'] += $salary->total_deduction;
                $totals['net'] += $salary->net_salary;
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $salary->user->employee_id ?? 'N/A' }}</td>
                    <td style="white-space: nowrap;">{{ $salary->user->name ?? 'N/A' }}</td>
                    <td>{{ $salary->user->employeeInfo->department->name ?? 'N/A' }}</td>
                    <td>{{ $salary->user->employeeInfo->designation->name ?? 'N/A' }}</td>
                    {{-- Salary Components --}}
                    <td>{{ number_format($salary->basic_salary, 0) }}</td>
                    <td>{{ number_format($salary->house_rent, 0) }}</td>
                    <td>{{ number_format($salary->medical_allowance, 0) }}</td>
                    <td>{{ number_format($salary->transport_allowance, 0) }}</td>
                    <td>{{ number_format($salary->other_allowance, 0) }}</td>
                    <td><strong>{{ number_format($salary->gross_salary, 0) }}</strong></td>
                    {{-- Extra Earnings --}}
                    <td>{{ number_format($ot, 0) }}</td>
                    <td>{{ number_format($specialOt, 0) }}</td>
                    <td>{{ number_format($grassTime, 0) }}</td>
                    <td>{{ number_format($bonus, 0) }}</td>
                    <td><strong>{{ number_format($totalEarning, 0) }}</strong></td>
                    {{-- Deductions --}}
                    <td>{{ number_format($salary->absent_deduction, 0) }}</td>
                    <td>{{ number_format($salary->late_deduction, 0) }}</td>
                    <td>{{ number_format($salary->tax_deduction, 0) }}</td>
                    <td>{{ number_format($pfDed, 0) }}</td>
                    <td>{{ number_format($loanDed, 0) }}</td>
                    <td>{{ number_format($advanceDed, 0) }}</td>
                    <td><strong>{{ number_format($salary->total_deduction, 0) }}</strong></td>
                    {{-- Attendance --}}
                    <td>{{ $salary->working_days ?? '-' }}</td>
                    <td>{{ $salary->present_days ?? '-' }}</td>
                    <td>{{ $salary->absent_days ?? '-' }}</td>
                    <td>{{ $salary->leave_days ?? '-' }}</td>
                    <td>{{ $salary->holiday_days ?? '-' }}</td>
                    {{-- Net Salary --}}
                    <td><strong>{{ number_format($salary->net_salary, 0) }}</strong></td>
                    <td>
                        @if($salary->payment_status == 'paid')
                        <span style="color: green;">Paid</span>
                        @elseif($salary->payment_status == 'held')
                        <span style="color: orange;">Held</span>
                        @else
                        <span style="color: red;">Pending</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="28" class="text-center">No salary records found for this month.</td>
                </tr>
                @endforelse

                @if($salaries->count() > 0)
                <tr style="background: #f0f0f0; font-weight: bold;">
                    <td colspan="5" class="text-right">Total</td>
                    {{-- Salary Components --}}
                    <td>{{ number_format($totals['basic'], 0) }}</td>
                    <td>{{ number_format($totals['house'], 0) }}</td>
                    <td>{{ number_format($totals['medical'], 0) }}</td>
                    <td>{{ number_format($totals['transport'], 0) }}</td>
                    <td>{{ number_format($totals['other'], 0) }}</td>
                    <td>{{ number_format($totals['gross'], 0) }}</td>
                    {{-- Extra Earnings --}}
                    <td>{{ number_format($totals['ot'], 0) }}</td>
                    <td>{{ number_format($totals['special_ot'], 0) }}</td>
                    <td>{{ number_format($totals['grass_time'], 0) }}</td>
                    <td>{{ number_format($totals['bonus'], 0) }}</td>
                    <td>{{ number_format($totals['total_earning'], 0) }}</td>
                    {{-- Deductions --}}
                    <td>{{ number_format($totals['absent_ded'], 0) }}</td>
                    <td>{{ number_format($totals['late_ded'], 0) }}</td>
                    <td>{{ number_format($totals['tax'], 0) }}</td>
                    <td>{{ number_format($totals['pf'], 0) }}</td>
                    <td>{{ number_format($totals['loan'], 0) }}</td>
                    <td>{{ number_format($totals['advance'], 0) }}</td>
                    <td>{{ number_format($totals['total_ded'], 0) }}</td>
                    {{-- Attendance --}}
                    <td colspan="4"></td>
                    {{-- Net Salary --}}
                    <td>{{ number_format($totals['net'], 0) }}</td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <div class="row">
            <div class="col-6">
                <p><strong>Total Employees:</strong> {{ $salaries->count() }}</p>
                <p><strong>Total Net Salary:</strong> {{ number_format($totals['net'], 2) }} BDT</p>
            </div>
            <div class="col-6 text-right">
                <p><strong>Generated on:</strong> {{ date('d-m-Y h:i A') }}</p>
                <p><strong>Generated by:</strong> {{ auth()->user()->name ?? 'System' }}</p>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <div class="row">
            <div class="col-4 text-center">
                <div style="border-top: 1px solid #000; padding-top: 5px;">
                    Prepared By
                </div>
            </div>
            <div class="col-4 text-center">
                <div style="border-top: 1px solid #000; padding-top: 5px;">
                    Checked By
                </div>
            </div>
            <div class="col-4 text-center">
                <div style="border-top: 1px solid #000; padding-top: 5px;">
                    Approved By
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
