@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Cutting List')}}</title>
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
<!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Cutting List</h3>
            <div class="dropdown">
                @can('cutting.add')
                <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddCutting">
                    <i class="bx bx-plus"></i> Add Cutting
                </a>
                @endcan
                <a href="{{route('admin.cutting')}}" class="btn-custom yellow">
                    <i class="bx bx-rotate-left"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- সার্চ এবং ফিল্টার সেকশন -->
            <form action="{{ route('admin.cutting') }}">
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
                            <th style="min-width: 120px;">Cutting Date</th>
                            <th style="min-width: 150px;">Buyer</th>
                            <th style="min-width: 150px;">Style Number</th>
                            <th style="min-width: 150px;">Order Number</th>
                            <th style="min-width: 120px;">Color</th>
                            <th style="min-width: 100px;">Color Qty</th>
                            <th style="min-width: 100px;">Cutting Qty</th>
                            <th style="min-width: 100px;">Total Cutting</th>
                            <th style="min-width: 100px;">Balance</th>
                            <th style="min-width: 150px;">Added By</th>
                            <th style="min-width: 100px; width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cuttings as $i => $cut)
                        @php
                            $colorQty = \App\Models\OrderDetailItem::where('order_no', $cut->order_no)->where('style_no', $cut->style_no)->where('color_name', $cut->color_name)->sum('qty');
                            $totalCutting = \App\Models\Cutting::where('order_no', $cut->order_no)->where('style_no', $cut->style_no)->where('color_name', $cut->color_name)->sum('cutting_qty');
                            $balance = $colorQty - $totalCutting;
                        @endphp
                        <tr>
                            <td>{{ $cuttings->firstItem() + $i }}</td>
                            <td>{{ $cut->cutting_date ? $cut->cutting_date->format('d.m.Y') : '--' }}</td>
                            <td class="">{{ $cut->order_no ? App\Models\OrderDetail::where('order_no', $cut->order_no)->first()?->buyer_name : '--' }}</td>
                            <td class="">{{ $cut->style_no }}</td>
                            <td class="">{{ $cut->order_no }}</td>
                            <td>{{ $cut->color_name }}</td>
                            <td>{{ number_format($colorQty) }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($cut->cutting_qty) }}</td>
                            <td class="text-primary font-weight-bold">{{ number_format($totalCutting) }}</td>
                            <td class="text-danger font-weight-bold">{{ number_format($balance) }}</td>
                            <td>{{ $cut->createdBy?->name }}</td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditCutting_{{$cut->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{ route('admin.cuttingAction', ['delete', 'id'=>$cut->id]) }}"
                                onclick="return confirm('Are you sure you want to delete?')"
                                class="btn-custom danger">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center">No Cutting Data Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($cuttings->count() > 0)
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="6" class="text-right">Total:</th>
                            <th></th>
                            <th class="text-success">{{ number_format($cuttings->sum('cutting_qty')) }}</th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            <!-- প্যাজিনেশন -->
            <div class="mt-2">
                {{ $cuttings->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>



 <div class="modal fade text-left" id="AddCutting" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.cuttingAction', 'create') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Add New Cutting</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Buyer Selection (New Cascade) -->
                        <div class="col-md-12 form-group">
                            <label>Select Buyer*</label>
                            <select name="buyer_name" id="buyer_select" class="form-control" required>
                                <option value="">-- Choose Buyer --</option>
                                @foreach($buyers as $buyer)
                                    <option value="{{ $buyer }}">{{ $buyer }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Style Selection (AJAX loaded based on buyer) -->
                        <div class="col-md-12 form-group">
                            <label>Select Style* <span id="style_qty_label" class="badge badge-warning"></span></label>
                            <select name="style_no" id="style_select" class="form-control" required disabled>
                                <option value="">-- Select Buyer First --</option>
                            </select>
                        </div>

                        <!-- Order Selection (AJAX loaded based on style) -->
                        <div class="col-md-12 form-group">
                            <label>Select Order (PO)* <span id="order_qty_label" class="badge badge-primary"></span></label>
                            <select name="order_no" id="order_select" class="form-control" required disabled>
                                <option value="">-- Select Style First --</option>
                            </select>
                        </div>

                        <!-- Color Selection (AJAX loaded) -->
                        <div class="col-md-12 form-group">
                            <label>Select Color* <span id="color_qty_label" class="badge badge-info"></span></label>
                            <select name="color_name" id="color_select" class="form-control" required disabled>
                                <option value="">-- Select Order First --</option>
                            </select>
                        </div>

                        <!-- Cutting Qty -->
                        <div class="col-md-6 form-group">
                            <label>Cutting Qty*</label>
                            <input type="number" name="cutting_qty" class="form-control cutting_qty" placeholder="0" required min="1">
                        </div>

                        <!-- Cutting Date -->
                        <div class="col-md-6 form-group">
                            <label>Cutting Date</label>
                            <input type="date" name="cutting_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Enter Remarks">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Save Cutting</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($cuttings as $cut)
 <div class="modal fade text-left" id="EditCutting_{{$cut->id}}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.cuttingAction', 'update') }}" method="post">
                @csrf
                <input type="hidden" name="id" id="" value="{{ $cut->id }}">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Cutting</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Hidden fields for update -->
                        <input type="hidden" name="order_no" value="{{ $cut->order_no }}">
                        <input type="hidden" name="color_name" value="{{ $cut->color_name }}">
                        <input type="hidden" name="style_no" value="{{ $cut->style_no }}">
                        
                        <!-- Buyer (Read Only) -->
                        <div class="col-md-12 form-group">
                            <label>Buyer</label>
                            <input type="text" value="{{ App\Models\OrderDetail::where('order_no', $cut->order_no)->where('style_no', $cut->style_no)->first()?->buyer_name }}" class="form-control" readonly>
                        </div>

                        <!-- Style (Read Only) -->
                        <div class="col-md-12 form-group">
                            <label>Style</label>
                            <input type="text" value="{{ $cut->style_no }}" class="form-control" readonly>
                        </div>

                        <!-- Order (Read Only) -->
                        <div class="col-md-12 form-group">
                            <label>Order</label>
                            <input type="text" value="{{ $cut->order_no }}" class="form-control" readonly>
                        </div>

                        <!-- Color Name (Read Only) -->
                        <div class="col-md-12 form-group">
                            <label>Color</label>
                            <input type="text" value="{{ $cut->color_name }}" class="form-control" readonly>
                        </div>

                        <!-- Cutting Qty -->
                        <div class="col-md-6 form-group">
                            <label>Cutting Qty*</label>
                            <input type="number" name="cutting_qty" class="form-control cutting_qty" placeholder="Cutting Quantity" value="{{ $cut->cutting_qty ?? 0 }}" required min="1">
                        </div>

                        <!-- Cutting Date -->
                        <div class="col-md-6 form-group">
                            <label>Cutting Date</label>
                            <input type="date" name="cutting_date" class="form-control" value="{{ $cut->cutting_date ? $cut->cutting_date->format('Y-m-d') : date('Y-m-d') }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Enter Remarks" value="{{ $cut->remarks ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Save Cutting</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach





@endsection
@push('js')

<script>
// New Cascade: Buyer -> Style -> Order -> Color

// Buyer -> Style Selection
$('#buyer_select').on('change', function() {
    let buyer = $(this).val();
    let $styleSelect = $('#style_select');
    let $orderSelect = $('#order_select');
    let $colorSelect = $('#color_select');

    if (buyer) {
        $styleSelect.html('<option>Loading Styles...</option>').prop('disabled', true);
        $orderSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
        $colorSelect.empty().append('<option value="">-- Select Order First --</option>').prop('disabled', true);
        $('#order_qty_label').text('');
        $('#style_qty_label').text('');
        $('#color_qty_label').text('');

        $.get("{{ route('admin.cuttingAction', 'get-styles-by-buyer') }}", { buyer: buyer }, function(data) {
            $styleSelect.empty().append('<option value="">-- Select Style --</option>').prop('disabled', false);
            data.forEach(function(item) {
                $styleSelect.append('<option value="' + item + '">' + item + '</option>');
            });
        });
    } else {
        $styleSelect.empty().append('<option value="">-- Select Buyer First --</option>').prop('disabled', true);
        $('#style_qty_label').text('');
    }
});

// Style -> Order Selection
$('#style_select').on('change', function() {
    let style_no = $(this).val();
    let buyer = $('#buyer_select').val();
    let $orderSelect = $('#order_select');
    let $colorSelect = $('#color_select');

    if (style_no && buyer) {
        $orderSelect.html('<option>Loading Orders...</option>').prop('disabled', true);
        $colorSelect.empty().append('<option value="">-- Select Order First --</option>').prop('disabled', true);
        $('#color_qty_label').text('');

        $.get("{{ route('admin.cuttingAction', 'get-orders-by-style') }}", { buyer: buyer, style_no: style_no }, function(data) {
            $orderSelect.empty().append('<option value="">-- Select Order --</option>').prop('disabled', false);
            data.forEach(function(item) {
                $orderSelect.append('<option value="' + item.order_no + '" data-qty="' + item.total_qty + '">' + item.order_no + ' (' + item.total_qty + ')</option>');
            });
        });
    } else {
        $orderSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
        $('#order_qty_label').text('');
    }
});

// Order -> Color Selection
$('#order_select').on('change', function() {
    let order_no = $(this).val();
    let style_no = $('#style_select').val();
    let buyer = $('#buyer_select').val();
    let $colorSelect = $('#color_select');
    let selectedOrder = $(this).find('option:selected');

    if (selectedOrder.data('qty')) {
        $('#order_qty_label').text('Qty: ' + selectedOrder.data('qty'));
    } else {
        $('#order_qty_label').text('');
    }

    if (order_no && style_no) {
        $colorSelect.html('<option>Loading Colors...</option>').prop('disabled', true);

        $.get("{{ route('admin.cuttingAction', 'get-colors-by-order') }}",
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
        $('#color_qty_label').text('');
    }
});

// Color selection - show color qty
$('#color_select').on('change', function() {
    let selected = $(this).find('option:selected');
    let qty = selected.data('qty');
    $('#color_qty_label').text(qty ? 'Qty: ' + qty : '');
});
</script>

@endpush
