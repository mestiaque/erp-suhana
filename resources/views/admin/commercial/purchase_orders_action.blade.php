@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create Purchase Order' : 'Edit Purchase Order') }}</title>
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
    <h1>{{ $action == 'create' ? 'Create Purchase Order' : 'Edit Purchase Order' }}</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.purchaseOrders') }}">Purchase Order</a></li>
        <li class="item">{{ $action == 'create' ? 'Create' : 'Edit' }}</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header"><h3>{{ $action == 'create' ? 'Create New Purchase Order' : 'Edit Purchase Order' }}</h3></div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        
        @php $route = $action == 'create' ? route('admin.commercial.purchaseOrdersAction', ['store', 0]) : route('admin.commercial.purchaseOrdersAction', ['update', $record->id ?? 0]); @endphp
        
        <form action="{{ $route }}" method="POST">
            @csrf
            <div class="form-section">
                <h5><i class="bx bx-file"></i> Order Information</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>PO No</label><input type="text" name="po_no" value="{{ $action == 'create' ? $poNo : ($record->po_no ?? '') }}" class="form-control" {{ $action == 'create' ? 'readonly' : '' }}></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Supplier</label><select name="supplier_id" id="supplierSelect" class="form-control select2">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" data-name="{{ $supplier->name }}" data-address="{{ $supplier->fullAddress() }}" data-mobile="{{ $supplier->mobile }}" {{ ($action == 'edit' && $record->supplier_id == $supplier->id) ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Buyer</label><select name="buyer_id" id="buyerSelect" class="form-control select2">
                        <option value="">Select Buyer</option>
                        @foreach($buyers as $buyer)
                            <option value="{{ $buyer->id }}" data-name="{{ $buyer->name }}" {{ ($action == 'edit' && $record->buyer_id == $buyer->id) ? 'selected' : '' }}>{{ $buyer->name }}</option>
                        @endforeach
                    </select></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ ($action == 'edit' && $record->status == 1) ? 'selected' : '' }}>Pending</option>
                            <option value="2" {{ ($action == 'edit' && $record->status == 2) ? 'selected' : '' }}>Confirmed</option>
                            <option value="3" {{ ($action == 'edit' && $record->status == 3) ? 'selected' : '' }}>Shipped</option>
                            <option value="4" {{ ($action == 'edit' && $record->status == 4) ? 'selected' : '' }}>Received</option>
                            <option value="5" {{ ($action == 'edit' && $record->status == 5) ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Supplier Name</label><input type="text" name="supplier_name" id="supplierName" value="{{ $action == 'edit' ? $record->supplier_name : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Supplier Address</label><textarea name="supplier_address" id="supplierAddress" class="form-control" rows="1">{{ $action == 'edit' ? $record->supplier_address : '' }}</textarea></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Supplier Contact</label><input type="text" name="supplier_contact" id="supplierContact" value="{{ $action == 'edit' ? $record->supplier_contact : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-calendar"></i> Dates & Reference</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>PO Date</label><input type="date" name="po_date" value="{{ $action == 'edit' ? $record->po_date : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Delivery Date</label><input type="date" name="delivery_date" value="{{ $action == 'edit' ? $record->delivery_date : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>PI No</label><input type="text" name="pi_no" value="{{ $action == 'edit' ? $record->pi_no : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>LC No</label><input type="text" name="lc_no" value="{{ $action == 'edit' ? $record->lc_no : '' }}" class="form-control"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Style No</label><input type="text" name="style_no" value="{{ $action == 'edit' ? $record->style_no : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Order No</label><input type="text" name="order_no" value="{{ $action == 'edit' ? $record->order_no : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Buyer Name</label><input type="text" name="buyer_name" id="buyerName" value="{{ $action == 'edit' ? $record->buyer_name : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-money"></i> Amount</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Total Qty</label><input type="number" name="total_qty" id="totalQty" value="{{ $action == 'edit' ? $record->total_qty : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Unit Price</label><input type="number" name="unit_price" id="unitPrice" value="{{ $action == 'edit' ? $record->unit_price : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Total Amount</label><input type="number" id="totalAmount" value="{{ $action == 'edit' ? $record->total_amount : 0 }}" class="form-control" readonly></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Currency</label>
                        <select name="currency" class="form-control">
                            <option value="USD" {{ ($action == 'edit' && $record->currency == 'USD') ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ ($action == 'edit' && $record->currency == 'EUR') ? 'selected' : '' }}>EUR</option>
                            <option value="BDT" {{ ($action == 'edit' && $record->currency == 'BDT') ? 'selected' : '' }}>BDT</option>
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
                    <a href="{{ route('admin.commercial.purchaseOrders') }}" class="btn btn-secondary">Cancel</a>
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
    $('#supplierSelect').on('change', function() {
        var option = $(this).find('option:selected');
        $('#supplierName').val(option.data('name') || '');
        $('#supplierAddress').val(option.data('address') || '');
        $('#supplierContact').val(option.data('mobile') || '');
    });
    $('#buyerSelect').on('change', function() {
        var option = $(this).find('option:selected');
        $('#buyerName').val(option.data('name') || '');
    });
    @if($action == 'edit') $('#supplierSelect').trigger('change'); $('#buyerSelect').trigger('change'); @endif
    
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
