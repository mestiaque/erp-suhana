<div class="row mb-4">
    <div class="col-lg-12">
        <h5>CM (Cutting & Making)</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="cmTable">
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
                        @foreach($budget->cms as $index => $cm)
                            <tr class="cmRow">
                                <td><input type="text" name="cm_desc[description][]" class="form-control form-control-sm" value="{{ $cm->description }}"></td>
                                <td><input type="text" name="cm_desc[supplier][]" class="form-control form-control-sm" value="{{ $cm->supplier }}"></td>
                                <td><input type="number" step="any" name="cm_desc[qty][]" class="form-control form-control-sm calc" value="{{ (int)$cm->qty }}"></td>
                                <td><input type="number" step="any" name="cm_desc[unit_price][]" class="form-control form-control-sm calc" value="{{ $cm->unit_price }}"></td>
                                <td><input type="number" step="any" name="cm_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly value="{{ $cm->ttl_usd }}"></td>
                                <td><input type="number" step="any" name="cm_desc[item_total][]" class="form-control form-control-sm item_total" readonly value="{{ $cm->item_total }}" @if($index != $budget->cms->count() - 1) style="display:none;" @endif></td>
                                <td><input type="number" step="any" name="cm_desc[percent][]" class="form-control form-control-sm" readonly value="{{ $cm->percent }}" @if($index != $budget->cms->count() - 1) style="display:none;" @endif></td>
                                <td><input type="text" name="cm_desc[company_name][]" class="form-control form-control-sm" value="{{ $cm->company_name }}" @if($index != $budget->cms->count() - 1) style="display:none;" @endif></td>
                                <td><input type="text" step="any" name="cm_desc[payment_value][]" class="form-control form-control-sm" value="{{ $cm->payment_value }}" @if($index != $budget->cms->count() - 1) style="display:none;" @endif></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="cmRow">
                            <td><input type="text" name="cm_desc[description][]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="cm_desc[supplier][]" class="form-control form-control-sm"></td>
                            <td><input type="number" step="any" name="cm_desc[qty][]" class="form-control form-control-sm calc"></td>
                            <td><input type="number" step="any" name="cm_desc[unit_price][]" class="form-control form-control-sm calc"></td>
                            <td><input type="number" step="any" name="cm_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                            <td><input type="number" step="any" name="cm_desc[item_total][]" class="form-control form-control-sm item_total" readonly></td>
                            <td><input type="number" step="any" name="cm_desc[percent][]" class="form-control form-control-sm" readonly></td>
                            <td><input type="text" name="cm_desc[company_name][]" class="form-control form-control-sm"></td>
                            <td><input type="text" step="any" name="cm_desc[payment_value][]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button></td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10">
                            <button type="button" class="btn btn-sm btn-primary" id="addCMRow">+ Add Row</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
