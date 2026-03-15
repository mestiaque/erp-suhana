@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Add Holiday')}}</title>
@endsection

@section('contents')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title mb-0">Add Holiday</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.holiday.index')}}">Holidays</a></li>
                        <li class="breadcrumb-item active">Add</li>
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
                                <form class="form" action="{{route('admin.holiday.store')}}" method="POST">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="title">Title <span class="text-danger">*</span></label>
                                                    <input type="text" id="title" class="form-control" name="title" placeholder="Holiday Title" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="type">Type <span class="text-danger">*</span></label>
                                                    <select id="type" name="type" class="form-control" required>
                                                        <option value="">Select Type</option>
                                                        <option value="General">General</option>
                                                        <option value="National">National</option>
                                                        <option value="Festival">Festival</option>
                                                        <option value="Religious">Religious</option>
                                                        <option value="Optional">Optional</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="from_date">From Date <span class="text-danger">*</span></label>
                                                    <input type="date" id="from_date" class="form-control" name="from_date" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="to_date">To Date <span class="text-danger">*</span></label>
                                                    <input type="date" id="to_date" class="form-control" name="to_date" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status">Status</label>
                                                    <select id="status" name="status" class="form-control">
                                                        <option value="active">Active</option>
                                                        <option value="inactive">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <textarea id="remarks" rows="5" class="form-control" name="remarks" placeholder="Additional remarks"></textarea>
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
