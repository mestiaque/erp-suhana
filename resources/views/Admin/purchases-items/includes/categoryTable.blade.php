<table class="table">
    <thead>
        <tr>
            <th style="min-width: 50px;width: 50px;">#</th>
            <th style="min-width: 200px;">Name</th>
            <th style="min-width: 60px;">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $i=>$category)
        <tr>
            <td>{{$i+1}}</td>
            <td class="ctgName">{{$category->name}}</td>
            <td>
                <a href="javascript:void(0)"  class="mr-3 editCtg" data-url="{{route('admin.purchasesItemsAction',['updateCtg',$category->id])}}" data-name="{{$category->name}}"  >
                    <i class="bx bx-edit"></i>
                </a>
                <a href="javascript:void(0)" data-url="{{route('admin.purchasesItemsAction',['deleteCtg',$category->id])}}" class="text-danger deleteCtg"><i class="bx bx-trash"></i></a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>