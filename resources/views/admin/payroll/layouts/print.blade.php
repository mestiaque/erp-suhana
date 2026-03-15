<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Print')</title>
    <link rel="stylesheet" href="{{ asset('admin/app-assets/css/bootstrap.min.css') }}">
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .print-area { padding: 10px; }
        }
        body { font-family: 'Bangla', 'Nikosh', sans-serif; }
        .table { font-size: 11px; }
        .table th, .table td { padding: 4px 6px; }
    </style>
</head>
<body>
    <div class="container-fluid p-3">
        @yield('contents')
    </div>
    
    <div class="no-print text-center mt-4 mb-4">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>
</body>
</html>
