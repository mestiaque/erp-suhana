

<div class="table-responsive">
    <table class="table table-bordered table-sm m-0">
        <thead>
            <tr>
                <th width="5%">SL</th>
                <th width="10%">Style</th>
                <th width="25%">Fabrication</th>
                <th width="15%">Total Req Qty</th>
            </tr>
        </thead>
        <tbody>

        @if($items && count($items) > 0)
            @foreach($items as $i => $item)

                @php
                    $knittings = [];
                    if (!empty($item->knitting_count)) {
                        $knittings = json_decode($item->knitting_count, true) ?? [];
                    }
                @endphp

                <tr>
                    <td class="text-center">{{ $i+1 }}</td>

                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                    <input type="hidden" name="items[{{ $i }}][booking_no]" value="{{ $item->booking_no }}">

                    <td>
                        <input type="text"
                               class="form-control form-control-sm"
                               value="{{ $item->style ?? $item->style_no }}"
                               readonly>
                        <input type="hidden" name="items[{{ $i }}][style_no]" value="{{ $item->style ?? $item->style_no }}">
                    </td>

                    <td>
                        <input type="text"
                               class="form-control form-control-sm"
                               value="{{ $item->fabric_type ?? $item->fabrication }}"
                               readonly>
                        <input type="hidden" name="items[{{ $i }}][fabrication]" value="{{ $item->fabric_type ?? $item->fabrication }}">
                    </td>
                    <td>
                        <input type="number"
                               name="items[{{ $i }}][requisition_qty]"
                               class="form-control form-control-sm total-qty"
                               value="{{ $item->required_qty }}"
                               >
                    </td>
                </tr>

            @endforeach
        @else
            <tr>
                <td colspan="5" class="text-center text-muted">No Knitting Items Found</td>
            </tr>
        @endif

        </tbody>
    </table>
</div>
