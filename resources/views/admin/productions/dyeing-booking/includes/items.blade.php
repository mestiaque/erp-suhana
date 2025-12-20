<div class="table-responsive">
    <table class="table table-bordered table-sm m-0">
        <thead>
            <tr>
                <th width="5%">SL</th>
                <th width="10%">Style</th>
                <th width="25%">Fabrication</th>
                <th width="25%">Composition</th>
                <th width="25%">Color</th>
                <th width="15%">Req Qty</th>
            </tr>
        </thead>
        <tbody>

        @if($items && count($items) > 0)
            @php $sl = 1; @endphp
            @foreach($items as $i => $item)

            <tr>
                <td class="text-center">{{ $sl++ }}</td>

                <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                <input type="hidden" name="items[{{ $i }}][booking_no]" value="{{ $item->booking_no }}">
                <input type="hidden" name="items[{{ $i }}][order_no]" value="{{ $item->order_no }}">

                <td>
                    <input type="text"
                           class="form-control form-control-sm"
                           value="{{ $item->style_no ?? $item->style }}"
                           readonly>
                    <input type="hidden" name="items[{{ $i }}][style_no]" value="{{ $item->style_no ?? $item->style }}">
                </td>
                <td>
                    <input type="text"
                           class="form-control form-control-sm"
                           value="{{ $item->orderDetail->fabrication ?? $item->fabric_type }}"
                           readonly>
                    <input type="hidden" name="items[{{ $i }}][fabrication]" value="{{ $item->orderDetail->fabrication ?? $item->fabric_type }}">
                </td>
                <td>
                    <input type="text"
                           class="form-control form-control-sm"
                           value="{{ $item->composition ?? $item->composition }}"
                           readonly>
                    <input type="hidden" name="items[{{ $i }}][composition]" value="{{ $item->orderDetail->composition ?? $item->composition }}">
                </td>
                <td>
                    <input type="text"
                           class="form-control form-control-sm"
                           value="{{ $item->color_name ?? $item->color }}"
                           readonly>
                    <input type="hidden" name="items[{{ $i }}][color]" value="{{ $item->color_name ?? $item->color }}">
                </td>

                <td>
                    <input type="number"
                           name="items[{{ $i }}][requisition_qty]"
                           class="form-control form-control-sm total-qty"
                           value="{{ $item->qty ?? $item->required_qty }}">
                </td>
            </tr>

            @endforeach
        @else
            <tr>
                <td colspan="5" class="text-center text-muted">No Dyeing Items Found</td>
            </tr>
        @endif

        </tbody>
    </table>
</div>
