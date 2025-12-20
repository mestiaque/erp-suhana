@extends(adminTheme().'layouts.app')

@section('title')
    <title>{{ websiteTitle('Order Details List') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Order Details List</h3>
            <div class="d-flex gap-1">

                <form action="{{ route('admin.orderDetails') }}" method="GET" target="_blank" class="d-inline">
                    <input type="hidden" name="print" value="true">
                    <input type="hidden" name="startDate" value="{{ request()->startDate }}">
                    <input type="hidden" name="endDate" value="{{ request()->endDate }}">
                    <input type="hidden" name="search" value="{{ request()->search }}">
                    <button type="submit" class="btn btn-info btn-sm">
                        <i class="fa fa-print"></i> Print
                    </button>
                </form>

                @can('order_details.add')
                    <a href="{{ route('admin.orderDetailsAction','create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus"></i> Add Order Details
                    </a>
                @endcan

                <a href="{{ route('admin.orderDetails') }}" class="btn btn-warning btn-sm">
                    <i class="bx bx-rotate-left"></i> Reset
                </a>

            </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search & Date Filter -->
            <form action="{{ route('admin.orderDetails') }}" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-6 d-flex">
                        <input type="date" name="startDate" value="{{ request()->startDate }}" class="form-control me-1">
                        <input type="date" name="endDate" value="{{ request()->endDate }}" class="form-control">
                    </div>
                    <div class="col-md-6 d-flex">
                        <input type="text" name="search" value="{{ request()->search ?? '' }}" class="form-control me-1"
                               placeholder="Search Order, Buyer, Style, Merchant, Invoice, Order, Composition, Fabrication, GSM">
                        <button type="submit" class="btn btn-success btn-sm">Search</button>
                    </div>
                </div>
            </form>

            <div class="row mb-0">
                <div class="col-md-12">
                    <ul class="statuslist p-0 mb-0">
                        <li class=""><a class=" {{ !request('status') ? 'active' : '' }}" href="{{ route('admin.orderDetails') }}">All ({{ $totals->total }})</a></li>
                        <li class=""><a class=" {{ request('status')=='pending' ? 'active' : '' }}" href="{{ route('admin.orderDetails',['status'=>'pending']) }}">Pending ({{ $totals->pending }})</a></li>
                        <li class=""><a class=" {{ request('status')=='confirmed' ? 'active' : '' }}" href="{{ route('admin.orderDetails',['status'=>'confirmed']) }}">Confirmed ({{ $totals->confirmed }})</a></li>
                        <li class=""><a class=" {{ request('status')=='completed' ? 'active' : '' }}" href="{{ route('admin.orderDetails',['status'=>'completed']) }}">Completed ({{ $totals->completed }})</a></li>
                        <li class=""><a class=" {{ request('status')=='canceled' ? 'active' : '' }}" href="{{ route('admin.orderDetails',['status'=>'canceled']) }}">Cancelled ({{ $totals->canceled ?? 0 }})</a></li>
                    </ul>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>SL</th>
                            <th>Buyer</th>
                            <th>Brand / Customer</th>
                            <th>Style No</th>
                            <th>Order / PO No</th>
                            <th>Order Qty</th>
                            <th>Shipment Date</th>
                            <th>Fabrication</th>
                            <th>GSM</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-hover">
                        @forelse($orderDetails as $i => $order)
                        <tr data-bs-toggle="collapse" data-bs-target="#items_{{ $order->id }}" class="accordion-toggle">
                            <td>{{ $orderDetails->firstItem() + $i }}</td>
                            <td>{{ $order->buyer_name ?? '--' }}</td>
                            <td>{{ $order->company_name ?? '--' }}</td>
                            <td>{{ $order->style_no ?? '--' }}</td>
                            <td>{{ $order->order_no ?? '--' }}</td>
                            <td>{{ number_format($order->total_qty,2) }}</td>
                            <td>{{ $order->shipment_date?->format('d.m.Y') ?? '--' }}</td>
                            <td>{{ $order->fabrication ?? '--' }}</td>
                            <td>{{ $order->gsm ?? '--' }}</td>
                            <td>{{ $order->remarks ?? '--' }}</td>
                            <td>
                                @php
                                    $statusClass = [
                                        'temp'=>'secondary',
                                        'pending'=>'warning',
                                        'confirmed'=>'info',
                                        'completed'=>'success',
                                        'canceled'=>'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusClass[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>
                                @can('order_details.view')
                                    <a href="javascript:void(0)" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#viewModal_{{ $order->id }}"><i class="fa fa-eye"></i></a>
                                @endcan
                                @if(in_array($order->status,['pending','confirmed']))
                                    @can('order_details.edit')
                                        <a href="{{ route('admin.orderDetailsAction',['edit',$order->id]) }}" class="btn btn-sm btn-success"><i class="bx bx-edit"></i></a>
                                    @endcan
                                    @can('order_details.delete')
                                        <a href="{{ route('admin.orderDetailsAction',['delete',$order->id]) }}" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></a>
                                    @endcan
                                @endif
                            </td>
                        </tr>

                        {{-- Collapsible Items --}}
                        <tr class="collapse" id="items_{{ $order->id }}">
                            <td colspan="12" class="p-0">
                                <table class="table table-sm mb-0">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>SL</th>
                                            <th>Composition</th>
                                            <th>Color</th>
                                            <th>Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($order->items as $ii=>$item)
                                        <tr>
                                            <td>{{ $ii+1 }}</td>
                                            <td>{{ $item->composition ?? $order->composition ?? '--' }}</td>
                                            <td>{{ $item->color_name ?? '--' }}</td>
                                            <td>{{ number_format($item->qty,2) }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted">No items found</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                        @empty
                        <tr><td colspan="12" class="text-center text-muted">No order details found</td></tr>
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
    .accordion-toggle:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
</style>
@endpush

@push('js')
<script>
    $(document).on('click', '.no-collapse', function(e){
        e.stopPropagation();
    });
</script>
@endpush
