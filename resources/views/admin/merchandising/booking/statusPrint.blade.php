<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{websiteTitle('PI Fabric Status')}}</title>
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
    <a href="{{ route('admin.fabricStatus',$id) }}"
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
        <div class="textarea" style="">
            <div class="text-center mb-2" style="">
                <div class="row text-left">
                    <div class="col-1 psss-0">
                        <img src="{{asset(general()->logo())}}" alt="logo" style="max-height: 44px;">
                    </div>
                    <div class="col-8 p-0" style="text-align: left; font-size:16px">
                        <p style="text-align: center; font-size: 40px; font-family: serif; line-height: 39px;">
                            {{general()->title}}
                        </p>
                    </div>
                    <div class="col-3 p-0" style="text-align: left">

                        {!!general()->address_one!!}<br>
                        <b>Phone:</b> {{general()->mobile}}
                        <br>
                        <b>Email:</b> {{general()->email}}<br>
                    </div>
                </div>

                <span style="display: inline-block;padding: 2px 25px;border: 1px solid #ddd;border-radius: 4px;background: #fbfbfb;">
                    PI WISE FABRIC STATUS
                </span>
            </div>
            <table class="table table-bordered table-sm text-center" style="font-size: 0.2rem; vertical-align: middle;">
                <thead class="">
                    <tr>
                        <th rowspan="2">SL</th>
                        <th rowspan="2">BUYER</th>
                        <th rowspan="2">PI NO</th>
                        <th rowspan="2">FABRICATION</th>
                        <th rowspan="2">COLOR</th>
                        <th rowspan="2">YARN COUNT</th>
                        <th rowspan="2">YARN BOOKING</th>
                        <th rowspan="2">INHOUSE YARN</th>
                        <th rowspan="2">KNITTING FACTORY</th>
                        <th rowspan="2">TO YARN DELI KNITTING</th>
                        <th rowspan="2">GREY RECEIVE FOR DYEING</th>
                        <th rowspan="2">GREY RECEIVE BAL</th>
                        <th rowspan="2">KNITTING PROCE 55 LOSS (%)</th>
                        <th rowspan="2">DYEING FACTORY</th>
                        <th rowspan="2">GREY DELIVERY</th>
                        <th rowspan="2">GREY DELIVERY BAL</th>
                        <th rowspan="2">TOTAL DYEING</th>
                        <th rowspan="2">DYEING BALANCE</th>
                        <th colspan="2">DYEING TO FINISHED FAB I/H</th>
                        <th rowspan="2">DYE PROCE 55 LOSS (N)</th>
                        <th rowspan="2">REMARKS</th>
                    </tr>
                    <tr>
                        <th>GREY</th>
                        <th>FINISH</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // ১. ইয়ার্ন কাউন্ট ও ফেব্রিকেশন প্রসেসিং
                        $uniqueYarnCounts = collect();
                        foreach($pi->yarnBookings as $yb) {
                            $countsArray = json_decode($yb->yarn_count, true);
                            if (is_array($countsArray)) {
                                foreach ($countsArray as $item) {
                                    if (!empty($item['count'])) $uniqueYarnCounts->push(trim($item['count']));
                                }
                            }
                        }
                        $yarnCountStr = $uniqueYarnCounts->unique()->implode(', ');
                        $fabrication = $pi->yarnBookings->pluck('fabric_type')->unique()->implode(', ');

                        // ২. ডাটা ক্যালকুলেশন (PI #25 এর জন্য)
                        $totalYarnReq = $pi->yarnBookings->sum('required_qty'); // ১০০০০
                        $totalYarnRecv = \App\Models\YarnReceive::where('pi_id', $pi->id)->sum('receive_qty'); // ৫০০০

                        $totalKnitReq = \App\Models\KnittingBooking::where('pi_id', $pi->id)->sum('booking_qty'); // ১০০০০
                        $totalGreyRecv = \App\Models\KnittingReceive::where('pi_id', $pi->id)->sum('weight'); // ৭০০০

                        // Knitting Loss
                        $knitLossPerc = $totalKnitReq > 0 ? (($totalKnitReq - $totalGreyRecv) / $totalKnitReq) * 100 : 0;

                        // ডাইং ও কালার লজিক
                        $dyeingReceives = \App\Models\DyeingReceive::where('pi_id', $pi->id)->get();
                        $dyeingColors = $dyeingReceives->pluck('color')->unique()->filter();
                        $totalDyeingFinishRecv = $dyeingReceives->sum('receive_qty'); // ফিনিশ ওজন (৬২০)

                        $totalDyeingReq = $pi->dyeingBookings->sum('required_qty'); // ৭০০
                        $dyeingBalance = $totalDyeingReq - $totalDyeingFinishRecv;
                    @endphp

                    <tr>
                        <td>1</td>
                        {{-- BUYER --}}
                        <td>{{ $pi->dyeingBookings->first()->buyer_name ?? ($pi->buyer->buyer_name ?? $pi->buyer_name) }}</td>

                        {{-- PI NO --}}
                        <td>{{ $pi->pi_no }}</td>

                        {{-- FABRICATION --}}
                        <td>{{ $fabrication ?: '--' }}</td>

                        {{-- COLOR --}}
                        <td class="text-left" style="line-height: 1.1;">
                            <span> {{ count($dyeingColors) }} COLORS</span>
                        </td>

                        {{-- YARN COUNT --}}
                        <td>{{ $yarnCountStr ?: '--' }}</td>

                        {{-- YARN BOOKING --}}
                        <td>{{ number_format($totalYarnReq, 2) }}</td>

                        {{-- INHOUSE YARN --}}
                        <td>{{ number_format($totalYarnRecv, 2) }}</td>

                        {{-- KNITTING FACTORY --}}
                        <td>{{ \App\Models\KnittingBooking::where('pi_id', $pi->id)->pluck('knitting_unit')->unique()->filter()->implode(', ') ?: '--' }}</td>

                        {{-- TO YARN DELI KNITTING (Knitting Booking) --}}
                        <td>{{ number_format($totalKnitReq, 2) }}</td>

                        {{-- GREY RECEIVE FOR DYEING --}}
                        <td>{{ number_format($totalGreyRecv, 2) }}</td>

                        {{-- GREY RECEIVE BAL (বুকিং - রিসিভ) --}}
                        <td>{{ number_format($totalKnitReq - $totalGreyRecv, 2) }}</td>

                        {{-- KNITTING PROCESS LOSS (%) --}}
                        <td>{{ number_format($knitLossPerc, 2) }}%</td>

                        {{-- DYEING FACTORY --}}
                        {{-- <td>{{ $pi->dyeingBookings->pluck('remarks')->unique()->filter()->implode(', ') ?: '--' }}</td> --}}
                        <td>{{ \App\Models\DyeingBooking::where('pi_id', $pi->id)->pluck('dyeing_unit')->unique()->filter()->implode(', ') ?: '--' }}</td>


                        {{-- GREY DELIVERY (নিটিং রিসিভকেই গ্রে ডেলিভারি ধরা হয়েছে) --}}
                        <td>{{ number_format($totalGreyRecv, 2) }}</td>

                        {{-- GREY DELIVERY BAL (ধরে নেওয়া হয়েছে ডেলিভারি ব্যালেন্স নেই) --}}
                        {{-- <td>0.00</td> --}}
                        <td>{{ number_format($totalKnitReq - $totalGreyRecv, 2) }}</td>

                        {{-- TOTAL DYEING (Required Qty) --}}
                        <td>{{ number_format($totalDyeingReq, 2) }}</td>

                        {{-- DYEING BALANCE (Required - Finish) --}}
                        <td>{{ number_format($dyeingBalance, 2) }}</td>

                        {{-- DYEING TO FINISHED FAB I/H (GREY Input) --}}
                        <td>{{ number_format($totalGreyRecv, 2) }}</td>

                        {{-- DYEING TO FINISHED FAB I/H (FINISH Output) --}}
                        <td>{{ number_format($totalDyeingFinishRecv, 2) }}</td>

                        {{-- DYE PROCESS LOSS (%) (Grey to Finish Loss Percentage) --}}
                        <td>
                            @php
                                $dyeLossQty = $totalGreyRecv - $totalDyeingFinishRecv;
                                $dyeLossPerc = $totalGreyRecv > 0 ? ($dyeLossQty / $totalGreyRecv) * 100 : 0;
                            @endphp
                            {{ number_format($dyeLossPerc, 2) }}%
                        </td>


                        {{-- REMARKS --}}
                        <td>{{ $pi->remarks ?: '--' }}</td>
                    </tr>
                </tbody>
                <tfoot class="font-weight-bold bg-light">
                    <tr>
                        <td colspan="6" class="text-right">TOTAL</td>

                        {{-- YARN BOOKING --}}
                        <td>{{ number_format($totalYarnReq, 2) }}</td>

                        {{-- INHOUSE YARN --}}
                        <td>{{ number_format($totalYarnRecv, 2) }}</td>

                        {{-- KNITTING FACTORY (খালি থাকবে) --}}
                        <td></td>

                        {{-- TO YARN DELI KNITTING --}}
                        <td>{{ number_format($totalKnitReq, 2) }}</td>

                        {{-- GREY RECEIVE FOR DYEING --}}
                        <td>{{ number_format($totalGreyRecv, 2) }}</td>

                        {{-- GREY RECEIVE BAL (বুকিং - রিসিভ) --}}
                        <td>{{ number_format($totalKnitReq - $totalGreyRecv, 2) }}</td>

                        {{-- KNITTING PROCESS LOSS % (খালি থাকবে) --}}
                        <td>
                            @php
                                $totalKnitLossQty = $totalKnitReq - $totalGreyRecv;
                                $totalKnitLossPerc = $totalKnitReq > 0 ? ($totalKnitLossQty / $totalKnitReq) * 100 : 0;
                            @endphp
                            {{ number_format($totalKnitLossPerc, 2) }}%
                        </td>

                        {{-- DYEING FACTORY (খালি থাকবে) --}}
                        <td></td>

                        {{-- GREY DELIVERY --}}
                        <td>{{ number_format($totalGreyRecv, 2) }}</td>

                        {{-- GREY DELIVERY BAL (বডি অনুযায়ী এটি বুকিং - রিসিভ হবে) --}}
                        <td>{{ number_format($totalKnitReq - $totalGreyRecv, 2) }}</td>

                        {{-- TOTAL DYEING (Required) --}}
                        <td>{{ number_format($totalDyeingReq, 2) }}</td>

                        {{-- DYEING BALANCE (Required - Finish) --}}
                        <td>{{ number_format($dyeingBalance, 2) }}</td>

                        {{-- DYEING TO FINISHED FAB I/H (GREY Input) --}}
                        <td>{{ number_format($totalGreyRecv, 2) }}</td>

                        {{-- DYEING TO FINISHED FAB I/H (FINISH Output) --}}
                        <td>{{ number_format($totalDyeingFinishRecv, 2) }}</td>

                        {{-- DYE PROCESS LOSS (%) --}}
                        <td>
                            @php
                                $totalDyeLossQty = $totalGreyRecv - $totalDyeingFinishRecv;
                                $totalDyeLossPerc = $totalGreyRecv > 0 ? ($totalDyeLossQty / $totalGreyRecv) * 100 : 0;
                            @endphp
                            {{ number_format($totalDyeLossPerc, 2) }}%
                        </td>

                        {{-- REMARKS --}}
                        <td></td>
                    </tr>
                </tfoot>
            </table>
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
