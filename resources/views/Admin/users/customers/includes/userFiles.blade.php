@if($user->galleryFiles->count() > 0)

@foreach($user->galleryFiles as $file)
<tr>
    <td style="padding: 3px;" >
        @if($file->file_url)
        <span style="padding: 7px;display: inline-block;">
            <a href="{{asset($file->file_url)}}" title="{{$file->file_name}}" download="">Download File</a>
            <span class="badge badge-danger removeFile" style="cursor:pointer;" data-id="{{$file->id}}" data-url="{{route('admin.usersCustomerAction',['user-document',$user->id,'file_action'=>'removeFile','file_id'=>$file->id])}}" ><i class="bx bx-x"></i></span>
        </span>
        @else
        <div class="custom-file">
             <input type="file" name="favicon" class="custom-file-input updateFile" data-id="{{$file->id}}" data-url="{{route('admin.usersCustomerAction',['user-document',$user->id])}}" >
             <label class="custom-file-label">Choose file... </label>
        </div>
        @endif
    </td>
    <td style="padding: 3px;" >
        <input type="text" name="fileName" value="{{$file->file_name}}" class="form-control updateData" data-url="{{route('admin.usersCustomerAction',['user-document',$user->id,'file_action'=>'updateTitle','file_id'=>$file->id])}}" placeholder="Enter Title">
    </td>
    <td style="padding: 5px 15px">
        <a href="javascript:void(0)" class="btn-custom danger removeData" data-url="{{route('admin.usersCustomerAction',['user-document',$user->id,'file_action'=>'removeData','file_id'=>$file->id])}}" ><i class="bx bx-trash"></i></a>
    </td>
</tr>
@endforeach

@else
<tr>
    <td colspan="3" style="text-align:center;">No Attachment File</td>
</tr>
@endif