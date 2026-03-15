<form action="{{route('admin.leadsAction',['note-update',$lead->id])}}"  method="post" >
    @csrf
    <input type="hidden" value="{{$note->id}}" name="note_id">
    <div class="form-group">
		<label for="name">Note Details</label>
		<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="write note">{{$note->description}}</textarea>
		@if ($errors->has('description'))
		<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
		@endif
 	</div>
    <button type="submit" class="btn btn-success">Submit</button>
</form>