@foreach($bookings as $booking)
<div class="modal fade" id="viewModal_{{ $booking->booking_no }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title">Dyeing Booking Details #{{ $booking->getBookingNo() }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Summary Info Card (আপনার থিম অনুযায়ী) -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body p-3">
                        <div class="row text-center">
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Buyer Name</small>
                                <strong>{{ $booking->pi->buyer_name ?? $booking->buyer_name ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">PI Number</small>
                                <strong>{{ $booking->pi->pi_no ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Booking Date</small>
                                <strong>{{ \Carbon\Carbon::parse($booking->created_at)->format('d.m.Y') }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Status</small>
                                <span class="badge {{ $booking->status == 'pending' ? 'badge-warning' : 'badge-success' }}">
                                    {{ strtoupper($booking->status ?? 'N/A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dyeing Items Table -->
                <h6 class="mb-3 font-weight-bold text-primary">
                    <i class="bx bx-list-ul"></i> Dyeing Specifications & Requirements
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-secondary text-white text-center">
                            <tr>
                                <th width="5%">SL</th>
                                <th width="25%">Style No</th>
                                <th width="25%">Fabrication</th>
                                <th width="25%">Composition</th>
                                <th>Color</th>
                                <th width="15%">Required Qnty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $booking_items = \App\Models\DyeingBooking::where('booking_no', $booking->booking_no)->get();
                                $totalReqQty = 0;
                            @endphp
                            @forelse($booking_items as $index => $item)
                            {{-- @dump($item); --}}
                                @php
                                    $totalReqQty += $item->required_qty;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center"><b>{{ $item->style }}</b></td>
                                    <td>{{ $item->fabric_type ?? '--' }}</td>
                                    <td>{{ $item->composition ?? '--' }}</td>
                                    <td>{{ $item->color ?? '--' }}</td>
                                    <td class="text-right font-weight-bold">{{ number_format($item->required_qty, 2) }} Kgs</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light font-weight-bold text-right">
                            <tr>
                                <td colspan="5">Grand Total:</td>
                                <td class="text-primary text-right">{{ number_format($totalReqQty, 2) }} KG</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($booking->remarks)
                <div class="mt-3">
                    <p class="mb-1"><strong>Note/Remarks:</strong> {{ $booking->remarks }}</p>
                </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
@endforeach
