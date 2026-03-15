@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Monthly Attendance Summary') }}</title>
@endsection

@push('css')
<style>
    .report-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .day-cell { 
        text-align: center; 
        font-size: 10px; 
        padding: 4px !important; 
        min-width: 35px;
        vertical-align: middle;
    }
    .day-header {
        font-size: 10px;
        padding: 4px !important;
        min-width: 35px;
        text-align: center;
    }
    .present { background: #d4edda !important; }
    .absent { background: #f8d7da !important; }
    .leave { background: #fff3cd !important; }
    .holiday { background: #cce5ff !important; }
    .status-p { color: #28a745; font-weight: bold; }
    .status-a { color: #dc3545; font-weight: bold; }
    .status-l { color: #ffc107; font-weight: bold; }
    .status-h { color: #17a2b8; font-weight: bold; }
    .status-dash { color: #6c757d; }
    .employee-cell {
        min-width: 150px;
    }
    .table-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
    }
    .table-avatar-placeholder {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #4ecdc4;
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 10px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .summary-stats {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }
    .stat-item {
        padding: 8px 15px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }
    .stat-present { background: #d4edda; color: #28a745; }
    .stat-absent { background: #f8d7da; color: #dc3545; }
    .stat-leave { background: #fff3cd; color: #ffc107; }
    .stat-holiday { background: #cce5ff; color: #17a2b8; }
    @media print {
        .no-print { display: none !important; }
        .report-card { box-shadow: none; }
        .day-cell, .day-header { font-size: 8px; padding: 2px !important; }
    }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Monthly Attendance Summary</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Attendance</li>
        <li class="item">Monthly Summary</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="report-card no-print">
        <form action="{{ route('admin.attendance.monthly.summary') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-2">
                <label>Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="form-control" required>
            </div>
            <div class="col-md-2">
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
            <div class="col-md-2">
                <label>Employee</label>
                <select name="employee_id" class="form-control">
                    <option value="">All Employees</option>
                    @foreach($allEmployees ?? [] as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->employee_id ?? $emp->id }} - {{ $emp->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Generate</button>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="button" onclick="window.print()" class="btn btn-success w-100">
                    <i class="bx bx-printer"></i> Print
                </button>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <a href="{{ route('admin.attendance.export', array_merge(request()->all(), ['format' => 'excel'])) }}" class="btn btn-info w-100" target="_blank">
                    <i class="bx bx-file"></i> Export Excel
                </a>
            </div>
        </form>

        <div class="summary-stats">
            <div class="stat-item stat-present">P = Present</div>
            <div class="stat-item stat-absent">A = Absent</div>
            <div class="stat-item stat-leave">L = Leave</div>
            <div class="stat-item stat-holiday">H = Holiday/Weekly Off</div>
            <div class="stat-item stat-absent">- = Absent (No Record)</div>
        </div>
    </div>

    <div class="report-card">
        <div class="d-flex justify-content-between mb-3">
            <h5>Attendance: {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}</h5>
            <span class="text-muted">Total Days: {{ count($dateRange) }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="attendanceTable">
                <thead>
                    <tr>
                        <th rowspan="2" class="employee-cell">Employee</th>
                        <th rowspan="2">Dept</th>
                        @foreach($dateRange as $date)
                        <th class="day-header" title="{{ $date->format('l') }}">
                            {{ $date->format('j') }}<br>
                            <small>{{ $date->format('M') }}</small>
                        </th>
                        @endforeach
                        <th rowspan="2" class="text-center">P</th>
                        <th rowspan="2" class="text-center">A</th>
                        <th rowspan="2" class="text-center">L</th>
                        <th rowspan="2" class="text-center">H</th>
                    </tr>
                    <tr>
                        @foreach($dateRange as $date)
                        <th class="day-header" style="font-size: 8px;">
                            {{ substr($date->format('l'), 0, 2) }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData ?? [] as $index => $data)
                    <tr>
                        <td>
                            @if($data['employee']->photo)
                                <img src="{{ asset($data['employee']->photo) }}" alt="{{ $data['employee']->name }}" class="table-avatar">
                            @else
                                <span class="table-avatar-placeholder">{{ substr($data['employee']->name, 0, 1) }}</span>
                            @endif
                            <strong>{{ $data['employee']->name }}</strong><br>
                            <small class="text-muted">{{ $data['employee']->employee_id ?? 'N/A' }}</small>
                        </td>
                        <td><small>{{ $data['employee']->department->name ?? 'N/A' }}</small></td>
                        @foreach($data['daily_data'] as $dayData)
                        <td class="day-cell {{ $dayData['status_class'] }}">
                            @if($dayData['status'] == 'P')
                                <span class="status-p">P</span>
                            @elseif($dayData['status'] == 'A')
                                <span class="status-a">A</span>
                            @elseif($dayData['status'] == 'L')
                                <span class="status-l">L</span>
                            @elseif($dayData['status'] == 'H')
                                <span class="status-h">H</span>
                            @else
                                <span class="status-dash">-</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="text-center"><strong class="text-success">{{ $data['present_count'] }}</strong></td>
                        <td class="text-center"><strong class="text-danger">{{ $data['absent_count'] }}</strong></td>
                        <td class="text-center"><strong class="text-warning">{{ $data['leave_count'] }}</strong></td>
                        <td class="text-center"><strong class="text-info">{{ $data['holiday_count'] }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($dateRange) + 7 }}" class="text-center text-muted py-4">
                            No attendance data available for selected date range
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
