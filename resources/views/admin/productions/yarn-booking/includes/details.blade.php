@foreach($bookings as $booking)
<div class="modal fade" id="viewModal_{{ $booking->booking_no }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title">Yarn Booking Details #{{ $booking->booking_no }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Summary Info Card -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">PI Number</small>
                                <strong>{{ $booking->pi->pi_no ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Buyer Name</small>
                                <strong>{{ $booking->pi->buyer_name ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Supplier</small>
                                <strong>{{ $booking->supplier ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Booking Date</small>
                                <strong>{{ \Carbon\Carbon::parse($booking->created_at)->format('d.m.Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Items Table -->
                <h5 class="mb-3"><i class="bx bx-list-ul"></i> Yarn Booking Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th width="5%" class="text-center">SL</th>
                                <th width="15%">Style</th>
                                <th width="25%">Fabrication</th>
                                <th width="40%">Yarn Count & Target Qty</th>
                                <th width="15%" class="text-right">Total Req. Qnty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // বুকিং নম্বর ধরে সব আইটেম আনা
                                $booking_items = \App\Models\YarnBooking::where('booking_no', $booking->booking_no)->get();
                                $grandTotal = 0;
                            @endphp
                            @forelse($booking_items as $i => $item)
                                @php $grandTotal += $item->required_qty; @endphp
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $item->style ?? '--' }}</td>
                                    <td>{{ $item->fabric_type ?? '--' }}</td>
                                    <td class="p-0">
                                        {{-- ইয়ার্ন কাউন্ট ও Qty কে সাব-টেবিল হিসেবে দেখানো --}}
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                            @foreach (json_decode($item->yarn_count, true) as $yarn)
                                                <tr class="border-bottom">
                                                    <td class="pl-3" width="50%">
                                                        <span class="badge badge-info" style="font-size: 12px;">{{ $yarn['count'] ?? '--' }}</span>
                                                    </td>
                                                    <td class="text-right pr-3 text-info" width="50%">
                                                        {{ number_format($yarn['qty'], 2) }} KG
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {{ number_format($item->required_qty, 2) }} KG
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No Items Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="4" class="text-right">Grand Total:</td>
                                <td class="text-right text-primary">{{ number_format($grandTotal, 2) }} KG</td>
                            </tr>
                        </tfoot>
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
