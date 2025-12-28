
@foreach($receives as $row)
<div class="modal fade" id="viewModal_{{ $row->receive_no }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header bg-success text-white py-2">
                <h5 class="modal-title">Yarn Receive Details #{{ $row->getRecvNo() }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Summary Info Card (Same as Booking UI) -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Receive No</small>
                                <strong>{{ $row->getRecvNo() }}</strong>
                            </div>
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Booking No</small>
                                <strong class="text-primary">{{ $row->getBookingNo() }}</strong>
                            </div>
                            <div class="col-md-3 border-right">
                                <small class="text-muted d-block">Supplier</small>
                                <strong>{{ $row->supplier ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Receive Date</small>
                                <strong>{{ \Carbon\Carbon::parse($row->receive_date)->format('d.m.Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Received Items Table -->
                <h5 class="mb-3"><i class="bx bx-package"></i> Received Yarn Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="bg-success text-white">
                            <tr>
                                <th width="5%" class="text-center">SL</th>
                                <th width="15%">Style</th>
                                <th width="25%">Fabrication</th>
                                <th width="40%">Yarn Count & Received Qty</th>
                                <th width="15%" class="text-right">Total Recv. Qnty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // N+1 Query এড়াতে রিলেশন সহ ডেটা আনা
                                $receive_details = \App\Models\YarnReceive::with('bookingRow')
                                                    ->where('receive_no', $row->receive_no)
                                                    ->get();

                                $grandTotalRecv = 0;

                                // ডেটাগুলোকে Style এবং Fabrication অনুযায়ী গ্রুপ করা হচ্ছে
                                $grouped_details = $receive_details->groupBy(function ($item) {
                                    return $item->bookingRow->style . '|' . $item->bookingRow->fabric_type;
                                });
                            @endphp

                            @forelse($grouped_details as $group_key => $items_in_group)
                                @php
                                    list($style, $fabric) = explode('|', $group_key);
                                    $totalQtyPerStyle = $items_in_group->sum('receive_qty');
                                    $grandTotalRecv += $totalQtyPerStyle;
                                @endphp
                                <tr>
                                    <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                    <td class="align-middle">
                                        {{ $style ?? '--' }}
                                    </td>
                                    <td class="align-middle text-muted">
                                        {{ $fabric ?? '--' }}
                                    </td>

                                    {{-- বুকিং UI-এর মতো সাব-টেবিল --}}
                                    <td class="p-0">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                            @foreach ($items_in_group as $detail)
                                                <tr class="{{ !$loop->last ? 'border-bottom' : '' }}">
                                                    <td class="pl-3 py-1" width="50%">
                                                        <span class="badge badge-info" style="font-size: 12px;">{{ $detail->yarn_count }}</span>
                                                    </td>
                                                    <td class="text-right pr-3 py-1 text-success" width="50%">
                                                        {{ number_format($detail->receive_qty, 2) }} KG
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </td>

                                    <td class="text-right align-middle font-weight-bold">
                                        {{ number_format($totalQtyPerStyle, 2) }} KG
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
                                <td class="text-right text-success" style="font-size: 1.1rem;">
                                    {{ number_format($grandTotalRecv, 2) }} KG
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                {{-- <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bx bx-printer"></i> Print</button> --}}
            </div>

        </div>
    </div>
</div>
@endforeach

