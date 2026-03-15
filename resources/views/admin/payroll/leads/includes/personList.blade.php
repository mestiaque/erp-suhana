<table class="table table-bordered mcTable">
    <tr>
        <th style="width: 40px;min-width: 40px;">SL</th>
        <th style="min-width: 200px;width: 250px;">Operator Name</th>
        <th style="min-width: 200px;width: 200px;">Mobile</th>
        <th style="min-width: 200px;">Operator Details</th>
        <th style="padding:2px;text-align:center;width: 50px;min-width: 50px;"><span class="btn btn-sm btn-info addPerson" data-type="{{$type}}"  data-url="{{route('admin.leadsAction',['add-person',$lead->id])}}"><i class="bx bx-plus"></i></span></th>
    </tr>
    @foreach($lead->persons()->where('type',$type)->get() as $i=>$person)
    <tr>
        <td style="padding:5px;">{{$i+1}}</td>
        <td style="padding:2px;"><input type="text"  value="{{$person->name}}" class="form-control form-control-sm updatePerson" data-id="{{$person->id}}" data-column="name"  placeholder="Name"></td>
        <td style="padding:2px;"><input type="text"  value="{{$person->mobile}}" class="form-control form-control-sm updatePerson" data-id="{{$person->id}}" data-column="mobile" placeholder="Mobile"></td>
        <td style="padding:2px;"><input type="text"  value="{{$person->description}}" class="form-control form-control-sm updatePerson" data-id="{{$person->id}}" data-column="description" placeholder="Details"></td>
        <td style="padding:5px;text-align: center;">
            <span class="text-white bg-danger p-1 removePerson" data-type="{{$type}}" data-url="{{route('admin.leadsAction',['remove-person',$lead->id,'person_id'=>$person->id])}}" style="border-radius: 3px;cursor: pointer;"><i class="bx bx-trash"></i></span>
        </td>
    </tr>
    @endforeach
    @if($lead->persons()->where('type',$type)->count()==0)
    <tr>
        <td colspan="6" style="text-align:center;color: #b1afaf;">No Operator</td>
    </tr>
    @endif
</table>