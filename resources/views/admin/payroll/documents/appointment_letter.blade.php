@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Appointment Letter') }}</title>
@endsection

@push('css')
<style>
    .letter-container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .letterhead { text-align: center; border-bottom: 3px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
    .letter-body { line-height: 1.8; text-align: justify; }

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
        .letter-container {
            max-width: 100%;
            page-break-inside: avoid;
            background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%) !important;
            color: #fff !important;
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
    <h1>Generate Appointment Letter</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Documents</li>
        <li class="item">Appointment Letter</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="no-print" style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <form action="{{ route('admin.documents.appointmentLetter') }}" method="GET" class="row g-3">
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
    <div class="letter-container">
        <div class="letterhead">
            <h3>{{ general()->title }}</h3>
            <p>{{ websiteSetting('address') ?? 'Company Address' }}</p>
            <p>Phone: {{ websiteSetting('phone') ?? 'N/A' }} | Email: {{ websiteSetting('email') ?? 'N/A' }}</p>
        </div>

        <p><strong>Date:</strong> {{ date('d F Y') }}</p>
        <p><strong>Ref:</strong> APT/{{ date('Y') }}/{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</p>

        <p><strong>To,</strong><br>
        {{ $employee->name }}<br>
        {{ $employee->address ?? 'N/A' }}</p>

        <p><strong>Subject: Letter of Appointment</strong></p>

        <div class="letter-body">
            <p>Dear {{ $employee->name }},</p>

            <p>We are pleased to inform you that you have been selected for the position of <strong>{{ $employee->designation->name ?? 'N/A' }}</strong> in the <strong>{{ $employee->department->name ?? 'N/A' }}</strong> department of {{ general()->title }}.</p>

            <p>Your appointment will be effective from <strong>{{ $employee->joining_date ? date('d F Y', strtotime($employee->joining_date)) : 'N/A' }}</strong>. The terms and conditions of your employment are as follows:</p>

            <ol>
                <li><strong>Position:</strong> {{ $employee->designation->name ?? 'N/A' }}</li>
                <li><strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }}</li>
                <li><strong>Basic Salary:</strong> ৳{{ number_format($employee->basic_salary ?? 0, 2) }} per month</li>
                <li><strong>House Rent:</strong> ৳{{ number_format($employee->house_rent ?? 0, 2) }}</li>
                <li><strong>Medical Allowance:</strong> ৳{{ number_format($employee->medical_allowance ?? 0, 2) }}</li>
                <li><strong>Transport Allowance:</strong> ৳{{ number_format($employee->transport_allowance ?? 0, 2) }}</li>
                <li><strong>Working Hours:</strong> As per company policy</li>
                <li><strong>Probation Period:</strong> 6 months from the date of joining</li>
            </ol>

            <p>You will be entitled to other benefits as per company policy including leave, festival bonuses, and increment facilities.</p>

            <p>Please sign and return the duplicate copy of this letter as a token of your acceptance of the above terms and conditions.</p>

            <p>We congratulate you on your appointment and look forward to a long and mutually beneficial association.</p>

            <p>Sincerely,</p>
            <br><br>

            <p>___________________________<br>
            <strong>{{ websiteSetting('hr_name') ?? 'HR Manager' }}</strong><br>
            {{ general()->title }}</p>

            <hr style="margin-top: 50px;">

            <p><strong>Acceptance by Employee:</strong></p>
            <p>I, {{ $employee->name }}, accept the above terms and conditions of my appointment.</p>
            <br><br>
            <p>___________________________<br>
            <strong>{{ $employee->name }}</strong><br>
            Date: _____________</p>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success btn-lg"><i class="bx bx-printer"></i> Print Letter</button>
        <a href="{{ route('admin.documents.appointmentLetter') }}" class="btn btn-secondary btn-lg">Generate Another</a>
    </div>
    @endif

</div>

@endsection
