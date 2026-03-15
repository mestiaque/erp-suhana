<ul>
    @if($services->count() > 0)
        @foreach($services as $service)
        <li>
            <span style="font-size: 14px;width: 90%;display: inline-block;">{{$service->name}}</span>
            <span class="btn-custom yellow addDataQuery" data-type="items" data-url="{{route('admin.purchasesAction',['add-goods',$invoice->id,'service_id'=>$service->id])}}" style="margin-left: 10px;cursor: pointer;position: absolute;right: 5px;"><i class="bx bx-plus"></i></span>
            <br>
            <b style="">{{priceFormat($service->item_price)}}</b>
        </li>
        @endforeach
    @else
    <li>
        <span>No Item of Goods Found</span>
    </li>
    @endif
</ul>