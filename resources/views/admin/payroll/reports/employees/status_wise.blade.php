@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Status-wise Employee Report') }}</title>
@endsection

@push('css')
<style>
    .status-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .stat-box { text-align: center; padding: 20px; border-radius: 8px; margin-bottom: 15px; color: white; }
    .stat-box.active { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .stat-box.inactive { background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%); }
    .stat-box.retired { background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); }
    .stat-box h2 { margin: 0; font-size: 48px; font-weight: bold; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Status-wise Employee Report</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Reports</li>
        <li class="item">Status-wise</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="status-card">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-box active">
                    <h2>{{ $stats['active'] ?? 0 }}</h2>
                    <p>Active Employees</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box inactive">
                    <h2>{{ $stats['inactive'] ?? 0 }}</h2>
                    <p>Inactive Employees</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box retired">
                    <h2>{{ $stats['retired'] ?? 0 }}</h2>
                    <p>Retired Employees</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.reports.employees.statusWise') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                </select>
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
                        <th>Designation</th>
                        <th>Joining Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees ?? [] as $i => $emp)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $emp->employee_id ?? 'N/A' }}</td>
                        <td>{{ $emp->name }}</td>
                        <td>{{ $emp->department->name ?? 'N/A' }}</td>
                        <td>{{ $emp->designation ?? 'N/A' }}</td>
                        <td>{{ $emp->joining_date ? date('d M Y', strtotime($emp->joining_date)) : 'N/A' }}</td>
                        <td>
                            @if($emp->employee_status == 'active')
                            <span class="badge bg-success">Active</span>
                            @elseif($emp->employee_status == 'inactive')
                            <span class="badge bg-warning">Inactive</span>
                            @else
                            <span class="badge bg-secondary">Retired</span>
                            @endif
                        </td>
                        <td>
                            @if($emp->employee_status == 'retired' && $emp->retirement_date)
                            Retired on {{ date('d M Y', strtotime($emp->retirement_date)) }}
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No employees found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="text-end mt-3">
            <button onclick="window.print()" class="btn btn-secondary"><i class="bx bx-printer"></i> Print</button>
        </div>
    </div>

</div>

@endsection
