@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Policy') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Policy Settings</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.policy.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Unit</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($policies as $index => $policy)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $policy->name }}</td>
                        <td>
                            @php
                                $typeLabels = [
                                    'late_deduction_per_minute' => 'Late Deduction (Per Minute)',
                                    'late_deduction_fixed' => 'Late Deduction (Fixed)',
                                    'absent_deduction_percentage' => 'Absent Deduction (%)',
                                    'late_count_for_absent' => 'Late Count for 1 Absent',
                                    'grace_time_minutes' => 'Grace Time (Minutes)',
                                    'overtime_rate_general' => 'Overtime Rate (General)',
                                    'overtime_rate_special' => 'Overtime Rate (Special)',
                                    'provident_fund_percentage' => 'Provident Fund (%)',
                                    'working_hours_per_day' => 'Working Hours/Day',
                                    'late_threshold_minutes' => 'Late Threshold (Minutes)',
                                    'late_fine' => 'Late Fine',
                                    'absent_fine' => 'Absent Fine',
                                    'overtime_rate' => 'Overtime Rate',
                                    'working_hour' => 'Working Hour',
                                ];
                            @endphp
                            <span class="badge bg-info">{{ $typeLabels[$policy->type] ?? ucfirst(str_replace('_', ' ', $policy->type)) }}</span>
                        </td>
                        <td class="text-end">{{ number_format($policy->value, 2) }}</td>
                        <td>{{ ucfirst($policy->unit) }}</td>
                        <td>{{ $policy->description ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $policy->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($policy->status) }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal{{ $policy->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.policy.destroy', $policy->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $policy->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.policy.update', $policy->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Policy</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $policy->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Type</label>
                                            <select name="type" class="form-control" required>
                                                <optgroup label="Deduction Policies">
                                                    <option value="late_deduction_per_minute" {{ $policy->type == 'late_deduction_per_minute' ? 'selected' : '' }}>Late Deduction (Per Minute)</option>
                                                    <option value="late_deduction_fixed" {{ $policy->type == 'late_deduction_fixed' ? 'selected' : '' }}>Late Deduction (Fixed)</option>
                                                    <option value="absent_deduction_percentage" {{ $policy->type == 'absent_deduction_percentage' ? 'selected' : '' }}>Absent Deduction (%)</option>
                                                    <option value="late_count_for_absent" {{ $policy->type == 'late_count_for_absent' ? 'selected' : '' }}>Late Count for 1 Absent</option>
                                                </optgroup>
                                                <optgroup label="Time Settings">
                                                    <option value="grace_time_minutes" {{ $policy->type == 'grace_time_minutes' ? 'selected' : '' }}>Grace Time (Minutes)</option>
                                                    <option value="late_threshold_minutes" {{ $policy->type == 'late_threshold_minutes' ? 'selected' : '' }}>Late Threshold (Minutes)</option>
                                                    <option value="working_hours_per_day" {{ $policy->type == 'working_hours_per_day' ? 'selected' : '' }}>Working Hours Per Day</option>
                                                </optgroup>
                                                <optgroup label="Overtime & Allowances">
                                                    <option value="overtime_rate_general" {{ $policy->type == 'overtime_rate_general' ? 'selected' : '' }}>Overtime Rate (General)</option>
                                                    <option value="overtime_rate_special" {{ $policy->type == 'overtime_rate_special' ? 'selected' : '' }}>Overtime Rate (Special)</option>
                                                </optgroup>
                                                <optgroup label="Fund & Tax">
                                                    <option value="provident_fund_percentage" {{ $policy->type == 'provident_fund_percentage' ? 'selected' : '' }}>Provident Fund (%)</option>
                                                </optgroup>
                                                <optgroup label="Legacy Types">
                                                    <option value="late_fine" {{ $policy->type == 'late_fine' ? 'selected' : '' }}>Late Fine</option>
                                                    <option value="absent_fine" {{ $policy->type == 'absent_fine' ? 'selected' : '' }}>Absent Fine</option>
                                                    <option value="overtime_rate" {{ $policy->type == 'overtime_rate' ? 'selected' : '' }}>Overtime Rate</option>
                                                    <option value="working_hour" {{ $policy->type == 'working_hour' ? 'selected' : '' }}>Working Hour</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Value</label>
                                            <input type="number" name="value" class="form-control" step="0.01" value="{{ $policy->value }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Unit</label>
                                            <select name="unit" class="form-control" required>
                                                <option value="amount" {{ $policy->unit == 'amount' ? 'selected' : '' }}>Amount</option>
                                                <option value="percentage" {{ $policy->unit == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                                <option value="hours" {{ $policy->unit == 'hours' ? 'selected' : '' }}>Hours</option>
                                                <option value="minutes" {{ $policy->unit == 'minutes' ? 'selected' : '' }}>Minutes</option>
                                                <option value="count" {{ $policy->unit == 'count' ? 'selected' : '' }}>Count</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control" rows="2">{{ $policy->description }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label>Status</label>
                                            <select name="status" class="form-control">
                                                <option value="active" {{ $policy->status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $policy->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="8" class="text-center">No policies found. <a href="{{ route('admin.policy.create') }}">Add first policy</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Policy Summary Card -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-header"><strong>Policy Rules Summary</strong></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Late Deduction:</strong> ৳{{ \App\Models\Policy::getLateDeduction(1) }} per minute / ৳{{ \App\Models\Policy::getValue('late_deduction_fixed', 0) }} fixed</p>
                                <p><strong>Grace Time:</strong> {{ \App\Models\Policy::getGraceTimeMinutes() }} minutes</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Absent Deduction:</strong> {{ \App\Models\Policy::getAbsentDeductionPercentage() }}% of daily salary</p>
                                <p><strong>Late Count for Absent:</strong> {{ \App\Models\Policy::getLateCountForAbsent() }} lates = 1 absent</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>OT Rate (General):</strong> ৳{{ \App\Models\Policy::getOvertimeRateGeneral() }}/hour</p>
                                <p><strong>OT Rate (Special):</strong> ৳{{ \App\Models\Policy::getOvertimeRateSpecial() }}/hour</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
