@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Packing List Details') }}</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>Packing List Details</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.packingList') }}">Packing List</a></li>
        <li class="item">View Details</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>PL No: {{ $record->pl_no }}</h3>
        <div>
            <a href="{{ route('admin.commercial.packingListAction', ['edit', $record->id]) }}" class="btn btn-success"><i class="bx bx-edit"></i> Edit</a>
            <a href="{{ route('admin.commercial.packingList') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Packing Information</h5>
                <table class="table table-borderless">
                    <tr><th width="150">PL No</th><td>{{ $record->pl_no }}</td></tr>
                    <tr><th>Invoice No</th><td>{{ $record->invoice_no }}</td></tr>
                    <tr><th>Buyer</th><td>{{ $record->buyer_name }}</td></tr>
                    <tr><th>PL Date</th><td>{{ $record->pl_date ? \Carbon\Carbon::parse($record->pl_date)->format('d M Y') : '' }}</td></tr>
                    <tr><th>Total CTN</th><td>{{ number_format($record->total_ctn, 2) }}</td></tr>
                    <tr><th>Total Qty</th><td>{{ number_format($record->total_qty, 2) }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-{{ $record->status == 1 ? 'warning' : 'success' }}">{{ $record->status_label }}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Weight Details</h5>
                <table class="table table-borderless">
                    <tr><th width="150">Net Weight</th><td>{{ number_format($record->net_weight, 2) }} kg</td></tr>
                    <tr><th>Gross Weight</th><td>{{ number_format($record->gross_weight, 2) }} kg</td></tr>
                    <tr><th>Carton Size</th><td>{{ $record->carton_size }}</td></tr>
                    <tr><th>Carton Weight</th><td>{{ $record->carton_weight }} kg</td></tr>
                    <tr><th>Total CBM</th><td>{{ number_format($record->total_cbm, 2) }}</td></tr>
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
