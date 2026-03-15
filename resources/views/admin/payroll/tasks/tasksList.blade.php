@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Tasks List')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">


<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Tasks List</h3>
         <div class="dropdown">
             @isset(json_decode(Auth::user()->permission->permission, true)['tasks']['add'])
             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddDesignations" style="padding:5px 15px;">
                 <i class="bx bx-plus"></i> Task
             </a>
             @endisset

             <a href="{{route('admin.tasks')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.tasks')}}">
            <div class="row">
                <div class="col-md-6 mb-1">
                    <div class="input-group">
                        <input type="date" name="startDate" value="{{request()->startDate}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                        <input type="date" value="{{request()->endDate}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                    </div>
                </div>
                <div class="col-md-6 mb-0">
                    <div class="input-group">
                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Task" class="form-control {{$errors->has('search')?'error':''}}" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <form action="{{route('admin.tasks')}}">
            <div class="row">
                <div class="col-md-4">
                    <!--<div class="input-group mb-1">-->
                    <!--    <select class="form-control form-control-sm rounded-0" name="action" required="">-->
                    <!--        <option value="">Select Action</option>-->
                    <!--        <option value="1">Pending</option>-->
                    <!--        <option value="2">In progress</option>-->
                    <!--        <option value="3">Review</option>-->
                    <!--        <option value="4">Completed</option>-->
                    <!--        <option value="5">On Hold</option>-->
                    <!--        <option value="6">Canceled</option>-->
                    <!--        <option value="7">Delete</option>-->
                    <!--    </select>-->
                    <!--    <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>-->
                    <!--</div>-->
                </div>
                <div class="col-md-8">
                    <ul class="statuslist mb-0">
                        <li><a href="{{route('admin.tasks')}}">All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.tasks',['status'=>'pending'])}}">Pending ({{$totals->pending}})</a></li>
                        <li><a href="{{route('admin.tasks',['status'=>'in progress'])}}">In progress ({{$totals->progress}})</a></li>
                        <li><a href="{{route('admin.tasks',['status'=>'review'])}}">Review ({{$totals->review}})</a></li>
                        <li><a href="{{route('admin.tasks',['status'=>'completed'])}}">Completed ({{$totals->completed}})</a></li>
                        <li><a href="{{route('admin.tasks',['status'=>'on hold'])}}">On Hold ({{$totals->hold}})</a></li>
                        <li><a href="{{route('admin.tasks',['status'=>'Canceled'])}}">Canceled ({{$totals->canceled}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
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
                            <th style="min-width: 200px;">Task Name</th>
                            <th style="min-width: 100px;">Assinee</th>
                            <th style="min-width: 100px;">Company</th>
                            <th style="min-width: 100px;">Priority</th>
                            <th style="min-width: 100px;">Status</th>
                            <th style="min-width: 120px;">Assinee Date</th>
                            <th style="min-width: 120px;">Due Date</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $i=>$task)
                        <tr>
                            <td>
                                <!--<div class="checkbox">-->
                                <!--     <input class="inp-cbx" id="cbx_{{$task->id}}" type="checkbox" name="checkid[]" value="{{$task->id}}" style="display: none;" />-->
                                <!--     <label class="cbx" for="cbx_{{$task->id}}">-->
                                <!--         <span>-->
                                <!--             <svg width="12px" height="10px" viewbox="0 0 12 10">-->
                                <!--                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>-->
                                <!--             </svg>-->
                                <!--         </span>-->
                                <!--     </label>-->
                                <!-- </div>-->
                                <span style="margin:0 5px;">{{$tasks->currentpage()==1?$i+1:$i+($tasks->perpage()*($tasks->currentpage() - 1))+1}}</span>
                            </td>
                            <td>{{$task->name}}</td>
                            <td>{{$task->assinee?$task->assinee->name:''}}</td>
                            <td>
                                @if($task->type==1)
                                <span>{{$task->company?$task->company->name:''}}</span>
                                @else
                                {{$task->company?$task->company->factory_name:''}}
                                @endif
                            </td>
                            <td>{{ucfirst($task->priority)}}</td>
                            <td>
                                @if($task->status=='in progress')
                                <span class="badge" style="background: #ff108c;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                @elseif($task->status=='review')
                                <span class="badge" style="background: #d5ab05;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                @elseif($task->status=='completed')
                                <span class="badge" style="background: #13c238;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                @elseif($task->status=='canceled')
                                <span class="badge" style="background: #ff2e37;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                @elseif($task->status=='on hold')
                                <span class="badge" style="background: #f326eb;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                @else
                                <span class="badge" style="background: #2c66cb;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                @endif
                            </td>
                            <td>{{$task->created_at->format('d-m-Y')}}</td>
                            <td>{{$task->due_date?Carbon\Carbon::parse($task->due_date)->format('d-m-Y'):''}}</td>
                            <td class="center">
                                @isset(json_decode(Auth::user()->permission->permission, true)['tasks']['add'])
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditDesignations_{{$task->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @endisset

                                @isset(json_decode(Auth::user()->permission->permission, true)['tasks']['delete'])
                                <a href="{{route('admin.tasksAction',['delete',$task->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                                @endisset
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$tasks->links('pagination')}}
            </div>
        </form>


    </div>
</div>
</div>

<!-- Add Modal -->
 <div class="modal fade text-left" id="AddDesignations" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.tasksAction','create')}}" method="post" enctype="multipart/form-data">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Task</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="name">Task name* </label>
                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" required="">
    				@if ($errors->has('name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
    				@endif
             	</div>
             	<div class="row">
        	   		<div class="col-md-6 form-group">
        			    <label for="host">Assignee*</label>
                        <select class="select2" data-placeholder="Select Assignee" name="assignee" required="">
                            <option></option>
                            @foreach($users as $user)
                            <option value="{{$user->id}}" >{{$user->name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('assignee'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('assignee') }}</p>
        				@endif
                 	</div>
        	   		<div class="col-md-6 form-group">
        			    <label for="host">Company</label>
                        <select class="select22" data-placeholder="Select Company" name="company">
                            <option></option>
                            @foreach($companies as $company)
                            <option value="{{$company->id}}" >{{$company->factory_name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('company'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('company') }}</p>
        				@endif
                 	</div>
                 	<div class="col-md-6 form-group">
                 	    <label for="priority">Task Priority*</label>
                 	    <select class="form-control" name="priority" required="">
                 	        <option value="">Select Priority</option>
                 	        <option value="Highest">Highest</option>
                 	        <option value="High">High</option>
                 	        <option value="Normal">Normal</option>
                 	        <option value="Low">Low</option>
                 	        <option value="Lowest">Lowest</option>
                 	    </select>
                 	</div>
                 	<div class="col-md-6 form-group">
                 	    <label for="due_date">Due Date*</label>
                 	    <input type="date" class="form-control {{$errors->has('due_date')?'error':''}}" name="due_date" required="">
                 	</div>
             	</div>
    			 <div class="form-group">
    				<label for="name">Description</label>
					<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description"></textarea>
					@if ($errors->has('description'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
					@endif
             	</div>
             	<div class="form-group">
    				<label for="name">Attachment</label>
    			    <input type="file" name="attachment" class="form-control" accept="image/*" style="padding: 3px;">
    			</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Task</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>

<!--Edit Modal -->
@foreach($tasks as $i=>$data)
 <div class="modal fade text-left" id="EditDesignations_{{$data->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.tasksAction',['update',$data->id])}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Task</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="name">Task name* </label>
                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" value="{{old('name',$data->name)}}" placeholder="Enter Name" required="">
    				@if ($errors->has('name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
    				@endif
             	</div>
             	<div class="row">
        	   		<div class="col-md-6 form-group">
        			    <label for="host">Assignee*</label>
                        <select class="select2_{{$data->id}}" data-placeholder="Select Assignee" name="assignee" required="">
                            <option></option>
                            @foreach($users as $user)
                            <option value="{{$user->id}}" {{ old('assignee', $data->assignby_id ?? '') == $user->id ? 'selected' : '' }} >{{$user->name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('assignee'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('assignee') }}</p>
        				@endif
                 	</div>
        	   		<div class="col-md-6 form-group">
        			    <label for="host">Company</label>
                        <select class="select22_{{$data->id}}" data-placeholder="Select Company" name="company">
                            <option></option>
                            @foreach($companies as $company)
                            <option value="{{$company->id}}" {{ old('company', $data->src_id ?? '') == $company->id ? 'selected' : '' }} >{{$company->factory_name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('company'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('company') }}</p>
        				@endif
                 	</div>
                 	<div class="col-md-6 form-group">
                 	    <label for="priority">Task Priority*</label>
                 	    <select class="form-control" name="priority" required="">
                 	        <option value="">Select Priority</option>
                 	        <option value="Highest" {{ old('priority', $data->priority ?? '') == 'Highest' ? 'selected' : '' }} >Highest</option>
                 	        <option value="High" {{ old('priority', $data->priority ?? '') == 'High' ? 'selected' : '' }} >High</option>
                 	        <option value="Normal" {{ old('priority', $data->priority ?? '') == 'Normal' ? 'selected' : '' }} >Normal</option>
                 	        <option value="Low" {{ old('priority', $data->priority ?? '') == 'Low' ? 'selected' : '' }} >Low</option>
                 	        <option value="Lowest" {{ old('priority', $data->priority ?? '') == 'Lowest' ? 'selected' : '' }}>Lowest</option>
                 	    </select>
                 	</div>
                 	<div class="col-md-6 form-group">
                 	    <label for="due_date">Due Date*</label>
                 	    <input type="date" class="form-control {{$errors->has('due_date')?'error':''}}" value="{{ old('due_date', Carbon\Carbon::parse($data->due_date)->format('Y-m-d')) }}" name="due_date" required="">
                 	</div>
             	</div>
    			 <div class="form-group">
    				<label for="name">Description</label>
					<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{{old('description', $data->description ?? '')}}</textarea>
					@if ($errors->has('description'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
					@endif
             	</div>
             	<div class="row">
                 	<div class="col-md-7 form-group">
        				<label for="name">Attachment</label>
        			    <input type="file" name="attachment" class="form-control" accept="image/*" style="padding: 3px;">
        			</div>
                 	<div class="col-md-5 form-group">
                 	    @if($data->imageFile)
        				<img src="{{asset($data->image())}}" alt="image" style="max-height: 80px;" />
        				@endif
        			</div>
             	</div>
             	<div class="row">
             	    <div class="col-md-6 form-group">
        			    <label for="status">Status* </label>
                        <select class="form-control" name="status">
                            <option value="">Select Type</option>
                            <option value="pending" {{ old('status', $data->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in progress" {{ old('status', $data->status ?? '') == 'in progress' ? 'selected' : '' }}>In progress</option>
                            <option value="review" {{ old('status', $data->status ?? '') == 'review' ? 'selected' : '' }}>Review</option>
                            <option value="completed" {{ old('status', $data->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="on hold" {{ old('status', $data->status ?? '') == 'on hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="canceled" {{ old('status', $data->status ?? '') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                        </select>
        				@if ($errors->has('status'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
        				@endif
                 	</div>
                 	<div class="col-md-6 form-group">
                 	    <label for="assinee_date">Assinee Date*</label>
                 	    <input type="date" class="form-control {{$errors->has('assinee_date')?'error':''}}" value="{{ old('assinee_date', $data->created_at->format('Y-m-d')) }}" name="assinee_date" required="">
                 	</div>
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Task</button>
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
            dropdownParent: $('#AddDesignations'),
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

        @foreach($tasks as $i=>$data)
        $('.select2_{{$data->id}}').select2({
            dropdownParent: $('#EditDesignations_{{$data->id}}'),
            placeholder: $('.select2_{{$data->id}}').data('placeholder'),
            width: '100%',
            allowClear: true
        });
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

