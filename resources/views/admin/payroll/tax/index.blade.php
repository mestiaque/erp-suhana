@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Tax') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Tax Management</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.tax.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
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
                    <th>Gross Salary</th>
                    <th>Taxable Income</th>
                    <th>Net Tax</th>
                </tr>
            </thead>
            <tbody>
                @forelse($taxes as $tax)
                <tr>
                    <td>{{ $tax->user->name ?? 'N/A' }}</td>
                    <td>{{ $tax->year }}</td>
                    <td>{{ $tax->month }}</td>
                    <td>{{ number_format($tax->gross_salary, 2) }}</td>
                    <td>{{ number_format($tax->taxable_income, 2) }}</td>
                    <td>{{ number_format($tax->net_tax, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">No data found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
