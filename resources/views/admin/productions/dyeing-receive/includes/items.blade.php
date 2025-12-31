<div class="table-responsive">
    <table class="table table-bordered table-sm m-0">
        <thead class="bg-lisght">
            <tr>
                <th>Style</th>
                <th>Fabrication</th>
                <th>Color</th>
                <th>Booking Qty</th>
                <th>Current Receive (KG)</th>
            </tr>
        </thead>
        <tbody>
        @foreach($items as $i => $item)
            @php
                $style = $item->style_no ?? $item->style;
                $color = $item->color_name ?? $item->color;
            @endphp
            <tr class="">
                <td>{{ $style }}</td>
                <td>{{ $item->fabric_type }}</td>
                <td>{{ $color }}</td>
                <td>{{ $item->required_qty ?? $item->bookingItem->required_qty }}</td>
                <td>
                    @if(isset($receive) && $action == 'update')
                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                    <input type="number" step="0.01" name="items[{{ $i }}][receive_qty]" class="form-control form-control-sm" value="{{ $item->receive_qty }}" required>
                    @else
                        {{-- ক্রিয়েট মোড --}}
                        <input type="hidden" name="items[{{ $i }}][style]" value="{{ $style }}">
                        <input type="hidden" name="items[{{ $i }}][color]" value="{{ $color }}">
                        <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                        <input type="number" step="0.01" name="items[{{ $i }}][receive_qty]" class="form-control form-control-sm" value="0">
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
