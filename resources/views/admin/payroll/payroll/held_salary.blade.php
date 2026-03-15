@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Held-up Salary') }}</title>
@endsection

@push('css')
<style>
    .held-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    table.table thead { background: #e74c3c; color: white; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Held-up Salary</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Payroll</li>
        <li class="item">Held-up Salary</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="held-card">
        <form action="{{ route('admin.payroll.heldSalary') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <label>Month</label>
                <input type="month" name="month" value="{{ request('month', date('Y-m')) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label>Department</label>
                <select name="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-danger w-100"><i class="bx bx-search"></i> Search</button>
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
                        <th>Month</th>
                        <th>Net Salary</th>
                        <th>Held Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($heldSalaries ?? [] as $i => $salary)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $salary->employee_id ?? 'N/A' }}</td>
                        <td>{{ $salary->employee_name }}</td>
                        <td>{{ $salary->department_name ?? 'N/A' }}</td>
                        <td>{{ $salary->month }}</td>
                        <td><strong>৳{{ number_format($salary->net_salary ?? 0, 2) }}</strong></td>
                        <td>
                            <small class="text-danger">{{ $salary->held_reason ?? 'Not specified' }}</small>
                        </td>
                        <td>
                            <form action="{{ route('admin.payroll.markPaid', $salary->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Release this salary?')">
                                    <i class="bx bx-check"></i> Release
                                </button>
                            </form>
                            <a href="{{ route('admin.payroll.paySlip', $salary->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                <i class="bx bx-receipt"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bx bx-check-circle" style="font-size: 48px; color: #28a745;"></i>
                            <p class="mb-0 mt-2">No held-up salaries found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
