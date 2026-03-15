@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Bonus') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Add Bonus</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.bonus.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Employee (Leave empty for all)</label>
                    <select name="user_id" class="form-control">
                        <option value="">All Employees</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Type</label>
                    <select name="type" class="form-control" required>
                        <option value="festival">Festival</option>
                        <option value="performance">Performance</option>
                        <option value="yearly">Yearly</option>
                        <option value="special">Special</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Amount</label>
                    <input type="number" name="amount" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Month</label>
                    <input type="month" name="month" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.bonus.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
