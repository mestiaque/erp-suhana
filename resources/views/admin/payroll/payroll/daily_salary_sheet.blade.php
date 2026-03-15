@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Daily Salary Sheet') }}</title>
@endsection

@push('css')
<style>
    .salary-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    table.table thead { background: #00b894; color: white; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Daily Salary Sheet</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Payroll</li>
        <li class="item">Daily Salary Sheet</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="salary-card">
        <form action="{{ route('admin.payroll.dailySalarySheet') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Date</label>
                <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Department</label>
                <select name="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Generate</button>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="button" onclick="window.print()" class="btn btn-success w-100"><i class="bx bx-printer"></i> Print</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Daily Rate</th>
                        <th>Status</th>
                        <th>Working Hours</th>
                        <th>Daily Salary</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalSalary = 0; @endphp
                    @forelse($dailySalaries ?? [] as $i => $salary)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $salary->employee_id ?? 'N/A' }}</td>
                        <td>{{ $salary->name }}</td>
                        <td>{{ $salary->department_name ?? 'N/A' }}</td>
                        <td>{{ $salary->designation_name ?? 'N/A' }}</td>
                        <td>৳{{ number_format($salary->daily_rate ?? 0, 2) }}</td>
                        <td>
                            @if($salary->status == 'present')
                                <span class="badge bg-success">Present</span>
                            @else
                                <span class="badge bg-danger">Absent</span>
                            @endif
                        </td>
                        <td>{{ $salary->working_hours ?? 0 }} hrs</td>
                        <td><strong>৳{{ number_format($salary->daily_salary ?? 0, 2) }}</strong></td>
                    </tr>
                    @php $totalSalary += $salary->daily_salary ?? 0; @endphp
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No data available</td>
                    </tr>
                    @endforelse
                    @if(count($dailySalaries ?? []) > 0)
                    <tr>
                        <th colspan="8" class="text-end">Total:</th>
                        <th>৳{{ number_format($totalSalary, 2) }}</th>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
