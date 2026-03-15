@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Asset Distribution') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Asset Distribution</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.assets.distribution.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Assign Asset</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Asset</th>
                    <th>Employee</th>
                    <th>Assignment Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($distributions as $dist)
                <tr>
                    <td>{{ $dist->asset->name ?? 'N/A' }}</td>
                    <td>{{ $dist->user->name ?? 'N/A' }}</td>
                    <td>{{ $dist->assignment_date }}</td>
                    <td>{{ $dist->return_date ?? 'Not returned' }}</td>
                    <td>
                        @if($dist->status == 'active')
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-info">Returned</span>
                        @endif
                    </td>
                    <td>
                        @if($dist->status == 'active')
                        <a href="{{ route('admin.assets.distribution.return', $dist->id) }}" class="btn btn-sm btn-warning">Return</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">No data found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
