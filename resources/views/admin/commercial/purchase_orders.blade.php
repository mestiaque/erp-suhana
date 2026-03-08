@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Purchase Order List') }}</title>
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
    <h1>Purchase Order (PO)</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Commercial</li>
        <li class="item">Purchase Order</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Purchase Order List</h3>
        <a href="{{ route('admin.commercial.purchaseOrdersAction', 'create') }}" class="btn-custom primary"><i class="bx bx-plus"></i> Add New</a>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')

        <form action="{{ route('admin.commercial.purchaseOrders') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4"><input type="text" name="search" value="{{ request()->search }}" placeholder="Search PO No" class="form-control"></div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="all">All Status</option>
                        <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Pending</option>
                        <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Confirmed</option>
                        <option value="3" {{ request()->status == 3 ? 'selected' : '' }}>Shipped</option>
                        <option value="4" {{ request()->status == 4 ? 'selected' : '' }}>Received</option>
                    </select>
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-success">Search</button></div>
            </div>
        </form>

        <ul class="statuslist mb-3">
            <li><a href="{{ route('admin.commercial.purchaseOrders') }}" class="{{ !request()->status ? 'active' : '' }}">All ({{ $statusCounts['total'] }})</a></li>
            <li><a href="{{ route('admin.commercial.purchaseOrders', ['status' => 1]) }}" class="{{ request()->status == 1 ? 'active' : '' }}">Pending ({{ $statusCounts['pending'] }})</a></li>
            <li><a href="{{ route('admin.commercial.purchaseOrders', ['status' => 2]) }}" class="{{ request()->status == 2 ? 'active' : '' }}">Confirmed ({{ $statusCounts['confirmed'] }})</a></li>
            <li><a href="{{ route('admin.commercial.purchaseOrders', ['status' => 3]) }}" class="{{ request()->status == 3 ? 'active' : '' }}">Shipped ({{ $statusCounts['shipped'] }})</a></li>
            <li><a href="{{ route('admin.commercial.purchaseOrders', ['status' => 4]) }}" class="{{ request()->status == 4 ? 'active' : '' }}">Received ({{ $statusCounts['received'] }})</a></li>
        </ul>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>SL</th><th>PO No</th><th>Supplier</th><th>Buyer</th><th>Total Qty</th><th>Total Amount</th><th>PO Date</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><a href="{{ route('admin.commercial.purchaseOrdersAction', ['view', $record->id]) }}">{{ $record->po_no }}</a></td>
                        <td>{{ $record->supplier_name }}</td>
                        <td>{{ $record->buyer_name }}</td>
                        <td>{{ number_format($record->total_qty, 2) }}</td>
                        <td>{{ $record->currency }} {{ number_format($record->total_amount, 2) }}</td>
                        <td>{{ $record->po_date ? \Carbon\Carbon::parse($record->po_date)->format('d M Y') : '' }}</td>
                        <td><span class="badge badge-{{ $record->status == 1 ? 'warning' : ($record->status == 2 ? 'success' : ($record->status == 3 ? 'info' : 'primary')) }}">{{ $record->status_label }}</span></td>
                        <td>
                            <a href="{{ route('admin.commercial.purchaseOrdersAction', ['view', $record->id]) }}" class="btn btn-sm btn-info"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.commercial.purchaseOrdersAction', ['edit', $record->id]) }}" class="btn btn-sm btn-success"><i class="bx bx-edit"></i></a>
                            <a href="{{ route('admin.commercial.purchaseOrdersAction', ['delete', $record->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash"></i></a>
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
