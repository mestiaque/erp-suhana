@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create Packing List' : 'Edit Packing List') }}</title>
@endsection

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
            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Packing List No</label><input type="text" name="packing_list_no" value="{{ $action == 'create' ? $listNo : ($record->packing_list_no ?? '') }}" class="form-control" readonly></div></div>
                <div class="col-md-3"><div class="form-group"><label>Invoice No</label><input type="text" name="invoice_no" value="{{ $action == 'edit' ? $record->invoice_no : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Buyer Name</label><input type="text" name="buyer_name" value="{{ $action == 'edit' ? $record->buyer_name : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ ($action == 'edit' && $record->status == 1) ? 'selected' : '' }}>Draft</option>
                        <option value="2" {{ ($action == 'edit' && $record->status == 2) ? 'selected' : '' }}>Packed</option>
                        <option value="3" {{ ($action == 'edit' && $record->status == 3) ? 'selected' : '' }}>Shipped</option>
                    </select>
                </div></div>
            </div>

            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Packing Date</label><input type="date" name="packing_date" value="{{ $action == 'edit' ? optional($record->packing_date)->format('Y-m-d') : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Shipment Date</label><input type="date" name="shipment_date" value="{{ $action == 'edit' ? optional($record->shipment_date)->format('Y-m-d') : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Shipment From</label><input type="text" name="shipment_from" value="{{ $action == 'edit' ? $record->shipment_from : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Shipment To</label><input type="text" name="shipment_to" value="{{ $action == 'edit' ? $record->shipment_to : '' }}" class="form-control"></div></div>
            </div>

            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Total Cartons</label><input type="number" name="total_cartons" value="{{ $action == 'edit' ? $record->total_cartons : 0 }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Net Weight</label><input type="number" name="net_weight" step="0.01" value="{{ $action == 'edit' ? $record->net_weight : 0 }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Gross Weight</label><input type="number" name="gross_weight" step="0.01" value="{{ $action == 'edit' ? $record->gross_weight : 0 }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Total Volume</label><input type="number" name="total_volume" step="0.0001" value="{{ $action == 'edit' ? $record->total_volume : 0 }}" class="form-control"></div></div>
            </div>

            <div class="form-group"><label>Remarks</label><textarea name="remarks" class="form-control" rows="2">{{ $action == 'edit' ? $record->remarks : '' }}</textarea></div>

            <div class="text-right">
                <a href="{{ route('admin.commercial.packingList') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $action == 'create' ? 'Create' : 'Update' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
