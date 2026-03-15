@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Loan') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Add Loan</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.loan.store') }}" method="POST">
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
                    <label>Type</label>
                    <select name="type" class="form-control" required>
                        <option value="personal">Personal</option>
                        <option value="house">House</option>
                        <option value="car">Car</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Principal Amount</label>
                    <input type="number" name="principal_amount" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Interest Rate (%)</label>
                    <input type="number" name="interest_rate" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Total Installments</label>
                    <input type="number" name="total_installments" class="form-control" min="1" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Disbursement Date</label>
                    <input type="date" name="disbursement_date" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.loan.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
