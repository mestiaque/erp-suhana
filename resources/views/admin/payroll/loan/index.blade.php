@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Loan') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Loan Management</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.loan.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Principal</th>
                    <th>Interest Rate</th>
                    <th>Total Amount</th>
                    <th>Paid/Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $loan)
                <tr>
                    <td>{{ $loan->user->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($loan->type) }}</td>
                    <td>{{ number_format($loan->principal_amount, 2) }}</td>
                    <td>{{ $loan->interest_rate }}%</td>
                    <td>{{ number_format($loan->total_amount, 2) }}</td>
                    <td>{{ $loan->paid_installments }}/{{ $loan->total_installments }}</td>
                    <td>
                        @if($loan->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($loan->status == 'active')
                            <span class="badge badge-success">Active</span>
                        @elseif($loan->status == 'completed')
                            <span class="badge badge-info">Completed</span>
                        @else
                            <span class="badge badge-danger">Rejected</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">No data found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
