@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Shipping Document Details') }}</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>Shipping Document Details</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.shippingDocs') }}">Shipping Bill/Docs</a></li>
        <li class="item">View Details</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>SB No: {{ $record->sb_no }}</h3>
        <div>
            <a href="{{ route('admin.commercial.shippingDocsAction', ['edit', $record->id]) }}" class="btn btn-success"><i class="bx bx-edit"></i> Edit</a>
            <a href="{{ route('admin.commercial.shippingDocs') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Shipping Bill Information</h5>
                <table class="table table-borderless">
                    <tr><th width="150">SB No</th><td>{{ $record->sb_no }}</td></tr>
                    <tr><th>Invoice No</th><td>{{ $record->invoice_no }}</td></tr>
                    <tr><th>SB Date</th><td>{{ $record->sb_date ? \Carbon\Carbon::parse($record->sb_date)->format('d M Y') : '' }}</td></tr>
                    <tr><th>Exporter</th><td>{{ $record->exporter_name }}</td></tr>
                    <tr><th>Buyer</th><td>{{ $record->buyer_name }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-{{ $record->status == 1 ? 'warning' : ($record->status == 2 ? 'info' : 'success') }}">{{ $record->status_label }}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Value Details</h5>
                <table class="table table-borderless">
                    <tr><th width="150">FOB Value</th><td>{{ $record->currency }} {{ number_format($record->fob_value, 2) }}</td></tr>
                    <tr><th>Freight</th><td>{{ $record->currency }} {{ number_format($record->freight, 2) }}</td></tr>
                    <tr><th>Insurance</th><td>{{ $record->currency }} {{ number_format($record->insurance, 2) }}</td></tr>
                    <tr><th>CIF Value</th><td>{{ $record->currency }} {{ number_format($record->cif_value, 2) }}</td></tr>
                    <tr><th>Port of Loading</th><td>{{ $record->port_of_loading }}</td></tr>
                    <tr><th>Port of Discharge</th><td>{{ $record->port_of_discharge }}</td></tr>
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
