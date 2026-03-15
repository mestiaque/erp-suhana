@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Monthly Attendance Report') }}</title>
@endsection

@push('css')
<style>
    .report-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    table.table thead { background: #4ecdc4; color: white; }
    .day-cell { text-align: center; font-size: 11px; padding: 5px !important; min-width: 28px; }
    .present { background: #d4edda; }
    .absent { background: #f8d7da; }
    .leave { background: #fff3cd; }
    .late { background: #ffeeba; }
    .holiday { background: #e2e8f0; }
    .weekend { background: #f1f5f9; }
    .overtime { background: #cce5ff; }
    .legend { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 15px; }
    .legend-item { display: flex; align-items: center; gap: 5px; font-size: 12px; }
    .legend-box { width: 20px; height: 20px; border: 1px solid #ddd; border-radius: 3px; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Monthly Attendance Report</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Attendance</li>
        <li class="item">Monthly Report</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="report-card">
        <form action="{{ route('admin.attendance.monthly.report') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Month</label>
                <input type="month" name="month" value="{{ request('month', date('Y-m')) }}" class="form-control">
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
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Generate</button>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="button" onclick="window.print()" class="btn btn-success w-100">
                    <i class="bx bx-printer"></i> Print
                </button>
            </div>
        </form>

        {{-- Legend --}}
        <div class="legend">
            <div class="legend-item"><div class="legend-box present"></div> P = Present</div>
            <div class="legend-item"><div class="legend-box absent"></div> A = Absent</div>
            <div class="legend-item"><div class="legend-box leave"></div> L = Leave</div>
            <div class="legend-item"><div class="legend-box late"></div> Lt = Late</div>
            <div class="legend-item"><div class="legend-box holiday"></div> H = Holiday</div>
            <div class="legend-item"><div class="legend-box weekend"></div> W = Weekend</div>
            <div class="legend-item"><div class="legend-box overtime"></div> OT = Overtime</div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th rowspan="2">SL</th>
                        <th rowspan="2">Employee</th>
                        <th rowspan="2">Dept</th>
                        @for($day = 1; $day <= $daysInMonth ?? 31; $day++)
                        <th class="day-cell">{{ $day }}</th>
                        @endfor
                        <th rowspan="2" class="text-success">P</th>
                        <th rowspan="2" class="text-danger">A</th>
                        <th rowspan="2" class="text-warning">L</th>
                        <th rowspan="2" class="text-info">Lt</th>
                        <th rowspan="2" class="text-secondary">H</th>
                        <th rowspan="2" class="text-muted">W</th>
                        <th rowspan="2" class="text-primary">OT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthlyData ?? [] as $i => $employee)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($employee->photo)
                                    <img src="{{ asset('uploads/user_photo/' . $employee->photo) }}" alt="{{ $employee->name }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover; margin-right: 8px;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 35px; height: 35px; background-color: {{ random_color($employee->id ?? 0) }}; margin-right: 8px; flex-shrink: 0;">
                                        {{ strtoupper(substr($employee->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <small class="font-weight-bold">{{ $employee->name }}</small><br>
                                    <small class="text-muted">{{ $employee->employee_id }}</small>
                                </div>
                            </div>
                        </td>
                        <td><small>{{ $employee->department_name ?? 'N/A' }}</small></td>
                        @for($day = 1; $day <= $daysInMonth ?? 31; $day++)
                        @php
                            $status = $employee->attendance[$day] ?? null;
                            $cellClass = '';
                            $cellText = '-';

                            if($status == 'present') { $cellClass = 'present'; $cellText = 'P'; }
                            elseif($status == 'absent') { $cellClass = 'absent'; $cellText = 'A'; }
                            elseif($status == 'leave') { $cellClass = 'leave'; $cellText = 'L'; }
                            elseif($status == 'late') { $cellClass = 'late'; $cellText = 'Lt'; }
                            elseif($status == 'holiday') { $cellClass = 'holiday'; $cellText = 'H'; }
                            elseif($status == 'weekend') { $cellClass = 'weekend'; $cellText = 'W'; }
                            elseif($status == 'overtime' || $status == 'present_ot') { $cellClass = 'overtime'; $cellText = 'OT'; }
                            elseif($status == 'present_late') { $cellClass = 'late'; $cellText = 'P/L'; }
                            elseif($status == 'half_day') { $cellClass = 'leave'; $cellText = 'HD'; }

                            // Handle array status (multiple flags)
                            if(is_array($status)) {
                                if(in_array('present', $status)) { $cellClass = 'present'; $cellText = 'P'; }
                                if(in_array('late', $status)) { $cellClass = 'late'; $cellText = isset($cellText) && $cellText == 'P' ? 'P/L' : 'Lt'; }
                                if(in_array('overtime', $status)) { $cellText .= '+'; }
                            }
                        @endphp
                        <td class="day-cell {{ $cellClass }}">
                            @if($status)
                                @if($cellClass == 'present')
                                    <span class="text-success">{{ $cellText }}</span>
                                @elseif($cellClass == 'absent')
                                    <span class="text-danger">{{ $cellText }}</span>
                                @elseif($cellClass == 'leave')
                                    <span class="text-warning">{{ $cellText }}</span>
                                @elseif($cellClass == 'late')
                                    <span class="text-info">{{ $cellText }}</span>
                                @elseif($cellClass == 'holiday')
                                    <span class="text-secondary">{{ $cellText }}</span>
                                @elseif($cellClass == 'weekend')
                                    <span class="text-muted">{{ $cellText }}</span>
                                @elseif($cellClass == 'overtime')
                                    <span class="text-primary">{{ $cellText }}</span>
                                @else
                                    {{ $cellText }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        @endfor
                        <td class="text-success"><strong>{{ $employee->present_count ?? 0 }}</strong></td>
                        <td class="text-danger"><strong>{{ $employee->absent_count ?? 0 }}</strong></td>
                        <td class="text-warning"><strong>{{ $employee->leave_count ?? 0 }}</strong></td>
                        <td class="text-info"><strong>{{ $employee->late_count ?? 0 }}</strong></td>
                        <td class="text-secondary"><strong>{{ $employee->holiday_count ?? 0 }}</strong></td>
                        <td class="text-muted"><strong>{{ $employee->weekend_count ?? 0 }}</strong></td>
                        <td class="text-primary"><strong>{{ $employee->overtime_count ?? 0 }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ ($daysInMonth ?? 31) + 10 }}" class="text-center text-muted py-4">
                            No attendance data available for selected month
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Summary Section --}}
        @if(isset($monthlyData) && count($monthlyData) > 0)
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <strong>Attendance Summary</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td>Total Employees</td>
                                <td class="text-end"><strong>{{ count($monthlyData) }}</strong></td>
                            </tr>
                            <tr>
                                <td>Total Present Days</td>
                                <td class="text-end text-success"><strong>{{ collect($monthlyData)->sum('present_count') }}</strong></td>
                            </tr>
                            <tr>
                                <td>Total Absent Days</td>
                                <td class="text-end text-danger"><strong>{{ collect($monthlyData)->sum('absent_count') }}</strong></td>
                            </tr>
                            <tr>
                                <td>Total Leave Days</td>
                                <td class="text-end text-warning"><strong>{{ collect($monthlyData)->sum('leave_count') }}</strong></td>
                            </tr>
                            <tr>
                                <td>Total Late Days</td>
                                <td class="text-end text-info"><strong>{{ collect($monthlyData)->sum('late_count') }}</strong></td>
                            </tr>
                            <tr>
                                <td>Total Overtime Days</td>
                                <td class="text-end text-primary"><strong>{{ collect($monthlyData)->sum('overtime_count') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

</div>

@endsection
