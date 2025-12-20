<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{websiteTitle('Daily Factory Expenditure Statement')}}</title>
    <link rel="apple-touch-icon" href="{{asset(general()->favicon())}}" />
    <link rel="shortcut icon" type="image/x-icon" href="{{asset(general()->favicon())}}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f2f2f2;
            font-size: 10px;

        }
        p{
            margin: 2px;
        }

        /* -------- A4 Layout -------- */
        .print-container {
            width: 210mm;
            min-height: 297mm;
            padding: 4mm;
            margin: 0px auto;
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


        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding-top: 20px;
        }

        .signature-box {
            text-align: center;
            flex: 1;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin: 40px 20px 5px 20px;
            position: relative;
        }

        .signature-text {
            font-family: 'Brush Script MT', cursive;
            font-size: 24px;
            margin-top: -35px;
            color: #1a3d0a;
        }


        /* -------- Print Mode -------- */
        @media print {
            body {
                background: none;
                font-size: 10px;
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

     ">

    <!-- Back Button (Left) -->
    <a href="{{ route('admin.orderDetails') }}"
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

    @if($orderDetails)
    <div class="text-center mb-2">
        <div class="row text-left">
            <div class="col-1 psss-0">
                <img src="{{asset(general()->logo())}}" alt="logo" style="max-height: 44px;">
            </div>
            <div class="col-7 p-0" style="text-align: left; font-size:20px">
                <p style="text-align: center; font-size: 40px; font-family: serif; line-height: 39px;">
                    {{general()->title}}
                </p>
            </div>
            <div class="col-4 p-0" style="text-align: center">

                {!!general()->address_one!!}<br>
                <b>Phone:</b> {{general()->mobile}}
                 <br>
                <b>Email:</b> {{general()->email}}<br>
            </div>
        </div>

        <span style="display: inline-block;padding: 2px 25px;border: 1px solid #ddd;border-radius: 4px;background: #fbfbfb;">
            Order Details
        </span>
    </div>

    @php
        $grandOrderQty = 0;
        $grandColorQty = 0;
        $totalOrders   = $orderDetails->count();
        $totalColors   = 0;

        foreach ($orderDetails as $order) {
            $grandOrderQty += $order->total_qty ?? 0;
            $totalColors   += $order->items->count();

            foreach ($order->items as $item) {
                $grandColorQty += $item->qty ?? 0;
            }
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Buyer</th>
                <th>Brand/Customer</th>
                <th>Style No</th>
                <th>Order / PO</th>
                <th>Shipment</th>
                <th>Composition</th>
                <th>Fabrication</th>
                <th>GSM</th>
                <th>Color</th>
                <th class="text-right">Color Qnty</th>
                <th class="text-right">Order Qnty</th>
            </tr>
        </thead>

        <tbody>
            @php $sl = 1; @endphp

            @foreach($orderDetails as $order)
                @php
                    $itemCount = $order->items->count();
                @endphp

                @foreach($order->items as $i => $item)
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $order->buyer_name ?? '--' }}</td>
                        <td>{{ $order->company_name ?? '--' }}</td>
                        <td>{{ $order->style_no ?? '--' }}</td>
                        <td>{{ $order->order_no ?? '--' }}</td>
                        <td>{{ $order->shipment_date ? \Carbon\Carbon::parse($order->shipment_date)->format('d.m.Y') : '--' }}</td>
                        <td>{{ $item->composition ?? $order->composition ?? '--' }}</td>
                        <td>{{ $order->fabrication ?? '--' }}</td>
                        <td>{{ $order->gsm ?? '--' }}</td>
                        <td>{{ $item->color_name ?? '--' }}</td>
                        <td class="text-right">{{ number_format($item->qty) }}</td>

                        {{-- Order Total Qnty (rowspan) --}}
                        @if($i === 0)
                            <td class="text-right" rowspan="{{ $itemCount }}" style="vertical-align: middle; font-weight:600;">
                                {{ number_format($order->total_qty) }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#f8f8f8; font-weight:600;">
                <td colspan="5">Total Orders</td>
                <td colspan="2">{{ $totalOrders }}</td>

                <td colspan="2">Total Colors</td>
                <td colspan="2">{{ $totalColors }}</td>

                <td class="text-right">{{ number_format($grandOrderQty) }}</td>
            </tr>
        </tfoot>

    </table>


    @else
    <span>No Data Found</span>
    @endif


    <div class="signature-section d-none">
        <div class="signature-box">
            <div class="signature-line">
                <div class="signature-text" style="height: 1px;"></div>
            </div>
            <small>Accounts Officer</small>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <div class="signature-text" style="height: 1px;"></div>
            </div>
            <small>Accounts Manager</small>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <div class="signature-text" style="height: 1px;" ></div>
            </div>
            <small>Managing Director</small>
        </div>
    </div>
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
