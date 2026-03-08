@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create Proforma Invoice' : 'Edit Proforma Invoice') }}</title>
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
    <h1>{{ $action == 'create' ? 'Create Proforma Invoice' : 'Edit Proforma Invoice' }}</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.pi') }}">Proforma Invoice</a></li>
        <li class="item">{{ $action == 'create' ? 'Create' : 'Edit' }}</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header"><h3>{{ $action == 'create' ? 'Create New Proforma Invoice' : 'Edit Proforma Invoice' }}</h3></div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        
        @php $route = $action == 'create' ? route('admin.commercial.piAction', ['store', 0]) : route('admin.commercial.piAction', ['update', $record->id ?? 0]); @endphp
        
        <form action="{{ $route }}" method="POST">
            @csrf
            <div class="form-section">
                <h5><i class="bx bx-file"></i> PI Information</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>PI No</label><input type="text" name="pi_no" value="{{ $action == 'create' ? $piNo : ($record->pi_no ?? '') }}" class="form-control" {{ $action == 'create' ? 'readonly' : '' }}></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Buyer</label><select name="buyer_id" id="buyerSelect" class="form-control select2">
                        <option value="">Select Buyer</option>
                        @foreach($buyers as $buyer)
                            <option value="{{ $buyer->id }}" data-name="{{ $buyer->name }}" data-address="{{ $buyer->fullAddress() }}" data-country="{{ $buyer->country?$buyer->country->name:'' }}" {{ ($action == 'edit' && $record->buyer_id == $buyer->id) ? 'selected' : '' }}>{{ $buyer->name }}</option>
                        @endforeach
                    </select></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ ($action == 'edit' && $record->status == 1) ? 'selected' : '' }}>Pending</option>
                            <option value="2" {{ ($action == 'edit' && $record->status == 2) ? 'selected' : '' }}>Confirmed</option>
                            <option value="3" {{ ($action == 'edit' && $record->status == 3) ? 'selected' : '' }}>Shipped</option>
                            <option value="4" {{ ($action == 'edit' && $record->status == 4) ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div></div>
                    <div class="col-md-3"><div class="form-group"><label>Currency</label>
                        <select name="currency" class="form-control">
                            <option value="USD" {{ ($action == 'edit' && $record->currency == 'USD') ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ ($action == 'edit' && $record->currency == 'EUR') ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ ($action == 'edit' && $record->currency == 'GBP') ? 'selected' : '' }}>GBP</option>
                            <option value="BDT" {{ ($action == 'edit' && $record->currency == 'BDT') ? 'selected' : '' }}>BDT</option>
                        </select>
                    </div></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Buyer Name</label><input type="text" name="buyer_name" id="buyerName" value="{{ $action == 'edit' ? $record->buyer_name : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Buyer Address</label><textarea name="buyer_address" id="buyerAddress" class="form-control" rows="1">{{ $action == 'edit' ? $record->buyer_address : '' }}</textarea></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Country</label><input type="text" name="country" id="buyerCountry" value="{{ $action == 'edit' ? $record->country : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-calendar"></i> Dates & Reference</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>PI Date</label><input type="date" name="pi_date" value="{{ $action == 'edit' ? $record->pi_date : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Delivery Date</label><input type="date" name="delivery_date" value="{{ $action == 'edit' ? $record->delivery_date : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class=">Style Noform-group"><label</label><input type="text" name="style_no" value="{{ $action == 'edit' ? $record->style_no : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Order No</label><input type="text" name="order_no" value="{{ $action == 'edit' ? $record->order_no : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-money"></i> Amount Details</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Total Qty</label><input type="number" name="total_qty" id="totalQty" value="{{ $action == 'edit' ? $record->total_qty : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Unit Price</label><input type="number" name="unit_price" id="unitPrice" value="{{ $action == 'edit' ? $record->unit_price : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Total Amount</label><input type="number" id="totalAmount" value="{{ $action == 'edit' ? $record->total_amount : 0 }}" class="form-control" readonly></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Exchange Rate</label><input type="number" name="exchange_rate" value="{{ $action == 'edit' ? $record->exchange_rate : 1 }}" class="form-control" step="0.01"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Discount (%)</label><input type="number" name="discount_percent" value="{{ $action == 'edit' ? $record->discount_percent : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Discount Amount</label><input type="number" name="discount_amount" value="{{ $action == 'edit' ? $record->discount_amount : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Net Amount</label><input type="number" name="net_amount" value="{{ $action == 'edit' ? $record->net_amount : 0 }}" class="form-control" step="0.01"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5>Terms & Conditions</h5>
                <div class="row">
                    <div class="col-md-6"><div class="form-group"><label>Payment Terms</label><input type="text" name="payment_terms" value="{{ $action == 'edit' ? $record->payment_terms : '100% Payment' }}" class="form-control"></div></div>
                    <div class="col-md-6"><div class="form-group"><label>Delivery Terms</label><input type="text" name="delivery_terms" value="{{ $action == 'edit' ? $record->delivery_terms : 'FOB' }}" class="form-control"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-12"><div class="form-group"><label>Remarks</label><textarea name="remarks" class="form-control" rows="2">{{ $action == 'edit' ? $record->remarks : '' }}</textarea></div></div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12 text-right">
                    <a href="{{ route('admin.commercial.pi') }}" class="btn btn-secondary">Cancel</a>
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
        $('#buyerCountry').val(option.data('country') || '');
    });
    @if($action == 'edit') $('#buyerSelect').trigger('change'); @endif
    
    function calcTotal() {
        var qty = parseFloat($('#totalQty').val()) || 0;
        var price = parseFloat($('#unitPrice').val()) || 0;
        $('#totalAmount').val((qty * price).toFixed(2));
    }
    $('#totalQty, #unitPrice').on('change', calcTotal);
});
</script>
@endpush
@endsection
