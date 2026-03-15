@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Probation') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Probation</h5>
            <a href="{{route('admin.probations.index')}}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.probations.store') }}">
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
                            <label>Probation Period (Months) <span class="text-danger">*</span></label>
                            <input type="number" name="months" class="form-control" min="1" value="3" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="probation_start_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>End Date <span class="text-danger">*</span></label>
                            <input type="date" name="probation_end_date" class="form-control" required>
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
