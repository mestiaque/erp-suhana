@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Termination') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Termination</h5>
            <a href="{{route('admin.terminations.index')}}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.terminations.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Employee <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-control" required>
                                <option value="">Select Employee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} [{{ $user->employee_id }}]</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Termination Date <span class="text-danger">*</span></label>
                            <input type="date" name="termination_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Termination Type <span class="text-danger">*</span></label>
                            <select name="termination_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="resignation">Resignation</option>
                                <option value="dismissal">Dismissal</option>
                                <option value="retirement">Retirement</option>
                                <option value="death">Death</option>
                                <option value="contract_end">Contract End</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Notice Period</label>
                            <input type="text" name="notice_period" class="form-control" placeholder="e.g., 1 month">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
