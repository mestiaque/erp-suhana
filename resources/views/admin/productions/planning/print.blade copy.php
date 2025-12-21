@extends(adminTheme().'layouts.app')

@section('title')
<title>Production Planning Print</title>
@endsection

@section('contents')

<div class="flex-grow-1">

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Production Planning Statement</h3>
            <div class="dropdown">
                <a href="{{ route('admin.productionPlanning') }}" class="btn-custom primary">
                    <i class="bx bx-left-arrow-alt"></i> Back List
                </a>
                <a href="javascript:void(0)" id="PrintAction" class="btn-custom yellow" onclick="window.print();">
                    <i class="bx bx-printer"></i> Print
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="PrintAreaContact">
                <style>
                    .print-container {
                        max-width: 1400px;
                        margin: 0 auto;
                        background: white;
                        padding: 5px 30px;
                        font-family: "Calibri", "Segoe UI", sans-serif;
                        font-size: 13px;
                    }
                    .header-title {
                        text-align: center;
                        font-size: 2rem;
                        font-weight: bold;
                        color: #0047ab;
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
                    .section-title {
                        font-weight: bold;
                        margin-top: 15px;
                        margin-bottom: 5px;
                        font-size: 14px;
                        border-bottom: 1px solid #000;
                    }
                    @media print {
                        .btn-custom { display: none; }
                    }
                </style>

                <div class="print-container">

                    <div class="header-title">
                        {{ general()->title }} <br>
                        <small>(Production Planning Statement)</small>
                    </div>

                    {{-- Overview --}}
                    <div class="section-title">Overview</div>
                    <table>
                        <tr>
                            <td><b>Style No</b></td>
                            <td>{{ $plan->style_no }}</td>
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
                            <td>{{ $plan->cutting_start }} → {{ $plan->cutting_end }}</td>
                            <td><b>Sewing</b></td>
                            <td>{{ $plan->sewing_start }} → {{ $plan->sewing_end }}</td>
                        </tr>
                        <tr>
                            <td><b>Packing</b></td>
                            <td>{{ $plan->packing_start }} → {{ $plan->packing_end }}</td>
                            <td><b>Shipment</b></td>
                            <td>{{ $plan->shippment_start }} → {{ $plan->shippment_end }}</td>
                        </tr>
                    </table>

                    {{-- Floor / Line Details --}}
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

                    {{-- Optional Notes --}}
                    @if($plan->remarks)
                        <div class="section-title">Remarks</div>
                        <p>{{ $plan->remarks }}</p>
                    @endif

                    <div style="margin-top:50px; display:table; width:100%;">
                        <div style="display:table-row;">
                            <div style="display:table-cell; width:50%; text-align:center;">
                                <p>For {{ general()->title }}</p>
                                @if(general()->signature())
                                    <img src="{{ asset(general()->signature()) }}" alt="Sign" style="max-width: 10rem;">
                                @endif
                                <p>Authorized Signature</p>
                            </div>
                            <div style="display:table-cell; width:50%; text-align:center;">
                                <p>For Buyer</p>
                                <p>Authorized Signature</p>
                            </div>
                        </div>
                    </div>

                    <p style="text-align:right; margin-top:20px;">Generated On: {{ now()->format('d-m-Y H:i') }}</p>

                </div>

            </div>
        </div>
    </div>

</div>

@endsection
