<table class="table table-hover table-striped">
    <thead>
        <tr>
            <th style="min-width: 50px;width: 50px;">#</th>
            <th style="min-width: 200px;">Name</th>
            <th style="min-width: 60px;">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($units as $i=>$unit)
        <tr>
            <td>{{$i+1}}</td>
            <td class="unitName">{{$unit->name}}</td>
            <td class="text-center">
                @if(can('purchases_items_units.add') || can('purchases_items_units.delete'))
                @can('purchases_items_units.add')
                <a href="javascript:void(0)"  class="mr-3 editUnit" data-url="{{route('admin.purchasesItemsAction',['updateUnit',$unit->id])}}"  data-name="{{$unit->name}}">
                    <i class="bx bx-edit"></i>
                </a>
                @endcan
                @can('purchases_items_units.delete')
                <a href="javascript:void(0)" data-url="{{route('admin.purchasesItemsAction',['deleteUnit',$unit->id])}}" class="text-danger deleteUnit"><i class="bx bx-trash"></i></a>
                @endcan
                @else -- @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
