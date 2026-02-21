<div class="row mb-4">
    <div class="col-lg-12">
        <h5>Accessories</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="accessoriesTable">
                <thead>
                    <tr>
                        <th>Description</th><th>Creditor</th><th>Qty</th><th>Unit Price ($)</th><th>TTL US $</th><th>Item Wise Total Value</th><th>%</th><th>Company Name</th><th>Payment Value</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $accessories = ['BENGAL TAIMS','Main Label','Size Label','Care Label','Hangtag & Price Tag','Sew/Thread','Poly 8 Pcs/Bilster','Woven Tap','7PLY CTN 80Pcs','Gum/Others'];
                    @endphp
                    @foreach($accessories as $item)
                        @php $existingAccessory = isset($budget) ? $budget->accessories->firstWhere('description',$item) : null; @endphp
                        <tr class="accessoryRow">
                            <td><input type="text" name="accessories_desc[description][]" class="form-control form-control-sm" value="{{ $existingAccessory->description ?? $item }}"></td>
                            <td><input type="text" name="accessories_desc[supplier][]" class="form-control form-control-sm" value="{{ $existingAccessory->supplier ?? '' }}"></td>
                            <td><input type="number" step="any" name="accessories_desc[qty][]" class="form-control form-control-sm calc pi_total_qty" value="{{ (int) ($existingAccessory->qty ?? $budget->total_qty ?? 0) }}"></td>
                            <td><input type="number" step="any" name="accessories_desc[unit_price][]" class="form-control form-control-sm calc" value="{{ $existingAccessory->unit_price ?? '' }}"></td>
                            <td><input type="number" step="any" name="accessories_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly value="{{ $existingAccessory->ttl_usd ?? '' }}"></td>
                            <td><input type="number" step="any" name="accessories_desc[item_total][]" class="form-control form-control-sm item_total" readonly value="{{ $existingAccessory->item_total ?? '' }}"></td>
                            <td><input type="number" step="any" name="accessories_desc[percent][]" class="form-control form-control-sm" readonly value="{{ $existingAccessory->percent ?? '' }}"></td>
                            <td><input type="text" name="accessories_desc[company_name][]" class="form-control form-control-sm" value="{{ $existingAccessory->company_name ?? '' }}"></td>
                            <td><input type="text" name="accessories_desc[payment_value][]" class="form-control form-control-sm" value="{{ $existingAccessory->payment_value ?? '' }}"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="10"><button type="button" class="btn btn-sm btn-primary" id="addAccessoriesRow"> + Add Accessories </button></td></tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
