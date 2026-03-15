<ul>
    @if($banks->count() > 0)
        @foreach($banks as $bank)
        <li>
            <span>{{$bank->name}}</span>
            <span class="btn-custom yellow addDataQuery" data-type="bank" data-url="{{route('admin.lcInvoicesAction',['add-bank',$invoice->id,'bank_id'=>$bank->id])}}" style="margin-left: 10px;cursor: pointer;float: right;"><i class="bx bx-plus"></i></span>
            <br>
            <span>{{$bank->description}}</span>
        </li>
        @endforeach
    @else
    <li>
        <span>No Bank Found</span>
    </li>
    @endif
</ul>