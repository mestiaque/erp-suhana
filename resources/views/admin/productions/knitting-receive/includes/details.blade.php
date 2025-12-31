@foreach($receives as $row)
<div class="modal fade" id="viewModal_{{ $row->receive_no }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header bg-success text-white py-2">
                <h5 class="modal-title">Knitting Receive Details #{{ $row->getRecvNo() }}</h5>
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
                                <small class="text-muted d-block">Buyer Name</small>
                                <strong>{{ $row->pi->buyer_name ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">PI Number</small>
                                <strong>{{ $row->pi->pi_no ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Chalan / Ref No</small>
                                <strong>{{ $row->chalan_no ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Receive Date</small>
                                <strong>{{ \Carbon\Carbon::parse($row->receive_date)->format('d M, Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Received Fabric Items Table -->
                <h6 class="mb-3 font-weight-bold text-success"><i class="bx bx-package"></i> Received Fabric Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-secondary text-white text-center">
                            <tr>
                                <th width="5%">SL</th>
                                <th width="15%">Style No</th>
                                <th width="30%">Fabrication</th>
                                <th width="10%">Dia</th>
                                <th width="10%">Total Rolls</th>
                                <th width="15%">Receive Qnty (KG)</th>
                                <th width="15%">Booking Ref</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // ওই নির্দিষ্ট রিসিভ নম্বরের সব আইটেম ডাটাবেস থেকে আনা
                                $receiveItems = \App\Models\KnittingReceive::where('receive_no', $row->receive_no)->get();
                                $totalWeight = 0;
                                $totalRolls = 0;
                            @endphp
                            @forelse($receiveItems as $index => $item)
                                @php
                                    $totalWeight += $item->weight;
                                    $totalRolls += $item->roll_qty;

                                    // সংশ্লিষ্ট নিটিং বুকিং রো থেকে ফ্যাব্রিক ডাটা নেওয়া
                                    $knitRow = \App\Models\KnittingBooking::find($item->knit_id);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center"><b>{{ $knitRow->style ?? '--' }}</b></td>
                                    <td>{{ $knitRow->fabric_type ?? '--' }}</td>
                                    <td class="text-center"><span class="badge badge-light border">{{ $knitRow->dia ?? 'N/A' }}</span></td>
                                    <td class="text-center font-weight-bold">{{ $item->roll_qty }}</td>
                                    <td class="text-right font-weight-bold text-success">{{ number_format($item->weight, 2) }} KG</td>
                                    <td class="text-center text-muted small">{{ $item->getKBookingNo() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No items found in this receive record.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="4" class="text-right">Total Summary:</td>
                                <td class="text-center text-primary">{{ $totalRolls }} Rolls</td>
                                <td class="text-right text-success" style="font-size: 16px;">{{ number_format($totalWeight, 2) }} KG</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                {{-- <button type="button" class="btn btn-success" onclick="window.print()"><i class="bx bx-printer"></i> Print</button> --}}
            </div>

        </div>
    </div>
</div>
@endforeach
