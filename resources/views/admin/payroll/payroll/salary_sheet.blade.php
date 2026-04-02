@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Monthly Salary Sheet') }}</title>
@endsection

@push('css')
<style>
    .salary-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    table.table thead { background: #6c5ce7; color: white; }
    table.table { font-size: 13px; }
    .stat-box { padding: 15px; border-radius: 8px; text-align: center; }
</style>
@endpush



@section('contents')

<div class="breadcrumb-area">
    <h1>Monthly Salary Sheet</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Payroll</li>
        <li class="item">Salary Sheet</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

<div class="row mb-3">
    {{-- Employees --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #007bff38">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                     style="width:50px;height:50px;">
                    <i class="fa fa-users"></i>
                </div>
                <div>
                    <h4 class="mb-0">{{ $summary['total_employees'] ?? 0 }}</h4>
                    <small class="text-muted">Employees</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Gross Salary --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #17a2b838">
            <div class="card-body d-flex align-items-center">
                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                     style="width:50px;height:50px;">
                    <i class="fa fa-money-bill-wave"></i>
                </div>
                <div>
                    <h5 class="mb-0">৳{{ number_format($summary['total_gross'] ?? 0, 0) }}</h5>
                    <small class="text-muted">Gross Salary</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Deduction --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #dc354538">
            <div class="card-body d-flex align-items-center">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                     style="width:50px;height:50px;">
                    <i class="fa fa-minus-circle"></i>
                </div>
                <div>
                    <h5 class="mb-0">৳{{ number_format($summary['total_deduction'] ?? 0, 0) }}</h5>
                    <small class="text-muted">Total Deduction</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Net Salary --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #28a74538">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                     style="width:50px;height:50px;">
                    <i class="fa fa-wallet"></i>
                </div>
                <div>
                    <h4 class="mb-0">৳{{ number_format($summary['total_net'] ?? 0, 0) }}</h4>
                    <small class="text-muted">Net Salary</small>
                </div>
            </div>
        </div>
    </div>
</div>


    <div class="salary-card">
        <form action="{{ route('admin.payroll.salarySheet') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-2">
                <label>Month</label>
                <select name="month" class="form-control form-control-sm">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ ($month ?? date('n')) == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label>Year</label>
                <select name="year" class="form-control form-control-sm">
                    @php
                        $currentYear = date('Y');
                    @endphp

                    @for ($y = 2000; $y <= 2099; $y++)
                        <option value="{{ $y }}"
                            {{ ($year ?? $currentYear) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label>Department</label>
                <select name="department_id" class="form-control form-control-sm">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Employee</label>
                <select name="employee_id" class="form-control form-control-sm">
                    <option value="">All Employees</option>
                    @foreach($employees ?? [] as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }} ({{ $emp->employee_id ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Attendance From</label>
                <input type="date" name="attendance_start_date" class="form-control form-control-sm" value="{{ request('attendance_start_date') }}">
            </div>
            <div class="col-md-2">
                <label>Attendance To</label>
                <input type="date" name="attendance_end_date" class="form-control form-control-sm" value="{{ request('attendance_end_date') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-sm btn-primary w-50 mr-1"><i class="bx bx-search"></i> Filter</button>
                 <a href="{{ route('admin.payroll.salarySheet') }}" class="btn btn-sm btn-secondary"><i class="bx bx-reset"></i> Reset</a>
            </div>
            </form>

        <div class="row mt-3 mb-3">
            <div class="col-md-12">
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#generateSalaryModal"><i class="bx bx-calculator"></i> Generate Salary</button>
                <a href="{{ route('admin.payroll.salarySheetExport', ['month' => $month, 'year' => $year]) }}" class="btn btn-sm btn-info"><i class="bx bx-printer"></i> Print All</a>
            </div>
        </div>

        <!-- Generate Salary Modal -->
        <div class="modal fade" id="generateSalaryModal" tabindex="-1" aria-labelledby="generateSalaryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="generateSalaryModalLabel">Process Monthly Salary</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.payroll.process') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Select Month</label>
                                <select name="month" class="form-control" required>
                                    @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Select Year</label>
                                <select name="year" class="form-control" required>
                                    @php
                                        $currentYear = date('Y');
                                    @endphp

                                    @for ($y = 2000; $y <= 2099; $y++)
                                        <option value="{{ $y }}"
                                            {{ ($year ?? $currentYear) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Attendance From Date (Optional)</label>
                                    <input
                                        type="date"
                                        name="attendance_start_date"
                                        class="form-control"
                                        value="{{ request('attendance_start_date', \Carbon\Carbon::createFromDate($year, $month, 1)->format('Y-m-d')) }}"
                                    >
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Attendance To Date (Optional)</label>
                                    <input
                                        type="date"
                                        name="attendance_end_date"
                                        class="form-control"
                                        value="{{ request('attendance_end_date', \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d')) }}"
                                    >
                                </div>
                            </div>
                            <small class="text-muted d-block mb-2">If provided, salary attendance will be calculated using this custom date range.</small>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="reprocess" id="reprocess" value="1">
                                    <label class="form-check-label" for="reprocess">
                                        Reprocess (Update existing salary)
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success"><i class="bx bx-calculator"></i> Process Salary</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" style="font-size: 11px;">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle text-center" style="min-width: 40px;">SL</th>
                        <th rowspan="2" class="align-middle text-center" style="min-width: 60px;">Emp ID</th>
                        <th rowspan="2" class="align-middle" style="min-width: 120px;">Name</th>
                        <th rowspan="2" class="align-middle" style="min-width: 80px;">Dept</th>
                        <th colspan="6" class="text-center bg-success text-white">Salary Components</th>
                        <th colspan="5" class="text-center bg-primary text-white">Extra Earnings</th>
                        <th colspan="7" class="text-center bg-danger text-white">Deductions</th>
                        <th colspan="5" class="text-center bg-info text-white">Attendance</th>
                        <th rowspan="2" class="align-middle text-center bg-warning" style="min-width: 80px;">Net Salary</th>
                        <th rowspan="2" class="align-middle text-center" style="min-width: 60px;">Status</th>
                        <th rowspan="2" class="align-middle text-center" style="min-width: 50px;">Action</th>
                    </tr>
                    <tr>
                        <!-- Salary Components -->
                        <th class="bg-success-light">Basic</th>
                        <th class="bg-success-light">House</th>
                        <th class="bg-success-light">Medical</th>
                        <th class="bg-success-light">Transport</th>
                        <th class="bg-success-light">Other</th>
                        <th class="bg-success-light">Gross</th>
                        <!-- Extra Earnings -->
                        <th class="bg-primary-light">OT</th>
                        <th class="bg-primary-light">Spc OT</th>
                        <th class="bg-primary-light">Grass</th>
                        <th class="bg-primary-light">Bonus</th>
                        <th class="bg-primary-light">Total Earn</th>
                        <!-- Deductions -->
                        <th class="bg-danger-light">Absent</th>
                        <th class="bg-danger-light">Late</th>
                        <th class="bg-danger-light">Tax</th>
                        <th class="bg-danger-light">PF</th>
                        <th class="bg-danger-light">Loan</th>
                        <th class="bg-danger-light">Advance</th>
                        <th class="bg-danger-light">Total Ded</th>
                        <!-- Attendance -->
                        <th class="bg-info-light">Work</th>
                        <th class="bg-info-light">Present</th>
                        <th class="bg-info-light">Absent</th>
                        <th class="bg-info-light">Leave</th>
                        <th class="bg-info-light">Holiday</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $totals = [
                        'basic' => 0, 'house' => 0, 'medical' => 0, 'transport' => 0, 'other' => 0, 'gross' => 0,
                        'overtime' => 0, 'special_overtime' => 0, 'grass_time' => 0, 'bonus' => 0, 'total_earning' => 0,
                        'absent' => 0, 'late' => 0, 'tax' => 0, 'pf' => 0, 'loan' => 0, 'advance' => 0, 'total_deduction' => 0,
                        'net' => 0
                    ];
                    @endphp
                    @forelse($salaries as $i => $salary)
                    @php
                        $user = $salary->user;
                        $empInfo = $user ?? null;
                        $totals['basic'] += $salary->basic_salary ?? 0;
                        $totals['house'] += $salary->house_rent ?? 0;
                        $totals['medical'] += $salary->medical_allowance ?? 0;
                        $totals['transport'] += $salary->transport_allowance ?? 0;
                        $totals['other'] += $salary->other_allowance ?? 0;
                        $totals['gross'] += $salary->gross_salary ?? 0;
                        $totals['overtime'] += $salary->overtime_amount ?? 0;
                        $totals['special_overtime'] += $salary->special_overtime_amount ?? 0;
                        $totals['grass_time'] += $salary->grass_time_amount ?? 0;
                        $totals['bonus'] += ($salary->bonus ?? 0) + ($salary->other_bonus ?? 0);
                        $totals['total_earning'] += $salary->total_earning ?? 0;
                        $totals['absent'] += $salary->absent_deduction ?? 0;
                        $totals['late'] += $salary->late_deduction ?? 0;
                        $totals['tax'] += $salary->tax ?? 0;
                        $totals['pf'] += $salary->provident_fund ?? 0;
                        $totals['loan'] += $salary->loan_deduction ?? 0;
                        $totals['advance'] += $salary->salary_advance_deduction ?? 0;
                        $totals['total_deduction'] += $salary->total_deduction ?? 0;
                        $totals['net'] += $salary->net_salary ?? 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-center">{{ $empInfo->employee_id ?? '--' }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                {!! $user->getAvt(25) ?? '' !!}
                                <small>{{ $user->name ?? '--' }}</small>
                            </div>
                        </td>
                        <td><small>{{ $empInfo->department->name ?? '--' }}</small></td>
                        <!-- Salary Components -->
                        <td class="text-end">{{ number_format($salary->basic_salary ?? 0, 0) }}</td>
                        <td class="text-end">{{ number_format($salary->house_rent ?? 0, 0) }}</td>
                        <td class="text-end">{{ number_format($salary->medical_allowance ?? 0, 0) }}</td>
                        <td class="text-end">{{ number_format($salary->transport_allowance ?? 0, 0) }}</td>
                        <td class="text-end">{{ number_format($salary->other_allowance ?? 0, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($salary->gross_salary ?? 0, 0) }}</td>
                        <!-- Extra Earnings -->
                        <td class="text-end text-success">{{ number_format($salary->overtime_amount ?? 0, 0) }}</td>
                        <td class="text-end text-success">{{ number_format($salary->special_overtime_amount ?? 0, 0) }}</td>
                        <td class="text-end text-success">{{ number_format($salary->grass_time_amount ?? 0, 0) }}</td>
                        <td class="text-end text-success">{{ number_format(($salary->bonus ?? 0) + ($salary->other_bonus ?? 0), 0) }}</td>
                        <td class="text-end text-success fw-bold">{{ number_format($salary->total_earning ?? 0, 0) }}</td>
                        <!-- Deductions -->
                        <td class="text-end text-danger">{{ number_format($salary->absent_deduction ?? 0, 0) }}</td>
                        <td class="text-end text-danger">{{ number_format($salary->late_deduction ?? 0, 0) }}</td>
                        <td class="text-end text-danger">{{ number_format($salary->tax ?? 0, 0) }}</td>
                        <td class="text-end text-danger">{{ number_format($salary->provident_fund ?? 0, 0) }}</td>
                        <td class="text-end text-danger">{{ number_format($salary->loan_deduction ?? 0, 0) }}</td>
                        <td class="text-end text-danger">{{ number_format($salary->salary_advance_deduction ?? 0, 0) }}</td>
                        <td class="text-end text-danger fw-bold">{{ number_format($salary->total_deduction ?? 0, 0) }}</td>
                        <!-- Attendance -->
                        <td class="text-center">{{ $salary->working_days ?? 0 }}</td>
                        <td class="text-center text-success">{{ $salary->present_days ?? 0 }}</td>
                        <td class="text-center text-danger">{{ $salary->absent_days ?? 0 }}</td>
                        <td class="text-center text-info">{{ $salary->leave_days ?? 0 }}</td>
                        <td class="text-center text-secondary">{{ $salary->holiday_days ?? 0 }}</td>
                        <!-- Net & Status -->
                        <td class="text-end fw-bold bg-warning-subtle">৳{{ number_format($salary->net_salary ?? 0, 0) }}</td>
                        <td class="text-center">
                            @if($salary->payment_status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($salary->payment_status == 'held')
                                <span class="badge bg-danger">Held</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.payroll.paySlip', $salary->id) }}" class="btn btn-sm btn-primary" target="_blank" title="View Pay Slip">
                                <i class="bx bx-receipt"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="29" class="text-center text-muted py-4">No salary data available. Click "Generate Salary" to process.</td>
                    </tr>
                    @endforelse
                    @if(count($salaries ?? []) > 0)
                    <tr style="background: #e9ecef; font-weight: bold;">
                        <th colspan="4" class="text-end">TOTAL:</th>
                        <!-- Salary Components -->
                        <th class="text-end">৳{{ number_format($totals['basic'], 0) }}</th>
                        <th class="text-end">৳{{ number_format($totals['house'], 0) }}</th>
                        <th class="text-end">৳{{ number_format($totals['medical'], 0) }}</th>
                        <th class="text-end">৳{{ number_format($totals['transport'], 0) }}</th>
                        <th class="text-end">৳{{ number_format($totals['other'], 0) }}</th>
                        <th class="text-end">৳{{ number_format($totals['gross'], 0) }}</th>
                        <!-- Extra Earnings -->
                        <th class="text-end text-success">৳{{ number_format($totals['overtime'], 0) }}</th>
                        <th class="text-end text-success">৳{{ number_format($totals['special_overtime'], 0) }}</th>
                        <th class="text-end text-success">৳{{ number_format($totals['grass_time'], 0) }}</th>
                        <th class="text-end text-success">৳{{ number_format($totals['bonus'], 0) }}</th>
                        <th class="text-end text-success">৳{{ number_format($totals['total_earning'], 0) }}</th>
                        <!-- Deductions -->
                        <th class="text-end text-danger">৳{{ number_format($totals['absent'], 0) }}</th>
                        <th class="text-end text-danger">৳{{ number_format($totals['late'], 0) }}</th>
                        <th class="text-end text-danger">৳{{ number_format($totals['tax'], 0) }}</th>
                        <th class="text-end text-danger">৳{{ number_format($totals['pf'], 0) }}</th>
                        <th class="text-end text-danger">৳{{ number_format($totals['loan'], 0) }}</th>
                        <th class="text-end text-danger">৳{{ number_format($totals['advance'], 0) }}</th>
                        <th class="text-end text-danger">৳{{ number_format($totals['total_deduction'], 0) }}</th>
                        <!-- Attendance -->
                        <th colspan="5"></th>
                        <!-- Net -->
                        <th class="text-end bg-warning">৳{{ number_format($totals['net'], 0) }}</th>
                        <th colspan="2"></th>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('css')
<style>
    .bg-success-light { background-color: #d4edda !important; }
    .bg-primary-light { background-color: #cce5ff !important; }
    .bg-danger-light { background-color: #f8d7da !important; }
    .bg-info-light { background-color: #d1ecf1 !important; }
    .bg-warning-subtle { background-color: #fff3cd !important; }
</style>
@endpush
