@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Bonus') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Bonus</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.bonus.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Month</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bonuses as $bonus)
                <tr>
                    <td>{{ $bonus->user->name ?? 'All' }}</td>
                    <td>{{ ucfirst($bonus->type) }}</td>
                    <td>{{ number_format($bonus->amount, 2) }}</td>
                    <td>{{ $bonus->month }}</td>
                    <td>
                        @if($bonus->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($bonus->status == 'approved')
                            <span class="badge badge-success">Approved</span>
                        @else
                            <span class="badge badge-info">Paid</span>
                        @endif
                    </td>
                    <td>
                        @if($bonus->status == 'pending')
                        <form action="{{ route('admin.bonus.update', $bonus->id) }}" method="POST" class="d-inline">
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
