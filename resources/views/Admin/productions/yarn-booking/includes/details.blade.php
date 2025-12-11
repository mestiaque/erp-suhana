@foreach($list as $booking)
<div class="modal fade" id="viewModal_{{ $booking->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header text-white py-2">
                <h5 class="modal-title">Yarn Booking Details # ({{ $booking->getBookingNo() }})</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- Booking Info -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Buyer Name:</strong>
                        <p>{{ $booking->buyer?->name ?? '--' }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>PI No:</strong>
                        <p>{{ $booking->pi_no ?? '--' }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Status:</strong><br>
                        @if($booking->status=='pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($booking->status=='confirmed')
                            <span class="badge badge-info">Confirmed</span>
                        @elseif($booking->status=='approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($booking->status=='cancel')
                            <span class="badge badge-danger">Cancelled</span>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <strong>Booking Date:</strong>
                        <p>{{ $booking->created_at ? \Carbon\Carbon::parse($booking->created_at)->format('d.m.Y') : '--' }}</p>
                    </div>

                    <div class="col-md-4">
                        <strong>Total Items:</strong>
                        <p>{{ $booking->items->count() }}</p>
                    </div>

                    <div class="col-md-4">
                        <strong>Total Yarn Qty:</strong>
                        <p>{{ number_format($booking->items->sum('requisition_qty'), 2) }}</p>
                    </div>

                    <div class="col-md-12">
                        <strong>Remarks:</strong>
                        <p>{{ $booking->remarks ?? '--' }}</p>
                    </div>
                </div>

                <hr>

                <!-- Booking Items Table -->
                <h5 class="mb-2">Yarn Booking Items</h5>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Fabrication</th>
                                <th>Yarn Count</th>
                                <th>Yarn Req. Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->items as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $item->fabrication ?? '--' }}</td>
                                    <td>{{ $item->yarn_count ?? '--' }}</td>
                                    <td>{{ number_format($item->requisition_qty, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No Yarn Items Found</td>
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
