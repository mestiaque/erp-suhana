@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Invalid Attendance Report') }}</title>
@endsection

@push('css')
<style>
    .report-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    table.table thead { background: #ff6b6b; color: white; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Invalid Attendance Report</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Attendance</li>
        <li class="item">Invalid Report</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="report-card">
        <form action="{{ route('admin.attendance.invalid.report') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Date</label>
                <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Issue Type</label>
                <select name="issue_type" class="form-control">
                    <option value="">All Issues</option>
                    <option value="missing_out" {{ request('issue_type') == 'missing_out' ? 'selected' : '' }}>Missing Out Time</option>
                    <option value="missing_in" {{ request('issue_type') == 'missing_in' ? 'selected' : '' }}>Missing In Time</option>
                    <option value="duplicate" {{ request('issue_type') == 'duplicate' ? 'selected' : '' }}>Duplicate Entry</option>
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
                        <th>Date</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th>Issue</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invalidRecords ?? [] as $i => $record)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                        <td>{{ $record->employee_id ?? 'N/A' }}</td>
                        <td>{{ $record->name }}</td>
                        <td>{{ $record->department_name ?? 'N/A' }}</td>
                        <td>{{ $record->in_time ? \Carbon\Carbon::parse($record->in_time)->format('h:i A') : '<span class="text-danger">Missing</span>' }}</td>
                        <td>{{ $record->out_time ? \Carbon\Carbon::parse($record->out_time)->format('h:i A') : '<span class="text-danger">Missing</span>' }}</td>
                        <td>
                            <span class="badge bg-danger">{{ $record->issue ?? 'Invalid' }}</span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#fixModal{{ $record->id }}">
                                <i class="bx bx-edit"></i> Fix
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bx bx-check-circle" style="font-size: 48px; color: #28a745;"></i>
                            <p class="mb-0 mt-2">Great! No invalid attendance records found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
