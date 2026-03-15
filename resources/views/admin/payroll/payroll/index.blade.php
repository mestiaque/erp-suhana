@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Payroll Management') }}</title>
@endsection

@push('css')
<style>
    .payroll-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .stat-box { padding: 20px; border-radius: 8px; text-align: center; }
    table.table thead { background: #6c5ce7; color: white; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Payroll Management</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Payroll</li>
        <li class="item">Dashboard</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <!-- Stats -->
    <div class="row mb-3">
        <div class="col-md-2">
            <div class="stat-box bg-primary text-white">
                <h3>{{ $summary['total_employees'] ?? 0 }}</h3>
                <p class="mb-0">Total Employees</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-success text-white">
                <h5>৳{{ number_format($summary['total_gross'] ?? 0, 0) }}</h5>
                <p class="mb-0">Total Gross</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-box bg-danger text-white">
                <h5>৳{{ number_format($summary['total_deduction'] ?? 0, 0) }}</h5>
                <p class="mb-0">Deduction</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-info text-white">
                <h4>৳{{ number_format($summary['total_net'] ?? 0, 0) }}</h4>
                <p class="mb-0">Total Net</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-box bg-warning text-white">
                <h3>{{ $summary['paid_count'] ?? 0 }}/{{ $summary['pending_count'] ?? 0 }}</h3>
                <p class="mb-0">Paid/Pending</p>
            </div>
        </div>
    </div>

    <!-- Process Salary -->
    <div class="payroll-card">
        <h5 class="mb-3"><i class="bx bx-calculator"></i> Process Monthly Salary</h5>
        <form action="{{ route('admin.payroll.processGet') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <label>Select Month</label>
                <select name="month" class="form-control" required>
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label>Select Year</label>
                <select name="year" class="form-control" required>
                    @php
                        $currentYear = date('Y');
                    @endphp

                    @for ($y = 2000; $y <= 2099; $y++)
                        <option value="{{ $y }}"
                            {{ ($year ?? $currentYear) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="reprocess" id="reprocess" value="1">
                    <label class="form-check-label" for="reprocess">
                        Reprocess
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-success w-100">
                    <i class="bx bx-play-circle"></i> Process
                </button>
            </div>
        </form>
    </div>

    <!-- Filter -->
    <div class="payroll-card">
        <form action="{{ route('admin.payroll.index') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Month</label>
                <select name="month" class="form-control">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label>Year</label>
                <select name="year" class="form-control">
                    @php
                        $currentYear = date('Y');
                    @endphp

                    @for ($y = 2000; $y <= 2099; $y++)
                        <option value="{{ $y }}"
                            {{ ($year ?? $currentYear) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label>Department</label>
                <select name="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Basic Salary</th>
                        <th>Allowances</th>
                        <th>Gross</th>
                        <th>Deductions</th>
                        <th>Net Salary</th>
                        {{-- <th>Status</th> --}}
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaries as $i => $salary)
                    @php
                        $user = $salary->user;
                        $empInfo = $user->employeeInfo ?? null;
                        $allowances = ($salary->house_rent ?? 0) + ($salary->medical_allowance ?? 0) +
                                     ($salary->transport_allowance ?? 0) + ($salary->other_allowance ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $empInfo->employee_id ?? 'N/A' }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user && $user->photo)
                                    <img src="{{ asset('uploads/user_photo/' . $user->photo) }}" alt="{{ $user->name }}" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover; margin-right: 8px;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 30px; height: 30px; background-color: {{ random_color($user->id ?? 0) }}; margin-right: 8px; flex-shrink: 0;">
                                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ $user->name ?? '' }}</span>
                            </div>
                        </td>
                        <td>{{ $empInfo->department->name ?? 'N/A' }}</td>
                        <td>৳{{ number_format($salary->basic_salary ?? 0, 2) }}</td>
                        <td>৳{{ number_format($allowances, 2) }}</td>
                        <td>৳{{ number_format($salary->gross_salary ?? 0, 2) }}</td>
                        <td class="text-danger">৳{{ number_format($salary->total_deduction ?? 0, 2) }}</td>
                        <td><strong>৳{{ number_format($salary->net_salary ?? 0, 2) }}</strong></td>
                        {{-- <td>
                            @if($salary->payment_status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($salary->payment_status == 'held')
                                <span class="badge bg-danger">Held</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td> --}}
                        <td>
                            <a href="{{ route('admin.payroll.paySlip', $salary->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                <i class="bx bx-receipt"></i> Pay Slip
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No payroll data available. Please process salary first.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($salaries->hasPages())
            <div class="mt-3">
                {{ $salaries->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

@endsection
