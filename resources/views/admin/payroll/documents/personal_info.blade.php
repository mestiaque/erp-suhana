@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Personal Information Sheet') }}</title>
@endsection

@push('css')
<style>
    .info-container { max-width: 900px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .header-section { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
    .info-table { width: 100%; border-collapse: collapse; }
    .info-table td { padding: 10px; border: 1px solid #ddd; }
    .info-table td:first-child { font-weight: bold; background: #f8f9fa; width: 35%; }
    .employee-photo { width: 150px; height: 180px; border: 2px solid #333; object-fit: cover; }

    @media print {
        /* Hide all UI elements */
        .no-print, .breadcrumb-area, form, button, .btn,
        .sidebar, .sidenav, .main-menu, .header-navbar, .footer,
        .sidebar-wrapper, .app-sidebar, .app-header, .app-footer,
        .sidemenu-area, .sidemenu-header, .sidemenu-body,
        .navbar, .top-navbar, .footer-area,
        nav, header, .navbar, .footer-area, .main-menu, .side-menu, .side-nav, .page-header, .page-footer, .layout-header, .layout-footer, .layout-sidebar, .sidebar-menu, .sidebar-footer, .sidebar-header, .sidebar-content, .sidebar, .footer, .header, .sidemenu, .sidemenu-area, .sidemenu-header, .sidemenu-body { display: none !important; }

        /* Show only document content */
        body { margin: 0 !important; padding: 5mm !important; background: white !important; }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .flex-grow-1 { display: block !important; }
        .info-container {
            max-width: 100%;
            page-break-inside: avoid;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Remove shadows for clean print */
        * { box-shadow: none !important; border: none !important; }
    }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area no-print">
    <h1>Personal Information Sheet</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Documents</li>
        <li class="item">Personal Info</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="no-print" style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <form action="{{ route('admin.documents.personalInfo') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <label>Select Employee</label>
                <select name="employee_id" class="form-control" required>
                    <option value="">Choose Employee</option>
                    @foreach($employees ?? [] as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->employee_id }} - {{ $emp->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-file-blank"></i> Generate</button>
            </div>
        </form>
    </div>

    @if($employee ?? false)
    <div class="info-container">
        <div class="header-section text-center">
            <h3>{{ general()->title }}</h3>
            <h4>PERSONAL INFORMATION SHEET</h4>
        </div>

        <div class="row mb-4">
            <div class="col-md-9">
                <h5>Employee Details</h5>
            </div>
            <div class="col-md-3 text-end">
                <img src="{{ $employee->photo ? asset('storage/employees/photos/'.$employee->photo) : asset('admin/images/user.png') }}" alt="Photo" class="employee-photo">
            </div>
        </div>

        <table class="info-table">
            <tr>
                <td>Employee ID</td>
                <td>{{ $employee->employee_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Full Name</td>
                <td>{{ $employee->name }}</td>
            </tr>
            <tr>
                <td>Father's Name</td>
                <td>{{ $employee->father_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Mother's Name</td>
                <td>{{ $employee->mother_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Date of Birth</td>
                <td>{{ $employee->dob ? date('d F Y', strtotime($employee->dob)) : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Gender</td>
                <td>{{ ucfirst($employee->gender ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td>Blood Group</td>
                <td>{{ $employee->blood_group ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Marital Status</td>
                <td>{{ ucfirst($employee->marital_status ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td>Religion</td>
                <td>{{ $employee->religion ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Nationality</td>
                <td>{{ $employee->nationality ?? 'Bangladeshi' }}</td>
            </tr>
            <tr>
                <td>National ID / Passport</td>
                <td>{{ $employee->nid ?? 'N/A' }}</td>
            </tr>
        </table>

        <h5 class="mt-4 mb-3">Contact Information</h5>
        <table class="info-table">
            <tr>
                <td>Present Address</td>
                <td>{{ $employee->address ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Permanent Address</td>
                <td>{{ $employee->permanent_address ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Mobile Number</td>
                <td>{{ $employee->phone }}</td>
            </tr>
            <tr>
                <td>Email Address</td>
                <td>{{ $employee->email }}</td>
            </tr>
            <tr>
                <td>Emergency Contact</td>
                <td>{{ $employee->emergency_contact ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Emergency Contact Name</td>
                <td>{{ $employee->emergency_contact_name ?? 'N/A' }}</td>
            </tr>
        </table>

        <h5 class="mt-4 mb-3">Employment Information</h5>
        <table class="info-table">
            <tr>
                <td>Department</td>
                <td>{{ $employee->department->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Designation</td>
                <td>{{ $employee->designation->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Date of Joining</td>
                <td>{{ $employee->joining_date ? date('d F Y', strtotime($employee->joining_date)) : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Date of Confirmation</td>
                <td>{{ $employee->confirmation_date ? date('d F Y', strtotime($employee->confirmation_date)) : 'Not yet confirmed' }}</td>
            </tr>
            <tr>
                <td>Employment Status</td>
                <td><span class="badge bg-{{ $employee->employee_status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($employee->employee_status ?? 'N/A') }}</span></td>
            </tr>
            <tr>
                <td>Employee Type</td>
                <td>{{ $employee->employee_type ?? 'Permanent' }}</td>
            </tr>
        </table>

        <h5 class="mt-4 mb-3">Salary Information</h5>
        <table class="info-table">
            <tr>
                <td>Basic Salary</td>
                <td>৳{{ number_format($employee->basic_salary ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>House Rent Allowance</td>
                <td>৳{{ number_format($employee->house_rent ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Medical Allowance</td>
                <td>৳{{ number_format($employee->medical_allowance ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Transport Allowance</td>
                <td>৳{{ number_format($employee->transport_allowance ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Food Allowance</td>
                <td>৳{{ number_format($employee->food_allowance ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Provident Fund Deduction</td>
                <td>৳{{ number_format($employee->provident_fund ?? 0, 2) }}</td>
            </tr>
            <tr style="background: #fffae6;">
                <td><strong>Gross Salary</strong></td>
                <td><strong>৳{{ number_format(($employee->basic_salary ?? 0) + ($employee->house_rent ?? 0) + ($employee->medical_allowance ?? 0) + ($employee->transport_allowance ?? 0) + ($employee->food_allowance ?? 0), 2) }}</strong></td>
            </tr>
        </table>

        <h5 class="mt-4 mb-3">Bank Information</h5>
        <table class="info-table">
            <tr>
                <td>Bank Name</td>
                <td>{{ $employee->bank_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Branch Name</td>
                <td>{{ $employee->bank_branch ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Account Number</td>
                <td>{{ $employee->bank_account_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Account Holder Name</td>
                <td>{{ $employee->bank_account_name ?? $employee->name }}</td>
            </tr>
        </table>

        <div class="mt-5 text-end">
            <p>Date: {{ date('d F Y') }}</p>
            <br><br>
            <p>___________________________<br>
            <strong>Authorized Signature</strong></p>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success btn-lg"><i class="bx bx-printer"></i> Print</button>
        <a href="{{ route('admin.documents.personalInfo') }}" class="btn btn-secondary btn-lg">Generate Another</a>
    </div>
    @endif

</div>

@endsection
