<div class="row">
    <div class="col-md-6">
        <div class="table-responsive">
            <table class="table table-borderless">
                <tr>
                    <th style="min-width: 40%;width:40%;padding:2px;">Supplier
                        <!--<span class="btn-custom" data-toggle="modal" data-target="#AddCompany" style="float:right;cursor:pointer;"><i class="bx bx-plus"></i></span>-->
                    </th>
                    <td style="padding:2px;">
                        <select class="form-control selectSupplier" data-url="{{route('admin.purchasesAction',['add-supplier',$invoice->id])}}" >
                            <option value="">Select Supplier</option>
                            @foreach(App\Models\User::where('status',1)->where('supplier',true)->select(['id','name','mobile','email'])->get() as $supplier)
                            <option value="{{$supplier->id}}">{{$supplier->name}} - {{$supplier->mobile?:$supplier->email}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <th style="padding:2px;">Name</th>
                    <td style="padding: 2px;">
                        <div class="input-group">
                            <input type="text" class="form-control updateInfo" data-url="{{route('admin.purchasesAction',['supplier-name',$invoice->id])}}" value="{{$invoice->name}}" placeholder="Enter Name">
                        </div> 
                        
                    </td>
                </tr>
                <tr>
                    <th style="padding:2px;">Mobile/Email</th>
                    <td style="padding: 2px;">
                        <div class="input-group">
                            <input type="text" class="form-control updateInfo" data-url="{{route('admin.purchasesAction',['supplier-mobile',$invoice->id])}}" value="{{$invoice->mobile?:$invoice->email}}" placeholder="Enter Mobile/Email">
                        </div> 
                        
                    </td>
                </tr>
                <tr>
                    <th style="padding:2px;">Address</th>
                    <td style="padding: 2px;">
                        <div class="input-group">
                            <textarea class="form-control updateInfo" data-url="{{route('admin.purchasesAction',['supplier-address',$invoice->id])}}" placeholder="Write Address">{{$invoice->address}}</textarea>
                        </div> 
                        
                    </td>
                </tr>
                
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="table-responsive">
            <table class="table table-borderless">
                <tr>
                    <th style="padding:2px;">Purchase NO*</th>
                    <td style="padding: 2px;">
                        <div class="input-group">
                            <input type="text" class="form-control updateInfo" data-url="{{route('admin.purchasesAction',['supplier-invoice',$invoice->id])}}"  value="{{$invoice->invoice}}" placeholder="LC Serial" required="">
                        </div> 
                    </td>
                </tr>
                <tr>
                    <th style="min-width: 40%;width:40%;padding:2px;">Purchase Date*</th>
                    <td style="padding: 2px;">
                        <input type="date" class="form-control" name="created_at" value="{{$invoice->created_at->format('Y-m-d')}}" required="">
                    </td>
                </tr>
                <tr>
                    <th style="padding:2px;">Status</th>
                    <td style="padding: 2px;">
                        <select class="form-control" name="status">
                            <option value="">Select Status</option>
                            <option value="pending" {{$invoice->order_status=='pending'?'selected':''}} >Pending</option>
                            <option value="confirmed" {{$invoice->order_status=='confirmed'?'selected':''}} >Confirmed</option>
                            <option value="cancelled" {{$invoice->order_status=='cancelled'?'selected':''}} >Cancelled</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="row" style="margin:0 -10px;">
    <div class="col-md-4" style="padding:10px;">
        <div class="searchGrid">
            <input type="text" class="form-control form-control-sm SearchQuery" data-type="goods" data-url="{{route('admin.purchasesAction',['search-goods',$invoice->id])}}"  placeholder="Search Item of Goods">
            <div class="itemSearch searchlist" style="height:200px;overflow:auto;">
                @include(adminTheme().'purchases.includes.searchGoods',['services'=>App\Models\Post::latest()->where('type',3)->where('status','active')->limit(10)->get()])
		    </div>
        </div>
    </div>
</div>

<div class="table-responsive" style="min-height: 300px;">
        <table class="table table-bordered invoiceTable">
        <tr>
            <th style="width: 40px;min-width: 40px;text-align: center;">SL</th>
            <!--<th style="min-width: 200px;">Ref</th>-->
            <th style="min-width: 200px;">Description of Goods</th>
            <th style="width: 80px;min-width: 80px;">Quantity</th>
            <th style="width: 80px;min-width: 80px;">Unit</th>
            <th style="width: 100px;min-width: 100px;">Price</th>
            <th style="width: 120px;min-width: 120px;">Total Price</th>
            <th style="padding: 2px;width: 60px;min-width: 60px;text-align: center;">
                <!--<span class="btn-custom success addItem" data-url="{{route('admin.purchasesAction',['add-item',$invoice->id])}}" ><i class="bx bx-plus"></i></span>-->
            </th>
        </tr>
        @if($invoice->items->count() > 0)
        @foreach($invoice->items as $i=>$item)
        <tr>
            <td>{{$i+1}}</td>
            <!--<td style="padding: 2px;">-->
            <!--    <textarea type="text" class="form-control form-control-sm updateItem" data-name="product_name" data-url="{{route('admin.purchasesAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" placeholder="Write Ref">{{$item->product_name}}</textarea>-->
            <!--</td>-->
            <td style="padding: 2px;">
                <textarea type="text" style="height:31px;" class="form-control form-control-sm updateItem" data-name="description" data-url="{{route('admin.purchasesAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" placeholder="Description">{{$item->description}}</textarea>
            </td>
            <td style="padding: 2px;">
                <input type="number" step="any" class="form-control form-control-sm updateItem calculate quantity_{{$item->id}}" data-id="{{$item->id}}" data-name="quantity" data-url="{{route('admin.purchasesAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" value="{{$item->quantity?:''}}" placeholder="Quantity">
            </td>
            <td style="padding: 2px;">
                <select class="form-control form-control-sm updateItem" data-name="unit" data-url="{{route('admin.purchasesAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" >
                    <option value="">Select</option>
                    @foreach(App\Models\PostExtra::where('type',1)->get(['name']) as $unit)
                    <option value="{{$unit->name}}" {{$unit->name==$item->unit?'selected':''}} >{{$unit->name}}</option>
                    @endforeach
                </select>
                <!--<input type="text"  class="form-control form-control-sm updateItem" data-name="unit" data-url="{{route('admin.purchasesAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" value="{{$item->unit}}" placeholder="Unit">-->
            </td>
            <td style="padding: 2px;">
                <input type="number" step="any" class="form-control form-control-sm updateItem calculate price_{{$item->id}}" data-id="{{$item->id}}" data-name="price" data-url="{{route('admin.purchasesAction',['update-item',$invoice->id,'item_id'=>$item->id])}}" value="{{$item->price > 0?$item->price:''}}" placeholder="Price">
            </td>
            <td>
                <span class="priceTotal priceTotal_{{$item->id}}">{{priceFormat($item->final_price)}}</span>
            </td>
            <td style="padding:2px;text-align: center;">
                <span class="btn-custom danger removeItem" data-url="{{route('admin.purchasesAction',['remove-item',$invoice->id,'item_id'=>$item->id])}}"><i class="bx bx-trash"></i></span>
            </td>
        </tr>
        @endforeach
        @else
            <tr>
                <td colspan="7" style="text-align: center;color: #cfcaca;">No Item</td>
            </tr>
        @endif
        <!--<tr>-->
        <!--    <th colspan="5"></th>-->
        <!--    <th>Total</th>-->
        <!--    <th>{{priceFormat($invoice->total_price)}}</th>-->
        <!--    <th></th>-->
        <!--</tr>-->
    </table>
</div>
