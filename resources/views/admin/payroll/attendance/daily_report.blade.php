@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Daily Attendance Report') }}</title>
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('public/admin/app-assets/vendors/css/tables/datatable/datatables.min.css') }}" />
<style>
    .report-card {
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .stat-box {
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }
    table.table thead { background: #56d2ff; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Daily Attendance Report</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Attendance</li>
        <li class="item">Daily Report</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <!-- Stats Cards -->
    <div class="row mb-3">
        <div class="col-md-2">
            <div class="stat-box bg-primary text-white">
                <h3>{{ $stats['total'] ?? 0 }}</h3>
                <p class="mb-0">Total Employees</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-box bg-success text-white">
                <h3>{{ $stats['present'] ?? 0 }}</h3>
                <p class="mb-0">Present</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-box bg-warning text-white">
                <h3>{{ $stats['late'] ?? 0 }}</h3>
                <p class="mb-0">Late</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-box bg-danger text-white">
                <h3>{{ $stats['absent'] ?? 0 }}</h3>
                <p class="mb-0">Absent</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-box bg-info text-white">
                <h3>{{ $stats['leave'] ?? 0 }}</h3>
                <p class="mb-0">On Leave</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-box bg-secondary text-white">
                <h3>{{ $stats['overtime'] ?? 0 }}</h3>
                <p class="mb-0">Overtime</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="report-card">
        <form action="{{ route('admin.attendance.daily.report') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label>Date</label>
                <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="leave" {{ request('status') == 'leave' ? 'selected' : '' }}>Leave</option>
                    <option value="holiday" {{ request('status') == 'holiday' ? 'selected' : '' }}>Holiday</option>
                    <option value="weekend" {{ request('status') == 'weekend' ? 'selected' : '' }}>Weekend</option>
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
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Filter</button>
            </div>
        </form>
    </div>

    <!-- Report Table -->
    <div class="report-card">
        <div class="table-responsive">
            <table class="table table-bordered" id="attendanceTable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Shift</th>
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th>Status</th>
                        <th>Late (min)</th>
                        <th>Working Hours</th>
                        <th>Overtime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances ?? [] as $i => $attendance)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $attendance->user->employee_id ?? 'N/A' }}</td>
                        <td>{{ $attendance->user->name ?? 'N/A' }}</td>
                        <td>{{ $attendance->user->department->name ?? 'N/A' }}</td>
                        <td>{{ $attendance->shift->name ?? ($attendance->user->employeeInfo->shift->name ?? 'N/A') }}</td>
                        <td>{{ $attendance->in_time ? \Carbon\Carbon::parse($attendance->in_time)->format('h:i A') : '-' }}</td>
                        <td>{{ $attendance->out_time ? \Carbon\Carbon::parse($attendance->out_time)->format('h:i A') : '-' }}</td>
                        <td>
                            @if($attendance->status == 'present')
                                <span class="badge bg-success">Present</span>
                            @elseif($attendance->status == 'absent')
                                <span class="badge bg-danger">Absent</span>
                            @elseif($attendance->status == 'late')
                                <span class="badge bg-warning">Late</span>
                            @elseif($attendance->status == 'leave')
                                <span class="badge bg-info">Leave</span>
                            @elseif($attendance->status == 'holiday')
                                <span class="badge bg-secondary">Holiday</span>
                            @elseif($attendance->status == 'weekend')
                                <span class="badge bg-light text-dark">Weekend</span>
                            @elseif($attendance->status == 'half_day')
                                <span class="badge bg-warning">Half Day</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($attendance->status ?? 'N/A') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($attendance->late_minutes > 0)
                                <span class="text-warning">{{ $attendance->late_minutes }} min</span>
                            @else
                                <span class="text-success">-</span>
                            @endif
                        </td>
                        <td>{{ $attendance->working_hours ?? '-' }}</td>
                        <td>
                            @if($attendance->overtime_hours > 0 || $attendance->overtime_minutes > 0)
                                <span class="text-primary">
                                    {{ $attendance->overtime_hours ?? 0 }}h {{ $attendance->overtime_minutes ?? 0 }}m
                                </span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">No records found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($attendances) && method_exists($attendances, 'links'))
        {{ $attendances->links('pagination') }}
        @endif
    </div>

</div>

@endsection

@push('js')
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script>
    $('#attendanceTable').DataTable({
        "paging": false,
        "searching": false,
        "info": false
    });
</script>
@endpush
