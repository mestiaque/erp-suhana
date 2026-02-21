


@foreach($bookings as $row)
<div class="modal fade" id="viewModal_{{ $row->booking_no }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title text-white">Knitting Booking Details #{{ $row->getBookingNo() }}</h5>
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
                                <strong>{{ $row->pi->pi_no ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Buyer Name</small>
                                <strong>{{ $row->pi->buyer_name ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Knitting Unit/Factory</small>
                                <strong>{{ $row->knitting_unit ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Booking Date</small>
                                <strong>{{ \Carbon\Carbon::parse($row->created_at)->format('d.m.Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Knitting Items Table -->
                <h5 class="mb-3 font-weight-bold"><i class="bx bx-list-ul"></i> Knitting Booking Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover">
                        <thead class="bg-secondary text-white text-center">
                            <tr>
                                <th width="5%">SL</th>
                                <th width="15%">Style No</th>
                                <th width="25%">Fabrication</th>
                                {{-- <th width="10%">GSM</th> --}}
                                <th width="10%">Dia</th>
                                <th width="15%">Booking Qnty (KG)</th>
                                {{-- <th width="15%">Produced (KG)</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // এই নিটিং বুকিং নম্বরের সব আইটেম রিলেশনসহ লোড করা
                                $knitDetails = \App\Models\KnittingBooking::where('booking_no', $row->booking_no)->get();
                                $totalKnitQty = 0;
                                $totalProdQty = 0;
                            @endphp
                            @forelse($knitDetails as $index => $item)
                                @php
                                    $totalKnitQty += $item->booking_qty;
                                    $totalProdQty += $item->produced_qty;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center"><b>{{ $item->style }}</b></td>
                                    <td>
                                        {{ $item->fabric_type }}
                                        {{-- @php
                                            $composition = \App\Models\OrderDetailItem::where('style_no', $item->style)->first()->composition ?? '';
                                        @endphp
                                        @if($composition)
                                            <br><small class="text-muted">({{ $composition }})</small>
                                        @endif --}}
                                    </td>
                                    {{-- <td class="text-center"><span class="badge badge-light border">{{ $item->gsm ?? 'N/A' }}</span></td> --}}
                                    <td class="text-center"><span class="badge badge-light border">{{ $item->dia ?? 'N/A' }}</span></td>
                                    <td class="text-right font-weight-bold">{{ number_format($item->booking_qty, 2) }}</td>
                                    {{-- <td class="text-right text-success">{{ number_format($item->produced_qty, 2) }}</td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="4" class="text-right">Grand Total:</td>
                                <td class="text-right text-primary">{{ number_format($totalKnitQty, 2) }} KG</td>
                                {{-- <td class="text-right text-success">{{ number_format($totalProdQty, 2) }} KG</td> --}}
                                {{-- <td></td> --}}
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Stitch Length & Additional Info -->
                {{-- <div class="mt-3">
                    <p class="mb-1"><strong>Note:</strong> নিটিং প্রোডাকশন শুরু করার আগে জিএসএম এবং ডায়ামিটার পুনরায় চেক করে নিন।</p>
                </div> --}}
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
@endforeach

