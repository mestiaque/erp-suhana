@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Policy') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Add Policy</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.policy.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., Late Fine Policy" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-control" required id="policyType">
                        <optgroup label="Deduction Policies">
                            <option value="late_deduction_per_minute">Late Deduction (Per Minute)</option>
                            <option value="late_deduction_fixed">Late Deduction (Fixed Amount)</option>
                            <option value="absent_deduction_percentage">Absent Deduction (% of Daily Salary)</option>
                            <option value="late_count_for_absent">Late Count for 1 Absent (e.g., 3 late = 1 absent)</option>
                        </optgroup>
                        <optgroup label="Time Settings">
                            <option value="grace_time_minutes">Grace Time (Minutes before Late)</option>
                            <option value="late_threshold_minutes">Late Threshold (Minutes)</option>
                            <option value="working_hours_per_day">Working Hours Per Day</option>
                        </optgroup>
                        <optgroup label="Overtime & Allowances">
                            <option value="overtime_rate_general">Overtime Rate (General - Per Hour)</option>
                            <option value="overtime_rate_special">Overtime Rate (Special - Per Hour)</option>
                        </optgroup>
                        <optgroup label="Fund & Tax">
                            <option value="provident_fund_percentage">Provident Fund (%)</option>
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Value <span class="text-danger">*</span></label>
                    <input type="number" name="value" class="form-control" step="0.01" placeholder="Enter value" required>
                    <small class="text-muted" id="valueHint">Enter the numeric value for this policy</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Unit <span class="text-danger">*</span></label>
                    <select name="unit" class="form-control" required id="policyUnit">
                        <option value="amount">Amount (৳)</option>
                        <option value="percentage">Percentage (%)</option>
                        <option value="hours">Hours</option>
                        <option value="minutes">Minutes</option>
                        <option value="count">Count</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Describe this policy..."></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Help Section -->
            <div class="alert alert-info mt-3">
                <strong>Policy Types Explained:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>Late Deduction Per Minute:</strong> Amount deducted for each minute of late arrival</li>
                    <li><strong>Late Deduction Fixed:</strong> Fixed amount deducted per late occurrence</li>
                    <li><strong>Absent Deduction %:</strong> Percentage of daily salary deducted per absent day</li>
                    <li><strong>Late Count for Absent:</strong> Number of lates that equal 1 absent (e.g., 3 lates = 1 absent worth of deduction)</li>
                    <li><strong>Grace Time:</strong> Minutes allowed before marking as late (e.g., 10 minutes)</li>
                    <li><strong>Overtime Rates:</strong> Payment per hour for overtime work</li>
                </ul>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>
            <a href="{{ route('admin.policy.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

<script>
document.getElementById('policyType').addEventListener('change', function() {
    var unit = document.getElementById('policyUnit');
    var hint = document.getElementById('valueHint');

    switch(this.value) {
        case 'late_deduction_per_minute':
        case 'late_deduction_fixed':
        case 'overtime_rate_general':
        case 'overtime_rate_special':
            unit.value = 'amount';
            hint.textContent = 'Enter amount in Taka (৳)';
            break;
        case 'absent_deduction_percentage':
        case 'provident_fund_percentage':
            unit.value = 'percentage';
            hint.textContent = 'Enter percentage (e.g., 100 for 100%)';
            break;
        case 'grace_time_minutes':
        case 'late_threshold_minutes':
            unit.value = 'minutes';
            hint.textContent = 'Enter time in minutes';
            break;
        case 'working_hours_per_day':
            unit.value = 'hours';
            hint.textContent = 'Enter working hours (e.g., 8)';
            break;
        case 'late_count_for_absent':
            unit.value = 'count';
            hint.textContent = 'Enter number of lates (e.g., 3)';
            break;
    }
});
</script>
@endsection
