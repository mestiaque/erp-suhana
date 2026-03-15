@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Provident Fund') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Provident Fund</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.provident-fund.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Year</th>
                    <th>Month</th>
                    <th>Employee Contribution</th>
                    <th>Company Contribution</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($providentFunds as $pf)
                <tr>
                    <td>{{ $pf->user->name ?? 'N/A' }}</td>
                    <td>{{ $pf->year }}</td>
                    <td>{{ $pf->month }}</td>
                    <td>{{ number_format($pf->employee_contribution, 2) }}</td>
                    <td>{{ number_format($pf->company_contribution, 2) }}</td>
                    <td>{{ number_format($pf->total_amount, 2) }}</td>
                    <td><span class="badge badge-success">{{ ucfirst($pf->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">No data found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
