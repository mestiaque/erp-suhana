{{-- <div class="table-responsive">
    <table class="table table-bordered table-sm m-0">
        <thead>
            <tr class="bg-light">
                <th width="5%">SL</th>
                <th width="10%">Style</th>
                <th width="20%">Fabrication</th>
                <th width="50%">Yarn Count Wise Quantity (Receive)</th>
                <th width="15%">Row Total</th>
            </tr>
        </thead>
        <tbody>
        @forelse($items as $i => $item)
            @php
                // বুকিং টেবিলের yarn_count কলামে থাকা JSON ডাটা
                $yarns = json_decode($item->yarn_count, true) ?? [];
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">

                <td>{{ $item->style }}</td>
                <td>{{ $item->fabric_type }}</td>
                <td>
                    <table class="table table-sm table-borderless mb-0">
                        <tbody class="yarnBody">
                        @foreach($yarns as $y)
                            <tr>
                                <td width="50%">
                                    <input type="text" name="items[{{ $i }}][yarn_count][]"
                                           value="{{ $y['count'] }}" class="form-control form-control-sm bg-light" readonly>
                                </td>
                                <td>
                                    <input type="number" step="any" name="items[{{ $i }}][yarn_receive_qty][]"
                                           placeholder="Qty for {{ $y['count'] }}"
                                           class="form-control form-control-sm yarn-recv" min="0">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </td>

                <td>
                    <input type="number" name="items[{{ $i }}][receive_qty]"
                           class="form-control form-control-sm total-qty" value="{{ $item->receive_qty ?? 0 }}" readonly>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No Booking Items Found</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div> --}}
<div class="table-responsive">
    <table class="table table-bordered table-sm m-0">
        <thead>
            <tr class="bg-ligsht">
                <th width="5%">SL</th>
                <th width="10%">Style</th>
                <th width="20%">Fabrication</th>
                <th width="50%">Yarn Count Wise Quantity (Receive)</th>
                <th width="15%">Row Total</th>
            </tr>
        </thead>
        <tbody>
        @forelse($items as $i => $item)
            @php
                // বুকিং টেবিলের yarn_count JSON ডাটা
                $yarns = json_decode($item->yarn_count, true) ?? [];
                $rowTotal = 0; // এডিট মোডের জন্য রো টোটাল হিসাব রাখার ভেরিয়েবল
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">

                <td>{{ $item->style }}</td>
                <td>{{ $item->fabric_type }}</td>
                <td>
                    <table class="table table-sm table-borderless mb-0">
                        <tbody class="yarnBody">
                        @foreach($yarns as $y)
                            @php
                                // এডিট মোড হলে ওই নির্দিষ্ট কাউন্টের আগের ভ্যালুটা ধরা
                                $currentCountQty = 0;
                                if(isset($item->current_receive_data) && isset($item->current_receive_data[$y['count']])) {
                                    $currentCountQty = $item->current_receive_data[$y['count']];
                                    $rowTotal += $currentCountQty; // রো টোটালে যোগ করা
                                }
                            @endphp
                            <tr>
                                <td width="50%">
                                    <input type="text" name="items[{{ $i }}][yarn_count][]"
                                           value="{{ $y['count'] }}" class="form-control form-control-sm bg-light" readonly>
                                </td>
                                <td>
                                    <input type="number" step="any" name="items[{{ $i }}][yarn_receive_qty][]"
                                           placeholder="Qty for {{ $y['count'] }}"
                                           value="{{ $currentCountQty > 0 ? $currentCountQty : '' }}"
                                           class="form-control form-control-sm yarn-recv" min="0">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </td>

                <td>
                    <input type="number" name="items[{{ $i }}][receive_qty]"
                           class="form-control form-control-sm total-qty"
                           value="{{ $rowTotal > 0 ? number_format($rowTotal, 2, '.', '') : '0.00' }}" readonly>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No Booking Items Found</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
