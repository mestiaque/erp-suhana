
<table class="table table-bordered table-sm" id="summaryTable">
    <tbody>
        <!-- 1. Total Value of Order -->
        <tr class="bot">
            <th class="vm bol">TOTAL VALUE OF ORDER</th>
            <td>
                {{ $budget->pi_value ?? 0 }}
            </td>

            <th class="text-right vm">Total Expenditure</th>
            <td>
                {{ $budget->summary->total_expenditure ?? 0 }}
            </td>

            <td>
                {{ old('summary.expenditure_percent', $budget->summary->expenditure_percent ?? 0.00) }}%
            </td>
        </tr>

        <!-- 2. Reservation -->
        <tr>
            <th colspan="3" class="text-right vm bol">Reservation</th>
            <td colspan="2">
                {{-- Logic usually: PI Value - Total Expenditure --}}
                {{ ($budget->pi_value ?? 0) - ($budget->summary->total_expenditure ?? 0) }}
            </td>
        </tr>

        <!-- 3. BTB -->
        <tr>
            <td class="bol">BTB</td>
            <td class="btb_percent_cell" style="vertical-align: middle">
                {{ $budget->summary->btb_percent ?? 0 }}% = {{ $budget->summary->btb_value ?? 0 }}
            </td>
            <th rowspan="2" class="text-right vm">BBLC</th>
            <td rowspan="4" colspan="2" class="bbcl_detail_cell">
                <div class="mb-1">{{ $budget->summary->bbcl_yarn_dyeing_print_access ?? 0 }} Yarn, Dyeing, Print & Acces</div>
                <div>{{ $budget->summary->bbcl_knitting ?? 0 }} Knitting</div>
            </td>
        </tr>

        <!-- 4. CASH -->
        <tr class="bob">
            <td class="vm bol">CASH</td>
            <td class="cash_percent_cell">
                {{ $budget->summary->cash_percent ?? 0 }}% = {{ $budget->summary->cash_value ?? 0 }}
            </td>
        </tr>
    </tbody>
</table>


<div class="no-page-break">
    <p style="font-size:10px">Production Cost</p>
    <table class="table table-bordered table-sm" id="productionCostTable">
        <thead>
            <tr class="bot">
                <th class="bol">Item</th>
                <th>Machine/Use</th>
                <th>O/Cost ($)</th>
                <th>Total Cost ($)</th>
                <th>Product/Day</th>
                <th class="bor">CM/Doz ($)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bob">
                <td class="bol">{{ $budget->productionCosts->item ?? '' }}</td>
                <td>{{ $budget->productionCosts->machine_use ?? '' }}</td>
                <td>{{ $budget->productionCosts->ocost ?? '' }}</td>
                <td>{{ $budget->productionCosts->total_cost ?? '' }}</td>
                <td>{{ $budget->productionCosts->product_day ?? '' }}</td>
                <td class="bor">{{ $budget->productionCosts->cm_doz ?? '' }}</td>
            </tr>
        </tbody>
    </table>
</div>
