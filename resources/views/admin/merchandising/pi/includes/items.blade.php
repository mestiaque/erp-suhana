                        @if($items && $items->count())
                            @foreach($items as $i => $item)
                            <tr class="itemRow" data-item="{{ $item->id }}">
                                <td class="p-1 text-center">{{ $i+1 }}</td>
                                <td class="p-1">
                                    <input type="text" class="form-control form-control-sm" name="items[{{ $i }}][order_no]" value="{{ $item->order_no }}" readonly>
                                </td>
                                <td class="p-1">
                                    <input type="text" class="form-control form-control-sm" name="items[{{ $i }}][style_no]" value="{{ $item->style_no }}" readonly>
                                </td>
                                <input type="hidden" name="items[{{ $i }}][method]" value="{{ isset($pi) && $pi->items->count() > 0 ? 'update' : 'create' }}"> {{-- if pi and pi->item count > 0 then value update else create --}}
                                <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">


                                <td class="p-1">
                                    <input type="text" class="form-control form-control-sm" name="items[{{ $i }}][fabrication]" value="{{ $item->fabrication }}" readonly>
                                </td>

                                <td class="p-1">
                                    <input type="number" class="form-control form-control-sm qty" name="items[{{ $i }}][order_qty]" value="{{ $item->order_qty ?? $item->total_qty }}" readonly>
                                </td>
                                <td class="p-1">
                                    <select class="form-control form-control-sm" name="items[{{ $i }}][uom]" required>
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

                        @else
                            <tr class="forced_hide">
                                <td colspan="8" class="text-center text-muted">No Items Found</td>
                            </tr>
                        @endif
