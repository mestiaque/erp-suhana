@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Confirmation Letter') }}</title>
@endsection

@push('css')
<style>
    .letter-container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .letterhead { text-align: center; border-bottom: 3px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
    .letter-body { line-height: 1.8; text-align: justify; }
    .highlight-box { background: #e8f5e9; padding: 15px; border-left: 4px solid #4caf50; margin: 20px 0; }

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
            background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%) !important;
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
    <h1>Generate Confirmation Letter</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Documents</li>
        <li class="item">Confirmation Letter</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="no-print" style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <form action="{{ route('admin.documents.confirmationLetter') }}" method="GET" class="row g-3">
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
    @php
        $confirmationDate = $employee->confirmation_date ? \Carbon\Carbon::parse($employee->confirmation_date) : null;
        $joiningDate = $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date) : null;
        $probationMonths = $joiningDate && $confirmationDate ? $joiningDate->diffInMonths($confirmationDate) : 6;
    @endphp

    <div class="letter-container">
        <div class="letterhead">
            <h3>{{ general()->title }}</h3>
            <p>{{ websiteSetting('address') ?? 'Company Address' }}</p>
            <p>Phone: {{ websiteSetting('phone') ?? 'N/A' }} | Email: {{ websiteSetting('email') ?? 'N/A' }}</p>
        </div>

        <p><strong>Date:</strong> {{ $confirmationDate ? $confirmationDate->format('d F Y') : date('d F Y') }}</p>
        <p><strong>Ref:</strong> CNF/{{ date('Y') }}/{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</p>

        <p><strong>To,</strong><br>
        {{ $employee->name }}<br>
        {{ $employee->designation->name ?? 'N/A' }}<br>
        {{ $employee->department->name ?? 'N/A' }} Department</p>

        <p><strong>Subject: Confirmation of Employment</strong></p>

        <div class="letter-body">
            <p>Dear {{ $employee->name }},</p>

            <div class="highlight-box">
                <p class="mb-0"><strong>Congratulations!</strong> We are pleased to confirm your employment with {{ general()->title }}.</p>
            </div>

            <p>You joined our organization on <strong>{{ $joiningDate ? $joiningDate->format('d F Y') : 'N/A' }}</strong> as <strong>{{ $employee->designation->name ?? 'N/A' }}</strong> in the <strong>{{ $employee->department->name ?? 'N/A' }}</strong> department on probation for a period of <strong>{{ $probationMonths }} months</strong>.</p>

            <p>During this probation period, your performance has been evaluated and found to be satisfactory. The management is pleased with your dedication, professionalism, and contribution to the organization.</p>

            <p>In view of the above, we are happy to inform you that your services are now <strong>confirmed with effect from {{ $confirmationDate ? $confirmationDate->format('d F Y') : date('d F Y') }}</strong>.</p>

            <p><strong>Your current employment details are as follows:</strong></p>
            <ul>
                <li><strong>Employee ID:</strong> {{ $employee->employee_id ?? 'N/A' }}</li>
                <li><strong>Designation:</strong> {{ $employee->designation->name ?? 'N/A' }}</li>
                <li><strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }}</li>
                <li><strong>Date of Joining:</strong> {{ $joiningDate ? $joiningDate->format('d F Y') : 'N/A' }}</li>
                <li><strong>Date of Confirmation:</strong> {{ $confirmationDate ? $confirmationDate->format('d F Y') : date('d F Y') }}</li>
                <li><strong>Current Salary:</strong> ৳{{ number_format($employee->basic_salary ?? 0, 2) }}</li>
            </ul>

            <p>As a confirmed employee, you are entitled to all benefits as per the company policy including:</p>
            <ul>
                <li>Annual leave and festival bonuses</li>
                <li>Medical benefits</li>
                <li>Provident fund benefits</li>
                <li>Annual increment consideration</li>
                <li>Other employee welfare benefits</li>
            </ul>

            <p>You will continue to be governed by the terms and conditions of your employment as mentioned in your appointment letter and company policies.</p>

            <p>We look forward to your continued contribution and wish you all the best in your career with us.</p>

            <p>Once again, congratulations on your confirmation!</p>

            <p>Sincerely,</p>
            <br><br>

            <p>___________________________<br>
            <strong>{{ websiteSetting('hr_name') ?? 'HR Manager' }}</strong><br>
            {{ general()->title }}</p>

            <hr style="margin-top: 50px;">

            <p><strong>Acknowledgement:</strong></p>
            <p>I, {{ $employee->name }}, acknowledge receipt of this confirmation letter and agree to abide by all company policies and regulations.</p>
            <br><br>
            <p>___________________________<br>
            <strong>{{ $employee->name }}</strong><br>
            Employee ID: {{ $employee->employee_id ?? 'N/A' }}<br>
            Date: _____________</p>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success btn-lg"><i class="bx bx-printer"></i> Print Letter</button>
        <a href="{{ route('admin.documents.confirmationLetter') }}" class="btn btn-secondary btn-lg">Generate Another</a>
    </div>
    @endif

</div>

@endsection
