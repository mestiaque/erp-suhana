@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Attendance Summary') }}</title>
@endsection

@push('css')
<style>
    .report-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    table.table thead { background: #4ecdc4; color: white; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Attendance Summary</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Attendance</li>
        <li class="item">Summary</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="report-card">
        <form action="{{ route('admin.attendance.summary') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}" class="form-control">
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
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Total Days</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Late</th>
                        <th>Leave</th>
                        <th>Attendance %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summaries ?? [] as $i => $summary)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $summary->employee_id ?? 'N/A' }}</td>
                        <td>{{ $summary->name }}</td>
                        <td>{{ $summary->department_name ?? 'N/A' }}</td>
                        <td>{{ $summary->total_days ?? 0 }}</td>
                        <td><span class="badge bg-success">{{ $summary->present ?? 0 }}</span></td>
                        <td><span class="badge bg-danger">{{ $summary->absent ?? 0 }}</span></td>
                        <td><span class="badge bg-warning">{{ $summary->late ?? 0 }}</span></td>
                        <td><span class="badge bg-info">{{ $summary->leave ?? 0 }}</span></td>
                        <td>
                            @php
                                $percentage = $summary->total_days > 0 ? round(($summary->present / $summary->total_days) * 100, 1) : 0;
                            @endphp
                            <strong class="{{ $percentage >= 90 ? 'text-success' : ($percentage >= 75 ? 'text-warning' : 'text-danger') }}">
                                {{ $percentage }}%
                            </strong>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No summary data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
