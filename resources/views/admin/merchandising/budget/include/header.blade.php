<div class="card mb-4">
    <div class="card-body">

        <h4 class="text-center fw-bold mb-3">COST SHEET</h4>
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle headerTable">
                <tbody>
                    <tr>
                        <th width="10%">P.I. No.</th>
                        <td width="30%">
                        @if(isset($budget) && $budget->pi_no)
                            <input type="hidden" name="budget[pi_no]" value="{{ $budget->pi_no }}">
                            <input type="text" class="form-control form-control-sm" value="{{ $budget->pi_no ?? '' }}" readonly>
                        @else
                            <select name="budget[pi_no]" id="pi_id" class="form-control form-control-sm">
                                <option value=""> -- Select PI -- </option>
                                @foreach ($pis as $pi)
                                    <option value="{{ $pi['id'] }}"
                                            data-pi-json='@json($pi, JSON_HEX_APOS | JSON_HEX_QUOT)'>
                                        {{ $pi['pi_no'] }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        </td>
                        <th width="10%">BUYER</th>
                        <td width="30%">
                            <input type="text" name="budget[buyer]" class="form-control form-control-sm buyer_name" value="{{ $budget->buyer ?? '' }}" readonly>
                        </td>

                        <th width="10%" class="text-center">GMTS PICTURE</th>
                    </tr>

                    <tr>
                        <th>TOTAL STYLES</th>
                        <td>
                            <input type="text" name="budget[total_styles]" class="form-control form-control-sm style_count" value="{{ $budget->total_style ?? '' }}" readonly>
                        </td>

                        <th>TOTAL POs</th>
                        <td>
                            <input type="text" name="budget[total_pos]" class="form-control form-control-sm order_count" value="{{ $budget->total_po ?? '' }}" readonly>
                        </td>

                        <td width="10%" rowspan="5">
                            {{-- <img src="" alt="Attachment"> --}}
                        </td>
                    </tr>

                    <tr>
                        <th>ITEM</th>
                        <td>
                            <input type="text" name="budget[item]" class="form-control form-control-sm item_name" value="{{ $budget->item ?? '' }}" readonly>
                        </td>
                        <th></th>
                        <td></td>
                    </tr>

                    <tr>
                        <th>L/C NO.</th>
                        <td>
                            <input type="text" name="budget[lc_no]" class="form-control form-control-sm" readonly>
                        </td>

                        <th>L/C DT.</th>
                        <td>
                            <input type="date" name="budget[lc_date]" class="form-control form-control-sm" readonly>
                        </td>
                    </tr>

                    <tr>
                        <th>L/C VALUE</th>
                        <td>
                            <input type="text" name="budget[lc_value]" class="form-control form-control-sm" value="" readonly>
                        </td>

                        <th>SHIP DATE</th>
                        <td>
                            <input type="date" name="budget[ship_date]" class="form-control form-control-sm shipment_date" value="{{ $budget->shipment_date ?? '' }}" readonly>
                        </td>
                    </tr>

                    <tr>
                        <th>P.I. VALUE</th>
                        <td>
                            <input type="number" name="budget[pi_value]" class="form-control form-control-sm pi_value" id="pi-value" value="{{ $budget->pi_value ?? 0.00 }}" readonly>
                        </td>

                        <th>TOTAL QTY (PCS)</th>
                        <td>
                            <input type="number" name="budget[total_qty]" class="form-control form-control-sm total_qty" value="{{ $budget->total_qty ?? 0 }}" readonly>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>
</div>
