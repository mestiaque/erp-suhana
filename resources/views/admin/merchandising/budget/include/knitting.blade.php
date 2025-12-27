<div class="row mb-4">
    <div class="col-lg-12">
        <h5>Knitting</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="knittingTable">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Supplier</th>
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
                    @if(isset($budget) && $budget->knittings->count() > 0)
                        @foreach($budget->knittings as $index => $knit)
                            <tr class="knitRow">
                                <td><input type="text" name="knitting_desc[description][]" class="form-control form-control-sm" value="{{ $knit->description }}"></td>
                                <td><input type="text" name="knitting_desc[supplier][]" class="form-control form-control-sm" value="{{ $knit->supplier }}"></td>
                                <td><input type="number" step="any" name="knitting_desc[qty][]" class="form-control form-control-sm calc" value="{{ (int)$knit->qty }}"></td>
                                <td><input type="number" step="any" name="knitting_desc[unit_price][]" class="form-control form-control-sm calc" value="{{ $knit->unit_price }}"></td>
                                <td><input type="number" step="any" name="knitting_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly value="{{ $knit->ttl_usd }}"></td>
                                <td><input type="number" step="any" name="knitting_desc[item_total][]" class="form-control form-control-sm item_total" readonly value="{{ $knit->item_total }}" @if($index != $budget->knittings->count() - 1) style="display:none;" @endif></td>
                                <td><input type="number" step="any" name="knitting_desc[percent][]" class="form-control form-control-sm" readonly value="{{ $knit->percent }}" @if($index != $budget->knittings->count() - 1) style="display:none;" @endif></td>
                                <td><input type="text" name="knitting_desc[company_name][]" class="form-control form-control-sm" value="{{ $knit->company_name }}" @if($index != $budget->knittings->count() - 1) style="display:none;" @endif></td>
                                <td><input type="text" step="any" name="knitting_desc[payment_value][]" class="form-control form-control-sm" value="{{ $knit->payment_value }}" @if($index != $budget->knittings->count() - 1) style="display:none;" @endif></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="knitRow">
                            <td><input type="text" name="knitting_desc[description][]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="knitting_desc[supplier][]" class="form-control form-control-sm"></td>
                            <td><input type="number" step="any" name="knitting_desc[qty][]" class="form-control form-control-sm calc"></td>
                            <td><input type="number" step="any" name="knitting_desc[unit_price][]" class="form-control form-control-sm calc"></td>
                            <td><input type="number" step="any" name="knitting_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                            <td><input type="number" step="any" name="knitting_desc[item_total][]" class="form-control form-control-sm item_total" readonly ></td>
                            <td><input type="number" step="any" name="knitting_desc[percent][]" class="form-control form-control-sm" readonly></td>
                            <td><input type="text" name="knitting_desc[company_name][]" class="form-control form-control-sm"></td>
                            <td><input type="text" step="any" name="knitting_desc[payment_value][]" class="form-control form-control-sm"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10">
                            <button type="button" class="btn btn-sm btn-primary" id="addKnitRow">+ Add Knitting</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
