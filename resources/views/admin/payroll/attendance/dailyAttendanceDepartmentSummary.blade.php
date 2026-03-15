@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Department Attendance Summary') }}</title>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('public/admin/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/admin/app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css') }}">

<style>
    .attendance-filters{
        padding:15px;
        border-radius:8px;
        box-shadow:0 2px 6px rgba(0,0,0,0.05);
        margin-bottom:20px;
    }
    table thead{background:#56d2ff;}
    .badge-summary{
        padding:5px 10px;
        border-radius:5px;
        color:#fff;
    }
    .badge-present{background:#28a745;}
    .badge-late{background:#ffc107;}
    .badge-absent{background:#dc3545;}
        .dataTables_filter{display:none;}
    table.table thead{background : #56d2ff;}
</style>
@endpush

@section('contents')
<div class="flex-grow-1">

    <!-- Filters -->
    <div class="attendance-filters">
        <form action="{{ route('admin.dailyAttendanceDepartmentSummary') }}" class="row g-3 align-items-end">

            <div class="col-md-2">
                <label>Start Date</label>
                <input type="date" name="startDate" value="{{ request()->startDate ?? date('Y-m-d') }}" class="form-control form-control-sm">
            </div>

            <div class="col-md-2">
                <label>End Date</label>
                <input type="date" name="endDate" value="{{ request()->endDate ?? date('Y-m-d') }}" class="form-control form-control-sm">
            </div>

            <div class="col-md-2">
                <label>Department</label>
                <select name="department" class="form-control form-control-sm">
                    <option value="">All</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" @if(request()->department==$dept->id) selected @endif>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>


            <div class="col-md-2 text-end">
                <button class="btn btn-success btn-sm" type="submit"><i class="bi bi-search"></i> Search</button>
                <a href="{{ route('admin.dailyAttendanceDepartmentSummary') }}" class="btn btn-warning btn-sm"><i class="bx bx-rotate-left"></i> Reset</a>
            </div>

        </form>
    </div>

    <!-- Department-wise Summary Table -->
    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover dataex-html5-export w-100">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Department</th>
                            <th>Total Employees</th>
                            <th>Present</th>
                            <th>Late</th>
                            <th>Absent</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    @forelse($dateWiseSummary as $day)
                    
                        <!-- Date Row -->
                        <tr class="table-primary text-center fw-bold">
                            <td colspan="6">
                                {{ $day['readable'] }}
                            </td>
                        </tr>
                    
                        <!-- Department Rows -->
                        @foreach($day['departments'] as $summary)
                    
                            <tr>
                                <td>{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                                <td>{{ $summary['department_name'] }}</td>
                                <td>{{ $summary['total'] }}</td>
                    
                                <td>
                                    <span class="badge-summary badge-present">
                                        {{ $summary['present'] }}
                                    </span>
                                </td>
                    
                                <td>
                                    <span class="badge-summary badge-late">
                                        {{ $summary['late'] }}
                                    </span>
                                </td>
                    
                                <td>
                                    <span class="badge-summary badge-absent">
                                        {{ $summary['absent'] }}
                                    </span>
                                </td>
                            </tr>
                    
                        @endforeach
                    
                    @empty
                    
                    <tr>
                        <td colspan="6" class="text-center">No data found</td>
                    </tr>
                    
                    @endforelse
                    
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('public/admin/app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
<script>
$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('.dataex-html5-export')) {
        $('.dataex-html5-export').DataTable().destroy();
    }

    $('.dataex-html5-export').DataTable({
        dom: "Bfrtip",
        buttons: [
            { extend: "copyHtml5", className: 'btn btn-sm btn-outline-secondary text-white mr-1' },
            { extend: "excelHtml5", className: 'btn btn-sm btn-outline-success text-white mr-1' },
            { extend: "csvHtml5", className: 'btn btn-sm btn-outline-info text-white mr-1' },
            { extend: "pdfHtml5", className: 'btn btn-sm btn-outline-danger text-white mr-1', orientation: "landscape", pageSize: "LEGAL" },
            { extend: "print", className: 'btn btn-sm btn-outline-primary text-white mr-1' }
        ],
        pageLength: 50,
        lengthMenu: [25,50,100,200],
        responsive: true,
        scrollX: true,
        autoWidth: false,
        ordering: false
    });
});
</script>
@endpush
