@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Working Hours') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Add Working Hours</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.working-hours.store') }}" method="POST">
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
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Actual Hours</label>
                    <input type="number" name="actual_hours" class="form-control" step="0.01">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Overtime Hours</label>
                    <input type="number" name="overtime_hours" class="form-control" step="0.01">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Grass Hours</label>
                    <input type="number" name="grass_hours" class="form-control" step="0.01">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.working-hours.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
