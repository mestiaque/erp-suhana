@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Export Realization') }}</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>Export Realization</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Commercial</li>
        <li class="item">Export Realization</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Export Realization List</h3>
        <a href="{{ route('admin.commercial.realizationAction', 'create') }}" class="btn-custom primary"><i class="bx bx-plus"></i> Add New</a>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')

        <form action="{{ route('admin.commercial.realization') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4"><input type="text" name="search" value="{{ request()->search }}" placeholder="Search Realization No" class="form-control"></div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="all">All Status</option>
                        <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Pending</option>
                        <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Partial</option>
                        <option value="3" {{ request()->status == 3 ? 'selected' : '' }}>Realized</option>
                    </select>
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-success">Search</button></div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>SL</th><th>Realization No</th><th>LC No</th><th>Buyer</th><th>Invoice Value</th><th>Realized Value</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($records as $index => $record)
                    <tr>
                        <td>{{ $records->firstItem() + $index }}</td>
                        <td><a href="{{ route('admin.commercial.realizationAction', ['view', $record->id]) }}">{{ $record->realization_no }}</a></td>
                        <td>{{ $record->lc_no }}</td>
                        <td>{{ $record->buyer_name }}</td>
                        <td>{{ $record->currency }} {{ number_format($record->invoice_value, 2) }}</td>
                        <td>{{ $record->currency }} {{ number_format($record->realized_value, 2) }}</td>
                        <td>{{ $record->realization_date ? \Carbon\Carbon::parse($record->realization_date)->format('d M Y') : '' }}</td>
                        <td>{{ $record->status_label }}</td>
                        <td>
                            <a href="{{ route('admin.commercial.realizationAction', ['view', $record->id]) }}" class="btn btn-sm btn-info"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.commercial.realizationAction', ['edit', $record->id]) }}" class="btn btn-sm btn-success"><i class="bx bx-edit"></i></a>
                            <a href="{{ route('admin.commercial.realizationAction', ['delete', $record->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash"></i></a>
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
