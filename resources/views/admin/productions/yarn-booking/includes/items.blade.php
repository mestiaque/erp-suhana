@php
    $yarnOptions = [];
    for($x = 16; $x <= 40; $x += 2){
        $yarnOptions[] = $x.'/1';
    }
@endphp
{{-- @php
    $yarnOptions = [];

    for ($x = 16; $x <= 40; $x += 2) {
        for ($y = 1; $y <= 2; $y++) {
            $yarnOptions[] = $x . '/' . $y;
        }
    }
@endphp --}}


<div class="table-responsive">
    <table class="table table-bordered table-sm m-0">
        <thead>
            <tr>
                <th width="5%">SL</th>
                <th width="10%">Style</th>
                <th width="25%">Fabrication</th>
                <th width="20%">Composition</th>
                <th width="30%">Yarn Count Wise Qnty</th>
                <th width="10%">Total Req Qnty</th>
            </tr>
        </thead>
        <tbody>

        @if($items && count($items) > 0)
            @foreach($items as $i => $item)

                @php
                    $yarns = [];
                    if (!empty($item->yarn_count)) {
                        $yarns = json_decode($item->yarn_count, true) ?? [];
                    }

                    $composition = '--';
                    if ($item->order) {
                        $composition = $item->order->items
                            ->pluck('composition')
                            ->filter()
                            ->unique()
                            ->implode(', ');

                        if ($composition === '') {
                            $composition = '--';
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $i+1 }}</td>

                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                    <input type="hidden" name="items[{{ $i }}][booking_no]" value="{{ $item->booking_no }}">
                    <input type="hidden" name="items[{{ $i }}][order_no]" value="{{ $item->order_no }}">

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
                        <input type="text"
                               class="form-control form-control-sm"
                               value="{{ $composition }}"
                               readonly>
                    </td>

                    {{-- Yarn Count + Qnty --}}
                    <td>
                        <table class="table table-bordered table-sm yarn-sub-table mb-0">
                            <tbody class="yarnBody">

                            @if(count($yarns) > 0)
                                @foreach($yarns as $y)
                                    <tr class="yarn-row">
                                        <td width="45%">
                                            <select name="items[{{ $i }}][yarn_count][]"
                                                    class="form-control form-control-sm yarn-count-select"
                                                    required>
                                                <option value="">-- Select Yarn Count --</option>
                                                @foreach($yarnOptions as $option)
                                                    <option value="{{ $option }}"
                                                        {{ $y['count']==$option?'selected':'' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="35%">
                                            <input type="number" step="0.1"
                                                   name="items[{{ $i }}][yarn_qty][]"
                                                   class="form-control form-control-sm yarn-qty"
                                                   value="{{ $y['qty'] }}"
                                                   min="">
                                        </td>
                                        <td width="20%" class="text-center">
                                            <button type="button" class="btn btn-success btn-sm addRow">+</button>
                                            <button type="button" class="btn btn-danger btn-sm removeRow">-</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="yarn-row">
                                    <td>
                                        <select name="items[{{ $i }}][yarn_count][]"
                                                class="form-control form-control-sm yarn-count-select"
                                                required>
                                            <option value="">-- Select Yarn Count --</option>
                                            @foreach($yarnOptions as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.1"
                                               name="items[{{ $i }}][yarn_qty][]"
                                               class="form-control form-control-sm yarn-qty"
                                               value="0"
                                               min="0">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-success btn-sm addRow">+</button>
                                        <button type="button" class="btn btn-danger btn-sm removeRow">-</button>
                                    </td>
                                </tr>
                            @endif

                            </tbody>
                        </table>
                    </td>

                    {{-- Total Qnty --}}
                    <td>
                        <input type="number" step="0.1"
                               name="items[{{ $i }}][requisition_qty]"
                               class="form-control form-control-sm total-qty"
                               value="{{ $item->required_qty }}"
                               readonly>
                    </td>
                </tr>

            @endforeach
        @else
            <tr>
                <td colspan="6" class="text-center text-muted">No Yarn Items Found</td>
            </tr>
        @endif

        </tbody>
    </table>
</div>
