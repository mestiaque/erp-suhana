@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Shipping Documents') }}</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>Shipping Documents</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Commercial</li>
        <li class="item">Shipping Documents</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Shipping Documents List</h3>
        <a href="{{ route('admin.commercial.shippingDocsAction', 'create') }}" class="btn-custom primary"><i class="bx bx-plus"></i> Add New</a>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')

        <form action="{{ route('admin.commercial.shippingDocs') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4"><input type="text" name="search" value="{{ request()->search }}" placeholder="Search Document No" class="form-control"></div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="all">All Status</option>
                        <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Pending</option>
                        <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Submitted</option>
                        <option value="3" {{ request()->status == 3 ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-success">Search</button></div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>SL</th><th>Document No</th><th>Invoice No</th><th>Buyer</th><th>Issue Date</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($records as $index => $record)
                    <tr>
                        <td>{{ $records->firstItem() + $index }}</td>
                        <td><a href="{{ route('admin.commercial.shippingDocsAction', ['view', $record->id]) }}">{{ $record->doc_no }}</a></td>
                        <td>{{ $record->invoice_no }}</td>
                        <td>{{ $record->buyer_name }}</td>
                        <td>{{ $record->issue_date ? \Carbon\Carbon::parse($record->issue_date)->format('d M Y') : '' }}</td>
                        <td>{{ $record->status_label }}</td>
                        <td>
                            <a href="{{ route('admin.commercial.shippingDocsAction', ['view', $record->id]) }}" class="btn btn-sm btn-info"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.commercial.shippingDocsAction', ['edit', $record->id]) }}" class="btn btn-sm btn-success"><i class="bx bx-edit"></i></a>
                            <a href="{{ route('admin.commercial.shippingDocsAction', ['delete', $record->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">No records found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $records->links() }}
    </div>
</div>
@endsection
