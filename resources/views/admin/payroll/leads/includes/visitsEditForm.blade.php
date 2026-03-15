<form action="{{route('admin.leadsAction',['visit-update',$lead->id])}}" class="meetingForm" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" value="{{$visit->id}}" name="visit_id">
    <div class="row">
        <div class="col-md-6 form-group">
          <label>Visit Date*</label>
          <input type="datetime-local" class="form-control {{$errors->has('visit_date')?'error':''}}" name="visit_date" value="{{$visit->created_at->format('Y-m-d\TH:i')}}" required="">
          @if ($errors->has('visit_date'))
			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('visit_date') }}</p>
		  @endif
        </div>
        <div class="col-md-6 form-group">
            <label>Location*</label>
            <input type="text" class="form-control {{$errors->has('location')?'error':''}}" name="location" placeholder="(In office or Factory visit)" value="{{$visit->location}}" required="">
            @if ($errors->has('location'))
			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('location') }}</p>
		  @endif
        </div>
      </div>
      <div class="form-group">
			<label for="name">Description</label>
			<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{{$visit->description}}</textarea>
			@if ($errors->has('description'))
			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
			@endif
     	</div>
        <div class="row">
            <div class="col-md-6 form-group">
			    <label for="status">Status* </label>
                <select class="form-control" name="status" required="">
                    <option value="">Select Type</option>
                    <option value="Not Potential" {{$visit->status=='Not Potential'?'selected':''}} >Not Potential</option>
                    <option value="Potential" {{$visit->status=='Potential'?'selected':''}} >Potential</option>
                    <option value="Very Potential" {{$visit->status=='Very Potential'?'selected':''}} >Very Potential</option>
                    <!--<option value="Canceled" {{$visit->status=='Canceled'?'selected':''}} >Canceled</option>-->
                    <!--<option value="Rescheduled" {{$visit->status=='Rescheduled'?'selected':''}} >Rescheduled</option>-->
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