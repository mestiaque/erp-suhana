@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Joining Letter') }}</title>
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
        .letter-container { max-width: 100%; page-break-inside: avoid; }

        /* Remove shadows for clean print */
        * { box-shadow: none !important; border: none !important; }
        .joining-letter-card {
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
            margin: 0 auto !important;
            display: block !important;
            width: 700px !important;
            background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%) !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area no-print">
    <h1>Generate Joining Letter</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Documents</li>
        <li class="item">Joining Letter</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="no-print" style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <form action="{{ route('admin.documents.joiningLetter') }}" method="GET" class="row g-3">
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

        <p><strong>Date:</strong> {{ $employee->joining_date ? date('d F Y', strtotime($employee->joining_date)) : date('d F Y') }}</p>
        <p><strong>Ref:</strong> JON/{{ date('Y') }}/{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</p>

        <p><strong>To,</strong><br>
        The HR Department<br>
        {{ general()->title }}</p>

        <p><strong>Subject: Joining Report</strong></p>

        <div class="letter-body">
            <p>Dear Sir/Madam,</p>

            <p>In reference to the appointment letter dated <strong>{{ $employee->joining_date ? date('d F Y', strtotime($employee->joining_date)) : 'N/A' }}</strong>, I am writing to confirm that I have joined {{ general()->title }} today as <strong>{{ $employee->designation->name ?? 'N/A' }}</strong> in the <strong>{{ $employee->department->name ?? 'N/A' }}</strong> department.</p>

            <p><strong>Employee Details:</strong></p>
            <ul>
                <li><strong>Full Name:</strong> {{ $employee->name }}</li>
                <li><strong>Employee ID:</strong> {{ $employee->employee_id ?? 'N/A' }}</li>
                <li><strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }}</li>
                <li><strong>Designation:</strong> {{ $employee->designation->name ?? 'N/A' }}</li>
                <li><strong>Date of Joining:</strong> {{ $employee->joining_date ? date('d F Y', strtotime($employee->joining_date)) : 'N/A' }}</li>
                <li><strong>Contact Number:</strong> {{ $employee->phone }}</li>
                <li><strong>Email Address:</strong> {{ $employee->email }}</li>
            </ul>

            <p>I have received and understood all the terms and conditions of my employment. I have also submitted all the required documents including:</p>
            <ul>
                <li>Educational certificates</li>
                <li>Experience certificates</li>
                <li>National ID/Passport copy</li>
                <li>Passport size photographs</li>
                <li>Bank account details</li>
            </ul>

            <p>I assure you that I will work with dedication and sincerity to contribute to the growth and success of the organization.</p>

            <p>Thank you for this opportunity.</p>

            <p>Sincerely,</p>
            <br><br>

            @if($employee->signature)
            <img src="{{ asset('storage/employees/signatures/'.$employee->signature) }}" alt="Signature" style="height: 40px;">
            @else
            ___________________________
            @endif
            <br>
            <strong>{{ $employee->name }}</strong><br>
            Employee ID: {{ $employee->employee_id ?? 'N/A' }}<br>
            Date: {{ date('d F Y') }}

            <hr style="margin-top: 50px;">

            <p><strong>For Office Use Only:</strong></p>
            <p>Received and verified by HR Department on _______________</p>
            <br><br>
            <p>___________________________<br>
            <strong>HR Manager</strong><br>
            {{ general()->title }}</p>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success btn-lg"><i class="bx bx-printer"></i> Print Letter</button>
        <a href="{{ route('admin.documents.joiningLetter') }}" class="btn btn-secondary btn-lg">Generate Another</a>
    </div>
    @endif

</div>

@endsection
