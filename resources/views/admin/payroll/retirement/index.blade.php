@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Retirement') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Retirement Management</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.retirement.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Retirement Date</th>
                    <th>Type</th>
                    <th>Settlement Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($retirements as $ret)
                <tr>
                    <td>{{ $ret->user->name ?? 'N/A' }}</td>
                    <td>{{ $ret->retirement_date }}</td>
                    <td>{{ ucfirst($ret->type) }}</td>
                    <td>{{ number_format($ret->settlement_amount, 2) }}</td>
                    <td>
                        @if($ret->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($ret->status == 'approved')
                            <span class="badge badge-success">Approved</span>
                        @else
                            <span class="badge badge-info">Processed</span>
                        @endif
                    </td>
                    <td>
                        @if($ret->status == 'pending')
                        <form action="{{ route('admin.retirement.update', $ret->id) }}" method="POST" class="d-inline">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                        </form>
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
