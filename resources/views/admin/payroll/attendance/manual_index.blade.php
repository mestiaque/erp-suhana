@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Manual Attendance')}}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    @include(adminTheme().'alerts')
    <div class="card mb-4 mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manual Attendance List</h5>
            <button class="btn btn-sm btn-success d-print-none" data-bs-toggle="modal" data-bs-target="#createAttendanceModal"><i class="bx bx-plus"></i> Add Attendance</button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.attendance.manual.index') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="user_id" class="form-label">Employee</label>
                    <select name="user_id" id="user_id" class="form-control form-control-sm">
                        <option value="">All</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>{{ $employee->employeeInfo->name ?? $employee->name }} [{{ $employee->employeeInfo->employee_id ?? 'N/A' }}]</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" id="department_id" class="form-control form-control-sm">
                        <option value="">All</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date" class="form-control form-control-sm" value="{{ request('date') }}">
                </div>
                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-sm btn-primary w-50">Filter</button>
                    <a href="{{ route('admin.attendance.manual.index') }}" class="btn btn-sm btn-secondary">Reset</a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $attendance?->user?->employee_id ?? 'N/A' }}</td>
                            <td>{{ $attendance?->user?->name ?? 'N/A' }}</td>
                            <td>{{ $attendance?->user?->department?->name ?? 'N/A' }}</td>
                            <td>{{ $attendance->date }}</td>
                            <td>{{ $attendance->in_time }}</td>
                            <td>{{ $attendance->out_time }}</td>
                            <td>{{ $attendance->remarks ?? 'N/A' }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-custom success" data-bs-toggle="modal" data-bs-target="#editAttendanceModal{{ $attendance->id }}"><i class="bx bx-edit"></i></button>
                                <form action="{{ route('admin.attendance.manual.destroy', $attendance->id) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-custom danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No attendance records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $attendances->links('pagination') }}
            </div>
        </div>
    </div>
</div>

<!-- Create Attendance Modal -->
<div class="modal fade" id="createAttendanceModal" tabindex="-1" aria-labelledby="createAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.attendance.manual.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createAttendanceModalLabel">Add Manual Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id_create" class="form-label">Employee</label>
                        <select name="user_id" id="user_id_create" class="form-control form-control-sm" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->employeeInfo->name ?? $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date_create" class="form-label">Date</label>
                        <input type="date" name="date" id="date_create" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="in_time_create" class="form-label">In Time</label>
                        <input type="time" name="in_time" id="in_time_create" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label for="out_time_create" class="form-label">Out Time</label>
                        <input type="time" name="out_time" id="out_time_create" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label for="remarks_create" class="form-label">Remarks</label>
                        <input type="text" name="remarks" id="remarks_create" class="form-control form-control-sm">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Attendance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Attendance Modals -->
@foreach($attendances as $attendance)
<div class="modal fade" id="editAttendanceModal{{ $attendance->id }}" tabindex="-1" aria-labelledby="editAttendanceModalLabel{{ $attendance->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.attendance.manual.update', $attendance->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editAttendanceModalLabel{{ $attendance->id }}">Edit Manual Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id_edit{{ $attendance->id }}" class="form-label">Employee</label>
                        <input type="hidden" name="user_id" id="user_id_edit{{ $attendance->id }}" value="{{ $attendance->user_id }}">
                        <input type="text" class="form-control form-control-sm" value="{{ $attendance?->user?->name ?? 'N/A' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="date_edit{{ $attendance->id }}" class="form-label">Date</label>
                        <input type="date" name="date" id="date_edit{{ $attendance->id }}" class="form-control form-control-sm" value="{{ $attendance->date }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="in_time_edit{{ $attendance->id }}" class="form-label">In Time</label>
                        <input type="time" name="in_time" id="in_time_edit{{ $attendance->id }}" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($attendance->in_time)->format('H:i') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="out_time_edit{{ $attendance->id }}" class="form-label">Out Time</label>
                        <input type="time" name="out_time" id="out_time_edit{{ $attendance->id }}" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($attendance->out_time)->format('H:i') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="remarks_edit{{ $attendance->id }}" class="form-label">Remarks</label>
                        <input type="text" name="remarks" id="remarks_edit{{ $attendance->id }}" class="form-control form-control-sm" value="{{ $attendance->remarks }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update Attendance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
