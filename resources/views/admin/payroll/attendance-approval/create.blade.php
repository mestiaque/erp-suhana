@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Attendance Approval') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Add Attendance Approval</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.attendance-approval.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Employee</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">Select Employee</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Attendance Date</label>
                    <input type="date" name="attendance_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Requested Status</label>
                    <select name="requested_status" class="form-control" required>
                        <option value="P">Present</option>
                        <option value="L">Leave</option>
                        <option value="LT">Late</option>
                        <option value="A">Absent</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.attendance-approval.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
