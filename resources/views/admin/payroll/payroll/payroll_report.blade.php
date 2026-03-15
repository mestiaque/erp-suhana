@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Payroll Report') }}</title>
@endsection

@push('css')
<style>
    .summary-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; }
    .stat-card.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .stat-card.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-card.red { background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%); }
    .stat-value { font-size: 24px; font-weight: bold; }
    .stat-label { font-size: 14px; opacity: 0.9; }
</style>
@endpush

@section('contents')

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    {{-- Summary Cards --}}
    <div class="row mb-3 ">
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
                        <small class="text-muted">Total Employees</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Gross Salary --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background: #28a74538">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">৳{{ number_format($summary['total_gross'], 0) }}</h5>
                        <small class="text-muted">Total Gross Salary</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Net Payable --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background: #ffc10738">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">৳{{ number_format($summary['total_net'], 0) }}</h5>
                        <small class="text-muted">Total Net Payable</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Paid / Pending --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background: #dc354538">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-user-times"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $summary['paid_count'] ?? 0 }}/{{ $summary['pending_count'] ?? 0 }}</h4>
                        <small class="text-muted">Paid / Pending</small>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Filter Form --}}
    <div class="summary-card">
        <form action="{{ route('admin.reports.payroll') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <label>Month</label>
                <input type="month" name="month" value="{{ $monthParam }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Load Report</button>
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <button type="button" onclick="window.print()" class="btn btn-success w-100"><i class="bx bx-printer"></i> Print</button>
            </div>
        </form>
    </div>

    {{-- Payroll Table --}}
    <div class="summary-card">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Basic Salary</th>
                        <th>Allowances</th>
                        <th>Gross Salary</th>
                        <th>Deductions</th>
                        <th>Net Salary</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalBasic = 0;
                        $totalAllowances = 0;
                        $totalGross = 0;
                        $totalDeductions = 0;
                        $totalNet = 0;
                    @endphp
                    @forelse($salaries as $key => $salary)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            <strong>{{ $salary->user->name ?? 'N/A' }}</strong><br>
                            <small class="text-muted">{{ $salary->user->employee_id ?? '' }}</small>
                        </td>
                        <td>{{ $salary->user->department->name ?? 'N/A' }}</td>
                        <td>{{ $salary->user->designation->name ?? 'N/A' }}</td>
                        <td>৳{{ number_format($salary->basic_salary, 2) }}</td>
                        <td>৳{{ number_format($salary->house_rent + $salary->medical_allowance + $salary->transport_allowance + $salary->other_allowance, 2) }}</td>
                        <td>৳{{ number_format($salary->gross_salary, 2) }}</td>
                        <td>৳{{ number_format($salary->total_deduction, 2) }}</td>
                        <td><strong>৳{{ number_format($salary->net_salary, 2) }}</strong></td>
                        <td>
                            @if($salary->payment_status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($salary->payment_status == 'held')
                                <span class="badge bg-warning">Held</span>
                            @else
                                <span class="badge bg-danger">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @php
                        $totalBasic += $salary->basic_salary;
                        $totalAllowances += $salary->house_rent + $salary->medical_allowance + $salary->transport_allowance + $salary->other_allowance;
                        $totalGross += $salary->gross_salary;
                        $totalDeductions += $salary->total_deduction;
                        $totalNet += $salary->net_salary;
                    @endphp
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No payroll data available for this month</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($salaries->count() > 0)
                <tfoot>
                    <tr style="background: #f8f9fa; font-weight: bold;">
                        <td colspan="4" class="text-end">TOTAL</td>
                        <td>৳{{ number_format($totalBasic, 2) }}</td>
                        <td>৳{{ number_format($totalAllowances, 2) }}</td>
                        <td>৳{{ number_format($totalGross, 2) }}</td>
                        <td>৳{{ number_format($totalDeductions, 2) }}</td>
                        <td>৳{{ number_format($totalNet, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        {{-- Pagination --}}
        @if($salaries->count() > 0)
        <div class="mt-3">
            {{ $salaries->links() }}
        </div>
        @endif
    </div>

</div>

@endsection
