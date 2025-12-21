@extends(adminTheme().'layouts.app')

@section('title')
<title>Production Planning View</title>
@endsection

@section('contents')

<div class="flex-grow-1">
    <!-- Breadcrumb -->
    <div class="breadcrumb-area">
        <h1>Production Planning</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.productionPlanning') }}">Production Planning List</a></li>
            <li class="item">View Production Planning</li>
        </ol>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Production Planning View</h3>
            <div class="dropdown">
                <a href="{{ route('admin.productionPlanning') }}" class="btn-custom primary">
                    <i class="bx bx-left-arrow-alt"></i> Back List
                </a>
                <a href="javascript:void(0)" id="PrintAction" class="btn-custom yellow">
                    <i class="bx bx-printer"></i> Print
                </a>
            </div>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')
            <div class="PrintAreaContact">
                <style>
                    .invoice-container {
                        max-width: 1400px;
                        margin: 0 auto;
                        background: white;
                        padding: 5px 30px;
                        box-shadow: 0 0 10px rgba(0,0,0,0.1);
                        position: relative;
                        overflow: hidden;
                        font-family: "Calibri", "Segoe UI", sans-serif;
                        font-size: 12px;
                    }
                    .invoice-box {
                        background: white;
                        border-radius: 6px;
                        padding: 30px;
                        box-shadow: 0 2px 10px rgba(0,0,0,.08);
                        font-size: 14px;
                        color: #000;
                    }

                    .invoice-header {
                        margin-bottom: 5px;
                    }

                    .invoice-header img {
                        height: 80px;
                    }

                    .badge-lg {
                        padding: 6px 12px;
                        border-radius: 6px;
                        font-size: 13px;
                    }

                    .section-title {
                        font-size: 15px;
                        font-weight: bold;
                        border-bottom: 2px solid #e3e3e3;
                        padding-bottom: 3px;
                        margin-bottom: 15px;
                    }

                    .info-list li {
                        border: none !important;
                        padding: 4px 0 !important;
                        font-size: 14px;
                    }

                    .invoice-table th {
                        background: #f6f6f6;
                        /* font-size: 13px; */
                        color:black;
                    }

                    .invoice-summary {
                        margin-top: 10px;
                        font-size: 14px;
                        font-weight: bold;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 10px;
                        font-size: 13px;
                    }
                    table, th, td {
                        border: 1px solid black;
                        padding: 5px;
                        text-align: left;
                    }
                    th {
                        background: #f6f6f6;
                    }
                    .company-title{
                        font-weight: bolder;
                        color: blue;
                        font-family: none;
                        font-size: 3rem;
                        display: flex;
                        text-align: center;
                        width: 100%;
                        justify-content: center;
                    }
                    .uppercase {
                        text-transform: uppercase;
                    }

                    @media print {
                        .invoice-box {
                            box-shadow: none;
                        }
                    }
                </style>

                <div class="invoice-container invoice-inner">

                    <div class="invoice-header" >
                        <div style="text-align:center;">

                            <div style="width:100%; display:table; table-layout:fixed; margin-bottom:5px">
                                <div style="display:table-row;">

                                    <!-- LOGO : 10% -->
                                    <div style="display:table-cell; width:10%; vertical-align:middle;">
                                        <img src="{{ asset(general()->logo()) }}"
                                            alt="logo"
                                            style="max-height:65px;">
                                    </div>

                                    <!-- TITLE : 50% -->
                                    <div style="display:table-cell; width:50%; vertical-align:top; text-align:center;">
                                        <div style="font-size:40px; font-weight:800; color:#0047ab; font-family:'Times New Roman', Times, serif; height:4rem">
                                            {{ general()->title }}
                                        </div>
                                        <div style="font-size:12px; color:coral; margin-top:-12px;">
                                            (100% Export Oriented Garments Manufacturing Factory)
                                        </div>
                                    </div>

                                    <!-- ADDRESS : 40% -->
                                    <div style="display:table-cell; width:40%; vertical-align:middle; font-size:12px;">
                                        <div>
                                            {!! general()->address_one !!}
                                        </div>
                                        <div style="margin-top:2px; color:#0047ab;">
                                            <b>Phone:</b> {{ general()->mobile }}<br>
                                            <b>Email:</b> {{ general()->email }}
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <hr style="border-bottom: 1px solid #2125298c;margin: 1px; ">
                            <h6 style="margin:2px;margin-top:5px;"><b>PRODUCTION PLANNING</b> </h6>
                        </div>
                    </div>

                    <div>

                        <div class="section-title">Overview</div>
                        <table class="invoice-table " style="margin-bottom: 1.2rem">
                            <tr>
                                <td><b>Style No</b></td>
                                <td style="font-size: 17px;padding-bottom: 0; padding-top: 0;"><b>{{ $plan->style_no }}</b></td>
                                <td><b>Status</b></td>
                                <td>{{ ucfirst($plan->status) }}</td>
                            </tr>
                            <tr>
                                <td><b>Quantity</b></td>
                                <td>{{ $plan->style_qty }}</td>
                                <td><b>Total Hourly Capacity</b></td>
                                <td>{{ $plan->sewingLines->sum('capacity_hour') }}</td>
                            </tr>
                            <tr>
                                <td><b>Total Working Time</b></td>
                                <td>{{ $plan->total_working_time }}</td>
                                <td><b>Extra Time</b></td>
                                <td>{{ $plan->extra_time }} min</td>
                            </tr>
                            <tr>
                                <td><b>Cutting</b></td>
                                <td>{{ $plan->cutting_start?->format('d.m.Y H:i A') }} → {{ $plan->cutting_end?->format('d.m.Y H:i A') }}</td>
                                <td><b>Sewing</b></td>
                                <td>{{ $plan->sewing_start?->format('d.m.Y H:i A') }} → {{ $plan->sewing_end?->format('d.m.Y H:i A') }}</td>
                            </tr>
                            <tr>
                                <td><b>Packing</b></td>
                                <td>{{ $plan->packing_start?->format('d.m.Y H:i A') }} → {{ $plan->packing_end?->format('d.m.Y H:i A') }}</td>
                                <td><b>Planning By</b></td>
                                <td>{{ $plan->user?->name }} ({{ $plan->created_at->format('d.m.Y') }})</td>
                            </tr>
                        </table>


                        <div class="section-title">Floor & Line Details</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Floor</th>
                                    <th>Line</th>
                                    <th>Capacity/Hour</th>
                                    <th>Working Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plan->sewingLines as $line)
                                <tr>
                                    <td>{{ $line->floor_name }}</td>
                                    <td>{{ $line->line_name }}</td>
                                    <td>{{ $line->capacity_hour }}</td>
                                    <td>{{ $line->working_hours }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" style="text-align:center"><b>Total</b></td>
                                    <td>{{ $plan->sewingLines->sum('capacity_hour') }}</td>
                                    <td>{{ $plan->sewingLines->sum('working_hours') }}</td>
                                </tr>
                            </tfoot>
                        </table>


                    </div>

                    <div style="margin-top: 3.5rem">
                        <div style="width:100%; display: table; table-layout: fixed;">
                           <div style="display: table-row;">
                               <div style="display: table-cell; width:33.33%; padding:10px; ">
                                    <p style="margin-bottom: 5rem">For Merchandiser</p>
                                    <p>Authorized signature</p>
                               </div>
                               <div style="display: table-cell; width:33.33%; padding:10px; ">
                               </div>
                               <div style="display: table-cell; width:33.33%; padding:10px; ">
                                    <p style="margin-bottom: 5rem">For Production Manager</p>
                                    <p>Authorized signature</p>
                               </div>
                           </div>
                       </div>
                    </div>

                </div>
           </div>
        </div>
    </div>


</div>

@endsection

