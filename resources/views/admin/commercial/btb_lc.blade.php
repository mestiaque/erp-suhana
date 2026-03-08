@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Bank BTB LC List') }}</title>
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
    <h1>Bank BTB LC</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Commercial</li>
        <li class="item">Bank BTB LC</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Bank BTB LC List</h3>
        <a href="{{ route('admin.commercial.btbLcAction', 'create') }}" class="btn-custom primary"><i class="bx bx-plus"></i> Add New LC</a>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')

        <form action="{{ route('admin.commercial.btbLc') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request()->search }}" placeholder="Search LC No" class="form-control">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="all">All Status</option>
                        <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Pending</option>
                        <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>Active</option>
                        <option value="3" {{ request()->status == 3 ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success">Search</button>
                </div>
            </div>
        </form>

        <ul class="statuslist mb-3">
            <li><a href="{{ route('admin.commercial.btbLc') }}" class="{{ !request()->status ? 'active' : '' }}">All ({{ $statusCounts['total'] }})</a></li>
            <li><a href="{{ route('admin.commercial.btbLc', ['status' => 1]) }}" class="{{ request()->status == 1 ? 'active' : '' }}">Pending ({{ $statusCounts['pending'] }})</a></li>
            <li><a href="{{ route('admin.commercial.btbLc', ['status' => 2]) }}" class="{{ request()->status == 2 ? 'active' : '' }}">Active ({{ $statusCounts['active'] }})</a></li>
            <li><a href="{{ route('admin.commercial.btbLc', ['status' => 3]) }}" class="{{ request()->status == 3 ? 'active' : '' }}">Closed ({{ $statusCounts['closed'] }})</a></li>
        </ul>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>LC No</th>
                        <th>Supplier</th>
                        <th>LC Value</th>
                        <th>Used Value</th>
                        <th>Remaining</th>
                        <th>Bank</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><a href="{{ route('admin.commercial.btbLcAction', ['view', $record->id]) }}">{{ $record->lc_no }}</a></td>
                        <td>{{ $record->supplier_name }}</td>
                        <td>{{ $record->currency }} {{ number_format($record->lc_value, 2) }}</td>
                        <td>{{ number_format($record->used_value, 2) }}</td>
                        <td>{{ number_format($record->remaining_value, 2) }}</td>
                        <td>{{ $record->bank_name }}</td>
                        <td>{{ $record->lc_expiry_date ? \Carbon\Carbon::parse($record->lc_expiry_date)->format('d M Y') : '' }}</td>
                        <td><span class="badge badge-{{ $record->status == 1 ? 'warning' : ($record->status == 2 ? 'success' : 'secondary') }}">{{ $record->status_label }}</span></td>
                        <td>
                            <a href="{{ route('admin.commercial.btbLcAction', ['view', $record->id]) }}" class="btn btn-sm btn-info"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.commercial.btbLcAction', ['edit', $record->id]) }}" class="btn btn-sm btn-success"><i class="bx bx-edit"></i></a>
                            <a href="{{ route('admin.commercial.btbLcAction', ['delete', $record->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash"></i></a>
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
