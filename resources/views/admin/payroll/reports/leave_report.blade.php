@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Leave Report') }}</title>
@endsection

@push('css')
<style>

</style>
@endpush

@section('contents')

@include(adminTheme().'alerts')

<div class="flex-grow-1">
        {{-- Summary Stats --}}
    @php
        $pendingCount = $leaves->total() > 0 ? $leaves->where('status', 'pending')->count() : 0;
        $approvedCount = $leaves->total() > 0 ? $leaves->where('status', 'approved')->count() : 0;
        $rejectedCount = $leaves->total() > 0 ? $leaves->where('status', 'rejected')->count() : 0;
    @endphp

        <!-- Stats -->
    <div class="row mb-3">
        {{-- Employees --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background: #007bff38">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-clipboard-list"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $leaves->total() }}</h4>
                        <small class="text-muted">Total Leaves</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Approved Leaves --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background: #28a74538">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $approvedCount ?? 0 }}</h5>
                        <small class="text-muted">Approved Leaves</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Leaves --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background: #ffc10738">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-hourglass-half"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $pendingCount ?? 0 }}</h5>
                        <small class="text-muted">Pending Leaves</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rejected Leaves --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background: #dc354538">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-times-circle"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $rejectedCount ?? 0 }}</h5>
                        <small class="text-muted">Rejected Leaves</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        {{-- Filter Form --}}
        <form action="{{ route('admin.leaves.report') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-2">
                <label>Month</label>
                <select name="month" class="form-control form-control-sm">
                    <option value="">All Months</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label>Year</label>
                <input type="number" name="year" class="form-control form-control-sm" value="{{ request('year', date('Y')) }}" min="2000" max="2100">
            </div>
            <div class="col-md-3">
                <label>Leave Type</label>
                <select name="leave_type_id" class="form-control form-control-sm">
                    <option value="">All Types</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Status</label>
                <select name="status" class="form-control form-control-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-sm btn-primary w-50 mr-1"><i class="bx bx-search"></i> Filter</button>
                <a href="{{ route('admin.leaves.report') }}" class="btn btn-sm btn-secondary"><i class="bx bx-reset"></i> Reset</a>
            </div>
        </form>

        {{-- Leave Table --}}
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover">
                <thead class="">
                    <tr>
                        <th>SL</th>
                        <th>Employee</th>
                        <th>Employee ID</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Days</th>
                        <th>Status</th>
                        <th>Applied On</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $leave->user->name ?? 'N/A' }}</strong>
                        </td>
                        <td>{{ $leave->user->employee_id ?? 'N/A' }}</td>
                        <td>{{ $leave->leaveType->name ?? 'N/A' }}</td>
                        <td>{{ date('d M Y', strtotime($leave->start_date)) }}</td>
                        <td>{{ date('d M Y', strtotime($leave->end_date)) }}</td>
                        <td>
                            @php
                                $start = new DateTime($leave->start_date);
                                $end = new DateTime($leave->end_date);
                                $days = $end->diff($start)->days + 1;
                            @endphp
                            {{ $days }} day(s)
                        </td>
                        <td>
                            @if($leave->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($leave->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ date('d M Y', strtotime($leave->created_at)) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No leave records found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $leaves->links() }}
    </div>

</div>

@endsection
