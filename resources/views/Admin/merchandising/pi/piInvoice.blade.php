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
            <h3>Purchase Order View</h3>
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
                        padding: 40px 50px;
                        box-shadow: 0 0 10px rgba(0,0,0,0.1);
                        position: relative;
                        overflow: hidden;
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
                        margin-bottom: 15px;
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
                        font-size: 13px;
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
                    @media print {
                        .invoice-box {
                            box-shadow: none;
                        }
                    }
                </style>

                <div class="invoice-container invoice-inner">

                    <div class="invoice-header">
                        <div style="text-align:center;">
                            <h2>{{general()->title}}</h2>
                            <p>
                                {{general()->address_one}}
                                <br>
                                <b>Mobile:</b> {{general()->mobile}} <b>Email:</b> {{general()->email}} 
                            </p>
                            <h4 style="margin:0;font-family: itially;font-style: italic;text-decoration: underline;">Proforma Invoice</h4>
                        </div>
                    </div>

                    <div>
                        <div class="row" style="margin: 0;border: 1px solid gray;">
                            <div class="col-6" style="padding:5px;">
                                <table class="table tableInfo">
                                        <tr>
                                            <td style="min-width:160px;width: 160px;">Customer Name:</td>
                                            <td style="width: 30px;text-align: center;">:</td>
                                            <td style="min-width:200px;">{{$pi->order?->company_name}}</td>
                                        </tr>
                                        <tr>
                                            <td>Buyer</td>
                                            <td style="text-align: center;">:</td>
                                            <td>{{ $pi->buyer?->name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Order No</td>
                                            <td style="text-align: center;">:</td>
                                            <td>{{$pi->order_no}}</td>
                                        </tr>
                                        <tr>
                                            <td>Booking No</td>
                                            <td style="text-align: center;">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Purchse Order No</td>
                                            <td style="text-align: center;">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Name of Orderer</td>
                                            <td style="text-align: center;">:</td>
                                            <td>{{ $pi->merchant?->name ?? '' }}</td>
                                        </tr>
                                </table>
                            </div>
                            <div class="col-6" style="padding:0px;border-left:1px solid gray;">
                                <div class="padding:5px;">
                                    <table class="table tableInfo">
                                        <tr>
                                            <td style="min-width:175px;width: 175px;">Proforma Invoice No</td>
                                            <td style="width: 30px;text-align: center;">:</td>
                                            <td style="min-width:200px;">{{$pi->order?->company_name}}</td>
                                        </tr>
                                        <tr>
                                            <td>Proforma Invoice Date</td>
                                            <td style="text-align: center;">:</td>
                                            <td>{{ $pi->created_at->format('Y-m-d')}}</td>
                                        </tr>
                                        <tr>
                                            <td>BIN Number</td>
                                            <td style="text-align: center;">:</td>
                                            <td>005225121-1021</td>
                                        </tr>
                                        <tr>
                                            <td>Job Number</td>
                                            <td style="text-align: center;">:</td>
                                            <td>FPL-02561255</td>
                                        </tr>
                                    </table>
                                </div>
                                <div style="border-top: 1px solid gray;border-bottom: 1px solid gray;text-align: center;font-size: 16px;font-weight: bold;">
                                    Advising Bank
                                </div>
                                <p style="padding: 5px;margin: 0;font-weight: bold;">
                                    Bank Detail: Uttara Bank PLC. Uttara Branch, Uttara Model Town, Dhaka, Bangladesh<br>
                                    Account Name: ANR Fashion LTD. Account No: SWIFT: UTBLBDDH465
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="table-responsive">
                            <table class="table table-bordered invoice-table">
                                <thead>
                                <tr>
                                    <th>S/L</th>
                                    <th>Style</th>
                                    <th>Composition</th>
                                    <th>Fabrication</th>
                                    <th>GSM</th>
                                    <th>Color</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                    <th>Commission</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($pi->items as $i=>$item)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td>{{ $item->style_no }}</td>
                                        <td>{{ $item->composition }}</td>
                                        <td>{{ $item->fabrication }}</td>
                                        <td>{{ $item->gsm }}</td>
                                        <td>{{ $item->color_name }}</td>
                                        <td>{{ number_format($item->color_qty) }}</td>
                                        <td>{{ number_format($item->unit_price,2) }}</td>
                                        <td>{{ number_format($item->total_price,2) }}</td>
                                        <td>{{ number_format($item->total_commission,2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-muted">No items found</td>
                                    </tr>
                                @endforelse
                                </tbody>

                                <tfoot>
                                <tr>
                                    <th colspan="6" class="text-right">Total</th>
                                    <th>{{ number_format($pi->items->sum('color_qty')) }}</th>
                                    <th></th>
                                    <th>{{ number_format($pi->items->sum('total_price'),2) }}</th>
                                    <th>{{ number_format($pi->items->sum('total_commission'),2) }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h3> Payment Terms </h2>
                        <ul>
                            <li>
                                Payment must be made through Bank Transfer / LC / TT / Cash (as applicable).
                            </li>
                            <li>
                                Advance payment of ___% must be made at the time of confirming PI.
                            </li>
                            <li>
                                Remaining balance must be settled before shipment or against agreed payment method.
                            </li>
                            <li>
                                All bank charges inside buyer’s country shall be borne by buyer and outside buyer’s country by seller.
                            </li>
                        </ul>
                    </div>
                </div>
           </div>
        </div>
    </div>


</div>

@endsection
