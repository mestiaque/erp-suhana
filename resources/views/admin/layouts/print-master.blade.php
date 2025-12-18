<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', websiteTitle('Print Layout'))</title>
    <link rel="apple-touch-icon" href="{{ asset(general()->favicon()) }}" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset(general()->favicon()) }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f2f2f2;
            font-size: 12px;
        }
        p {
            margin: 2px;
        }

        .print-container {
            width: 210mm;
            min-height: 297mm;
            padding: 4mm;
            margin: 10px auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .no-print-container {
            width: 210mm;
            padding: 1mm;
            margin: 10px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse !important;
        }
        table th, table td {
            border: 1px solid #dee2e6 !important;
            padding: 4px 6px;
        }
        thead th {
            background: #e9ecef !important;
        }

        tr, td, th {
            page-break-inside: avoid !important;
        }

        @media print {
            body {
                background: none;
                font-size: 12px;
            }
            .print-container {
                margin: 0;
                width: 100%;
                min-height: auto;
                box-shadow: none;
                padding: 0;
            }
            @page {
                size: A4;
                margin: 4mm;
            }
            .no-print-container{
                display: none !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="no-print-container"
     style="position:sticky; top:0; z-index:999; display:flex; justify-content:space-between; align-items:center; padding:10px 0; margin-bottom:15px;">

    <a href="@yield('back_route')"
       style="padding:6px 18px; background:#6c757d; color:#fff; border-radius:4px; text-decoration:none; font-size:14px; border:1px solid #6c757d;">
        ← Back
    </a>

    <button id="PrintAction"
        style="padding:6px 18px; background:#0d6efd; color:#fff; border-radius:4px; border:1px solid #0d6efd; font-size:14px; cursor:pointer;">
        🖨️ Print
    </button>
</div>

<div class="print-container">
    @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    document.getElementById('PrintAction').addEventListener('click', function () {
        window.print();
    });
</script>

@stack('scripts')
</body>
</html>
