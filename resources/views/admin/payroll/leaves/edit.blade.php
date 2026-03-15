@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Edit Leave')}}</title>
@endsection

@section('contents')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title mb-0">Edit Leave</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.leaves.index')}}">Leaves</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                                <form class="form" action="{{route('admin.leaves.update', $leave->id)}}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Employee</label>
                                                    <input type="text" class="form-control" value="{{$leave->user->name}}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="leave_type_id">Leave Type</label>
                                                    <select id="leave_type_id" name="leave_type_id" class="form-control" required>
                                                        @foreach($leaveTypes as $type)
                                                            <option value="{{$type->id}}" {{$leave->leave_type_id == $type->id ? 'selected' : ''}}>{{$type->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="start_date">Start Date</label>
                                                    <input type="date" id="start_date" class="form-control" name="start_date" value="{{$leave->start_date->format('Y-m-d')}}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_date">End Date</label>
                                                    <input type="date" id="end_date" class="form-control" name="end_date" value="{{$leave->end_date->format('Y-m-d')}}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="reason">Reason</label>
                                            <textarea id="reason" rows="5" class="form-control" name="reason">{{$leave->reason}}</textarea>
                                        </div>

                                        <hr>
                                        <h4>Approval</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status">Status</label>
                                                    <select id="status" name="status" class="form-control">
                                                        <option value="pending" {{$leave->status == 'pending' ? 'selected' : ''}}>Pending</option>
                                                        <option value="approved" {{$leave->status == 'approved' ? 'selected' : ''}}>Approved</option>
                                                        <option value="rejected" {{$leave->status == 'rejected' ? 'selected' : ''}}>Rejected</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="rejection_reason">Rejection Reason (if rejected)</label>
                                                    <textarea id="rejection_reason" rows="2" class="form-control" name="rejection_reason">{{$leave->rejection_reason}}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-actions">
                                        <button type="button" class="btn btn-warning mr-1" onclick="history.back()">
                                            <i class="ft-x"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-check-square-o"></i> Update
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
