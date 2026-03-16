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
        <h3>Packing List No: {{ $record->packing_list_no }}</h3>
        <div>
            <a href="{{ route('admin.commercial.packingListAction', ['edit', $record->id]) }}" class="btn btn-success"><i class="bx bx-edit"></i> Edit</a>
            <a href="{{ route('admin.commercial.packingList') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr><th width="220">Packing List No</th><td>{{ $record->packing_list_no }}</td></tr>
            <tr><th>Invoice No</th><td>{{ $record->invoice_no }}</td></tr>
            <tr><th>Buyer</th><td>{{ $record->buyer_name }}</td></tr>
            <tr><th>Packing Date</th><td>{{ $record->packing_date ? \Carbon\Carbon::parse($record->packing_date)->format('d M Y') : '' }}</td></tr>
            <tr><th>Shipment Date</th><td>{{ $record->shipment_date ? \Carbon\Carbon::parse($record->shipment_date)->format('d M Y') : '' }}</td></tr>
            <tr><th>Total Cartons</th><td>{{ number_format($record->total_cartons) }}</td></tr>
            <tr><th>Net Weight</th><td>{{ number_format($record->net_weight, 2) }} kg</td></tr>
            <tr><th>Gross Weight</th><td>{{ number_format($record->gross_weight, 2) }} kg</td></tr>
            <tr><th>Total Volume</th><td>{{ number_format($record->total_volume, 4) }}</td></tr>
            <tr><th>Status</th><td>{{ $record->status_label }}</td></tr>
            <tr><th>Remarks</th><td>{{ $record->remarks }}</td></tr>
        </table>
    </div>
</div>
@endsection
