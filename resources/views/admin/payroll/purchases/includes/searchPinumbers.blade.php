<ul>
    @if($pinumbers->count() > 0)
        @foreach($pinumbers as $data)
        <li>
            <span>{{$data->invoice}} - {{$data->id}}</span>
            <span class="btn-custom yellow addDataQuery" data-type="pinumbers" data-url="{{route('admin.lcInvoicesAction',['add-pinumber',$invoice->id,'pinumber_id'=>$data->id])}}" style="margin-left: 10px;cursor: pointer;float: right;"><i class="bx bx-plus"></i></span>
        </li>
        @endforeach
    @else
    <li>
        <span>No PI Number Found</span>
    </li>
    @endif
</ul>