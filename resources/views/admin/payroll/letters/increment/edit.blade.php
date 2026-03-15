@extends('admin.layouts.master')

@section('title', 'Edit Salary Increment')

@section('content')
<section id="basic-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Salary Increment</h4>
                    <a href="{{ route('admin.letters.increment.index') }}" class="btn btn-secondary btn-sm">
                        <i class="feather icon-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <form action="{{ route('admin.letters.increment.update', $increment->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Employee</label>
                                        <select name="user_id" class="form-control select2" required>
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $increment->user_id == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }} ({{ $employee->employee_id }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Increment Date</label>
                                        <input type="date" name="increment_date" class="form-control" value="{{ $increment->increment_date->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Previous Salary</label>
                                        <input type="number" name="previous_salary" class="form-control" step="0.01" value="{{ $increment->previous_salary }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Increment Amount</label>
                                        <input type="number" name="increment_amount" class="form-control" step="0.01" value="{{ $increment->increment_amount }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea name="remarks" class="form-control" rows="3">{{ $increment->remarks }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Update Increment</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
