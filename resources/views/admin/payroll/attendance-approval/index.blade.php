@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Attendance Approval') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Attendance Approval</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.attendance-approval.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Original Status</th>
                    <th>Requested Status</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($approvals as $app)
                <tr>
                    <td>{{ $app->user->name ?? 'N/A' }}</td>
                    <td>{{ $app->attendance_date }}</td>
                    <td>{{ $app->original_status ?? 'N/A' }}</td>
                    <td>{{ $app->requested_status }}</td>
                    <td>{{ $app->reason }}</td>
                    <td>
                        @if($app->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($app->status == 'approved')
                            <span class="badge badge-success">Approved</span>
                        @else
                            <span class="badge badge-danger">Rejected</span>
                        @endif
                    </td>
                    <td>
                        @if($app->status == 'pending')
                        <form action="{{ route('admin.attendance-approval.update', $app->id) }}" method="POST" class="d-inline">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                        </form>
                        <form action="{{ route('admin.attendance-approval.update', $app->id) }}" method="POST" class="d-inline">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">No data found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
