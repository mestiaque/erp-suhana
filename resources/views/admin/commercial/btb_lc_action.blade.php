@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create BTB LC' : 'Edit BTB LC') }}</title>
@endsection

@push('css')
<style>
    .form-section { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #e9ecef; }
    .form-section h5 { margin-bottom: 15px; color: #333; font-weight: 600; }
    .select2-container { width: 100% !important; }
</style>
@endpush

@section('contents')
<div class="breadcrumb-area">
    <h1>{{ $action == 'create' ? 'Create Bank BTB LC' : 'Edit Bank BTB LC' }}</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.btbLc') }}">BTB LC</a></li>
        <li class="item">{{ $action == 'create' ? 'Create' : 'Edit' }}</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>{{ $action == 'create' ? 'Create New BTB LC' : 'Edit BTB LC' }}</h3>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        
        @php
        $route = $action == 'create' 
            ? route('admin.commercial.btbLcAction', ['store', 0])
            : route('admin.commercial.btbLcAction', ['update', $record->id ?? 0]);
        @endphp
        
        <form action="{{ $route }}" method="POST">
            @csrf
            
            <div class="form-section">
                <h5><i class="bx bx-file"></i> LC Information</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>LC No</label>
                            <input type="text" name="lc_no" value="{{ $action == 'create' ? $lcNo : ($record->lc_no ?? '') }}" class="form-control" {{ $action == 'create' ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Supplier</label>
                            <select name="supplier_id" id="supplierSelect" class="form-control select2">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" data-name="{{ $supplier->name }}" data-address="{{ $supplier->fullAddress() }}" data-mobile="{{ $supplier->mobile }}" {{ ($action == 'edit' && $record->supplier_id == $supplier->id) ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ ($action == 'edit' && $record->status == 1) ? 'selected' : '' }}>Pending</option>
                                <option value="2" {{ ($action == 'edit' && $record->status == 2) ? 'selected' : '' }}>Active</option>
                                <option value="3" {{ ($action == 'edit' && $record->status == 3) ? 'selected' : '' }}>Closed</option>
                                <option value="4" {{ ($action == 'edit' && $record->status == 4) ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Bank</label>
                            <select name="bank_id" id="bankSelect" class="form-control select2">
                                <option value="">Select Bank</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" data-name="{{ $bank->name }}" {{ ($action == 'edit' && $record->bank_id == $bank->id) ? 'selected' : '' }}>
                                        {{ $bank->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Supplier Name</label>
                            <input type="text" name="supplier_name" id="supplierName" value="{{ $action == 'edit' ? $record->supplier_name : '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Supplier Address</label>
                            <textarea name="supplier_address" id="supplierAddress" class="form-control" rows="1">{{ $action == 'edit' ? $record->supplier_address : '' }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Supplier Contact</label>
                            <input type="text" name="supplier_contact" id="supplierContact" value="{{ $action == 'edit' ? $record->supplier_contact : '' }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-calendar"></i> Dates & Bank Details</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>LC Open Date</label>
                            <input type="date" name="lc_open_date" value="{{ $action == 'edit' ? $record->lc_open_date : '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>LC Expiry Date</label>
                            <input type="date" name="lc_expiry_date" value="{{ $action == 'edit' ? $record->lc_expiry_date : '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Shipment Date</label>
                            <input type="date" name="shipment_date" value="{{ $action == 'edit' ? $record->shipment_date : '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Delivery Date</label>
                            <input type="date" name="delivery_date" value="{{ $action == 'edit' ? $record->delivery_date : '' }}" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" name="bank_name" id="bankName" value="{{ $action == 'edit' ? $record->bank_name : '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Branch Name</label>
                            <input type="text" name="branch_name" value="{{ $action == 'edit' ? $record->branch_name : '' }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-money"></i> Amount Details</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>LC Value</label>
                            <input type="number" name="lc_value" id="lcValue" value="{{ $action == 'edit' ? $record->lc_value : 0 }}" class="form-control" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Currency</label>
                            <select name="currency" class="form-control">
                                <option value="USD" {{ ($action == 'edit' && $record->currency == 'USD') ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ ($action == 'edit' && $record->currency == 'EUR') ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ ($action == 'edit' && $record->currency == 'GBP') ? 'selected' : '' }}>GBP</option>
                                <option value="BDT" {{ ($action == 'edit' && $record->currency == 'BDT') ? 'selected' : '' }}>BDT</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Exchange Rate</label>
                            <input type="number" name="exchange_rate" id="exchangeRate" value="{{ $action == 'edit' ? $record->exchange_rate : 1 }}" class="form-control" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>LC Value (BDT)</label>
                            <input type="number" id="lcValueBdt" value="{{ $action == 'edit' ? $record->lc_value_bdt : 0 }}" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-note"></i> Remarks</h5>
                <textarea name="remarks" class="form-control" rows="3">{{ $action == 'edit' ? $record->remarks : '' }}</textarea>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12 text-right">
                    <a href="{{ route('admin.commercial.btbLc') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ $action == 'create' ? 'Create LC' : 'Update LC' }}</button>
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
    
    $('#bankSelect').on('change', function() {
        var option = $(this).find('option:selected');
        $('#bankName').val(option.data('name') || '');
    });
    
    @if($action == 'edit')
    $('#supplierSelect').trigger('change');
    $('#bankSelect').trigger('change');
    @endif
    
    $('#lcValue, #exchangeRate').on('change', function() {
        var lcValue = parseFloat($('#lcValue').val()) || 0;
        var exchangeRate = parseFloat($('#exchangeRate').val()) || 1;
        $('#lcValueBdt').val((lcValue * exchangeRate).toFixed(2));
    });
});
</script>
@endpush
@endsection
