@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create Packing List' : 'Edit Packing List') }}</title>
@endsection

@push('css')
<style>
    .form-section { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .form-section h5 { margin-bottom: 15px; color: #333; font-weight: 600; }
</style>
@endpush

@section('contents')
<div class="breadcrumb-area">
    <h1>{{ $action == 'create' ? 'Create Packing List' : 'Edit Packing List' }}</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.packingList') }}">Packing List</a></li>
        <li class="item">{{ $action == 'create' ? 'Create' : 'Edit' }}</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header"><h3>{{ $action == 'create' ? 'Create New Packing List' : 'Edit Packing List' }}</h3></div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        
        @php $route = $action == 'create' ? route('admin.commercial.packingListAction', ['store', 0]) : route('admin.commercial.packingListAction', ['update', $record->id ?? 0]); @endphp
        
        <form action="{{ $route }}" method="POST">
            @csrf
            <div class="form-section">
                <h5><i class="bx bx-file"></i> Basic Information</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>PL No</label><input type="text" name="pl_no" value="{{ $action == 'create' ? $plNo : ($record->pl_no ?? '') }}" class="form-control" {{ $action == 'create' ? 'readonly' : '' }}></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Invoice No</label><input type="text" name="invoice_no" value="{{ $action == 'edit' ? $record->invoice_no : '' }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Buyer</label><select name="buyer_id" id="buyerSelect" class="form-control select2">
                        <option value="">Select Buyer</option>
                        @foreach($buyers as $buyer)
                            <option value="{{ $buyer->id }}" data-name="{{ $buyer->name }}" {{ ($action == 'edit' && $record->buyer_id == $buyer->id) ? 'selected' : '' }}>{{ $buyer->name }}</option>
                        @endforeach
                    </select></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ ($action == 'edit' && $record->status == 1) ? 'selected' : '' }}>Pending</option>
                            <option value="2" {{ ($action == 'edit' && $record->status == 2) ? 'selected' : '' }}>Shipped</option>
                            <option value="3" {{ ($action == 'edit' && $record->status == 3) ? 'selected' : '' }}>Delivered</option>
                        </select>
                    </div></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Buyer Name</label><input type="text" name="buyer_name" id="buyerName" value="{{ $action == 'edit' ? $record->buyer_name : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Buyer Address</label><textarea name="buyer_address" class="form-control" rows="1">{{ $action == 'edit' ? $record->buyer_address : '' }}</textarea></div></div>
                    <div class="col-md-4"><div class="form-group"><label>PL Date</label><input type="date" name="pl_date" value="{{ $action == 'edit' ? $record->pl_date : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-package"></i> Packing Details</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Total CTN</label><input type="number" name="total_ctn" id="totalCtn" value="{{ $action == 'edit' ? $record->total_ctn : 0 }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Total Qty</label><input type="number" name="total_qty" id="totalQty" value="{{ $action == 'edit' ? $record->total_qty : 0 }}" class="form-control"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Net Weight (kg)</label><input type="number" name="net_weight" value="{{ $action == 'edit' ? $record->net_weight : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Gross Weight (kg)</label><input type="number" name="gross_weight" value="{{ $action == 'edit' ? $record->gross_weight : 0 }}" class="form-control" step="0.01"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Carton Size (L x W x H cm)</label><input type="text" name="carton_size" value="{{ $action == 'edit' ? $record->carton_size : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Carton Weight (kg)</label><input type="number" name="carton_weight" value="{{ $action == 'edit' ? $record->carton_weight : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Total CBM</label><input type="number" name="total_cbm" value="{{ $action == 'edit' ? $record->total_cbm : 0 }}" class="form-control" step="0.01"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i name="remarks">Remarks</label></h5>
                <textarea class="form-control" rows="2">{{ $action == 'edit' ? $record->remarks : '' }}</textarea>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12 text-right">
                    <a href="{{ route('admin.commercial.packingList') }}" class="btn btn-secondary">Cancel</a>
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
