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
                    <button type="submit" class="btn btn-info btn-sm mr-1">
                        <i class="fa fa-print"></i> Print
                    </button>
                </form>

                @can('order_details.add')
                    <a href="{{ route('admin.orderDetailsAction','create') }}" class="btn btn-primary btn-sm mr-1">
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
                               placeholder="Search Order, Buyer, Style, Merchant, Invoice, Order, Composition, Fabrication">
                        <button type="submit" class="btn btn-success btn-sm">Search</button>
                    </div>
                </div>
            </form>

            <div class="row g-3"> <!-- g-3 adds gutter between cards -->

                <div class="col-12 col-md-2">
                    <div class="custom-card" style="">
                        <div style="font-size:30px;">👕</div>
                        <div style="font-weight:bold;">Total</div>
                        <div class="text-success">Qty: {{ number_format($totalOrderQty) }}</div>
                        <div class="text-danger">Balance: 0</div>
                    </div>
                </div>

                <div class="col-12 col-md-2">
                    <div class="custom-card" style="">
                        <div style="font-size:30px;">✂️</div>
                        <div style="font-weight:bold;">Cutting</div>
                        <div class="text-success">Qty: {{ number_format($grandTotalCuttingOutput) }}</div>
                        <div class="text-danger">Balance: {{ number_format($totalOrderQty - $grandTotalCuttingOutput) }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-2">
                    <div class="custom-card" style="">
                        <div style="font-size:30px;">🧵</div>
                        <div style="font-weight:bold;">Sewing Output</div>
                        <div class="text-success">Qty: {{ number_format($grandTotalSewingOutput) }}</div>
                        <div class="text-danger">Balance: {{ number_format($totalOrderQty - $grandTotalSewingOutput) }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-2">
                    <div class="custom-card" style="">
                        <div style="font-size:30px;">📦</div>
                        <div style="font-weight:bold;">Packing</div>
                        <div class="text-success">Qty: 0</div>
                        <div class="text-danger">Balance: 0</div>
                    </div>
                </div>

                <div class="col-12 col-md-2">
                    <div class="custom-card" style="">
                        <div style="font-size:30px;">🚚</div>
                        <div style="font-weight:bold;">Shipped</div>
                        <div class="text-success">Qty: 0</div>
                        <div class="text-danger">Balance: 0</div>
                    </div>
                </div>

                <div class="col-12 col-md-2">
                    <div class="custom-card" style="">
                        <div style="font-size:30px;">🎨</div>
                        <div style="font-weight:bold;">Print & Embroidery</div>
                        <div class="text-success">Qty: 0</div>
                        <div class="text-danger">Balance: 0</div>
                    </div>
                </div>

            </div>

            <div class="row mb-0">
                <div class="col-md-12">
                    <ul class="statuslist p-0 mb-0">
                        <li class=""><a class=" {{ !request('status') ? 'active' : '' }}" href="{{ route('admin.orderDetails') }}">All ({{ $totals->total }})</a></li>
                        <li class=""><a class=" {{ request('status')=='pending' ? 'active' : '' }}" href="{{ route('admin.orderDetails',['status'=>'pending']) }}">Pending ({{ $totals->pending }})</a></li>
                        <li class=""><a class=" {{ request('status')=='confirmed' ? 'active' : '' }}" href="{{ route('admin.orderDetails',['status'=>'confirmed']) }}">Confirmed ({{ $totals->confirmed }})</a></li>
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
                            <th style="min-width: 160px">Brand / Customer</th>
                            <th>Style No</th>
                            <th style="width: 130px;min-width: 130px">Order / PO No</th>
                            <th style="width: 130px;min-width: 130px">Order Qty</th>
                            <th style="width: 140px;min-width: 140px">Shipment Date</th>
                            <th>Fabrication</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th style="width: 140px;min-width: 140px">Action</th>
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
                            <td>{{ number_format($order->total_qty) }}</td>
                            <td>{{ $order->shipment_date?->format('d.m.Y') ?? '--' }}</td>
                            <td>{{ $order->fabrication ?? '--' }}</td>
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
                                {{-- @if(in_array($order->status,['pending','confirmed'])) --}}
                                    @can('order_details.edit')
                                        <a href="{{ route('admin.orderDetailsAction',['edit',$order->id]) }}" class="btn btn-sm btn-success"><i class="bx bx-edit"></i></a>
                                    @endcan
                                    @can('order_details.delete')
                                        <a href="{{ route('admin.orderDetailsAction',['delete',$order->id]) }}" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></a>
                                    @endcan
                                {{-- @endif --}}
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
    .custom-card {
    background-color: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease-in-out; /* অ্যানিমেশন স্মুথ করবে */
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* হোভার ইফেক্ট */
.custom-card:hover {
    transform: translateY(-8px); /* কার্ডটি উপরে উঠবে */
    box-shadow: 0 12px 20px rgba(0,0,0,0.1); /* শ্যাডো বাড়বে */
    border-color: #007bff; /* বর্ডারের রঙ নীল হবে */
}

/* আইকন হোভার করলে বড় হবে */
.custom-card:hover .card-icon {
    transform: scale(1.2);
    transition: transform 0.3s ease;
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
