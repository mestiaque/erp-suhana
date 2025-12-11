<div class="table-responsive" style="min-height: 100px;">
    <table class="table table-bordered table-sm m-0">
        <thead>
            <tr>
                <th style="width: 40px;">SL</th>
                <th>Fabrication</th>
                <th>Yarn Count</th>
                <th>Yarn Req. Qty</th>
            </tr>
        </thead>

        <tbody>
            @if($items && $items->count() > 0)
                @foreach($items as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>

                        <td>
                            <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                            <input type="text" class="form-control form-control-sm"
                                value="{{ $item->fabrication }}" readonly>
                        </td>
                        <td>
                            <select name="items[{{ $i }}][yarn_count]" class="form-control form-control-sm" required>
                                <option value="">-- Select Yarn Count --</option>
                                @for ($x = 16; $x <= 40; $x += 2)
                                    <option value="{{ $x }}/1" {{ (isset($item) && $item->yarn_count == $x.'/1') ? 'selected' : '' }}>
                                        {{ $x }}/1
                                    </option>
                                @endfor
                            </select>
                        </td>

                        <td>
                            <input type="number" step="0.01"
                                class="form-control form-control-sm"
                                name="items[{{ $i }}][requisition_qty]"
                                value="{{ $item->requisition_qty }}" required>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">
                        No Yarn Items Found
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
