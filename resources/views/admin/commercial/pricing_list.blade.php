@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Pricing List') }}</title>
@endsection

@push('css')
<style>
    .statuslist { list-style: none; display: flex; gap: 10px; flex-wrap: wrap; }
    .statuslist li a { padding: 5px 15px; border-radius: 20px; background: #f0f0f0; color: #333; font-size: 13px; text-decoration: none; }
    .statuslist li a:hover, .statuslist li a.active { background: #4c4a4a; color: #fff; }
</style>
@endpush

@section('contents')
<div class="breadcrumb-area">
    <h1>Pricing List</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Commercial</li>
        <li class="item">Pricing List</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Pricing List</h3>
        <a href="{{ route('admin.commercial.pricingListAction', 'create') }}" class="btn-custom primary"><i class="bx bx-plus"></i> Add New</a>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')

        <form action="{{ route('admin.commercial.pricingList') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4"><input type="text" name="search" value="{{ request()->search }}" placeholder="Search Ref No" class="form-control"></div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="all">All Status</option>
                        <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request()->status === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-success">Search</button></div>
            </div>
        </form>

        <ul class="statuslist mb-3">
            <li><a href="{{ route('admin.commercial.pricingList') }}" class="{{ !request()->status ? 'active' : '' }}">All ({{ $statusCounts['total'] }})</a></li>
            <li><a href="{{ route('admin.commercial.pricingList', ['status' => 1]) }}" class="{{ request()->status == 1 ? 'active' : '' }}">Active ({{ $statusCounts['active'] }})</a></li>
            <li><a href="{{ route('admin.commercial.pricingList', ['status' => 0]) }}" class="{{ request()->status === '0' ? 'active' : '' }}">Inactive ({{ $statusCounts['inactive'] }})</a></li>
        </ul>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>SL</th><th>Ref No</th><th>Buyer</th><th>Style No</th><th>Item Name</th><th>Unit Price</th><th>Currency</th><th>Valid Till</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><a href="{{ route('admin.commercial.pricingListAction', ['view', $record->id]) }}">{{ $record->ref_no }}</a></td>
                        <td>{{ $record->buyer_name }}</td>
                        <td>{{ $record->style_no }}</td>
                        <td>{{ $record->item_name }}</td>
                        <td>{{ number_format($record->unit_price, 2) }}</td>
                        <td>{{ $record->currency }}</td>
                        <td>{{ $record->valid_till ? \Carbon\Carbon::parse($record->valid_till)->format('d M Y') : '' }}</td>
                        <td><span class="badge badge-{{ $record->status ? 'success' : 'warning' }}">{{ $record->status ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <a href="{{ route('admin.commercial.pricingListAction', ['view', $record->id]) }}" class="btn btn-sm btn-info"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.commercial.pricingListAction', ['edit', $record->id]) }}" class="btn btn-sm btn-success"><i class="bx bx-edit"></i></a>
                            <a href="{{ route('admin.commercial.pricingListAction', ['delete', $record->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center">No records found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $records->links() }}
    </div>
</div>
@endsection
