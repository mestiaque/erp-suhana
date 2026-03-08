@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Proforma Invoice Details') }}</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>Proforma Invoice Details</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.pi') }}">Proforma Invoice</a></li>
        <li class="item">View Details</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>PI: {{ $record->pi_no }}</h3>
        <div>
            <a href="{{ route('admin.commercial.piAction', ['edit', $record->id]) }}" class="btn btn-success"><i class="bx bx-edit"></i> Edit</a>
            <a href="{{ route('admin.commercial.pi') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>PI Information</h5>
                <table class="table table-borderless">
                    <tr><th width="150">PI No</th><td>{{ $record->pi_no }}</td></tr>
                    <tr><th>PI Date</th><td>{{ $record->pi_date ? \Carbon\Carbon::parse($record->pi_date)->format('d M Y') : '' }}</td></tr>
                    <tr><th>Buyer</th><td>{{ $record->buyer_name }}</td></tr>
                    <tr><th>Total Amount</th><td>{{ $record->currency }} {{ number_format($record->total_amount, 2) }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-{{ $record->status == 1 ? 'warning' : ($record->status == 2 ? 'success' : 'info') }}">{{ $record->status_label }}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Reference</h5>
                <table class="table table-borderless">
                    <tr><th width="150">Style No</th><td>{{ $record->style_no }}</td></tr>
                    <tr><th>Order No</th><td>{{ $record->order_no }}</td></tr>
                    <tr><th>Delivery Date</th><td>{{ $record->delivery_date ? \Carbon\Carbon::parse($record->delivery_date)->format('d M Y') : '' }}</td></tr>
                    <tr><th>Payment Terms</th><td>{{ $record->payment_terms }}</td></tr>
                    <tr><th>Delivery Terms</th><td>{{ $record->delivery_terms }}</td></tr>
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
