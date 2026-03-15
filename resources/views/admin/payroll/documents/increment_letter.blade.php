@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Increment Letter') }}</title>
@endsection

@push('css')
<style>
    .letter-container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .letterhead { text-align: center; border-bottom: 3px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
    .letter-body { line-height: 1.8; text-align: justify; }
    .salary-comparison { background: #f8f9fa; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0; }

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
            background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%) !important;
            color: #222 !important;
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
    <h1>Generate Increment Letter</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Documents</li>
        <li class="item">Increment Letter</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="no-print" style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <form action="{{ route('admin.documents.incrementLetter') }}" method="GET" class="row g-3">
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
            <div class="col-md-3">
                <label>Increment Amount</label>
                <input type="number" name="increment_amount" class="form-control" value="{{ request('increment_amount') }}" step="0.01" placeholder="Enter amount">
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-file-blank"></i> Generate</button>
            </div>
        </form>
    </div>

    @if($employee ?? false)
    @php
        $incrementAmount = request('increment_amount', 0);
        $previousSalary = $employee->basic_salary ?? 0;
        $newSalary = $previousSalary + $incrementAmount;
        $percentage = $previousSalary > 0 ? (($incrementAmount / $previousSalary) * 100) : 0;
    @endphp

    <div class="letter-container">
        <div class="letterhead">
            <h3>{{ general()->title }}</h3>
            <p>{{ websiteSetting('address') ?? 'Company Address' }}</p>
            <p>Phone: {{ websiteSetting('phone') ?? 'N/A' }} | Email: {{ websiteSetting('email') ?? 'N/A' }}</p>
        </div>

        <p><strong>Date:</strong> {{ date('d F Y') }}</p>
        <p><strong>Ref:</strong> INC/{{ date('Y') }}/{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</p>

        <p><strong>To,</strong><br>
        {{ $employee->name }}<br>
        {{ $employee->designation->name ?? 'N/A' }}<br>
        {{ $employee->department->name ?? 'N/A' }} Department</p>

        <p><strong>Subject: Salary Increment Letter</strong></p>

        <div class="letter-body">
            <p>Dear {{ $employee->name }},</p>

            <p>We are pleased to inform you that the management has decided to increase your salary in recognition of your excellent performance, dedication, and contribution to {{ general()->title }}.</p>

            <p>Your revised salary structure is as follows:</p>

            <div class="salary-comparison">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Particulars</th>
                            <th class="text-end">Previous</th>
                            <th class="text-end">Increment</th>
                            <th class="text-end">Revised</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td class="text-end">৳{{ number_format($previousSalary, 2) }}</td>
                            <td class="text-end text-success"><strong>+৳{{ number_format($incrementAmount, 2) }}</strong></td>
                            <td class="text-end"><strong>৳{{ number_format($newSalary, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td>House Rent</td>
                            <td class="text-end">৳{{ number_format($employee->house_rent ?? 0, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">৳{{ number_format($employee->house_rent ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Medical Allowance</td>
                            <td class="text-end">৳{{ number_format($employee->medical_allowance ?? 0, 2) }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end">৳{{ number_format($employee->medical_allowance ?? 0, 2) }}</td>
                        </tr>
                        <tr class="table-warning">
                            <td><strong>Gross Salary</strong></td>
                            <td class="text-end"><strong>৳{{ number_format($previousSalary + ($employee->house_rent ?? 0) + ($employee->medical_allowance ?? 0), 2) }}</strong></td>
                            <td class="text-end text-success"><strong>+৳{{ number_format($incrementAmount, 2) }}</strong></td>
                            <td class="text-end"><strong>৳{{ number_format($newSalary + ($employee->house_rent ?? 0) + ($employee->medical_allowance ?? 0), 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
                <p class="mt-2 mb-0"><small><strong>Increment Percentage: {{ number_format($percentage, 2) }}%</strong></small></p>
            </div>

            <p>This increment will be effective from <strong>{{ date('1 F Y', strtotime('first day of next month')) }}</strong> and will be reflected in your salary for the month of {{ date('F Y', strtotime('next month')) }}.</p>

            <p>We appreciate your hard work and commitment to the organization. We hope you will continue to excel in your role and contribute to the continued success of our company.</p>

            <p>Congratulations on your well-deserved increment!</p>

            <p>Sincerely,</p>
            <br><br>

            <p>___________________________<br>
            <strong>{{ websiteSetting('hr_name') ?? 'HR Manager' }}</strong><br>
            {{ general()->title }}</p>

            <hr style="margin-top: 50px;">

            <p><strong>Acknowledgement:</strong></p>
            <p>I, {{ $employee->name }}, acknowledge receipt of this increment letter and understand the revised salary structure.</p>
            <br><br>
            <p>___________________________<br>
            <strong>{{ $employee->name }}</strong><br>
            Employee ID: {{ $employee->employee_id ?? 'N/A' }}<br>
            Date: _____________</p>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success btn-lg"><i class="bx bx-printer"></i> Print Letter</button>
        <a href="{{ route('admin.documents.incrementLetter') }}" class="btn btn-secondary btn-lg">Generate Another</a>
    </div>
    @endif

</div>

@endsection
