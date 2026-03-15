<form action="{{route('admin.leadsAction',['visit',$lead->id])}}" class="meetingForm" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-6 form-group">
          <label>Visit Date*</label>
          <input type="datetime-local" class="form-control {{$errors->has('visit_date')?'error':''}}" name="visit_date" required="">
          @if ($errors->has('visit_date'))
			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('visit_date') }}</p>
		  @endif
        </div>
        <div class="col-md-6 form-group">
            <label>Location*</label>
            <input type="text" class="form-control {{$errors->has('location')?'error':''}}" value="{{$lead->address}}" name="location" placeholder="(In office or Factory visit)" required="">
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
                    <option value="Not Potential">Not Potential</option>
                    <option value="Potential">Potential</option>
                    <option value="Very Potential">Very Potential</option>
                    <!--<option value="Canceled">Canceled</option>-->
                    <!--<option value="Rescheduled">Rescheduled</option>-->
                </select>
				@if ($errors->has('status'))
				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
				@endif
         	</div>
         	<div class="col-md-6 form-group">
                <label for="name">Attachment(Image)</label>
    	        <input type="file" name="attachment" class="form-control" accept="image/*" style="padding: 3px;">
                @if ($errors->has('meeting_type'))
    			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('meeting_type') }}</p>
    		  @endif
            </div>
        </div>
      <button type="submit" class="btn btn-success">Submit</button>
</form>