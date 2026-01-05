<div class="row mb-4">
    <div class="col-lg-12">
        <h5>Summary / Total Cost & Payment</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="summaryTable">
                <tbody>
                    {{-- @dd($budget) --}}
                    <!-- 1. Total Value of Order -->
                    <tr>
                        <th class="vm">TOTAL VALUE OF ORDER</th>
                        <td>
                            <input type="number" step="any" name="summary[pi_value]" class="form-control form-control-sm summary_pi_value" value="{{ $budget->total_qty ?? 0 }}" readonly comment="User input for PI value">
                        </td>

                        <th class="text-right vm">Total Expenditure</th>
                        <td>
                            <input type="number" step="any" name="summary[total_expenditure]" class="form-control form-control-sm summary_total_expenditure" value="{{ $budget->summary->total_expenditure ?? 0 }}" readonly comment="Sum of all Item Wise Total Values, calculated by JS">
                        </td>

                        <td>
                            <div class="input-group input-group-sm"><input type="text" name="summary[expenditure_percent]" class="form-control form-control-sm text-right summary_expenditure_percent" value="{{ old('summary.expenditure_percent', 0.00) }}" readonly title="Percentage of PI value calculated from total expenditure"><input class="form-control form-control-sm" value="%" style="max-width: 2rem"  readonly></div>

                        </td>
                    </tr>

                    <!-- 2. Reservation -->
                    <tr>
                        <th colspan="3" class="text-right vm">Reservation</th>
                        <td colspan="2">
                            <input type="number" step="any" name="summary[reservation]" class="form-control form-control-sm summary_reservation" value="0" readonly comment="PI Value minus Total Expenditure, calculated by JS">
                        </td>
                    </tr>

                    <!-- 3. BTB -->
                    <tr>
                        <td>BTB</td>
                        <td class="btb_percent_cell d-flex" style="vertical-align: middle" comment="User input for % of PI value allocated to BTB">
                            <input type="number" step="any" name="summary[btb_percent]" class="form-control form-control-sm btb_percent_input w-50" value="{{ $budget->summary->btb_percent ?? 0 }}">
                            <span type="text" readonly class="form-control form-control-sm" style="width:3rem;background: none; border: none;"> % = </span>
                            <input type="number" step="any" name="summary[btb_value]" class="form-control form-control-sm btb_value w-50" value="{{ $budget->summary->btb_value ?? 0 }}" readonly>
                        </td>
                        <th rowspan="2" class="text-right vm">BBLC</th>
                        <td rowspan="4" colspan="2" class="bbcl_detail_cell">
                            <input type="text" class="form-control form-control-sm bbcl_yarn_dyeing_print_access mb-1" value="0 Yarn, Dyeing, Print & Acces" readonly>
                            <input type="hidden" name="summary[bbcl_yarn_dyeing_print_access]" class="form-control form-control-sm bbcl_yarn_dyeing_print_access_val" value="{{ $budget->summary->bbcl_yarn_dyeing_print_access ?? 0 }}" readonly>
                            <input type="text" class="form-control form-control-sm bbcl_knitting" value="0 Knitting" readonly>
                            <input type="hidden" name="summary[bbcl_knitting]" class="form-control form-control-sm bbcl_knitting_val" value="{{ $budget->summary->bbcl_knitting ?? 0 }}" readonly>
                        </td>
                    </tr>

                    <!-- 4. CASH -->
                    <tr>
                        <td class="vm">CASH</td>
                        <td class="cash_percent_cell d-flex" comment="User input for % of PI value allocated to CASH">
                            <input type="number" step="any" name="summary[cash_percent]" class="form-control form-control-sm cash_percent_input w-50" value="{{ $budget->summary->cash_percent ?? 0 }}">
                            <span type="text" readonly class="form-control form-control-sm" style="width:3rem;background: none; border: none;"> % = </span>
                            <input type="number" step="any" name="summary[cash_value]" class="form-control form-control-sm cash_value w-50" value="{{ $budget->summary->cash_value ?? 0 }}" readonly>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
