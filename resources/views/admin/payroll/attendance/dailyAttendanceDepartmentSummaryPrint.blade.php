@extends('printMaster')
@section('title') Department Attendance Summary Print @endsection

@push('css')
<style>
    body {
        font-size: 12px;
    }
    .print-header {
        text-align: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #333;
    }
    .print-header h2 {
        margin: 0;
        font-size: 18px;
    }
    .print-header p {
        margin: 5px 0 0;
        font-size: 12px;
        color: #666;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }
    table th, table td {
        border: 1px solid #ddd;
        padding: 6px;
        text-align: center;
    }
    table th {
        background: #f0f0f0;
        font-weight: bold;
    }
    .badge-summary{
        padding:3px 8px;
        border-radius:3px;
        color:#fff;
        font-size: 10px;
    }
    .badge-present{background:#28a745;}
    .badge-late{background:#ffc107; color: #000 !important;}
    .badge-absent{background:#dc3545;}
    .badge-leave{background:#6f42c1;}
    .badge-holiday{background:#17a2b8;}
    .badge-total{background:#6c757d;}
    .text-success { color: #28a745; font-weight: bold; }
    .text-warning { color: #ffc107; font-weight: bold; }
    .text-danger { color: #dc3545; font-weight: bold; }
    @media print {
        .no-print { display: none !important; }
        body { font-size: 10px; }
        table { font-size: 9px; }
    }
</style>
@endpush

@section('contents')
    <div class="print-header">
        <h2>Department Attendance Summary</h2>
        <p>{{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Department</th>
                <th>Total Employees</th>
                <th>Total Days</th>
                <th>Present</th>
                <th>Late</th>
                <th>Absent</th>
                <th>Leave</th>
                <th>Holiday/WO</th>
                <th>Attendance Rate</th>
            </tr>
        </thead>
        <tbody>
            @php
                $departmentTotals = [];
                $totalDays = $startDate->diffInDays($endDate) + 1;
                
                foreach($dateWiseSummary as $day) {
                    foreach($day['departments'] as $summary) {
                        $deptName = $summary['department_name'];
                        if (!isset($departmentTotals[$deptName])) {
                            $departmentTotals[$deptName] = [
                                'total' => 0,
                                'present' => 0,
                                'late' => 0,
                                'absent' => 0,
                                'leave' => 0,
                                'holiday' => 0,
                            ];
                        }
                        $departmentTotals[$deptName]['total'] += $summary['total'];
                        $departmentTotals[$deptName]['present'] += $summary['present'];
                        $departmentTotals[$deptName]['late'] += $summary['late'];
                        $departmentTotals[$deptName]['absent'] += $summary['absent'];
                        $departmentTotals[$deptName]['leave'] += $summary['leave'] ?? 0;
                        $departmentTotals[$deptName]['holiday'] += $summary['holiday'] ?? 0;
                    }
                }
            @endphp

            @forelse($departmentTotals as $deptName => $totals)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="text-align: left;"><strong>{{ $deptName }}</strong></td>
                    <td>{{ $totals['total'] / $totalDays }}</td>
                    <td>{{ $totalDays }}</td>
                    <td>{{ $totals['present'] }}</td>
                    <td>{{ $totals['late'] }}</td>
                    <td>{{ $totals['absent'] }}</td>
                    <td>{{ $totals['leave'] }}</td>
                    <td>{{ $totals['holiday'] }}</td>
                    <td>
                        @php
                            $totalPresent = $totals['present'] + $totals['late'];
                            $totalPossible = $totals['total'];
                            $presentPercent = $totalPossible > 0 
                                ? round(($totalPresent / $totalPossible) * 100, 1) 
                                : 0;
                            $percentClass = $presentPercent >= 80 ? 'text-success' : ($presentPercent >= 60 ? 'text-warning' : 'text-danger');
                        @endphp
                        <span class="{{ $percentClass }}">{{ $presentPercent }}%</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">No data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: center; font-size: 10px; color: #666;">
        <p>Printed on: {{ date('d M Y, h:i A') }}</p>
    </div>
@endsection
