@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Yarn Receive List') }}</title>
@endsection

@push('css')
<style>
.table th, .table td { vertical-align: middle; }
.btn-custom { padding: 5px 10px; border-radius: 4px; display: inline-block; }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Yarn Receive List</h3>
            <div class="dropdown">
                <a href="{{ route('admin.yarnReceiveAction','create') }}" class="btn-custom primary">
                    <i class="bx bx-plus"></i> Add Yarn Receive
                </a>
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
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate }}" class="form-control">
                            <input type="date" name="endDate" value="{{ request()->endDate }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search }}"
                                   placeholder="Search Receive No, Chalan, Booking No..." class="form-control">
                            <button class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr class="">
                            <th style="width: 50px">SL</th>
                            <th>PI No</th>
                            <th>Booking No</th>
                            <th>Receive No</th>
                            <th>Receive Date</th>
                            <th>Chalan No</th>
                            <th>Supplier</th>
                            <th>Total Recv Qnty</th>
                            <th style="width: 150px">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($receives as $i => $row)
                        <tr>
                            <td class="text-center">{{ $receives->firstItem() + $i }}</td>
                            <td class="text-center">{{ $row->pi->pi_no }}</td>
                            <td class="text-center">{{ $row->getBookingNo() }}</td>
                            <td class="text-center"><b>{{ $row->getRecvNo() }}</b></td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($row->receive_date)->format('d.m.Y') }}</td>
                            <td class="text-center">{{ $row->chalan_no ?? '-' }}</td>
                            <td>{{ $row->supplier ?? '-' }}</td>
                            <td class="text-center"><b>{{ number_format($row->total_receive_qty, 2) }} KG</b></td>
                            <td class="text-center d-flex justify-content-center">

                                <a href="javascript:void(0)" class="btn-custom yellow mr-1" data-toggle="modal" data-target="#viewModal_{{ $row->receive_no }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.yarnReceiveAction',['edit',$row->receive_no]) }}" class="btn-custom success mr-1">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{ route('admin.yarnReceiveAction',['delete',$row->receive_no]) }}" onclick="return confirm('Are You Sure To Delete?')" class="btn-custom danger">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No Yarn Receive Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $receives->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    @include(adminTheme().'productions.yarn-receive.includes.details')
</div>
@endsection
