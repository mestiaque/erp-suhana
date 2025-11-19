@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Designations List')}}</title>
@endsection @push('css')
<style type="text/css">

/* Header Flex Layout */
        .invoice-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 5px; /* Reduced margin */
            border-bottom: 1px solid #333; /* Thinner border */
            padding-bottom: 2px; /* Reduced padding */
            position: relative;
            z-index: 2;
        }
        .header-logo {
            width: 90px; /* Smaller logo */
            height: 90px; /* Smaller logo */
            object-fit: contain;
        }
        .header-info {
            flex: 1;
            text-align: center;
        }
        .header-info p {
            margin: 1px 0; /* Reduced margin */
            font-size: 13px; /* Smaller font size */
        }
        .header-qrcode {
            width: 90px; /* Smaller QR code */
            text-align: right;
        }

        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px; /* Reduced padding */
            box-sizing: border-box;
            position: relative;
            z-index: 2;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 10px; /* Reduced margin */
            border-bottom: 1px solid #333; /* Thinner border */
            padding-bottom: 5px; /* Reduced padding */
        }
        .invoice-header h1 {
            font-size: 20px; /* Smaller header */
            margin: 0;
            color: #333;
        }
        .invoice-header p {
            margin: 2px 0; /* Reduced margin */
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px; /* Reduced margin */
        }
        .invoice-info div {
            width: 48%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 4px; /* Reduced padding */
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary {
            margin-top: 10px; /* Reduced margin */
            text-align: right;
        }
        .footer {
            margin-top: 15px; /* Reduced margin */
            text-align: center;
            color: #666;
            font-size: 10px; /* Smaller font */
            border-top: 1px solid #ddd;
            padding-top: 5px; /* Reduced padding */
        }
        .bengali {
            font-family: 'SolaimanLipi', Arial, sans-serif;
        }
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }

            .custom-print-footer {
                display: block !important;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                text-align: center;
                color: #666;
                font-size: 10px;
                padding: 5px 0 2px 0;
                background: #fff;
                z-index: 9999;
            }
        }
        .custom-print-footer {
            display: none;
        }
        @page {
            margin: 0;
        }
        /* For Firefox only: show custom message and page number in footer */
        @page {
            /* Uncomment below for Firefox support */
            /* @bottom-center {
                content: "Thank you for your business! | Page " counter(page) " of " counter(pages);
            } */
        }
        .signature {
            margin-top: 35px; /* Reduced margin significantly */
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            width: 30%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 2px; /* Reduced padding */
        }

        .summary-flex-container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            align-items: flex-start;
        }

        .summary-table {
            width: 35%; /* Reduced width */
            margin-left: auto;
            border-collapse: collapse;
            margin-top: 0; /* Reset margin since using flex container */
        }

        .summary-table th, .summary-table td {
            padding: 3px; /* Further reduced padding */
            text-align: right;
            font-size: 13px; /* Smaller font size */
        }

        .summary-table th {
            font-weight: bold;
            width: 55%;
        }

        .notes-container {
            width: 60%;
            font-size: 10px;
            padding: 3px;
            align-self: flex-start;
        }

        /* Watermark */
        .watermark-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            opacity: 0.06;
        }
        .watermark-bg img {
            max-width: 60%;
            max-height: 60%;
        }
        .encodex-watermark{
            rotate: -45deg;
        }
        .text-end{
            text-align: right;
        }

        .btn-custom {
            border: 2px solid #000000; /* Button border */
            display: inline-block;
            padding: 3px 8px;
            background-color: #0f2d4a;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-custom:hover {
            background-color: #3751c7; /* hover এ একটু গাঢ় রঙ */
            transform: translateY(-1px); /* সামান্য উপরে উঠবে */
        }

        .btn-custom:active {
            transform: translateY(1px); /* চাপ দিলে নিচে নামবে */
        }


    
</style>
@endpush @section('contents')

sdfdsfdsfdsf


@endsection @push('js') @endpush

