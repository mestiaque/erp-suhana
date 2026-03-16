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
                <div class="col-md-4"><input type="text" name="search" value="{{ request()->search }}" placeholder="Search Price List No" class="form-control"></div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="all">All Status</option>
                        <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Expired</option>
                        <option value="3" {{ request()->status == 3 ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-success">Search</button></div>
            </div>
        </form>

        <ul class="statuslist mb-3">
            <li><a href="{{ route('admin.commercial.pricingList') }}" class="{{ !request()->status ? 'active' : '' }}">All ({{ $statusCounts['total'] }})</a></li>
            <li><a href="{{ route('admin.commercial.pricingList', ['status' => 1]) }}" class="{{ request()->status == 1 ? 'active' : '' }}">Active ({{ $statusCounts['active'] }})</a></li>
            <li><a href="{{ route('admin.commercial.pricingList', ['status' => 2]) }}" class="{{ request()->status == 2 ? 'active' : '' }}">Expired ({{ $statusCounts['expired'] }})</a></li>
            <li><a href="{{ route('admin.commercial.pricingList', ['status' => 3]) }}" class="{{ request()->status == 3 ? 'active' : '' }}">Cancelled ({{ $statusCounts['cancelled'] }})</a></li>
        </ul>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>SL</th><th>Price List No</th><th>Buyer</th><th>Effective Date</th><th>Expiry Date</th><th>Season</th><th>Year</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($records as $index => $record)
                    <tr>
                        <td>{{ $records->firstItem() + $index }}</td>
                        <td><a href="{{ route('admin.commercial.pricingListAction', ['view', $record->id]) }}">{{ $record->price_list_no }}</a></td>
                        <td>{{ $record->buyer_name }}</td>
                        <td>{{ $record->effective_date ? \Carbon\Carbon::parse($record->effective_date)->format('d M Y') : '' }}</td>
                        <td>{{ $record->expiry_date ? \Carbon\Carbon::parse($record->expiry_date)->format('d M Y') : '' }}</td>
                        <td>{{ $record->season }}</td>
                        <td>{{ $record->year }}</td>
                        <td>{{ $record->status_label }}</td>
                        <td>
                            <a href="{{ route('admin.commercial.pricingListAction', ['view', $record->id]) }}" class="btn btn-sm btn-info"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.commercial.pricingListAction', ['edit', $record->id]) }}" class="btn btn-sm btn-success"><i class="bx bx-edit"></i></a>
                            <a href="{{ route('admin.commercial.pricingListAction', ['delete', $record->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center">No records found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $records->links() }}
    </div>
</div>
@endsection
