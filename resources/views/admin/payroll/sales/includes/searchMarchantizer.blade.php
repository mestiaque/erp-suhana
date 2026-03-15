<ul>
    @if($merchandisers->count() > 0)
        @foreach($merchandisers as $data)
        <li>
            <span><img src="{{asset($data->image())}}"></span>
            <span>{{$data->name}}</span>
            <span class="btn-custom yellow addDataQuery" data-type="merchandisers" data-url="{{route('admin.piInvoicesAction',['add-merchandiser',$invoice->id,'merchandiser_id'=>$data->id])}}" style="margin-left: 10px;cursor: pointer;float: right;"><i class="bx bx-plus"></i></span>
        </li>
        @endforeach
    @else
    <li>
        <span>No Merchandiser Found</span>
    </li>
    @endif
</ul>