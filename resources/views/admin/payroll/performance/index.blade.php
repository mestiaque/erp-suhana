@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Performance') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Employee Performance</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.performance.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Year</th>
                    <th>Quarter</th>
                    <th>Rating</th>
                    <th>Reviewer</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($performances as $perf)
                <tr>
                    <td>{{ $perf->user->name ?? 'N/A' }}</td>
                    <td>{{ $perf->year }}</td>
                    <td>{{ $perf->quarter }}</td>
                    <td>{{ $perf->rating }}/5</td>
                    <td>{{ $perf->reviewer->name ?? 'N/A' }}</td>
                    <td><span class="badge badge-success">{{ ucfirst($perf->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">No data found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
