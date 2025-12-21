
    @foreach($orderDetails as $order)
    <div class="modal fade" id="viewModal_{{ $order->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header text-white py-2">
                    <h5 class="modal-title">Order Details # ({{ $order->order_no }})</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <!-- Order Info -->

                    <div class="mb-3 p-3 border rounded bg-light">
                        <div class="row">

                            <div class="col-md-4 mb-2">
                                <strong>Buyer Name</strong>
                                <div>{{ $order->buyer_name ?? '--' }}</div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <strong>Brand / Customer</strong>
                                <div>{{ $order->company_name ?? '--' }}</div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong>Merchandiser</strong>
                                <div>{{ $order->merchant_name ?? '--' }}</div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <strong>Style No</strong>
                                <div>{{ $order->style_no ?? '--' }}</div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <strong>Order / PO No</strong>
                                <div>{{ $order->order_no ?? '--' }}</div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <strong>Total Order Qnty</strong>
                                <div>{{ number_format($order->total_qty ?? 0) }}</div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <strong>Shipment Date</strong>
                                <div>
                                    {{ $order->shipment_date
                                        ? \Carbon\Carbon::parse($order->shipment_date)->format('d M Y')
                                        : '--'
                                    }}
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <strong>Fabrication</strong>
                                <div>{{ $order->fabrication ?? '--' }}</div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong>Composition</strong>
                                <div>{{ $order->composition ?? '--' }}</div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <strong>GSM</strong>
                                <div>{{ $order->gsm ?? '--' }}</div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <strong>Status</strong><br>
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
                                ({{$order->createdBy?->name}})
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong>Created Date</strong><br>
                                {{ $order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('d.m.Y h:i A') : '--' }}
                            </div>

                            <div class="col-md-12 mt-2">
                                <strong>Remarks</strong>
                                <div class="border rounded p-2 bg-white">
                                    {{ $order->remarks ?? '--' }}
                                </div>
                            </div>

                        </div>
                    </div>

                    <hr>

                    <!-- Order Items Table -->
                    <h5 class="mb-2">Order Items</h5>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Item Name</th>
                                    <th>Composition</th>
                                    <th>Color Name</th>
                                    <th>Qnty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $ii => $item)
                                    <tr>
                                        <td>{{ $ii + 1 }}</td>
                                        <td>{{ $item->item_name ?? '--' }}</td>
                                        <td>{{ $item->composition ?? $order->composition ?? '--' }}</td>
                                        <td>{{ $item->color_name ?? '--' }}</td>
                                        <td>{{ number_format($item->qty) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    @endforeach
