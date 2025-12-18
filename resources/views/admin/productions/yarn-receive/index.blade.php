@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Yarn Receive List') }}</title>
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
            <h3>Yarn Receive List</h3>
            <div class="dropdown">
                {{-- @can('production_planning.add') --}}
                    {{-- <a href="{{ route('admin.yarnReceiveAction','create') }}" class="btn-custom primary" style="padding:5px 15px;">
                        <i class="bx bx-plus"></i> Add Yarn Receive
                    </a> --}}
                {{-- @endcan --}}

                    <a href="{{ route('admin.yarnReceive') }}" class="btn-custom yellow">
                        <i class="bx bx-rotate-left"></i>
                    </a>
            </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            {{-- Search / Filter --}}
            <form method="GET" action="{{ route('admin.yarnReceive') }}">
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
                                   placeholder="Search Buyer, Pi No, Receive No, Fabrication"
                                   class="form-control">

                            <button class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>

                </div>
            </form>

            {{-- Table --}}
            {{-- <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 60px">SL</th>
                            <th>Receive No</th>
                            <th>Receive Date</th>
                            <th>Supplier</th>
                            <th>Total Items</th>
                            <th>Total Req. Qty</th>
                            <th>Added By</th>
                            <th style="width: 200px">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($bookings as $i => $row)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $row->getReceiveNo() }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d.m.Y') }}</td>
                            <td>{{ $row->supplier ?? '-' }}</td>
                            <td class="text-center">{{ $row->total_items }}</td>
                            <td class="text-center">{{ $row->total_req_qty }}</td>
                            <td>
                                @php
                                    $createdBy = App\Models\User::findOrFail($row->created_by);
                                @endphp
                                {{ $createdBy?->name ?? '-' }}
                            </td>
                            <td>
                                <a href="javascript:void(0)" class="btn-custom yellow mr-1" data-toggle="modal" data-target="#viewModal_{{ $row->booking_no }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.yarnReceiveAction',['edit',$row->booking_no]) }}" class="btn-custom success mr-1">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{ route('admin.yarnReceiveAction',['delete',$row->booking_no]) }}" onclick="return confirm('Are You Sure To Delete?')" class="btn-custom danger">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">
                                No Yarn Receive Found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div> --}}

            {{-- Pagination --}}
            <div class="mt-3">
                {{-- {{ $bookings->appends(request()->query())->links() }} --}}
            </div>

        </div>
    </div>

     {{-- @include(adminTheme().'productions.yarn-booking.includes.details') --}}


</div>
@endsection

@push('js')
@endpush
