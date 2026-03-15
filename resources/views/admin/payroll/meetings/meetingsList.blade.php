@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Meetings List')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">


<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Meetings List</h3>
         <div class="dropdown">
             @isset(json_decode(Auth::user()->permission->permission, true)['meetings']['add'])
             <!--<a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddDesignations" style="padding:5px 15px;">-->
             <!--    <i class="bx bx-plus"></i> Meeting-->
             <!--</a>-->
             @endisset
             <a href="{{route('admin.meetings')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.meetings')}}">
            <div class="row">
                <div class="col-md-6 mb-1">
                    <div class="input-group">
                        <input type="date" name="startDate" value="{{request()->startDate}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                        <input type="date" value="{{request()->endDate}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                    </div>
                </div>
                <div class="col-md-6 mb-0">
                    <div class="input-group">
                        <input type="text" name="search" value="{{request()->search}}" placeholder="Search Meating" class="form-control {{$errors->has('search')?'error':''}}" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <form action="{{route('admin.meetings')}}">
            <div class="row">
                <div class="col-md-4">
                    <!--<div class="input-group mb-1">-->
                    <!--    <select class="form-control form-control-sm rounded-0" name="action" required="">-->
                    <!--        <option value="">Select Action</option>-->
                    <!--        <option value="Scheduled">Scheduled</option>-->
                    <!--        <option value="In progress">In progress</option>-->
                    <!--        <option value="Completed">Completed</option>-->
                    <!--        <option value="Canceled">Canceled</option>-->
                    <!--        <option value="Rescheduled">Rescheduled</option>-->
                    <!--        <option value="5">Delete</option>-->
                    <!--    </select>-->
                    <!--    <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>-->
                    <!--</div>-->
                </div>
                <div class="col-md-8">
                    <ul class="statuslist mb-0">
                        <li><a href="{{route('admin.meetings')}}">All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.meetings',['status'=>'Scheduled'])}}">Scheduled ({{$totals->scheduled}})</a></li>
                        <li><a href="{{route('admin.meetings',['status'=>'In progress'])}}">In progress ({{$totals->progress}})</a></li>
                        <li><a href="{{route('admin.meetings',['status'=>'Completed'])}}">Completed ({{$totals->completed}})</a></li>
                        <li><a href="{{route('admin.meetings',['status'=>'Canceled'])}}">Canceled ({{$totals->canceled}})</a></li>
                        <li><a href="{{route('admin.meetings',['status'=>'Rescheduled'])}}">Rescheduled ({{$totals->rescheduled}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 80px;width: 80px;padding-right:0;">
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
                            <th style="min-width: 150px;">Meeting Title</th>
                            <th style="min-width: 200px;">Participants</th>
                            <th style="min-width: 120px;">Date & Time</th>
                            <th style="min-width: 120px;">Type</th>
                            <th style="min-width: 120px;">Status</th>
                            <th style="min-width: 100px;">Host</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($meetings as $i=>$meeting)
                        <tr>
                            <td style="position: relative;">
                                <!--<div class="checkbox">-->
                                <!--     <input class="inp-cbx" id="cbx_{{$meeting->id}}" type="checkbox" name="checkid[]" value="{{$meeting->id}}" style="display: none;" />-->
                                <!--     <label class="cbx" for="cbx_{{$meeting->id}}">-->
                                <!--         <span>-->
                                <!--             <svg width="12px" height="10px" viewbox="0 0 12 10">-->
                                <!--                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>-->
                                <!--             </svg>-->
                                <!--         </span>-->
                                <!--     </label>-->
                                <!-- </div>-->
                                <span style="margin:0 5px;">{{$meetings->currentpage()==1?$i+1:$i+($meetings->perpage()*($meetings->currentpage() - 1))+1}}</span>
                            </td>
                            <td>{{$meeting->name}}</td>
                            <td>
                                @foreach($meeting->participantsUsers()->get() as $user)
                                @if($meeting->type==1)
                                <span>{{$user->name}} - {{$user->email}}</span>
                                @else
                                <span>{{$user->factory_name}} - {{$user->owner_name}}</span>
                                @endif
                                @endforeach
                            </td>
                            <td>{{$meeting->created_at->format('d-m-Y h:i A')}}</td>
                            <td>{{ucfirst($meeting->meeting_type)}}</td>
                            <td>
                                @if($meeting->status=='In progress')
                                <span class="badge" style="background: #ff108c;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                @elseif($meeting->status=='Completed')
                                <span class="badge" style="background: #13c238;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                @elseif($meeting->status=='Canceled')
                                <span class="badge" style="background: #ff2e37;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                @elseif($meeting->status=='Rescheduled')
                                <span class="badge" style="background: #f326eb;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                @else
                                <span class="badge" style="background: #2c66cb;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                @endif
                            </td>
                            <td>{{$meeting->hostUser?$meeting->hostUser->name:'Not Found'}}</td>
                            <td class="center">
                                @isset(json_decode(Auth::user()->permission->permission, true)['meetings']['add'])
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditDesignations_{{$meeting->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @endisset

                                @isset(json_decode(Auth::user()->permission->permission, true)['meetings']['delete'])
                                <a href="{{route('admin.meetingsAction',['delete',$meeting->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                                @endisset
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$meetings->links('pagination')}}
            </div>
        </form>


    </div>
</div>
</div>

<!-- Add Modal -->
 <div class="modal fade text-left" id="AddDesignations" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.meetingsAction','create')}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Meeting</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="name">Participants* </label>
                    <select class="select2" multiple="multiple" data-placeholder="Select Company" name="company[]">
                        @foreach($companies as $company)
                        <option value="{{$company->id}}">{{$company->factory_name}}</option>
                        @endforeach
                    </select>
    				@if ($errors->has('company'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('company') }}</p>
    				@endif
             	</div>
    	   		<div class="form-group">
    			    <label for="host">Host Person*</label>
                    <select class="select22" data-placeholder="Select Host" name="host">
                        @foreach($users as $user)
                        <option value="{{$user->id}}" {{Auth::id()==$user->id?'selected':''}}>{{$user->name}}</option>
                        @endforeach
                    </select>
    				@if ($errors->has('host'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('host') }}</p>
    				@endif
             	</div>
    	   		<div class="form-group">
    			    <label for="name">Title/Subject* </label>
                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Title/Subject" required="">
    				@if ($errors->has('name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
    				@endif
             	</div>
    	   		<div class="form-group">
    			    <label for="date_time">Date & Time* </label>
                    <input type="datetime-local" class="form-control {{$errors->has('date_time')?'error':''}}" name="date_time" required="">
    				@if ($errors->has('date_time'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('date_time') }}</p>
    				@endif
             	</div>
    	   		<div class="form-group">
    			    <label for="location">Location* </label>
                    <input type="text" class="form-control {{$errors->has('location')?'error':''}}" name="location" placeholder="(physical or virtual link)" required="">
    				@if ($errors->has('location'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('location') }}</p>
    				@endif
             	</div>
             	<div class="row">
        	   		<div class="col-md-6 form-group">
        			    <label for="meeting_type">Meeting Type* </label>
                        <select class="form-control" name="meeting_type">
                            <option value="">Select Type</option>
                            <option value="In-person">In-person</option>
                            <option value="Zoom">Zoom</option>
                            <option value="Google Meet">Google Meet</option>
                            <option value="Phone">Phone</option>
                        </select>
        				@if ($errors->has('meeting_type'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('meeting_type') }}</p>
        				@endif
                 	</div>
        	   		<div class="col-md-6 form-group">
        			    <label for="status">Status* </label>
                        <select class="form-control" name="status">
                            <option value="">Select Type</option>
                            <option value="Scheduled">Scheduled</option>
                            <option value="In progress">In progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Canceled">Canceled</option>
                            <option value="Rescheduled">Rescheduled</option>
                        </select>
        				@if ($errors->has('status'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
        				@endif
                 	</div>
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
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Meeting</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>

<!--Edit Modal -->
@foreach($meetings as $i=>$data)
 <div class="modal fade text-left" id="EditDesignations_{{$data->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.meetingsAction',['update',$data->id])}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Meeting</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="name">Participants* </label>
                    <select class="select2" multiple="multiple" data-placeholder="Select Company" name="company[]">
                        @foreach($companies as $company)
                        <option value="{{$company->id}}"  {{ in_array($company->id, old('company', $data->participantsIDs())) ? 'selected' : '' }}>{{$company->factory_name}}</option>
                        @endforeach
                    </select>
    				@if ($errors->has('company'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('company') }}</p>
    				@endif
             	</div>
    	   		<div class="form-group">
    			    <label for="host">Host Person*</label>
                    <select class="select22_{{$data->id}}" data-placeholder="Select Host" name="host">
                        @foreach($users as $user)
                        <option value="{{$user->id}}" {{$data->host_id==$user->id?'selected':''}}>{{$user->name}}</option>
                        @endforeach
                    </select>
    				@if ($errors->has('host'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('host') }}</p>
    				@endif
             	</div>
    	   		<div class="form-group">
    			    <label for="name">Title/Subject* </label>
                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" value="{{old('name',$data->name)}}" placeholder="Enter Title/Subject" required="">
    				@if ($errors->has('name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
    				@endif
             	</div>
    	   		<div class="form-group">
    			    <label for="date_time">Date & Time* </label>
                    <input type="datetime-local" class="form-control {{$errors->has('date_time')?'error':''}}" value="{{ old('date_time', $data->created_at->format('Y-m-d\TH:i')) }}"  name="date_time" required="">
    				@if ($errors->has('date_time'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('date_time') }}</p>
    				@endif
             	</div>
    	   		<div class="form-group">
    			    <label for="location">Location* </label>
                    <input type="text" class="form-control {{$errors->has('location')?'error':''}}" name="location" value="{{old('location',$data->location)}}" placeholder="(physical or virtual link)" required="">
    				@if ($errors->has('location'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('location') }}</p>
    				@endif
             	</div>
             	<div class="row">
        	   		<div class="col-md-6 form-group">
        			    <label for="meeting_type">Meeting Type* </label>
                        <select class="form-control" name="meeting_type">
                            <option value="">Select Type</option>
                            <option value="In-person" {{ old('meeting_type', $data->meeting_type ?? '') == 'In-person' ? 'selected' : '' }} >In-person</option>
                            <option value="Zoom" {{ old('meeting_type', $data->meeting_type ?? '') == 'Zoom' ? 'selected' : '' }}>Zoom</option>
                            <option value="Google Meet" {{ old('meeting_type', $data->meeting_type ?? '') == 'Google Meet' ? 'selected' : '' }}>Google Meet</option>
                            <option value="Phone" {{ old('meeting_type', $data->meeting_type ?? '') == 'Phone' ? 'selected' : '' }}>Phone</option>
                        </select>
        				@if ($errors->has('meeting_type'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('meeting_type') }}</p>
        				@endif
                 	</div>
        	   		<div class="col-md-6 form-group">
        			    <label for="status">Status* </label>
                        <select class="form-control" name="status">
                            <option value="">Select Type</option>
                            <option value="Scheduled" {{ old('status', $data->status ?? '') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="In progress" {{ old('status', $data->status ?? '') == 'In progress' ? 'selected' : '' }}>In progress</option>
                            <option value="Completed" {{ old('status', $data->status ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Canceled" {{ old('status', $data->status ?? '') == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                            <option value="Rescheduled" {{ old('status', $data->status ?? '') == 'Rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                        </select>
        				@if ($errors->has('status'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
        				@endif
                 	</div>
             	</div>
    			 <div class="form-group">
    				<label for="name">Description</label>
					<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{{old('description', $data->description ?? '')}}</textarea>
					@if ($errors->has('description'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
					@endif
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Meeting</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>
@endforeach



@endsection
@push('js')

<script>
    $(document).ready(function(){

        $('.select2').select2({
            placeholder: $('.select2').data('placeholder'),
            width: '100%',
            allowClear: true
        });

        $('.select22').select2({
             dropdownParent: $('#AddDesignations'),
            placeholder: $('.select22').data('placeholder'),
            width: '100%',
            allowClear: true
        });

        @foreach($meetings as $i=>$data)
        $('.select22_{{$data->id}}').select2({
             dropdownParent: $('#EditDesignations_{{$data->id}}'),
            placeholder: $('.select22_{{$data->id}}').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        @endforeach

    });
</script>

@endpush

