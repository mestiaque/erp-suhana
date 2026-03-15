@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Leads List')}}</title>
@endsection @push('css')
<style type="text/css">

</style>
@endpush @section('contents')

<div class="flex-grow-1">

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Leads List</h3>
         <div class="dropdown">
             <a href="{{ route('admin.leads', [
            'export' => 'report',
            'concern' => request()->concern,
            'district' => request()->district,
            'city' => request()->city,
            'employee' => request()->employee,
            'startDate' => request()->startDate,
            'endDate' => request()->endDate,
            'search' => request()->search]) }}" class="btn-custom yellow">
                <i class="bx bx-export"></i> Export
             </a>
             @isset(json_decode(Auth::user()->permission->permission, true)['leads']['add'])
             <a href="{{route('admin.leadsAction','create')}}" class="btn-custom primary"  style="padding:5px 15px;">
                 <i class="bx bx-plus"></i> Lead
             </a>
             @endisset
             <a href="{{route('admin.leads')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.leads')}}">
            <div class="row">
                <div class="col-md-3 mb-1">
                     <select class="form-control" name="concern">
                        <option value="" >Select Sister Concern</option>
                        <option value="MMC" {{request()->concern=='MMC'?'selected':''}}  >MG Machineries Corporation (MMC)</option>
                        <option value="EMC" {{request()->concern=='EMC'?'selected':''}} >Embroidery Machine Corporation (EMC)</option>
                        <option value="MTCI" {{request()->concern=='MTCI'?'selected':''}} >MG Training Centre Institute (MTCI)</option>
                        <option value="FLCD" {{request()->concern=='FLCD'?'selected':''}} >Fiber Laser Cutting Division (FLCD)</option>
                     </select>
                </div>
                <div class="col-md-3 mb-1">
                    <select id="district" class="form-control {{$errors->has('district')?'error':''}}" name="district">
                        <option value="">Select District</option>
                        @foreach(App\Models\Country::where('type',3)->get() as $data)
                        <option value="{{$data->id}}" {{$data->id==request()->district?'selected':''}}>{{$data->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-1">
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

                <div class="col-md-3 mb-1">
                    <select  class="form-control" name="employee">
                        <option value="">Select Employee</option>
                        @foreach(App\Models\User::where('admin',true)->where('status',1)->get() as $data)
                        <option value="{{$data->id}}" {{$data->id==request()->employee?'selected':''}}>{{$data->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-1">
                    <select  class="form-control" name="status">
                        <option value="">Select Status</option>
                        <option value="Not Potential" {{'Not Potential'==request()->status?'selected':''}}>Not Potential</option>
                        <option value="Potential" {{'Potential'==request()->status?'selected':''}}>Potential</option>
                        <option value="Very Potential" {{'Very Potential'==request()->status?'selected':''}}>Very Potential</option>
                        <option value="next-call" {{'next-call'==request()->status?'selected':''}}>Next Call</option>
                    </select>
                </div>
                <div class="col-md-5 mb-1">
                    <div class="input-group">
                        <input type="date" name="startDate" value="{{request()->startDate}}" class="form-control" />
                        <input type="date" value="{{request()->endDate}}" name="endDate" class="form-control" />
                    </div>
                </div>
                <div class="col-md-4 mb-1">
                    <div class="input-group">
                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Company name, Owner name, mobile" class="form-control" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <form action="{{route('admin.leads')}}">
            <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-10">
                    <ul class="statuslist mb-0">
                        <li><a href="{{route('admin.leads',['status'=>'all'])}}">All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.leads',['status'=>'Not Potential'])}}">Not Potential ({{$totals->nonPotential}})</a></li>
                        <li><a href="{{route('admin.leads',['status'=>'Potential'])}}">Potential ({{$totals->potential}})</a></li>
                        <li><a href="{{route('admin.leads',['status'=>'Very Potential'])}}">Very Potential ({{$totals->veryPotential}})</a></li>
                        <!--<li><a href="{{route('admin.leads',['status'=>'next-call'])}}">Next Call ({{$totals->todayOrUpcomingVisit}})</a></li>-->
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 70px;width: 70px;padding-right:0;">
                                SL
                            </th>
                            <th style="min-width: 130px;">Customer</th>
                            <th style="min-width: 130px;">Company</th>
                            <th style="min-width: 100px;">Mobile</th>
                            <th style="min-width: 180px;">Address</th>
                            <th style="min-width: 100px;">Cus. Status</th>
                            <th style="min-width: 100px;">Next Date</th>
                            <th style="min-width: 100px;">Lead Type</th>
                            <th style="min-width: 130px;">Product</th>
                            <th style="min-width: 130px;">Remarks</th>
                            <th style="min-width: 100px;">Employee</th>
                            <th style="min-width: 100px;">Date</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leads as $i=>$lead)
                        <tr>
                            <td>
                                <span style="margin:0 5px;">{{$leads->currentpage()==1?$i+1:$i+($leads->perpage()*($leads->currentpage() - 1))+1}}</span>
                                <br><b>{{$lead->concernShort()}}</b>
                            </td>
                            <td>
                               <a href="{{route('admin.leadsAction',['view',$lead->id])}}" >  {{$lead->name}} </a>
                            </td>
                            <td>{{$lead->factory_name}}</td>
                            <td>{{$lead->mobile}}</td>
                            <td>{{$lead->fullAddress()}}</td>



                            <td>

                                @if($lead->customer_status=='Not Potential')
                                <span class="badge" style="background: #9baaff;font-size: 14px;color: white;" >Not Potential</span>
                                @elseif($lead->customer_status=='Potential')
                                <span class="badge" style="background: #5970f3;font-size: 14px;color: white;" >Potential</span>
                                @elseif($lead->customer_status=='Very Potential')
                                <span class="badge" style="background: #0829e5;font-size: 14px;color: white;" >Very Potential</span>
                                @endif
                            </td>
                             <td>
                                <span style="color: {{
                                        ($nextVisit = $lead->next_visit_day ? \Carbon\Carbon::parse($lead->next_visit_day) : null)
                                        && \Carbon\Carbon::today()->gte($nextVisit->copy()->subDay(2)) ? 'red' : 'black'
                                    }}">
                                    {{ $nextVisit ? $nextVisit->format('d-m-Y') : '' }}
                                </span>
                            </td>
                            <td>{{$lead->source}}</td>
                            <td>
                                {{ $lead->services()->pluck('name')->join(', ') }}
                            </td>
                            <td>{{$lead->requirement}}</td>
                            <td>
                                @if($lead->assineeUser)
                                <a href="{{route('admin.usersCustomerAction',['view',$lead->assineeUser->id])}}" >{{$lead->assineeUser->name}}</a>
                                @endif
                            </td>
                            <td>{{$lead->created_at->format('d-m-Y')}}</td>
                            <td class="center">
                                @isset(json_decode(Auth::user()->permission->permission, true)['leads']['add'])
                                <a href="{{route('admin.leadsAction',['view',$lead->id])}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @endisset

                                @isset(json_decode(Auth::user()->permission->permission, true)['leads']['delete'])
                                <a href="{{route('admin.leadsAction',['delete',$lead->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                                @endisset
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$leads->links('pagination')}}
            </div>
        </form>


    </div>
</div>
</div>

<!-- Add Modal -->
 <div class="modal fade text-left" id="AddDesignations" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.leadsAction','create')}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Lead</h4>
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
    				<label for="name">Description</label>
					<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description"></textarea>
					@if ($errors->has('description'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
					@endif
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Designations</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>

<!--Edit Modal -->
@foreach($leads as $i=>$dpm)
 <div class="modal fade text-left" id="EditDesignations_{{$dpm->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.leadsAction',['update',$dpm->id])}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Lead</h4>
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
    				<label for="name">Description</label>
					<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{!!$dpm->description!!}</textarea>
					@if ($errors->has('description'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
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
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Designations</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>
@endforeach



@endsection @push('js') @endpush

