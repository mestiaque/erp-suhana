@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Export Realization Details') }}</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>Export Realization Details</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.exportRealization') }}">Export Realization</a></li>
        <li class="item">View Details</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Invoice: {{ $record->invoice_no }}</h3>
        <div>
            <a href="{{ route('admin.commercial.exportRealizationAction', ['edit', $record->id]) }}" class="btn btn-success"><i class="bx bx-edit"></i> Edit</a>
            <a href="{{ route('admin.commercial.exportRealization') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Realization Information</h5>
                <table class="table table-borderless">
                    <tr><th width="150">Invoice No</th><td>{{ $record->invoice_no }}</td></tr>
                    <tr><th>Invoice Date</th><td>{{ $record->invoice_date ? \Carbon\Carbon::parse($record->invoice_date)->format('d M Y') : '' }}</td></tr>
                    <tr><th>Buyer</th><td>{{ $record->buyer_name }}</td></tr>
                    <tr><th>LC No</th><td>{{ $record->lc_no }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-{{ $record->status == 1 ? 'warning' : ($record->status == 2 ? 'info' : 'success') }}">{{ $record->status_label }}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Amount Details</h5>
                <table class="table table-borderless">
                    <tr><th width="150">Invoice Value</th><td>{{ $record->currency }} {{ number_format($record->invoice_value, 2) }}</td></tr>
                    <tr><th>Realized Amount</th><td>{{ $record->currency }} {{ number_format($record->realized_amount, 2) }}</td></tr>
                    <tr><th>Pending Amount</th><td>{{ $record->currency }} {{ number_format($record->pending_amount, 2) }}</td></tr>
                    <tr><th>Realization Date</th><td>{{ $record->realization_date ? \Carbon\Carbon::parse($record->realization_date)->format('d M Y') : '' }}</td></tr>
                    <tr><th>Bank</th><td>{{ $record->bank_name }}</td></tr>
                    <tr><th>Realization Type</th><td>{{ $record->realization_type }}</td></tr>
                </table>
            </div>
        </div>
        @if($record->remarks)
        <div class="row mt-3">
            <div class="col-md-12">
                <h5>Remarks</h5>
                <p>{{ $record->remarks }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
