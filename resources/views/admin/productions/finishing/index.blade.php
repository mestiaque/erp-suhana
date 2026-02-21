@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Finishing List')}}</title>
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
            <h3>Finishing List</h3>
            <div class="dropdown">
                <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddFinishing">
                    <i class="bx bx-plus"></i> Add Finishing
                </a>
                <a href="{{route('admin.finishing')}}" class="btn-custom yellow">
                    <i class="bx bx-rotate-left"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.finishing') }}">
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
                            <th style="min-width: 120px;">Finishing Date</th>
                            <th style="min-width: 150px;">PI Number</th>
                            <th style="min-width: 150px;">Style Number</th>
                            <th style="min-width: 120px;">Color</th>
                            <th style="min-width: 120px;">Finishing Qty</th>
                            <th style="min-width: 150px;">Added By</th>
                            <th style="min-width: 150px;">Remarks</th>
                            <th style="min-width: 100px; width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($finishings as $i => $fin)
                        <tr>
                            <td>{{ $finishings->firstItem() + $i }}</td>
                            <td>{{ $fin->finishing_date ? $fin->finishing_date->format('d.m.Y') : '--' }}</td>
                            <td class="font-weight-bold">{{ $fin->pi_no }}</td>
                            <td>
                                <span class="badge badge-info">{{ $fin->style_no }}</span>
                            </td>
                            <td>{{ $fin->color_name }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($fin->finishing_qty) }} Pcs</td>
                            <td>{{ $fin->createdBy?->name }}</td>
                            <td><smalls>{{ $fin->remarks }}</smalls></td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditFinishing_{{$fin->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{ route('admin.finishingAction', ['delete', 'id'=>$fin->id]) }}"
                                onclick="return confirm('Are you sure you want to delete?')"
                                class="btn-custom danger">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No Finishing Data Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($finishings->count() > 0)
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="5" class="text-right">Total:</th>
                            <th class="text-success">{{ number_format($finishings->sum('finishing_qty')) }} Pcs</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            <div class="mt-2">
                {{ $finishings->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>


<div class="modal fade text-left" id="AddFinishing" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.finishingAction', 'create') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Add New Finishing</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label>Select PI*</label>
                            <select name="pi_no" id="finishing_pi_select" class="form-control" required>
                                <option value="">-- Choose PI --</option>
                                @foreach($pis as $pi)
                                    <option value="{{ $pi->id }}">{{ $pi->pi_no }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Select Order (PO)* <span id="finishing_order_qty_label" class="badge badge-primary"></span></label>
                            <select name="order_no" id="finishing_order_select" class="form-control" required disabled>
                                <option value="">-- First Select PI --</option>
                            </select>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Select Style* <span id="finishing_style_qty_label" class="badge badge-warning"></span></label>
                            <select name="style_no" id="finishing_style_select" class="form-control" required disabled>
                                <option value="">-- Select Order First --</option>
                            </select>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Select Color* <span id="finishing_color_qty_label" class="badge badge-info"></span></label>
                            <select name="color_name" id="finishing_color_select" class="form-control" required disabled>
                                <option value="">-- Select Style First --</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Finishing Qty*</label>
                            <input type="number" name="finishing_qty" class="form-control finishing_qty" placeholder="0" required min="1">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Finishing Date</label>
                            <input type="date" name="finishing_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Enter Remarks">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Save Finishing</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($finishings as $fin)
<div class="modal fade text-left" id="EditFinishing_{{$fin->id}}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.finishingAction', 'update') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $fin->id }}">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Finishing</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label>PI Number*</label>
                            <input type="text" value="{{ $fin->pi_no }}" class="form-control" readonly>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Style Number*</label>
                            <input type="text" value="{{ $fin->style_no }}" class="form-control" readonly>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Color Name*</label>
                            <input type="text" name="color_name" value="{{ $fin->color_name }}" class="form-control">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Finishing Qty*</label>
                            <input type="number" name="finishing_qty" class="form-control finishing_qty" value="{{ $fin->finishing_qty ?? 0 }}" required min="1">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Finishing Date</label>
                            <input type="date" name="finishing_date" class="form-control" value="{{ $fin->finishing_date->format('Y-m-d') ?? date('Y-m-d') }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Remarks</label>
                            <input type="text" name="remarks" class="form-control" value="{{ $fin->remarks ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Finishing</button>
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
    // Finishing PI -> Order
    $('#finishing_pi_select').on('change', function() {
        let pi_no = $(this).val();
        let $orderSelect = $('#finishing_order_select');
        let $styleSelect = $('#finishing_style_select');
        let $colorSelect = $('#finishing_color_select');

        if (pi_no) {
            $orderSelect.html('<option>Loading Orders...</option>').prop('disabled', true);
            $styleSelect.empty().append('<option value="">-- Select Order First --</option>').prop('disabled', true);
            $colorSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
            $('#finishing_order_qty_label').text('');
            $('#finishing_style_qty_label').text('');
            $('#finishing_color_qty_label').text('');

            $.get("{{ route('admin.finishingAction', 'get-orders') }}", { pi_id: pi_no }, function(data) {
                $orderSelect.empty().append('<option value="">-- Select Order --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $orderSelect.append(`<option value="${item.order_no}" data-qty="${item.total_order_qty}">${item.order_no} (${item.total_order_qty})</option>`);
                });
            });
        } else {
            $orderSelect.empty().append('<option value="">-- First Select PI --</option>').prop('disabled', true);
            $('#finishing_order_qty_label').text('');
        }
    });

    // Finishing Order -> Style
    $('#finishing_order_select').on('change', function() {
        let order_no = $(this).val();
        let pi_no = $('#finishing_pi_select').val();
        let $styleSelect = $('#finishing_style_select');
        let $colorSelect = $('#finishing_color_select');
        let selectedOrder = $(this).find('option:selected');

        if (selectedOrder.data('qty')) {
            $('#finishing_order_qty_label').text('Qty: ' + selectedOrder.data('qty'));
        } else {
            $('#finishing_order_qty_label').text('');
        }

        if (order_no && pi_no) {
            $styleSelect.html('<option>Loading Styles...</option>').prop('disabled', true);
            $colorSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
            $('#finishing_color_qty_label').text('');

            $.get("{{ route('admin.finishingAction', 'get-styles') }}", { pi_id: pi_no, order_no: order_no }, function(data) {
                $styleSelect.empty().append('<option value="">-- Select Style --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $styleSelect.append(`<option value="${item.style_no}" data-qty="${item.total_style_qty}">${item.style_no}</option>`);
                });
            });
        } else {
            $styleSelect.empty().append('<option value="">-- Select Order First --</option>').prop('disabled', true);
            $('#finishing_style_qty_label').text('');
        }
    });

    // Finishing Style -> Color
    $('#finishing_style_select').on('change', function() {
        let style_no = $(this).val();
        let pi_no = $('#finishing_pi_select').val();
        let order_no = $('#finishing_order_select').val();
        let $colorSelect = $('#finishing_color_select');
        let selected = $(this).find('option:selected');

        if (selected.data('qty')) {
            $('#finishing_style_qty_label').text('Qty: ' + selected.data('qty'));
        } else {
            $('#finishing_style_qty_label').text('');
        }

        if (style_no && pi_no && order_no) {
            $colorSelect.html('<option>Loading Colors...</option>').prop('disabled', true);

            $.get("{{ route('admin.finishingAction', 'get-colors') }}", { pi_id: pi_no, order_no: order_no, style_no: style_no }, function(data) {
                $colorSelect.empty().append('<option value="">-- Select Color --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $colorSelect.append(`<option value="${item.color_name}" data-qty="${item.total_color_qty}">${item.color_name} (${item.total_color_qty})</option>`);
                });
            });
        } else {
            $colorSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
            $('#finishing_color_qty_label').text('');
        }
    });

    // Show color qty when selected
    $('#finishing_color_select').on('change', function() {
        let selected = $(this).find('option:selected');
        let qty = selected.data('qty');
        if (qty) {
            $('#finishing_color_qty_label').text('Qty: ' + qty);
        } else {
            $('#finishing_color_qty_label').text('');
        }
    });
});
</script>
@endpush
