@extends('admin.layouts.app')

@section('title', 'Manual Leave Create')

@section('contents')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Manual Leave Create</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.leaves.manual.store') }}">
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
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="reason" class="form-label">Reason</label>
                        <input type="text" name="reason" id="reason" class="form-control" required>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Create Leave</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
