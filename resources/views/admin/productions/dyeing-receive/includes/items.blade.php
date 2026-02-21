<div class="table-responsive">
    <table class="table table-bordered table-sm m-0">
        <thead class="bg-">
            <tr>
                <th>Style</th>
                <th>Fabrication</th>
                <th>Color</th>
                <th>Booking Qty</th>
                <th>Receive Qty (KG)</th>
            </tr>
        </thead>
        <tbody>
        @foreach($items as $i => $item)

            @php
                $style = $item->style_no ?? $item->style;
                $color = $item->color_name ?? $item->color;
                $bookingQty = $item->required_qty
                    ?? $item->bookingItem->required_qty
                    ?? 0;
            @endphp

            <tr>
                <td>{{ $style }}</td>
                <td>{{ $item->fabric_type }}</td>
                <td>{{ $color }}</td>
                <td class="text-right">{{ number_format($bookingQty,2) }}</td>
                <td>
                    @if($action === 'update')
                        {{-- UPDATE MODE --}}
                        <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                        <input type="number"
                               step="0.01"
                               min="0"
                               name="items[{{ $i }}][receive_qty]"
                               class="form-control form-control-sm"
                               value="{{ $item->receive_qty }}"
                               required>
                    @else
                        {{-- CREATE MODE --}}
                        <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                        <input type="hidden" name="items[{{ $i }}][style]" value="{{ $style }}">
                        <input type="hidden" name="items[{{ $i }}][color]" value="{{ $color }}">
                        <input type="number"
                               step="0.01"
                               min="0"
                               name="items[{{ $i }}][receive_qty]"
                               class="form-control form-control-sm"
                               value="0">
                    @endif
                </td>
            </tr>

        @endforeach
        </tbody>
    </table>
</div>
