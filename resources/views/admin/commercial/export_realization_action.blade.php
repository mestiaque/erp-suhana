@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create Export Realization' : 'Edit Export Realization') }}</title>
@endsection

@push('css')
<style>
    .form-section { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .form-section h5 { margin-bottom: 15px; color: #333; font-weight: 600; }
</style>
@endpush

@section('contents')
<div class="breadcrumb-area">
    <h1>{{ $action == 'create' ? 'Create Export Realization' : 'Edit Export Realization' }}</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.exportRealization') }}">Export Realization</a></li>
        <li class="item">{{ $action == 'create' ? 'Create' : 'Edit' }}</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header"><h3>{{ $action == 'create' ? 'Create Export Realization' : 'Edit Export Realization' }}</h3></div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        
        @php $route = $action == 'create' ? route('admin.commercial.exportRealizationAction', ['store', 0]) : route('admin.commercial.exportRealizationAction', ['update', $record->id ?? 0]); @endphp
        
        <form action="{{ $route }}" method="POST">
            @csrf
            <div class="form-section">
                <h5><i class="bx bx-file"></i> Realization Information</h5>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Invoice No</label><input type="text" name="invoice_no" value="{{ $action == 'edit' ? $record->invoice_no : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Invoice Date</label><input type="date" name="invoice_date" value="{{ $action == 'edit' ? $record->invoice_date : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ ($action == 'edit' && $record->status == 1) ? 'selected' : '' }}>Pending</option>
                            <option value="2" {{ ($action == 'edit' && $record->status == 2) ? 'selected' : '' }}>Partially Realized</option>
                            <option value="3" {{ ($action == 'edit' && $record->status == 3) ? 'selected' : '' }}>Fully Realized</option>
                        </select>
                    </div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-user"></i> Buyer Information</h5>
                <div class="row">
                    <div class="col-md-6"><div class="form-group"><label>Buyer Name</label><input type="text" name="buyer_name" value="{{ $action == 'edit' ? $record->buyer_name : '' }}" class="form-control"></div></div>
                    <div class="col-md-6"><div class="form-group"><label>LC No</label><input type="text" name="lc_no" value="{{ $action == 'edit' ? $record->lc_no : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5><i class="bx bx-money"></i> Amount Details</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Invoice Value</label><input type="number" name="invoice_value" id="invoiceValue" value="{{ $action == 'edit' ? $record->invoice_value : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Realized Amount</label><input type="number" name="realized_amount" id="realizedAmount" value="{{ $action == 'edit' ? $record->realized_amount : 0 }}" class="form-control" step="0.01"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Pending Amount</label><input type="number" id="pendingAmount" value="{{ $action == 'edit' ? $record->pending_amount : 0 }}" class="form-control" readonly></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Currency</label>
                        <select name="currency" class="form-control">
                            <option value="USD" {{ ($action == 'edit' && $record->currency == 'USD') ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ ($action == 'edit' && $record->currency == 'EUR') ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ ($action == 'edit' && $record->currency == 'GBP') ? 'selected' : '' }}>GBP</option>
                        </select>
                    </div></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Realization Date</label><input type="date" name="realization_date" value="{{ $action == 'edit' ? $record->realization_date : '' }}" class="form-control"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Realization Type</label><input type="text" name="realization_type" value="{{ $action == 'edit' ? $record->realization_type : '' }}" class="form-control" placeholder="e.g. TT, DD, LC"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Bank</label><input type="text" name="bank_name" value="{{ $action == 'edit' ? $record->bank_name : '' }}" class="form-control"></div></div>
                </div>
            </div>
            
            <div class="form-section">
                <h5>Remarks</h5>
                <textarea name="remarks" class="form-control" rows="2">{{ $action == 'edit' ? $record->remarks : '' }}</textarea>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12 text-right">
                    <a href="{{ route('admin.commercial.exportRealization') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ $action == 'create' ? 'Create' : 'Update' }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
$(document).ready(function() {
    function calcPending() {
        var invoice = parseFloat($('#invoiceValue').val()) || 0;
        var realized = parseFloat($('#realizedAmount').val()) || 0;
        $('#pendingAmount').val((invoice - realized).toFixed(2));
    }
    $('#invoiceValue, #realizedAmount').on('change', calcPending);
});
</script>
@endpush
@endsection
