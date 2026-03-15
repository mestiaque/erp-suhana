@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Engineers List')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1" >
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Engineers List</h3>
             <div class="dropdown">
                 @isset(json_decode(Auth::user()->permission->permission, true)['engineers']['add'])
                 <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddUser" style="padding:5px 15px;">
                     <i class="bx bx-plus"></i> Engineer
                 </a>
                 @endisset
                 <a href="{{route('admin.engineers')}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')
            <form action="{{route('admin.engineers')}}">
                <div class="row">
                    <div class="col-md-2 mb-0">
                        <select id="division" class="form-control {{$errors->has('division')?'error':''}}" name="division">
                            <option value="">Select Division</option>
                            @foreach(App\Models\Country::where('type',2)->where('parent_id',1)->get() as $data)
                            <option value="{{$data->id}}" {{$data->id==request()->division?'selected':''}}>{{$data->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-0">
                        <select id="district" class="form-control {{$errors->has('division')?'error':''}}" name="district">
                            @if(request()->division==null)
                            <option value="">No District</option>
                            @else
                            <option value="">Select District</option>
                            @foreach(App\Models\Country::where('type',3)->where('parent_id',request()->division)->get() as $data)
                            <option value="{{$data->id}}" {{$data->id==request()->district?'selected':''}}>{{$data->name}}</option>
                            @endforeach @endif
                        </select>
                    </div>
                    <div class="col-md-2 mb-0">
                        <select id="city" class="form-control {{$errors->has('city')?'error':''}}" name="city">
                            @if(request()->district==null)
                            <option value="">No City</option>
                            @else
                            <option value="">Select City</option>
                            @foreach(App\Models\Country::where('type',4)->where('parent_id',request()->district)->get() as $data)
                            <option value="{{$data->id}}" {{$data->id==request()->city?'selected':''}}>{{$data->name}}</option>
                            @endforeach @endif
                        </select>
                    </div>
                    <div class="col-md-6 mb-0">
                        <div class="input-group">
                            <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Engineer Name, Mobile" class="form-control {{$errors->has('search')?'error':''}}" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>
            <br>
            <form action="{{route('admin.engineers')}}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group mb-1">
                            <select class="form-control form-control-sm rounded-0" name="action" required="">
                                <option value="">Select Action</option>
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>
                                <option value="5">Delete</option>
                            </select>
                            <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!--@isset(json_decode(Auth::user()->permission->permission, true)['engineers']['export'])-->
                        <!--<a class="btn btn-success" href="{{route('admin.engineersExport',['division'=>request()->division,'district'=>request()->district,'city'=>request()->city,'search'=>request()->search])}}"> Export</a>-->
                        <!--@endisset-->
                    </div>
                    <div class="col-md-4">
                        <ul class="statuslist mb-0">
                            <li><a href="{{route('admin.engineers')}}">All ({{$totals->total}})</a></li>
                            <li><a href="{{route('admin.engineers',['status'=>'active'])}}">Active ({{$totals->active}})</a></li>
                            <li><a href="{{route('admin.engineers',['status'=>'inactive'])}}">Inactive ({{$totals->inactive}})</a></li>
                        </ul>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="min-width: 100px;width: 100px;padding-right:0;">
                                    <div class="checkbox mr-3">
                                     <input class="inp-cbx" id="checkall" type="checkbox" style="display: none;" />
                                     <label class="cbx" for="checkall">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                         All <span class="checkCounter"></span>
                                     </label>
                                    </div>
                                </th>
                                <th style="min-width: 80px;">Image</th>
                                <th style="min-width: 200px;">Name</th>
                                <th style="min-width: 150px;">Mobile</th>
                                <th style="min-width: 200px;">Email</th>
                                <th style="min-width: 100px;">Designation</th>
                                <th style="min-width: 120px;">Date</th>
                                <th style="min-width: 100px;width:100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($engineers as $i=>$supplier)
                            <tr>
                                <td style="position: relative;">
                                    <div class="checkbox">
                                         <input class="inp-cbx" id="cbx_{{$supplier->id}}" type="checkbox" name="checkid[]" value="{{$supplier->id}}" style="display: none;" />
                                         <label class="cbx" for="cbx_{{$supplier->id}}">
                                             <span>
                                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                 </svg>
                                             </span>
                                         </label>
                                     </div>
                                    <span style="margin:0 5px;">{{$engineers->currentpage()==1?$i+1:$i+($engineers->perpage()*($engineers->currentpage() - 1))+1}}</span>
                                    @if($supplier->status==1)
                                    <span style="color: #43d39e;font-size: 20px;line-height: 20px;position:absolute;">
                                        <i class="bx bx-check-circle"></i>
                                    </span>
                                    @else
                                    <span style="color: #FF9800;font-size: 20px;line-height: 20px;position:absolute;">
                                        <i class="bx bx-analyse"></i>
                                    </span>
                                    @endif
                                </td>
                                <td style="padding: 5px;">
                                    <img src="{{asset($supplier->image())}}" alt="name" style="max-width:70px;max-height:50px;">
                                </td>
                                <td>{{$supplier->name}}</td>
                                <td>{{$supplier->mobile}}</td>
                                <td>{{$supplier->email}}</td>
                                <td>{{$supplier->short_description}}</td>
                                <td>{{$supplier->created_at->format('d-m-Y')}}</td>
                                <td class="center">
                                    @isset(json_decode(Auth::user()->permission->permission, true)['engineers']['add'])
                                    <a href="{{route('admin.engineersAction',['edit',$supplier->id])}}" class="btn-custom success">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    @endisset

                                    @isset(json_decode(Auth::user()->permission->permission, true)['engineers']['delete'])
                                    <a href="{{route('admin.engineersAction',['delete',$supplier->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                                    @endisset
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$engineers->links('pagination')}}
                </div>
            </form>


        </div>
    </div>
</div>


 <!-- Modal -->
 <div class="modal fade text-left" id="AddUser" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 	<form action="{{route('admin.engineersAction','create')}}" method="post">
	   		@csrf
	   <div class="modal-header">
		 <h4 class="modal-title">Add Engineer</h4>
		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		   <span aria-hidden="true">&times; </span>
		 </button>
	   </div>
	   <div class="modal-body">
	   		<div class="form-group">
			 <label for="name">Name* </label>
             <div class="controls">
                 <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" required="">
				@if ($errors->has('name'))
				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
				@endif
				</div>
         	</div>
			 <div class="form-group">
				<label for="name">Email* </label>
				<div class="controls">
					<input type="email" class="form-control {{$errors->has('email')?'error':''}}" name="email" placeholder="Enter Email" required="">
					@if ($errors->has('email'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email') }}</p>
					@endif
				</div>
         	</div>
	   </div>
	   <div class="modal-footer">
		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
		 <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Engineer</button>
	   </div>
	   </form>
	 </div>
   </div>
 </div>


@endsection @push('js') @endpush

