@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Poly List')}}</title>
@endsection
@push('css')
<style type="text/css">
  @media (max-width: 1400px) {
        table tr td {
            font-size: 12px;
        }
        .table thead th {
            font-size: 14px;
        }
  }
</style>
@endpush
@section('contents')

@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Poly List</h3>
            <div class="dropdown">
                <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddPoly">
                    <i class="bx bx-plus"></i> Add Poly
                </a>
                <a href="{{route('admin.poly')}}" class="btn-custom yellow">
                    <i class="bx bx-rotate-left"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.poly') }}">
                <div class="row">
                    <div class="col-md-7 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate ?: '' }}" class="form-control" />
                            <input type="date" name="endDate" value="{{ request()->endDate ?: '' }}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-5 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?: '' }}" placeholder="PI No, Order No, Style No, Color" class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>
            <br>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 50px;">SL</th>
                            <th style="min-width: 120px;">Poly Date</th>
                            <th style="min-width: 150px;">Buyer</th>
                            <th style="min-width: 150px;">Style Number</th>
                            <th style="min-width: 150px;">Order Number</th>
                            <th style="min-width: 120px;">Color</th>
                            <th style="min-width: 100px;">Color Qty</th>
                            <th style="min-width: 100px;">Poly Qty</th>
                            <th style="min-width: 100px;">Total Poly</th>
                            <th style="min-width: 100px;">Balance</th>
                            <th style="min-width: 150px;">Added By</th>
                            <th style="min-width: 100px; width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($polies as $i => $ply)
                        @php
                            $colorQty = \App\Models\OrderDetailItem::where('order_no', $ply->order_no)->where('style_no', $ply->style_no)->where('color_name', $ply->color_name)->sum('qty');
                            $totalPoly = \App\Models\Poly::where('order_no', $ply->order_no)->where('style_no', $ply->style_no)->where('color_name', $ply->color_name)->sum('poly_qty');
                            $balance = $colorQty - $totalPoly;
                        @endphp
                        <tr>
                            <td>{{ $polies->firstItem() + $i }}</td>
                            <td>{{ $ply->poly_date ? $ply->poly_date->format('d.m.Y') : '--' }}</td>
                            <td class="">{{ $ply->order_no ? App\Models\OrderDetail::where('order_no', $ply->order_no)->first()?->buyer_name : '--' }}</td>
                            <td class="">{{ $ply->style_no }}</td>
                            <td class="">{{ $ply->order_no }}</td>
                            <td>{{ $ply->color_name }}</td>
                            <td>{{ number_format($colorQty) }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($ply->poly_qty) }}</td>
                            <td class="text-primary font-weight-bold">{{ number_format($totalPoly) }}</td>
                            <td class="text-danger font-weight-bold">{{ number_format($balance) }}</td>
                            <td>{{ $ply->createdBy?->name }}</td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditPoly_{{$ply->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{ route('admin.polyAction', ['delete', 'id'=>$ply->id]) }}"
                                onclick="return confirm('Are you sure you want to delete?')"
                                class="btn-custom danger">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center">No Poly Data Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($polies->count() > 0)
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="6" class="text-right">Total:</th>
                            <th></th>
                            <th class="text-success">{{ number_format($polies->sum('poly_qty')) }}</th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            <div class="mt-2">
                {{ $polies->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>


<div class="modal fade text-left" id="AddPoly" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.polyAction', 'create') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Add New Poly</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Buyer Selection (New Cascade) -->
                        <div class="col-md-12 form-group">
                            <label>Select Buyer*</label>
                            <select name="buyer_name" id="poly_buyer_select" class="form-control" required>
                                <option value="">-- Choose Buyer --</option>
                                @foreach($buyers as $buyer)
                                    <option value="{{ $buyer }}">{{ $buyer }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Style Selection (AJAX loaded based on buyer) -->
                        <div class="col-md-12 form-group">
                            <label>Select Style* <span id="poly_style_qty_label" class="badge badge-warning"></span></label>
                            <select name="style_no" id="poly_style_select" class="form-control" required disabled>
                                <option value="">-- Select Buyer First --</option>
                            </select>
                        </div>

                        <!-- Order Selection (AJAX loaded based on style) -->
                        <div class="col-md-12 form-group">
                            <label>Select Order (PO)* <span id="poly_order_qty_label" class="badge badge-primary"></span></label>
                            <select name="order_no" id="poly_order_select" class="form-control" required disabled>
                                <option value="">-- Select Style First --</option>
                            </select>
                        </div>

                        <!-- Color Selection (AJAX loaded) -->
                        <div class="col-md-12 form-group">
                            <label>Select Color* <span id="poly_color_qty_label" class="badge badge-info"></span></label>
                            <select name="color_name" id="poly_color_select" class="form-control" required disabled>
                                <option value="">-- Select Order First --</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Poly Qty*</label>
                            <input type="number" name="poly_qty" class="form-control poly_qty" placeholder="0" required min="1">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Poly Date</label>
                            <input type="date" name="poly_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Enter Remarks">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Save Poly</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($polies as $ply)
<div class="modal fade text-left" id="EditPoly_{{$ply->id}}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.polyAction', 'update') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $ply->id }}">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Poly</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Hidden fields for update -->
                        <input type="hidden" name="order_no" value="{{ $ply->order_no }}">
                        <input type="hidden" name="color_name" value="{{ $ply->color_name }}">
                        <input type="hidden" name="style_no" value="{{ $ply->style_no }}">
                        
                        <!-- Buyer (Read Only) -->
                        <div class="col-md-12 form-group">
                            <label>Buyer</label>
                            <input type="text" value="{{ App\Models\OrderDetail::where('order_no', $ply->order_no)->where('style_no', $ply->style_no)->first()?->buyer_name }}" class="form-control" readonly>
                        </div>

                        <!-- Style (Read Only) -->
                        <div class="col-md-12 form-group">
                            <label>Style</label>
                            <input type="text" value="{{ $ply->style_no }}" class="form-control" readonly>
                        </div>

                        <!-- Order (Read Only) -->
                        <div class="col-md-12 form-group">
                            <label>Order</label>
                            <input type="text" value="{{ $ply->order_no }}" class="form-control" readonly>
                        </div>

                        <!-- Color Name (Read Only) -->
                        <div class="col-md-12 form-group">
                            <label>Color</label>
                            <input type="text" value="{{ $ply->color_name }}" class="form-control" readonly>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Poly Qty*</label>
                            <input type="number" name="poly_qty" class="form-control poly_qty" value="{{ $ply->poly_qty ?? 0 }}" required min="1">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Poly Date</label>
                            <input type="date" name="poly_date" class="form-control" value="{{ $ply->poly_date ? $ply->poly_date->format('Y-m-d') : date('Y-m-d') }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Remarks</label>
                            <input type="text" name="remarks" class="form-control" value="{{ $ply->remarks ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Poly</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
@push('js')
<script>
$(document).ready(function() {
    // New Cascade: Buyer -> Style -> Order -> Color
    
    // Poly Buyer -> Style
    $('#poly_buyer_select').on('change', function() {
        let buyer = $(this).val();
        let $styleSelect = $('#poly_style_select');
        let $orderSelect = $('#poly_order_select');
        let $colorSelect = $('#poly_color_select');

        if (buyer) {
            $styleSelect.html('<option>Loading Styles...</option>').prop('disabled', true);
            $orderSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
            $colorSelect.empty().append('<option value="">-- Select Order First --</option>').prop('disabled', true);
            $('#poly_order_qty_label').text('');
            $('#poly_style_qty_label').text('');
            $('#poly_color_qty_label').text('');

            $.get("{{ route('admin.polyAction', 'get-styles-by-buyer') }}", { buyer: buyer }, function(data) {
                $styleSelect.empty().append('<option value="">-- Select Style --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $styleSelect.append('<option value="' + item + '">' + item + '</option>');
                });
            });
        } else {
            $styleSelect.empty().append('<option value="">-- Select Buyer First --</option>').prop('disabled', true);
            $('#poly_style_qty_label').text('');
        }
    });

    // Poly Style -> Order
    $('#poly_style_select').on('change', function() {
        let style_no = $(this).val();
        let buyer = $('#poly_buyer_select').val();
        let $orderSelect = $('#poly_order_select');
        let $colorSelect = $('#poly_color_select');

        if (style_no && buyer) {
            $orderSelect.html('<option>Loading Orders...</option>').prop('disabled', true);
            $colorSelect.empty().append('<option value="">-- Select Order First --</option>').prop('disabled', true);
            $('#poly_color_qty_label').text('');

            $.get("{{ route('admin.polyAction', 'get-orders-by-style') }}", { buyer: buyer, style_no: style_no }, function(data) {
                $orderSelect.empty().append('<option value="">-- Select Order --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $orderSelect.append('<option value="' + item.order_no + '" data-qty="' + item.total_qty + '">' + item.order_no + ' (' + item.total_qty + ')</option>');
                });
            });
        } else {
            $orderSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
            $('#poly_order_qty_label').text('');
        }
    });

    // Poly Order -> Color
    $('#poly_order_select').on('change', function() {
        let order_no = $(this).val();
        let style_no = $('#poly_style_select').val();
        let buyer = $('#poly_buyer_select').val();
        let $colorSelect = $('#poly_color_select');
        let selectedOrder = $(this).find('option:selected');

        if (selectedOrder.data('qty')) {
            $('#poly_order_qty_label').text('Qty: ' + selectedOrder.data('qty'));
        } else {
            $('#poly_order_qty_label').text('');
        }

        if (order_no && style_no) {
            $colorSelect.html('<option>Loading Colors...</option>').prop('disabled', true);

            $.get("{{ route('admin.polyAction', 'get-colors-by-order') }}",
                { order_no: order_no, style_no: style_no },
                function(data) {
                    $colorSelect.empty()
                        .append('<option value="">-- Select Color --</option>')
                        .prop('disabled', false);

                    data.forEach(function(item) {
                        $colorSelect.append(
                            '<option value="' + item.color_name +
                            '" data-qty="' + item.total_qty + '">' +
                            item.color_name + ' (' + item.total_qty + ')</option>'
                        );
                    });
                }
            );
        } else {
            $colorSelect.empty()
                .append('<option value="">-- Select Order First --</option>')
                .prop('disabled', true);
            $('#poly_color_qty_label').text('');
        }
    });

    // Show color qty when selected
    $('#poly_color_select').on('change', function() {
        let selected = $(this).find('option:selected');
        let qty = selected.data('qty');
        if (qty) {
            $('#poly_color_qty_label').text('Qty: ' + qty);
        } else {
            $('#poly_color_qty_label').text('');
        }
    });
});
</script>
@endpush
