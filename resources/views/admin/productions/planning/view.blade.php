@foreach($masterPlans as $plan)
<div class="modal fade" id="masterPlanModal_{{ $plan->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title text-white">
                    Master Plan Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <!-- Summary -->
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Created By</small><br>
                                <strong>{{ $plan->creator?->name ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Status</small><br>
                                <strong>{{ ucfirst($plan->status) }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Created At</small><br>
                                <strong>{{ $plan->created_at->format('d.m.Y') }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Total Styles</small><br>
                                <strong>{{ $plan->productions->count() }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-secondary text-white text-center">
                            <tr>
                                <th>SL</th>
                                <th>Style</th>
                                <th>Buyer</th>
                                <th>Order No</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalQty = 0; @endphp
                            @foreach($plan->productions as $i => $p)
                                @php
                                    $qty = $p->style?->total_qty ?? 0;
                                    $totalQty += $qty;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $i+1 }}</td>
                                    <td>{{ $p->style_no }}</td>
                                    <td>{{ $p->orderDetailItems->buyer_name ?? '--' }}</td>
                                    <td>{{ $p->order_no }}</td>
                                    <td class="text-right">{{ number_format($qty) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="font-weight-bold bg-light">
                            <tr>
                                <td colspan="4" class="text-right">Total</td>
                                <td class="text-right">{{ number_format($totalQty) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
@endforeach
