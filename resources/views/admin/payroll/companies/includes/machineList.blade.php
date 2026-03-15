<table class="table table-bordered mcTable">
    <tr>
        <th style="width: 40px;min-width: 40px;">SL</th>
        <th style="min-width: 200px;width: 250px;">Machine Name</th>
        <th style="min-width: 200px;width: 200px;">Brand</th>
        <th style="min-width: 100px;width: 100px;">Qty</th>
        <th style="min-width: 200px;">Note</th>
        <th style="padding:2px;text-align:center;width: 50px;min-width: 50px;"><span class="btn btn-sm btn-info addMachine"  data-url="{{route('admin.companiesAction',['add-machine',$company->id])}}"><i class="bx bx-plus"></i></span></th>
    </tr>
    @foreach($company->machinery as $i=>$machine)
    <tr>
        <td style="padding:5px;">{{$i+1}}</td>
        <td style="padding:2px;"><input type="text"  value="{{$machine->name}}" class="form-control form-control-sm updateMachine" data-id="{{$machine->id}}" data-column="name"  placeholder="Name"></td>
        <td style="padding:2px;"><input type="text"  value="{{$machine->brand_name}}" class="form-control form-control-sm updateMachine" data-id="{{$machine->id}}" data-column="brand_name" placeholder="Brand"></td>
        <td style="padding:2px;"><input type="number" value="{{$machine->quantity}}" class="form-control form-control-sm updateMachine" data-id="{{$machine->id}}" data-column="quantity" placeholder="Quantity"></td>
        <td style="padding:2px;"><input type="text"  value="{{$machine->note}}" class="form-control form-control-sm updateMachine" data-id="{{$machine->id}}" data-column="note" placeholder="Note"></td>
        <td style="padding:5px;text-align: center;">
            <span class="text-white bg-danger p-1 removeMachine" data-url="{{route('admin.companiesAction',['remove-machine',$company->id,'machine_id'=>$machine->id])}}" style="border-radius: 3px;cursor: pointer;"><i class="bx bx-trash"></i></span>
        </td>
    </tr>
    @endforeach
    
    @if($company->machinery->count()==0)
    <tr>
        <td colspan="6" style="text-align:center;color: #b1afaf;">No Machine</td>
    </tr>
    @endif
    
    
</table>