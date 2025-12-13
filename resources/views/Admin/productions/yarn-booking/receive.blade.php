@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Yarn Booking Edit') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">

    {{-- Breadcrumb --}}
    <div class="breadcrumb-area">
        <h1>Yarn Delivery</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="bx bx-home-alt"></i>
                </a>
            </li>
            <li class="item">
                <a href="{{ route('admin.yarnBooking') }}">Yarn Bookings</a>
            </li>
            <li class="item">Delivery</li>
        </ol>
    </div>

    @include(adminTheme().'alerts')

    {{-- ================= BASIC INFO ================= --}}
    <div class="card mb-30">
        <div class="card-body">
            <h5 class="mb-3"><b>Basic Information</b></h5>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label>PI Number</label>
                    <input type="text" class="form-control" readonly
                           value="{{ $booking->getBookingNo() }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Buyer Name</label>
                    <input type="text" class="form-control" readonly
                           value="{{ $booking->buyer?->name }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label>Booking Date</label>
                    <input type="text" class="form-control" readonly
                           value="{{ $booking->created_at?->format('d.m.Y') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label>Total Req. Qty</label>
                    <input type="text" class="form-control" readonly
                           value="{{ number_format($booking->items->sum('requisition_qty'), 2) }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label>Total Del. Qty</label>
                    <input type="text" class="form-control" readonly
                           value="{{ number_format($booking->items->sum('received_qty'), 2) }}">
                </div>
            </div>
        </div>
    </div>

    {{-- ================= YARN RECEIVED HISTORY ================= --}}
    <div class="card mb-30">
        <div class="card-body">
            <h5 class="mb-3"><b>Yarn Delivered History</b></h5>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th width="40">SL</th>
                            <th>Date</th>
                            <th>Fabrication</th>
                            <th>Yarn Count</th>
                            <th>Received Qty</th>
                            <th width="80">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($booking->receiveItems as $i => $history)
                            <tr>
                                <td class="text-center">{{ $i+1 }}</td>
                                <td>{{ $history->created_at->format('d.m.Y') }}</td>
                                <td>{{ $history->fabrication }}</td>
                                <td>{{ $history->yarn_count }}</td>
                                <td>{{ numberFormat($history->delivery_qty, 2) }}</td>
                                <td class="text-center">
                                    <button type="button"
                                            class="btn-custom btn-sm success"
                                            data-toggle="modal"
                                            data-target="#editHistoryModal{{ $history->id }}">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- ========= EDIT MODAL (LOOP) ========= --}}
                            <div class="modal fade" id="editHistoryModal{{ $history->id }}">
                                <div class="modal-dialog">
                                    <form method="POST"
                                          action="{{ route('admin.yarnBookingAction', ['delivery-update', $history->id]) }}">
                                        @csrf

                                        <input type="hidden" name="id" value="{{ $history->id }}">

                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5>Edit Yarn Received</h5>
                                                <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="mb-2">
                                                    <label>Date</label>
                                                    <input type="created_at"
                                                           name="created_at"
                                                           class="form-control"
                                                           value="{{ $history->created_at->format('Y-m-d') }}">
                                                </div>

                                                <div class="mb-2">
                                                    <label>Fabrication</label>
                                                    <input type="text" readonly
                                                           name="fabrication"
                                                           class="form-control"
                                                           value="{{ $history->fabrication }}">
                                                </div>

                                                <div class="mb-2">
                                                    <label>Yarn Count</label>
                                                    <input type="text" readonly
                                                           name="yarn_count"
                                                           class="form-control"
                                                           value="{{ $history->yarn_count }}">
                                                </div>

                                                <div class="mb-2">
                                                    <label>Received Qty</label>
                                                    <input type="number" required
                                                           step="0.01"
                                                           name="qty"
                                                           class="form-control"
                                                           value="{{ $history->delivery_qty }}">
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit"
                                                        class="btn btn-success">
                                                    Update
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="text-center text-muted py-3">
                                    No Yarn Received History Found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= YARN BOOKING ITEMS ================= --}}
    <form action="{{ route('admin.yarnBookingAction', ['delivery-add', $booking->id]) }}"
          method="POST">
        @csrf

        <div class="card mb-30">
            <div class="card-body">
                <h5 class="mb-3"><b>Yarn Delivery Items</b></h5>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th width="40">SL</th>
                                <th>Fabrication</th>
                                <th>Yarn Count</th>
                                <th>Req. Qty</th>
                                <th>Del. Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->items as $i => $item)
                                <tr>
                                    <td class="text-center">{{ $i+1 }}</td>

                                    <td>
                                        <input type="hidden"
                                               name="items[{{ $i }}][id]"
                                               value="{{ $item->id }}">
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               readonly
                                               value="{{ $item->fabrication }}">
                                    </td>

                                    <td>
                                        <input type="text" class="form-control form-control-sm" id="" value="{{  $item->yarn_count }}" readonly>
                                    </td>

                                    <td>
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               value="{{ numberFormat($item->requisition_qty, 2) }}"
                                               readonly>
                                    </td>
                                    <td>
                                        <input type="number" placeholder="Delivery Qty"
                                               step="0.01" max="{{ $item->requisition_qty }}"
                                               name="items[{{ $i }}][delivery_qty]"
                                               class="form-control form-control-sm"
                                               required>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="text-center text-muted">
                                        No Yarn Items Found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-success mt-3">
                    <i class="bx bx-check"></i> Received Yarn
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
