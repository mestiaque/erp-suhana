@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Leaves List') }}</title>
@endsection

@section('contents')
<section id="basic-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create Joining Letter</h4>
                    <a href="{{ route('admin.letters.joining.index') }}" class="btn btn-secondary btn-sm">
                        <i class="feather icon-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <form action="{{ route('admin.letters.joining.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Employee</label>
                                        <select name="user_id" class="form-control select2" required>
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->employee_id }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Letter Date</label>
                                        <input type="date" name="letter_date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Joining Date</label>
                                        <input type="date" name="joining_date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Designation</label>
                                        <input type="text" name="designation" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <input type="text" name="department" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea name="remarks" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Save Letter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
