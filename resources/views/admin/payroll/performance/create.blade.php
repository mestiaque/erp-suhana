@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Performance Review') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Add Performance Review</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.performance.store') }}" method="POST">
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
                <div class="col-md-3 mb-3">
                    <label>Year</label>
                    <select name="year" class="form-control" required>
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Quarter</label>
                    <select name="quarter" class="form-control" required>
                        <option value="Q1">Q1</option>
                        <option value="Q2">Q2</option>
                        <option value="Q3">Q3</option>
                        <option value="Q4">Q4</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Rating (0-5)</label>
                    <input type="number" name="rating" class="form-control" step="0.1" min="0" max="5" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Comments</label>
                    <textarea name="comments" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.performance.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
