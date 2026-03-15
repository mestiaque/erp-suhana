@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Newly Joined Employees') }}</title>
@endsection

@push('css')
<style>
    .joined-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .welcome-banner { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
    .welcome-banner h2 { margin: 0; font-size: 36px; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Newly Joined Employees</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Reports</li>
        <li class="item">Newly Joined</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="welcome-banner">
        <h2><i class="bx bx-user-plus"></i> {{ count($employees ?? []) }} New Employees</h2>
        <p>Welcome to our organization!</p>
    </div>

    <div class="joined-card">
        <form action="{{ route('admin.reports.employees.newlyJoined') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date', date('Y-m-01')) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date', date('Y-m-d')) }}" class="form-control">
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
                        <th>Days Since Joined</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees ?? [] as $i => $emp)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $emp->employee_id ?? 'N/A' }}</td>
                        <td>
                            <strong>{{ $emp->name }}</strong>
                            <span class="badge bg-success ms-2">New</span>
                        </td>
                        <td>{{ $emp->department->name ?? 'N/A' }}</td>
                        <td>{{ $emp->designation ?? 'N/A' }}</td>
                        <td>{{ $emp->joining_date ? date('d M Y', strtotime($emp->joining_date)) : 'N/A' }}</td>
                        <td>
                            @if($emp->joining_date)
                            {{ \Carbon\Carbon::parse($emp->joining_date)->diffInDays(now()) }} days
                            @else
                            N/A
                            @endif
                        </td>
                        <td>{{ $emp->phone }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No newly joined employees in this period</td>
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
