@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Gender-wise Employee Report') }}</title>
@endsection

@push('css')
<style>
    .gender-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .stat-box { text-align: center; padding: 20px; border-radius: 8px; margin-bottom: 15px; }
    .stat-box.male { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .stat-box.female { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
    .stat-box.other { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
    .stat-box h2 { margin: 0; font-size: 48px; font-weight: bold; }
    .stat-box p { margin: 10px 0 0; font-size: 16px; opacity: 0.9; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Gender-wise Employee Report</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Reports</li>
        <li class="item">Gender-wise</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="gender-card">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-box male">
                    <h2>{{ $stats['male'] ?? 0 }}</h2>
                    <p><i class="bx bx-male"></i> Male Employees</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box female">
                    <h2>{{ $stats['female'] ?? 0 }}</h2>
                    <p><i class="bx bx-female"></i> Female Employees</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box other">
                    <h2>{{ $stats['other'] ?? 0 }}</h2>
                    <p><i class="bx bx-user"></i> Other</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.reports.employees.genderWise') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="">All</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
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
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>SL</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Department</th>
                        <th>Designation</th>
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
                        <td>
                            @if($emp->gender == 'male')
                            <span class="badge bg-primary"><i class="bx bx-male"></i> Male</span>
                            @elseif($emp->gender == 'female')
                            <span class="badge bg-danger"><i class="bx bx-female"></i> Female</span>
                            @else
                            <span class="badge bg-info">Other</span>
                            @endif
                        </td>
                        <td>{{ $emp->department->name ?? 'N/A' }}</td>
                        <td>{{ $emp->designation ?? 'N/A' }}</td>
                        <td>{{ $emp->phone }}</td>
                        <td>
                            <span class="badge bg-{{ $emp->employee_status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($emp->employee_status ?? 'N/A') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No employees found</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="text-end"><strong>Total: {{ count($employees ?? []) }} employees</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-end mt-3">
            <button onclick="window.print()" class="btn btn-secondary"><i class="bx bx-printer"></i> Print</button>
        </div>
    </div>

</div>

@endsection
