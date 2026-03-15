@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Absent Report') }}</title>
@endsection

@push('css')
<style>
    .report-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    table.table thead { background: #ff6b6b; color: white; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Last 7/10 Days Absent Report</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Attendance</li>
        <li class="item">Absent Report</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="report-card">
        <form action="{{ route('admin.attendance.absent.report') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Days Range</label>
                <select name="days" class="form-control">
                    <option value="7" {{ request('days', 7) == 7 ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="10" {{ request('days') == 10 ? 'selected' : '' }}>Last 10 Days</option>
                    <option value="15" {{ request('days') == 15 ? 'selected' : '' }}>Last 15 Days</option>
                    <option value="30" {{ request('days') == 30 ? 'selected' : '' }}>Last 30 Days</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Department</label>
                <select name="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-danger w-100"><i class="bx bx-search"></i> Filter</button>
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
                        <th>Mobile</th>
                        <th>Absent Days</th>
                        <th>Last Absent Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absentEmployees ?? [] as $i => $employee)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $employee->employee_id ?? 'N/A' }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($employee->photo)
                                    <img src="{{ asset('uploads/user_photo/' . $employee->photo) }}" alt="{{ $employee->name }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover; margin-right: 10px;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 35px; height: 35px; background-color: {{ random_color($employee->id ?? 0) }}; margin-right: 10px;">
                                        {{ strtoupper(substr($employee->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ $employee->name }}</span>
                            </div>
                        </td>
                        <td>{{ $employee->department->name ?? 'N/A' }}</td>
                        <td>{{ $employee->designation->name ?? 'N/A' }}</td>
                        <td>{{ $employee->mobile ?? 'N/A' }}</td>
                        <td><span class="badge bg-danger">{{ $employee->absent_count ?? 0 }}</span></td>
                        <td>{{ $employee->last_absent_date ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bx bx-check-circle" style="font-size: 48px; color: #28a745;"></i>
                            <p class="mb-0 mt-2">Great! No frequent absentees found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
