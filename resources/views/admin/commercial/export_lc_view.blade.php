@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Export LC Details') }}</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>Export LC Details</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.exportLc') }}">Export LC</a></li>
        <li class="item">View Details</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Export LC: {{ $record->lc_no }}</h3>
        <div>
            <a href="{{ route('admin.commercial.exportLcAction', ['edit', $record->id]) }}" class="btn btn-success"><i class="bx bx-edit"></i> Edit</a>
            <a href="{{ route('admin.commercial.exportLc') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>LC Information</h5>
                <table class="table table-borderless">
                    <tr><th width="150">LC No</th><td>{{ $record->lc_no }}</td></tr>
                    <tr><th>LC Date</th><td>{{ $record->lc_date ? \Carbon\Carbon::parse($record->lc_date)->format('d M Y') : '' }}</td></tr>
                    <tr><th>Buyer</th><td>{{ $record->buyer_name }}</td></tr>
                    <tr><th>Invoice Value</th><td>{{ $record->currency }} {{ number_format($record->invoice_value, 2) }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-{{ $record->status == 1 ? 'warning' : ($record->status == 2 ? 'success' : 'info') }}">{{ $record->status_label }}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Shipment Details</h5>
                <table class="table table-borderless">
                    <tr><th width="150">Shipment Date</th><td>{{ $record->shipment_date ? \Carbon\Carbon::parse($record->shipment_date)->format('d M Y') : '' }}</td></tr>
                    <tr><th>Expiry Date</th><td>{{ $record->expiry_date ? \Carbon\Carbon::parse($record->expiry_date)->format('d M Y') : '' }}</td></tr>
                    <tr><th>Destination</th><td>{{ $record->destination_country }}</td></tr>
                    <tr><th>Port of Loading</th><td>{{ $record->port_of_loading }}</td></tr>
                    <tr><th>Created At</th><td>{{ $record->created_at->format('d M Y') }}</td></tr>
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
