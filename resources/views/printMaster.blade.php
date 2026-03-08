<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            color: #333;
            background: #fff;
        }
        .container {
            width: 100%;
            /* width: 210mm; */
            padding: 15px;
            margin-top: 3rem;
            margin-left: auto;
            margin-right: auto;
        }

        .fixed-top {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 3rem !important;
        }

        /* Buttons (hidden in print) */
        .no-print {
            text-align: center;
            margin-bottom: 15px;
            padding: 5px;
            background: #333;
        }
        .no-print button {
            padding: 8px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: #fff;
        }
        .btn-print { background: #28a745; }
        .btn-close { background: #dc3545; }

        /* Header */
        .print-header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #909090;
        }
        .company-info {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 5px;
        }
        .company-logo { width: 60px; height: 60px; object-fit: contain; }
        .company-name { font-size: 30px; font-weight: bold; text-transform: uppercase; color: #0047ab;}
        .company-address, .company-contact { font-size: 12px; color: #555; }

        /* Report title */
        .report-title { font-size: 14px; font-weight: bold; margin: -20px 0 0px; text-transform: uppercase; }
        .report-title span {
            display: inline-block;
            padding: 5px 15px;
            background: #333333b5;
            color: #fff;
            border-radius: 4px;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #333;
            font-size: 12px;
            text-align: left;
        }
        th { background: #cfcfcf !important; }

        /* Signature */
        .print-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            font-size: 11px;
            color: #666;
        }
        .print-time{
            float: right !important;
            font-size: 10px !important;
            background: white !important;
            color: #000000a8 !important;
            padding: 2px 2px !important;
            vertical-align: bottom !important;
            margin-top: 1rem !important;
        }
        .signature-box { text-align: center; width: 200px; }
        .signature-line { border-top: 1px solid #333; margin-top: 30px; padding-top: 5px; }

        /* Print adjustments */
        @media print {
            .no-print { display: none; }
            body { margin: 1mm; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .container { padding: 0; margin-top: 0rem; width: 100%; }
            .company-name { font-size: 28px !important; }
        }
    </style>
    @stack('css')
</head>
<body>
    <div class="no-print fixed-top">
        <button class="btn-print" onclick="window.print()">
            <i class="fa fa-print"></i> Print
        </button>
        <button class="btn-close" onclick="window.close()">
            <i class="fa fa-times"></i> Close
        </button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="print-header">
            <div class="company-info">
                @if(general() && general()->logo())
                <img src="{{ asset(general()->logo()) }}" alt="Logo" class="company-logo">
                @endif
                <div class="company-name">{{ general()->title ?? 'Company Name' }}</div>
                <div class="text-right" style="text-align: end; width: 42mm;">
                    @if(general())
                        <div class="company-address">
                            {{ general()->address_one ?? '' }}
                        </div>
                        <div class="company-contact">
                            Phone: {{ general()->mobile ?? '' }}, Email: {{ general()->email ?? '' }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="report-title"><span style="margin-left:2rem ">@yield('title')</span> <span class="print-time"><i>{{ now()->format('d-m-Y H:i:s') }}</i></span></div>
        </div>

        @yield('contents')
        <div class=" print-footer">
                @php
                    $signatures = isset($signatures) ? $signatures : ['Prepared By', 'Checked By', 'Approved By'];
                    if(is_string($signatures)) {
                        $signatures = json_decode($signatures, true);
                    }
                @endphp

            @foreach($signatures as $signature)
                <div class="sig signature-box">
                    <div class="signature-line"></div>
                    {{ $signature }}
                </div>
            @endforeach
        </div>
    </div>
    @stack('js')
</body>
</html>
