<div class="row mb-4">
    <div class="col-lg-12">
        <h5>Others</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="testTable">
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

                    {{-- ================= 1. TEST ================= --}}
                    <tr class="testRow">
                        <td colspan="10">TEST</td>
                    </tr>

                    @php
                        $test = (isset($budget) && isset($budget->tests))
                            ? $budget->tests->where('key', 'test')->first()
                            : null;
                    @endphp
                    <tr class="testRow">
                        <td><input type="text" name="test_desc[test][]" class="form-control form-control-sm" value="{{ isset($test) ? $test->description : 'TEST' }}"></td>
                        <td><input type="text" name="test_desc[test_supplier][]" class="form-control form-control-sm" value="{{ isset($test) ? $test->supplier : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[test_qty][]" class="form-control form-control-sm calc" value="{{ isset($test) ? $test->qty : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[test_unit_price][]" class="form-control form-control-sm calc" value="{{ isset($test) ? $test->unit_price : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[test_ttl_usd][]" class="form-control form-control-sm ttl_usd" value="{{ isset($test) ? $test->ttl_usd : '' }}" readonly></td>
                        <td><input type="number" step="any" name="test_desc[test_item_total][]" class="form-control form-control-sm item_total" value="{{ isset($test) ? $test->item_total : '' }}" readonly></td>
                        <td><input type="number" step="any" name="test_desc[test_percent][]" class="form-control form-control-sm testPer" value="{{ isset($test) ? $test->percent : '' }}" readonly></td>
                        <td><input type="text" name="test_desc[test_company][]" class="form-control form-control-sm" value="{{ isset($test) ? $test->company_name : '' }}"></td>
                        <td><input type="text" step="any" name="test_desc[test_payment_value][]" class="form-control form-control-sm" value="{{ isset($test) ? $test->payment_value : '' }}"></td>
                        <td></td>
                    </tr>


                    {{-- ================= 2. BUYING COMMISSION ================= --}}
                    <tr class="testRow">
                        <td colspan="10">Buying Commission (Pcs)</td>
                    </tr>

                    @php
                        $buying = (isset($budget) && isset($budget->tests))
                            ? $budget->tests->where('key', 'buying_commission')->first()
                            : null;
                    @endphp
                    <tr class="testRow">
                        <td><input type="text" step="any" name="test_desc[buying_commission][]" class="form-control form-control-sm" value="{{ isset($buying) ? $buying->description : 'Buying Commission (Pcs)' }}"></td>
                        <td><input type="text" name="test_desc[buying_commission_supplier][]" class="form-control form-control-sm" value="{{ isset($buying) ? $buying->supplier : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[buying_commission_qty][]" class="form-control form-control-sm" value="{{ isset($buying) ? $buying->qty : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[buying_commission_unit_price][]" class="form-control form-control-sm" value="{{ isset($buying) ? $buying->unit_price : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[buying_commission_ttl_usd][]" class="form-control form-control-sm ttl_usd" value="{{ isset($buying) ? $buying->ttl_usd : '' }}" readonly></td>
                        <td><input type="number" step="any" name="test_desc[buying_commission_item_total][]" class="form-control form-control-sm item_total" value="{{ isset($buying) ? $buying->item_total : '' }}" readonly></td>
                        <td><input type="number" step="any" name="test_desc[buying_commission_percent][]" class="form-control form-control-sm testPer" value="{{ isset($buying) ? $buying->percent : '' }}" readonly></td>
                        <td><input type="text" name="test_desc[buying_commission_company][]" class="form-control form-control-sm" value="{{ isset($buying) ? $buying->company_name : '' }}"></td>
                        <td><input type="text" step="any" name="test_desc[buying_commission_payment_value][]" class="form-control form-control-sm" value="{{ isset($buying) ? $buying->payment_value : '' }}"></td>
                        <td></td>
                    </tr>


                    {{-- ================= 3. LOCAL TRANSPORTATION ================= --}}
                    <tr class="testRow">
                        <td colspan="10">LOCAL TRANSPORTATION</td>
                    </tr>

                    @php
                        $local = (isset($budget) && isset($budget->tests))
                            ? $budget->tests->where('key', 'local_transportation')->first()
                            : null;
                    @endphp
                    <tr class="testRow">
                        <td><input type="text" step="any" name="test_desc[local_transportation][]" class="form-control form-control-sm" value="{{ isset($local) ? $local->description : 'LOCAL TRANSPORTATION' }}"></td>
                        <td><input type="text" name="test_desc[local_transportation_supplier][]" class="form-control form-control-sm" value="{{ isset($local) ? $local->supplier : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[local_transportation_qty][]" class="form-control form-control-sm" value="{{ isset($local) ? $local->qty : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[local_transportation_unit_price][]" class="form-control form-control-sm" value="{{ isset($local) ? $local->unit_price : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[local_transportation_ttl_usd][]" class="form-control form-control-sm ttl_usd" value="{{ isset($local) ? $local->ttl_usd : '' }}" readonly></td>
                        <td><input type="number" step="any" name="test_desc[local_transportation_item_total][]" class="form-control form-control-sm item_total" value="{{ isset($local) ? $local->item_total : '' }}" readonly></td>
                        <td><input type="number" step="any" name="test_desc[local_transportation_percent][]" class="form-control form-control-sm testPer" value="{{ isset($local) ? $local->percent : '' }}" readonly></td>
                        <td><input type="text" name="test_desc[local_transportation_company][]" class="form-control form-control-sm" value="{{ isset($local) ? $local->company_name : '' }}"></td>
                        <td><input type="text" step="any" name="test_desc[local_transportation_payment_value][]" class="form-control form-control-sm" value="{{ isset($local) ? $local->payment_value : '' }}"></td>
                        <td></td>
                    </tr>


                    {{-- ================= 4. BANK & COMMERCIAL ================= --}}
                    <tr class="testRow">
                        <td colspan="10">BANK & COMMERCIAL</td>
                    </tr>

                    @php
                        $bankCommercial = (isset($budget) && isset($budget->tests))
                            ? $budget->tests->where('key', 'bank_commercial')->first()
                            : null;
                    @endphp
                    <tr class="testRow">
                        <td><input type="text" step="any" name="test_desc[bank_commercial][]" class="form-control form-control-sm" value="{{ isset($bankCommercial) ? $bankCommercial->description : 'BANK & COMMERCIAL' }}"></td>
                        <td><input type="text" name="test_desc[bank_commercial_supplier][]" class="form-control form-control-sm" value="{{ isset($bankCommercial) ? $bankCommercial->supplier : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[bank_commercial_qty][]" class="form-control form-control-sm" value="{{ isset($bankCommercial) ? $bankCommercial->qty : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[bank_commercial_unit_price][]" class="form-control form-control-sm" value="{{ isset($bankCommercial) ? $bankCommercial->unit_price : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[bank_commercial_ttl_usd][]" class="form-control form-control-sm ttl_usd" value="{{ isset($bankCommercial) ? $bankCommercial->ttl_usd : '' }}" readonly></td>
                        <td><input type="number" step="any" name="test_desc[bank_commercial_item_total][]" class="form-control form-control-sm item_total" value="{{ isset($bankCommercial) ? $bankCommercial->item_total : '' }}" readonly></td>
                        <td><input type="number" step="any" name="test_desc[bank_commercial_percent][]" class="form-control form-control-sm testPer" value="{{ isset($bankCommercial) ? $bankCommercial->percent : '' }}" readonly></td>
                        <td><input type="text" name="test_desc[bank_commercial_company][]" class="form-control form-control-sm" value="{{ isset($bankCommercial) ? $bankCommercial->company_name : '' }}"></td>
                        <td><input type="text" step="any" name="test_desc[bank_commercial_payment_value][]" class="form-control form-control-sm" value="{{ isset($bankCommercial) ? $bankCommercial->payment_value : '' }}"></td>
                        <td></td>
                    </tr>


                    {{-- ================= 5. COMMISSION % ================= --}}
                    <tr class="testRow">
                        <td colspan="10">COMMISSION %</td>
                    </tr>

                    @php
                        $commission = (isset($budget) && isset($budget->tests))
                            ? $budget->tests->where('key', 'commission_percent')->first()
                            : null;
                    @endphp
                    <tr class="testRow">
                        <td><input type="text" step="any" name="test_desc[commission_percent][]" class="form-control form-control-sm" value="{{ isset($commission) ? $commission->description : 'COMMISSION %' }}"></td>
                        <td><input type="text" name="test_desc[commission_percent_supplier][]" class="form-control form-control-sm" value="{{ isset($commission) ? $commission->supplier : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[commission_percent_qty][]" class="form-control form-control-sm" value="{{ isset($commission) ? $commission->qty : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[commission_percent_unit_price][]" class="form-control form-control-sm" value="{{ isset($commission) ? $commission->unit_price : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[commission_percent_ttl_usd][]" class="form-control form-control-sm ttl_usd" value="{{ isset($commission) ? $commission->ttl_usd : '' }}" readonly></td>
                        <td><input type="number" step="any" name="test_desc[commission_percent_item_total][]" class="form-control form-control-sm item_total" value="{{ isset($commission) ? $commission->item_total : '' }}" readonly></td>
                        <td><input type="number" step="any" name="test_desc[commission_percent_percent][]" class="form-control form-control-sm testPer" value="{{ isset($commission) ? $commission->percent : '' }}" readonly></td>
                        <td><input type="text" name="test_desc[commission_percent_company][]" class="form-control form-control-sm" value="{{ isset($commission) ? $commission->company_name : '' }}"></td>
                        <td><input type="text" step="any" name="test_desc[commission_percent_payment_value][]" class="form-control form-control-sm" value="{{ isset($commission) ? $commission->payment_value : '' }}"></td>
                        <td></td>
                    </tr>


                    {{-- ================= 6. FREIGHT ================= --}}
                    <tr class="testRow">
                        <td colspan="10">FREIGHT</td>
                    </tr>

                    @php
                        $freight = (isset($budget) && isset($budget->tests))
                            ? $budget->tests->where('key', 'freight')->first()
                            : null;
                    @endphp
                    <tr class="testRow">
                        <td><input type="text" step="any" name="test_desc[freight][]" class="form-control form-control-sm" value="{{ isset($freight) ? $freight->description : 'FREIGHT' }}"></td>
                        <td><input type="text" name="test_desc[freight_supplier][]" class="form-control form-control-sm" value="{{ isset($freight) ? $freight->supplier : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[freight_qty][]" class="form-control form-control-sm" value="{{ isset($freight) ? $freight->qty : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[freight_unit_price][]" class="form-control form-control-sm" value="{{ isset($freight) ? $freight->unit_price : '' }}"></td>
                        <td><input type="number" step="any" name="test_desc[freight_ttl_usd][]" class="form-control form-control-sm ttl_usd" value="{{ isset($freight) ? $freight->ttl_usd : '' }}" readonly></td>
                        <td><input type="number" step="any" name="test_desc[freight_item_total][]" class="form-control form-control-sm item_total" value="{{ isset($freight) ? $freight->item_total : '' }}" readonly></td>
                        <td><input type="number" step="any" name="test_desc[freight_percent][]" class="form-control form-control-sm testPer" value="{{ isset($freight) ? $freight->percent : '' }}" readonly></td>
                        <td><input type="text" name="test_desc[freight_company][]" class="form-control form-control-sm" value="{{ isset($freight) ? $freight->company_name : '' }}"></td>
                        <td><input type="text" step="any" name="test_desc[freight_payment_value][]" class="form-control form-control-sm" value="{{ isset($freight) ? $freight->payment_value : '' }}"></td>
                        <td></td>
                    </tr>

                </tbody>

            </table>
        </div>
    </div>
</div>
