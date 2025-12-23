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
                    .uppercase {
                        text-transform: uppercase;
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
                            <h6 style="margin:2px;margin-top:5px;"><b>PROFORMA INVOICE</b> </h6>
                        </div>
                    </div>


                        <div class="text-center mb-2" style="display:none">
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
                                    Order Details
                                </span>
                            </div>
                    <div>
                        <div class="row d-none" style="margin:0px">
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

                        <div class="row d-none " style="margin:0px">
                            <div class="col-7" style="padding:0px;">
                                Proforma Invoice no. {{ $pi->pi_no }}

                                {{-- Applicant --}}
                                <div style="margin-top: 2rem;">
                                    <b>Applicant :</b>
                                    <br>{!! nl2br(e($pi->applicant)) !!}
                                </div>



                                {{-- 1st Beneficiary --}}
                                <div style="margin-top: 2rem;">
                                    <b>1st Beneficiary :</b>
                                    <br>{!! nl2br(e($pi->first_beneficiary)) !!}
                                </div>



                                {{-- 2nd Beneficiary --}}
                                <div style="margin-top: 2rem;">
                                    <b>2nd Beneficiary :</b>
                                    <br>{!! nl2br(e($pi->second_beneficiary)) !!}
                                </div>

                                {{-- Buyer --}}
                                <div style="margin-top: 2rem;">
                                    <b>Buyer :</b> {{ $pi->buyer?->name ?? '' }}
                                    <br>
                                    @php
                                        $words = preg_split('/\s+/', strip_tags($pi->buyer?->address_line1 ?? ''));
                                        $chunks = array_chunk($words, 4);
                                    @endphp
                                    @foreach($chunks as $line)
                                        {{ implode(' ', $line) }} <br>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-5">
                                <div style="padding:5px;">
                                    Date : {{ $pi->created_at->format('d.m.Y') }}
                                {{-- Applicant Bank --}}
                                    <div style="margin-top: 1rem;">
                                        <b>Applicant Bank :</b>
                                        <br>{!! nl2br(e($pi->applicant_bank)) !!}
                                    </div>
                                            {{-- 1st Beneficiary Bank --}}
                                    <div style="margin-top: 1rem;">
                                        <b>1st Beneficiary Bank :</b>
                                        <br>{!! nl2br(e($pi->first_beneficiary_bank)) !!}
                                    </div>
                                            {{-- 2nd Beneficiary Bank --}}
                                    <div style="margin-top: 1rem;">
                                        <b>2nd Beneficiary Bank :</b>
                                        <br>{!! nl2br(e($pi->second_beneficiary_bank)) !!}
                                    </div>
                                    <div style="margin-top: 1rem;">
                                        <b>Notify Party /Consignee :</b>
                                        <br>{!! nl2br(e($pi->notify_party)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Date Row (optional, you can keep it on top or bottom) --}}
                        <div class="row" style="margin:0px; margin-top:0.5rem;">
                            <div class="col-7" style="padding:0px;">
                                <b>Proforma Invoice no. {{ $pi->pi_no}}</b>
                            </div>
                            <div class="col-5 p-0">
                                <b>
                                    PI Date : {{ $pi->created_at->format('d.m.Y') }}
                                </b>
                            </div>
                        </div>

                         {{-- Applicant Row --}}
                        <div class="row" style="margin:0px; margin-top:0.5rem;">
                            <div class="col-7" style="padding:0px;">
                                @if($pi->applicant !== null)
                                    <b class="uppercase">Applicant :</b>
                                    <br>{!! nl2br(e($pi->applicant)) !!}
                                @endif
                            </div>
                            <div class="col-5" style="padding:0px;">
                                @if($pi->applicant_bank !== null)
                                    <b class="uppercase">Applicant Bank :</b>
                                    <br>{!! nl2br(e($pi->applicant_bank)) !!}
                                @endif
                            </div>
                        </div>

                        {{-- 1st Beneficiary Row --}}
                        <div class="row" style="margin:0px; margin-top:0.5rem;">
                            <div class="col-7" style="padding:0px;">
                                @if($pi->first_beneficiary !== null)
                                    <b class="uppercase">1st Beneficiary :</b>
                                    <br>{!! nl2br(e($pi->first_beneficiary)) !!}
                                @endif
                            </div>
                            <div class="col-5" style="padding:0px;">
                                @if($pi->first_beneficiary_bank !== null)
                                    <b class="uppercase">1st Beneficiary Bank :</b>
                                    <br>{!! nl2br(e($pi->first_beneficiary_bank)) !!}
                                @endif
                            </div>
                        </div>

                        {{-- 2nd Beneficiary Row --}}
                        <div class="row" style="margin:0px; margin-top:0.5rem;">
                            <div class="col-7" style="padding:0px;">
                                @if($pi->second_beneficiary !== null)
                                    <b class="uppercase">2nd Beneficiary :</b>
                                    <br>{!! nl2br(e($pi->second_beneficiary)) !!}
                                @endif
                            </div>
                            <div class="col-5" style="padding:0px;">
                                @if($pi->second_beneficiary_bank !== null)
                                    <b class="uppercase">2nd Beneficiary Bank :</b>
                                    <br>{!! nl2br(e($pi->second_beneficiary_bank)) !!}
                                @endif
                            </div>
                        </div>

                        {{-- Buyer + Notify Party Row --}}
                        <div class="row" style="margin:0px; margin-top:0.5rem;">
                            <div class="col-7" style="padding:0px;">
                                @if($pi->buyer !== null)
                                    <b class="uppercase">Buyer :</b> {{ $pi->buyer?->name ?? '' }}
                                    <br>
                                    @php
                                        $words = preg_split('/\s+/', strip_tags($pi->buyer?->address_line1 ?? ''));
                                        $chunks = array_chunk($words, 4);
                                    @endphp
                                    @foreach($chunks as $line)
                                        {{ implode(' ', $line) }} <br>
                                    @endforeach
                                @endif
                            </div>
                            <div class="col-5" style="padding:0px;">
                                @if($pi->notify_party !== null)
                                    <b class="uppercase">Notify Party / Consignee :</b>
                                    <br>{!! nl2br(e($pi->notify_party)) !!}
                                @endif
                            </div>
                        </div>


                    </div>
                    <div class="mt-4">
                        <div class="table-responsive">
                            <table class="table table-bordered invoice-table">
                                <thead>
                                    <tr>
                                        <th>SN</th>
                                        <th>STYLE</th>
                                        <th>Description</th>
                                        <th>PO Number</th>
                                        <th>Qnty (PCS/SET)</th>
                                        <th>FOB</th>
                                        <th>Total Value</th>
                                        <th>Buyer Del. Date</th>
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

                                        $shipmentDate = 'N/A';

                                        if($item->orderDetails){
                                            $shipmentDate = $item->orderDetails->shipment_date
                                                ? \Carbon\Carbon::parse($item->orderDetails->shipment_date)->format('d.m.Y')
                                                : 'N/A';
                                        }

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
                                        <td>{{ $item->fabrication }}</td>
                                        <td>{{ $item->order_no ?? '--' }}</td>
                                        <td>{{ number_format($item->order_qty) }} {{ $item->uom ?? ''}}</td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td>${{ number_format($item->total_price, 2) }}</td>
                                        @if($prevShipmentDate !== $shipmentDate)
                                            <td rowspan="{{ $rowspan }}">{{ $shipmentDate }}</td>
                                        @endif
                                        <td>{{$pi->remarks}}</td>
                                    </tr>
                                     @php $prevShipmentDate = $shipmentDate; @endphp
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No items found</td>
                                    </tr>
                                @endforelse
                                </tbody>

                                <tfoot style="font-weight: 600;">
                                    <tr>
                                        <td colspan="3" ></td>
                                        <td class="text-center">Total</td>
                                        <td>{{ number_format($pi->items->sum('order_qty')) }}</td>
                                        <td></td>
                                        <td>${{ number_format($pi->items->sum('total_price'),2) }}</td>
                                        <td colspan=""></td>
                                        <td colspan=""></td>
                                    </tr>
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <input type="hidden" name="total_amount_input" id="total_amount_input" value="{{ $pi->items->sum('total_price') }}">
                                            In Words - Total Amount (USD) : <span id="total_amount_word"></span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="row m-0 p-0">
                        <div class="col-9 pl-0">
                            @if($pi->terms !== null && !empty(json_decode($pi->terms, true)))
                                <div style="margin-top: 0.2rem">
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
                        <div class="col-3 pl-5">
                            BIN : 004569897-0103 <br>
                            ERC : 260326211263822 <br>
                            IRC : 260326120696022 <br>
                            REX : BDREX03324
                        </div>
                    </div>


                    <div style="margin-top: 3.5rem ;page-break-inside: avoid;  break-inside: avoid;">
                        <div style="width:100%; display: table; table-layout: fixed;">
                           <div style="display: table-row;">
                               <div style="display: table-cell; width:33.33%; padding:10px; ">
                                    @if(general()->signature())
                                    <p style="margin-bottom: 5px">For  {{general()->title}}</p>
                                    <img src="{{asset(general()->signature())}}" alt="Sign" style="max-width: 12.5rem">
                                    @else
                                    <p style="margin-bottom: 5rem">For  {{general()->title}}</p>
                                    @endif
                                    <p>Authorized signature</p>
                               </div>
                               <div style="display: table-cell; width:33.33%; padding:10px; ">
                               </div>
                               <div style="display: table-cell; width:33.33%; padding:10px; ">
                                    <p style="margin-bottom: 3.9rem">For  Buyer</p>
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

@push('js')
<script src="{{asset('admin/assets/js/inword.js')}}"></script>
<script>
    var amount = Number($('#total_amount_input').val());
    var words = toWords(amount);
    $('#total_amount_word').html(words + ' USD Only');

</script>
@endpush
