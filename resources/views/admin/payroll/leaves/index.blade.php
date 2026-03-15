@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Leaves List') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Leaves List</h5>
            <div>
                <button class="btn btn-sm btn-success d-print-none" data-toggle="modal" data-target="#addLeaveModal"><i class="fa fa-plus"></i> Apply Leave</button>
                <a href="{{route('admin.leaves.types')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> Leave Types</a>
            </div>
        </div>
        <!-- Filter Section -->
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.leaves.index') }}" class="row g-3">
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
                    <label for="leave_type_id">Leave Type</label>
                    <select name="leave_type_id" id="leave_type_id" class="form-control form-control-sm">
                        <option value="">Select Leave Type</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
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
                    <label for="department_id">Department</label>
                    <select name="department_id" id="department_id" class="form-control form-control-sm">
                        <option value="">Select Department</option>
                        @if(isset($departments))
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="start_date_from">From Date</label>
                    <input type="date" name="start_date_from" id="start_date_from" class="form-control form-control-sm" placeholder="From Date" value="{{ request('start_date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="start_date_to">To Date</label>
                    <input type="date" name="start_date_to" id="start_date_to" class="form-control form-control-sm" placeholder="To Date" value="{{ request('start_date_to') }}">
                </div>
                <div class="col-md-12 text-right adjustments">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.leaves.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-times"></i> Reset</a>
                </div>
            </form>
        </div>
        <div class="card-body ">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered ">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>SL</th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Type</th>
                            <th>Leave Balance</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaves as $leave)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($leave->user && $leave->user->photo)
                                        <img src="{{ asset('uploads/user_photo/' . $leave->user->photo) }}" alt="{{ $leave->user->name }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover; margin-right: 10px;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 35px; height: 35px; background-color: {{ random_color($leave->user_id ?? 0) }}; margin-right: 10px;">
                                            {{ strtoupper(substr($leave->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold">{{ $leave->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $leave->user->employee_id ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{$leave->user->department->name ?? 'N/A'}}</td>
                            <td>{{$leave->leaveType->name ?? 'N/A'}}</td>
                            <td>
                                @if(isset($leaveBalances[$leave->user_id][$leave->leave_type_id]))
                                    @php $balance = $leaveBalances[$leave->user_id][$leave->leave_type_id]; @endphp
                                    <span class="text-success">{{ $balance['taken'] }}/{{ $balance['allowed'] }}</span>
                                    @if($balance['remaining'] < 0)
                                        <span class="text-danger">(Over: {{ abs($balance['remaining']) }})</span>
                                    @else
                                        <span class="text-muted">(Rem: {{ $balance['remaining'] }})</span>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{$leave->start_date->format('d M Y')}}</td>
                            <td>{{$leave->end_date->format('d M Y')}}</td>
                            <td>{{$leave->days}}</td>
                            <td>
                                @if($leave->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($leave->status == 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-custom success" data-toggle="modal" data-target="#editLeaveModal{{$leave->id}}"><i class="fa fa-edit"></i></button>
                                <form action="{{route('admin.leaves.destroy', $leave->id)}}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-custom danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$leaves->links()}}
            </div>
        </div>
    </div>

</div>

<!-- Add Leave Modal -->
<div class="modal fade" id="addLeaveModal" tabindex="-1" role="dialog" aria-labelledby="addLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLeaveModalLabel">Apply Leave</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('admin.leaves.store')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_id">Employee</label>
                        <select class="form-control" name="user_id" required>
                            <option value="">Select Employee</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="leave_type_id">Leave Type</label>
                        <select class="form-control" name="leave_type_id" required>
                            <option value="">Select Leave Type</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{$type->id}}">{{$type->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason</label>
                        <textarea class="form-control" name="reason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Leave Modals -->
@foreach($leaves as $leave)
<div class="modal fade" id="editLeaveModal{{$leave->id}}" tabindex="-1" role="dialog" aria-labelledby="editLeaveModalLabel{{$leave->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLeaveModalLabel{{$leave->id}}">Edit Leave</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('admin.leaves.update', $leave->id)}}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="leave_type_id">Leave Type</label>
                        <select class="form-control" name="leave_type_id" required>
                            @foreach($leaveTypes as $type)
                                <option value="{{$type->id}}" {{$leave->leave_type_id == $type->id ? 'selected' : ''}}>{{$type->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{$leave->start_date->format('Y-m-d')}}" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="{{$leave->end_date->format('Y-m-d')}}" required>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason</label>
                        <textarea class="form-control" name="reason" rows="3">{{$leave->reason}}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" name="status">
                            <option value="pending" {{$leave->status == 'pending' ? 'selected' : ''}}>Pending</option>
                            <option value="approved" {{$leave->status == 'approved' ? 'selected' : ''}}>Approved</option>
                            <option value="rejected" {{$leave->status == 'rejected' ? 'selected' : ''}}>Rejected</option>
                        </select>
                    </div>
                    @if($leave->status == 'rejected')
                    <div class="form-group">
                        <label for="rejection_reason">Rejection Reason</label>
                        <textarea class="form-control" name="rejection_reason" rows="2">{{$leave->rejection_reason}}</textarea>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
