@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Service Completed Report') }}</title>
@endsection

@push('css')
<style>
    .service-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .milestone-1 { background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%); }
    .milestone-3 { background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%); }
    .milestone-5 { background: linear-gradient(135deg, #fd79a8 0%, #e84393 100%); }
    .milestone-10 { background: linear-gradient(135deg, #a29bfe 0%, #6c5ce7 100%); }
    .milestone-card { color: white; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 15px; }
    .milestone-card h3 { margin: 0; font-size: 36px; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Service Completed Milestone Report</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Reports</li>
        <li class="item">Service Completed</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="milestone-card milestone-1">
                <h3>{{ $stats['1_year'] ?? 0 }}</h3>
                <p>1 Year Completed</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="milestone-card milestone-3">
                <h3>{{ $stats['3_years'] ?? 0 }}</h3>
                <p>3 Years Completed</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="milestone-card milestone-5">
                <h3>{{ $stats['5_years'] ?? 0 }}</h3>
                <p>5 Years Completed</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="milestone-card milestone-10">
                <h3>{{ $stats['10_years'] ?? 0 }}</h3>
                <p>10+ Years Completed</p>
            </div>
        </div>
    </div>

    <div class="service-card">
        <form action="{{ route('admin.reports.employees.serviceCompleted') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <label>Service Years</label>
                <select name="years" class="form-control">
                    <option value="">All</option>
                    <option value="1" {{ request('years') == '1' ? 'selected' : '' }}>1 Year</option>
                    <option value="3" {{ request('years') == '3' ? 'selected' : '' }}>3 Years</option>
                    <option value="5" {{ request('years') == '5' ? 'selected' : '' }}>5 Years</option>
                    <option value="10" {{ request('years') == '10' ? 'selected' : '' }}>10+ Years</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Department</label>
                <select name="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
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
                        <th>Service Duration</th>
                        <th>Milestone</th>
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
                            @if($emp->joining_date)
                            @php
                                $joining = \Carbon\Carbon::parse($emp->joining_date);
                                $years = $joining->diffInYears(now());
                                $months = $joining->copy()->addYears($years)->diffInMonths(now());
                            @endphp
                            {{ $years }} years {{ $months }} months
                            @else
                            N/A
                            @endif
                        </td>
                        <td>
                            @if($emp->joining_date)
                            @php
                                $serviceYears = \Carbon\Carbon::parse($emp->joining_date)->diffInYears(now());
                            @endphp
                            @if($serviceYears >= 10)
                                <span class="badge milestone-10" style="color:white;">10+ Years</span>
                            @elseif($serviceYears >= 5)
                                <span class="badge milestone-5" style="color:white;">5 Years</span>
                            @elseif($serviceYears >= 3)
                                <span class="badge milestone-3" style="color:white;">3 Years</span>
                            @elseif($serviceYears >= 1)
                                <span class="badge milestone-1" style="color:white;">1 Year</span>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No employees found for selected criteria</td>
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
