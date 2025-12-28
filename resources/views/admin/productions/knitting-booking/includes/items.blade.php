

<div class="table-responsive">
    <table class="table table-bordered table-sm">
        <thead>
            <tr class=" text-center">
                <th>Style</th>
                <th>Fabrication</th>
                {{-- <th>GSM</th> --}}
                <th>Dia</th>
                <th>Booking Qty (KG)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
                @php
                    $orderItem = $item->pi->order->items->where('style_no', $item->style)->first();
                    $gsm = $item->gsm ?? ($orderItem->gsm ?? '');
                    $fabric = $item->fabric_type ?? ($orderItem->fabrication ?? '');
                @endphp
                <tr>
                    <td>
                        {{ $item->style }}
                        <input type="hidden" name="items[{{ $i }}][yarn_booking_id]" value="{{ $item->id }}">
                        <input type="hidden" name="items[{{ $i }}][style]" value="{{ $item->style }}">
                        <input type="hidden" name="items[{{ $i }}][fabrication]" value="{{ $item->fabric_type ?? $fabric }}">
                    </td>
                    <td>{{ $fabric }}</td>
                    <td>
                        {{-- আপনার অর্ডারের কমপজিশন (যেমন: 95% Cotton 5% Polister) দেখাতে পারেন --}}
                        <input type="text" name="items[{{ $i }}][dia]"
                            value="{{ $item->dia ?? '' }}" class="form-control form-control-sm">
                    </td>
                    <td>
                        <input type="number" step="any" name="items[{{ $i }}][booking_qty]"
                            value="{{ $item->knit_booking_qty ?? $item->required_qty }}"
                            class="form-control form-control-sm">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
