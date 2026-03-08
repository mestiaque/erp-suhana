@extends('printMaster')

@section('title', websiteTitle('Creditor Statement - ' . $user->name))

@section('contents')

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
    .section-title {
        font-size: 15px;
        font-weight: bold;
        border-bottom: 2px solid #e3e3e3;
        padding-bottom: 3px;
        margin-bottom: 15px;
    }
    .invoice-table th {
        background: #f6f6f6;
        color: black;
    }
    .invoice-summary {
        margin-top: 10px;
        font-size: 14px;
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
        vertical-align: middle !important;
    }
    @media print {
        .invoice-box {
            box-shadow: none;
        }
    }
</style>

<div class="invoice-container invoice-inner">
    <!-- Header -->
    <div class="invoice-header">
        <div style="text-align:center;">
            <div style="width:100%; display:table; table-layout:fixed; margin-bottom:5px">
                <div style="display:table-row;">
                    <!-- LOGO -->
                    <div style="display:table-cell; width:10%; vertical-align:middle;">
                        <img src="{{ asset(general()->logo()) }}" alt="logo" style="max-height:65px;">
                    </div>
                    <!-- TITLE -->
                    <div style="display:table-cell; width:70%; vertical-align:top; text-align:center;">
                        <div style="font-size:30px; font-weight:800; color:#0047ab; font-family:'Times New Roman', Times, serif; height:4rem">
                            {{ general()->title }}
                        </div>
                        <div style="font-size:12px; color:coral; margin-top:-12px;">
                            (100% Export Oriented Garments Manufacturing Factory)
                        </div>
                    </div>
                    <!-- ADDRESS -->
                    <div style="display:table-cell; width:20%; vertical-align:middle; font-size:12px; text-align:left;">
                        <div>{!! general()->address_one !!}</div>
                        <div style="margin-top:2px; color:#0047ab;">
                            <b>Phone:</b> {{ general()->mobile }}<br>
                            <b>Email:</b> {{ general()->email }}
                        </div>
                    </div>
                </div>
            </div>
            <hr style="border-bottom: 1px solid #2125298c;margin: 1px; ">
            <h6 style="margin:2px;margin-top:5px;"><b>CREDITOR STATEMENT</b></h6>
        </div>
    </div>

    <!-- Creditor Info -->
    <div class="row" style="margin:0px; margin-top:0.5rem;">
        <div class="col-7" style="padding:0px;">
            <b class="uppercase">Creditor Name :</b> {{ $user->name }}
            <br>
            <b>Company :</b> {{ $user->company_name ?? 'N/A' }}
            <br>
            <b>Mobile :</b> {{ $user->mobile ?? 'N/A' }}
            <br>
            <b>Address :</b> {{ $user->fullAddress() ?? 'N/A' }}
        </div>
        <div class="col-5" style="padding:0px;">
            <b>Print Date :</b> {{ now()->format('d.m.Y') }}
        </div>
    </div>

    <!-- Summary Box -->
    <div class="row mt-3">
        <div class="col-12">
            <table class="table table-bordered invoice-table">
                <thead>
                    <tr>
                        <th class="text-center">Total Purchases</th>
                        <th class="text-center">Total Paid</th>
                        <th class="text-center">Net Due Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{ priceFullFormat($totalPurchases) }}</td>
                        <td class="text-center">{{ priceFullFormat($totalPaid) }}</td>
                        <td class="text-center" style="background: #fff3cd; font-weight: bold;">{{ priceFullFormat($netDue) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="mt-3">
        <div class="table-responsive">
            <table class="table table-bordered invoice-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">SL</th>
                        <th style="width: 100px;">Date</th>
                        <th style="width: 150px;">Invoice/ID</th>
                        <th>Description</th>
                        <th style="width: 100px;" class="text-right">Credit (+)</th>
                        <th style="width: 100px;" class="text-right">Debit (-)</th>
                        <th style="width: 100px;" class="text-right">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ledgerEntries as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->date ? $item->date->format('d-m-Y') : '' }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->note ?? '-' }}</td>
                        <td class="text-right">{{ $item->credit > 0 ? priceFullFormat($item->credit) : '-' }}</td>
                        <td class="text-right">{{ $item->debit > 0 ? priceFullFormat($item->debit) : '-' }}</td>
                        <td class="text-right">{{ priceFullFormat($item->balance) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
