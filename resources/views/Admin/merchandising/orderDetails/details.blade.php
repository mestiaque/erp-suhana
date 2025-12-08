
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
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Buyer Name:</strong>
                            <p>{{ $order->buyer_name ?? '--' }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Brand / Customer:</strong>
                            <p>{{ $order->company_name ?? '--' }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Style No:</strong>
                            <p>{{ $order->style_no ?? '--' }}</p>
                        </div>

                        <div class="col-md-4">
                            <strong>Order/PO No:</strong>
                            <p>{{ $order->order_no ?? '--' }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Order Qty:</strong>
                            <p>{{ numberFormat($order->total_qty,2) }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Shipment Date:</strong>
                            <p>{{ $order && $order->shipment_date ? \Carbon\Carbon::parse($order->shipment_date)->format('d.m.Y') : '--' }}</p>
                        </div>

                        {{-- <div class="col-md-4">
                            <strong>Composition:</strong>
                            <p>{{ $order->composition ?? '--' }}</p>
                        </div> --}}

                        <div class="col-md-4">
                            <strong>Fabrication:</strong>
                            <p>{{ $order->fabrication ?? '--' }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>GSM:</strong>
                            <p>{{ $order->gsm ?? '--' }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Status:</strong><br>
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
                        </div>

                        <div class="col-md-12">
                            <strong>Remarks:</strong>
                            <p>{{ $order->remarks ?? '--' }}</p>
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
                                    <th>Composition</th>
                                    <th>Color Name</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $ii => $item)
                                    <tr>
                                        <td>{{ $ii + 1 }}</td>
                                        <td>{{ $item->composition ?? $order->composition ?? '--' }}</td>
                                        <td>{{ $item->color_name ?? '--' }}</td>
                                        <td>{{ numberFormat($item->qty,2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No items found</td>
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
