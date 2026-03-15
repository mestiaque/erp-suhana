@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Convenience Request') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Add Convenience Request</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.convenience.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Employee</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Type</label>
                        <select name="type" class="form-control" required>
                            <option value="salary_advance">Salary Advance</option>
                            <option value="loan">Loan</option>
                            <option value="transfer">Transfer</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Amount</label>
                        <input type="number" name="amount" class="form-control" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Reason</label>
                        <textarea name="reason" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.convenience.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
