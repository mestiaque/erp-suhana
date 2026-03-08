@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create Pricing List' : 'Edit Pricing List') }}</title>
@endsection

@push('css')
<style>
    .form-section { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .form-section h5 { margin-bottom: 15px; color: #333; font-weight: 600; }
</style>
@endpush

@section('contents')
<div class="breadcrumb-area">
    <h1>{{ $action == 'create' ? 'Create Pricing List' : 'Edit Pricing List' }}</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.pricingList') }}">Pricing List</a></li>
        <li class="item">{{ $action == 'create' ? 'Create' : 'Edit' }}</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header"><h3>{{ $action == 'create' ? 'Create New Pricing List' : 'Edit Pricing List' }}</h3></div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        
        @php $route = $action == 'create' ? route('admin.commercial.pricingListAction', ['store', 0]) : route('admin.commercial.pricingListAction', ['update', $record->id ?? 0]); @endphp
        
        <form action="{{ $route }}" method="POST">
            @csrf
            <div class="form-section">
                <h5><i class="bx bx-file"></i> Basic Information</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Ref No</label><input type="text" name="ref_no" value="{{ $action == 'create' ? $listNo : ($record->ref_no ?? '') }}" class="form-control" {{ $action == 'create' ? 'readonly' : '' }}></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Buyer</label><select name="buyer_id" id="buyerSelect" class="form-control select2">
                        <option value="">Select Buyer</option>
                        @foreach($buyers as $buyer)
                            <option value="{{ $buyer->id }}" data-name="{{ $buyer->name }}" {{ ($action == 'edit' && $record->buyer_id == $buyer->id) ? 'selected' : '' }}>{{ $buyer->name }}</option>
                        @endforeach
                    </select></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Buyer Name</label><input type="text" name="buyer_name" id="buyerName" value="{{ $action == 'edit' ? $record->buyer_name : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ ($action == 'edit' && $record->status == 1) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ ($action == 'edit' && $record->status == 0) ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div></div>
                </div>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Style No</label><input type="text" name="style_no" value="{{ $action == 'edit' ? $record->style_no : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Item Name</label><input type="text" name="item_name" value="{{ $action == 'edit' ? $record->item_name : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Item Description</label><input type="text" name="item_description" value="{{ $action == 'edit' ? $record->item_description : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Color</label><input type="text" name="color" value="{{ $action == 'edit' ? $record->color : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-money"></i> Pricing</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Unit Price</label><input type="number" name="unit_price" value="{{ $action == 'edit' ? $record->unit_price : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Currency</label>
                        <select name="currency" class="form-control">
                            <option value="USD" {{ ($action == 'edit' && $record->currency == 'USD') ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ ($action == 'edit' && $record->currency == 'EUR') ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ ($action == 'edit' && $record->currency == 'GBP') ? 'selected' : '' }}>GBP</option>
                            <option value="BDT" {{ ($action == 'edit' && $record->currency == 'BDT') ? 'selected' : '' }}>BDT</option>
                        </select>
                    </div></div>
                    <div class="col-md-3"><div class="form-group"><label>MOQ (Min Order Qty)</label><input type="number" name="moq" value="{{ $action == 'edit' ? $record->moq : 0 }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Packaging</label><input type="text" name="packaging" value="{{ $action == 'edit' ? $record->packaging : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-calendar"></i> Validity</h5>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Valid From</label><input type="date" name="valid_from" value="{{ $action == 'edit' ? $record->valid_from : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Valid Till</label><input type="valid_till"date" name=" value="{{ $action == 'edit' ? $record->valid_till : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Lead Time (Days)</label><input type="number" name="lead_time" value="{{ $action == 'edit' ? $record->lead_time : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5>Remarks</h5>
                <textarea name="remarks" class="form-control" rows="2">{{ $action == 'edit' ? $record->remarks : '' }}</textarea>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12 text-right">
                    <a href="{{ route('admin.commercial.pricingList') }}" class="btn btn-secondary">Cancel</a>
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
    });
    @if($action == 'edit') $('#buyerSelect').trigger('change'); @endif
});
</script>
@endpush
@endsection
