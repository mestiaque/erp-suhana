<div class="table-responsive">
    <table class="table table-bordered invoiceTable">
        <tr>
            <th style="width:40px;">SL</th>
            <th>Product</th>
            <th style="width:120px;text-align:center;">Ordered Qty</th>
            <th style="width:120px;">Received Qty</th>
            <th style="width:80px;">Unit</th>
        </tr>

        @if($receive->items->count() > 0)
            @foreach($receive->items as $i => $item)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $item->material_name ?? 'N/A' }}</td>
                    <td style="text-align:center;">{{ numberFormat($item->orderItem?->qty ?? 0,1)}}  ({{ numberFormat($item->orderItem?->received_qty ?? 0,1)}})</td>
                    <td>
                        <input type="hidden" value="{{$item->id}}" name="item_id" >
                        <input type="number" step="any" min="0" max="{{ $item->orderItem?->qty ?? 0 }}" name="qty_{{$item->id}}" class="form-control form-control-sm"
                               data-item="{{ $item->id }}"
                               value="{{ $item->received_qty ?? '' }}">
                    </td>
                    <td>{{ $item->orderItem?->unit ?? '--' }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5" style="text-align:center;color:#aaa;">No Item Found</td>
            </tr>
        @endif
    </table>
</div>
