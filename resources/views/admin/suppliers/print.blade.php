@extends('printMaster')
@section('title', ('Creditor Statement - ' . $user->name))

@section('contents')
<div style="">
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Supplier/Creditor Info:</strong><br>
                Name: {{ $user->name }}<br>
                @if($user->company_name)Company: {{ $user->company_name }}<br>@endif
                @if($user->mobile)Mobile: {{ $user->mobile }}<br>@endif
                @if($user->email)Email: {{ $user->email }}<br>@endif
                @if($user->address_line1)Address: {{ $user->address_line1 }}@endif
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <strong>Statement Date:</strong> {{ now()->format('d-m-Y') }}<br>
                <strong>Print Date:</strong> {{ now()->format('d-m-Y h:i A') }}
            </td>
        </tr>
    </table>
</div>

<h5 style="text-align: center; margin: 10px 0; background: #f8f9fa; padding: 5px;">
    Transaction Statement / Ledger
</h5>

<table style="width: 100%; border-collapse: collapse; font-size: 11px;">
    <thead>
        <tr style="background: #e9ecef;">
            <th style="border: 1px solid #dee2e6; padding: 5px; text-align: center;">SL</th>
            <th style="border: 1px solid #dee2e6; padding: 5px;">Date</th>
            <th style="border: 1px solid #dee2e6; padding: 5px;">Title/Invoice/Trans ID</th>
            <th style="border: 1px solid #dee2e6; padding: 5px;">Description</th>
            <th style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ledgerEntries as $item)
        <tr>
            <td style="border: 1px solid #dee2e6; padding: 5px; text-align: center;">{{ $loop->iteration }}</td>
            <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $item->date ? $item->date->format('d-m-Y') : '' }}</td>
            <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $item->title }}</td>
            <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $item->note ?? '-' }}</td>
            <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right; @if($item->credit > 0) color: green; @else color: red; @endif">
                @if($item->credit > 0)+ @endif
                @if($item->debit > 0)- @endif
                {{ priceFullFormat($item->credit > 0 ? $item->credit : $item->debit) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="border: 1px solid #dee2e6; padding: 10px; text-align: center;">No transactions found</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 20px; padding-top: 10px; border-top: 1px solid #dee2e6;">
    <table style="width: 100%; font-size: 11px;">
        <tr>
            <td style="width: 50%;">
                <strong>Total Purchases:</strong> {{ priceFullFormat($user->creditorBill->sum('amount')) }}
            </td>
            <td style="width: 50%;">
                <strong>Total Paid:</strong> {{ priceFullFormat($totalPaid) }}
            </td>
        </tr>
        <tr>
            <td style="width: 50%;">
                <strong>Net Due Balance:</strong> {{ priceFullFormat($user->creditorBill->sum('amount') - $totalPaid) }}
            </td>
            <td style="width: 50%;">
            </td>
        </tr>
    </table>
</div>
@endsection
