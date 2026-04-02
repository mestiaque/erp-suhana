@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Individual Attendance Report') }}</title>
@endsection

@push('css')
<style>
    .report-card {
        background: #fff;
        padding: 14px;
        border-radius: 10px;
        border: 1px solid #e9edf3;
        box-shadow: 0 2px 10px rgba(20, 38, 62, 0.04);
        margin-bottom: 14px;
    }
    .day-cell {
        text-align: center;
        font-size: 11px;
        padding: 3px !important;
        min-width: 35px;
    }
    .day-header {
        font-size: 10px;
        padding: 3px !important;
        min-width: 35px;
        text-align: center;
    }
    .present { background: #d4edda !important; }
    .absent { background: #f8d7da !important; }
    .leave { background: #fff3cd !important; }
    .late { background: #ffeeba !important; }
    .holiday { background: #cce5ff !important; }
    .offday { background: #ffffff !important; }
    .incomplete { background: #0000002b !important; }
    .status-p { color: #28a745; font-weight: bold; }
    .status-a { color: #dc3545; font-weight: bold; }
    .status-l { color: #ffc107; font-weight: bold; }
    .status-lt { color: #fd7e14; font-weight: bold; }
    .status-h { color: #17a2b8; font-weight: bold; }
    .status-wo { color: #6c757d; font-weight: bold; }
    .status-i { color: #3d3d3d; font-weight: bold; }
    .status-dash { color: #0000008e; }
    .time-cell {
        font-size: 11px;
        color: #495057;
        white-space: nowrap;
    }
    .employee-header {
        background: linear-gradient(120deg, #7bb7eb 0%, #707070 100%);
        color: white;
        padding: 16px;
        border-radius: 10px;
        margin-bottom: 14px;
    }
    .employee-photo {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
    }
    .employee-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
        border: 3px solid white;
    }
    .summary-box {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 999px;
        margin: 2px 2px 0 0;
        font-size: 11px;
        font-weight: 600;
    }
    .summary-present { background: #d4edda; color: #28a745; }
    .summary-late { background: #ffeeba; color: #ffc107; }
    .summary-absent { background: #f8d7da; color: #dc3545; }
    .summary-leave { background: #fff3cd; color: #ffc107; }
    .summary-holiday { background: #cce5ff; color: #17a2b8; }
    .summary-total { background: #e2e3e5; color: #383d41; }
    .summary-incomplete { background: #0000002b; color: #3d3d3d; }

    /* Grid View Styles */
    .grid-view .day-cell {
        font-size: 10px;
        min-width: 30px;
    }
    .grid-view .day-header {
        font-size: 9px;
        min-width: 30px;
    }
    .stat-present { background: #d4edda; color: #28a745; }
    .stat-absent { background: #f8d7da; color: #dc3545; }
    .stat-leave { background: #f5cdff; color: #6f42c1; }
    .stat-late { background: #fff3cd; color: #ffc107; }
    .stat-holiday { background: #cce5ff; color: #17a2b8; }
    .stat-incomplete { background: #0000002b; color: #3d3d3d; }
    .stat-weekly-off { background: #e2e6ea; color: #6c757d; }

    .report-filter label {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .report-title {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 0;
    }
    .compact-table th,
    .compact-table td {
        padding: 0.35rem 0.45rem !important;
        vertical-align: middle;
    }
    .compact-table thead th {
        background: #f7f9fc;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #556070;
    }

    @media print {
        .no-print { display: none !important; }
        .report-card { box-shadow: none; }
    }
</style>
@endpush

@section('contents')


@include(adminTheme().'alerts')

<div class="flex-grow-1">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between mb-1">
            <h3 class="card-title"> Individual Attendance Report</h3>
            <a href="{{ route('admin.attendance.individual.report.print', ['employee_id' => $employee_id, 'month' => $month]) }}" target="_blank" class="btn btn-success btn-sm no-print">
                <i class="bx bx-printer"></i> Print
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.attendance.individual.report') }}" method="GET" class="row g-2 report-filter mb-1 align-items-end mb-2">
                <div class="col-md-4">
                    <label>Employee</label>
                    <select name="employee_id" class="form-control form-control-sm" required>
                        <option value="">Select Employee</option>
                        @foreach($employees ?? [] as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->employee_id ?? $emp->id }} - {{ $emp->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Month</label>
                    <input type="month" name="month" value="{{ $month ?? date('Y-m') }}" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-search"></i> Load </button>
                    <a href="{{ route('admin.attendance.individual.report') }}" class="btn btn-success btn-sm">
                        Reset
                    </a>
                </div>
            </form>

            @if($employee)
                <div class="employee-header">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            @if($employee->photo)
                                <img src="{{ asset($employee->photo) }}" alt="Photo" class="employee-photo">
                            @else
                                <div class="employee-avatar">{{ substr($employee->name, 0, 1) }}</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h3>{{ $employee->name }}</h3>
                            <p class="mb-1">
                                <strong>ID:</strong> {{ $employee->employee_id ?? 'N/A' }} |
                                <strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }} |
                                <strong>Designation:</strong> {{ $employee->designation->name ?? 'N/A' }}
                            </p>
                            <p class="mb-0">
                                <strong>Month:</strong> {{ isset($startDate) ? $startDate->format('F Y') : date('F Y') }}
                            </p>
                        </div>
                        <div class="col-md-4 text-md-right">
                            @if($summary)
                            <div class="mt-2">
                                <span class="summary-box summary-present">P: {{ $summary['present'] ?? 0 }}</span>
                                <span class="summary-box summary-late">LT: {{ $summary['late'] ?? 0 }}</span>
                                <span class="summary-box summary-absent">A: {{ $summary['absent'] ?? 0 }}</span>
                                <span class="summary-box summary-leave">L: {{ $summary['leave'] ?? 0 }}</span>
                                <span class="summary-box summary-holiday">H: {{ $summary['holiday'] ?? 0 }}</span>
                                <span class="summary-box summary-total">Total: {{ $summary['total'] ?? 0 }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Grid View Section (Similar to Monthly Summary) --}}
                <div class="report-card">
                    <div class="d-flex justify-content-between mb-2 align-items-center">
                        <h6 class="report-title"><i class="bx bx-grid"></i> Monthly Overview: {{ isset($startDate) ? $startDate->format('F Y') : date('F Y') }}</h6>
                        <div class="summary-stats">
                            <span class="summary-box stat-present">P = Present</span>
                            <span class="summary-box stat-absent">A = Absent</span>
                            <span class="summary-box stat-late">LT = Late</span>
                            <span class="summary-box stat-leave">L = Leave</span>
                            <span class="summary-box stat-holiday">H = Holiday/WO</span>
                            <span class="summary-box stat-incomplete">I = Incomplete</span>
                        </div>
                    </div>

                    @if($employee && $gridData && isset($gridData['dateRange']))
                    <div class="table-responsive grid-view">
                        <table class="table table-bordered table-sm compact-table">
                            <thead>
                                <tr>
                                    <th rowspan="2">Employee</th>
                                    @foreach($gridData['dateRange'] as $date)
                                    <th class="day-header" title="{{ $date->format('l') }}">
                                        {{ $date->format('j') }}<br>
                                        <small>{{ $date->format('M') }}</small>
                                    </th>
                                    @endforeach
                                    <th rowspan="2" class="text-center">P</th>
                                    <th rowspan="2" class="text-center">A</th>
                                    <th rowspan="2" class="text-center">LT</th>
                                    <th rowspan="2" class="text-center">LV</th>
                                    <th rowspan="2" class="text-center">H</th>
                                    <th rowspan="2" class="text-center">I</th>
                                </tr>
                                <tr>
                                    @foreach($gridData['dateRange'] as $date)
                                    <th class="day-header" style="font-size: 8px;">
                                        {{ substr($date->format('l'), 0, 2) }}
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>{{ $employee->name }}</strong><br>
                                        <small class="text-muted">{{ $employee->employee_id ?? 'N/A' }}</small>
                                    </td>
                                    @foreach($gridData['daily_data'] as $dayData)
                                    <td class="day-cell {{ $dayData['status_class'] }}">
                                        @if($dayData['status'] == 'P')
                                            <span class="status-p">P</span>
                                        @elseif($dayData['status'] == 'A')
                                            <span class="status-a">A</span>
                                        @elseif($dayData['status'] == 'L' && $dayData['status_class'] == 'late')
                                            <span class="status-lt">L</span>
                                        @elseif($dayData['status'] == 'L' && $dayData['status_class'] == 'leave')
                                            <span class="status-l">L</span>
                                        @elseif($dayData['status'] == 'H')
                                            <span class="status-h">H</span>
                                        @elseif($dayData['status'] == 'I')
                                            <span class="status-i">I</span>
                                        @else
                                            <span class="status-dash">-</span>
                                        @endif
                                    </td>
                                    @endforeach
                                    <td class="text-center"><strong class="text-success">{{ $gridData['present_count'] }}</strong></td>
                                    <td class="text-center"><strong class="text-danger">{{ $gridData['absent_count'] }}</strong></td>
                                    <td class="text-center"><strong class="text-warning">{{ $gridData['late_count'] }}</strong></td>
                                    <td class="text-center"><strong style="color: #6f42c1">{{ $gridData['leave_count'] }}</strong></td>
                                    <td class="text-center"><strong class="text-info">{{ $gridData['holiday_count'] }}</strong></td>
                                    <td class="text-center"><strong class="text-dark">{{ $gridData['incomplete_count'] }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- Detailed View Section --}}
                <div class="report-card">
                    <div class="d-flex justify-content-between mb-2 align-items-center">
                        <h6 class="report-title"><i class="bx bx-list-ul"></i> Detailed Attendance</h6>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm compact-table" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Status</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>Working Hours</th>
                                    <th>Late (min)</th>
                                    <th>Overtime</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dailyData ?? [] as $data)
                                <tr class="{{ $data['status_class'] }}">
                                    <td class="day-cell">{{ $data['day'] }}</td>
                                    <td class="day-cell">{{ $data['day_name'] }}</td>
                                    <td class="day-cell">
                                        @if($data['status'] == 'P')
                                            <span class="status-p">P</span>
                                        @elseif($data['status'] == 'LT')
                                            <span class="status-lt">LT</span>
                                        @elseif($data['status'] == 'A')
                                            <span class="status-a">A</span>
                                        @elseif($data['status'] == 'L')
                                            <span class="status-l">L</span>
                                        @elseif($data['status'] == 'H')
                                            <span class="status-h">H</span>
                                        @elseif($data['status'] == 'WO')
                                            <span class="status-wo">WO</span>
                                        @elseif($data['status'] == 'I')
                                            <span class="status-i">I</span>
                                        @else
                                            {{ $data['status'] }}
                                        @endif
                                    </td>
                                    <td class="time-cell">{{ $data['in_time'] ?? '-' }}</td>
                                    <td class="time-cell">{{ $data['out_time'] ?? '-' }}</td>
                                    <td class="time-cell">{{ is_numeric($data['working_hours'] ?? null) ? number_format((float) $data['working_hours'], 2) : '-' }}</td>
                                    <td class="time-cell">
                                        @if(isset($data['late_minutes']) && $data['late_minutes'] > 0)
                                            <span class="text-warning">{{ $data['late_minutes'] }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="time-cell">
                                        @if(isset($data['overtime']) && $data['overtime'])
                                            <span class="text-primary">{{ number_format((float) $data['overtime'], 2) }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No data available
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(isset($dailyData) && count($dailyData) > 0)
                            <tfoot>
                                <tr style="background: #f8f9fa; font-weight: bold;">
                                    <td colspan="5" class="text-end">Totals:</td>
                                    <td>{{ isset($totalWorkingHours) ? number_format((float) $totalWorkingHours, 2) : '-' }}</td>
                                    <td>{{ $totalLateMinutes ?? '-' }}</td>
                                    <td>{{ isset($totalOvertime) ? number_format((float) $totalOvertime, 2) : '-' }}</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Summary Statistics --}}
                <div class="report-card">
                    <h5 class="report-title mb-2"><i class="bx bx-chart"></i> Monthly Summary</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-bordered compact-table">
                                <tr>
                                    <th>Total Days</th>
                                    <td>{{ $summary['total'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Present Days</th>
                                    <td class="text-success">{{ $summary['present'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Absent Days</th>
                                    <td class="text-danger">{{ $summary['absent'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Leave Days</th>
                                    <td class="text-warning">{{ $summary['leave'] ?? 0 }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-bordered compact-table">
                                <tr>
                                    <th>Late Days</th>
                                    <td class="text-warning">{{ $summary['late'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Total Late Minutes</th>
                                    <td class="text-warning">{{ $totalLateMinutes ?? 0 }} min</td>
                                </tr>
                                <tr>
                                    <th>Holiday/Weekly Off</th>
                                    <td class="text-info">{{ $summary['holiday'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Total Working Hours</th>
                                    <td class="text-primary">{{ isset($totalWorkingHours) ? number_format((float) $totalWorkingHours, 2) : 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Total Overtime</th>
                                    <td class="text-primary">{{ isset($totalOvertime) ? number_format((float) $totalOvertime, 2) : 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Attendance Rate</th>
                                    <td>
                                        @php
                                            $attendanceRate = isset($summary['total']) && $summary['total'] > 0
                                                ? round((($summary['present'] ?? 0) + ($summary['leave'] ?? 0)) / $summary['total'] * 100, 1)
                                                : 0;
                                        @endphp
                                        <span class="{{ $attendanceRate >= 80 ? 'text-success' : 'text-danger' }}">
                                            {{ $attendanceRate }}%
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

            @else
                <div class="report-card text-center py-5">
                    <i class="bx bx-user" style="font-size: 48px; color: #ccc;"></i>
                    <p class="mt-3 text-muted">Please select an employee to view attendance report</p>
                </div>
            @endif
        </div>
    </div>




</div>

@endsection
