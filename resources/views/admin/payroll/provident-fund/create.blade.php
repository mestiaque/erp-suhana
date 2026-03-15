@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Provident Fund') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Add Provident Fund</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.provident-fund.store') }}" method="POST">
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
                    <label>Month</label>
                    <select name="month" class="form-control" required>
                        @foreach(['01','02','03','04','05','06','07','08','09','10','11','12'] as $m)
                            <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Employee Contribution</label>
                    <input type="number" name="employee_contribution" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Company Contribution</label>
                    <input type="number" name="company_contribution" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Interest Rate (%)</label>
                    <input type="number" name="interest_rate" class="form-control" step="0.01" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.provident-fund.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
