@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Department-wise Employee Report') }}</title>
@endsection

@push('css')
<style>
    .department-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .dept-summary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
    .dept-summary h5 { margin: 0; }
    .dept-summary .count { font-size: 28px; font-weight: bold; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Department-wise Employee Report</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Reports</li>
        <li class="item">Department-wise</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="row mb-3">
        @foreach($summary ?? [] as $dept)
        <div class="col-md-4">
            <div class="dept-summary">
                <div>
                    <h5>{{ $dept->department_name ?? 'N/A' }}</h5>
                    <small>Department</small>
                </div>
                <div class="count">{{ $dept->employee_count ?? 0 }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="department-card">
        <form action="{{ route('admin.reports.employees.departmentWise') }}" method="GET" class="row g-3 mb-3">
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
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                        <th>Department</th>
                        <th>Total Employees</th>
                        <th>Active</th>
                        <th>Inactive</th>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Avg Salary</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departmentStats ?? [] as $i => $stat)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $stat->department_name }}</strong></td>
                        <td>{{ $stat->total_employees }}</td>
                        <td><span class="badge bg-success">{{ $stat->active_count ?? 0 }}</span></td>
                        <td><span class="badge bg-secondary">{{ $stat->inactive_count ?? 0 }}</span></td>
                        <td><span class="badge bg-primary">{{ $stat->male_count ?? 0 }}</span></td>
                        <td><span class="badge bg-danger">{{ $stat->female_count ?? 0 }}</span></td>
                        <td>৳{{ number_format($stat->avg_salary ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No department data found</td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($departmentStats ?? []) > 0)
                <tfoot class="table-warning">
                    <tr>
                        <td colspan="2" class="text-end"><strong>Grand Total:</strong></td>
                        <td><strong>{{ collect($departmentStats)->sum('total_employees') }}</strong></td>
                        <td><strong>{{ collect($departmentStats)->sum('active_count') }}</strong></td>
                        <td><strong>{{ collect($departmentStats)->sum('inactive_count') }}</strong></td>
                        <td><strong>{{ collect($departmentStats)->sum('male_count') }}</strong></td>
                        <td><strong>{{ collect($departmentStats)->sum('female_count') }}</strong></td>
                        <td>-</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <hr>

        <h5 class="mb-3">Detailed Employee List</h5>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th>SL</th>
                        <th>Emp ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Status</th>
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
                        <td>{{ ucfirst($emp->gender ?? 'N/A') }}</td>
                        <td>{{ $emp->phone }}</td>
                        <td>
                            <span class="badge bg-{{ $emp->employee_status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($emp->employee_status ?? 'N/A') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">No employees found</td>
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
