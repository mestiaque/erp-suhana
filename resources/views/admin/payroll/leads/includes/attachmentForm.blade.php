<form action="{{route('admin.leadsAction',['attachment',$lead->id])}}" class="meetingForm" method="post" enctype="multipart/form-data">
    @csrf
    
 	<div class="form-group">
        <label for="name">Attachment(Image/video/pdf)</label>
        <input type="file" name="attachment" class="form-control" accept="image/*, video/mp4, application/pdf" style="padding: 3px;" required="">
        @if ($errors->has('attachment'))
		<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
	  @endif
    </div>
     <button type="submit" class="btn btn-success">Submit</button>
</form>