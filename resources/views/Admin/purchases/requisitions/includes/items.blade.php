<div class="row">
    <div class="col-md-4" style="padding:10px;">
        <div class="searchGrid">
            <input type="text" class="form-control form-control-sm SearchQuery"
                   data-type="goods"
                   data-url="{{ route('admin.purchasesRequisitionsAction',['search-product',$requisition->id]) }}"
                   placeholder="Search Product">

            <div class="itemSearch searchlist" style="height:200px;overflow:auto;">
                @include(adminTheme().'purchases.requisitions.includes.searchGoods', [
                    'goods' => App\Models\Post::latest()->limit(10)->get(),
                    'req' => $requisition,
                ])
            </div>
        </div>
    </div>
</div>

<div class="table-responsive" style="min-height: 200px;">
    <table class="table table-bordered invoiceTable">
        <tr>
            <th class="px-2 pb-1" style="width: 40px;">SL</th>
            <th class="px-2 pb-1" style="min-width: 200px;">Product</th>
            <th class="px-2 pb-1" style="width: 80px;">Qty</th>
            <th class="px-2 pb-1" style="width: 120px;">Unit</th>
            <th class="px-2 pb-1" style="width: 150px;">Expected Date</th>
            <th class="px-2 pb-1" style="width: 200px;">Note</th>
            <th class="p-1" style="width: 60px;text-align:center;vertical-align: middle;">
                <span class="btn-custom success addItem"
                      data-url="{{route('admin.purchasesRequisitionsAction',['add-item',$requisition->id])}}">
                      <i class="bx bx-plus"></i>
                </span>
            </th>
        </tr>

        @if($requisition->items->count() > 0)
        @foreach($requisition->items as $i => $item)
        <tr>
            <td class="p-1" style="    vertical-align: bottom; text-align: center;">{{ $i+1 }}</td>

            <td class="p-1">
                <textarea type="text"
                          class="form-control form-control-sm updateItem"
                          data-name="product_name"
                          data-url="{{route('admin.purchasesRequisitionsAction',['update-item',$requisition->id,'item_id'=>$item->id])}}"
                          style="height:31px;"
                          placeholder="Product name">{{ $item->product_name }}</textarea>
            </td>

            <td class="p-1">
                <input type="number" step="any"
                       class="form-control form-control-sm updateItem qty qty_{{$item->id}}"
                       data-id="{{$item->id}}"
                       data-name="qty"
                       data-url="{{route('admin.purchasesRequisitionsAction',['update-item',$requisition->id,'item_id'=>$item->id])}}"
                       value="{{$item->qty}}">
            </td>

            <td class="p-1">
                <select class="form-control form-control-sm updateItem"
                        data-name="unit"
                        data-url="{{route('admin.purchasesRequisitionsAction',['update-item',$requisition->id,'item_id'=>$item->id])}}">
                    <option value="">Select</option>
                    {{-- @foreach(App\Models\PostExtra::where('type',1)->get(['name']) as $unit)
                        <option value="{{$unit->name}}"
                                {{$unit->name==$item->unit?'selected':''}}>
                            {{$unit->name}}
                        </option>
                    @endforeach --}}
                </select>
            </td>

            <td class="p-1">
                <input type="date"
                       class="form-control form-control-sm updateItem"
                       data-name="expected_date"
                       data-url="{{route('admin.purchasesRequisitionsAction',['update-item',$requisition->id,'item_id'=>$item->id])}}"
                       value="{{$item->expected_date}}">
            </td>

            <td class="p-1">
                <input type="text"
                       class="form-control form-control-sm updateItem"
                       data-name="note"
                       data-url="{{route('admin.purchasesRequisitionsAction',['update-item',$requisition->id,'item_id'=>$item->id])}}"
                       value="{{$item->note}}">
            </td>

            <td style="text-align:center;" class="p-1">
                <span class="btn-custom danger removeItem"
                      data-url="{{route('admin.purchasesRequisitionsAction',['remove-item',$requisition->id,'item_id'=>$item->id])}}">
                      <i class="bx bx-trash"></i>
                </span>
            </td>
        </tr>
        @endforeach

        @else
        <tr>
            <td colspan="7" style="text-align:center;color:#aaa;">No Item</td>
        </tr>
        @endif
    </table>
</div>
