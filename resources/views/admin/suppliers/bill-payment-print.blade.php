@extends('admin.layouts.print-master')
@section('title', websiteTitle('Bill Payment Ledger Print'))

@section('back_route', route('admin.billPayment'))

@section('content')
<div style="text-align: center; margin-bottom: 20px;">
    <!-- Company Logo -->
    @if(general()->logo)
    <img src="{{ asset(general()->logo()) }}" alt="Company Logo" style="max-width: 80px; max-height: 80px;">
    @endif
    
    <!-- Company Name -->
    <h4 style="margin: 5px 0; font-weight: bold;">{{ general()->title }}</h4>
    
    <!-- Company Address -->
    <p style="margin: 0; font-size: 11px;">
        {{ general()->address_one }}
        {{ general()->address_two ? ', ' . general()->address_two : '' }}
        {{ general()->city ? ', ' . general()->city : '' }}
        {{ general()->country ? ', ' . general()->country : '' }}
    </p>
    <p style="margin: 0; font-size: 11px;">
        {{ general()->mobile ? 'Mobile: ' . general()->mobile : '' }}
        {{ general()->email ? ' | Email: ' . general()->email : '' }}
    </p>
</div>

<div style="border-top: 2px solid #333; border-bottom: 1px solid #333; padding: 8px 0; margin-bottom: 15px;">
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
    Transaction Statement (Len-Den)
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

<div style="margin-top: 30px; padding-top: 20px; display: flex; justify-content: space-between; font-size: 11px;">
    <div style="text-align: center;">
        <div style="border-top: 1px solid #333; width: 150px; padding-top: 5px;">Prepared By</div>
    </div>
    <div style="text-align: center;">
        <div style="border-top: 1px solid #333; width: 150px; padding-top: 5px;">Checked By</div>
    </div>
    <div style="text-align: center;">
        <div style="border-top: 1px solid #333; width: 150px; padding-top: 5px;">Approved By</div>
    </div>
</div>
@endsection
