
<div class="table-responsive">
    <table class="table table-bordered table-sm">
        <thead class=" text-center">
            <tr>
                <th width="5%">SL</th>
                <th width="20%">Style</th>
                <th width="25%">Fabrication</th>
                <th width="15%">Booking Qty</th>
                <th width="10%">Rolls</th>
                <th width="25%">Receive Weight (KG)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $i => $item)
                @php
                    // এডিট মোড হলে রিসিভ টেবিল থেকে ডাটা আসবে, নতুন হলে বুকিং থেকে
                    $rollQty = $item->current_roll_qty ?? '';
                    $weightVal = $item->current_weight ?? '';
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td><b>{{ $item->style }}</b></td>
                    <td>{{ $item->fabric_type }}</td>
                    <td class="text-center">{{ number_format($item->booking_qty, 2) }} KG</td>

                    <input type="hidden" name="items[{{$i}}][knit_id]" value="{{ $item->id }}">
                    <td>
                        <input type="number" name="items[{{$i}}][roll_qty]"
                               value="{{ $rollQty }}"
                               class="form-control form-control-sm text-center">
                    </td>
                    <td>
                        <input type="number" step="any" name="items[{{$i}}][weight]"
                               value="{{ $weightVal }}"
                               class="form-control form-control-sm text-center border-primary font-weight-bold"
                               placeholder="0.00">
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-3 text-muted">Please select a PI to load knitting items</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
