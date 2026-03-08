@extends('printMaster')

@section('title', 'Proforma Invoice View')

@section('contents')

        <div class="invoice-info">
            <div class="left-info">
                <p><b>Proforma Invoice no.:</b> {{ $pi->pi_no }}</p>
                
                @if($pi->applicant)
                    <p><b>Applicant:</b><br>{!! nl2br(e($pi->applicant)) !!}</p>
                @endif

                @if($pi->first_beneficiary)
                    <p><b>1st Beneficiary:</b><br>{!! nl2br(e($pi->first_beneficiary)) !!}</p>
                @endif

                @if($pi->second_beneficiary)
                    <p><b>2nd Beneficiary:</b><br>{!! nl2br(e($pi->second_beneficiary)) !!}</p>
                @endif

                @if($pi->buyer)
                    <p><b>Buyer:</b> {{ $pi->buyer->name }}<br>
                        @php
                            $words = preg_split('/\s+/', strip_tags($pi->buyer?->address_line1 ?? ''));
                            $chunks = array_chunk($words, 4);
                        @endphp
                        @foreach($chunks as $line)
                            {{ implode(' ', $line) }}<br>
                        @endforeach
                    </p>
                @endif
            </div>

            <div class="right-info">
                <p><b>PI Date:</b> {{ $pi->created_at->format('d.m.Y') }}</p>

                @if($pi->applicant_bank)
                    <p><b>Applicant Bank:</b><br>{!! nl2br(e($pi->applicant_bank)) !!}</p>
                @endif

                @if($pi->first_beneficiary_bank)
                    <p><b>1st Beneficiary Bank:</b><br>{!! nl2br(e($pi->first_beneficiary_bank)) !!}</p>
                @endif

                @if($pi->second_beneficiary_bank)
                    <p><b>2nd Beneficiary Bank:</b><br>{!! nl2br(e($pi->second_beneficiary_bank)) !!}</p>
                @endif

                @if($pi->notify_party)
                    <p><b>Notify Party / Consignee:</b><br>{!! nl2br(e($pi->notify_party)) !!}</p>
                @endif
            </div>
        </div>

        {{-- Items Table --}}
        <div class="table-container">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>STYLE</th>
                        <th>Description</th>
                        <th>Fabrication</th>
                        <th>Composition</th>
                        <th>GSM</th>
                        <th>PO Number</th>
                        <th>Qnty ({{ $pi->items->pluck('uom')->unique()->implode(', ') }})</th>
                        <th>FOB</th>
                        <th>Total Value</th>
                        <th>Del. Date</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pi->items as $i => $item)
                        @php
                            $shipmentDate = $item->orderDetails ? ($item->orderDetails->shipment_date ? \Carbon\Carbon::parse($item->orderDetails->shipment_date)->format('d.m.Y') : 'N/A') : 'N/A';
                        @endphp
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $item->style_no }}</td>
                            <td>{{ $item->getActucalOrder()->items->pluck('item_name')->unique()->implode(', ') }}</td>
                            <td>{{ $item->fabrication }}</td>
                            <td>{{ $item->getActucalOrder()->items->pluck('composition')->unique()->implode(', ') }}</td>
                            <td>{{ $item->getActucalOrder()->items->pluck('gsm')->unique()->implode(', ') }}</td>
                            <td>{{ $item->order_no ?? '--' }}</td>
                            <td>{{ number_format($item->order_qty) }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>${{ number_format($item->total_price, 2) }}</td>
                            <td>{{ $shipmentDate }}</td>
                            <td>{{ $pi->remarks }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="no-items">No items found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-right"><b>Total</b></td>
                        <td>{{ number_format($pi->items->sum('order_qty')) }}</td>
                        <td></td>
                        <td>${{ number_format($pi->items->sum('total_price'),2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="12" class="amount-words">
                            <input type="hidden" id="total_amount_input" value="{{ $pi->items->sum('total_price') }}">
                            In Words - Total Amount (USD) : <span id="total_amount_word"></span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Terms & Conditions --}}
        @if($pi->terms)
            <div class="terms-conditions">
                <h4>TERMS & CONDITIONS</h4>
                <table class="borderless">
                    @php $terms = json_decode($pi->terms, true); @endphp
                    @foreach($terms as $key => $term)
                        <tr>
                            <td class="borderless" style="width: 30%">{{ $key }}</td>
                            <td class="borderless" style="width: 1%">:</td>
                            <td class="borderless" style="width: 69%">{{ $term }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        {{-- Signatures --}}
        <div class="signatures">
            <div class="signature-block">
                <p>For {{ general()->title }}</p>
                @if(general()->signature())
                    <img src="{{ asset(general()->signature()) }}" alt="Sign" class="signature-img">
                @else
                    <div class="signature-placeholder"></div>
                @endif
                <p>Authorized Signature</p>
            </div>
            <div class="signature-block">
                <p>For Buyer</p>
                <div class="signature-placeholder"></div>
                <p>Authorized Signature</p>
            </div>
        </div>

@endsection



@push('css')
<style>
    .borderless{
        border: none;
    }
    .invoice-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .invoice-info .left-info,
    .invoice-info .right-info {
        width: 48%;
    }

    .invoice-info p {
        margin: 4px 0;
        line-height: 1.3;
    }

    .table-container {
        overflow-x: auto;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .invoice-table th,
    .invoice-table td {
        border: 1px solid #ccc;
        padding: 5px;
        text-align: center;
        font-size: 12px;
    }

    .invoice-table th {
        background: #f2f2f2;
        font-weight: bold;
    }

    .no-items {
        text-align: center;
        font-style: italic;
        color: #888;
    }

    .amount-words {
        text-align: center;
        font-weight: bold;
        padding: 5px 0;
    }

    .terms-conditions {
        margin-top: 20px;
        font-size: 12px;
    }

    .terms-conditions h4 {
        margin-bottom: 10px;
        font-size: 14px;
        text-decoration: underline;
    }

    .terms-conditions table {
        width: 100%;
    }

    .terms-conditions table td {
        vertical-align: top;
        padding: 2px 5px;
    }

    .signatures {
        display: flex;
        justify-content: space-between;
        margin-top: 40px;
    }

    .signature-block {
        text-align: center;
        width: 38%;
    }

    .signature-img {
        max-width: 150px;
        margin: 5px 0;
    }

    .signature-placeholder {
        height: 60px;
        border-bottom: 1px solid #000;
        margin: 5px 0;
    }

    .print-footer {
        display: none;
    }


</style>
@endpush

@push('js')
<script src="{{asset('admin/assets/js/inword.js')}}"></script>
<script>
    var amount = Number(document.getElementById('total_amount_input').value);
    var words = toWords(amount);
    document.getElementById('total_amount_word').textContent = words + ' USD Only';
</script>
@endpush