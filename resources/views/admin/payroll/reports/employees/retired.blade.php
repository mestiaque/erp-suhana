@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Retired Employees') }}</title>
@endsection

@push('css')
<style>
    .retired-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .retired-banner { background: linear-gradient(135deg, #868f96 0%, #596164 100%); color: white; padding: 25px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Retired Employees</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Reports</li>
        <li class="item">Retired</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="retired-banner">
        <h2><i class="bx bx-trophy"></i> {{ count($employees ?? []) }} Retired Employees</h2>
        <p>Thank you for your dedicated service</p>
    </div>

    <div class="retired-card">
        <form action="{{ route('admin.reports.employees.retired') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Department</label>
                <select name="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
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
                <thead class="table-dark">
                    <tr>
                        <th>SL</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Joining Date</th>
                        <th>Retirement Date</th>
                        <th>Service Years</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees ?? [] as $i => $emp)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $emp->employee_id ?? 'N/A' }}</td>
                        <td>{{ $emp->name }}</td>
                        <td>{{ $emp->department->name ?? 'N/A' }}</td>
                        <td>{{ $emp->designation ?? 'N/A' }}</td>
                        <td>{{ $emp->joining_date ? date('d M Y', strtotime($emp->joining_date)) : 'N/A' }}</td>
                        <td>
                            <strong class="text-danger">
                                {{ $emp->retirement_date ? date('d M Y', strtotime($emp->retirement_date)) : 'N/A' }}
                            </strong>
                        </td>
                        <td>
                            @if($emp->joining_date && $emp->retirement_date)
                            @php
                                $joining = \Carbon\Carbon::parse($emp->joining_date);
                                $retirement = \Carbon\Carbon::parse($emp->retirement_date);
                                $years = $joining->diffInYears($retirement);
                                $months = $joining->copy()->addYears($years)->diffInMonths($retirement);
                            @endphp
                            <span class="badge bg-info">{{ $years }} years {{ $months }} months</span>
                            @else
                            N/A
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No retired employees found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="text-end mt-3">
            <button onclick="window.print()" class="btn btn-secondary"><i class="bx bx-printer"></i> Print</button>
        </div>
    </div>

</div>

@endsection
