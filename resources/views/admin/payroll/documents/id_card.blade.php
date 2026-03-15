@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Generate ID Card') }}</title>
@endsection

@push('css')
<style>
    .doc-card {
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .id-card {
        width: 350px;
        height: 220px;
        border: 2px solid #333;
        border-radius: 10px;
        padding: 15px;
        margin: 10px;
        display: inline-block;
        position: relative;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .id-card-back {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .id-photo {
        width: 80px;
        height: 100px;
        border: 2px solid white;
        border-radius: 5px;
        object-fit: cover;
    }

    .company-logo {
        width: 50px;
        height: 50px;
        position: absolute;
        top: 10px;
        right: 10px;
    }

    /* ================= PRINT FIX ================= */
    @media print {

        /* 🔥 VERY IMPORTANT — removes extra blank page */
        @page {
            size: A4;
            margin: 0;
        }

        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            height: auto !important;
        }

        /* Hide UI */
        @media print {
            /* Hide all UI elements */
            .no-print, .breadcrumb-area, .doc-card, form, button, .btn,
            .sidebar, .sidenav, .main-menu, .header-navbar, .footer,
            .sidebar-wrapper, .app-sidebar, .app-footer,
            .sidemenu-area, .sidemenu-header, .sidemenu-body,
            nav, header, .navbar, .top-navbar, .footer-area,
            nav, header, .navbar { display: none !important; }

            /* Show only document content */
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: 100% !important;
                overflow: visible !important;
            }
            .main-content, .flex-grow-1, .text-center {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: 100% !important;
                overflow: visible !important;
            }

            /* ID card vertical stacking, remove blank space */
            .d-inline-block {
                display: block !important;
                width: 100% !important;
                text-align: center !important;
                margin: 0 !important;
                padding: 0 !important;
                page-break-after: avoid !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: 100% !important;
            }
            .id-card {
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                margin: 0 auto 0 auto !important;
                display: block !important;
                width: 350px !important;
                height: 220px !important;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .id-card-back {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .id-card:last-child {
                page-break-after: auto !important;
            }

            /* Remove shadows and borders for clean print */
            * { box-shadow: none !important; border: none !important; }
        }
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            display: block !important;
        }

        /* ⭐ CARD PRINT PERFECT */
        .id-card {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            margin: 5mm auto !important;
            display: block !important;
            width: 350px !important;
            height: 220px !important;
        }

        .d-inline-block {
            display: block !important;
            width: 100% !important;
            text-align: center !important;
        }

        /* Remove only shadow (NOT border!) */
        * {
            box-shadow: none !important;
        }

        /* Optional zoom tweak */
        body {
            zoom: 0.95;
        }
    }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area no-print">
    <h1>Generate ID Card</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Documents</li>
        <li class="item">ID Card</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="doc-card no-print">
        <form action="{{ route('admin.documents.idCard') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <label>Select Employees (Hold Ctrl/Cmd to select multiple)</label>
                <select name="employee_ids[]" class="form-control" multiple size="8">
                    @foreach($employees ?? [] as $emp)
                    <option value="{{ $emp->id }}" {{ in_array($emp->id, request()->employee_ids ?? []) ? 'selected' : '' }}>
                        {{ $emp->employee_id }} - {{ $emp->name }}
                    </option>
                    @endforeach
                </select>
                <small class="text-muted">Select multiple employees to print at once</small>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-id-card"></i> Generate</button>
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <div class="mt-2">
                    @if(count(request()->employee_ids ?? []) > 0)
                    <button type="button" onclick="window.print()" class="btn btn-success w-100"><i class="bx bx-printer"></i> Print ID Cards</button>
                    @endif
                </div>
            </div>
        </form>
    </div>

    @if(count($selectedEmployees ?? []) > 0)
    <div class="text-center">
        @foreach($selectedEmployees as $employee)
        <div class="d-inline-block">
            <!-- Front Side -->
            <div class="id-card">
                <img src="{{ asset('admin/images/logo.png') }}" alt="Logo" class="company-logo">
                <div class="text-center">
                    <h6 class="mb-1">{{ general()->title }}</h6>
                    <small>Employee ID Card</small>
                </div>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <div class="row">
                    <div class="col-5">
                        <img src="{{ $employee->photo ? asset('storage/employees/photos/'.$employee->photo) : asset('admin/images/user.png') }}" alt="Photo" class="id-photo">
                    </div>
                    <div class="col-7 text-start">
                        <p class="mb-1" style="font-size: 13px;"><strong>{{ $employee->name }}</strong></p>
                        <p class="mb-1" style="font-size: 11px;">{{ $employee->designation->name ?? 'N/A' }}</p>
                        <p class="mb-1" style="font-size: 11px;">{{ $employee->department->name ?? 'N/A' }}</p>
                        <p class="mb-0" style="font-size: 12px;"><strong>ID: {{ $employee->employee_id }}</strong></p>
                    </div>
                </div>
            </div>

            <!-- Back Side -->
            <div class="id-card id-card-back">
                <h6 class="text-center mb-3">Important Information</h6>
                <p style="font-size: 11px; line-height: 1.6;">
                    <strong>Blood Group:</strong> {{ $employee->blood_group ?? 'N/A' }}<br>
                    <strong>Phone:</strong> {{ $employee->phone }}<br>
                    <strong>Emergency:</strong> {{ $employee->emergency_contact ?? 'N/A' }}<br>
                    <strong>Address:</strong> {{ $employee->address ?? 'N/A' }}
                </p>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <p style="font-size: 10px;" class="mb-0">
                    If found, please contact:<br>
                    {{ general()->title }}<br>
                    Phone: {{ websiteSetting('phone') ?? 'N/A' }}
                </p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success btn-lg"><i class="bx bx-printer"></i> Print ID Cards</button>
        <a href="{{ route('admin.documents.idCard') }}" class="btn btn-secondary btn-lg">Generate Another</a>
    </div>
    @endif

</div>

@endsection
