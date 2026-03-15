@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Create Roaster') }}</title>
@endsection

@push('css')
<style>
    .employee-card {
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .employee-select-box {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 10px;
    }
    .employee-item {
        padding: 10px;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .employee-item:hover {
        background: #f8f9fa;
        border-color: #56d2ff;
    }
    .employee-item.selected {
        background: #e7f9ff;
        border-color: #56d2ff;
    }
</style>
@endpush

@section('contents')

<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>Create Roaster</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">Attendance</li>
        <li class="item">
            <a href="{{route('admin.attendance.roaster.index')}}">Roaster Management</a>
        </li>
        <li class="item">Create</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="employee-card">
        <div class="card-header mb-3">
            <h3>Assign Roaster to Employees</h3>
        </div>

        <form action="{{ route('admin.attendance.roaster.store') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Date and Shift Selection -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Roaster Date *</label>
                        <input type="date" name="roster_date" value="{{ old('roster_date', date('Y-m-d')) }}" class="form-control form-control-sm" required>
                        @error('roster_date')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Select Shift *</label>
                        <select name="shift_id" id="shift_id" class="form-control form-control-sm" required>
                            <option value="">-- Select Shift --</option>
                            @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                {{ $shift->name_of_shift }} ({{ \Carbon\Carbon::parse($shift->shift_starting_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($shift->shift_closing_time)->format('h:i A') }})
                            </option>
                            @endforeach
                        </select>
                        @error('shift_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="selectAll()">
                                <i class="bx bx-check-square"></i> Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="deselectAll()">
                                <i class="bx bx-square"></i> Deselect All
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Employee Selection -->
            <div class="row">
                <div class="col-md-12">
                    <label class="mb-2">Select Employees * ({{ $employees->count() }} Active Employees)</label>

                    <!-- Search Box -->
                    <div class="form-group mb-3">
                        <input type="text" id="employeeSearch" class="form-control" placeholder="Search employee by name, ID...">
                    </div>

                    <div class="employee-select-box" id="employeeList">
                        @forelse($employees as $employee)
                        <div class="employee-item" data-employee-id="{{ $employee->id }}"
                             data-name="{{ strtolower($employee->name) }}"
                             data-emp-id="{{ strtolower($employee->employee_id ?? '') }}"
                             onclick="toggleEmployee(this)">
                            <div class="form-check">
                                <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}"
                                       class="form-check-input employee-checkbox" id="emp{{ $employee->id }}">
                                <label class="form-check-label w-100" for="emp{{ $employee->id }}" style="cursor: pointer;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $employee->name }}</strong>
                                            <small class="text-muted d-block">
                                                ID: {{ $employee->employee_id ?? 'N/A' }} |
                                                {{ $employee->designation->name ?? 'No Designation' }} |
                                                {{ $employee->department->name ?? 'No Department' }}
                                            </small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4">
                            <i class="bx bx-user-x" style="font-size: 48px;"></i>
                            <p class="mb-0">No active employees found</p>
                        </div>
                        @endforelse
                    </div>

                    @error('employee_ids')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <hr>

            <div class="form-group text-end">
                <a href="{{ route('admin.attendance.roaster.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save"></i> Create Roaster
                </button>
            </div>

        </form>
    </div>

</div>

@endsection

@push('js')
<script>
    // Toggle employee selection
    function toggleEmployee(element) {
        const checkbox = element.querySelector('.employee-checkbox');
        checkbox.checked = !checkbox.checked;
        element.classList.toggle('selected', checkbox.checked);
    }

    // Select all employees
    function selectAll() {
        document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
            checkbox.checked = true;
            checkbox.closest('.employee-item').classList.add('selected');
        });
    }

    // Deselect all employees
    function deselectAll() {
        document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
            checkbox.checked = false;
            checkbox.closest('.employee-item').classList.remove('selected');
        });
    }

    // Search functionality
    document.getElementById('employeeSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.employee-item').forEach(item => {
            const name = item.dataset.name;
            const empId = item.dataset.empId;
            if (name.includes(searchTerm) || empId.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Prevent label click from toggling twice
    document.querySelectorAll('.form-check-label').forEach(label => {
        label.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    // Initialize selected state on page load
    document.querySelectorAll('.employee-checkbox:checked').forEach(checkbox => {
        checkbox.closest('.employee-item').classList.add('selected');
    });
</script>
@endpush
