@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Manual Attendance')}}</title>
@endsection

@section('contents')

<div class="flex-grow-1">
<!-- Breadcrumb Area -->
    <div class="breadcrumb-area">
        <h1>Setting</h1>

        <ol class="breadcrumb">
            <li class="item">
                <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item"><a href="{{ route('admin.attendance.manual.index') }}">Manual Attendance</a></li>
            <li class="item">Create</li>
        </ol>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Add Manual Attendance</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.attendance.manual.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="user_id" class="form-label">Employee</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->employeeInfo->name ?? $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label for="in_time" class="form-label">In Time</label>
                        <input type="time" name="in_time" id="in_time" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label for="out_time" class="form-label">Out Time</label>
                        <input type="time" name="out_time" id="out_time" class="form-control" required>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Save Attendance</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
