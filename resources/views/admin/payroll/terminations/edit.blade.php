@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Edit Termination') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Termination</h5>
            <a href="{{route('admin.terminations.index')}}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.terminations.update', $termination->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Employee <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-control" required>
                                <option value="">Select Employee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $termination->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }} [{{ $user->employee_id }}]</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Termination Date <span class="text-danger">*</span></label>
                            <input type="date" name="termination_date" class="form-control" value="{{ $termination->termination_date }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Termination Type <span class="text-danger">*</span></label>
                            <select name="termination_type" class="form-control" required>
                                <option value="resignation" {{ $termination->termination_type == 'resignation' ? 'selected' : '' }}>Resignation</option>
                                <option value="dismissal" {{ $termination->termination_type == 'dismissal' ? 'selected' : '' }}>Dismissal</option>
                                <option value="retirement" {{ $termination->termination_type == 'retirement' ? 'selected' : '' }}>Retirement</option>
                                <option value="death" {{ $termination->termination_type == 'death' ? 'selected' : '' }}>Death</option>
                                <option value="contract_end" {{ $termination->termination_type == 'contract_end' ? 'selected' : '' }}>Contract End</option>
                                <option value="other" {{ $termination->termination_type == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Notice Period</label>
                            <input type="text" name="notice_period" class="form-control" value="{{ $termination->notice_period }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required>{{ $termination->reason }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
