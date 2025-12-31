@extends(adminTheme().'layouts.app')

@section('title')
<title>Production View</title>
@endsection

@section('contents')

<div class="flex-grow-1">
    <!-- Breadcrumb -->
    <div class="breadcrumb-area">
        <h1>Production Planning</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.production') }}">Production List</a></li>
            <li class="item">Production View</li>
        </ol>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Production View</h3>
            <div class="dropdown">
                <a href="{{ route('admin.production') }}" class="btn-custom primary">
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
                        padding: 1px 10px;
                        color:black;
                    }
                    .invoice-table td {
                        padding: 1px 10px;
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
                    .table-bordered td {
                        padding: 5px;
                        text-align: left;
                    }
                    .table-bordered th {
                        padding: 5px;
                        text-align: left;
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
                            <h6 style="margin:2px;margin:5px 0;"><b>PRODUCTION VIEW</b> </h6>
                            <h6>Style No: {{ $plan->style_no }}</h6>
                            <h6>Buyer Name: {{$plan->style?->buyer_name}}</h6>
                        </div>
                    </div>

                    <div>

                        <div class="row">
                            <div class="col-4">
                                <div class="section-title">Planning</div>
                                <ul class="info-list p-0" style="list-style:none;">
                                    <li><strong>Merchandiser:</strong> {{ $plan->style?->merchant_name }}</li>
                                    <li><strong>Style No:</strong> {{ $plan->style_no }}</li>
                                    <li><strong>Order Quantity:</strong> {{ number_format($plan->style_qty) }} pcs</li>
                                    <li><strong>Total Working Hours:</strong> {{ $plan->total_working_time }}</li>
                                    <li><strong>Plan By:</strong> {{ $plan->user?->name }}</li>
                                    <li><strong>Planning Date:</strong> {{ $plan->created_at->format('d.m.Y') }}</li>
                                    <li><strong>Hourly Capacity:</strong> {{ $plan->total_hourly_capacity }} pcs</li>
                                    <li>
                                        @php
                                            $lines = $plan->floorLines()->groupBy('name');
                                        @endphp

                                        @foreach($lines as $name => $items)
                                        <p>
                                            <b>{{ $name }}</b> : <br>
                                            @foreach($items as $line)
                                            @php
                                                $exSew = App\Models\ProductionSewing::where('planning_id', $plan->id)->where('line_name', $line->slug)->first();
                                            @endphp
                                            Line: {{ $line->slug }}
                                            @endforeach
                                        </p>
                                        @endforeach

                                    <b>Lose Time (In Minite):</b> {{$plan->extra_time}}

                                    </li>
                                </ul>
                            </div>
                            <div class="col-8">
                                <div class="section-title">Production</div>
                                <ul class="info-list p-0" style="list-style:none;">
                                    <li><strong>Total Output:</strong> {{ $plan->sewingOutputs->sum('production') }} Pcs</li>
                                    <li><strong>Remaining Qty:</strong> {{ $plan->style_qty - $plan->sewingOutputs->sum('production') }} Pcs</li>
                                </ul>

                                @foreach($plan->sewingOutputs->groupBy('date') as $date => $outputs)

                                    @php
                                        $outputs = $outputs->sortBy('hour');
                                        $hours = $outputs->pluck('hour')->unique();
                                    @endphp

                                    <h6 class="mt-3">
                                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}
                                    </h6>

                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                @foreach($hours as $hour)
                                                    <th>
                                                        {{ $hour }}.00 - {{ $hour + 1 }}.00
                                                    </th>
                                                @endforeach
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                @foreach($hours as $hour)
                                                    @php
                                                        $row = $outputs->firstWhere('hour', $hour);
                                                    @endphp
                                                    <td>
                                                        {{ $row?->production ?? 0 }} Pcs
                                                    </td>
                                                @endforeach
                                                <td>
                                                    {{ $outputs->sum('production') }} Pcs
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                @endforeach


                            </div>

                        </div>


                    </div>


                </div>
           </div>
        </div>
    </div>
</div>

@endsection

