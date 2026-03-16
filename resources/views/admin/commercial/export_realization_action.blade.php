@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create Export Realization' : 'Edit Export Realization') }}</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>{{ $action == 'create' ? 'Create Export Realization' : 'Edit Export Realization' }}</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.commercial.realization') }}">Export Realization</a></li>
        <li class="item">{{ $action == 'create' ? 'Create' : 'Edit' }}</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header"><h3>{{ $action == 'create' ? 'Create Export Realization' : 'Edit Export Realization' }}</h3></div>
    <div class="card-body">
        @include(adminTheme().'alerts')

        @php $route = $action == 'create' ? route('admin.commercial.realizationAction', ['store', 0]) : route('admin.commercial.realizationAction', ['update', $record->id ?? 0]); @endphp

        <form action="{{ $route }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Realization No</label><input type="text" name="realization_no" value="{{ $action == 'create' ? $realNo : ($record->realization_no ?? '') }}" class="form-control" readonly></div></div>
                <div class="col-md-3"><div class="form-group"><label>LC No</label><input type="text" name="lc_no" value="{{ $action == 'edit' ? $record->lc_no : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Buyer Name</label><input type="text" name="buyer_name" value="{{ $action == 'edit' ? $record->buyer_name : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ ($action == 'edit' && $record->status == 1) ? 'selected' : '' }}>Pending</option>
                        <option value="2" {{ ($action == 'edit' && $record->status == 2) ? 'selected' : '' }}>Partial</option>
                        <option value="3" {{ ($action == 'edit' && $record->status == 3) ? 'selected' : '' }}>Realized</option>
                    </select>
                </div></div>
            </div>

            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Submission Date</label><input type="date" name="submission_date" value="{{ $action == 'edit' ? optional($record->submission_date)->format('Y-m-d') : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Realization Date</label><input type="date" name="realization_date" value="{{ $action == 'edit' ? optional($record->realization_date)->format('Y-m-d') : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Bank Name</label><input type="text" name="bank_name" value="{{ $action == 'edit' ? $record->bank_name : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Bank Branch</label><input type="text" name="bank_branch" value="{{ $action == 'edit' ? $record->bank_branch : '' }}" class="form-control"></div></div>
            </div>

            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Invoice Value</label><input type="number" name="invoice_value" step="0.01" value="{{ $action == 'edit' ? $record->invoice_value : 0 }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Realized Value</label><input type="number" name="realized_value" step="0.01" value="{{ $action == 'edit' ? $record->realized_value : 0 }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Discount</label><input type="number" name="discount" step="0.01" value="{{ $action == 'edit' ? $record->discount : 0 }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Bank Charges</label><input type="number" name="bank_charges" step="0.01" value="{{ $action == 'edit' ? $record->bank_charges : 0 }}" class="form-control"></div></div>
            </div>

            <div class="row">
                <div class="col-md-4"><div class="form-group"><label>Currency</label><input type="text" name="currency" value="{{ $action == 'edit' ? $record->currency : 'USD' }}" class="form-control"></div></div>
                <div class="col-md-4"><div class="form-group"><label>Exchange Rate</label><input type="number" name="exchange_rate" step="0.01" value="{{ $action == 'edit' ? $record->exchange_rate : 1 }}" class="form-control"></div></div>
                <div class="col-md-4"><div class="form-group"><label>Remarks</label><input type="text" name="remarks" value="{{ $action == 'edit' ? $record->remarks : '' }}" class="form-control"></div></div>
            </div>

            <div class="text-right">
                <a href="{{ route('admin.commercial.realization') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $action == 'create' ? 'Create' : 'Update' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
