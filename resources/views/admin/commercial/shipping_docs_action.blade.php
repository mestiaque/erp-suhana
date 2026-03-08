@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create Shipping Bill' : 'Edit Shipping Bill') }}</title>
@endsection

@push('css')
<style>
    .form-section { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .form-section h5 { margin-bottom: 15px; color: #333; font-weight: 600; }
</style>
@endpush

@section('contents')
<div class="breadcrumb-area">
    <h1>{{ $action == 'create' ? 'Create Shipping Bill' : 'Edit Shipping Bill' }}</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.shippingDocs') }}">Shipping Bill/Docs</a></li>
        <li class="item">{{ $action == 'create' ? 'Create' : 'Edit' }}</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header"><h3>{{ $action == 'create' ? 'Create New Shipping Bill' : 'Edit Shipping Bill' }}</h3></div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        
        @php $route = $action == 'create' ? route('admin.commercial.shippingDocsAction', ['store', 0]) : route('admin.commercial.shippingDocsAction', ['update', $record->id ?? 0]); @endphp
        
        <form action="{{ $route }}" method="POST">
            @csrf
            <div class="form-section">
                <h5><i class="bx bx-file"></i> Shipping Bill Information</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>SB No</label><input type="text" name="sb_no" value="{{ $action == 'create' ? $sbNo : ($record->sb_no ?? '') }}" class="form-control" {{ $action == 'create' ? 'readonly' : '' }}></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Invoice No</label><input type="text" name="invoice_no" value="{{ $action == 'edit' ? $record->invoice_no : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ ($action == 'edit' && $record->status == 1) ? 'selected' : '' }}>Pending</option>
                            <option value="2" {{ ($action == 'edit' && $record->status == 2) ? 'selected' : '' }}>Submitted</option>
                            <option value="3" {{ ($action == 'edit' && $record->status == 3) ? 'selected' : '' }}>Cleared</option>
                            <option value="4" {{ ($action == 'edit' && $record->status == 4) ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div></div>
                    <div class="col-md-3"><div class="form-group"><label>SB Date</label><input type="date" name="sb_date" value="{{ $action == 'edit' ? $record->sb_date : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-user"></i> Exporter & Buyer</h5>
                <div class="row">
                    <div class="col-md-6"><div class="form-group"><label>Exporter Name</label><input type="text" name="exporter_name" value="{{ $action == 'edit' ? $record->exporter_name : '' }}" class="form-control"></div></div>
                    <div class="col-md-6"><div class="form-group"><label>Exporter Address</label><textarea name="exporter_address" class="form-control" rows="1">{{ $action == 'edit' ? $record->exporter_address : '' }}</textarea></div></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><div class="form-group"><label>Buyer Name</label><input type="text" name="buyer_name" value="{{ $action == 'edit' ? $record->buyer_name : '' }}" class="form-control"></div></div>
                    <div class="col-md-6"><div class="form-group"><label>Buyer Address</label><textarea name="buyer_address" class="form-control" rows="1">{{ $action == 'edit' ? $record->buyer_address : '' }}</textarea></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-package"></i> Shipping Details</h5>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>LC No</label><input type="text" name="lc_no" value="{{ $action == 'edit' ? $record->lc_no : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Export LC No</label><input type="text" name="export_lc_no" value="{{ $action == 'edit' ? $record->export_lc_no : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Country of Origin</label><input type="text" name="country_of_origin" value="{{ $action == 'edit' ? $record->country_of_origin : 'Bangladesh' }}" class="form-control"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Destination Country</label><input type="text" name="destination_country" value="{{ $action == 'edit' ? $record->destination_country : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Port of Loading</label><input type="text" name="port_of_loading" value="{{ $action == 'edit' ? $record->port_of_loading : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Port of Discharge</label><input type="text" name="port_of_discharge" value="{{ $action == 'edit' ? $record->port_of_discharge : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-money"></i> Value Details</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>FOB Value</label><input type="number" name="fob_value" value="{{ $action == 'edit' ? $record->fob_value : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Freight</label><input type="number" name="freight" value="{{ $action == 'edit' ? $record->freight : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Insurance</label><input type="number" name="insurance" value="{{ $action == 'edit' ? $record->insurance : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>CIF Value</label><input type="number" name="cif_value" value="{{ $action == 'edit' ? $record->cif_value : 0 }}" class="form-control" step="0.01"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Currency</label>
                        <select name="currency" class="form-control">
                            <option value="USD" {{ ($action == 'edit' && $record->currency == 'USD') ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ ($action == 'edit' && $record->currency == 'EUR') ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ ($action == 'edit' && $record->currency == 'GBP') ? 'selected' : '' }}>GBP</option>
                        </select>
                    </div></div>
                    <div class="col-md-4"><div class="form-group"><label>Exchange Rate</label><input type="number" name="exchange_rate" value="{{ $action == 'edit' ? $record->exchange_rate : 1 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>BDT Value</label><input type="number" name="bdt_value" value="{{ $action == 'edit' ? $record->bdt_value : 0 }}" class="form-control" step="0.01"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5>Remarks</h5>
                <textarea name="remarks" class="form-control" rows="2">{{ $action == 'edit' ? $record->remarks : '' }}</textarea>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12 text-right">
                    <a href="{{ route('admin.commercial.shippingDocs') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ $action == 'create' ? 'Create' : 'Update' }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
