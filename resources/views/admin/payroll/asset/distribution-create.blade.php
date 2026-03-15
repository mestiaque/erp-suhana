@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Assign Asset') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Assign Asset to Employee</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.assets.distribution.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Asset</label>
                    <select name="asset_id" class="form-control" required>
                        <option value="">Select Asset</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->serial_number }})</option>
                        @endforeach
                    </select>
                </div>
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
                    <label>Assignment Date</label>
                    <input type="date" name="assignment_date" class="form-control" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Condition on Assignment</label>
                    <textarea name="condition_on_assign" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Assign</button>
            <a href="{{ route('admin.assets.distribution') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
