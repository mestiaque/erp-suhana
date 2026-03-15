@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Salary Report') }}</title>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('public/admin/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/admin/app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/admin/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css') }}">

<style>
.salary-filters { padding: 15px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.05); margin-bottom:20px; }
.statuslist { display:flex; gap:10px; flex-wrap:wrap; list-style:none; padding-left:0; margin-bottom:0; }
.statuslist li { background:#e9ecef; padding:5px 12px; border-radius:12px; font-weight:500; font-size:0.875rem; }
.dataTables_wrapper .dt-buttons { margin-bottom:10px; }
.dataTables_filter{display:none;}
table.table thead{background:#56d2ff;}
</style>
@endpush

@section('contents')
<div class="flex-grow-1">

    <!-- Filters -->
    <div class="salary-filters">
        <form action="{{ route('admin.salaryReport') }}" class="row g-3 align-items-end">
            
            <div class="col-12 mb-3">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="p-2 border-start border-primary border-4 bg-light rounded">
                            <small class="text-muted d-block">Total Employees</small>
                            <h5 class="mb-0 fw-bold">{{ $totalEmployees }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2 border-start border-success border-4 bg-light rounded">
                            <small class="text-muted d-block">Total Salary (TK)</small>
                            <h5 class="mb-0 fw-bold">{{ $totalSalary }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2 border-start border-warning border-4 bg-light rounded">
                            <small class="text-muted d-block">Total Paid (TK)</small>
                            <h5 class="mb-0 fw-bold">{{ $totalPaid }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2 border-start border-danger border-4 bg-light rounded">
                            <small class="text-muted d-block">Unpaid (TK)</small>
                            <h5 class="mb-0 fw-bold">{{ $totalSalary - $totalPaid }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <!-- Filters -->
            <div class="col-md-2"><label>Start Date</label><input type="date" name="startDate" class="form-control form-control-sm" value="{{ request()->startDate ?? date('Y-m-d') }}"></div>
            <div class="col-md-2"><label>End Date</label><input type="date" name="endDate" class="form-control form-control-sm" value="{{ request()->endDate ?? date('Y-m-d') }}"></div>
            <div class="col-md-2"><label>Name</label><input type="text" name="search" class="form-control form-control-sm" value="{{ request()->search }}" placeholder="Employee Name"></div>
            <div class="col-md-2"><label>Grade</label>
                <select name="grade" class="form-control form-control-sm">
                    <option value="">All Grades</option>
                    @foreach($grades as $g)<option value="{{ $g->id }}" @if(request()->grade==$g->id) selected @endif>{{ $g->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2"><label>Department</label>
                <select name="department" class="form-control form-control-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $d)<option value="{{ $d->id }}" @if(request()->department==$d->id) selected @endif>{{ $d->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-success btn-sm">Search</button>
                <a href="{{ route('admin.salaryReport') }}" class="btn btn-warning btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <!-- Salary Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover dataex-html5-export w-100">
                    <thead>
                        <tr>
                            <th>SL</th><th>Name</th><th>Grade</th><th>Department</th><th>Designation</th><th>Employee Type</th>
                            <th>Basic (%)</th><th>House Rent (%)</th><th>Medical</th><th>Transport</th><th>Food</th>
                            <th>Attendance Bonus</th><th>Other Allowance</th><th>Stamp Charge</th>
                            <th>Computed Salary</th><th>Total Paid</th><th>Last Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaryData as $key => $row)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['grade'] }}</td>
                            <td>{{ $row['department'] }}</td>
                            <td>{{ $row['designation'] }}</td>
                            <td>{{ $row['employee_type'] }}</td>
                            <td>{{ $row['basic'] }}</td>
                            <td>{{ $row['house_rent'] }}</td>
                            <td>{{ $row['medical'] }}</td>
                            <td>{{ $row['transport'] }}</td>
                            <td>{{ $row['food'] }}</td>
                            <td>{{ $row['attendance_bonus'] }}</td>
                            <td>{{ $row['other_allowance'] }}</td>
                            <td>{{ $row['stamp_charge'] }}</td>
                            <td>{{ $row['computed_salary'] }}</td>
                            <td>{{ $row['total_paid'] }}</td>
                            <td>{{ $row['last_paid'] }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="17" class="text-center">No salary data found</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">{{ $users->appends(request()->all())->links('pagination') }}</div>
            </div>
        </div>
    </div>

</div>

@push('js')
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/jszip.min.js') }}"></script>
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/pdfmake.min.js') }}"></script>
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/vfs_fonts.js') }}"></script>
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/buttons.html5.min.js') }}"></script>
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/buttons.print.min.js') }}"></script>
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/buttons.colVis.min.js') }}"></script>

<script>
$(document).ready(function () {
    $('.dataex-html5-export').DataTable({
        dom: "Bfrtip",
        buttons: ["copy","excel","csv","pdf","print"],
        pageLength: 50,
        lengthMenu: [25,50,100,200],
        responsive:true,
        scrollX:true,
        autoWidth:false
    });
});
</script>
@endpush
@endsection
