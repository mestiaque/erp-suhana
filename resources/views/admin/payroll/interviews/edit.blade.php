@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Edit Interview') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Interview</h5>
            <a href="{{route('admin.interviews.index')}}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.interviews.update', $interview->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Candidate Name <span class="text-danger">*</span></label>
                            <input type="text" name="candidate_name" class="form-control" value="{{ $interview->candidate_name }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Position <span class="text-danger">*</span></label>
                            <input type="text" name="position" class="form-control" value="{{ $interview->position }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $interview->email }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $interview->phone }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Department</label>
                            <select name="department_id" class="form-control">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ $interview->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Interview Date <span class="text-danger">*</span></label>
                            <input type="date" name="interview_date" class="form-control" value="{{ $interview->interview_date }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Interview Time <span class="text-danger">*</span></label>
                            <input type="time" name="interview_time" class="form-control" value="{{ $interview->interview_time }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Interview Type <span class="text-danger">*</span></label>
                            <select name="interview_type" class="form-control" required>
                                <option value="written" {{ $interview->interview_type == 'written' ? 'selected' : '' }}>Written</option>
                                <option value="oral" {{ $interview->interview_type == 'oral' ? 'selected' : '' }}>Oral</option>
                                <option value="practical" {{ $interview->interview_type == 'practical' ? 'selected' : '' }}>Practical</option>
                                <option value="final" {{ $interview->interview_type == 'final' ? 'selected' : '' }}>Final</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Venue</label>
                            <input type="text" name="venue" class="form-control" value="{{ $interview->venue }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Interviewer</label>
                            <select name="interviewer_id" class="form-control">
                                <option value="">Select Interviewer</option>
                                @foreach($interviewers as $interviewer)
                                    <option value="{{ $interviewer->id }}" {{ $interview->interviewer_id == $interviewer->id ? 'selected' : '' }}>{{ $interviewer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ $interview->notes }}</textarea>
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
