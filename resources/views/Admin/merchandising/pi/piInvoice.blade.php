@extends(adminTheme().'layouts.app')

@section('title')
<title>Proforma Invoice View</title>
@endsection

@section('contents')

<div class="flex-grow-1">
    <!-- Breadcrumb -->
    <div class="breadcrumb-area">
        <h1>Proforma Invoice</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.proformaInvoice') }}">Proforma Invoice List</a></li>
            <li class="item">View Proforma Invoice</li>
        </ol>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Proforma Invoice View</h3>
            <div class="dropdown">
                <a href="{{ route('admin.proformaInvoice') }}" class="btn-custom primary">
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
                    .tableInfo tr td {
                        border: none;
                        padding: 2px 5px;
                        font-size: 15px;
                        font-weight: bold;
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
                    table th, td{
                        padding: 2px !important;
                    }
                    @media print {
                        .invoice-box {
                            box-shadow: none;
                        }
                    }
                </style>

                <div class="invoice-container invoice-inner">

                    <div class="invoice-header">
                        <div style="text-align:center;">
                            <h2 style="" class="company-title">
                                <img src="{{asset(general()->logo())}}" alt="logo" style="max-height: 60px; margin-right: 1rem;">
                                   {{general()->title}}
                            </h2>
                            <p style="color: coral;margin-top: -1.3rem;    margin-bottom: 5px;">(100%Export Oriented Garments Manufacturing Factory)</p>
                            <p style="margin-top: -10px; margin-bottom:2px">
                                {!!general()->address_one!!}
                            </p>
                            <p style="margin-top: -10px; margin-bottom:2px">
                                <span style="color: #0000ff8c">
                                    <b>Phone:</b> {{general()->mobile}}
                                    <b>Email:</b> {{general()->email}}
                                </span>
                            </p>
                            <hr style="border-bottom: 1px solid #2125298c;margin: 1px;">
                            <h6 style="margin:2px;margin-top:5px;"><b>PROFORMA INVOICE</b> </h6>
                        </div>
                    </div>
                    <div>
                        <div class="row" style="margin:0px">
                            <div class="col-8" style="padding:0px;">
                                Proforma Invoice no. {{ $pi->pi_no}}
                                <div style="margin-top: 2rem;">
                                    Beneficiary :
                                    <br><b>{{general()->title}}</b>
                                    <br>
                                        @php
                                            $words = preg_split('/\s+/', strip_tags(general()->address_one));
                                            $chunks = array_chunk($words, 4);
                                        @endphp

                                        @foreach($chunks as $line)
                                            {{ implode(' ', $line) }} <br>
                                        @endforeach
                                </div>
                                <div  style="margin-top: 2rem;">
                                    <b>Buyer : {{$pi->buyer->name}}</b>
                                    <br>
                                        @php
                                            $words = preg_split('/\s+/', strip_tags($pi->buyer->address_line1));
                                            $chunks = array_chunk($words, 4);
                                        @endphp

                                        @foreach($chunks as $line)
                                            {{ implode(' ', $line) }} <br>
                                        @endforeach
                                </div>
                            </div>
                            <div class="col-4" style="">
                                <div class="padding:5px;">
                                    Date : {{ $pi->created_at->format('d.m.Y') }}
                                    <div  style="margin-top: 1.2rem;">
                                        {!! nl2br(e($pi->advising_bank)) !!}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="table-responsive">
                            <table class="table table-bordered invoice-table">
                                <thead>
                                    <tr>
                                        <th>SL NO.</th>
                                        <th>STYLE</th>
                                        <th>Size Range</th>
                                        <th>Item Description</th>
                                        <th>Fabric / Composition</th>
                                        <th>Quantity (Pc)</th>
                                        <th>Unit Price</th>
                                        <th>Total Amount</th>
                                        <th>Shipment Date</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>

                                @php
                                    $prevShipmentDate = null;
                                    $rowspanTracker = [];
                                @endphp

                                @forelse($pi->items as $i=>$item)

                                    @php
                                        // Format shipment date
                                        $shipmentDate = $item->orderDetails->shipment_date
                                            ? \Carbon\Carbon::parse($item->orderDetails->shipment_date)->format('d.m.Y')
                                            : 'N/A';

                                        // Count how many consecutive rows have same shipment date (for rowspan)
                                        if(!isset($rowspanTracker[$shipmentDate])) {
                                            $rowspanTracker[$shipmentDate] = $pi->items
                                                ->slice($i) // remaining items
                                                ->takeWhile(fn($it) =>
                                                    $it->orderDetails->shipment_date
                                                        ? \Carbon\Carbon::parse($it->orderDetails->shipment_date)->format('d.m.Y') == $shipmentDate
                                                        : $shipmentDate == 'N/A'
                                                )
                                                ->count();
                                        }

                                        $rowspan = $rowspanTracker[$shipmentDate];
                                    @endphp

                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $item->style_no }}</td>
                                        <td>{{ $item->size ?? '' }}</td>
                                        <td>{{ $item->description ?? '' }}</td>
                                        <td>
                                            {{ $item->fabrication }},
                                            {{ $item->gsm }} gsm
                                        </td>
                                        <td>{{ number_format($item->order_qty) }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ number_format($item->total_price, 2) }}</td>
                                        @if($prevShipmentDate !== $shipmentDate)
                                            <td rowspan="{{ $rowspan }}">{{ $shipmentDate }}</td>
                                        @endif
                                        <td>{{$pi->remarks}}</td>
                                    </tr>
                                     @php $prevShipmentDate = $shipmentDate; @endphp
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">No items found</td>
                                    </tr>
                                @endforelse
                                </tbody>

                                <tfoot style="font-weight: 600;">
                                    <tr>
                                        <td colspan="5" class="text-center">Total</td>
                                        <td>{{ number_format($pi->items->sum('order_qty')) }}</td>
                                        <td></td>
                                        <td>{{ number_format($pi->items->sum('total_price'),2) }}</td>
                                        <td colspan=""></td>
                                        <td colspan=""></td>
                                    </tr>
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <input type="hidden" name="total_amount_input" id="total_amount_input" value="{{ $pi->items->sum('total_price') }}">
                                            In Words - Total Amount (Tk) : <span id="total_amount_word"></span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="row m-0 p-0">
                        <div class="col-10 pl-0">
                            @if($pi->terms)
                                <div style="margin-top: 1rem">
                                    <h6 style="font-size: 13px;">TERMS & CONDITIONS</h2>
                                    <div style="">
                                        @php
                                            $terms = json_decode($pi->terms ?? '{}', true);
                                        @endphp

                                        <table>
                                            @foreach ($terms as $key=>$term)
                                            <tr>
                                                <td style="vertical-align: top; width:20%">{{ $key }}</td>
                                                <td style="vertical-align: top;text-align: center; width:2%">:</td>
                                                <td style="vertical-align: top;  width:68%">{{ $term }}</td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-2 pl-5">
                            BIN : 004569897-0103 <br>
                            ERC : 260326211263822 <br>
                            IRC : 260326120696022 <br>
                            REX : BDREX03324
                        </div>
                    </div>

                    <div style="margin-top: 3.5rem">
                        @if(general()->signature())
                        <p style="margin-bottom: 5px">For  {{general()->title}}</p>
                        <img src="{{asset(general()->signature())}}" alt="Sign" style="max-width: 12.5rem">
                        @else
                        <p style="margin-bottom: 5rem">For  {{general()->title}}</p>
                        @endif
                        <p>Authorized signature</p>
                    </div>

                </div>
           </div>
        </div>
    </div>


</div>

@endsection

@push('js')
<script src="{{asset('admin/assets/js/inword.js')}}"></script>
<script>
    var amount = Number($('#total_amount_input').val());
    var words = toWords(amount);
    $('#total_amount_word').html(words + ' Taka Only');

</script>
@endpush
