<div class="row mb-2">
    <div class="col-md-4" style="padding:10px;">
        <div class="searchGrid">
            <input type="text" class="form-control form-control-sm search-material SearchQuery"
                   data-item=""
                   placeholder="Search Material">

            <div class="itemSearch searchlist" style="height:200px;overflow:auto;">
                @include(adminTheme().'purchases.orders.includes.searchGoods')
            </div>
        </div>
    </div>
</div>

<div class="table-responsive" style="min-height: 200px;">
    <table class="table table-bordered orderTable">
        <tr>
            <th class="px-2 pb-1" style="width:40px;">SL</th>
            <th class="px-2 pb-1" style="min-width:200px;">Material</th>
            <th class="px-2 pb-1" style="width:80px;">Qty</th>
            <th class="px-2 pb-1" style="width:120px;">Unit</th>
            <th class="px-2 pb-1" style="width:80px;">Price</th>
            <th class="px-2 pb-1" style="width:120px;">Total Price</th>
            <th  class="p-1" style="width: 60px;text-align:center;vertical-align: middle;">
                <span class="btn-custom success addItem" data-url="{{ route('admin.purchasesOrdersAction',['add-item',$order->id]) }}">
                    <i class="bx bx-plus"></i>
                </span>
            </th>
        </tr>

        <tbody>
            @if($order->items->count() > 0)
                @foreach($order->items as $i => $item)
                <tr class="itemRow" data-item="{{ $item->id }}">
                    <td class="text-center" style="padding:5px;">{{ $i+1 }}</td>

                    <td style="padding:5px;">
                        <input type="text" class="form-control form-control-sm update-field"
                               value="{{ $item->material_name }}"
                               data-url="{{ route('admin.purchasesOrdersAction',['update-item',$order->id,'item_id'=>$item->id,'name'=>'material_name']) }}"
                               placeholder="Material Name">
                    </td>

                    <td style="padding:5px;">
                        <input type="number" step="any" class="form-control form-control-sm update-field qty"
                               placeholder="Qty"
                               value="{{ $item->qty > 0? $item->qty : '' }}"
                               data-url="{{ route('admin.purchasesOrdersAction',['update-item',$order->id,'item_id'=>$item->id,'name'=>'qty']) }}"
                               >
                    </td>

                    <td style="padding:5px;">
                        <select class="form-control form-control-sm update-field" data-url="{{ route('admin.purchasesOrdersAction',['update-item',$order->id,'item_id'=>$item->id,'name'=>'unit']) }}">
                            <option value="">Select Unit</option>
                            @foreach(App\Models\Attribute::latest()->where('type',6)->where('status','active')->select(['id','name'])->get() as $unit)
                                <option value="{{ $unit->name }}" {{ $item->unit == $unit->name ? 'selected':'' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="padding:5px;">
                        <input type="number" step="any" class="form-control form-control-sm update-field price"
                                placeholder="Price"
                               value="{{ $item->price > 0? $item->price :'' }}"
                               data-url="{{ route('admin.purchasesOrdersAction',['update-item',$order->id,'item_id'=>$item->id,'name'=>'price']) }}"
                               >
                    </td>
                    <td style="padding:5px;" class="priceTotal">
                        {{ number_format($item->total_price, 2) }}
                    </td>
                    <td class="text-center" style="padding:5px;">
                        <span class="btn-custom danger removeItem" data-item="{{ $item->id }}" data-url="{{ route('admin.purchasesOrdersAction',['remove-item',$order->id,'item_id'=>$item->id]) }}">
                            <i class="bx bx-trash"></i>
                        </span>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center text-muted">No Item</td>
                </tr>
            @endif
            <tr>
                <th colspan="2" class="text-end px-2 pb-1">Total</th>
                <th class="px-2 pb-1 totalQty">{{number_format($order->total_qty)}}</th>
                <th></th>
                <th></th>
                <th class="px-2 pb-1 totalPrice">{{number_format($order->grand_total)}}</th>
                <th></th>
        </tbody>
    </table>
</div>
