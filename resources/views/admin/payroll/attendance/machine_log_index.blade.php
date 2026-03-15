@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Machine Log - Attendance') }}</title>
@endsection

@section('contents')

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="card">
        <div class="card-body">
            <div class="mb-2">
                <form action="{{ route('admin.attendance.machine.log.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4 ">
                        <label class="mb-0">Date</label>
                        <div class="d-flex">
                            <input type="date" name="date" value="{{ $date ?? date('Y-m-d') }}" class="form-control form-control-sm">
                             <span class="mx-2 p-1">to</span>
                            <input type="date" name="date_to" value="{{ $date_to ?? date('Y-m-d') }}" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="mb-0">Employee</label>
                        <select name="user_id" class="form-control form-control-sm select2">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name ?? $employee->first_name }} ({{ $employee->employee_id ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="mb-0">Device</label>
                        <select name="device_sn" class="form-control form-control-sm">
                            <option value="">All Devices</option>
                            @foreach($devices as $device)
                                <option value="{{ $device }}" {{ request('device_sn') == $device ? 'selected' : '' }}>
                                    {{ $device }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm mr-2">
                            <i class="bx bx-filter"></i> Filter
                        </button>
                        <a href="{{ route('admin.attendance.machine.log.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-reset"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered" id="machineLogTable">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Device SN</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $key => $log)
                        <tr>
                            <td>{{ $key + 1 + ($logs->perPage() * ($logs->currentPage() - 1)) }}</td>
                            <td>{{ Carbon\Carbon::parse($log->log_time)->format('Y-m-d') }}</td>
                            <td>{{ Carbon\Carbon::parse($log->log_time)->format('h:i:s A') }}</td>
                            <td>{{ $log->user->employee_id ?? 'N/A' }}</td>
                            <td>{{ $log->user->name ?? $log->user->first_name ?? 'Unknown' }}</td>
                            <td>{{ $log->user->department->name ?? 'N/A' }}</td>
                            <td>{{ $log->device_sn ?? 'N/A' }}</td>
                            <td>{{ $log->type_name ?? 'Check In/Out' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No machine logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $logs->links('pagination') }}
        </div>

    </div>

</div>

@endsection

@push('js')

@endpush
