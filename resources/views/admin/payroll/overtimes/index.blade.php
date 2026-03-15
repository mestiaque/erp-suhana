@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Overtime List') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Overtime List</h5>
            <div>
                <a href="{{route('admin.overtimes.create')}}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add Overtime</a>
            </div>
        </div>
        <!-- Filter Section -->
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.overtimes.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label for="user_id">Employee</label>
                    <select name="user_id" id="user_id" class="form-control form-control-sm">
                        <option value="">Select Employee</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} [{{ $user->employee_id }}]</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="overtime_type">Overtime Type</label>
                    <select name="overtime_type" id="overtime_type" class="form-control form-control-sm">
                        <option value="">Select Type</option>
                        <option value="general" {{ request('overtime_type') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="special" {{ request('overtime_type') == 'special' ? 'selected' : '' }}>Special</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control form-control-sm">
                        <option value="">Select Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-12 text-right adjustments">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.overtimes.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-times"></i> Reset</a>
                </div>
            </form>
        </div>
        <div class="card-body ">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered ">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Hours</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overtimes as $key => $overtime)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $overtime->user->employee_id ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($overtime->user && $overtime->user->photo)
                                        <img src="{{ asset('uploads/user_photo/' . $overtime->user->photo) }}" alt="{{ $overtime->user->name }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover; margin-right: 10px;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 35px; height: 35px; background-color: {{ random_color($overtime->user_id ?? 0) }}; margin-right: 10px;">
                                            {{ strtoupper(substr($overtime->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span>{{ $overtime->user->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>{{ $overtime->user->department->name ?? 'N/A' }}</td>
                            <td>
                                @if($overtime->overtime_type == 'general')
                                    <span class="badge badge-info">General</span>
                                @else
                                    <span class="badge badge-warning">Special</span>
                                @endif
                            </td>
                            <td>{{ $overtime->overtime_date }}</td>
                            <td>{{ $overtime->start_time }}</td>
                            <td>{{ $overtime->end_time }}</td>
                            <td>{{ $overtime->hours }}</td>
                            <td>{{ $overtime->rate }}</td>
                            <td>{{ $overtime->amount }}</td>
                            <td>
                                @if($overtime->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($overtime->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @else
                                    <span class="badge badge-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                @if($overtime->status == 'pending')
                                    <a href="{{ route('admin.overtimes.approve', $overtime->id) }}" class="btn btn-xs btn-success" title="Approve"><i class="fa fa-check"></i></a>
                                    <form action="{{ route('admin.overtimes.reject', $overtime->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="button" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#rejectModal{{ $overtime->id }}"><i class="fa fa-times"></i></button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.overtimes.edit', $overtime->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('admin.overtimes.destroy', $overtime->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal{{ $overtime->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form action="{{ route('admin.overtimes.reject', $overtime->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Overtime</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Rejection Reason</label>
                                                <textarea name="rejection_reason" class="form-control" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="13" class="text-center">No data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $overtimes->links() }}
        </div>
    </div>
</div>
@endsection
