<div class="table-responsive" style="min-height: 100px;">
    <table class="table table-bordered orderTable">
        <tr>
            <th class="px-2 pb-1" style="width: 20px;">SL</th>
            <th class="px-2 pb-1" style="width: 180px;">Composition</th>
            <th class="px-2 pb-1" style="width: 80px;">GSM</th>
            <th class="px-2 pb-1" style="width: 80px;">Color</th>
            <th class="px-2 pb-1" style="width: 80px;">Size</th>
            <th class="px-2 pb-1" style="width: 80px;">Quantity</th>
            <th class="px-2 pb-1" style="width: 80px;">Unit Price</th>
            <th class="px-2 pb-1" style="width: 80px;">Amount</th>
            <th class="px-2 pb-1" style="width: 80px;">Discount</th>
            <th class="px-2 pb-1" style="width: 150px;">Comments</th>
        </tr>

        @if($sample->items->count() > 0)
            @foreach($sample->items as $i => $item)
                <tr class="itemRow" data-item="{{ $item->id }}">
                    <td class="p-1 text-start" style="vertical-align: bottom; text-align: center;">{{ $i+1 }}</td>

                    <td class="p-1">
                        <input class="form-control form-control-sm" style="height:31px;" value="{{ $item->composition }}" readonly>
                    </td>

                    <td class="p-1">
                        <input readonly type="text" class="form-control form-control-sm" value="{{ $item->gsm }}">
                    </td>

                    <td class="p-1">
                        <input readonly type="text" class="form-control form-control-sm" value="{{ $item->color }}">
                    </td>

                    <td class="p-1">
                        <input readonly type="text" class="form-control form-control-sm" value="{{ $item->size }}">
                    </td>

                    <td class="p-1">
                        <input readonly type="number" class="form-control form-control-sm qty" value="{{ $item->quantity }}">
                    </td>
                    <td class="p-1">
                        <input type="number" class="form-control form-control-sm unit-price updateItem" value="{{ $item->unit_price }}" data-name="unit_price"
                               data-url="{{ route('admin.proformaInvoiceAction',['update-item',$sample->id,'item_id'=>$item->id]) }}">
                    </td>
                    <td class="p-1">
                        <input type="number" readonly class="form-control form-control-sm amount" value="{{ $item->amount }}" data-name="amount"
                               data-url="{{ route('admin.proformaInvoiceAction',['update-item',$sample->id,'item_id'=>$item->id]) }}">
                    </td>
                    <td class="p-1">
                        <input type="number" class="form-control form-control-sm discount updateItem" value="{{ $item->discount }}" data-name="discount"
                               data-url="{{ route('admin.proformaInvoiceAction',['update-item',$sample->id,'item_id'=>$item->id]) }}">
                    </td>
                    <td class="p-1">
                        <input readonly type="text" class="form-control form-control-sm" value="{{ $item->comments }}">
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8" style="text-align:center;color:#aaa;">No Item</td>
            </tr>
        @endif
        <tfoot>
            <tr> 
                <th class="text-right" colspan="5">Total Quantity : </th> 
                <th class="totalQty">{{ $sample->items->sum('quantity') ?? 0 }}</th> 
                <th></th>
                <th class="totalAmount">{{ $sample->items->sum('amount') ?? 0 }}</th>
                <th class="totalDiscount">{{ $sample->items->sum('discount') ?? 0 }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
