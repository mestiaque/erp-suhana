<div class="row mb-2">
    <div class="col-md-4" style="padding:10px;">
        <div class="searchGrid">
            <input type="text" class="form-control form-control-sm search-material"
                   data-item=""
                   placeholder="Search Material">

            <div class="itemSearch searchlist" style="height:200px;overflow:auto;">
                @include(adminTheme().'purchases.orders.includes.searchGoods', [
                    'goods' => App\Models\Post::latest()->limit(10)->get(),
                    'order' => $order,
                ])
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
            <th  class="p-1" style="width: 60px;text-align:center;vertical-align: middle;">
                <span class="btn-custom success addItem" data-url="{{ route('admin.purchasesOrdersAction',['add-item',$order->id]) }}">
                    <i class="bx bx-plus"></i>
                </span>
            </th>
        </tr>

        <tbody>
            @if($order->items->count() > 0)
                @foreach($order->items as $i => $item)
                <tr>
                    <td class="text-center">{{ $i+1 }}</td>

                    <td>
                        <input type="text" class="form-control form-control-sm update-field"
                               data-item="{{ $item->id }}"
                               data-name="material_name"
                               value="{{ $item->material_name }}"
                               placeholder="Material Name">
                    </td>

                    <td>
                        <input type="number" step="any" class="form-control form-control-sm update-field"
                               data-item="{{ $item->id }}"
                               data-name="qty"
                               value="{{ $item->qty }}">
                    </td>

                    <td>
                        <select class="form-control form-control-sm update-field" data-item="{{ $item->id }}" data-name="unit">
                            <option value="">Select Unit</option>
                            {{-- Optional: load unit list dynamically --}}
                            {{-- @foreach($units as $unit)
                                <option value="{{ $unit->name }}" {{ $item->unit == $unit->name ? 'selected':'' }}>{{ $unit->name }}</option>
                            @endforeach --}}
                        </select>
                    </td>

                    <td>
                        <input type="number" step="any" class="form-control form-control-sm update-field"
                               data-item="{{ $item->id }}"
                               data-name="price"
                               value="{{ $item->price ?? 0 }}">
                    </td>

                    <td class="text-center">
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
        </tbody>
    </table>
</div>
