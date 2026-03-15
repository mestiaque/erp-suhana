@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Apply Leave')}}</title>
@endsection

@section('contents')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title mb-0">Apply Leave</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.leaves.index')}}">Leaves</a></li>
                        <li class="breadcrumb-item active">Apply</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <section id="basic-form-layouts">
            <div class="row match-height">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-content collapse show">
                            <div class="card-body">
                                <form class="form" action="{{route('admin.leaves.store')}}" method="POST">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="user_id">Employee</label>
                                                    <select id="user_id" name="user_id" class="form-control">
                                                        <option value="">Select Employee (Optional - Defaults to You)</option>
                                                        @foreach($users as $user)
                                                            <option value="{{$user->id}}">{{$user->name}} ({{$user->email}})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="leave_type_id">Leave Type</label>
                                                    <select id="leave_type_id" name="leave_type_id" class="form-control" required>
                                                        <option value="">Select Leave Type</option>
                                                        @foreach($leaveTypes as $type)
                                                            <option value="{{$type->id}}">{{$type->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="start_date">Start Date</label>
                                                    <input type="date" id="start_date" class="form-control" name="start_date" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_date">End Date</label>
                                                    <input type="date" id="end_date" class="form-control" name="end_date" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="reason">Reason</label>
                                            <textarea id="reason" rows="5" class="form-control" name="reason" placeholder="Reason for leave"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="button" class="btn btn-warning mr-1" onclick="history.back()">
                                            <i class="ft-x"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-check-square-o"></i> Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
