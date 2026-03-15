<form action="{{route('admin.leadsAction',['meeting',$lead->id])}}" class="meetingForm" method="post">
         @csrf
      <div class="row">
        <div class="col-md-12 form-group">
          <label>Title/Subject*</label>
          <input type="text" class="form-control {{$errors->has('title')?'error':''}}" name="title" placeholder="Enter Title/Subject" required="">
          @if ($errors->has('title'))
			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('title') }}</p>
			@endif
        </div>
        <div class="col-md-12 form-group">
          <label>Date & Time*</label>
          <input type="datetime-local" class="form-control {{$errors->has('date_time')?'error':''}}" name="date_time" required="">
          @if ($errors->has('date_time'))
			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('date_time') }}</p>
		  @endif
        </div>
        <div class="col-md-6 form-group">
            <label>Meeting type*</label>
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
            <label>Location*</label>
            <input type="text" class="form-control {{$errors->has('location')?'error':''}}" name="location" placeholder="(physical or virtual link)" required="">
            @if ($errors->has('location'))
			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('location') }}</p>
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
        <div class="row">
            <div class="col-md-6 form-group">
			    <label for="status">Status* </label>
                <select class="form-control" name="status" required="">
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
      <button type="submit" class="btn btn-success">Submit</button>
</form>