@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Deductions') }}</title>
@endsection

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h5 class="mb-0">Deductions</h5></div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('admin.deductions.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add New</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Month</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deductions as $ded)
                    <tr>
                        <td>{{ $ded->user->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($ded->type) }}</td>
                        <td>{{ number_format($ded->amount, 2) }}</td>
                        <td>{{ $ded->month }}</td>
                        <td><span class="badge badge-{{ $ded->status == 'deducted' ? 'success' : 'warning' }}">{{ ucfirst($ded->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">No data found</td></tr>
                    @endforelse
                </tbody>
                    <div class="flex-grow-1">
                        <div class="breadcrumb-area">
                            <h1>Deductions</h1>
                            <ol class="breadcrumb">
                                <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
                                <li class="item">Payroll</li>
                                <li class="item">Deductions</li>
                            </ol>
                        </div>
                        @include(adminTheme().'alerts')
                        <div class="card">
</div>
@endsection
