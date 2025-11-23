<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Factory Expenditure Statement</title>
    <link rel="apple-touch-icon" href="{{asset(general()->favicon())}}" />
    <link rel="shortcut icon" type="image/x-icon" href="{{asset(general()->favicon())}}" />
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

        /* -------- Table Fix -------- */
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
                background: none;
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
</head>

<body>
<div class="no-print-container"
     style="
        position:sticky;
        top:0;
        z-index:999;
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:10px 0;
        margin-bottom:15px;
     ">

    <!-- Back Button (Left) -->
    <a href="{{ route('admin.expenseReports') }}"
       style="
            padding:6px 18px;
            background:#6c757d;
            color:#fff;
            border-radius:4px;
            text-decoration:none;
            font-size:14px;
            border:1px solid #6c757d;
       ">
        ← Back
    </a>

    <!-- Print Button (Right) -->
    <button id="PrintAction"
        style="
            padding:6px 18px;
            background:#0d6efd;
            color:#fff;
            border-radius:4px;
            border:1px solid #0d6efd;
            font-size:14px;
            cursor:pointer;
        ">
        🖨️ Print
    </button>

</div>
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
            Daily Factory Expenditure Statement
        </span>
    </div>


    @php
        $items = $expenseTypes;

        // প্রথমে শুধু সেগুলো নিন যাদের amount > 0
        $filteredItems = $items->filter(function($item) use ($expenses){
            return $expenses->where('category_id', $item->id)->sum('amount') > 0;
        })->values();

        $count = $filteredItems->count();
        $half = ceil($count / 2);

        $leftItems  = $filteredItems->slice(0, $half)->values();
        $rightItems = $filteredItems->slice($half)->values();

        $leftSubTotal = 0;
        $rightSubTotal = 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width: 60px;">SL</th>
                <th style="width: 200px;">Particulars</th>
                <th style="width: 120px;">Amount</th>

                <th style="width: 60px;">SL</th>
                <th style="width: 200px;">Particulars</th>
                <th style="width: 120px;">Amount</th>
            </tr>
        </thead>

        <tbody>

            @for($i = 0; $i < $half; $i++)

                @php
                    // Left
                    $left = $leftItems[$i] ?? null;
                    $leftTotal = $left ? $expenses->where('category_id',$left->id)->sum('amount') : 0;
                    $leftSubTotal += $leftTotal;

                    // Right
                    $right = $rightItems[$i] ?? null;
                    $rightTotal = $right ? $expenses->where('category_id',$right->id)->sum('amount') : 0;
                    $rightSubTotal += $rightTotal;
                @endphp

                <tr>
                    {{-- LEFT --}}
                    <td>{{ $left ? ($i+1) : '' }}</td>
                    <td>{!! $left ? nl2br(e($left->name)) : '' !!}</td>
                    <td class="text-end">{{ $left ? numberFormat($leftTotal,2) : '' }}</td>

                    {{-- RIGHT --}}
                    <td>
                        @if($right)
                            {{ $i + 1 + $half }}
                        @endif
                    </td>
                    <td>{!! $right ? nl2br(e($right->name)) : '' !!}</td>
                    <td class="text-end">{{ $right ? numberFormat($rightTotal,2) : '' }}</td>
                </tr>

            @endfor

        </tbody>

        <tfoot>
            <tr>
                <th></th>
                <th class="text-end">Sub Total #</th>
                <th class="text-end">{{ numberFormat($leftSubTotal,2) }}</th>

                <th></th>
                <th class="text-end">Sub Total #</th>
                <th class="text-end">{{ numberFormat($rightSubTotal,2) }}</th>
            </tr>

            <tr>
                <th colspan="6" style="text-align: center">
                    Grand Total # {{ numberFormat($leftSubTotal + $rightSubTotal,2) }}
                </th>
            </tr>
            <tr>
                <th colspan="6" style="text-align: center">
                    <input type="hidden" name="total_amount_input" id="total_amount_input" value="{{ $leftSubTotal + $rightSubTotal }}">
                    In Words - Total Amount (Tk) : <span id="total_amount_word"></span>
                </th>
            </tr>
        </tfoot>
    </table>


    @else
    <span>No Report Data Found</span>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{asset('admin/assets/js/inword.js')}}"></script>
<script>
    // window.print();
    document.getElementById('PrintAction').addEventListener('click', function () {
        window.print();
    });

    var amount = Number($('#total_amount_input').val());
    console.log(amount);
    var words = toWords(amount);
    $('#total_amount_word').html(words + ' Taka Only');

</script>

</body>
</html>
