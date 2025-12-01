<div class="row mb-2">
    <div class="col-md-4" style="padding:10px;">
        <div class="searchGrid">
            <input type="text" class="form-control form-control-sm search-product SearchQuery"
                   placeholder="Search Product">

            <div class="itemSearch searchlist" style="height:200px;overflow:auto;">
                @include(adminTheme().'orders.includes.searchProducts')
            </div>
        </div>
    </div>
</div>

<div class="table-responsive" style="min-height: 200px;">
    <table class="table table-bordered orderTable">
        <tr>
            <th style="width:40px;">SL</th>
            <th style="min-width:200px;">Product</th>
            <th style="width:80px;">Qty</th>
            <th style="width:120px;">Unit</th>
            <th style="width:80px;">Price</th>
            <th style="width:120px;">Total Price</th>
            <th style="width:60px;text-align:center;">Action</th>
        </tr>

        <tbody>
            @if($order->items->count() > 0)
                @foreach($order->items as $i => $item)
                <tr class="itemRow" data-item="{{ $item->id }}">
                    <td class="text-center">{{ $i+1 }}</td>

                    <td>
                        <input type="text" class="form-control form-control-sm update-field"
                               value="{{ $item->product_name }}"
                               data-url="{{ route('admin.ordersAction',['update-item',$order->id,'item_id'=>$item->id,'name'=>'product_name']) }}"
                               placeholder="Product Name">
                    </td>

                    <td>
                        <input type="number" step="any" class="form-control form-control-sm update-field qty"
                               value="{{ $item->quantity > 0 ? $item->quantity : '' }}"
                               data-url="{{ route('admin.ordersAction',['update-item',$order->id,'item_id'=>$item->id,'name'=>'qty']) }}">
                    </td>

                    <td>
                        <select class="form-control form-control-sm update-field"
                                data-url="{{ route('admin.ordersAction',['update-item',$order->id,'item_id'=>$item->id,'name'=>'unit']) }}">
                            <option value="">Select Unit</option>
                            @foreach(App\Models\Attribute::latest()->where('type',6)->where('status','active')->select(['id','name'])->get() as $unit)
                                <option value="{{ $unit->name }}" {{ $item->unit == $unit->name ? 'selected':'' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <input type="number" step="any" class="form-control form-control-sm update-field price"
                               value="{{ $item->price > 0 ? $item->price : '' }}"
                               data-url="{{ route('admin.ordersAction',['update-item',$order->id,'item_id'=>$item->id,'name'=>'price']) }}">
                    </td>

                    <td class="priceTotal">
                        {{ number_format($item->total_price,2) }}
                    </td>

                    <td class="text-center">
                        <span class="btn-custom danger removeItem"
                              data-item="{{ $item->id }}"
                              data-url="{{ route('admin.ordersAction',['remove-item',$order->id,'item_id'=>$item->id]) }}">
                            <i class="bx bx-trash"></i>
                        </span>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center text-muted">No Items</td>
                </tr>
            @endif

            <tr>
                <th colspan="2" class="text-end">Total</th>
                <th class="totalQty">{{ number_format($order->total_qty) }}</th>
                <th></th>
                <th></th>
                <th class="totalPrice">{{ number_format($order->grand_total) }}</th>
                <th></th>
            </tr>
        </tbody>
    </table>
</div>
