@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Salary Summary') }}</title>
@endsection

@push('css')
<style>
    .summary-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    table.table thead { background: #fdcb6e; color: white; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Salary Summary</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Payroll</li>
        <li class="item">Summary</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="summary-card">
        <form action="{{ route('admin.payroll.salarySummary') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <label>Month</label>
                <input type="month" name="month" value="{{ request('month', date('Y-m')) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Load Summary</button>
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <button type="button" onclick="window.print()" class="btn btn-success w-100"><i class="bx bx-printer"></i> Print</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Employees</th>
                        <th>Total Basic</th>
                        <th>Total Allowances</th>
                        <th>Total Deductions</th>
                        <th>Gross Salary</th>
                        <th>Net Payable</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = ['employees' => 0, 'basic' => 0, 'allowances' => 0, 'deductions' => 0, 'gross' => 0, 'net' => 0]; @endphp
                    @forelse($departmentSummary ?? [] as $dept)
                    <tr>
                        <td><strong>{{ $dept->department_name ?? 'N/A' }}</strong></td>
                        <td>{{ $dept->employee_count ?? 0 }}</td>
                        <td>৳{{ number_format($dept->total_basic ?? 0, 2) }}</td>
                        <td>৳{{ number_format($dept->total_allowances ?? 0, 2) }}</td>
                        <td>৳{{ number_format($dept->total_deductions ?? 0, 2) }}</td>
                        <td>৳{{ number_format($dept->gross_salary ?? 0, 2) }}</td>
                        <td><strong>৳{{ number_format($dept->net_salary ?? 0, 2) }}</strong></td>
                    </tr>
                    @php
                    $grandTotal['employees'] += $dept->employee_count ?? 0;
                    $grandTotal['basic'] += $dept->total_basic ?? 0;
                    $grandTotal['allowances'] += $dept->total_allowances ?? 0;
                    $grandTotal['deductions'] += $dept->total_deductions ?? 0;
                    $grandTotal['gross'] += $dept->gross_salary ?? 0;
                    $grandTotal['net'] += $dept->net_salary ?? 0;
                    @endphp
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No summary data available</td>
                    </tr>
                    @endforelse
                    @if(count($departmentSummary ?? []) > 0)
                    <tr style="background: #fff3cd; font-weight: bold; font-size: 15px;">
                        <td>GRAND TOTAL</td>
                        <td>{{ $grandTotal['employees'] }}</td>
                        <td>৳{{ number_format($grandTotal['basic'], 2) }}</td>
                        <td>৳{{ number_format($grandTotal['allowances'], 2) }}</td>
                        <td>৳{{ number_format($grandTotal['deductions'], 2) }}</td>
                        <td>৳{{ number_format($grandTotal['gross'], 2) }}</td>
                        <td>৳{{ number_format($grandTotal['net'], 2) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
