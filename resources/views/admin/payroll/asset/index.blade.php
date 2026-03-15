@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Assets') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Asset Management</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.assets.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Serial Number</th>
                    <th>Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td>{{ $asset->name }}</td>
                    <td>{{ ucfirst($asset->category) }}</td>
                    <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                    <td>{{ number_format($asset->value, 2) }}</td>
                    <td>
                        @if($asset->status == 'available')
                            <span class="badge badge-success">Available</span>
                        @elseif($asset->status == 'assigned')
                            <span class="badge badge-info">Assigned</span>
                        @elseif($asset->status == 'maintenance')
                            <span class="badge badge-warning">Maintenance</span>
                        @else
                            <span class="badge badge-secondary">Retired</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No data found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
