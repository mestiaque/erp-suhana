@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Shipping Document Details') }}</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>Shipping Document Details</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.shippingDocs') }}">Shipping Documents</a></li>
        <li class="item">View Details</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Document No: {{ $record->doc_no }}</h3>
        <div>
            <a href="{{ route('admin.commercial.shippingDocsAction', ['edit', $record->id]) }}" class="btn btn-success"><i class="bx bx-edit"></i> Edit</a>
            <a href="{{ route('admin.commercial.shippingDocs') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr><th width="220">Document No</th><td>{{ $record->doc_no }}</td></tr>
            <tr><th>Invoice No</th><td>{{ $record->invoice_no }}</td></tr>
            <tr><th>Buyer</th><td>{{ $record->buyer_name }}</td></tr>
            <tr><th>Issue Date</th><td>{{ $record->issue_date ? \Carbon\Carbon::parse($record->issue_date)->format('d M Y') : '' }}</td></tr>
            <tr><th>Shipment Type</th><td>{{ $record->shipment_type }}</td></tr>
            <tr><th>Vessel/Flight</th><td>{{ $record->vessel_name ?: $record->flight_no }}</td></tr>
            <tr><th>Port of Loading</th><td>{{ $record->port_of_loading }}</td></tr>
            <tr><th>Port of Discharge</th><td>{{ $record->port_of_discharge }}</td></tr>
            <tr><th>Status</th><td>{{ $record->status_label }}</td></tr>
            <tr><th>Remarks</th><td>{{ $record->remarks }}</td></tr>
        </table>
    </div>
</div>
@endsection
