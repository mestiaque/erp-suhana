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
        <h3>Price List No: {{ $record->price_list_no }}</h3>
        <div>
            <a href="{{ route('admin.commercial.pricingListAction', ['edit', $record->id]) }}" class="btn btn-success"><i class="bx bx-edit"></i> Edit</a>
            <a href="{{ route('admin.commercial.pricingList') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr><th width="220">Price List No</th><td>{{ $record->price_list_no }}</td></tr>
            <tr><th>Buyer</th><td>{{ $record->buyer_name }}</td></tr>
            <tr><th>Effective Date</th><td>{{ $record->effective_date ? \Carbon\Carbon::parse($record->effective_date)->format('d M Y') : '' }}</td></tr>
            <tr><th>Expiry Date</th><td>{{ $record->expiry_date ? \Carbon\Carbon::parse($record->expiry_date)->format('d M Y') : '' }}</td></tr>
            <tr><th>Season</th><td>{{ $record->season }}</td></tr>
            <tr><th>Year</th><td>{{ $record->year }}</td></tr>
            <tr><th>Status</th><td>{{ $record->status_label }}</td></tr>
            <tr><th>Remarks</th><td>{{ $record->remarks }}</td></tr>
        </table>
    </div>
</div>
@endsection
