@extends('admin.layouts.app')

@section('title', 'Manual Attendance Edit')

@section('contents')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Edit Manual Attendance</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.attendance.manual.update', $attendance->id) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="user_id" class="form-label">Employee</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $attendance->user_id == $employee->id ? 'selected' : '' }}>{{ $employee->employeeInfo->name ?? $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ $attendance->date }}" required>
                    </div>
                    <div class="col-md-2">
                        <label for="in_time" class="form-label">In Time</label>
                        <input type="time" name="in_time" id="in_time" class="form-control" value="{{ $attendance->in_time }}" required>
                    </div>
                    <div class="col-md-2">
                        <label for="out_time" class="form-label">Out Time</label>
                        <input type="time" name="out_time" id="out_time" class="form-control" value="{{ $attendance->out_time }}" required>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Update Attendance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
