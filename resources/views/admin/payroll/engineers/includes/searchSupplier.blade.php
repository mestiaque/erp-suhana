<ul>
    @if($suppliers->count() > 0)
        @foreach($suppliers as $emp)
        <li>
            <img src="{{asset($emp->image())}}" style="height:35px;width:35px;">
            <span>{{$emp->name}}</span>
            <span><b>Balance:</b>BDT {{priceFormat($emp->amount)}}</span>
            <br>
            <a href="{{route('admin.supplierTradingAction',['add-goods',$emp->id])}}" class="btn-custom primary"  style="margin: 3px;cursor: pointer;">Received Goods</a>
            <a href="{{route('admin.supplierTradingAction',['add-paybill',$emp->id])}}" class="btn-custom yellow"  style="margin-left: 10px;cursor: pointer;float: right;">Pay Bill</a>
        </li>
        @endforeach
    @else
    <li>
        <span>No Supplier Found</span>
    </li>
    @endif
</ul>