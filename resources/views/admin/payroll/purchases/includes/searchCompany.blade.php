<ul>
    @if($companies->count() > 0)
        @foreach($companies as $company)
        <li>
            <span>{{$company->factory_name}} / {{$company->owner_name}}</span>
            <span class="btn-custom yellow addDataQuery" data-type="companies" data-url="{{route('admin.purchasesAction',['add-company',$invoice->id,'company_id'=>$company->id])}}" style="margin-left: 10px;cursor: pointer;float: right;"><i class="bx bx-plus"></i></span>
            <br>
            <span>{{$company->description}}</span>
        </li>
        @endforeach
    @else
    <li>
        <span>No Company Found</span>
    </li>
    @endif
</ul>