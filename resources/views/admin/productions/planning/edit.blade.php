@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle(isset($masterPlan) ? 'Master Planning Edit' : 'Master Planning Create') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>{{ isset($masterPlan) ? 'Edit Planning' : 'Create Planning' }}</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.productionPlanning') }}">Planning</a></li>
            <li class="item">{{ isset($masterPlan) ? 'Edit Planning' : 'Create Planning' }}</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Master Planning</h3>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ isset($masterPlan) ? route('admin.productionPlanningAction', ['update', $masterPlan->id]) : route('admin.productionPlanningAction', ['store']) }}" method="POST">
                @csrf
                <div class="row">

                    {{-- Style Select --}}
                    <div class="col-md-4">
                        <div class="style-info">
                            <select class="form-control form-control-sm mb-2 styleSelect select2" name="style_no">
                                <option value="">-- Select --</option>
                                @foreach($styles as $style)
                                <option value="{{ $style->style_no }}"
                                    data-buyer="{{ $style->buyer_name }}"
                                    data-merchandiser="{{ $style->merchant_name }}"
                                    data-order_no="{{ $style->order_no }}"
                                    data-qty="{{ $style->total_qty }}">
                                    {{ $style->style_no }} | {{ $style->merchant_name }} | {{ $style->order_no }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Selected Styles Table --}}
                    <div class="col-md-8 selected-style">
                        <table class="table table-sm">
                            <thead>
                                <th>Style</th>
                                <th>Buyer</th>
                                <th>Order No</th>
                                <th>Order Qty</th>
                                <th class="text-center">Action</th>
                            </thead>
                            <tbody>
                                {{-- @dd($masterPlan->productions) --}}
                                @if(isset($masterPlan) && count($masterPlan->productions) > 0)
                                    @foreach($masterPlan->productions as $index => $p)
                                        <tr id="row-{{ $p->style_no }}">
                                            <td>
                                                {{ $p->style_no }}
                                                <input type="hidden" name="styles[{{ $index }}][style_no]" value="{{ $p->style_no }}">
                                                <input type="hidden" name="styles[{{ $index }}][order_no]" value="{{ $p->order_no }}">
                                            </td>
                                            <td>{{ $p->orderDetailItems->buyer_name }}</td>
                                            <td>{{ $p->order_no }}</td>
                                            <td>{{ $p->style_qty ?? 0 }}</td>
                                            <td class="text-center p-0">
                                                <button type="button"
                                                        class="btn btn-sm btn-custom danger remove-style"
                                                        data-style="{{ $p->style_no }}"
                                                        data-buyer="{{ $p->orderDetailItems->buyer_name }}"
                                                        data-order_no="{{ $p->order_no }}"
                                                        data-qty="{{ $p->style_qty ?? 0 }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>

                        </table>
                    </div>

                    {{-- Submit --}}
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success"><i class="bx bx-check"></i>
                            {{ isset($masterPlan) ? 'Update Plan' : 'Create Plan' }}
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function () {

    // ================= Add Style =================
    let rowIndex = 0;

    $(document).on('change', '.styleSelect', function () {
        let select = $(this);
        let option = select.find('option:selected');
        if (!option.val()) return;

        let styleNo = option.val();
        let buyer = option.data('buyer');
        let qty = option.data('qty');
        let order_no = option.data('order_no');

        if ($('#row-' + styleNo).length) {
            alert('Already added');
            return;
        }

        let row = `
            <tr id="row-${styleNo}">
                <td>
                    ${styleNo}
                    <input type="hidden" name="styles[${rowIndex}][style_no]" value="${styleNo}">
                    <input type="hidden" name="styles[${rowIndex}][order_no]" value="${order_no}">
                </td>
                <td>${buyer}</td>
                <td>${order_no}</td>
                <td>${qty}</td>
                <td class="text-center p-0">
                    <button type="button"
                            class="btn btn-sm btn-custom danger remove-style"
                            data-style="${styleNo}"
                            data-buyer="${buyer}"
                            data-order_no="${order_no}"
                            data-qty="${qty}">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        $('.selected-style tbody').append(row);
        option.remove();
        select.val('');

        rowIndex++; // increment for next row
    });


    // ================= Remove Style =================
    $(document).on('click', '.remove-style', function () {
        let btn = $(this);
        let styleNo = btn.data('style');
        let buyer = btn.data('buyer');
        let qty = btn.data('qty');
        let order_no = btn.data('order_no');
        let option = `
            <option value="${styleNo}"
                    data-buyer="${buyer}"
                    data-qty="${qty}"
                    data-order_no="${order_no}">
                ${styleNo} | ${buyer} | ${qty}
            </option>
        `;
        $('.styleSelect').append(option);
        btn.closest('tr').remove();
    });

});
</script>
@endpush
