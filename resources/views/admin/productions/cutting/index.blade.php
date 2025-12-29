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
                            <input type="text" name="search" value="{{ request()->search ?: '' }}" placeholder="PI No, Style No" class="form-control" />
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
                            <th colspan="4" class="text-right">Total:</th>
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

                        <!-- Style Selection (AJAX loaded) -->
                        <div class="col-md-12 form-group">
                            <label>Select Style* <span id="style_qty_label" class="badge badge-warning"></span></label>
                            <select name="style_no" id="style_select" class="form-control" required disabled>
                                <option value="">-- First Select PI --</option>
                            </select>
                            <input type="hidden" name="planning_id" id="planning_id">
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
$(document).ready(function() {
    $('#pi_select').on('change', function() {
        let pi_no = $(this).val();
        let $styleSelect = $('#style_select');
        let $qtyLabel = $('#style_qty_label');

        if (pi_no) {
            $styleSelect.html('<option>Loading Styles...</option>').prop('disabled', true);
            $qtyLabel.text('');

            // আপনার রাউট অনুযায়ী URL পরিবর্তন করুন
            $.get("{{ route('admin.cuttingAction', 'get-styles') }}", { pi_id: pi_no }, function(data) {
                $styleSelect.empty().append('<option value="">-- Select Style --</option>').prop('disabled', false);

                data.forEach(function(item) {
                    // এখানে planning_id এবং style_qty ডাটা এট্রিবিউট হিসেবে রাখা হচ্ছে
                    $styleSelect.append(`<option value="${item.style_no}" data-qty="${item.total_style_qty}" data-plan="${item.id}">${item.style_no}</option>`);
                });
            });
        } else {
            $styleSelect.empty().append('<option value="">-- First Select PI --</option>').prop('disabled', true);
            $qtyLabel.text('');
        }
    });

    // স্টাইল সিলেক্ট করলে Qty লেবেলে দেখানো
    $('#style_select').on('change', function() {
        let selected = $(this).find('option:selected');
        let qty = selected.data('qty');
        let planId = selected.data('plan');

        if (qty) {
            $('#style_qty_label').text('Qty: ' + qty);
            // $('.cutting_qty').attr('max', qty);

        } else {
            $('#style_qty_label').text('');
            // $('.cutting_qty').attr('max', '');
        }
    });
});
</script>

@endpush
