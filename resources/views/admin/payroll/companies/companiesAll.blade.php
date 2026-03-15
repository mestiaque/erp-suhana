@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Customers List')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">


<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Customers List</h3>
         <div class="dropdown">
             <a href="{{ route('admin.companies', [
            'export' => 'report',
            'concern' => request()->concern,
            'division' => request()->division,
            'district' => request()->district,
            'city' => request()->city,
            'deed_serial' => request()->deed_serial,
            'search' => request()->search]) }}" class="btn-custom yellow">
                <i class="bx bx-export"></i> Export
             </a>
             @isset(json_decode(Auth::user()->permission->permission, true)['company']['add'])
             <a href="{{route('admin.companiesAction','create')}}" class="btn-custom primary" style="padding:5px 15px;">
                 <i class="bx bx-plus"></i> Customer
             </a>
             @endisset
             <a href="{{route('admin.companies')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.companies')}}">
            <div class="row">
                <!--<div class="col-md-2 mb-0">-->
                <!--     <select class="form-control" name="concern">-->
                <!--        <option value="" >Select Sister Concern</option>-->
                <!--        <option value="MMC" {{request()->concern=='MMC'?'selected':''}}  >MG Machineries Corporation (MMC)</option>-->
                <!--        <option value="EMC" {{request()->concern=='EMC'?'selected':''}} >Embroidery Machine Corporation (EMC)</option>-->
                <!--        <option value="MTCI" {{request()->concern=='MTCI'?'selected':''}} >MG Training Centre Institute (MTCI)</option>-->
                <!--        <option value="FLCD" {{request()->concern=='FLCD'?'selected':''}} >Fiber Laser Cutting Division (FLCD)</option>-->
                <!--     </select>-->
                <!--</div>-->
                <div class="col-md-3 mb-0">
                        <select id="division" class="form-control {{$errors->has('division')?'error':''}}" name="division">
                            <option value="">Select Division</option>
                            @foreach(App\Models\Country::where('type',2)->where('parent_id',1)->get() as $data)
                            <option value="{{$data->id}}" {{$data->id==request()->division?'selected':''}}>{{$data->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-0">
                        <select id="district" class="form-control {{$errors->has('division')?'error':''}}" name="district">
                            @if(request()->division==null)
                            <option value="">Select District</option>
                            @else
                            <option value="">Select District</option>
                            @foreach(App\Models\Country::where('type',3)->where('parent_id',request()->division)->get() as $data)
                            <option value="{{$data->id}}" {{$data->id==request()->district?'selected':''}}>{{$data->name}}</option>
                            @endforeach @endif
                        </select>
                    </div>
                    <div class="col-md-3 mb-0">
                        <select id="city" class="form-control {{$errors->has('city')?'error':''}}" name="city">
                            @if(request()->district==null)
                            <option value="">Select Thana</option>
                            @else
                            <option value="">Select Thana</option>
                            @foreach(App\Models\Country::where('type',4)->where('parent_id',request()->district)->get() as $data)
                            <option value="{{$data->id}}" {{$data->id==request()->city?'selected':''}}>{{$data->name}}</option>
                            @endforeach @endif
                        </select>
                    </div>

                    <!--<div class="col-md-2 mb-0">-->
                    <!--     <input type="text" name="deed_serial" value="{{request()->deed_serial}}" placeholder="Deed / CT Serial" class="form-control {{$errors->has('deed_serial')?'error':''}}" />-->
                    <!--</div>-->
                    <div class="col-md-3 mb-0">
                        <div class="input-group">
                            <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Name, Mobile" class="form-control {{$errors->has('search')?'error':''}}" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
            </div>
        </form>
        <br>
        <form action="{{route('admin.companies')}}">
            <div class="row">
                <div class="col-md-4">
                    <!--<div class="input-group mb-1">-->
                    <!--    <select class="form-control form-control-sm rounded-0" name="action" required="">-->
                    <!--        <option value="">Select Action</option>-->
                    <!--        <option value="1">Pending</option>-->
                    <!--        <option value="2">Confirmed</option>-->
                    <!--        <option value="2">Cancelled</option>-->
                    <!--        <option value="5">Delete</option>-->
                    <!--    </select>-->
                    <!--    <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>-->
                    <!--</div>-->
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-6">
                    <ul class="statuslist mb-0">
                        <li><a href="{{route('admin.companies')}}">All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.companies',['status'=>'active'])}}">Active ({{$totals->active}})</a></li>
                        <li><a href="{{route('admin.companies',['status'=>'inactive'])}}">Inactive ({{$totals->inactive}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="min-width: 100px;width: 100px;padding-right:0;">
                                <!--<div class="checkbox mr-3">-->
                                <!-- <input class="inp-cbx" id="checkall" type="checkbox" style="display: none;" />-->
                                <!-- <label class="cbx" for="checkall">-->
                                <!--     <span>-->
                                <!--         <svg width="12px" height="10px" viewbox="0 0 12 10">-->
                                <!--             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>-->
                                <!--         </svg>-->
                                <!--     </span>-->
                                <!--     All <span class="checkCounter"></span> -->
                                <!-- </label>-->
                                <!--</div>-->
                                SL
                            </th>

                            <th style="min-width: 200px;">Company Name</th>
                            <th style="min-width: 200px;">Customer Name</th>
                            <!--<th style="min-width: 150px;">Owner Deg.</th>-->
                            <th style="min-width: 150px;">Mobile</th>
                            <!--<th style="min-width: 200px;">Email</th>-->
                            <!--<th style="min-width: 200px;">K/P name</th>-->
                            <!--<th style="min-width: 200px;">K/P Des</th>-->
                            <!--<th style="min-width: 150px;">K/P Mobile</th>-->
                            <!--<th style="min-width: 200px;">K/P Email</th>-->
                            <th style="min-width: 250px;">Address</th>
                            <!--<th style="min-width: 150px;">Customer Status</th>-->
                            <!--<th style="min-width: 100px;">Com. Cty</th>-->
                            <!--<th style="min-width: 150px;">Com. Status</th>-->
                            <!--<th style="min-width: 100px;">M/c QTY</th>-->
                            <!--<th style="min-width: 150px;">Brand Name</th>-->
                            <!--<th style="min-width: 60px;">Employee</th>-->
                            <!--<th style="min-width: 200px;">Next Visit</th>-->
                            <!--<th style="min-width: 200px;">Requirement</th>-->
                            <!--<th style="min-width: 200px;">remarks</th>-->
                            <th style="min-width: 120px;">Date</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                            <th style="min-width: 150px;">Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $i=>$company)
                        <tr>
                            <td style="position: relative;">
                                <!--<div class="checkbox">-->
                                <!--     <input class="inp-cbx" id="cbx_{{$company->id}}" type="checkbox" name="checkid[]" value="{{$company->id}}" style="display: none;" />-->
                                <!--     <label class="cbx" for="cbx_{{$company->id}}">-->
                                <!--         <span>-->
                                <!--             <svg width="12px" height="10px" viewbox="0 0 12 10">-->
                                <!--                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>-->
                                <!--             </svg>-->
                                <!--         </span>-->
                                <!--     </label>-->
                                <!-- </div>-->
                                <span style="margin:0 5px;">{{$companies->currentpage()==1?$i+1:$i+($companies->perpage()*($companies->currentpage() - 1))+1}}</span>
                                @if($company->status=='active')
                                <span style="color: #43d39e;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                @else
                                <span style="color: #FF9800;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-analyse"></i>
                                </span>
                                @endif

                            </td>


                            <td>

                                {{$company->factory_name}}

                            </td>
                            <td>
                                <a href="{{route('admin.companiesAction',['view',$company->id])}}" >
                                {{$company->owner_name}} @if($company->persons()->where('type',2)->count() > 0) ({{$company->persons()->where('type',2)->count()}}) @endif
                                </a>
                            </td>
                            <!--<td>{{$company->owner_designation}}</td>-->
                            <td>{{$company->owner_mobile}}</td>
                            <!--<td>{{$company->owner_email}}</td>-->
                            <!--<td>{{$company->key_parson_name}}</td>-->
                            <!--<td>{{$company->key_parson_designation}}</td>-->
                            <!--<td>{{$company->key_parson_mobile}}</td>-->
                            <!--<td>{{$company->key_parson_email}}</td>-->
                            <td>{{$company->fullAddress()}}</td>
                            <!--<td>{{$company->customer_status}}</td>-->
                            <!--<td>{{$company->company_category}}</td>-->
                            <!--<td>{{$company->company_status}}</td>-->
                            <!--<td>{{$company->machine_quantity}}</td>-->
                            <!--<td>{{$company->brand_name}}</td>-->
                            <!--<td>{{$company->number_of_employee}}</td>-->
                            <!--<td>{{$company->next_visit_day}} Days / {{Carbon\Carbon::parse($company->next_visit_date)->format('d-m-Y')}}</td>-->
                            <!--<td>{{$company->requirement}}</td>-->
                            <!--<td>{{$company->remarks}}</td>-->
                            <td>{{$company->created_at->format('d-m-Y')}}</td>
                            <td class="center">
                                @isset(json_decode(Auth::user()->permission->permission, true)['company']['add'])
                                <a href="{{route('admin.companiesAction',['edit',$company->id])}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @endisset

                                @isset(json_decode(Auth::user()->permission->permission, true)['company']['delete'])
                                <a href="{{route('admin.companiesAction',['delete',$company->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                                @endisset
                            </td>
                            <td>
                                 @if($company->user)
                                <span>
                                    <a href="{{route('admin.usersCustomerAction',['view',$company->user->id])}}" >{{$company->user->name}}</a>
                                    <!--<br><b>C:</b> {{$company->user->companies->count()}}, <b>E:</b> {{$company->user->engineers->count()}}, <b>S:</b> {{$company->user->sales->count()}}-->
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$companies->links('pagination')}}
            </div>
        </form>


    </div>
</div>
</div>

<!-- Add Modal -->
 <div class="modal fade text-left" id="AddCompany" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.companiesAction','create')}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Customer</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="name">Name* </label>
                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" required="">
    				@if ($errors->has('name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
    				@endif
             	</div>
             	<div class="form-group">
    			    <label for="short_name">Short Name <small>(Must Be Unique)</small> </label>
                    <input type="text" class="form-control {{$errors->has('short_name')?'error':''}}" value="{{old('short_name')}}" name="short_name" placeholder="Enter Short Name">
    				@if ($errors->has('short_name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('short_name') }}</p>
    				@endif
             	</div>
    			 <div class="form-group">
    				<label for="name">Address</label>
					<textarea name="address" class="form-control {{$errors->has('address')?'error':''}}" placeholder="Enter Address"></textarea>
					@if ($errors->has('address'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('address') }}</p>
					@endif
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Customer</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>

<!--Edit Modal -->
@foreach($companies as $i=>$dpm)
 <div class="modal fade text-left" id="EditCompany_{{$dpm->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.companiesAction',['update',$dpm->id])}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Customer</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="name">Name* </label>
                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" value="{{$dpm->name?:old('name')}}" name="name" placeholder="Enter Name" required="">
    				@if ($errors->has('name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
    				@endif
             	</div>
             	<div class="form-group">
    			    <label for="short_name">Short Name <small>(Must Be Unique | Min:3 letter)</small> </label>
                    <input type="text" class="form-control {{$errors->has('short_name')?'error':''}}" value="{{$dpm->slug?:old('short_name')}}" name="short_name" placeholder="Enter Short Name">
    				@if ($errors->has('short_name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('short_name') }}</p>
    				@endif
             	</div>
    			 <div class="form-group">
    				<label for="name">Address</label>
					<textarea name="address" class="form-control {{$errors->has('address')?'error':''}}" placeholder="Enter Description">{!!$dpm->description!!}</textarea>
					@if ($errors->has('address'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('address') }}</p>
					@endif
             	</div>
             	<div class="row">
                 	<div class="col-md-6 form-group">
                 	    <label for="name">Status</label><br>
                 	    <div class="checkbox">
                             <input class="inp-cbx" id="status_{{$dpm->id}}" type="checkbox" name="status" style="display: none;" {{$dpm->status=='active'?'checked':''}} />
                             <label class="cbx" for="status_{{$dpm->id}}">
                                 <span>
                                     <svg width="12px" height="10px" viewbox="0 0 12 10">
                                         <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                     </svg>
                                 </span>
                                 Active
                             </label>
                         </div>
                 	</div>
                    <div class="col-md-6 form-group">
                        <label for="name">Publish Date*</label>
                        <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{$dpm->created_at->format('Y-m-d')}}" name="created_at" required="">
                        @if ($errors->has('created_at'))
    					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
    					@endif
                    </div>
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Customer</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>
@endforeach



@endsection @push('js') @endpush
