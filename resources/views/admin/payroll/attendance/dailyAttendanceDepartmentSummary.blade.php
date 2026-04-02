@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Department Attendance Summary') }}</title>
@endsection

@push('css')
<style>
    .attendance-filters{
        padding:15px;
        border-radius:8px;
        box-shadow:0 2px 6px rgba(0,0,0,0.05);
        margin-bottom:20px;
        background: #fff;
    }
    .summary-card {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .badge-summary{
        padding:5px 10px;
        border-radius:5px;
        color:#fff;
        font-weight: 600;
    }
    .badge-present{background:#28a745;}
    .badge-late{background:#ffc107; color: #000 !important;}
    .badge-absent{background:#dc3545;}
    .badge-leave{background:#6f42c1;}
    .badge-holiday{background:#17a2b8;}
    .badge-total{background:#6c757d;}
    .dataTables_filter{display:none;}
    table.table thead{background : #56d2ff;}
    .print-btn {
        background: #28a745;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
    }
    @media print {
        .no-print { display: none !important; }
        .summary-card { box-shadow: none; }
    }
</style>
@endpush

@section('contents')


@include(adminTheme().'alerts')

<div class="flex-grow-1">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center mb-1">
            <h3 class="mb-0">Department Attendance Summary</h3>
            <div>
                <a href="{{ route('admin.dailyAttendanceDepartmentSummaryPrint', request()->all()) }}" target="_blank" class="btn btn-primary btn-sm"><i class="bx bx-printer"></i> Print</a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.dailyAttendanceDepartmentSummary') }}" class="row g-3 mb-2 align-items-end">
    
                <div class="col-md-2">
                    <label>Start Date</label>
                    <input type="date" name="startDate" value="{{ request()->startDate ?? date('Y-m-d') }}" class="form-control form-control-sm">
                </div>
    
                <div class="col-md-2">
                    <label>End Date</label>
                    <input type="date" name="endDate" value="{{ request()->endDate ?? date('Y-m-d') }}" class="form-control form-control-sm">
                </div>
    
                <div class="col-md-2">
                    <label>Department</label>
                    <select name="department" class="form-control form-control-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" @if(request()->department==$dept->id) selected @endif>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
    
                <div class="col-md-3 text-end">
                    <button class="btn btn-success btn-sm" type="submit"><i class="bi bi-search"></i> Search</button>
                    <a href="{{ route('admin.dailyAttendanceDepartmentSummary') }}" class="btn btn-warning btn-sm"><i class="bx bx-rotate-left"></i> Reset</a>
                    
                </div>
    
            </form>
            <h6>
                <i class="bx bx-chart"></i> Attendance Summary: {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered table-hover dataex-html5-export w-100">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Department</th>
                            <th class="text-center">Total Employees</th>
                            <th class="text-center">Total Days</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Late</th>
                            <th class="text-center">Absent</th>
                            <th class="text-center">Leave</th>
                            <th class="text-center">Holiday/WO</th>
                            <th class="text-center">Attendance Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $departmentTotals = [];
                            $totalDays = 0;
    
                            // Calculate date range days
                            $dateDiff = $startDate->diffInDays($endDate) + 1;
                            $totalDays = $dateDiff;
    
                            // Aggregate department data
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
                                <td><strong>{{ $deptName }}</strong></td>
                                <td class="text-center">
                                    {{ $totals['total'] / $totalDays }}
                                </td>
                                <td class="text-center">
                                    {{ $totalDays }}
                                </td>
                                <td class="text-center">
                                    {{ $totals['present'] }}
                                </td>
                                <td class="text-center">
                                    {{ $totals['late'] }}
                                </td>
                                <td class="text-center">
                                    {{ $totals['absent'] }}
                                </td>
                                <td class="text-center">
                                    {{ $totals['leave'] }}
                                </td>
                                <td class="text-center">
                                    {{ $totals['holiday'] }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $totalPresent = $totals['present'] + $totals['late'];
                                        $totalPossible = $totals['total'];
                                        $presentPercent = $totalPossible > 0
                                            ? round(($totalPresent / $totalPossible) * 100, 1)
                                            : 0;
                                        $percentClass = $presentPercent >= 80 ? 'text-success' : ($presentPercent >= 60 ? 'text-warning' : 'text-danger');
                                    @endphp
                                    <span class="{{ $percentClass }}" style="font-weight: bold; font-size: 14px;">{{ $presentPercent }}%</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No data found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')

@endpush
