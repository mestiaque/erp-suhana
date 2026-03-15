@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Employee Management')}}</title>
@endsection

@section('contents')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Employee List</h4>
                        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                            <i data-feather="plus"></i> Add New Employee
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.employees.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="employee_status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('employee_status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('employee_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="retired" {{ request('employee_status') == 'retired' ? 'selected' : '' }}>Retired</option>
                                    <option value="resigned" {{ request('employee_status') == 'resigned' ? 'selected' : '' }}>Resigned</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="gender" class="form-control">
                                    <option value="">All Gender</option>
                                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="department_id" class="form-control">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="filter"></i> Filter
                                </button>
                                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                                    <i data-feather="x"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Employee Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Photo</th>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                <tr>
                                    <td>{{ $employee->id }}</td>
                                    <td>
                                        @if($employee->photo)
                                            <img src="{{ asset('storage/'.$employee->photo) }}"
                                                 alt="{{ $employee->name }}"
                                                 class="rounded-circle"
                                                 width="40" height="40">
                                        @else
                                            <img src="{{ asset('public/medies/profile.png') }}"
                                                 alt="No Photo"
                                                 class="rounded-circle"
                                                 width="40" height="40">
                                        @endif
                                    </td>
                                    <td>{{ $employee->employee_id ?? 'N/A' }}</td>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->email ?? 'N/A' }}</td>
                                    <td>{{ $employee->mobile }}</td>
                                    <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                    <td>{{ $employee->designation->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($employee->employee_status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($employee->employee_status == 'inactive')
                                            <span class="badge bg-warning">Inactive</span>
                                        @elseif($employee->employee_status == 'retired')
                                            <span class="badge bg-info">Retired</span>
                                        @else
                                            <span class="badge bg-secondary">Resigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.employees.show', $employee->id) }}"
                                               class="btn btn-sm btn-info"
                                               title="View">
                                                <i data-feather="eye"></i>
                                            </a>
                                            <a href="{{ route('admin.employees.edit', $employee->id) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit">
                                                <i data-feather="edit"></i>
                                            </a>
                                            <form action="{{ route('admin.employees.destroy', $employee->id) }}"
                                                  method="POST"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i data-feather="trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No employees found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Initialize Feather Icons
    feather.replace();
</script>
@endpush
