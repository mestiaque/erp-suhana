@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create Export LC' : 'Edit Export LC') }}</title>
@endsection

@push('css')
<style>
    .form-section { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .form-section h5 { margin-bottom: 15px; color: #333; font-weight: 600; }
    .select2-container { width: 100% !important; }
</style>
@endpush

@section('contents')
<div class="breadcrumb-area">
    <h1>{{ $action == 'create' ? 'Create Export LC' : 'Edit Export LC' }}</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.exportLc') }}">Export LC</a></li>
        <li class="item">{{ $action == 'create' ? 'Create' : 'Edit' }}</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header"><h3>{{ $action == 'create' ? 'Create New Export LC' : 'Edit Export LC' }}</h3></div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        
        @php $route = $action == 'create' ? route('admin.commercial.exportLcAction', ['store', 0]) : route('admin.commercial.exportLcAction', ['update', $record->id ?? 0]); @endphp
        
        <form action="{{ $route }}" method="POST">
            @csrf
            <div class="form-section">
                <h5><i class="bx bx-file"></i> LC Information</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group"><label>LC No</label><input type="text" name="lc_no" value="{{ $action == 'create' ? $lcNo : ($record->lc_no ?? '') }}" class="form-control" {{ $action == 'create' ? 'readonly' : '' }}></div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group"><label>Buyer</label><select name="buyer_id" id="buyerSelect" class="form-control select2">
                            <option value="">Select Buyer</option>
                            @foreach($buyers as $buyer)
                                <option value="{{ $buyer->id }}" data-name="{{ $buyer->name }}" data-address="{{ $buyer->fullAddress() }}" data-mobile="{{ $buyer->mobile }}" {{ ($action == 'edit' && $record->buyer_id == $buyer->id) ? 'selected' : '' }}>{{ $buyer->name }}</option>
                            @endforeach
                        </select></div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group"><label>Status</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ ($action == 'edit' && $record->status == 1) ? 'selected' : '' }}>Pending</option>
                                <option value="2" {{ ($action == 'edit' && $record->status == 2) ? 'selected' : '' }}>Partially Realized</option>
                                <option value="3" {{ ($action == 'edit' && $record->status == 3) ? 'selected' : '' }}>Fully Realized</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Buyer Name</label><input type="text" name="buyer_name" id="buyerName" value="{{ $action == 'edit' ? $record->buyer_name : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Buyer Address</label><textarea name="buyer_address" id="buyerAddress" class="form-control" rows="1">{{ $action == 'edit' ? $record->buyer_address : '' }}</textarea></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Buyer Contact</label><input type="text" name="buyer_contact" id="buyerContact" value="{{ $action == 'edit' ? $record->buyer_contact : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-calendar"></i> Dates & Bank</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>LC Open Date</label><input type="date" name="lc_open_date" value="{{ $action == 'edit' ? $record->lc_open_date : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>LC Expiry Date</label><input type="date" name="lc_expiry_date" value="{{ $action == 'edit' ? $record->lc_expiry_date : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Shipment Date</label><input type="date" name="shipment_date" value="{{ $action == 'edit' ? $record->shipment_date : '' }}" class="form-control"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Issuing Bank</label><input type="text" name="issuing_bank" value="{{ $action == 'edit' ? $record->issuing_bank : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Branch</label><input type="text" name="issuing_bank_branch" value="{{ $action == 'edit' ? $record->issuing_bank_branch : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Negotiating Bank</label><input type="text" name="negotiating_bank" value="{{ $action == 'edit' ? $record->negotiating_bank : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-money"></i> Amount</h5>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>LC Value</label><input type="number" name="lc_value" value="{{ $action == 'edit' ? $record->lc_value : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Currency</label>
                        <select name="currency" class="form-control">
                            <option value="USD" {{ ($action == 'edit' && $record->currency == 'USD') ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ ($action == 'edit' && $record->currency == 'EUR') ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ ($action == 'edit' && $record->currency == 'GBP') ? 'selected' : '' }}>GBP</option>
                        </select>
                    </div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5>Remarks</h5>
                <textarea name="remarks" class="form-control" rows="3">{{ $action == 'edit' ? $record->remarks : '' }}</textarea>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12 text-right">
                    <a href="{{ route('admin.commercial.exportLc') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ $action == 'create' ? 'Create' : 'Update' }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();
    $('#buyerSelect').on('change', function() {
        var option = $(this).find('option:selected');
        $('#buyerName').val(option.data('name') || '');
        $('#buyerAddress').val(option.data('address') || '');
        $('#buyerContact').val(option.data('mobile') || '');
    });
    @if($action == 'edit') $('#buyerSelect').trigger('change'); @endif
});
</script>
@endpush
@endsection
