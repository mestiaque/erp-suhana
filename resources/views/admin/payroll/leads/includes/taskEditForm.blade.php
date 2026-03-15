<form action="{{route('admin.leadsAction',['task-update',$lead->id])}}"  method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" value="{{$task->id}}" name="task_id">
    <div class="form-group">
	    <label for="name">Task name* </label>
        <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" value="{{$task->name}}" placeholder="Enter Name" required="">
		@if ($errors->has('name'))
		<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
		@endif
 	</div>
 	<div class="row">
        <div class="col-md-6 form-group">
     	    <label for="priority">Task Priority*</label>
     	    <select class="form-control" name="priority" required="">
     	        <option value="">Select Priority</option>
     	        <option value="Highest" {{$task->priority=='Highest'?'selected':''}} >Highest</option>
     	        <option value="High" {{$task->priority=='High'?'selected':''}} >High</option>
     	        <option value="Normal" {{$task->priority=='Normal'?'selected':''}} >Normal</option>
     	        <option value="Low" {{$task->priority=='Low'?'selected':''}} >Low</option>
     	        <option value="Lowest" {{$task->priority=='Lowest'?'selected':''}} >Lowest</option>
     	    </select>
     	</div>
     	<div class="col-md-6 form-group">
     	    <label for="due_date">Due Date*</label>
     	    <input type="date" class="form-control {{$errors->has('due_date')?'error':''}}" value="{{$task->created_at->format('Y-m-d')}}" name="due_date" required="">
     	</div>
 	</div>
    <div class="form-group">
		<label for="name">Description</label>
		<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{{$task->description}}</textarea>
		@if ($errors->has('description'))
		<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
		@endif
 	</div>
 	<div class="row">
     	<div class="col-md-8 form-group">
    		<label for="name"> Attachment</label>
    	    <input type="file" name="attachment" class="form-control" accept="image/*" style="padding: 3px;">
    	</div>
 	    <div class="col-md-4">
 	        <img src="{{asset($task->image())}}" style="max-height:60px;" />
 	    </div>
 	</div>
 	<div class="row">
 	    <div class="col-md-6 form-group">
		    <label for="status">Status* </label>
            <select class="form-control" name="status">
                <option value="">Select Type</option>
                <option value="pending" {{ old('status', $task->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in progress" {{ old('status', $task->status ?? '') == 'in progress' ? 'selected' : '' }}>In progress</option>
                <option value="review" {{ old('status', $task->status ?? '') == 'review' ? 'selected' : '' }}>Review</option>
                <option value="completed" {{ old('status', $task->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="on hold" {{ old('status', $task->status ?? '') == 'on hold' ? 'selected' : '' }}>On Hold</option>
                <option value="canceled" {{ old('status', $task->status ?? '') == 'canceled' ? 'selected' : '' }}>Canceled</option>
            </select>
			@if ($errors->has('status'))
			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
			@endif
     	</div>
     	<div class="col-md-6 form-group">
     	    <label for="assinee_date">Assinee Date*</label>
     	    <input type="date" class="form-control {{$errors->has('assinee_date')?'error':''}}" value="{{ old('assinee_date', $task->created_at->format('Y-m-d')) }}" name="assinee_date" required="">
     	</div>
 	</div>
    <button type="submit" class="btn btn-success">Submit</button>
</form>