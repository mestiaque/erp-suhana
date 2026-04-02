@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Daily Attendance List') }}</title>
@endsection

@push('css')

@endpush

@section('contents')

<div class="flex-grow-1">
    @include(adminTheme().'alerts')
    <!-- Filters -->
                <div class="row  mb-3 w-100">
                {{-- Employees --}}
                <div class="col-md-3">
                    <div class="card shadow-sm border-0" style="background: #007bff38">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                style="width:50px;height:50px;">
                                <i class="fa fa-users"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $total ?? 0 }}</h4>
                                <small class="text-muted">Total Employees</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Present Employees --}}
                <div class="col-md-3">
                    <div class="card shadow-sm border-0" style="background: #28a74538">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                style="width:50px;height:50px;">
                                <i class="fa fa-user-check"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $present ?? 0 }}</h5>
                                <small class="text-muted">Present Employees</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Late Employees --}}
                <div class="col-md-3">
                    <div class="card shadow-sm border-0" style="background: #ffc10738">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                style="width:50px;height:50px;">
                                <i class="fa fa-clock"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $late ?? 0 }}</h5>
                                <small class="text-muted">Late Employees</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Absent Employees --}}
                <div class="col-md-3">
                    <div class="card shadow-sm border-0" style="background: #dc354538">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                style="width:50px;height:50px;">
                                <i class="fa fa-user-times"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $absent ?? 0 }}</h4>
                                <small class="text-muted">Absent Employees</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <div class="attendance-filters pt-0">
        <form action="{{ route('admin.dailyAttendance') }}" method="GET" class="row g-3 align-items-end">


                <!-- Stats -->




            <div class="col-md-2">
                <label for="startDate" class="form-label mb-0">Start Date</label>
                <input type="date" name="startDate" value="{{ request()->startDate ?? date('Y-m-d') }}" class="form-control form-control-sm">
            </div>

            <div class="col-md-2">
                <label for="endDate" class="form-label mb-0">End Date</label>
                <input type="date" name="endDate" value="{{ request()->endDate ?? date('Y-m-d') }}" class="form-control form-control-sm">
            </div>

            <div class="col-md-2">
                <label for="employeeId" class="form-label mb-0">Employee ID</label>
                <input type="text" name="employeeId" value="{{ request()->employeeId }}" class="form-control form-control-sm" placeholder="ID">
            </div>

            <div class="col-md-2">
                <label for="search" class="form-label mb-0">Employee Name</label>
                <input type="text" name="search" value="{{ request()->search }}" class="form-control form-control-sm" placeholder="Name">
            </div>

            <div class="col-md-2">
                <label for="designation" class="form-label mb-0">Designation</label>
                <select name="designation" class="form-control form-control-sm">
                    <option value="">All Designations</option>
                    @foreach($designations as $des)
                        <option value="{{ $des->id }}" @if(request()->designation==$des->id) selected @endif>{{ $des->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="department" class="form-label mb-0">Department</label>
                <select name="department" class="form-control form-control-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" @if(request()->department==$dept->id) selected @endif>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="employeeType" class="form-label mb-0">Employee Type</label>
                <select name="employeeType" class="form-control form-control-sm">
                    <option value="">All Types</option>
                    @foreach($employeeTypes as $type)
                        <option value="{{ $type->id }}" @if(request()->employeeType==$type->id) selected @endif>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="status" class="form-label mb-0">Status</label>
                <select name="status" class="form-control form-control-sm">
                    <option value="">All Status</option>
                    <option value="Present" @if(request()->status=='Present') selected @endif>Present</option>
                    <option value="Late" @if(request()->status=='Late') selected @endif>Late</option>
                    <option value="Absent" @if(request()->status=='Absent') selected @endif>Absent</option>
                </select>
            </div>

            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i> Search</button>
                <a href="{{ route('admin.dailyAttendance') }}" class="btn btn-warning btn-sm"><i class="bx bx-rotate-left"></i> Reset</a>
            </div>

            <div class=" text-end col-md-3 offset-md-3 text-right">
                <a href="{{ route('admin.dailyAttendanceExport', request()->query()) }}" target="_blank" class="btn btn-success btn-sm">
                    <i class="bx bx-file"></i> Export Excel
                </a>
                <a href="{{ route('admin.dailyAttendancePrint', request()->query()) }}" target="_blank" class="btn btn-primary btn-sm">
                    <i class="bx bx-printer"></i> Print
                </a>
            </div>

        </form>
    </div>




    <!-- Summary -->


    <!-- Attendance Table -->
    <div class="card shadow-sm w-100">
        <div class="card-body">
            <!-- Add print btn -->

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover dataex-html5-export w-100">
                    <thead class="table-darkx">
                        <tr>
                            <th>SL</th>
                            <th>Name</th>
                            <th>Emp. ID</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Employee Type</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Work Hr.</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Map</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($finalData as $key => $row)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td class="d-flex align-items-center gap-2">
                                {!! $row['avatar'] !!}
                                {{ $row['name'] }}</td>
                            <td>{{ $row['employee_id'] ?? '--' }}</td>
                            <td>{{ $row['designation'] ?? '--' }}</td>
                            <td>{{ $row['department'] ?? '--' }}</td>
                            <td>{{ $row['employee_type'] ?? '--' }}</td>
                            <td>{{ $row['in_time'] }}</td>
                            <td>{{ $row['out_time'] }}</td>
                            <td>{{ $row['work_hr'] }}</td>
                            <td>
                                <span class="badge
                                    @if($row['status']=='Present') badge-success
                                    @elseif($row['status']=='Late') bg-warning
                                    @elseif($row['status']=='Holiday') bg-info
                                    @elseif($row['status']=='Present') bg-info
                                    @else bg-danger
                                    @endif text-white">
                                    {{ $row['status'] }}
                                </span>
                            </td>
                            <td>{{ $row['date'] }}    <br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($row['date'])->format('l') }}
                            </small></td>
                            <td>
                                @if($row['map_url'])
                                <a href="{{ $row['map_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                @else -- @endif
                            </td>
                            <td>
                                @if(!empty($row['attendance_id']))
                                    <details>
                                        <summary class="btn btn-sm btn-info" style="list-style:none;cursor:pointer;">Edit</summary>
                                        <form action="{{ route('admin.dailyAttendance.update', $row['attendance_id']) }}" method="POST" class="mt-2" style="min-width:260px;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="date" value="{{ $row['date'] }}">
                                            <div class="mb-1">
                                                <input type="time" name="in_time" class="form-control form-control-sm" value="{{ $row['in_time'] != '--' ? \Carbon\Carbon::createFromFormat('h:i A', $row['in_time'])->format('H:i') : '' }}" required>
                                            </div>
                                            <div class="mb-1">
                                                <input type="time" name="out_time" class="form-control form-control-sm" value="{{ $row['out_time'] != '--' ? \Carbon\Carbon::createFromFormat('h:i A', $row['out_time'])->format('H:i') : '' }}" required>
                                            </div>
                                            <div class="mb-1">
                                                <select name="status" class="form-control form-control-sm" required>
                                                    <option value="Present" {{ $row['status'] == 'Present' ? 'selected' : '' }}>Present</option>
                                                    <option value="Late" {{ $row['status'] == 'Late' ? 'selected' : '' }}>Late</option>
                                                    <option value="Absent" {{ $row['status'] == 'Absent' ? 'selected' : '' }}>Absent</option>
                                                    <option value="Leave" {{ $row['status'] == 'Leave' ? 'selected' : '' }}>Leave</option>
                                                </select>
                                            </div>
                                            <div class="mb-1">
                                                <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Remarks (optional)">
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary w-100">Update</button>
                                        </form>
                                    </details>
                                @elseif($row['status'] == 'Absent')
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#absentToManualModal{{ $key }}">
                                        Add
                                    </button>
                                @else
                                    <span class="text-muted">No Edit</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="13" class="text-center">No attendance found</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    @if($users->hasPages())
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                            </div>
                            {{ $users->appends(request()->all())->links('pagination') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($finalData as $key => $row)
    @if(empty($row['attendance_id']) && $row['status'] == 'Absent')
        <div class="modal fade" id="absentToManualModal{{ $key }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.attendance.manual.store') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Add Attendance Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="user_id" value="{{ $row['id'] }}">
                            <div class="mb-2">
                                <label class="form-label">Employee</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $row['name'] }} ({{ $row['employee_id'] ?? 'N/A' }})" disabled>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control form-control-sm" value="{{ $row['date'] }}" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">In Time</label>
                                <input type="time" name="in_time" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Out Time</label>
                                <input type="time" name="out_time" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Optional remarks">
                            </div>
                            <small class="text-muted">This request will be counted as attendance only after approval.</small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Submit for Approval</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@push('js')
@endpush
@endsection
