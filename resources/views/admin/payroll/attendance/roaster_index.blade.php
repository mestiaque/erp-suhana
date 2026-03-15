@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Roaster Management') }}</title>
@endsection

@push('css')
<style>
    .roaster-card {
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }


    .table th {
        background: #eaf9ff;
    }
</style>
@endpush

@section('contents')

<!-- Breadcrumb Area -->


@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Roaster Management</h3>
            <a href="{{ route('admin.attendance.roaster.create') }}" class="btn btn-success btn-sm">
                <i class="bx bx-plus"></i> Create Roaster
            </a>
        </div>

        <div class="filter-section">
            <form action="{{ route('admin.attendance.roaster.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1">Roaster Date</label>
                    <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label mb-1">Department</label>
                    <select name="department_id" class="form-control form-control-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label mb-1">Employee</label>
                    <select name="employee_id" class="form-control form-control-sm">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }} ({{ $emp->employee_id }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bx bx-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.attendance.roaster.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-reset"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-0 mt-3">
            <h6>Roaster for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h6>
            <span class="badge bg-primary text-white">Total: {{ $roasters->count() }} Employees</span>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">SL</th>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Shift</th>
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th style="width: 100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roasters as $i => $roaster)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            @if($roaster->user)
                                {{ $roaster->user->employee_id ?? 'N/A' }}
                            @else
                                <span class="text-danger">User Not Found</span>
                            @endif
                        </td>
                        <td>
                            @if($roaster->user)
                                {{ $roaster->user->name }}
                            @else
                                <span class="text-danger">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($roaster->user && $roaster->user->department)
                                {{ $roaster->user->department->name }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($roaster->user && $roaster->user->designation)
                                {{ $roaster->user->designation->name }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($roaster->shift)
                                <span class="badge bg-info text-white">
                                    {{ $roaster->shift->name_of_shift }}
                                </span>
                            @else
                                <span class="text-muted">No Shift</span>
                            @endif
                        </td>
                        <td>
                            @if($roaster->shift)
                                {{ \Carbon\Carbon::parse($roaster->shift->in_time)->format('h:i A') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($roaster->shift)
                                {{ \Carbon\Carbon::parse($roaster->shift->out_time)->format('h:i A') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editRoaster{{ $roaster->id }}">
                                <i class="bx bx-edit"></i>
                            </button>
                            <form action="{{ route('admin.attendance.roaster.destroy', $roaster->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editRoaster{{ $roaster->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.attendance.roaster.update', $roaster->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Roaster</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Employee</label>
                                            <input type="text" class="form-control" value="{{ $roaster->user->name ?? 'N/A' }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Shift *</label>
                                            <select name="shift_id" class="form-control" required>
                                                <option value="">Select Shift</option>
                                                @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}" {{ $roaster->shift_id == $shift->id ? 'selected' : '' }}>
                                                    {{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->in_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($shift->out_time)->format('h:i A') }})
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="date" name="roster_date" value="{{ $roaster->roster_date }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Update Roaster</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bx bx-info-circle" style="font-size: 48px;"></i>
                            <p class="mb-0 mt-2">No roaster assigned for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                            <a href="{{ route('admin.attendance.roaster.create') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bx bx-plus"></i> Create Roaster
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="row text-center g-3">
                            @foreach($shifts as $shift)
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-white rounded shadow">
                                        <h5 class="mb-1 fw-bold">
                                            {{ $roasters->where('shift_id', $shift->id)->count() }}
                                        </h5>
                                        <small class="text-muted">{{ $shift->name_of_shift }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('js')
<script>
    // Auto-submit form on date change
    $('input[name="date"]').on('change', function() {
        $(this).closest('form').submit();
    });
</script>
@endpush
