@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Employee Report') }}</title>
@endsection

@push('css')
<style>

</style>
@endpush

@section('contents')

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <!-- Status -->
    <div class="row mb-3">
        {{-- Employees --}}
        <div class="col-md-2">
            <div class="card shadow-sm border-0" style="background: #007bff38">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-users"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $status['total'] ?? 0 }}</h4>
                        <small class="text-muted">Total Employees</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Employees --}}
        <div class="col-md-2">
            <div class="card shadow-sm border-0" style="background: #28a74538">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-user-check"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $status['active'] ?? 0 }}</h5>
                        <small class="text-muted">Active</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Deduction --}}
        <div class="col-md-2">
            <div class="card shadow-sm border-0" style="background: #dc353538">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-user-minus"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $status['inactive'] ?? 0 }}</h5>
                        <small class="text-muted">Inactive</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Deduction --}}
        <div class="col-md-2">
            <div class="card shadow-sm border-0" style="background: #dcc33538">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-hourglass-half"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $status['probation'] ?? 0 }}</h5>
                        <small class="text-muted">Probation</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Retired --}}
        <div class="col-md-2">
            <div class="card shadow-sm border-0" style="background: #17a2b838">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-user-tie"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $status['retired'] ?? 0 }}</h4>
                        <small class="text-muted">Retired</small>
                    </div>
                </div>
            </div>
        </div>
        {{-- Terminated --}}
        <div class="col-md-2">
            <div class="card shadow-sm border-0" style="background: #6c757d38">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-user-slash"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $status['terminated'] ?? 0 }}</h4>
                        <small class="text-muted">Terminated</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="emp-card">
        <form action="{{ route('admin.reports.employees.index') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-2">
                <label class="mb-0">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, ID, Phone, Email" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="mb-0">Department</label>
                <select name="department_id" class="form-control form-control-sm">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="mb-0">Designation</label>
                <select name="designation_id" class="form-control form-control-sm">
                    <option value="">All Designations</option>
                    @foreach($designations ?? [] as $desig)
                    <option value="{{ $desig->id }}" {{ request('designation_id') == $desig->id ? 'selected' : '' }}>
                        {{ $desig->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="mb-0">Status</label>
                <select name="employee_status" class="form-control form-control-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('employee_status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="probation" {{ request('employee_status') == 'probation' ? 'selected' : '' }}>Probation</option>
                    <option value="inactive" {{ request('employee_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="retired" {{ request('employee_status') == 'retired' ? 'selected' : '' }}>Retired</option>
                    <option value="terminated" {{ request('employee_status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="mb-0">Gender</label>
                <select name="gender" class="form-control form-control-sm">
                    <option value="">All Gender</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <label class="mb-0">&nbsp;</label>
                <button type="submit" class="btn btn-sm btn-primary mr-1"><i class="bx bx-search"></i>Search</button>
                <a href="{{ route('admin.reports.employees.index') }}" class="btn btn-sm btn-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Emp ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Join Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees ?? [] as $key => $employee)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $employee->employee_id ?? 'N/A' }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($employee->photo)
                                    <img src="{{ asset('uploads/user_photo/' . $employee->photo) }}" alt="{{ $employee->name }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover; margin-right: 10px;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 35px; height: 35px; background-color: {{ random_color($employee->id ?? 0) }}; margin-right: 10px;">
                                        {{ strtoupper(substr($employee->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ $employee->name }}</span>
                            </div>
                        </td>
                        <td>{{ $employee->department->name ?? 'N/A' }}</td>
                        <td>{{ $employee->designation->name ?? 'N/A' }}</td>
                        <td>{{ $employee->mobile ?? 'N/A' }}</td>
                        <td>{{ $employee->email ?? 'N/A' }}</td>
                        <td>{{ ucfirst($employee->gender ?? 'N/A') }}</td>
                        <td>{{ $employee->joining_date ? date('d-m-Y', strtotime($employee->joining_date)) : 'N/A' }}</td>
                        <td>
                            @if($employee->employee_status == 'active')
                            <span class="badge bg-success text-white">Active</span>
                            @elseif($employee->employee_status == 'probation')
                            <span class="badge bg-info text-white">Probation</span>
                            @elseif($employee->employee_status == 'retired')
                            <span class="badge bg-warning text-white">Retired</span>
                            @elseif($employee->employee_status == 'terminated')
                            <span class="badge bg-dark text-white">Terminated</span>
                            @else
                            <span class="badge bg-secondary">{{ $employee->employee_status ?? 'N/A' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No employees found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
