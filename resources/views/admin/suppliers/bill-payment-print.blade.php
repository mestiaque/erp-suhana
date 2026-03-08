@extends('printMaster')
@section('title', ('Bill Payment Ledger Print'))

@section('contents')


<div style="">
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Report:</strong> Bill Payment Ledger (All Suppliers)
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <strong>Print Date:</strong> {{ now()->format('d-m-Y h:i A') }}
            </td>
        </tr>
    </table>
</div>

<h5 style="text-align: center; margin: 10px 0; background: #f8f9fa; padding: 5px;">
    Transaction Statement
</h5>

<table style="width: 100%; border-collapse: collapse; font-size: 11px;">
    <thead>
        <tr style="background: #e9ecef;">
            <th style="border: 1px solid #dee2e6; padding: 5px; text-align: center;">SL</th>
            <th style="border: 1px solid #dee2e6; padding: 5px;">Date</th>
            <th style="border: 1px solid #dee2e6; padding: 5px;">Supplier</th>
            <th style="border: 1px solid #dee2e6; padding: 5px;">Code</th>
            <th style="border: 1px solid #dee2e6; padding: 5px;">Title / Ref</th>
            <th style="border: 1px solid #dee2e6; padding: 5px;">Details</th>
            <th style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($merged as $item)
        <tr>
            <td style="border: 1px solid #dee2e6; padding: 5px; text-align: center;">{{ $loop->iteration }}</td>
            <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $item->date ? $item->date->format('d.m.Y') : '' }}</td>
            <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $item->user?->name ?? '-' }}</td>
            <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $item->user?->employee_id ?? '-' }}</td>
            <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $item->title }}</td>
            <td style="border: 1px solid #dee2e6; padding: 5px;">{{ $item->description ?? '-' }}</td>
            <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right; @if($item->credit > 0) color: green; @else color: red; @endif">
                @if($item->credit > 0)+ @endif
                @if($item->debit > 0)- @endif
                {{ number_format($item->credit > 0 ? $item->credit : $item->debit, 2) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="border: 1px solid #dee2e6; padding: 10px; text-align: center;">No transactions found</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr style="background: #f8f9fa; font-weight: bold;">
            <td colspan="6" style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">Total:</td>
            <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">{{ number_format($totalBills - $totalPayments, 2) }}</td>
        </tr>
    </tfoot>
</table>
@endsection
