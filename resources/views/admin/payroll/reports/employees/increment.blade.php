@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Increment Report') }}</title>
@endsection

@push('css')
<style>
    .increment-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .increment-badge { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 5px 15px; border-radius: 20px; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Employee Increment Report</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Reports</li>
        <li class="item">Increment</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="increment-card">
        <form action="{{ route('admin.reports.employees.increment') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Month</label>
                <input type="month" name="month" value="{{ request('month', date('Y-m')) }}" class="form-control">
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
            <div class="col-md-3">
                <label>Increment Type</label>
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="annual" {{ request('type') == 'annual' ? 'selected' : '' }}>Annual</option>
                    <option value="promotion" {{ request('type') == 'promotion' ? 'selected' : '' }}>Promotion</option>
                    <option value="special" {{ request('type') == 'special' ? 'selected' : '' }}>Special</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>SL</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Previous Salary</th>
                        <th>Increment Amount</th>
                        <th>New Salary</th>
                        <th>Increment %</th>
                        <th>Effective Date</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($increments ?? [] as $i => $inc)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $inc->employee->employee_id ?? 'N/A' }}</td>
                        <td>{{ $inc->employee->name ?? 'N/A' }}</td>
                        <td>{{ $inc->employee->department->name ?? 'N/A' }}</td>
                        <td>৳{{ number_format($inc->previous_salary ?? 0, 2) }}</td>
                        <td>
                            <span class="increment-badge">
                                +৳{{ number_format($inc->increment_amount ?? 0, 2) }}
                            </span>
                        </td>
                        <td><strong>৳{{ number_format($inc->new_salary ?? 0, 2) }}</strong></td>
                        <td>
                            @php
                                $percentage = $inc->previous_salary > 0 ? (($inc->increment_amount / $inc->previous_salary) * 100) : 0;
                            @endphp
                            <span class="badge bg-success">{{ number_format($percentage, 2) }}%</span>
                        </td>
                        <td>{{ $inc->effective_date ? date('d M Y', strtotime($inc->effective_date)) : 'N/A' }}</td>
                        <td>
                            <span class="badge bg-info">{{ ucfirst($inc->type ?? 'N/A') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No increment records found</td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($increments ?? []) > 0)
                <tfoot>
                    <tr class="table-warning">
                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                        <td><strong>৳{{ number_format($increments->sum('previous_salary'), 2) }}</strong></td>
                        <td><strong>৳{{ number_format($increments->sum('increment_amount'), 2) }}</strong></td>
                        <td><strong>৳{{ number_format($increments->sum('new_salary'), 2) }}</strong></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <div class="text-end mt-3">
            <button onclick="window.print()" class="btn btn-secondary"><i class="bx bx-printer"></i> Print</button>
        </div>
    </div>

</div>

@endsection
