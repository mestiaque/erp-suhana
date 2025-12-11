@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Order Details List') }}</title>
@endsection


@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Order Details List</h3>
             <div class="dropdown">
                @can('order_details.add')
                 <a href="{{ route('admin.orderDetailsAction','create') }}" class="btn-custom primary" style="padding:5px 15px;">
                     <i class="bx bx-plus"></i> Add Order Details
                 </a>
                 @endcan
                 <a href="{{ route('admin.orderDetails') }}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{ route('admin.orderDetails') }}">
                <div class="row mb-2">
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate ? Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') : '' }}" class="form-control" />
                            <input type="date" name="endDate" value="{{ request()->endDate ? Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') : '' }}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search Order, Buyer, Style, Merchant, Invoice, Order, Composition, Gabrication, GMS " class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Status Filter -->
            <div class="row mb-2">
                <div class="col-md-12">
                    <ul class="statuslist p-0">
                        <li><a href="{{ route('admin.orderDetails') }}">All ({{ $totals->total }})</a></li>
                        {{-- <li><a href="{{ route('admin.orderDetails',['status'=>'temp']) }}">Temp ({{ $totals->temp }})</a></li> --}}
                        <li><a href="{{ route('admin.orderDetails',['status'=>'pending']) }}">Pending ({{ $totals->pending }})</a></li>
                        <li><a href="{{ route('admin.orderDetails',['status'=>'confirmed']) }}">Confirmed ({{ $totals->confirmed }})</a></li>
                        <li><a href="{{ route('admin.orderDetails',['status'=>'completed']) }}">Completed ({{ $totals->completed }})</a></li>
                        <li><a href="{{ route('admin.orderDetails',['status'=>'cancel']) }}">Cancelled ({{ $totals->canceled }})</a></li>
                    </ul>
                </div>
            </div>

            <!-- orderDetails Table -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered nested-table">
                    <thead>
                        <tr>
                            <th style="width: 80px">SL</th>
                            <th style="min-width: 200px">Buyer Name</th>
                            <th style="min-width: 200px">Brand / Customer</th>
                            <th style="min-width: 200px">Style No</th>
                            <th style="min-width: 120px">Order/PO No</th>
                            <th style="min-width: 120px">Order Qty</th>
                            <th style="min-width: 150px">Shipment Date</th>
                            <th style="min-width: 200px">Fabrication</th>
                            <th style="min-width: 100px">GSM</th>
                            <th style="min-width: 150px">Remarks</th>
                            <th style="min-width: 120px">Status</th>
                            <th style="min-width: 150px">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orderDetails as $i => $order)
                            {{-- Main Order Row --}}
                            <tr data-toggle="collapse" data-target="#items_{{ $order->id }}" class="accordion-toggle order-parent">
                                <td>{{$orderDetails->currentpage()==1?$i+1:$i+($orderDetails->perpage()*($orderDetails->currentpage() - 1))+1}}</td>
                                <td>{{ $order->buyer_name ?? '--' }}</td>
                                <td>{{ $order->company_name ?? '--' }}</td>
                                <td>{{ $order->style_no ?? '--' }}</td>
                                <td>{{ $order->order_no ?? '--' }}</td>
                                <td>{{ numberFormat($order->total_qty, 2) ?? '--' }}</td>
                                <td>{{ $order && $order->shipment_date ? \Carbon\Carbon::parse($order->shipment_date)->format('d.m.Y') : '--' }}</td>
                                {{-- <td>{{ $order->composition ?? '--' }}</td> --}}
                                <td>{{ $order->fabrication ?? '--' }}</td>
                                <td>{{ $order->gsm ?? '--' }}</td>
                                <td>{{ $order->remarks ?? '--' }}</td>
                                <td>
                                    @if($order->status=='temp')
                                        <span class="badge badge-secondary">Temp</span>
                                    @elseif($order->status=='pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($order->status=='confirmed')
                                        <span class="badge badge-info">Confirmed</span>
                                    @elseif($order->status=='completed')
                                        <span class="badge badge-success">Completed</span>
                                    @elseif($order->status=='canceled')
                                        <span class="badge badge-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td class="text-center no-collapse">
                                    @can('order_details.view')
                                    <a href="javascript:void(0)" class="btn-custom yellow mr-1" data-toggle="modal" data-target="#viewModal_{{ $order->id }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @endcan

                                    @if(in_array($order->status, ['confirmed','pending']))
                                        @can('order_details.edit')
                                        <a href="{{ route('admin.orderDetailsAction',['edit',$order->id]) }}" class="btn-custom success mr-1">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        @endcan

                                        @can('order_details.delete')
                                        <a href="{{ route('admin.orderDetailsAction',['delete',$order->id]) }}" onclick="return confirm('Are You Sure To Delete?')" class="btn-custom danger">
                                            <i class="bx bx-trash"></i>
                                        </a>
                                        @endcan
                                    @endif
                                </td>
                            </tr>

                            {{-- Collapsible Items Row --}}
                            <tr class="order-child">
                                <td colspan="13" class="hiddenRow p-0">
                                    <div class="collapse" id="items_{{ $order->id }}">
                                        <table class="table table-sm mb-0" >
                                            <thead style="background: #7c7c7cd9">
                                                <tr>
                                                    <th style="width: 42px">SL</th>
                                                    <th style="min-width: 228px">Buyer Name</th>
                                                    <th style="min-width: 195px">Brand / Customer</th>
                                                    <th style="min-width: 193px">Style No</th>
                                                    <th style="min-width: 115px">Order/PO No</th>
                                                    <th style="min-width: 117px">Order Qty</th>
                                                    <th style="min-width: 147px">Shipment Date</th>
                                                    <th style="min-width: 200px">Composition</th>
                                                    <th style="min-width: 200px">Fabrication</th>
                                                    <th style="min-width: 100px">GSM</th>
                                                    <th style="min-width: 200px">Color Name</th>
                                                    <th style="min-width: 120px">Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($order->items as $ii=>$item)
                                                <tr>
                                                    <td>{{ $ii+1 }}</td>
                                                    <td>{{ $order->buyer_name ?? '--' }}</td>
                                                    <td>{{ $order->company_name ?? '--' }}</td>
                                                    <td>{{ $order->style_no ?? '--' }}</td>
                                                    <td>{{ $order->order_no ?? '--' }}</td>
                                                    @if($ii == 0)
                                                        <td style="vertical-align: middle;" rowspan="{{ count($order->items) }}">{{ numberFormat($order->total_qty, 2) ?? '--' }}</td>
                                                    @endif
                                                    <td>{{ $order && $order->shipment_date ? \Carbon\Carbon::parse($order->shipment_date)->format('d.m.Y') : '--' }}</td>
                                                    <td>{{ $item->composition ?? $order->composition ?? '--' }}</td>
                                                    <td>{{ $order->fabrication ?? '--' }}</td>
                                                    <td>{{ $order->gsm ?? '--' }}</td>
                                                    <td>{{ $item->color_name ?? '--' }}</td>
                                                    <td>{{ numberFormat($item->qty, 2) ?? '--' }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="12" class="text-center text-muted">No items found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>


                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted">No order details found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $orderDetails->links('pagination') }}
            </div>



        </div>
    </div>

</div>
@include(adminTheme().'merchandising.orderDetails.details')
@endsection



@push('css')
<style>
    /* Optional: Enhance collapse hover effect */
    .accordion-toggle:hover {
        background-color: #f1f1f1;
        cursor: pointer;
    }

    /* Table borders rounded */
    table.table {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .order-parent {
    border-top: 3px solid #dee2e6; /* red border */
    }

    .order-child {
        border-bottom: 3px solid #dee2e6;
    }


</style>
@endpush

@push('js')
<script>
    $(document).on('click', '.no-collapse', function(e) {
        e.stopPropagation();
    });
</script>
@endpush
