<form action="{{route('admin.leadsAction',['task',$lead->id])}}"  method="post" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
	    <label for="name">Task name* </label>
        <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" required="">
		@if ($errors->has('name'))
		<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
		@endif
 	</div>
 	<div class="row">
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
		<label for="name">Attachment (Image)</label>
	    <input type="file" name="attachment" class="form-control" accept="image/*" style="padding: 3px;">
	</div>
    <button type="submit" class="btn btn-success">Submit</button>
</form>