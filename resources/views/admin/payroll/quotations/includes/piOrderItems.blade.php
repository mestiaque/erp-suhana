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
                {{--
                <tr>
                    <th style="padding:2px;">Marchantize
                    <span class="btn-custom" data-toggle="modal" data-target="#AddMarchantizer" style="float:right;cursor:pointer;"><i class="bx bx-plus"></i></span>
                    </th>
                    <td style="padding:2px;">
                        @if($invoice->marchantize)
                            <span>{{$invoice->marchantize->name}}</span> </a>
                        @endif
                    </td>
                </tr>
                --}}
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="table-responsive">
            <table class="table table-borderless">
                <tr>
                    <th style="min-width: 40%;width:40%;padding:2px;">Date</th>
                    <td style="padding: 2px;">
                        <input type="date" class="form-control" name="created_at" value="{{old('created_at')?:$invoice->created_at->format('Y-m-d')}}">
                        @if ($errors->has('created_at'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
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
                    <th style="padding:2px;">Status</th>
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
            </table>
        </div>
    </div>
        
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4" style="padding:10px;">
                <div class="searchGrid">
                    <input type="text" class="form-control form-control-sm SearchQuery" data-type="goods" data-url="{{route('admin.quotationsAction',['search-goods',$invoice->id])}}"  placeholder="Search Item of Goods">
                    <div class="itemSearch searchlist" style="height:200px;overflow:auto;">
                        @include(adminTheme().'quotations.includes.searchGoods',['services'=>App\Models\Post::latest()->where('type',3)->where('status','active')->limit(10)->get()])
                        
        		    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive" style="min-height: 200px;">
            <table class="table table-bordered invoiceTable">
                <tr>
                    <th>SL</th>
                    <!--<th style="min-width: 200px;">Ref</th>-->
                    <th style="min-width: 200px;">Description of Goods</th>
                    <th style="width: 80px;min-width: 80px;">Quantity</th>
                    <th style="width: 80px;min-width: 80px;">Unit</th>
                    <th style="width: 100px;min-width: 100px;">Price</th>
                    <th style="width: 120px;min-width: 120px;">Total Price</th>
                    <th style="padding: 2px;">
                        <span class="btn-custom success addItem" data-url="{{route('admin.quotationsAction',['add-item',$invoice->id])}}" ><i class="bx bx-plus"></i></span>
                    </th>
                </tr>
                @if($invoice->items->count() > 0)
                @foreach($invoice->items as $i=>$item)
                <tr>
                    <td>{{$i+1}}</td>
                    <!--<td style="padding: 2px;">-->
                    <!--    <textarea type="text" class="form-control form-control-sm updateItem" data-name="product_name" data-url="{{route('admin.quotationsAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" placeholder="Write Ref">{{$item->product_name}}</textarea>-->
                    <!--</td>-->
                    <td style="padding: 2px;">
                        <textarea type="text" class="form-control form-control-sm updateItem" data-name="description" data-url="{{route('admin.quotationsAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" placeholder="Description">{{$item->description}}</textarea>
                    </td>
                    <td style="padding: 2px;">
                        <input type="number" step="any" class="form-control form-control-sm updateItem calculate quantity_{{$item->id}}" data-id="{{$item->id}}" data-name="quantity" data-url="{{route('admin.quotationsAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" value="{{$item->quantity?:''}}" placeholder="Quantity">
                    </td>
                    <td style="padding: 2px;">
                        <select class="form-control form-control-sm updateItem" data-name="unit" data-url="{{route('admin.quotationsAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" >
                            <option value="">Select</option>
                            @foreach(App\Models\PostExtra::where('type',1)->get(['name']) as $unit)
                            <option value="{{$unit->name}}" {{$unit->name==$item->unit?'selected':''}} >{{$unit->name}}</option>
                            @endforeach
                        </select>
                        <!--<input type="text"  class="form-control form-control-sm updateItem" data-name="unit" data-url="{{route('admin.quotationsAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" value="{{$item->unit}}" placeholder="Unit">-->
                    </td>
                    <td style="padding: 2px;">
                        <input type="number" step="any" class="form-control form-control-sm updateItem calculate price_{{$item->id}}" data-id="{{$item->id}}" data-name="price" data-url="{{route('admin.quotationsAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" value="{{$item->price > 0?$item->price:''}}" placeholder="Price">
                    </td>
                    <td>
                        <span class="priceTotal priceTotal_{{$item->id}}">{{priceFormat($item->final_price)}}</span>
                    </td>
                    <td style="padding:2px;">
                        <span class="btn-custom danger removeItem" data-url="{{route('admin.quotationsAction',['remove-item',$invoice->id,'item_id'=>$item->id])}}"><i class="bx bx-trash"></i></span>
                    </td>
                </tr>
                @endforeach
                @else
                    <tr>
                        <td colspan="7" style="text-align: center;color: #cfcaca;">No Item</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>
</div>

        
