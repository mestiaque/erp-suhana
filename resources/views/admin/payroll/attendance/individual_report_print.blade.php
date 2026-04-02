@extends('printMaster')
@section('title')
Individual Attendance Report - {{ $startDate->format('F Y') }}
@endsection

@push('css')
<style>
    body {
        font-size: 11px;
    }
    .print-header {
        text-align: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #333;
    }
    .print-header h2 {
        margin: 0;
        font-size: 16px;
    }
    .employee-info {
        margin-bottom: 15px;
    }
    .employee-info table {
        width: 100%;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    table th, table td {
        border: 1px solid #ddd;
        padding: 4px;
        text-align: center;
    }
    table th {
        background: #f0f0f0;
        font-weight: bold;
    }
    .day-cell {
        text-align: center;
        font-size: 9px;
        padding: 2px !important;
        min-width: 25px;
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
    .summary-stats {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 10px;
    }
    .summary-box {
        padding: 3px 10px;
        border-radius: 3px;
        font-size: 10px;
        font-weight: 600;
    }
    .summary-present { background: #d4edda; color: #28a745; }
    .summary-late { background: #ffeeba; color: #ffc107; }
    .summary-absent { background: #f8d7da; color: #dc3545; }
    .summary-leave { background: #fff3cd; color: #ffc107; }
    .summary-holiday { background: #cce5ff; color: #17a2b8; }
    .summary-total { background: #e2e3e5; color: #383d41; }
    .summary-incomplete { background: #0000002b; color: #3d3d3d; }
    @media print {
        body { font-size: 9px; }
        table { font-size: 8px; }
    }
</style>
@endpush

@section('contents')
@if($employee)

<div class="employee-info">
    <table>
        <tr>
            <td style="text-align: left;"><strong>Name:</strong> {{ $employee->name }}</td>
            <td style="text-align: left;"><strong>ID:</strong> {{ $employee->employee_id ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="text-align: left;"><strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }}</td>
            <td style="text-align: left;"><strong>Designation:</strong> {{ $employee->designation->name ?? 'N/A' }}</td>
        </tr>
    </table>

    @if($summary)
    <div class="summary-stats">
        <span class="summary-box summary-present">P: {{ $summary['present'] ?? 0 }}</span>
        <span class="summary-box summary-late">LT: {{ $summary['late'] ?? 0 }}</span>
        <span class="summary-box summary-absent">A: {{ $summary['absent'] ?? 0 }}</span>
        <span class="summary-box summary-leave">L: {{ $summary['leave'] ?? 0 }}</span>
        <span class="summary-box summary-holiday">H: {{ $summary['holiday'] ?? 0 }}</span>
    </div>
    @endif
</div>

@if($gridData && isset($gridData['dateRange']))
<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th rowspan="2">Employee</th>
            @foreach($gridData['dateRange'] as $date)
            <th class="day-cell" title="{{ $date->format('l') }}">
                {{ $date->format('j') }}
            </th>
            @endforeach
            <th rowspan="2">P</th>
            <th rowspan="2">A</th>
            <th rowspan="2">LT</th>
            <th rowspan="2">LV</th>
            <th rowspan="2">H</th>
            <th rowspan="2">I</th>
        </tr>
        <tr>
            @foreach($gridData['dateRange'] as $date)
            <th class="day-cell">{{ substr($date->format('l'), 0, 1) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: left; min-width: 80px;">
                <strong>{{ $employee->name }}</strong>
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
                    -
                @endif
            </td>
            @endforeach
            <td><strong class="text-success">{{ $gridData['present_count'] }}</strong></td>
            <td><strong class="text-danger">{{ $gridData['absent_count'] }}</strong></td>
            <td><strong class="text-warning">{{ $gridData['late_count'] }}</strong></td>
            <td><strong style="color: #6f42c1">{{ $gridData['leave_count'] }}</strong></td>
            <td><strong class="text-info">{{ $gridData['holiday_count'] }}</strong></td>
            <td><strong class="text-dark">{{ $gridData['incomplete_count'] }}</strong></td>
        </tr>
    </tbody>
</table>
@endif

{{-- Detailed Attendance Table --}}
<div style="margin-top: 20px;">
    <h4 style="font-size: 12px; margin-bottom: 10px;">Detailed Attendance</h4>
    <table class="table table-bordered table-sm">
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
                <td class="day-cell">{{ $data['in_time'] ?? '-' }}</td>
                <td class="day-cell">{{ $data['out_time'] ?? '-' }}</td>
                <td class="day-cell">{{ is_numeric($data['working_hours'] ?? null) ? number_format((float) $data['working_hours'], 2) : '-' }}</td>
                <td class="day-cell">
                    @if(isset($data['late_minutes']) && $data['late_minutes'] > 0)
                        <span class="text-warning">{{ $data['late_minutes'] }}</span>
                    @else
                        -
                    @endif
                </td>
                <td class="day-cell">
                    @if(isset($data['overtime']) && $data['overtime'])
                        <span class="text-primary">{{ number_format((float) $data['overtime'], 2) }}</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 10px;">No data available</td>
            </tr>
            @endforelse
        </tbody>
        @if(isset($dailyData) && count($dailyData) > 0)
        <tfoot>
            <tr style="background: #f0f0f0; font-weight: bold;">
                <td colspan="5" style="text-align: right;">Totals:</td>
                <td>{{ isset($totalWorkingHours) ? number_format((float) $totalWorkingHours, 2) : '-' }}</td>
                <td>{{ $totalLateMinutes ?? '-' }}</td>
                <td>{{ isset($totalOvertime) ? number_format((float) $totalOvertime, 2) : '-' }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

{{-- Monthly Summary Statistics --}}
<div style="margin-top: 20px;">
    <h4 style="font-size: 12px; margin-bottom: 10px;">Monthly Summary</h4>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table class="table table-bordered table-sm" style="width: 100%;">
                    <tr>
                        <th style="text-align: left; padding: 4px;">Total Days</th>
                        <td style="padding: 4px;">{{ $summary['total'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding: 4px;">Present Days</th>
                        <td style="padding: 4px; color: #28a745;">{{ $summary['present'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding: 4px;">Absent Days</th>
                        <td style="padding: 4px; color: #dc3545;">{{ $summary['absent'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding: 4px;">Leave Days</th>
                        <td style="padding: 4px; color: #ffc107;">{{ $summary['leave'] ?? 0 }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <table class="table table-bordered table-sm" style="width: 100%;">
                    <tr>
                        <th style="text-align: left; padding: 4px;">Late Days</th>
                        <td style="padding: 4px; color: #ffc107;">{{ $summary['late'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding: 4px;">Total Late Minutes</th>
                        <td style="padding: 4px; color: #ffc107;">{{ $totalLateMinutes ?? 0 }} min</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding: 4px;">Holiday/Weekly Off</th>
                        <td style="padding: 4px; color: #17a2b8;">{{ $summary['holiday'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding: 4px;">Total Working Hours</th>
                        <td style="padding: 4px; color: #007bff;">{{ isset($totalWorkingHours) ? number_format((float) $totalWorkingHours, 2) : 0 }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding: 4px;">Total Overtime</th>
                        <td style="padding: 4px; color: #007bff;">{{ isset($totalOvertime) ? number_format((float) $totalOvertime, 2) : 0 }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding: 4px;">Attendance Rate</th>
                        <td style="padding: 4px;">
                            @php
                                $attendanceRate = isset($summary['total']) && $summary['total'] > 0
                                    ? round((($summary['present'] ?? 0) + ($summary['leave'] ?? 0)) / $summary['total'] * 100, 1)
                                    : 0;
                            @endphp
                            <span style="color: {{ $attendanceRate >= 80 ? '#28a745' : '#dc3545' }};">
                                {{ $attendanceRate }}%
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div style="margin-top: 15px; text-align: center; font-size: 10px; color: #666;">
    <p>Printed on: {{ date('d M Y, h:i A') }}</p>
</div>
@else
<p style="text-align: center;">No employee selected</p>
@endif
@endsection
