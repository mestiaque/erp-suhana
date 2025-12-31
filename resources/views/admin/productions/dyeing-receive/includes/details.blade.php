@foreach($bookings as $row)
<div class="modal fade" id="viewModal_{{ $row->receive_no }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title">Dyeing Receive Details #{{ $row->getReceiveNo() }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Summary Info Card (Theme Based) -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body p-3">
                        <div class="row text-center">
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Booking Number</small>
                                <strong>{{ $row->getBookingNo() }}</strong>
                            </div>
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Buyer Name</small>
                                <strong>{{ $row->pi->buyer->name ?? $row->buyer_name ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Challan No</small>
                                <strong>{{ $row->challan_no ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Receive Date</small>
                                <strong>{{ \Carbon\Carbon::parse($row->receive_date)->format('d M, Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Receive Items Table -->
                <h6 class="mb-3 font-weight-bold text-primary">
                    <i class="bx bx-package"></i> Received Fabric Items Summary
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-secondary text-white text-center">
                            <tr>
                                <th width="5%">SL</th>
                                <th width="20%">Style No</th>
                                <th width="20%">Fabrication</th>
                                <th width="20%">Composition</th>
                                <th width="20%">Color</th>
                                <th width="20%">Received Qnty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // এই রিসিভ নম্বরের সব আইটেম লোড করা
                                $receiveItems = \App\Models\DyeingReceive::where('receive_no', $row->receive_no)->get();
                                $totalRcvQty = 0;
                            @endphp
                            @forelse($receiveItems as $index => $item)
                                @php
                                    $totalRcvQty += $item->receive_qty;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center"><b>{{ $item->style }}</b></td>
                                    <td>
                                        {{ $item->fabric_type ?? '--' }}
                                    </td>
                                    <td>
                                        {{ $item->composition ?? '--' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $item->color }}
                                    </td>
                                    <td class="text-right font-weight-bold">{{ number_format($item->receive_qty, 2) }} KG</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No items found in this receive record.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="5" class="text-right">Grand Total Received:</td>
                                <td class="text-right text-success">{{ number_format($totalRcvQty, 2) }} KG</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($row->remarks)
                <div class="mt-3 p-2 bg-light border-left border-primary">
                    <strong>Note/Remarks:</strong> {{ $row->remarks }}
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
