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
                            <th style="min-width: 120px;">Cutting Date</th>
                            <th style="min-width: 150px;">PI Number</th>
                            <th style="min-width: 150px;">Style Number</th>
                            <th style="min-width: 120px;">Color</th>
                            <th style="min-width: 120px;">Cutting Qty</th>
                            <th style="min-width: 150px;">Added By</th>
                            <th style="min-width: 150px;">Remarks</th>
                            <th style="min-width: 100px; width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cuttings as $i => $cut)
                        <tr>
                            <td>{{ $cuttings->firstItem() + $i }}</td>
                            <td>{{ $cut->cutting_date ? $cut->cutting_date->format('d.m.Y') : '--' }}</td>
                            <td class="font-weight-bold">{{ $cut->pi_no }}</td>
                            <td>
                                <span class="badge badge-info">{{ $cut->style_no }}</span>
                            </td>
                            <td>{{ $cut->color_name }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($cut->cutting_qty) }} Pcs</td>
                            <td>{{ $cut->createdBy?->name }}</td>
                            <td><smalls>{{ $cut->remarks }}</smalls></td>
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
                            <td colspan="10" class="text-center">No Cutting Data Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($cuttings->count() > 0)
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="5" class="text-right">Total:</th>
                            <th class="text-success">{{ number_format($cuttings->sum('cutting_qty')) }} Pcs</th>
                            <th colspan="3"></th>
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
                        <!-- PI Selection -->
                        <div class="col-md-12 form-group">
                            <label>Select PI*</label>
                            <select name="pi_no" id="pi_select" class="form-control" required>
                                <option value="">-- Choose PI --</option>
                                @foreach($pis as $pi)
                                    <option value="{{ $pi->id }}">{{ $pi->pi_no }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Order Selection (AJAX loaded) -->
                        <div class="col-md-12 form-group">
                            <label>Select Order (PO)* <span id="order_qty_label" class="badge badge-primary"></span></label>
                            <select name="order_no" id="order_select" class="form-control" required disabled>
                                <option value="">-- First Select PI --</option>
                            </select>
                        </div>

                        <!-- Style Selection (AJAX loaded) -->
                        <div class="col-md-12 form-group">
                            <label>Select Style* <span id="style_qty_label" class="badge badge-warning"></span></label>
                            <select name="style_no" id="style_select" class="form-control" required disabled>
                                <option value="">-- Select Order First --</option>
                            </select>
                            <input type="hidden" name="planning_id" id="planning_id">
                        </div>

                        <!-- Color Selection (AJAX loaded) -->
                        <div class="col-md-12 form-group">
                            <label>Select Color* <span id="color_qty_label" class="badge badge-info"></span></label>
                            <select name="color_name" id="color_select" class="form-control" required disabled>
                                <option value="">-- Select Style First --</option>
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
                        <!-- PI Selection -->
                        <div class="col-md-12 form-group">
                            <label>Select PI*</label>
                            <input type="text" value="{{ $cut->pi_no }}" class="form-control" readonly>
                            {{-- <select name="pi_no" id="pi_select" class="form-control" required>
                                <option value="">-- Choose PI --</option>
                                @foreach($pis as $pi)
                                    <option value="{{ $pi->id }}" {{ $pi->id == $cut->pi_id ? 'selected':'' }}>{{ $pi->pi_no }}</option>
                                @endforeach
                            </select> --}}
                        </div>

                        <!-- Style Selection (AJAX loaded) -->
                        <div class="col-md-12 form-group">
                            <label>Select Style* <span id="style_qty_label" class="badge badge-warning"></span></label>
                            <input type="text" value="{{ $cut->style_no }}" class="form-control" readonly>
                        </div>

                        <!-- Color Name -->
                        <div class="col-md-12 form-group">
                            <label>Color Name*</label>
                            <input type="text" name="color_name" value="{{ $cut->color_name }}" class="form-control">
                        </div>

                        <!-- Cutting Qty -->
                        <div class="col-md-6 form-group">
                            <label>Cutting Qty*</label>
                            <input type="number" name="cutting_qty" class="form-control cutting_qty" placeholder="Cutting Quantity" value="{{ $cut->cutting_qty ?? 0 }}" required min="1">
                        </div>

                        <!-- Cutting Date -->
                        <div class="col-md-6 form-group">
                            <label>Cutting Date</label>
                            <input type="date" name="cutting_date" class="form-control" value="{{ $cut->cutting_date->format('Y-m-d') ?? date('Y-m-d') }}">
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
// PI -> Order Selection
    $('#pi_select').on('change', function() {
        let pi_no = $(this).val();
        let $orderSelect = $('#order_select');
        let $styleSelect = $('#style_select');
        let $colorSelect = $('#color_select');

        if (pi_no) {
            $orderSelect.html('<option>Loading Orders...</option>').prop('disabled', true);
            $styleSelect.empty().append('<option value="">-- Select Order First --</option>').prop('disabled', true);
            $colorSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
            $('#order_qty_label').text('');
            $('#style_qty_label').text('');
            $('#color_qty_label').text('');

            $.get("{{ route('admin.cuttingAction', 'get-orders') }}", { pi_id: pi_no }, function(data) {
                $orderSelect.empty().append('<option value="">-- Select Order --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $orderSelect.append('<option value="' + item.order_no + '" data-qty="' + item.total_order_qty + '">' + item.order_no + ' (' + item.total_order_qty + ')</option>');
                });
            });
        } else {
            $orderSelect.empty().append('<option value="">-- First Select PI --</option>').prop('disabled', true);
            $('#order_qty_label').text('');
        }
    });

    // Order -> Style Selection
    $('#order_select').on('change', function() {
        let order_no = $(this).val();
        let pi_no = $('#pi_select').val();
        let $styleSelect = $('#style_select');
        let $colorSelect = $('#color_select');
        let selectedOrder = $(this).find('option:selected');

        if (selectedOrder.data('qty')) {
            $('#order_qty_label').text('Qty: ' + selectedOrder.data('qty'));
        } else {
            $('#order_qty_label').text('');
        }

        if (order_no && pi_no) {
            $styleSelect.html('<option>Loading Styles...</option>').prop('disabled', true);
            $colorSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
            $('#color_qty_label').text('');

            $.get("{{ route('admin.cuttingAction', 'get-styles') }}", { pi_id: pi_no, order_no: order_no }, function(data) {
                $styleSelect.empty().append('<option value="">-- Select Style --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $styleSelect.append('<option value="' + item.style_no + '" data-qty="' + item.total_style_qty + '">' + item.style_no + '</option>');
                });
            });
        } else {
            $styleSelect.empty().append('<option value="">-- Select Order First --</option>').prop('disabled', true);
            $('#style_qty_label').text('');
        }
    });

    // Style -> Color Selection (updated)
    $('#style_select').on('change', function() {
        let style_no = $(this).val();
        let pi_no = $('#pi_select').val();
        let order_no = $('#order_select').val();
        let $colorSelect = $('#color_select');
        let selected = $(this).find('option:selected');

        if (selected.data('qty')) {
            $('#style_qty_label').text('Qty: ' + selected.data('qty'));
        } else {
            $('#style_qty_label').text('');
        }

        if (style_no && pi_no && order_no) {
            $colorSelect.html('<option>Loading Colors...</option>').prop('disabled', true);

            $.get("{{ route('admin.cuttingAction', 'get-colors') }}", { pi_id: pi_no, order_no: order_no, style_no: style_no }, function(data) {
                $colorSelect.empty().append('<option value="">-- Select Color --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $colorSelect.append('<option value="' + item.color_name + '" data-qty="' + item.total_color_qty + '">' + item.color_name + ' (' + item.total_color_qty + ')</option>');
                });
            });
        } else {
            $colorSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
            $('#color_qty_label').text('');
        }
    });

    // Color selection - show color qty
    $('#color_select').on('change', function() {
        let selected = $(this).find('option:selected');
        let qty = selected.data('qty');
        if (qty) {
            $('#color_qty_label').text('Qty: ' + qty);
        } else {
            $('#color_qty_label').text('');
        }
    });


    // স্টাইল সিলেক্ট করলে Qty লেবেলে দেখানো এবং কালার লোড
    $('#style_select').on('change', function() {
        let selected = $(this).find('option:selected');
        let qty = selected.data('qty');
        let planId = selected.data('plan');
        let pi_no = $('#pi_select').val();
        let style_no = $(this).val();
        let $colorSelect = $('#color_select');

        if (qty) {
            $('#style_qty_label').text('Qty: ' + qty);
        } else {
            $('#style_qty_label').text('');
        }

        // Load colors for selected style
        if (style_no && pi_no) {
            $colorSelect.html('<option>Loading Colors...</option>').prop('disabled', true);

            $.get("{{ route('admin.cuttingAction', 'get-colors') }}", { pi_id: pi_no, style_no: style_no }, function(data) {
                $colorSelect.empty().append('<option value="">-- Select Color --</option>').prop('disabled', false);
                data.forEach(function(item) {
                    $colorSelect.append(`<option value="${item.color_name}" data-qty="${item.total_color_qty}">${item.color_name} (${item.total_color_qty})</option>`);
                });
            });
        } else {
            $colorSelect.empty().append('<option value="">-- Select Style First --</option>').prop('disabled', true);
            $('#color_qty_label').text('');
        }
    });

    // Color selection - show color qty
    $('#color_select').on('change', function() {
        let selected = $(this).find('option:selected');
        let qty = selected.data('qty');
        if (qty) {
            $('#color_qty_label').text('Qty: ' + qty);
        } else {
            $('#color_qty_label').text('');
        }
    });
});
</script>

@endpush
