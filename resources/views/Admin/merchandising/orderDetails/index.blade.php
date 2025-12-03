@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Order Details List') }}</title>
@endsection

@push('css')
<style type="text/css">

</style>
@endpush

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
                <table class="table table-striped table-borderd">
                    <thead>
                        <tr>
                            <th style="width: 80px">SL</th>
                            <th style="min-width: 200px">Buyer</th>
                            <th style="min-width: 200px">Merchent</th>
                            <th style="min-width: 150px">Style</th>
                            <th style="min-width: 100px">Order No</th>
                            <th style="min-width: 100px">Invoice No</th>
                            <th style="min-width: 250px">Company Name</th>
                            <th style="min-width: 150px">Shipment Date</th>
                            <th style="min-width: 250px">Composition</th>
                            <th style="min-width: 200px">Fabrication</th>
                            <th style="min-width: 100px">GSM</th>
                            <th style="min-width: 100px">Total Qty</th>
                            <th style="min-width: 100px">Total Bill</th>
                            <th style="min-width: 180px">Remarks</th>
                            <th style="min-width: 120px">Create Date</th>
                            <th style="min-width: 100px">Status</th>
                            <th style="min-width: 150px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orderDetails as $i => $order)
                        <tr>
                            <td>{{$orderDetails->currentpage()==1?$i+1:$i+($orderDetails->perpage()*($orderDetails->currentpage() - 1))+1}}</td>
                            <td>{{ $order->buyer_name }}</td>
                            <td>{{ $order->merchant_name ?? '--' }}</td>
                            <td>{{ $order->style_no ?? '--' }}</td>
                            <td>{{ $order->order_no ?? '--' }}</td>
                            <td>{{ $order->invoice_no ?? '--' }}</td>
                            <td>{{ $order->company_name ?? '--' }}</td>
                            <td>{{ $order->shipment_date->format('d.m.Y') }}</td>
                            <td>{{ $order->composition ?? '--' }}</td>
                            <td>{{ $order->fabrication ?? '--' }}</td>
                            <td>{{ $order->gsm ?? '--' }}</td>
                            <td>{{ $order->total_qty ?? '--' }}</td>
                            <td>{{ $order->total_bill ?? '--' }}</td>
                            <td>{{ $order->remarks ?? '--' }}</td>
                            <td>{{ $order->created_at->format('d.m.Y') }}</td>
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

                            <td class="text-center">
                                @if(can('order_details.view') || can('order_details.view') || can('order_details.view'))
                                    @can('order_details.view')
                                    <a href="javascript:void(0)" class="btn-custom yellow mr-1" data-toggle="modal" data-target="#viewModal_{{ $order->id }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @endcan
                                    @if(in_array($order->status, ['confirmed', 'pending']))
                                        @can('order_details.edit')
                                        <a href="{{ route('admin.orderDetailsAction',['edit',$order->id]) }}" class="btn-custom success mr-1"><i class="bx bx-edit"></i></a>
                                        @endcan
                                        @can('order_details.delete')
                                        <a href="{{ route('admin.orderDetailsAction',['delete',$order->id]) }}" onclick="return confirm('Are You Sure To Delete?')" class="btn-custom danger"><i class="bx bx-trash"></i></a>
                                        @endcan
                                    @endif
                                @else -- @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="17" class="text-center text-muted">No order details Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $orderDetails->links('pagination') }}
            </div>
        </div>
    </div>




</div>
@endsection
    @foreach($orderDetails as $order)
        <div class="modal fade" id="viewModal_{{ $order->id }}" tabindex="-1">
            <div class="modal-dialog ">
                <div class="modal-content">

                    <div class="modal-header text-white py-2">
                        <h5 class="modal-title">Order Details ({{ $order->order_no }})</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                    <div class="row g-3">

                        <!-- Basic Info -->
                        <div class="col-12">
                            <h6 class="fw-bold text-primary border-start border-3 ps-2">Basic Information</h6>
                        </div>

                        <div class="col-md-6">
                            <span class="text-secondary fw-semibold">Buyer</span>
                            <div class="fw-normal">{{ $order->buyer_name }}</div>
                        </div>

                        <div class="col-md-6">
                            <span class="text-secondary fw-semibold">Merchandiser</span>
                            <div class="fw-normal">{{ $order->merchant_name ?? '--' }}</div>
                        </div>

                        <div class="col-md-6">
                            <span class="text-secondary fw-semibold">Style</span>
                            <div class="fw-normal">{{ $order->style ?? '--' }}</div>
                        </div>

                        <div class="col-md-6">
                            <span class="text-secondary fw-semibold">Order No</span>
                            <div class="fw-normal">{{ $order->order_no ?? '--' }}</div>
                        </div>

                        <div class="col-md-6">
                            <span class="text-secondary fw-semibold">Invoice No</span>
                            <div class="fw-normal">{{ $order->invoice_no ?? '--' }}</div>
                        </div>

                        <div class="col-md-6">
                            <span class="text-secondary fw-semibold">Company Name</span>
                            <div class="fw-normal">{{ $order->company_name ?? '--' }}</div>
                        </div>

                        <div class="col-md-6">
                            <span class="text-secondary fw-semibold">Shipment Date</span>
                            <div class="fw-normal">{{ $order->shipment_date?->format('d.m.Y') ?? '--' }}</div>
                        </div>

                        <!-- Fabric Info -->
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-start border-3 ps-2">Fabric Details</h6>
                        </div>

                        <div class="col-md-12">
                            <span class="text-secondary fw-semibold">Composition</span>
                            <div class="fw-normal">{{ $order->composition ?? '--' }}</div>
                        </div>

                        <div class="col-md-12">
                            <span class="text-secondary fw-semibold">Fabrication</span>
                            <div class="fw-normal">{{ $order->fabrication ?? '--' }}</div>
                        </div>

                        <div class="col-md-6">
                            <span class="text-secondary fw-semibold">GSM</span>
                            <div class="fw-normal">{{ $order->gsm ?? '--' }}</div>
                        </div>

                        <!-- Order Summary -->
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-start border-3 ps-2">Order Summary</h6>
                        </div>

                        <div class="col-md-6">
                            <span class="text-secondary fw-semibold">Total Qty</span>
                            <div class="fw-normal">{{ $order->total_qty ?? '--' }}</div>
                        </div>

                        <div class="col-md-6">
                            <span class="text-secondary fw-semibold">Total Bill</span>
                            <div class="fw-normal">{{ $order->total_bill ?? '--' }}</div>
                        </div>

                        <!-- Additional Info -->
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-start border-3 ps-2">Additional Information</h6>
                        </div>

                        <div class="col-md-12">
                            <span class="text-secondary fw-semibold">Remarks</span>
                            <div class="fw-normal">{{ $order->remarks ?? '--' }}</div>
                        </div>

                        <div class="col-md-6">
                            <span class="text-secondary fw-semibold">Create Date</span>
                            <div class="fw-normal">{{ $order->created_at->format('d.m.Y') }}</div>
                        </div>

                        <div class="col-md-6">
                            <span class="text-secondary fw-semibold">Status</span>
                            <div>
                                <span class="badge
                                    @if($order->status=='temp') bg-secondary
                                    @elseif($order->status=='pending') bg-warning text-dark
                                    @elseif($order->status=='confirmed') bg-info text-dark
                                    @elseif($order->status=='completed') bg-success
                                    @elseif($order->status=='cancelled') bg-danger
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>

                    </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach

@push('js')
@endpush
