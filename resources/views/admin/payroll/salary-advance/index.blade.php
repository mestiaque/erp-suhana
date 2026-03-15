@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Salary Advance') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Salary Advance</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.salary-advance.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Requested Amount</th>
                    <th>Installments</th>
                    <th>Monthly Deduction</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($advances as $adv)
                <tr>
                    <td>{{ $adv->user->name ?? 'N/A' }}</td>
                    <td>{{ number_format($adv->requested_amount, 2) }}</td>
                    <td>{{ $adv->installment_months }}</td>
                    <td>{{ number_format($adv->monthly_deduction, 2) }}</td>
                    <td>
                        @if($adv->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($adv->status == 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($adv->status == 'disbursed')
                            <span class="badge badge-info">Disbursed</span>
                        @else
                            <span class="badge badge-danger">Rejected</span>
                        @endif
                    </td>
                    <td>
                        @if($adv->status == 'pending')
                        <form action="{{ route('admin.salary-advance.update', $adv->id) }}" method="POST" class="d-inline">
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
