@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Iron List')}}</title>
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
            <h3>Iron List</h3>
            <div class="dropdown">
                <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddIron">
                    <i class="bx bx-plus"></i> Add Iron
                </a>
                <a href="{{route('admin.iron')}}" class="btn-custom yellow">
                    <i class="bx bx-rotate-left"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.iron') }}">
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
                            <th style="min-width: 120px;">Iron Date</th>
                            <th style="min-width: 150px;">Buyer</th>
                            <th style="min-width: 150px;">Style Number</th>
                            <th style="min-width: 150px;">Order Number</th>
                            <th style="min-width: 120px;">Color</th>
                            <th style="min-width: 100px;">Color Qty</th>
                            <th style="min-width: 100px;">Iron Qty</th>
                            <th style="min-width: 100px;">Total Iron</th>
                            <th style="min-width: 100px;">Balance</th>
                            <th style="min-width: 150px;">Added By</th>
                            <th style="min-width: 100px; width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($irons as $i => $irn)
                        @php
                            $colorQty = \App\Models\OrderDetailItem::where('order_no', $irn->order_no)->where('style_no', $irn->style_no)->where('color_name', $irn->color_name)->sum('qty');
                            $totalIron = \App\Models\Iron::where('order_no', $irn->order_no)->where('style_no', $irn->style_no)->where('color_name', $irn->color_name)->sum('iron_qty');
                            $balance = $colorQty - $totalIron;
                        @endphp
                        <tr>
                            <td>{{ $irons->firstItem() + $i }}</td>
                            <td>{{ $irn->iron_date ? $irn->iron_date->format('d.m.Y') : '--' }}</td>
                            <td class="">{{ $irn->order_no ? App\Models\OrderDetail::where('order_no', $irn->order_no)->first()?->buyer_name : '--' }}</td>
                            <td class="">{{ $irn->style_no }}</td>
                            <td class="">{{ $irn->order_no }}</td>
                            <td>{{ $irn->color_name }}</td>
                            <td>{{ number_format($colorQty) }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($irn->iron_qty) }}</td>
                            <td class="text-primary font-weight-bold">{{ number_format($totalIron) }}</td>
                            <td class="text-danger font-weight-bold">{{ number_format($balance) }}</td>
                            <td>{{ $irn->createdBy?->name }}</td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditIron_{{$irn->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{ route('admin.ironAction', ['delete', 'id'=>$irn->id]) }}"
                                onclick="return confirm('Are you sure you want to delete?')"
                                class="btn-custom danger">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center">No Iron Data Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($irons->count() > 0)
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="6" class="text-right">Total:</th>
                            <th></th>
                            <th class="text-success">{{ number_format($irons->sum('iron_qty')) }}</th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            <div class="mt-2">
                {{ $irons->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>


<div class="modal fade text-left" id="AddIron" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.ironAction', 'create') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Add New Iron</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Buyer Selection (New Cascade) -->
                        <div class="col-md-12 form-group">
                            <label>Select Buyer*</label>
                            <select name="buyer_name" id="iron_buyer_select" class="form-control" required>
                                <option value="">-- Choose Buyer --</option>
                                @foreach($buyers as $buyer)
                                    <option value="{{ $buyer }}">{{ $buyer }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Style Selection (AJAX loaded based on buyer) -->
                        <div class="col-md-12 form-group">
                            <label>Select Style* <span id="iron_style_qty_label" class="badge badge-warning"></span></label>
                            <select name="style_no" id="iron_style_select" class="form-control" required disabled>
                                <option value="">-- Select Buyer First --</option>
                            </select>
                        </div>

                        <!-- Order Selection (AJAX loaded based on style) -->
                        <div class="col-md-12 form-group">
                            <label>Select Order (PO)* <span id="iron_order_qty_label" class="badge badge-primary"></span></label>
                            <select name="order_no" id="iron_order_select" class="form-control" required disabled>
                                <option value="">-- Select Style First --</option>
                            </select>
                        </div>

                        <!-- Color Selection (AJAX loaded) -->
                        <div class="col-md-12 form-group">
                            <label>Select Color* <span id="iron_color_qty_label" class="badge badge-info"></span></label>
                            <select name="color_name" id="iron_color_select" class="form-control" required disabled>
                                <option value="">-- Select Order First --</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Iron Qty*</label>
                            <input type="number" name="iron_qty" class="form-control iron_qty" placeholder="0" required min="1">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Iron Date</label>
                            <input type="date" name="iron_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Enter Remarks">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Save Iron</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($irons as $irn)
<div class="modal fade text-left" id="EditIron_{{$irn->id}}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.ironAction', 'update') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $irn->id }}">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Iron</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Hidden fields for update -->
                        <input type="hidden" name="order_no" value="{{ $irn->order_no }}">
                        <input type="hidden" name="color_name" value="{{ $irn->color_name }}">
                        <input type="hidden" name="style_no" value="{{ $irn->style_no }}">

                        <!-- Buyer (Read Only) -->
                        <div class="col-md-12 form-group">
                            <label>Buyer</label>
                            <input type="text" value="{{ App\Models\OrderDetail::where('order_no', $irn->order_no)->where('style_no', $irn->style_no)->first()?->buyer_name }}" class="form-control" readonly>
                        </div>

                        <!-- Style (Read Only) -->
                        <div class="col-md-12 form-group">
                            <label>Style</label>
                            <input type="text" value="{{ $irn->style_no }}" class="form-control" readonly>
                        </div>

                        <!-- Order (Read Only) -->
                        <div class="col-md-12 form-group">
                            <label>Order</label>
                            <input type="text" value="{{ $irn->order_no }}" class="form-control" readonly>
                        </div>

                        <!-- Color Name (Read Only) -->
                        <div class="col-md-12 form-group">
                            <label>Color</label>
                            <input type="text" value="{{ $irn->color_name }}" class="form-control" readonly>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Iron Qty*</label>
                            <input type="number" name="iron_qty" class="form-control iron_qty" value="{{ $irn->iron_qty ?? 0 }}" required min="1">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Iron Date</label>
                            <input type="date" name="iron_date" class="form-control" value="{{ $irn->iron_date ? $irn->iron_date->format('Y-m-d') : date('Y-m-d') }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Remarks</label>
                            <input type="text" name="remarks" class="form-control" value="{{ $irn->remarks ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Iron</button>
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

    // Iron Buyer -> Style
    $('#iron_buyer_select').on('change', function() {
        let buyer = $(this).val();
        let $styleSelect = $('#iron_style_select');
        let $orderSelect = $('#iron_order_select');
        let $colorSelect = $('#iron_color_select');

        if (buyer) {
            $styleSelect.html('<option>Loading Styles...</option>').prop('disabled', true);
            $orderSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
            $colorSelect.empty().append('<option value="">-- Select Order First --</option>').prop('disabled', true);
            $('#iron_order_qty_label').text('');
            $('#iron_style_qty_label').text('');
            $('#iron_color_qty_label').text('');

            $.get("{{ route('admin.ironAction', 'get-styles-by-buyer') }}", { buyer: buyer }, function(data) {
                $styleSelect.empty().append('<option value="">-- Select Style --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $styleSelect.append('<option value="' + item + '">' + item + '</option>');
                });
            });
        } else {
            $styleSelect.empty().append('<option value="">-- Select Buyer First --</option>').prop('disabled', true);
            $('#iron_style_qty_label').text('');
        }
    });

    // Iron Style -> Order
    $('#iron_style_select').on('change', function() {
        let style_no = $(this).val();
        let buyer = $('#iron_buyer_select').val();
        let $orderSelect = $('#iron_order_select');
        let $colorSelect = $('#iron_color_select');

        if (style_no && buyer) {
            $orderSelect.html('<option>Loading Orders...</option>').prop('disabled', true);
            $colorSelect.empty().append('<option value="">-- Select Order First --</option>').prop('disabled', true);
            $('#iron_color_qty_label').text('');

            $.get("{{ route('admin.ironAction', 'get-orders-by-style') }}", { buyer: buyer, style_no: style_no }, function(data) {
                $orderSelect.empty().append('<option value="">-- Select Order --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $orderSelect.append('<option value="' + item.order_no + '" data-qty="' + item.total_qty + '">' + item.order_no + ' (' + item.total_qty + ')</option>');
                });
            });
        } else {
            $orderSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
            $('#iron_order_qty_label').text('');
        }
    });

    // Iron Order -> Color
    $('#iron_order_select').on('change', function() {
        let order_no = $(this).val();
        let style_no = $('#iron_style_select').val();
        let buyer = $('#iron_buyer_select').val();
        let $colorSelect = $('#iron_color_select');
        let selectedOrder = $(this).find('option:selected');

        if (selectedOrder.data('qty')) {
            $('#iron_order_qty_label').text('Qty: ' + selectedOrder.data('qty'));
        } else {
            $('#iron_order_qty_label').text('');
        }

        if (order_no && style_no) {
            $colorSelect.html('<option>Loading Colors...</option>').prop('disabled', true);

            $.get("{{ route('admin.ironAction', 'get-colors-by-order') }}",
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
            $('#iron_color_qty_label').text('');
        }
    });

    // Show color qty when selected
    $('#iron_color_select').on('change', function() {
        let selected = $(this).find('option:selected');
        let qty = selected.data('qty');
        if (qty) {
            $('#iron_color_qty_label').text('Qty: ' + qty);
        } else {
            $('#iron_color_qty_label').text('');
        }
    });
});
</script>
@endpush

