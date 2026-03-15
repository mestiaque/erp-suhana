@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Probations List') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Probations List</h5>
            <div>
                <a href="{{route('admin.probations.create')}}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add Probation</a>
            </div>
        </div>
        <!-- Filter Section -->
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.probations.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="user_id">Employee</label>
                    <select name="user_id" id="user_id" class="form-control form-control-sm">
                        <option value="">Select Employee</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} [{{ $user->employee_id }}]</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control form-control-sm">
                        <option value="">Select Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="extended" {{ request('status') == 'extended' ? 'selected' : '' }}>Extended</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="confirmation_status">Confirmation Status</label>
                    <select name="confirmation_status" id="confirmation_status" class="form-control form-control-sm">
                        <option value="">Select Status</option>
                        <option value="pending" {{ request('confirmation_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('confirmation_status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="rejected" {{ request('confirmation_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-12 text-right adjustments">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.probations.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-times"></i> Reset</a>
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
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Months</th>
                            <th>Status</th>
                            <th>Confirmation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($probations as $key => $probation)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $probation->user->employee_id ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($probation->user && $probation->user->photo)
                                        <img src="{{ asset('uploads/user_photo/' . $probation->user->photo) }}" alt="{{ $probation->user->name }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover; margin-right: 10px;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 35px; height: 35px; background-color: {{ random_color($probation->user_id ?? 0) }}; margin-right: 10px;">
                                            {{ strtoupper(substr($probation->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span>{{ $probation->user->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>{{ $probation->user->department->name ?? 'N/A' }}</td>
                            <td>{{ $probation->probation_start_date }}</td>
                            <td>{{ $probation->probation_end_date }}</td>
                            <td>{{ $probation->months }}</td>
                            <td>
                                @if($probation->status == 'active')
                                    <span class="badge badge-primary">Active</span>
                                @elseif($probation->status == 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($probation->status == 'extended')
                                    <span class="badge badge-warning">Extended</span>
                                @else
                                    <span class="badge badge-danger">Terminated</span>
                                @endif
                            </td>
                            <td>
                                @if($probation->confirmation_status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($probation->confirmation_status == 'confirmed')
                                    <span class="badge badge-success">Confirmed</span>
                                @else
                                    <span class="badge badge-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                @if($probation->confirmation_status == 'pending')
                                    <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#confirmModal{{ $probation->id }}"><i class="fa fa-check"></i></button>
                                @endif
                                <button type="button" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#extendModal{{ $probation->id }}"><i class="fa fa-extend"></i></button>
                                <a href="{{ route('admin.probations.edit', $probation->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('admin.probations.destroy', $probation->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <!-- Confirm Modal -->
                        <div class="modal fade" id="confirmModal{{ $probation->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form action="{{ route('admin.probations.confirm', $probation->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Probation - {{ $probation->user->name ?? 'N/A' }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Confirmation Status</label>
                                                <select name="confirmation_status" class="form-control" required>
                                                    <option value="confirmed">Confirm</option>
                                                    <option value="rejected">Reject</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea name="confirmation_notes" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Submit</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Extend Modal -->
                        <div class="modal fade" id="extendModal{{ $probation->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form action="{{ route('admin.probations.extend', $probation->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Extend Probation - {{ $probation->user->name ?? 'N/A' }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>New End Date</label>
                                                <input type="date" name="new_end_date" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Performance Notes</label>
                                                <textarea name="performance_notes" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Extend</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $probations->links() }}
        </div>
    </div>
</div>
@endsection
