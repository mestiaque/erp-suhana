@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Daily Attendance Department Wise') }}</title>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('public/admin/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/admin/app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css') }}">

<style>
    .attendance-filters{
        padding:15px;
        border-radius:8px;
        box-shadow:0 2px 6px rgba(0,0,0,0.05);
        margin-bottom:20px;
    }
    table thead{background:#56d2ff;}
    .dept-header{
        background:#343a40;
        color:#fff;
        font-weight:bold;
        font-size:15px;
    }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">

    {{-- ================= FILTER SECTION (UNCHANGED) ================= --}}
    <!-- Filters -->
    <div class="attendance-filters">
        <form action="{{ route('admin.dailyAttendanceDepartmentWise') }}" class="row g-3 align-items-end">
            
            <div class="col-12 mb-3">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="p-2 border-start border-primary border-4 bg-light rounded" style="background:#002bff54 !important">
                            <small class="text-muted d-block">Total Employees</small>
                            <h5 class="mb-0 fw-bold ">{{ $total }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2 border-start border-success border-4 bg-light rounded" style="background:#11ff0054 !important">
                            <small class="text-muted d-block">Present Today</small>
                            <h5 class="mb-0 fw-bold  text-">{{ $present }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2 border-start border-warning border-4 bg-light rounded" style="background:#ffcd0054 !important">
                            <small class="text-muted d-block">Late Entry</small>
                            <h5 class="mb-0 fw-bold  text-">{{ $late }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2 border-start border-danger border-4 bg-light rounded" style="background:#ff000054 !important">
                            <small class="text-muted d-block">Absent</small>
                            <h5 class="mb-0 fw-bold  text-">{{ $absent }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="text-muted">
            
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
                <a href="{{ route('admin.dailyAttendanceDepartmentWise') }}" class="btn btn-warning btn-sm"><i class="bx bx-rotate-left"></i> Reset</a>
            </div>
            
        </form>
    </div>
    

    {{-- ================= TABLE ================= --}}
    <div class="card shadow-sm w-100">
        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-striped table-bordered table-hover dataex-html5-export w-100">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Name</th>
                            <th>ID</th>
                            <th>Designation</th>
                            <th>Employee Type</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Work Hr</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Map</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $sl = 1; @endphp

                        @forelse($departmentWiseAttendances as $department => $employees)

                            {{-- Department Header Row --}}
                            <tr class="dept-header">
                                <td colspan="11">
                                    {{ $department }} ({{ $employees->count() }})
                                </td>
                            </tr>

                            @foreach($employees as $row)
                                <tr>
                                    <td>{{ $sl++ }}</td>
                                    <td>{{ $row['name'] }}</td>
                                    <td>{{ $row['employee_id'] }}</td>
                                    <td>{{ $row['designation'] }}</td>
                                    <td>{{ $row['employee_type'] }}</td>
                                    <td>{{ $row['in_time'] }}</td>
                                    <td>{{ $row['out_time'] }}</td>
                                    <td>{{ $row['work_hr'] }}</td>
                                    <td>
                                        <span class="badge
                                            @if($row['status']=='Present') bg-success
                                            @elseif($row['status']=='Late') bg-warning
                                            @else bg-danger @endif">
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                    <td>{{ $row['date'] }}</td>
                                    <td>
                                        @if($row['map_url'])
                                            <a href="{{ $row['map_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        @empty
                            <tr>
                                <td colspan="11" class="text-center">No attendance found</td>
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
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>

<script>
$(document).ready(function () {
    $('.dataex-html5-export').DataTable({
        dom: "Bfrtip",
        buttons: ['copy','excel','csv','pdf','print'],
        pageLength: 50,
        scrollX: true,
        ordering:false
    });
});
</script>
@endpush