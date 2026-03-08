@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Pricing List Details') }}</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>Pricing List Details</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.pricingList') }}">Pricing List</a></li>
        <li class="item">View Details</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Ref No: {{ $record->ref_no }}</h3>
        <div>
            <a href="{{ route('admin.commercial.pricingListAction', ['edit', $record->id]) }}" class="btn btn-success"><i class="bx bx-edit"></i> Edit</a>
            <a href="{{ route('admin.commercial.pricingList') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Pricing Information</h5>
                <table class="table table-borderless">
                    <tr><th width="150">Ref No</th><td>{{ $record->ref_no }}</td></tr>
                    <tr><th>Buyer</th><td>{{ $record->buyer_name }}</td></tr>
                    <tr><th>Style No</th><td>{{ $record->style_no }}</td></tr>
                    <tr><th>Item Name</th><td>{{ $record->item_name }}</td></tr>
                    <tr><th>Unit Price</th><td>{{ $record->currency }} {{ number_format($record->unit_price, 2) }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-{{ $record->status ? 'success' : 'warning' }}">{{ $record->status ? 'Active' : 'Inactive' }}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Validity</h5>
                <table class="table table-borderless">
                    <tr><th width="150">Valid From</th><td>{{ $record->valid_from ? \Carbon\Carbon::parse($record->valid_from)->format('d M Y') : '' }}</td></tr>
                    <tr><th>Valid Till</th><td>{{ $record->valid_till ? \Carbon\Carbon::parse($record->valid_till)->format('d M Y') : '' }}</td></tr>
                    <tr><th>MOQ</th><td>{{ $record->moq }}</td></tr>
                    <tr><th>Packaging</th><td>{{ $record->packaging }}</td></tr>
                    <tr><th>Lead Time</th><td>{{ $record->lead_time }} days</td></tr>
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
