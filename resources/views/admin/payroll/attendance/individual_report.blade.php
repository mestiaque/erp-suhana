@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Individual Attendance Report') }}</title>
@endsection

@push('css')
<style>
    .report-card {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .day-cell {
        text-align: center;
        font-size: 11px;
        padding: 4px !important;
        min-width: 35px;
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
    .late { background: #ffeeba !important; }
    .holiday { background: #cce5ff !important; }
    .status-p { color: #28a745; font-weight: bold; }
    .status-a { color: #dc3545; font-weight: bold; }
    .status-l { color: #ffc107; font-weight: bold; }
    .status-lt { color: #fd7e14; font-weight: bold; }
    .status-h { color: #17a2b8; font-weight: bold; }
    .status-wo { color: #6c757d; font-weight: bold; }
    .time-cell {
        font-size: 10px;
        color: #666;
    }
    .employee-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
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
        padding: 8px 15px;
        border-radius: 5px;
        margin: 2px;
        font-size: 12px;
        font-weight: 600;
    }
    .summary-present { background: #d4edda; color: #28a745; }
    .summary-late { background: #ffeeba; color: #ffc107; }
    .summary-absent { background: #f8d7da; color: #dc3545; }
    .summary-leave { background: #fff3cd; color: #ffc107; }
    .summary-holiday { background: #cce5ff; color: #17a2b8; }
    .summary-total { background: #e2e3e5; color: #383d41; }
    @media print {
        .no-print { display: none !important; }
        .report-card { box-shadow: none; }
    }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Individual Attendance Report</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Attendance</li>
        <li class="item">Individual Report</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="report-card no-print">
        <form action="{{ route('admin.attendance.individual.report') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <label>Employee</label>
                <select name="employee_id" class="form-control" required>
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
                <input type="month" name="month" value="{{ $month ?? date('Y-m') }}" class="form-control" required>
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
        </form>
    </div>

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
                    <strong>Month:</strong> {{ $startDate->format('F Y') }}
                </p>
            </div>
            <div class="col-md-4 text-md-right">
                @if($summary)
                <div class="mt-2">
                    <span class="summary-box summary-present">P: {{ $summary['present'] }}</span>
                    <span class="summary-box summary-late">LT: {{ $summary['late'] }}</span>
                    <span class="summary-box summary-absent">A: {{ $summary['absent'] }}</span>
                    <span class="summary-box summary-leave">L: {{ $summary['leave'] }}</span>
                    <span class="summary-box summary-holiday">H: {{ $summary['holiday'] }}</span>
                    <span class="summary-box summary-total">Total: {{ $summary['total'] }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="report-card">
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="attendanceTable">
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
                            @else
                                {{ $data['status'] }}
                            @endif
                        </td>
                        <td class="time-cell">{{ $data['in_time'] }}</td>
                        <td class="time-cell">{{ $data['out_time'] }}</td>
                        <td class="time-cell">{{ $data['working_hours'] ?? '-' }}</td>
                        <td class="time-cell">
                            @if(isset($data['late_minutes']) && $data['late_minutes'] > 0)
                                <span class="text-warning">{{ $data['late_minutes'] }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="time-cell">
                            @if(isset($data['overtime']) && $data['overtime'])
                                <span class="text-primary">{{ $data['overtime'] }}</span>
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
                        <td>{{ $totalWorkingHours ?? '-' }}</td>
                        <td>{{ $totalLateMinutes ?? '-' }}</td>
                        <td>{{ $totalOvertime ?? '-' }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="report-card no-print">
        <h5>Legend:</h5>
        <div>
            <span class="summary-box summary-present">P = Present</span>
            <span class="summary-box summary-late">LT = Late</span>
            <span class="summary-box summary-absent">A = Absent</span>
            <span class="summary-box summary-leave">L = Leave</span>
            <span class="summary-box summary-holiday">H = Holiday</span>
            <span class="summary-box summary-holiday">WO = Weekly Off</span>
            <span class="summary-box" style="background: #cce5ff; color: #004085;">OT = Overtime</span>
        </div>
    </div>

    {{-- Summary Statistics --}}
    <div class="report-card">
        <h5>Monthly Summary</h5>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-bordered">
                    <tr>
                        <th>Total Working Days</th>
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
                <table class="table table-sm table-bordered">
                    <tr>
                        <th>Late Days</th>
                        <td class="text-warning">{{ $summary['late'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th>Total Late Minutes</th>
                        <td class="text-warning">{{ $totalLateMinutes ?? 0 }} min</td>
                    </tr>
                    <tr>
                        <th>Overtime Days</th>
                        <td class="text-primary">{{ $summary['overtime'] ?? 0 }}</td>
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

@endsection
