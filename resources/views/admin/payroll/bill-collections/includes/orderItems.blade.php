<div class="row">
    <div class="col-4">
        @if($invoice->company)
         <b>{{$invoice->company->factory_name}}</b> / {{$invoice->company->owner_name}}<br>
        {{$invoice->company->company_address}}
        @endif
        <br>
        <b>Invoice Date</b>: {{$invoice->created_at->format('d.m.Y')}} <br>
        <b>Invoice No</b>: {{$invoice->invoice}} <br>
        <b>Invoice By</b>: {{$invoice->saleBy?$invoice->saleBy->name:''}} <br>
    </div>
    <div class="col-4"></div>
    <div class="col-4">
        <div class="invoice-info">
            <b>Order Status</b>:
            {{ucfirst($invoice->order_status)}}
            </br>
            <b>Payment Currency</b>:
            {{ucfirst($invoice->currency)}}
            </br>
            <b>Payment Mode</b>:
            @if($invoice->emi_status)
            <span>EMI</span>
            @else
            Regular
            @endif
            </br>
            <b>Payment Status</b>:
            <span style="width: 150px;display: inline-block;text-align: left;">
            @if($invoice->payment_status=='paid')
            <span style="color: #0cd836;font-weight: bold;">{{ucfirst($invoice->payment_status)}}</span>
            @elseif($invoice->payment_status=='partial')
            <span style="color: #ffc310;font-weight: bold;">{{ucfirst($invoice->payment_status)}}</span>
            @else
            <span style="color: #e1000a;font-weight: bold;">{{ucfirst($invoice->payment_status)}}</span>
            @endif
            </span>
        </div>
    </div>
</div>
<br>
<div class="invoice-products">
    <table class="table table-bordered invoiceTable" style="width: 100% !important;">
      <thead>
        <tr>
          <th style="width: 60px;min-width: 60px;text-align: center;">SL.</th>
          <!--<th style="width: 150px;min-width: 150px;text-align: center;">REF</th>-->
          <th style="min-width: 200px;text-align: left;">DESCRIPTION OF GOODS</th>
          <th style="width: 80px;min-width: 80px;text-align: center;">QUANTITY</th>
          <th style="width: 100px;min-width: 100px;text-align: center;">PRICE</th>
          <th style="width: 120px;min-width: 120px;text-align: center;">TOTAL PRICE</th>
        </tr>
      </thead>
      <tbody>
      	@foreach($invoice->items as $i=>$item)
            <tr>
              <td style="text-align: center;">{{$i+1}}</td>
              <!--<td>{{$item->product_name}}</td>-->
              <td style="text-align:left;">{{$item->description}}</td>
              <td style="text-align: center;">{{$item->quantity}} {{$item->unit}}</td>
              <td style="text-align: center;" >{{ucfirst($invoice->currency)}} {{$item->itemPrice()}}</td>
              <td style="text-align: center;">{{ucfirst($invoice->currency)}} {{$item->final_price}}</td>
            </tr>
        @endforeach
        <tr>
        	<td colspan="3"></td>
        	<th style="text-align: center;font-size:16px;">Subtotal</th>
        	<th style="text-align: center;font-size:16px;">{{ucfirst($invoice->currency)}} {{$invoice->total_price}}</th>
        </tr>
        <tr>
        	<td colspan="3"></td>
        	<th style="text-align: center;font-size:16px;">Paid</th>
        	<th style="text-align: center;font-size:16px;">{{ucfirst($invoice->currency)}} {{$invoice->paid_amount}}</th>
        </tr>
        <tr>
        	<td colspan="3"></td>
        	<th style="text-align: center;font-size:16px;">Due</th>
        	<th style="text-align: center;font-size:16px;">{{ucfirst($invoice->currency)}} {{$invoice->due_amount}}</th>
        </tr>
      </tbody>
    </table>
</div>

<div class="paymentCollection">
    <div class="row">
        <div class="col-md-8">
            <h4>Payment Collection
            @if($invoice->emi_status) @else
                <a href="javascript:void(0)" class="btn btn-sm btn-success" data-toggle="modal" data-target="#AddBill"><i class="bx bx-plus"></i> Bill</a>
            @endif
            </h4>
            <div class="table-responsive" style="min-height: 200px;">
                <table class="table table-bordered invoiceTable">
                    <thead>
                        <tr>
                            <th style="min-width: 40px;width:50px;text-align: center;">SL</th>
                            <th style="min-width: 110px;width:110px;text-align: center;">Date</th>
                            <th style="min-width: 150px;">Title</th>
                            <th style="min-width: 120px;width:120px;text-align: center;">Amount</th>
                            <th style="min-width: 80px;width:80px;text-align: center;">Status</th>
                            <th style="min-width: 90px;width:90px;text-align: center;">Action</th>
                        </tr>
                    </thead>
                    @php
                        $serial = 1;
                    @endphp
                    @if($invoice->emi_status)
                        @foreach($invoice->transectionsAll()->whereNot('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->get() as $installment)
                        <tr>
                            <td>{{ $serial++ }}</td>
                            <td style="text-align:center;">{{$installment->created_at->format('d.m.Y')}}</td>
                            <td>{{$installment->billing_reason}}</td>
                            <td style="text-align:center;">{{$installment->currency}} {{priceFormat($installment->amount)}}</td>
                            <td style="text-align:center;">
                                @if($installment->status=='pending')
                                Due
                                @else
                                Paid
                                @endif
                            </td>
                            <td style="text-align:center;">
                               @if($installment->status=='success')
                               <a href="{{route('admin.billCollectionAction',['payment-reset',$invoice->id,'trans_id'=>$installment->id])}}" onclick="return confirm('Are You Want To Reset installment?')"  class="btn btn-sm btn-danger">Reset</a>
                               @else
                               <a href="javascript:void(0)" data-toggle="modal" data-target="#AddInstallment_{{$installment->id}}"  class="btn btn-sm btn-info">Pay</a>
                               @endif
                            </td>
                        </tr>
                        @endforeach
                        
                        @foreach($invoice->transectionsAll()->where('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->get() as $installment)
                        <tr>
                            <td>{{ $serial++ }}</td>
                            <td style="text-align:center;">{{$installment->created_at->format('d.m.Y')}}</td>
                            <td>{{$installment->billing_reason}}</td>
                            <td style="text-align:center;">{{$installment->currency}} {{priceFormat($installment->amount)}}</td>
                            <td style="text-align:center;">
                                @if($installment->status=='pending')
                                Due
                                @else
                                Paid
                                @endif
                            </td>
                            <td style="text-align:center;">
                               @if($installment->status=='success')
                               <a href="{{route('admin.billCollectionAction',['payment-reset',$invoice->id,'trans_id'=>$installment->id])}}" onclick="return confirm('Are You Want To Reset installment?')"  class="btn btn-sm btn-danger">Reset</a>
                               @else
                               <a href="javascript:void(0)" data-toggle="modal" data-target="#AddInstallment_{{$installment->id}}"  class="btn btn-sm btn-info">Pay</a>
                               @endif
                            </td>
                        </tr>
                        @endforeach
                        
                        
                        @if($invoice->transectionsSuccess()->count()==0 && $invoice->transectionsAll()->where('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->count()==0)
                        <tr>
                            <td colspan="6" style="text-align:center;">No Collection</td>
                        </tr>
                        @endif
                        
                    @else
                    
                    @foreach($invoice->transectionsAll()->get() as $transection)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td style="text-align:center;">{{$transection->created_at->format('d.m.Y')}}</td>
                        <td>{{$transection->billing_reason}}</td>
                        <td style="text-align:center;">{{$transection->currency}} {{priceFormat($transection->amount)}}</td>
                        <td style="text-align:center;">
                            @if($transection->status=='pending')
                            Due
                            @else
                            Paid
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if($transection->status=='pending')
                            <a href="#" data-toggle="modal" data-target="#Bill_{{$transection->id}}"  class="btn btn-sm btn-success mr-2"><i class="bx bx-check"></i></a>
                            
                            <!-- Add Modal -->
                            <div class="modal fade text-left" id="Bill_{{$transection->id}}" tabindex="-1" role="dialog">
                               <div class="modal-dialog" role="document">
                            	 <div class="modal-content">
                            	    <form action="{{route('admin.billCollectionAction',['payment-received',$invoice->id])}}" method="post" enctype="multipart/form-data">
                            	   	  @csrf
                                	   <div class="modal-header">
                                		 <h4 class="modal-title">Add Bill</h4>
                                		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                		   <span aria-hidden="true">&times; </span>
                                		 </button>
                                	   </div>
                                	   <div class="modal-body">
                                	       <input type="hidden" value="{{$transection->id}}" name="transection_id">
                                	        <div class="row">
                                         	    <div class="col-md-6">
                                        	        <div class="form-group">
                                        			    <label for="created_at">Date*</label>
                                                        <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{$transection->created_at->format('Y-m-d')}}" name="created_at"  required="">
                                        				@if ($errors->has('created_at'))
                                        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
                                        				@endif
                                                 	</div>
                                                </div>
                                         	    <div class="col-md-6">
                                        	   		<div class="form-group">
                                        			    <label for="amount">Amount* </label>
                                                        <input type="number" class="form-control {{$errors->has('amount')?'error':''}}" readonly="" name="amount" value="{{$transection->amount}}" placeholder="Enter amount" required="">
                                        				@if ($errors->has('amount'))
                                        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('amount') }}</p>
                                        				@endif
                                                 	</div>
                                             	</div>
                                            </div>
                                         	<div class="row">
                                         	    <div class="col-md-6">
                                        	   		<div class="form-group">
                                        			    <label for="account">Account* </label>
                                                        <select class="form-control" name="account" required="" >
                                                            <option value="">Select Account</option>
                                                            @foreach($accountMethods as $method)
                                                            <option value="{{$method->id}}" {{request()->account==$method->id?'selected':''}}>{{$method->name}}</option>
                                                            @endforeach
                                                        </select>
                                        				@if ($errors->has('account'))
                                        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('account') }}</p>
                                        				@endif
                                                 	</div>
                                             	</div>
                                         	    <div class="col-md-6">
                                        	   		<div class="form-group">
                                        			    <label for="method">Payment Method* </label>
                                                        <select class="form-control" name="method" required="" >
                                                            <option value="">Select Method</option>
                                                            @foreach($paymentMethods as $method)
                                                            <option value="{{$method->id}}" {{request()->payment==$method->id?'selected':''}}>{{$method->name}}</option>
                                                            @endforeach
                                                        </select>
                                        				@if ($errors->has('method'))
                                        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('method') }}</p>
                                        				@endif
                                                 	</div>
                                             	</div>
                                         	</div>
                                	   		<div class="form-group">
                                			    <label for="attachment">Attachment <small>(Image/max 2mb)</small> </label>
                                                <input type="file" class="form-control {{$errors->has('attachment')?'error':''}}" accept="image/*" name="attachment" style="padding: 3px;">
                                				@if ($errors->has('attachment'))
                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
                                				@endif
                                         	</div>
                                	   		
                                         	<div class="form-group">
                                			    <label for="note">Note</label>
                                                <input type="text" class="form-control {{$errors->has('note')?'error':''}}" name="note" placeholder="Enter Note">
                                				@if ($errors->has('note'))
                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('note') }}</p>
                                				@endif
                                         	</div>
                                	   </div>
                                	   <div class="modal-footer">
                                		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Bill</button>
                                	   </div>
                            	    </form>
                            	 </div>
                               </div>
                            </div>
                            
                            
                            @endif
                            <a href="{{route('admin.billCollectionAction',['payment-delete',$invoice->id,'trans_id'=>$transection->id])}}" onclick="return confirm('Are You Want To Cancelled?')"  class="btn btn-sm btn-danger"><i class="bx bx-x"></i></a>
                        </td>
                    </tr>
                    @endforeach
                    
                    @if($invoice->transectionsSuccess()->count()==0)
                    <tr>
                        <td colspan="6" style="text-align:center;">No Collection</td>
                    </tr>
                    @endif
                    
                    
                    @endif

                    
                </table>
            </div>
        </div>
    </div>
</div>




@foreach($invoice->transectionsAll()->whereIn('status',['pending'])->get() as $install)
<!-- Add Modal -->
<div class="modal fade text-left" id="AddInstallment_{{$install->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	    <form action="{{route('admin.billCollectionAction',['payment-pay',$invoice->id])}}" method="post" enctype="multipart/form-data">
	   	  @csrf
	   	  <input type="hidden" value="{{$install->id}}" name="trans_id">
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Bill</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	        <div class="row">
             	    <div class="col-md-6">
            	        <div class="form-group">
            			    <label for="created_at">Date*</label>
                            <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{$install->created_at->format('Y-m-d')}}" name="created_at"  required="">
            				@if ($errors->has('created_at'))
            				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
            				@endif
                     	</div>
                    </div>
             	    <div class="col-md-6">
            	   		<div class="form-group">
            			    <label for="amount">Amount* </label>
                            <input type="number" class="form-control {{$errors->has('amount')?'error':''}}" readonly="" name="amount" value="{{$install->amount}}" placeholder="Enter amount" required="">
            				@if ($errors->has('amount'))
            				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('amount') }}</p>
            				@endif
                     	</div>
                 	</div>
                </div>
             	<div class="row">
             	    <div class="col-md-6">
            	   		<div class="form-group">
            			    <label for="account">Account* </label>
                            <select class="form-control" name="account" required="" >
                                <option value="">Select Account</option>
                                @foreach($accountMethods as $method)
                                <option value="{{$method->id}}" {{request()->account==$method->id?'selected':''}}>{{$method->name}}</option>
                                @endforeach
                            </select>
            				@if ($errors->has('account'))
            				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('account') }}</p>
            				@endif
                     	</div>
                 	</div>
             	    <div class="col-md-6">
            	   		<div class="form-group">
            			    <label for="method">Payment Method* </label>
                            <select class="form-control" name="method" required="" >
                                <option value="">Select Method</option>
                                @foreach($paymentMethods as $method)
                                <option value="{{$method->id}}" {{request()->payment==$method->id?'selected':''}}>{{$method->name}}</option>
                                @endforeach
                            </select>
            				@if ($errors->has('method'))
            				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('method') }}</p>
            				@endif
                     	</div>
                 	</div>
             	</div>
    	   		<div class="form-group">
    			    <label for="attachment">Attachment <small>(Image/max 2mb)</small> </label>
                    <input type="file" class="form-control {{$errors->has('attachment')?'error':''}}" accept="image/*" name="attachment" style="padding: 3px;">
    				@if ($errors->has('attachment'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
    				@endif
             	</div>
    	   		
             	<div class="form-group">
    			    <label for="note">Note</label>
                    <input type="text" class="form-control {{$errors->has('note')?'error':''}}" name="note" placeholder="Enter Note">
    				@if ($errors->has('note'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('note') }}</p>
    				@endif
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Bill</button>
    	   </div>
	    </form>
	 </div>
   </div>
</div>
@endforeach

        
