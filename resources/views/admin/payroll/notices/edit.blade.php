@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Edit Notice') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Notice</h5>
            <a href="{{route('admin.notices.index')}}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.notices.update', $notice->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ $notice->title }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Notice Date <span class="text-danger">*</span></label>
                            <input type="date" name="notice_date" class="form-control" value="{{ $notice->notice_date }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-control" required>
                                <option value="low" {{ $notice->priority == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $notice->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ $notice->priority == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ $notice->start_date }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ $notice->end_date }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ $notice->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $notice->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" required>{{ $notice->description }}</textarea>
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
