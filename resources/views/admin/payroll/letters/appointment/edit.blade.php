@extends('admin.layouts.master')

@section('title', 'Edit Appointment Letter')

@section('content')
<section id="basic-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Appointment Letter</h4>
                    <a href="{{ route('admin.letters.appointment.index') }}" class="btn btn-secondary btn-sm">
                        <i class="feather icon-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <form action="{{ route('admin.letters.appointment.update', $letter->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Employee</label>
                                        <select name="user_id" class="form-control select2" required>
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $letter->user_id == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }} ({{ $employee->employee_id }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Letter Date</label>
                                        <input type="date" name="letter_date" class="form-control" value="{{ $letter->letter_date->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Position/Designation</label>
                                        <input type="text" name="position" class="form-control" value="{{ $letter->position }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <input type="text" name="department" class="form-control" value="{{ $letter->department }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Salary</label>
                                        <input type="number" name="salary" class="form-control" step="0.01" value="{{ $letter->salary }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Joining Date</label>
                                        <input type="date" name="joining_date" class="form-control" value="{{ $letter->joining_date->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Terms & Conditions</label>
                                        <textarea name="terms" class="form-control" rows="4">{{ $letter->terms }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Update Letter</button>
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
