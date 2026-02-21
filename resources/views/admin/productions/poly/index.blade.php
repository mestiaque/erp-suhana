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
@endsection
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
                            <input type="text" name="search" value="{{ request()->search ?: '' }}" placeholder="PI No, Style No, Color" class="form-control" />
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
                            <th style="min-width: 150px;">PI Number</th>
                            <th style="min-width: 150px;">Order No</th>
                            <th style="min-width: 150px;">Style Number</th>
                            <th style="min-width: 120px;">Color</th>
                            <th style="min-width: 120px;">Poly Qty</th>
                            <th style="min-width: 150px;">Added By</th>
                            <th style="min-width: 150px;">Remarks</th>
                            <th style="min-width: 100px; width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($polies as $i => $ply)
                        <tr>
                            <td>{{ $polies->firstItem() + $i }}</td>
                            <td>{{ $ply->poly_date ? $ply->poly_date->format('d.m.Y') : '--' }}</td>
                            <td class="font-weight-bold">{{ $ply->pi_no }}</td>
                            <td><span class="badge badge-primary">{{ $ply->order_no }}</span></td>
                            <td>
                                <span class="badge badge-info">{{ $ply->style_no }}</span>
                            </td>
                            <td>{{ $ply->color_name }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($ply->poly_qty) }} Pcs</td>
                            <td>{{ $ply->createdBy?->name }}</td>
                            <td><smalls>{{ $ply->remarks }}</smalls></td>
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
                            <td colspan="10" class="text-center">No Poly Data Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($polies->count() > 0)
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="6" class="text-right">Total:</th>
                            <th class="text-success">{{ number_format($polies->sum('poly_qty')) }} Pcs</th>
                            <th colspan="3"></th>
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
                        <div class="col-md-12 form-group">
                            <label>Select PI*</label>
                            <select name="pi_no" id="poly_pi_select" class="form-control" required>
                                <option value="">-- Choose PI --</option>
                                @foreach($pis as $pi)
                                    <option value="{{ $pi->id }}">{{ $pi->pi_no }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Select Order (PO)* <span id="poly_order_qty_label" class="badge badge-primary"></span></label>
                            <select name="order_no" id="poly_order_select" class="form-control" required disabled>
                                <option value="">-- First Select PI --</option>
                            </select>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Select Style* <span id="poly_style_qty_label" class="badge badge-warning"></span></label>
                            <select name="style_no" id="poly_style_select" class="form-control" required disabled>
                                <option value="">-- Select Order First --</option>
                            </select>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Select Color* <span id="poly_color_qty_label" class="badge badge-info"></span></label>
                            <select name="color_name" id="poly_color_select" class="form-control" required disabled>
                                <option value="">-- Select Style First --</option>
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
                        <div class="col-md-12 form-group">
                            <label>PI Number*</label>
                            <input type="text" value="{{ $ply->pi_no }}" class="form-control" readonly>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Style Number*</label>
                            <input type="text" value="{{ $ply->style_no }}" class="form-control" readonly>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Color Name*</label>
                            <input type="text" name="color_name" value="{{ $ply->color_name }}" class="form-control">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Poly Qty*</label>
                            <input type="number" name="poly_qty" class="form-control poly_qty" value="{{ $ply->poly_qty ?? 0 }}" required min="1">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Poly Date</label>
                            <input type="date" name="poly_date" class="form-control" value="{{ $ply->poly_date->format('Y-m-d') ?? date('Y-m-d') }}">
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
    // Poly PI -> Order -> Style -> Color
    $('#poly_pi_select').on('change', function() {
        let pi_no = $(this).val();
        let $orderSelect = $('#poly_order_select');
        let $styleSelect = $('#poly_style_select');
        let $colorSelect = $('#poly_color_select');
        let $orderQtyLabel = $('#poly_order_qty_label');
        let $styleQtyLabel = $('#poly_style_qty_label');

        // Reset downstream dropdowns
        $orderSelect.empty().append('<option value="">-- Select Order --</option>').prop('disabled', true);
        $styleSelect.empty().append('<option value="">-- Select Order First --</option>').prop('disabled', true);
        $colorSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
        $orderQtyLabel.text('');
        $styleQtyLabel.text('');

        if (pi_no) {
            // Load Orders by PI
            $orderSelect.html('<option>Loading Orders...</option>').prop('disabled', true);
            
            $.get("{{ route('admin.polyAction', 'get-orders') }}", { pi_id: pi_no }, function(data) {
                $orderSelect.empty().append('<option value="">-- Select Order --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $orderSelect.append(`<option value="${item.order_no}" data-qty="${item.total_order_qty}">${item.order_no}</option>`);
                });
            });
        } else {
            $orderSelect.empty().append('<option value="">-- First Select PI --</option>').prop('disabled', true);
            $orderQtyLabel.text('');
        }
    });

    // Poly Order -> Style
    $('#poly_order_select').on('change', function() {
        let order_no = $(this).val();
        let pi_no = $('#poly_pi_select').val();
        let $styleSelect = $('#poly_style_select');
        let $colorSelect = $('#poly_color_select');
        let $styleQtyLabel = $('#poly_style_qty_label');

        // Reset downstream dropdowns
        $styleSelect.empty().append('<option value="">-- Select Style --</option>').prop('disabled', true);
        $colorSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
        $styleQtyLabel.text('');

        if (order_no && pi_no) {
            let selectedOption = $(this).find('option:selected');
            let orderQty = selectedOption.data('qty');
            if (orderQty) {
                $('#poly_order_qty_label').text('Qty: ' + orderQty);
            }

            $styleSelect.html('<option>Loading Styles...</option>').prop('disabled', true);

            $.get("{{ route('admin.polyAction', 'get-styles') }}", { pi_id: pi_no, order_no: order_no }, function(data) {
                $styleSelect.empty().append('<option value="">-- Select Style --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $styleSelect.append(`<option value="${item.style_no}" data-qty="${item.total_style_qty}">${item.style_no}</option>`);
                });
            });
        } else {
            $styleSelect.empty().append('<option value="">-- Select Order First --</option>').prop('disabled', true);
            $styleQtyLabel.text('');
        }
    });

    // Poly Style -> Color
    $('#poly_style_select').on('change', function() {
        let style_no = $(this).val();
        let order_no = $('#poly_order_select').val();
        let pi_no = $('#poly_pi_select').val();
        let $colorSelect = $('#poly_color_select');
        let $qtyLabel = $('#poly_color_qty_label');

        // Reset color dropdown
        $colorSelect.empty().append('<option value="">-- Select Color --</option>').prop('disabled', true);
        $qtyLabel.text('');

        if (style_no && pi_no) {
            let selectedOption = $(this).find('option:selected');
            let styleQty = selectedOption.data('qty');
            if (styleQty) {
                $('#poly_style_qty_label').text('Qty: ' + styleQty);
            }

            $colorSelect.html('<option>Loading Colors...</option>').prop('disabled', true);

            $.get("{{ route('admin.polyAction', 'get-colors') }}", { pi_id: pi_no, style_no: style_no, order_no: order_no }, function(data) {
                $colorSelect.empty().append('<option value="">-- Select Color --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $colorSelect.append(`<option value="${item.color_name}" data-qty="${item.total_color_qty}">${item.color_name} (${item.total_color_qty})</option>`);
                });
            });
        } else {
            $colorSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
            $qtyLabel.text('');
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
