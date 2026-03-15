@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Job Card') }}</title>
@endsection

@push('css')
<style>
    @media print {
        .no-print { display: none !important; }
        .card { border: none !important; box-shadow: none !important; margin: 0 !important; }
        .card-body { padding: 5px !important; }
        body { font-size: 12px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        table { font-size: 10px; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: none !important; }
        @page { margin: 0.5cm; }
    }
    .job-card-table th, .job-card-table td {
        text-align: center;
        vertical-align: middle;
        padding: 4px !important;
    }
    .status-P { background: #d4edda; }
    .status-LT { background: #fff3cd; }
    .status-A { background: #f8d7da; }
    .status-L { background: #cce5ff; }
    .status-H { background: #d1ecf1; }
    .status-WO { background: #e2e3e5; }
    .employee-photo {
        width: 120px;
        height: 140px;
        object-fit: cover;
        border: 2px solid #ddd;
        border-radius: 4px;
    }
</style>
@endpush

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <!-- Filter Section -->
    <div class="card no-print">
        <div class="card-header">
            <h5 class="mb-0">Job Card</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.jobcard.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label>Select Employee</label>
                    <select name="user_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Select Employee</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} [{{ $user->employee_id ?? $user->id }}]</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Select Month</label>
                    <input type="month" name="month" class="form-control" value="{{ $month }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-2" style="margin-top: 28px;">
                    <button type="submit" class="btn btn-primary">View Job Card</button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedUser && $summary)
    <!-- Job Card Header -->
    <div class="card" id="printableArea">
        <div class="card-body">
            <div class="text-center mb-3">
                <h4 style="margin-bottom: 5px;"><strong> JOB CARD </strong></h4>
                <p style="margin: 0;">Employee Information & Monthly Attendance Report</p>
            </div>

            <!-- Employee Basic Info with Photo -->
            <table class="table table-bordered table-sm" style="margin-bottom: 15px;">
                <tr>
                    <td width="15%" rowspan="5" class="text-center">
                        @if($selectedUser->photo)
                            <img src="{{ asset($selectedUser->photo) }}" alt="Photo" class="employee-photo">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" alt="Photo" class="employee-photo">
                        @endif
                    </td>
                    <td width="20%"><strong>Employee Name:</strong></td>
                    <td width="25%">{{ $selectedUser->name }}</td>
                    <td width="15%"><strong>Employee ID:</strong></td>
                    <td width="25%">{{ $selectedUser->employee_id ?? $selectedUser->id }}</td>
                </tr>
                <tr>
                    <td><strong>Department:</strong></td>
                    <td>{{ $selectedUser->department ? $selectedUser->department->name : 'N/A' }}</td>
                    <td><strong>Designation:</strong></td>
                    <td>{{ $selectedUser->designation ? $selectedUser->designation->name : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Join Date:</strong></td>
                    <td>{{ $selectedUser->joining_date ? \Carbon\Carbon::parse($selectedUser->joining_date)->format('d M Y') : 'N/A' }}</td>
                    <td><strong>Shift:</strong></td>
                    <td>{{ $selectedUser->shift ? $selectedUser->shift->name : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Line Number:</strong></td>
                    <td>{{ $selectedUser->line ? $selectedUser->line->name : 'N/A' }}</td>
                    <td><strong>Grade:</strong></td>
                    <td>{{ $selectedUser->grade ? $selectedUser->grade->name : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Section:</strong></td>
                    <td>{{ $selectedUser->section ? $selectedUser->section->name : 'N/A' }}</td>
                    <td><strong>Division:</strong></td>
                    <td>{{ $selectedUser->divisionData ? $selectedUser->divisionData->name : 'N/A' }}</td>
                </tr>
            </table>

            <!-- Contact Information -->
            <table class="table table-bordered table-sm" style="margin-bottom: 15px;">
                <tr>
                    <th colspan="4" class="table-secondary text-center">Contact Information</th>
                </tr>
                <tr>
                    <td width="25%"><strong>Mobile:</strong></td>
                    <td width="25%">{{ $selectedUser->mobile ?? 'N/A' }}</td>
                    <td width="25%"><strong>Email:</strong></td>
                    <td width="25%">{{ $selectedUser->email ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Address:</strong></td>
                    <td colspan="3">
                        {{ $selectedUser->address_line1 ?? '' }} 
                        {{ $selectedUser->address_line2 ? ', ' . $selectedUser->address_line2 : '' }}
                        {{ $selectedUser->city ? ', ' . $selectedUser->city : '' }}
                        {{ $selectedUser->district ? ', ' . $selectedUser->district : '' }}
                        {{ $selectedUser->divisionData && $selectedUser->divisionData->name ? ', ' . $selectedUser->divisionData->name : '' }}
                        {{ $selectedUser->country ? ', ' . $selectedUser->country : '' }}
                    </td>
                </tr>
            </table>

            <!-- Salary Information -->
            <table class="table table-bordered table-sm" style="margin-bottom: 15px;">
                <tr>
                    <th colspan="6" class="table-secondary text-center">Salary Information</th>
                </tr>
                <tr>
                    <td><strong>Basic Salary:</strong><br>{{ number_format($selectedUser->basic_salary ?? 0, 2) }}</td>
                    <td><strong>House Rent:</strong><br>{{ number_format($selectedUser->house_rent ?? 0, 2) }}</td>
                    <td><strong>Medical Allowance:</strong><br>{{ number_format($selectedUser->medical_allowance ?? 0, 2) }}</td>
                    <td><strong>Transport Allowance:</strong><br>{{ number_format($selectedUser->transport_allowance ?? 0, 2) }}</td>
                    <td><strong>Other Allowance:</strong><br>{{ number_format($selectedUser->other_allowance ?? 0, 2) }}</td>
                    <td><strong>Total Salary:</strong><br>{{ number_format(($selectedUser->basic_salary ?? 0) + ($selectedUser->house_rent ?? 0) + ($selectedUser->medical_allowance ?? 0) + ($selectedUser->transport_allowance ?? 0) + ($selectedUser->other_allowance ?? 0), 2) }}</td>
                </tr>
            </table>

            <!-- Increment History -->
            @if(isset($increments) && $increments->count() > 0)
            <table class="table table-bordered table-sm" style="margin-bottom: 15px;">
                <tr>
                    <th colspan="5" class="table-secondary text-center">Increment History</th>
                </tr>
                <tr>
                    <th>Date</th>
                    <th>Previous Salary</th>
                    <th>Increment Amount</th>
                    <th>New Salary</th>
                    <th>Remarks</th>
                </tr>
                @foreach($increments as $increment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($increment->increment_date)->format('d M Y') }}</td>
                    <td>{{ number_format($increment->previous_salary ?? 0, 2) }}</td>
                    <td>{{ number_format($increment->increment_amount ?? 0, 2) }}</td>
                    <td>{{ number_format($increment->new_salary ?? 0, 2) }}</td>
                    <td>{{ $increment->remarks ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </table>
            @endif

            <!-- Monthly Summary -->
            <table class="table table-bordered table-sm" style="margin-bottom: 15px;">
                <thead>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-center">Attendance Summary - {{ Carbon\Carbon::parse($month)->format('F Y') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Present</strong><br><span class="badge badge-success">{{ $summary['present'] }}</span></td>
                        <td><strong>Late</strong><br><span class="badge badge-warning">{{ $summary['late'] }}</span></td>
                        <td><strong>Absent</strong><br><span class="badge badge-danger">{{ $summary['absent'] }}</span></td>
                        <td><strong>Leave</strong><br><span class="badge badge-primary">{{ $summary['leave'] }}</span></td>
                        <td><strong>Holiday</strong><br><span class="badge badge-info">{{ $summary['holiday'] }}</span></td>
                        <td><strong>Total Work Hrs</strong><br>{{ $summary['total_work_hours'] }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Daily Attendance -->
            <table class="table table-bordered table-sm job-card-table" style="width: 100%;">
                <thead>
                    <tr style="background: #343a40; color: white;">
                        <th>Date</th>
                        <th>Day</th>
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th>Work Hrs</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyData as $day)
                    <tr class="status-{{ $day['status'] }}">
                        <td>{{ $day['date'] }}</td>
                        <td>{{ $day['day'] }}</td>
                        <td>{{ $day['in_time'] }}</td>
                        <td>{{ $day['out_time'] }}</td>
                        <td>{{ $day['work_hours'] }}</td>
                        <td>
                            @if($day['status'] == 'P')
                                <span class="badge badge-success">P</span>
                            @elseif($day['status'] == 'LT')
                                <span class="badge badge-warning">LT</span>
                            @elseif($day['status'] == 'A')
                                <span class="badge badge-danger">A</span>
                            @elseif($day['status'] == 'L')
                                <span class="badge badge-primary">L</span>
                            @elseif($day['status'] == 'H')
                                <span class="badge badge-info">H</span>
                            @elseif($day['status'] == 'WO')
                                <span class="badge badge-secondary">WO</span>
                            @endif
                        </td>
                        <td></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Status Legend -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <strong>Status Key:</strong>
                    <span class="badge badge-success">P = Present</span>
                    <span class="badge badge-warning">LT = Late</span>
                    <span class="badge badge-danger">A = Absent</span>
                    <span class="badge badge-primary">L = Leave</span>
                    <span class="badge badge-info">H = Holiday</span>
                    <span class="badge badge-secondary">WO = Weekly Off</span>
                </div>
            </div>

            <!-- Signatures -->
            <div class="row mt-4" style="margin-top: 30px;">
                <div class="col-md-4 text-center">
                    <p>__________________________<br>Employee Signature</p>
                </div>
                <div class="col-md-4 text-center">
                    <p>__________________________<br>Supervisor Signature</p>
                </div>
                <div class="col-md-4 text-center">
                    <p>__________________________<br>HR/Admin Signature</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="text-center no-print mt-3">
        @if($selectedUser && $summary)
        <a href="{{ route('admin.jobcard.print', ['user_id' => $selectedUser->id, 'month' => $month]) }}" target="_blank" class="btn btn-primary">
            <i class="fa fa-print"></i> Print Job Card
        </a>
        @endif
    </div>
    @else
    <div class="card">
        <div class="card-body text-center">
            <p class="text-muted">Select an employee to view their job card</p>
        </div>
    </div>
    @endif

</div>
@endsection
