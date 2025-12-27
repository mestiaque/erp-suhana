<div class="row mb-4">
    <div class="col-lg-12">
        <h5>Production Cost</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="productionCostTable">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Machine/Use</th>
                        <th>O/Cost ($)</th>
                        <th>Total Cost ($)</th>
                        <th>Product/Day</th>
                        <th>CM/Doz ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="prod_cost[item][]" class="form-control form-control-sm" value="{{ $budget->productionCosts->item ?? '' }}"></td>
                        <td><input type="number" step="any" name="prod_cost[machine_use][]" class="form-control form-control-sm machine_use" value="{{ $budget->productionCosts->machine_use ?? '' }}"></td>
                        <td><input type="number" step="any" name="prod_cost[ocost][]" class="form-control form-control-sm o_cost" value="{{ $budget->productionCosts->ocost ?? '' }}"></td>
                        <td><input type="number" step="any" name="prod_cost[total_cost][]" class="form-control form-control-sm production_total_cost" readonly value="{{ $budget->productionCosts->total_cost ?? '' }}" ></td>
                        <td><input type="text" name="prod_cost[product_day][]" class="form-control form-control-sm production_day" value="{{ $budget->productionCosts->product_day ?? '' }}"></td>
                        <td><input type="number" step="any" name="prod_cost[cm_doz][]" class="form-control form-control-sm cm_doz" value="{{ $budget->productionCosts->cm_doz ?? '' }}" ></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
