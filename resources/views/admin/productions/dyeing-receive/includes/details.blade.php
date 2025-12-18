@foreach($bookings as $booking)
<div class="modal fade" id="viewModal_{{ $booking->booking_no }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg- text- py-2">
                <h5 class="modal-title">Dyeing Booking Details #{{ $booking->getBookingNo() }}</h5>
                <button type="button" class="close text-" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- Booking Info -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>PI Number:</strong>
                        <p>{{ $booking->pi->pi_no?? '--' }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Buyer Name:</strong>
                        <p>{{ $booking->pi->buyer_name ?? '--' }}</p>
                    </div>
                    {{-- <div class="col-md-4">
                        <strong>Supplier:</strong>
                        <p>{{ $booking->supplier ?? '--' }}</p>
                    </div> --}}
                    <div class="col-md-4">
                        <strong>Booking Date:</strong>
                        <p>{{ \Carbon\Carbon::parse($row->created_at)->format('d.m.Y') }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Status:</strong>
                        <p>{{ ucfirst($booking->status ?? '--') }}</p>
                    </div>
                    {{-- <div class="col-md-4">
                        <strong>Expected Delivery:</strong>
                        <p>{{ $booking->expected_delivery ? \Carbon\Carbon::parse($booking->expected_delivery)->format('d.m.Y') : '--' }}</p>
                    </div>
                    <div class="col-12">
                        <strong>Remarks:</strong>
                        <p>{{ $booking->remarks ?? '--' }}</p>
                    </div> --}}
                </div>

                <hr>

                <!-- Booking Items Table -->
                <h5 class="mb-2">Dyeing Booking Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Style</th>
                                <th>Fabrication</th>
                                <th>Composition</th>
                                <th>Req. Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $booking_items = \App\Models\DyeingBooking::where('booking_no', $booking->booking_no)->get();
                            @endphp
                            @forelse($booking_items as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $item->style ?? '--' }}</td>
                                    <td>{{ $item->fabric_type ?? '--' }}</td>
                                    <td>{{ $item->composition ?? '--' }}</td>
                                    <td>{{ number_format($item->required_qty, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No Items Found</td>
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
