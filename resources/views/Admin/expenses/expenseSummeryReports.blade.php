<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Report</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f2f2f2;
            font-size: 12px;

        }
        p{
            margin: 2px;
        }

        /* -------- A4 Layout -------- */
        .print-container {
            width: 210mm;
            min-height: 297mm;
            padding: 1mm;
            margin: 10px auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        /* -------- Table Fix -------- */
        table {
            width: 100%;
            border-collapse: collapse !important;
        }

        table th, table td {
            border: 1px solid #000 !important;
            padding: 6px 8px;
        }

        thead th {
            background: #e9ecef !important;
        }

        tr, td, th {
            page-break-inside: avoid !important;
        }

        /* -------- Print Mode -------- */
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
                margin: 1mm;
            }
        }
    </style>
</head>

<body>

<div class="print-container">

    @if($expenses)

    <div class="text-center mb-2">
        <img src="{{asset(general()->logo())}}" alt="logo" style="max-height: 80px;">
        <h2>{{general()->title}}</h2>
        <p>
            {!!general()->address_one!!}<br>
            <b>Phone:</b> {{general()->mobile}}
            &nbsp; | &nbsp;
            <b>Email:</b> {{general()->email}}<br>
            <b>Date:</b> {{ date('d M, Y') }}
        </p>

        <span style="display: inline-block;padding: 2px 25px;border: 1px solid #ddd;border-radius: 4px;background: #fbfbfb;">
            Expense Report
        </span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 120px;">SL</th>
                <th style="width: 250px;">Particulars</th>
                <th style="width: 150px;">Amount TK.</th>
                <th style="width: 120px;">SL</th>
                <th style="width: 250px;">Particulars</th>
                <th style="width: 150px;">Amount TK.</th>
            </tr>
        </thead>

        <tbody>
            @dd($expenses);
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->created_at->format('d.m.Y') }}</td>
                <td>{!! nl2br(e($expense->description)) !!}</td>
                <td>{{ $expense->method->name ?? 'Not Found' }}</td>
                <td>{{ $expense->category->name ?? 'Not Found' }}</td>
                <td>{{ $expense->branch->name ?? 'Not Found' }}</td>
                <td>{{ numberFormat($expense->amount,2) }}</td>
            </tr>
            @endforeach

            @if($expenses->count()==0)
            <tr>
                <td colspan="6" class="text-center">No Data Found</td>
            </tr>
            @endif
        </tbody>

        <tfoot>
            <tr>
                <th colspan="4"></th>
                <th>Total</th>
                <th>{{ numberFormat($expenses->sum('amount'),2) }}</th>
            </tr>
        </tfoot>
    </table>

    @else
    <span>No Report Data Found</span>
    @endif
</div>

<script>
    // window.print();
</script>

</body>
</html>
