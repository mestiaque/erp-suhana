@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Yarn Booking List') }}</title>
@endsection

@push('css')
<style>
.table th, .table td { vertical-align: middle; }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Yarn Booking List</h3>

            <a href="{{ route('admin.yarnBookingAction', 'create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> Add New
            </a>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            {{-- Search / Filter --}}
            <form method="GET" action="{{ route('admin.yarnBooking') }}">
                <div class="row mb-3">

                    {{-- Date Range --}}
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate"
                                   value="{{ request()->startDate ?? '' }}"
                                   class="form-control">

                            <input type="date" name="endDate"
                                   value="{{ request()->endDate ?? '' }}"
                                   class="form-control">
                        </div>
                    </div>

                    {{-- Search Text --}}
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search"
                                   value="{{ request()->search ?? '' }}"
                                   placeholder="Search Buyer, Pi No, Booking No, Fabrication"
                                   class="form-control">

                            <button class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>

                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 60px">SL</th>
                            <th>Booking No</th>
                            <th>Buyer</th>
                            <th>Booking Date</th>
                            <th>Total Items</th>
                            <th>Total Req. Qty</th>
                            <th>Total Del. Qty</th>
                            <th>Added By</th>
                            <th style="width: 200px">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($list as $i => $row)
                        <tr>
                            <td class="text-center">{{ $list->firstItem() + $i }}</td>
                            <td>{{ $row->getBookingNo() }}</td>
                            <td>{{ $row->buyer?->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->booking_date)->format('d-m-Y') }}</td>
                            <td class="text-center">{{ $row->items->count() }}</td>
                            <td class="text-">
                                {{ number_format($row->items->sum('requisition_qty'), 2) }}
                            </td>
                            <td class="text-">
                                {{ number_format($row->items->sum('received_qty'), 2) }}
                            </td>
                            <td>{{ $row->addedBy?->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.yarnBookingAction',['delivery',$row->id]) }}" class="btn-custom info mr-1">
                                    <i class="fa fa-truck"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn-custom yellow mr-1" data-toggle="modal" data-target="#viewModal_{{ $row->id }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.yarnBookingAction',['edit',$row->id]) }}" class="btn-custom success mr-1">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{ route('admin.yarnBookingAction',['delete',$row->id]) }}" onclick="return confirm('Are You Sure To Delete?')" class="btn-custom danger">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">
                                No Yarn Booking Found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $list->appends(request()->query())->links() }}
            </div>

        </div>
    </div>

     @include(adminTheme().'productions.yarn-booking.includes.details')


</div>
@endsection

@push('js')
@endpush
