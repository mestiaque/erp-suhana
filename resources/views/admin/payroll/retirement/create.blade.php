@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Retirement') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Add Retirement</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.retirement.store') }}" method="POST">
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
                    <label>Retirement Date</label>
                    <input type="date" name="retirement_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Type</label>
                    <select name="type" class="form-control" required>
                        <option value="mandatory">Mandatory</option>
                        <option value="voluntary">Voluntary</option>
                        <option value="medical">Medical</option>
                        <option value="early">Early</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Settlement Amount</label>
                    <input type="number" name="settlement_amount" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.retirement.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
