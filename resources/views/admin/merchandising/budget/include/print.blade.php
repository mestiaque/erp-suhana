<div class="row mb-4">
    <div class="col-lg-12">
        <h5>Print & Embroidery</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="printEmbroideryTable">
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
                    @if(isset($budget) && $budget->printEmbroidery->count() > 0)
                        @foreach($budget->printEmbroidery as $index => $printEmb)
                            <tr class="printEmbRow">
                                <td><input type="text" name="print_emb_desc[description][]" class="form-control form-control-sm" value="{{ $printEmb->description }}"></td>
                                <td><input type="text" name="print_emb_desc[supplier][]" class="form-control form-control-sm" value="{{ $printEmb->supplier }}"></td>
                                <td><input type="number" step="any" name="print_emb_desc[qty][]" class="form-control form-control-sm calc" value="{{ (int)$printEmb->qty }}"></td>
                                <td><input type="number" step="any" name="print_emb_desc[unit_price][]" class="form-control form-control-sm calc" value="{{ $printEmb->unit_price }}"></td>
                                <td><input type="number" step="any" name="print_emb_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly value="{{ $printEmb->ttl_usd }}"></td>
                                <td><input type="number" step="any" name="print_emb_desc[item_total][]" class="form-control form-control-sm item_total" readonly value="{{ $printEmb->item_total }}" @if($index != $budget->printEmbroidery->count() - 1) style="display:none;" @endif></td>
                                <td><input type="number" step="any" name="print_emb_desc[percent][]" class="form-control form-control-sm" readonly value="{{ $printEmb->percent }}" @if($index != $budget->printEmbroidery->count() - 1) style="display:none;" @endif></td>
                                <td><input type="text" name="print_emb_desc[company_name][]" class="form-control form-control-sm" value="{{ $printEmb->company_name }}" @if($index != $budget->printEmbroidery->count() - 1) style="display:none;" @endif></td>
                                <td><input type="text" step="any" name="print_emb_desc[payment_value][]" class="form-control form-control-sm" value="{{ $printEmb->payment_value }}" @if($index != $budget->printEmbroidery->count() - 1) style="display:none;" @endif></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="printEmbRow">
                            <td><input type="text" name="print_emb_desc[description][]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="print_emb_desc[supplier][]" class="form-control form-control-sm"></td>
                            <td><input type="number" step="any" name="print_emb_desc[qty][]" class="form-control form-control-sm calc"></td>
                            <td><input type="number" step="any" name="print_emb_desc[unit_price][]" class="form-control form-control-sm calc"></td>
                            <td><input type="number" step="any" name="print_emb_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                            <td><input type="number" step="any" name="print_emb_desc[item_total][]" class="form-control form-control-sm item_total" readonly></td>
                            <td><input type="number" step="any" name="print_emb_desc[percent][]" class="form-control form-control-sm" readonly></td>
                            <td><input type="text" name="print_emb_desc[company_name][]" class="form-control form-control-sm"></td>
                            <td><input type="text" step="any" name="print_emb_desc[payment_value][]" class="form-control form-control-sm"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button></td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10">
                            <button type="button" class="btn btn-sm btn-primary" id="addPrintEmbRow">+ Add Row</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
