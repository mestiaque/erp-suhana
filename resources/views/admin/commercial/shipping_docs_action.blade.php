@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create Shipping Document' : 'Edit Shipping Document') }}</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>{{ $action == 'create' ? 'Create Shipping Document' : 'Edit Shipping Document' }}</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.shippingDocs') }}">Shipping Documents</a></li>
        <li class="item">{{ $action == 'create' ? 'Create' : 'Edit' }}</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header"><h3>{{ $action == 'create' ? 'Create Shipping Document' : 'Edit Shipping Document' }}</h3></div>
    <div class="card-body">
        @include(adminTheme().'alerts')

        @php $route = $action == 'create' ? route('admin.commercial.shippingDocsAction', ['store', 0]) : route('admin.commercial.shippingDocsAction', ['update', $record->id ?? 0]); @endphp

        <form action="{{ $route }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Document No</label><input type="text" name="doc_no" value="{{ $action == 'create' ? $docNo : ($record->doc_no ?? '') }}" class="form-control" readonly></div></div>
                <div class="col-md-3"><div class="form-group"><label>Invoice No</label><input type="text" name="invoice_no" value="{{ $action == 'edit' ? $record->invoice_no : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Buyer Name</label><input type="text" name="buyer_name" value="{{ $action == 'edit' ? $record->buyer_name : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Issue Date</label><input type="date" name="issue_date" value="{{ $action == 'edit' ? optional($record->issue_date)->format('Y-m-d') : '' }}" class="form-control"></div></div>
            </div>

            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Shipment Type</label><input type="text" name="shipment_type" value="{{ $action == 'edit' ? $record->shipment_type : '' }}" class="form-control" placeholder="Air/Sea/Land"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Vessel Name</label><input type="text" name="vessel_name" value="{{ $action == 'edit' ? $record->vessel_name : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Flight No</label><input type="text" name="flight_no" value="{{ $action == 'edit' ? $record->flight_no : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ ($action == 'edit' && $record->status == 1) ? 'selected' : '' }}>Pending</option>
                        <option value="2" {{ ($action == 'edit' && $record->status == 2) ? 'selected' : '' }}>Submitted</option>
                        <option value="3" {{ ($action == 'edit' && $record->status == 3) ? 'selected' : '' }}>Approved</option>
                        <option value="4" {{ ($action == 'edit' && $record->status == 4) ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div></div>
            </div>

            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Departure Date</label><input type="date" name="departure_date" value="{{ $action == 'edit' ? optional($record->departure_date)->format('Y-m-d') : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Arrival Date</label><input type="date" name="arrival_date" value="{{ $action == 'edit' ? optional($record->arrival_date)->format('Y-m-d') : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Port of Loading</label><input type="text" name="port_of_loading" value="{{ $action == 'edit' ? $record->port_of_loading : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Port of Discharge</label><input type="text" name="port_of_discharge" value="{{ $action == 'edit' ? $record->port_of_discharge : '' }}" class="form-control"></div></div>
            </div>

            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Country of Origin</label><input type="text" name="country_of_origin" value="{{ $action == 'edit' ? $record->country_of_origin : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Destination Country</label><input type="text" name="destination_country" value="{{ $action == 'edit' ? $record->destination_country : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>BL/AWB No</label><input type="text" name="bl_awb_no" value="{{ $action == 'edit' ? $record->bl_awb_no : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>BL/AWB Date</label><input type="date" name="bl_awb_date" value="{{ $action == 'edit' ? optional($record->bl_awb_date)->format('Y-m-d') : '' }}" class="form-control"></div></div>
            </div>

            <div class="form-group"><label>Remarks</label><textarea name="remarks" class="form-control" rows="2">{{ $action == 'edit' ? $record->remarks : '' }}</textarea></div>

            <div class="text-right">
                <a href="{{ route('admin.commercial.shippingDocs') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $action == 'create' ? 'Create' : 'Update' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
