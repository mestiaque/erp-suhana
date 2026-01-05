<div class="row mb-4">
    <div class="col-lg-12">
        <h5>Yarn</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="yarnTable">
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
                    @if(isset($budget) && $budget->yarns->count() > 0)
                        @foreach($budget->yarns as $index => $yarn)
                            <tr class="yarnRow">
                                <td><input type="text" name="yarn_desc[description][]" class="form-control form-control-sm" value="{{ $yarn->description }}"></td>
                                <td><input type="text" name="yarn_desc[supplier][]" class="form-control form-control-sm" value="{{ $yarn->supplier }}"></td>
                                <td><input type="number" step="any" name="yarn_desc[qty][]" class="form-control form-control-sm calc" value="{{ (int)$yarn->qty }}"></td>
                                <td><input type="number" step="any" name="yarn_desc[unit_price][]" class="form-control form-control-sm calc" value="{{ $yarn->unit_price }}"></td>
                                <td><input type="number" step="any" name="yarn_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly value="{{ $yarn->ttl_usd }}"></td>
                                <td><input type="number" step="any" name="yarn_desc[item_total][]" class="form-control form-control-sm item_total" readonly value="{{ $yarn->item_total }}" @if($index != $budget->yarns->count() - 1) style="display:none;" @endif></td>
                                <td><input type="number" step="any" name="yarn_desc[percent][]" class="form-control form-control-sm" readonly value="{{ $yarn->percent }}" @if($index != $budget->yarns->count() - 1) style="display:none;" @endif></td>
                                <td><input type="text" name="yarn_desc[company_name][]" class="form-control form-control-sm" value="{{ $yarn->company_name }}" @if($index != $budget->yarns->count() - 1) style="display:none;" @endif></td>
                                <td><input type="text" step="any" name="yarn_desc[payment_value][]" class="form-control form-control-sm" value="{{ $yarn->payment_value }}" @if($index != $budget->yarns->count() - 1) style="display:none;" @endif></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="yarnRow">
                            <td><input type="text" name="yarn_desc[description][]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="yarn_desc[supplier][]" class="form-control form-control-sm"></td>
                            <td><input type="number" step="any" name="yarn_desc[qty][]" class="form-control form-control-sm calc"></td>
                            <td><input type="number" step="any" name="yarn_desc[unit_price][]" class="form-control form-control-sm calc"></td>
                            <td><input type="number" step="any" name="yarn_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                            <td><input type="number" step="any" name="yarn_desc[item_total][]" class="form-control form-control-sm item_total" readonly></td>
                            <td><input type="number" step="any" name="yarn_desc[percent][]" class="form-control form-control-sm" readonly></td>
                            <td><input type="text" name="yarn_desc[company_name][]" class="form-control form-control-sm"></td>
                            <td><input type="text" step="any" name="yarn_desc[payment_value][]" class="form-control form-control-sm"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10">
                            <button type="button" class="btn btn-sm btn-primary" id="addYarnRow">+ Add Yarn</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
