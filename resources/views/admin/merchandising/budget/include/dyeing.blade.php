<div class="row mb-4">
    <div class="col-lg-12">
        <h5>Dyeing</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="dyeingTable">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Creditor</th>
                        <th>Qty</th>
                        <th>Unit Price ($)</th>
                        <th>TTL US $</th>
                        <th>Item Wise Total Value</th>
                        <th>%</th>
                        <th>Company Name</th>
                        <th>Payment Value</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($budget) && $budget->dyeings->count() > 0)
                        @foreach($budget->dyeings as $index => $dye)
                            <tr class="dyeingRow">
                                <td><input type="text" name="dyeing_desc[description][]" class="form-control form-control-sm" value="{{ $dye->description }}"></td>
                                <td><input type="text" name="dyeing_desc[supplier][]" class="form-control form-control-sm" value="{{ $dye->supplier }}"></td>
                                <td><input type="number" step="any" name="dyeing_desc[qty][]" class="form-control form-control-sm calc" value="{{ (int)$dye->qty }}"></td>
                                <td><input type="number" step="any" name="dyeing_desc[unit_price][]" class="form-control form-control-sm calc" value="{{ $dye->unit_price }}"></td>
                                <td><input type="number" step="any" name="dyeing_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly value="{{ $dye->ttl_usd }}"></td>
                                <td><input type="number" step="any" name="dyeing_desc[item_total][]" class="form-control form-control-sm item_total" readonly value="{{ $dye->item_total }}" @if($index != $budget->dyeings->count() - 1) style="display:none;" @endif></td>
                                <td><input type="number" step="any" name="dyeing_desc[percent][]" class="form-control form-control-sm" readonly value="{{ $dye->percent }}" @if($index != $budget->dyeings->count() - 1) style="display:none;" @endif></td>
                                <td><input type="text" name="dyeing_desc[company_name][]" class="form-control form-control-sm" value="{{ $dye->company_name }}" @if($index != $budget->dyeings->count() - 1) style="display:none;" @endif></td>
                                <td><input type="text" step="any" name="dyeing_desc[payment_value][]" class="form-control form-control-sm" value="{{ $dye->payment_value }}" @if($index != $budget->dyeings->count() - 1) style="display:none;" @endif></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="dyeingRow">
                            <td><input type="text" name="dyeing_desc[description][]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="dyeing_desc[supplier][]" class="form-control form-control-sm"></td>
                            <td><input type="number" step="any" name="dyeing_desc[qty][]" class="form-control form-control-sm calc"></td>
                            <td><input type="number" step="any" name="dyeing_desc[unit_price][]" class="form-control form-control-sm calc"></td>
                            <td><input type="number" step="any" name="dyeing_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                            <td><input type="number" step="any" name="dyeing_desc[item_total][]" class="form-control form-control-sm item_total" readonly></td>
                            <td><input type="number" step="any" name="dyeing_desc[percent][]" class="form-control form-control-sm" readonly></td>
                            <td><input type="text" name="dyeing_desc[company_name][]" class="form-control form-control-sm"></td>
                            <td><input type="text" step="any" name="dyeing_desc[payment_value][]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button></td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10">
                            <button type="button" class="btn btn-sm btn-primary" id="addDyeingRow"> + Add Dyeing </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
