<div class="row">
    <div class="col-md-6">
        <div class="table-responsive">
            <table class="table table-borderless">
                
                <tr>
                    <th style="min-width: 40%;width:40%;padding:2px;">Company
                        <span class="btn-custom" data-toggle="modal" data-target="#AddCompany" style="float:right;cursor:pointer;"><i class="bx bx-plus"></i></span>
                    </th>
                    <td style="padding:2px;">
                        @if($invoice->company)
                         <b>{{$invoice->company->factory_name}}</b> / {{$invoice->company->owner_name}}<br>
                         
                         {{$invoice->company->company_address}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="padding:2px;">Invoice No</th>
                    <td style="padding: 2px;">
                        <div class="input-group">
                            <input type="text" readonly="" class="form-control" name="invoice" value="{{old('invoice')?:$invoice->invoice}}" placeholder="invoice no">
                        </div> 
                        @if ($errors->has('invoice'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('invoice') }}</p>
        				@endif
                    </td>
                </tr>
                <tr>
                    <th >Date</th>
                    <td style="padding: 2px;">
                        <input type="date" class="form-control" name="created_at" value="{{old('created_at')?:$invoice->created_at->format('Y-m-d')}}">
                        @if ($errors->has('created_at'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
        				@endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="table-responsive">
            <table class="table table-borderless">
                
                <tr>
                    <th style="min-width: 40%;width:40%;padding:2px;">Status</th>
                    <td style="padding: 2px;">
                        @if($invoice->hasLcOrders->count() > 0)
                        <select class="form-control" name="status" disabled="" required="">
                            <option value="{{$invoice->order_status}}" >{{ucfirst($invoice->order_status)}}</option>
                        </select>
                        @else
                        <select class="form-control" name="status" required="">
                            <option value="pending" {{$invoice->order_status=='pending'?'selected':''}} >Pending</option>
                            <option value="confirmed" {{$invoice->order_status=='confirmed'?'selected':''}} >Confirmed</option>
                            <option value="cancelled" {{$invoice->order_status=='cancelled'?'selected':''}} >Cancelled</option>
                        </select>
                        @endif
                        @if ($errors->has('status'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
        				@endif
                    </td>
                </tr>
                <tr>
                    <th style="padding:2px;">Payment Mode</th>
                    <td style="padding: 2px;">
                        <select class="form-control changeMode" {{$invoice->paid_amount > 0?'disabled':''}} data-name="payment_mode" data-url="{{route('admin.salesAction',['update-paymentmode',$invoice->id])}}">
                            <option value="Regular" {{$invoice->emi_status?'':'selected'}} >Regular</option>
                            <option value="EMI" {{$invoice->emi_status?'selected':''}} >EMI</option>
                        </select>
                        @if ($errors->has('payment_mode'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('payment_mode') }}</p>
        				@endif
                    </td>
                </tr>
                <tr>
                    <th style="padding:2px;">Currency</th>
                    <td style="padding: 2px;">
                        <select class="form-control changeMode" {{$invoice->paid_amount > 0?'disabled':''}} data-name="currency" data-url="{{route('admin.salesAction',['update-currencymode',$invoice->id])}}">
                            <option value="BDT" {{$invoice->currency=='BDT'?'selected':''}} >BDT (TK)</option>
                            <!--<option value="USD" {{$invoice->currency=='USD'?'selected':''}} >USD (Dollar)</option>-->
                        </select>
                  
                        @if ($errors->has('currency'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('currency') }}</p>
        				@endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
        
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4" style="padding:10px;">
                <div class="searchGrid">
                    <input type="text" class="form-control form-control-sm SearchQuery" data-type="goods" data-url="{{route('admin.salesAction',['search-goods',$invoice->id])}}"  placeholder="Search Item of Goods">
                    <div class="itemSearch searchlist" style="height:200px;overflow:auto;">
                        @include(adminTheme().'sales.includes.searchGoods',['services'=>App\Models\Post::latest()->where('type',3)->where('status','active')->limit(10)->get()])
                        
        		    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive" style="min-height: 200px;">
            <table class="table table-bordered invoiceTable">
                <tr>
                    <th style="width: 40px;min-width: 40px;">SL</th>
                    <!--<th style="min-width: 200px;">Ref</th>-->
                    <th style="min-width: 200px;">Description of Goods</th>
                    <th style="width: 80px;min-width: 80px;">Quantity</th>
                    <th style="width: 80px;min-width: 80px;">Unit</th>
                    <th style="width: 100px;min-width: 100px;">Price
                    @if($invoice->currency)
                    {{$invoice->currency}}
                    @endif
                    </th>
                    <th style="width: 120px;min-width: 120px;">Total Price</th>
                    <th style="padding: 2px;width: 60px;min-width: 60px;text-align: center;">
                        <span class="btn-custom success addItem" data-url="{{route('admin.salesAction',['add-item',$invoice->id])}}" ><i class="bx bx-plus"></i></span>
                    </th>
                </tr>
                @if($invoice->items->count() > 0)
                @foreach($invoice->items as $i=>$item)
                <tr>
                    <td>{{$i+1}}</td>
                    <!--<td style="padding: 2px;">-->
                    <!--    <textarea type="text" class="form-control form-control-sm updateItem" data-name="product_name" data-url="{{route('admin.salesAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" placeholder="Write Ref">{{$item->product_name}}</textarea>-->
                    <!--</td>-->
                    <td style="padding: 2px;">
                        <textarea type="text" class="form-control form-control-sm updateItem" style="height:31px;" data-name="description" data-url="{{route('admin.salesAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" placeholder="Description">{{$item->description}}</textarea>
                    </td>
                    <td style="padding: 2px;">
                        <input type="number" step="any" class="form-control form-control-sm updateItem calculate quantity_{{$item->id}}" data-id="{{$item->id}}" data-name="quantity" data-url="{{route('admin.salesAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" value="{{$item->quantity?:''}}" placeholder="Quantity">
                    </td>
                    <td style="padding: 2px;">
                        <select class="form-control form-control-sm updateItem" data-name="unit" data-url="{{route('admin.salesAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" >
                            <option value="">Select</option>
                            @foreach(App\Models\PostExtra::where('type',1)->get(['name']) as $unit)
                            <option value="{{$unit->name}}" {{$unit->name==$item->unit?'selected':''}} >{{$unit->name}}</option>
                            @endforeach
                        </select>
                        <!--<input type="text"  class="form-control form-control-sm updateItem" data-name="unit" data-url="{{route('admin.salesAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" value="{{$item->unit}}" placeholder="Unit">-->
                    </td>
                    <td style="padding: 2px;">
                        <input type="number" step="any" class="form-control form-control-sm updateItem calculate price_{{$item->id}}" data-id="{{$item->id}}" data-name="price" data-url="{{route('admin.salesAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" value="{{$item->price > 0?$item->price:''}}" placeholder="Price">
                    </td>
                    <td>
                        <span class="priceTotal priceTotal_{{$item->id}}">{{$item->final_price}}</span>
                    </td>
                    <td style="padding:2px;text-align: center;">
                        <span class="btn-custom danger removeItem" data-url="{{route('admin.salesAction',['remove-item',$invoice->id,'item_id'=>$item->id])}}"><i class="bx bx-trash"></i></span>
                    </td>
                </tr>
                @endforeach
                <tr>
                    <th colspan="5" style="text-align:right;">Total</th>
                    <th class="totalSum">{{priceFormat($invoice->grand_total)}}</th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan="5" style="text-align:right;">Paid</th>
                    <th class="totalPaid" data-amount="{{$invoice->paid_amount}}">{{priceFormat($invoice->paid_amount)}}</th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan="5" style="text-align:right;">Due</th>
                    <th class="totalDue">{{priceFormat($invoice->due_amount)}}</th>
                    <th></th>
                </tr>
                @else
                    <tr>
                        <td colspan="8" style="text-align: center;color: #cfcaca;">No Item</td>
                    </tr>
                @endif
            </table>
        </div>
        
        @if($invoice->emi_status)

        <div class="row">
            <div class="col-md-6" style="padding:10px;">
                @php
                $downPayment =$invoice->grand_total - $invoice->transectionsAll()->where('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->sum('amount');
                $collectPayment =$invoice->transectionsAll()->where('billing_reason','not like','%Installment%')->whereIn('status',['success'])->sum('amount');
                @endphp
                

                <div class="row m-0">
                    <div class="col-md-4 form-group">
                        <label>Starting Date</label>
                        <input type="date" value="{{$invoice->created_at->addMonth()->format('Y-m-d')}}" class="form-control form-control-sm emiStartDate" >
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Amount</label>
                        <input type="number" class="form-control form-control-sm emiAmount"  placeholder="Amount" >
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Installment Time</label>
                        <div class="input-group">
                            <input type="number" class="form-control form-control-sm emiTime" value="" placeholder="Time" max="999" >
                            <button type="button" class="btn btn-sm btn-info rounded-0 emiUpdate" data-url="{{route('admin.salesAction',['emi-update',$invoice->id])}}" >Add</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered EmiTable">
                        <tr>
                            <th style="padding: 5px;min-width: 140px;">Installment</th>
                            <th style="padding: 5px;width: 120px;min-width: 120px;">Amount</th>
                            <th style="padding: 5px;width: 60px;min-width: 60px;">Status</th>
                            <th style="padding: 5px;width: 120px;min-width: 120px;">Date</th>
                        </tr>
                        @foreach($invoice->transectionsAll()->whereNot('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->get() as $l=>$installment)
                        <tr>
                            
                            <td style="padding:5px 10px;">
                                Down payment
                            </td>
                            <td style="padding:5px; 10px">
                                {{$installment->currency}} {{number_format($installment->amount,2)}}
                            </td>
                            <td style="padding:5px 10px;">
                                @if($installment->status=='pending')
                                Due
                                @else
                                Paid
                                @endif
                            </td>
                            <td style="padding:5px;">
                                {{$installment->created_at->format('d M, Y')}}
                                @if($installment->status=='pending')
                                <span class="text-danger removeInstallment" data-url="{{route('admin.salesAction',['remove-installment',$invoice->id,'installment_id'=>$installment->id])}}" style="cursor: pointer;"><i class="bx bx-trash"></i></span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        
                        @foreach($invoice->transectionsAll()->where('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->get() as $l=>$installment)
                        <tr>
                            <td style="padding:5px 10px;">
                                {{serial($l+1)}} Installment
                            </td>
                            <td style="padding:5px; 10px">
                                {{$installment->currency}} {{number_format($installment->amount,2)}}
                            </td>
                            <td style="padding:5px 10px;">
                                @if($installment->status=='pending')
                                Due
                                @else
                                Paid
                                @endif
                            </td>
                            <td style="padding:2px;">
                                {{$installment->created_at->format('d M, Y')}}
                                @if($installment->status=='pending')
                                <span class="text-danger removeInstallment" data-url="{{route('admin.salesAction',['remove-installment',$invoice->id,'installment_id'=>$installment->id])}}" style="cursor: pointer;"><i class="bx bx-trash"></i></span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td style="padding:5px 10px;text-align:right">Total</td>
                            <td style="padding:5px;">{{$invoice->currency}} {{priceFormat($invoice->transectionsAll()->whereIn('status',['pending','success'])->sum('amount'))}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @endif
        
    </div>
</div>

        
