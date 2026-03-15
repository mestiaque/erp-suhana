@extends('admin.layouts.master')

@section('title', 'Edit Confirmation Letter')

@section('content')
<section id="basic-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Confirmation Letter</h4>
                    <a href="{{ route('admin.letters.confirmation.index') }}" class="btn btn-secondary btn-sm">
                        <i class="feather icon-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <form action="{{ route('admin.letters.confirmation.update', $letter->id) }}" method="POST">
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
                                        <label>Confirmation Date</label>
                                        <input type="date" name="confirmation_date" class="form-control" value="{{ $letter->confirmation_date->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="pending" {{ $letter->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="confirmed" {{ $letter->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="rejected" {{ $letter->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Performance Remarks</label>
                                        <textarea name="performance_remarks" class="form-control" rows="3">{{ $letter->performance_remarks }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea name="remarks" class="form-control" rows="2">{{ $letter->remarks }}</textarea>
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
