@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Edit Overtime') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Overtime</h5>
            <a href="{{route('admin.overtimes.index')}}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.overtimes.update', $overtime->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Employee <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-control" required>
                                <option value="">Select Employee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $overtime->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }} [{{ $user->employee_id }}]</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Overtime Type <span class="text-danger">*</span></label>
                            <select name="overtime_type" class="form-control" required>
                                <option value="general" {{ $overtime->overtime_type == 'general' ? 'selected' : '' }}>General</option>
                                <option value="special" {{ $overtime->overtime_type == 'special' ? 'selected' : '' }}>Special</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="date" name="overtime_date" class="form-control" value="{{ $overtime->overtime_date }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control" value="{{ $overtime->start_time }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control" value="{{ $overtime->end_time }}" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Reason</label>
                            <textarea name="reason" class="form-control" rows="3">{{ $overtime->reason }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
