@extends(adminTheme().'layouts.app')

@section('title')
    <title>{{ websiteTitle('Budget List') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Order Details List</h3>
            <div class="d-flex gap-1">
                @can('budget.add')
                    <a href="{{ route('admin.budgetAction','create') }}" class="btn btn-primary btn-sm mr-1">
                        <i class="bx bx-plus"></i> Add Budget
                    </a>
                @endcan

                <a href="{{ route('admin.budget') }}" class="btn btn-warning btn-sm">
                    <i class="bx bx-rotate-left"></i> Reset
                </a>

            </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search & Date Filter -->
            <form action="{{ route('admin.budget') }}" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-6 d-flex">
                        <input type="date" name="startDate" value="{{ request()->startDate }}" class="form-control me-1">
                        <input type="date" name="endDate" value="{{ request()->endDate }}" class="form-control">
                    </div>
                    <div class="col-md-6 d-flex">
                        <input type="text" name="search" value="{{ request()->search ?? '' }}" class="form-control me-1"
                               placeholder="Search by PI No, Buyer...">
                        <button type="submit" class="btn btn-success btn-sm">Search</button>
                    </div>
                </div>
            </form>

            <div class="row mb-0">
                <div class="col-md-12">
                    {{-- <ul class="statuslist p-0 mb-0">
                        <li class=""><a class=" {{ !request('status') ? 'active' : '' }}" href="{{ route('admin.budget') }}">All ({{ $totals->total }})</a></li>
                        <li class=""><a class=" {{ request('status')=='pending' ? 'active' : '' }}" href="{{ route('admin.budget',['status'=>'pending']) }}">Pending ({{ $totals->pending }})</a></li>
                        <li class=""><a class=" {{ request('status')=='confirmed' ? 'active' : '' }}" href="{{ route('admin.budget',['status'=>'confirmed']) }}">Confirmed ({{ $totals->confirmed }})</a></li>
                    </ul> --}}
                </div>
            </div>

            <!-- Orders Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>SL</th>
                            <th>PI No</th>
                            <th>Buyer</th>
                            <th>Total PO</th>
                            <th>Total Style</th>
                            <th>PI Value</th>
                            <th>Total Qty</th>
                            <th>Created At</th>
                            <th>Created By</th>
                            <th style="min-width: 140px">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-hover">
                        @forelse($budgets as $i => $budget)
                            <tr>
                                <td>{{ $budgets->firstItem() + $i }}</td>
                                <td>{{ $budget->pi_no ?? '--' }}</td>
                                <td>{{ $budget->buyer ?? '--' }}</td>
                                <td>{{ $budget->total_po ?? 0 }}</td>
                                <td>{{ $budget->total_style ?? 0 }}</td>

                                <td>{{ $budget->pi_value ?? 0 }}</td>
                                <td>{{ number_format($budget->total_qty ?? 0) }}</td>
                                <td>{{ $budget->created_at?->format('d.m.Y H:i') ?? '--' }}</td>
                                <td>{{ $budget->created_by ? $budget->creator?->name ?? $budget->created_by : '--' }}</td>
                                <td>
                                    @can('budget.view')
                                        <a href="{{ route('admin.budgetAction', ['view', $budget->id]) }}" class="btn btn-sm btn-warning"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('budget.edit')
                                        <a href="{{ route('admin.budgetAction', ['edit', $budget->id]) }}" class="btn btn-sm btn-success"><i class="bx bx-edit"></i></a>
                                    @endcan
                                    @can('budget.delete')
                                        <a href="{{ route('admin.budgetAction', ['delete', $budget->id]) }}" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="text-center text-muted">No budget found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $budgets->links('pagination') }}
            </div>
        </div>
    </div>
</div>

@endsection

@push('css')

@endpush

@push('js')
<script>
    $(document).on('click', '.no-collapse', function(e){
        e.stopPropagation();
    });
</script>
@endpush
