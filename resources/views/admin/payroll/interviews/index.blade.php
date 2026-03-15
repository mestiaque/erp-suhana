@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Interviews List') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Interviews List</h5>
            <div>
                <a href="{{route('admin.interviews.create')}}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Schedule Interview</a>
            </div>
        </div>
        <!-- Filter Section -->
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.interviews.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="position">Position</label>
                    <input type="text" name="position" id="position" class="form-control form-control-sm" placeholder="Search by position" value="{{ request('position') }}">
                </div>
                <div class="col-md-3">
                    <label for="department_id">Department</label>
                    <select name="department_id" id="department_id" class="form-control form-control-sm">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="interview_type">Interview Type</label>
                    <select name="interview_type" id="interview_type" class="form-control form-control-sm">
                        <option value="">Select Type</option>
                        <option value="written" {{ request('interview_type') == 'written' ? 'selected' : '' }}>Written</option>
                        <option value="oral" {{ request('interview_type') == 'oral' ? 'selected' : '' }}>Oral</option>
                        <option value="practical" {{ request('interview_type') == 'practical' ? 'selected' : '' }}>Practical</option>
                        <option value="final" {{ request('interview_type') == 'final' ? 'selected' : '' }}>Final</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control form-control-sm">
                        <option value="">Select Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="selected" {{ request('status') == 'selected' ? 'selected' : '' }}>Selected</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>
                <div class="col-md-12 text-right adjustments">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.interviews.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-times"></i> Reset</a>
                </div>
            </form>
        </div>
        <div class="card-body ">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered ">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Candidate Name</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Marks</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interviews as $key => $interview)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $interview->candidate_name }}</td>
                            <td>{{ $interview->position }}</td>
                            <td>{{ $interview->department->name ?? 'N/A' }}</td>
                            <td>{{ $interview->interview_date }}</td>
                            <td>{{ $interview->interview_time }}</td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($interview->interview_type) }}</span>
                            </td>
                            <td>{{ $interview->total_marks ?? 'N/A' }}</td>
                            <td>
                                @if($interview->status == 'scheduled')
                                    <span class="badge badge-primary">Scheduled</span>
                                @elseif($interview->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($interview->status == 'selected')
                                    <span class="badge badge-success">Selected</span>
                                @elseif($interview->status == 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-secondary">On Hold</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#statusModal{{ $interview->id }}"><i class="fa fa-edit"></i></button>
                                <a href="{{ route('admin.interviews.edit', $interview->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('admin.interviews.destroy', $interview->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <!-- Status Update Modal -->
                        <div class="modal fade" id="statusModal{{ $interview->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form action="{{ route('admin.interviews.status', $interview->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Interview Status - {{ $interview->candidate_name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="scheduled" {{ $interview->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                    <option value="pending" {{ $interview->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="selected" {{ $interview->status == 'selected' ? 'selected' : '' }}>Selected</option>
                                                    <option value="rejected" {{ $interview->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                    <option value="on_hold" {{ $interview->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Written Marks</label>
                                                        <input type="number" name="written_marks" class="form-control" value="{{ $interview->written_marks }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Oral Marks</label>
                                                        <input type="number" name="oral_marks" class="form-control" value="{{ $interview->oral_marks }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Practical Marks</label>
                                                        <input type="number" name="practical_marks" class="form-control" value="{{ $interview->practical_marks }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Feedback</label>
                                                <textarea name="feedback" class="form-control">{{ $interview->feedback }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Update</button>
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
            {{ $interviews->links() }}
        </div>
    </div>
</div>
@endsection
