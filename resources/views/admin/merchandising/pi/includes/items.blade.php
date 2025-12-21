<div class="table-responsive" style="min-height: 100px;">
    <table class="table table-bordered orderTable">
        <thead>
            <tr>
                <th class="px-2 pb-1" style="width: 20px;">SL</th>
                <th class="px-2 pb-1" style="width: 100px;">Style No</th>
                <th class="px-2 pb-1" style="width: 150px;">Composition</th>
                <th class="px-2 pb-1" style="width: 150px;">Fabrication</th>
                <th class="px-2 pb-1" style="width: 80px;">GSM</th>
                <th class="px-2 pb-1" style="width: 80px;">Qnty</th>
                <th class="px-2 pb-1" style="width: 80px;">Unit of Measurement</th>
                <th class="px-2 pb-1" style="width: 80px;">Unit Price</th>
                <th class="px-2 pb-1" style="width: 80px;">Total Amount</th>
            </tr>
        </thead>
        <tbody class="cardItems">
            @if($items && $items->count())
                @foreach($items as $i => $item)
                <tr class="itemRow" data-item="{{ $item->id }}">
                    <td class="p-1 text-center">{{ $i+1 }}</td>
                    <td class="p-1">
                        <input type="text" class="form-control form-control-sm" name="items[{{ $i }}][style_no]" value="{{ $item->style_no }}" readonly>
                    </td>
                    <td class="p-1">
                        <input type="hidden" name="items[{{ $i }}][method]" value="{{ isset($pi) && $pi->items->count() > 0 ? 'update' : 'create' }}"> {{-- if pi and pi->item count > 0 then value update else create --}}
                        <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                        <input type="text" class="form-control form-control-sm" name="items[{{ $i }}][composition]" value="{{ $item->composition }}" readonly>
                    </td>

                    <td class="p-1">
                        <input type="text" class="form-control form-control-sm" name="items[{{ $i }}][fabrication]" value="{{ $item->fabrication }}" readonly>
                    </td>
                    <td class="p-1">
                        <input type="text" class="form-control form-control-sm" name="items[{{ $i }}][gsm]" value="{{ $item->gsm }}" readonly>
                    </td>
                    <td class="p-1">
                        <input type="number" class="form-control form-control-sm qty" name="items[{{ $i }}][order_qty]" value="{{ $item->order_qty ?? $item->total_qty }}" readonly>
                    </td>
                    <td class="p-1">
                        <select class="form-control form-control-sm" name="items[{{ $i }}][uom]">
                            <option value="">-- Select UOM --</option>
                            <option value="PCS" {{ ($item->uom ?? '') == 'PCS' ? 'selected' : '' }}>PCS</option>
                            <option value="SETS" {{ ($item->uom ?? '') == 'SETS' ? 'selected' : '' }}>SETS</option>
                        </select>
                    </td>

                    <td class="p-1">
                        <input type="number" step="any" class="form-control form-control-sm updateItem" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}" required>
                    </td>

                    <td class="p-1">
                        <input type="number" class="form-control form-control-sm updateItem" name="items[{{ $i }}][total_price]" value="{{ $item->total_price }}" readonly>
                    </td>
                </tr>
                @endforeach

                <tr>
                    <td colspan="5" class="text-right">Total </td>
                    <td class="totalQty text-center">0</td>
                    <td></td>
                    <td></td>
                    <td class="totalAmount text-center">0.00</td>
                </tr>
            @else
                <tr>
                    <td colspan="9" class="text-center text-muted">No Items Found</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
