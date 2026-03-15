<table class="table table-bordered">
    <tr>
         <th style="width: 120px;min-width: 120px;" >EMI</th>
         <th style="min-width: 140px;">Amount</th>
         <th style="width: 120px;min-width: 120px;">Date</th>
         <th style="width: 50px;min-width: 50px;">
             <span class="btn btn-info addEMI" data-sale="{{$sale->id}}" data-url="{{route('admin.companiesAction',['sale-emi-add',$company->id,'sale_id'=>$sale->id])}}" style="padding:1px 10px;line-height: 15px;"><i class="bx bx-plus"></i></span>
         </th>
    </tr>
     @if($sale->transectionsAll()->where('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->count() > 0)
     
        @foreach($sale->transectionsAll()->where('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->get() as $in=>$installment)
 	        <tr>
 	            <td>
 	                {{$in+1}} EMI
 	            </td>
 	            <td>
 	                <input type="number" placeholder="Amount" value="{{$installment->amount}}" class="emiUpdate" data-sale="{{$sale->id}}" data-url="{{route('admin.companiesAction',['sale-emi-amount',$company->id,'sale_id'=>$sale->id,'emi_id'=>$installment->id])}}" style="width: 100%;"
 	                {{$installment->status=='success'?'disabled':''}}
 	                >
 	            </td>
 	            <td>
 	                <input type="date" value="{{$installment->created_at->format('Y-m-d')}}" class="emiUpdate" data-sale="{{$sale->id}}" data-url="{{route('admin.companiesAction',['sale-emi-date',$company->id,'sale_id'=>$sale->id,'emi_id'=>$installment->id])}}" style="width: 100%;"
 	                {{$installment->status=='success'?'disabled':''}}
 	                > 
 	            </td>
 	            <td style="text-align:center;">
 	                <span class="removeEMI" style="cursor:pointer;" data-sale="{{$sale->id}}" data-url="{{route('admin.companiesAction',['sale-emi-remove',$company->id,'sale_id'=>$sale->id,'emi_id'=>$installment->id])}}"><i class="bx bx-trash"></i></span>
 	            </td>
 	        </tr>
         @endforeach
         <tr>
             <td style="text-align:center;">Total</td>
             <td>{{number_format($sale->emi_amount)}}</td>
             <td></td>
             <td></td>
         </tr>
     @else
     <tr>
         <td style="text-align:center;" colspan="4">No EMI</td>
     </tr>
     @endif
</table>