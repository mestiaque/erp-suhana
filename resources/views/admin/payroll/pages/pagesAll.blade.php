@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Pages List')}}</title>
@endsection
@push('css')
<style type="text/css"></style>
@endpush
@section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Pages List</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Pages List</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
        	<!--
					//// Permission Page Add
          -->
        	@isset(json_decode(Auth::user()->permission->permission, true)['pages']['add'])
            <a class="btn btn-outline-primary" href="{{route('admin.pagesAction','create')}}">Add Page</a>
            @endisset
            <a class="btn btn-outline-primary" href="{{route('admin.pages')}}">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Basic Elements start -->
    <section class="basic-elements">
        <div class="row">
            <div class="col-md-12">
            @include(adminTheme().'alerts')
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div id="accordion">
                                <div
                                    class="card-header collapsed"
                                    data-toggle="collapse"
                                    data-target="#collapseTwo"
                                    aria-expanded="false"
                                    aria-controls="collapseTwo"
                                    id="headingTwo"
                                    style="background: #f5f7fa; padding: 10px; cursor: pointer; border: 1px solid #00b5b8;"
                                >
                                    Search click Here..
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion" style="border: 1px solid #00b5b8; border-top: 0;">
                                    <div class="card-body">
                                        <form action="{{route('admin.pages')}}">
                                            <div class="row">
                                                <div class="col-md-6 mb-1">
                                                    <div class="input-group">
                                                        <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                                                        <input type="date" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-1">
                                                    <div class="input-group">
                                                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Page Name" class="form-control {{$errors->has('search')?'error':''}}" />
                                                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                        <h4 class="card-title">Pages List</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{route('admin.pages')}}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group mb-1">
                                            <select class="form-control form-control-sm rounded-0" name="action" required="">
                                                <option value="">Select Action</option>
                                                <option value="1">Active</option>
                                                <option value="2">InActive</option>
                                                <option value="3">Feature</option>
                                                <option value="4">Un-feature</option>
                                                <option value="5">Delete</option>
                                            </select>
                                            <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">

                                    </div>
                                    <div class="col-md-4">
                                        <ul class="statuslist mb-0">
                                            <li><a href="{{route('admin.pages')}}">All ({{$totals->total}})</a></li>
                                            <li><a href="{{route('admin.pages',['status'=>'active'])}}">Active ({{$totals->active}})</a></li>
                                            <li><a href="{{route('admin.pages',['status'=>'inactive'])}}">Inactive ({{$totals->inactive}})</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 60px;">
                                                    <label style="cursor: pointer; margin-bottom: 0;"> <input class="checkbox" type="checkbox" class="form-control" id="checkall" /> All <span class="checkCounter"></span> </label>
                                                </th>
                                                <th style="min-width: 300px;">Name</th>
                                                <th style="min-width: 100px; width: 100px;">Image</th>
                                                <th style="min-width: 100px; width: 100px;">Status</th>
                                                <th style="min-width: 160px; width: 160px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pages as $i=>$page)
                                            <tr>
                                                <td>
                                                    <input class="checkbox" type="checkbox" name="checkid[]" value="{{$page->id}}" /><br />

                                                    {{$pages->currentpage()==1?$i+1:$i+($pages->perpage()*($pages->currentpage() - 1))+1}}

                                                </td>
                                                <td>
                                                    <span>
                                                        <a href="{{route('pageView',$page->slug?:'no-slug')}}" target="_blank">{{$page->name}}
                                                        </a>
                                                        @if($page->template)
                                                    	<span style="color: #ccc;">({{$page->template}})</span>
                                                    	@endif
                                                        </span>
                                                        <br />
                                                    @if($page->fetured==true)
                                                    <span><i class="fa fa-star" style="color: #1ab394;"></i></span>
                                                    @endif

                                                    <span style="color: #ccc;"><i class="fa fa-calendar" style="color: #1ab394;"></i> {{$page->created_at->format('d-m-Y')}}</span>
                                                    <span style="color: #ccc;">
                                                        <i class="fa fa-user" style="color: #1ab394;"></i>
                                                        {{Str::limit($page->user?$page->user->name:'No Author',15)}}
                                                    </span>
                                                </td>
                                                <td style="padding:0 5px;text-align: center;">
                                                    <img src="{{asset($page->image())}}" style="max-width: 60px;max-height: 60px;" />
                                                </td>
                                                <td>
                                                    @if($page->status=='active')
                                                    <span class="badge badge-success">Active </span>
                                                    @elseif($page->status=='inactive')
                                                    <span class="badge badge-danger">Inactive </span>
                                                    @else
                                                    <span class="badge badge-danger">Draft </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{route('admin.pagesAction',['edit',$page->id])}}" class="btn btn-md btn-info">
                                                       Edit
                                                    </a>
                                                    <!--
												    //// Permission Page Delete
                                                    -->
                                                    @isset(json_decode(Auth::user()->permission->permission, true)['pages']['delete'])
                                                     <a href="{{route('admin.pagesAction',['delete',$page->id])}}" onclick="return confirm('Are You Want To Delete')" class="btn btn-md btn-danger">
                                                       <i class="fa fa-trash"></i>
                                                     </a>
                                                    @endisset
                                                     <!--
													//// Permission Page Delete
                                                    -->

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    {{$pages->links('pagination')}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
</div>

@endsection @push('js') @endpush
