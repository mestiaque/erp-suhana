@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Production Planning List') }}</title>
@endsection

@push('css')
<style type="text/css">

</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Planning List</h3>
             <div class="dropdown d-flex">
                <form action="{{ route('admin.productionPlanning') }}" method="GET" target="_blank" class="d-inline">
                    <input type="hidden" name="print" value="true">
                    <input type="hidden" name="startDate" value="{{ request()->startDate }}">
                    <input type="hidden" name="endDate" value="{{ request()->endDate }}">
                    <input type="hidden" name="search" value="{{ request()->search }}">
                    <button type="submit" class="btn btn-info btn-sm mr-1">
                        <i class="fa fa-print"></i> Print
                    </button>
                </form>

                @can('production_planning.add')
                 <a href="{{ route('admin.productionPlanningAction','create') }}" class="btn-custom primary mr-1" style="padding:5px 15px;">
                     <i class="bx bx-plus"></i> Add Planning
                 </a>
                @endcan

                 <a href="{{ route('admin.productionPlanning') }}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{ route('admin.productionPlanning') }}">
                <div class="row mb-2">
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate ? Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') : '' }}" class="form-control" />
                            <input type="date" name="endDate" value="{{ request()->endDate ? Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') : '' }}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search Style No, Buyer, Merchandiser" class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Status Filter -->
            <div class="row mb-2">
                <div class="col-md-12">
                    <ul class="statuslist p-0">
                        <li><a href="{{ route('admin.productionPlanning') }}">All ({{ $totals->total }})</a></li>
                        <li><a href="{{ route('admin.productionPlanning',['status'=>'confirmed']) }}">Confirmed ({{ $totals->confirmed }})</a></li>
                    </ul>
                </div>
            </div>

            <!-- Samples Table -->
            <div class="table-responsive">
                <table class="table table-striped table-borderd">
                    <thead>
                        <tr>
                            <th style="width: 150px;min-width: 150px">Style No</th>
                            <th style="width: 150px;min-width: 150px">Merchant/Buyer</th>
                            <th style="min-width:200px">Cutting</th>
                            <th style="min-width:200px">Swetting</th>
                            <th style="width: 150px;min-width: 150px">Total Hours</th>
                            <th style="width: 200px;min-width: 200px">Packing</th>
                            <th style="width: 160px;min-width: 160px">Plan By/Date</th>
                            <th style="width: 200px;min-width: 200px">Action/Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $i => $order)
                        <tr>
                            <td>
                                <b>No:</b> {{ $order->style_no}}
                                <br> <b>Qnty:</b> {{number_format($order->style_qty)}} pcs
                            </td>
                            <td>
                                <b>M:</b> {{$order->getPiStyle()?->merchant_name}}
                                <br><b>B:</b> {{$order->getPiStyle()?->buyer_name}}

                            </td>
                            <td>
                                <b>S:</b> {{$order->cutting_start?Carbon\Carbon::parse($order->cutting_start)->format('d.m.Y h:i A'):''}}
                                <br><b>E:</b> {{$order->cutting_end?Carbon\Carbon::parse($order->cutting_end)->format('d.m.Y h:i A'):''}}
                            </td>
                            <td>
                                <b>S:</b> {{$order->sewing_start?Carbon\Carbon::parse($order->sewing_start)->format('d.m.Y h:i A'):''}}
                                <br><b>E:</b> {{$order->sewing_end?Carbon\Carbon::parse($order->sewing_end)->format('d.m.Y h:i A'):''}}
                            </td>
                            <td>
                                <b>Total:</b> {{$order->total_working_time}}
                                <br><b>H/T:</b> {{$order->total_hourly_capacity}} pcs
                            </td>
                            <td>
                                <b>S:</b> {{$order->packing_start?Carbon\Carbon::parse($order->packing_start)->format('d.m.Y h:i A'):''}}
                                <br><b>E:</b> {{$order->packing_end?Carbon\Carbon::parse($order->packing_end)->format('d.m.Y h:i A'):''}}
                            </td>
                            <td>
                                <b>By:</b> {{ $order->user?->name }}
                                <br><b>Date:</b> {{ $order->created_at->format('d.m.Y') }}
                            </td>
                            <td class="text-center">
                                @if(can('production_planning.view') || can('production_planning.edit') || can('production_planning.delete'))
                                    @can('production_planning.view')
                                    <a href="{{ route('admin.productionPlanningAction',['print',$order->id]) }}" class="btn-custom info mr-1"><i class="fa fa-print"></i></a>
                                    <a href="{{ route('admin.productionPlanningAction',['view',$order->id]) }}" class="btn-custom yellow mr-1"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('production_planning.edit')
                                    <a href="{{ route('admin.productionPlanningAction',['edit',$order->id]) }}" class="btn-custom success mr-1"><i class="bx bx-edit"></i></a>
                                    @endcan
                                    @if($order->sewingOutputs->sum('production')==0)
                                    @can('production_planning.delete')
                                    <a href="{{ route('admin.productionPlanningAction',['delete',$order->id]) }}" onclick="return confirm('Are you sure?')" class="btn-custom danger mr-1"><i class="bx bx-trash"></i></a>
                                    @endcan
                                    @endif
                                @else
                                --
                                @endif
                                <br>
                                @if($order->status=='pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($order->status=='confirmed')
                                    <span class="badge badge-info">Confirmed</span>
                                @elseif($order->status=='completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($order->status=='cancel')
                                    <span class="badge badge-danger">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No Samples Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $orders->links('pagination') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
@endpush
