<ul>
    @if($employees->count() > 0)
        @foreach($employees as $emp)
        <li>
            <img src="{{asset($emp->image())}}" style="height:35px;width:35px;">
            <span>{{$emp->name}}</span>
            <a href="{{route('admin.loansManagementAction',['add-loan',$emp->id])}}" class="btn-custom yellow"  style="margin-left: 10px;cursor: pointer;float: right;"><i class="bx bx-plus"></i></a>
            <br>
            <span><b>Mobile:</b>{{$emp->mobile}}</span>
        </li>
        @endforeach
    @else
    <li>
        <span>No Bank Found</span>
    </li>
    @endif
</ul>