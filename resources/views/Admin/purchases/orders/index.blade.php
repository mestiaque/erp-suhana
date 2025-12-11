@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Purchase Order List') }}</title>
@endsection

@push('css')
<style type="text/css">

table.table a {
    color: #000;
}
.badge-warning {
    color: #000;
    background-color: #d9a50c4d;
}
.badge-success {
    color: #035415;
    background-color: #17e64642;
}
    
</style>
@endpush

@section('contents')

<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Purchase Order List</h3>
             <div class="dropdown">
                @can('purchases_orders.add')
                 <a href="{{ route('admin.purchasesOrdersAction','create') }}" class="btn-custom primary" style="padding:5px 15px;">
                     <i class="bx bx-plus"></i> Add Purchase Order
                 </a>
                 @endcan
                 <a href="{{ route('admin.purchasesOrders') }}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{ route('admin.purchasesOrders') }}">
                <div class="row">
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate ? Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') : '' }}" class="form-control" />
                            <input type="date" name="endDate" value="{{ request()->endDate ? Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') : '' }}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search Order No, Company" class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>
            <br>

            <!-- Bulk Actions -->
            <form action="{{ route('admin.purchasesOrders') }}">
                <div class="row">
                    <div class="col-md-4">
                        <!-- <div class="input-group mb-1">
                            <select class="form-control form-control-sm rounded-0" name="action" required>
                                <option value="">Select Action</option>
                                <option value="1">Pending</option>
                                <option value="2">Approved</option>
                                <option value="3">Rejected</option>
                                <option value="4">Delete</option>
                            </select>
                            <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Sure?')">Apply</button>
                        </div> -->
                    </div>
                    <div class="col-md-8">
                        <ul class="statuslist p-0">
                            <li><a href="{{ route('admin.purchasesOrders') }}">All ({{ $totals->total }})</a></li>
                            <li><a href="{{ route('admin.purchasesOrders',['status'=>'pending']) }}">Pending ({{ $totals->pending }})</a></li>
                            <li><a href="{{ route('admin.purchasesOrders',['status'=>'approved']) }}">Approved ({{ $totals->approved }})</a></li>
                            <li><a href="{{ route('admin.purchasesOrders',['status'=>'rejected']) }}">Rejected ({{ $totals->rejected }})</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="min-width: 50px;">SL</th>
                                <th style="min-width: 150px;">Order No</th>
                                <th style="min-width: 150px;">Supplier</th>
                                <th style="min-width: 200px;">Created By</th>
                                <th style="min-width: 150px;">Items</th>
                                <th style="min-width: 150px;">Bill Amount</th>
                                <th style="min-width: 150px;">Due Amount</th>
                                <th style="min-width: 150px;">Paid Amount</th>
                                <th style="min-width: 100px;">Status</th>
                                <th style="min-width: 100px;">Date</th>
                                <th style="min-width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $i => $order)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.purchasesOrdersAction',['view',$order->id]) }}" target="_blank">{{ $order->order_no }}</a>
                                </td>
                                <td>
                                    @if($order->supplier)
                                    <a href="{{route('admin.suppliersAction',['view',$order->supplier->id])}}" target="_blank" class="invoice-action-view mr-1">
                                    {{ $order->supplier?$order->supplier->name : '--' }}
                                    </a>
                                @else
                                N/A
                                @endif

                                </td>
                                <td>{{ $order->user?->name }}</td>
                                <td>{{ $order->items()->count() }} Items</td>
                                <td>{{ numberFormat($order->grand_total,3,$order->currency) }}</td>
                                <td class="text-danger">{{ numberFormat($order->due_amount,3,$order->currency) }}</td>
                                <td >{{ numberFormat($order->paid_amount,3,$order->currency) }}</td>
                                <td>
                                    @if($order->status=='temp')
                                        <span class="badge badge-secondary">Temp</span>
                                    @elseif($order->status=='pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($order->status=='approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($order->status=='rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('d.m.Y') }}</td>
                                <td>
                                     @if(can('purchases_orders.edit')  || can('purchases_orders.delete'))
                                     @can('purchases_orders.edit')
                                     <a href="{{ route('admin.purchasesOrdersAction',['edit',$order->id]) }}" class="btn-custom success"><i class="bx bx-edit"></i></a>
                                    @endcan
                                    @can('purchases_orders.delete')
                                    <a href="{{ route('admin.purchasesOrdersAction',['delete',$order->id]) }}" onclick="return confirm('Are You Sure To Delete?')" class="btn-custom danger"><i class="bx bx-trash"></i></a>
                                    @endcan
                                    @else -- @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $orders->links('pagination') }}
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
@endpush
