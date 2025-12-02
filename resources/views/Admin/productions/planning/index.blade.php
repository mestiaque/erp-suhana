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
             <div class="dropdown">
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
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search Buyer, Style" class="form-control" />
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
                        <li><a href="{{ route('admin.productionPlanning',['status'=>'pending']) }}">Pending ({{ $totals->pending }})</a></li>
                        <li><a href="{{ route('admin.productionPlanning',['status'=>'confirmed']) }}">Confirmed ({{ $totals->confirmed }})</a></li>
                        <li><a href="{{ route('admin.productionPlanning',['status'=>'completed']) }}">Completed ({{ $totals->completed }})</a></li>
                        <li><a href="{{ route('admin.productionPlanning',['status'=>'cancelled']) }}">Cancelled ({{ $totals->cancelled }})</a></li>
                    </ul>
                </div>
            </div>

            <!-- Samples Table -->
            <div class="table-responsive">
                <table class="table table-striped table-borderd">
                    <thead>
                        <tr>
                            <th style="width: 80px">SL</th>
                            <th style="width: 150px">Order No</th>
                            <th style="width: 150px">Merchant</th>
                            <th style="min-width:200px">Buyer</th>
                            <th style="width: 150px">Total Qty</th>
                            <th style="width: 150px">Total Price</th>
                            <th style="width: 150px">Status</th>
                            <th style="width: 150px">Date</th>
                            <th style="width: 150px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $i => $order)
                        <tr>
                            <td>{{$orders->currentpage()==1?$i+1:$i+($orders->perpage()*($orders->currentpage() - 1))+1}}</td>
                            <td>{{ str_pad($order->id, 10, '0', STR_PAD_LEFT) }}</td>
                            <td>{{$order->merchant_name}}</td>
                            <td>
  
                                {{
                                    collect([
                                        $order->buyer_name,
                                        $order?->buyer?->company_name,
                                        $order?->buyer?->country
                                    ])->filter()->implode(' | ')
                                }}
     
                            </td>
                            <td>{{ number_format($order->total_qty) }}</td>
                            <td>{{ numberFormat($order->total_bill,2,$order->currency) }}</td>
                            <td>
                                @if($order->pi_status=='temp')
                                    <span class="badge badge-secondary">Temp</span>
                                @elseif($order->pi_status=='pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($order->pi_status=='confirmed')
                                    <span class="badge badge-info">Confirmed</span>
                                @elseif($order->pi_status=='completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($order->pi_status=='cancel')
                                    <span class="badge badge-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                {{ $order->created_at->format('d.m.Y') }}
                            </td>
                            <td class="text-center">
                                @if(can('samples.view') || can('samples.view') || can('samples.view'))
                                    @can('samples.view')
                                    <a href="{{ route('admin.productionPlanningAction',['view',$order->id]) }}" class="btn-custom yellow mr-1"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('samples.edit')
                                    <a href="{{ route('admin.productionPlanningAction',['edit',$order->id]) }}" class="btn-custom success mr-1"><i class="bx bx-edit"></i></a>
                                    @endcan
                                @else 
                                -- 
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
