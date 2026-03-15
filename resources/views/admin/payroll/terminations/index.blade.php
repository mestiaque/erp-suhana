@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Terminations List') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Terminations List</h5>
            <div>
                <a href="{{route('admin.terminations.create')}}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add Termination</a>
            </div>
        </div>
        <!-- Filter Section -->
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.terminations.index') }}" class="row g-3">
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
                    <label for="termination_type">Termination Type</label>
                    <select name="termination_type" id="termination_type" class="form-control form-control-sm">
                        <option value="">Select Type</option>
                        <option value="resignation" {{ request('termination_type') == 'resignation' ? 'selected' : '' }}>Resignation</option>
                        <option value="dismissal" {{ request('termination_type') == 'dismissal' ? 'selected' : '' }}>Dismissal</option>
                        <option value="retirement" {{ request('termination_type') == 'retirement' ? 'selected' : '' }}>Retirement</option>
                        <option value="death" {{ request('termination_type') == 'death' ? 'selected' : '' }}>Death</option>
                        <option value="contract_end" {{ request('termination_type') == 'contract_end' ? 'selected' : '' }}>Contract End</option>
                        <option value="other" {{ request('termination_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control form-control-sm">
                        <option value="">Select Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3 text-right ">
                    <label for="">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Filter</button>
                        <a href="{{ route('admin.terminations.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-times"></i> Reset</a>
                    </div>
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
                            <th>Termination Date</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($terminations as $key => $termination)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $termination->user->employee_id ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($termination->user && $termination->user->photo)
                                        <img src="{{ asset('uploads/user_photo/' . $termination->user->photo) }}" alt="{{ $termination->user->name }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover; margin-right: 10px;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 35px; height: 35px; background-color: {{ random_color($termination->user_id ?? 0) }}; margin-right: 10px;">
                                            {{ strtoupper(substr($termination->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span>{{ $termination->user->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>{{ $termination->user->department->name ?? 'N/A' }}</td>
                            <td>{{ $termination->termination_date }}</td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($termination->termination_type) }}</span>
                            </td>
                            <td>{{ Str::limit($termination->reason, 50) }}</td>
                            <td>
                                @if($termination->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($termination->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @else
                                    <span class="badge badge-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                @if($termination->status == 'pending')
                                    <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#approveModal{{ $termination->id }}"><i class="fa fa-check"></i></button>
                                    <button type="button" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#rejectModal{{ $termination->id }}"><i class="fa fa-times"></i></button>
                                @endif
                                <a href="{{ route('admin.terminations.edit', $termination->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('admin.terminations.destroy', $termination->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <!-- Approve Modal -->
                        <div class="modal fade" id="approveModal{{ $termination->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form action="{{ route('admin.terminations.approve', $termination->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Approve Termination - {{ $termination->user->name ?? 'N/A' }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Exit Interview Notes</label>
                                                <textarea name="exit_interview_notes" class="form-control"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Documents</label>
                                                <textarea name="documents" class="form-control" placeholder="List of documents collected"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Approve</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal{{ $termination->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form action="{{ route('admin.terminations.reject', $termination->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Termination</h5>
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
                            <td colspan="9" class="text-center">No data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $terminations->links() }}
        </div>
    </div>
</div>
@endsection
