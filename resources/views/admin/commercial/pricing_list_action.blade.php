@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create Pricing List' : 'Edit Pricing List') }}</title>
@endsection

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
            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Price List No</label><input type="text" name="price_list_no" value="{{ $action == 'create' ? $listNo : ($record->price_list_no ?? '') }}" class="form-control" readonly></div></div>
                <div class="col-md-3"><div class="form-group"><label>Buyer</label><select name="buyer_id" class="form-control">
                    <option value="">Select Buyer</option>
                    @foreach($buyers as $buyer)
                        <option value="{{ $buyer->id }}" {{ ($action == 'edit' && $record->buyer_id == $buyer->id) ? 'selected' : '' }}>{{ $buyer->name }}</option>
                    @endforeach
                </select></div></div>
                <div class="col-md-3"><div class="form-group"><label>Buyer Name</label><input type="text" name="buyer_name" value="{{ $action == 'edit' ? $record->buyer_name : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ ($action == 'edit' && $record->status == 1) ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ ($action == 'edit' && $record->status == 2) ? 'selected' : '' }}>Expired</option>
                        <option value="3" {{ ($action == 'edit' && $record->status == 3) ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div></div>
            </div>

            <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Effective Date</label><input type="date" name="effective_date" value="{{ $action == 'edit' ? optional($record->effective_date)->format('Y-m-d') : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Expiry Date</label><input type="date" name="expiry_date" value="{{ $action == 'edit' ? optional($record->expiry_date)->format('Y-m-d') : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Season</label><input type="text" name="season" value="{{ $action == 'edit' ? $record->season : '' }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Year</label><input type="text" name="year" value="{{ $action == 'edit' ? $record->year : '' }}" class="form-control"></div></div>
            </div>

            <div class="form-group"><label>Remarks</label><textarea name="remarks" class="form-control" rows="2">{{ $action == 'edit' ? $record->remarks : '' }}</textarea></div>

            <div class="text-right">
                <a href="{{ route('admin.commercial.pricingList') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ $action == 'create' ? 'Create' : 'Update' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
