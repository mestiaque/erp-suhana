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
        <div class="card-header">
            <h3>Master Planning</h3>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')


            <form action="{{ isset($masterPlan) ? route('admin.productionPlanningAction', ['update', $masterPlan->id]) : route('admin.productionPlanningAction', ['store']) }}" method="POST">
                @csrf

                <div class="row">
                <div class="col-md-2 mb-3" style="border:1px solid #b4b4b4;">
                    <label for="planning_month">Planning Months <span class="text-danger">*</span></label>

                    <div class="selected-months-container mb-2">
                        @php
                            $monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                                        'July', 'August', 'September', 'October', 'November', 'December'];

                            $selectedMonths = [];

                            if (isset($masterPlan) && $masterPlan->planning_month) {
                                if (is_array($masterPlan->planning_month)) {
                                    $selectedMonths = $masterPlan->planning_month;
                                } else {
                                    $decoded = json_decode($masterPlan->planning_month, true);
                                    $selectedMonths = $decoded ?: [$masterPlan->planning_month];
                                }
                            } elseif (isset($planning_month)) {
                                $selectedMonths = [$planning_month];
                            }
                        @endphp
                        @if(count($selectedMonths) > 0)
                            @foreach($selectedMonths as $month)
                                <span class="badge badge-primary badge-pill mr-1 mb-1 selected-month-badge" data-month="{{ $month }}">
                                    {{ $monthNames[substr($month, 5, 2) - 1] . ' ' . substr($month, 0, 4) }}
                                    <i class="fa fa-times ml-1 remove-month" style="cursor:pointer;"></i>
                                </span>
                            @endforeach
                        @else
                            <span class="text-muted">No months selected</span>
                        @endif
                    </div>

                    <div class="month-selector-dropdown">
                        <input type="month" id="monthInput" class="form-control" placeholder="Select month">
                    </div>

                    <input type="hidden" name="planning_month" id="planningMonthsJson" value="{{ json_encode($selectedMonths) }}">
                    @error('planning_month')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                    {{-- LEFT : AVAILABLE COLORS --}}
                    <div class="col-md-5" style="border:1px solid #24e00b;">
                        <h6 class="mb-2">Available Colors</h6>

                        <div class="row style-grid" style="max-height:55vh; overflow-y:auto;">
                            @forelse($colors as $colorItem)
                                @php
                                    $pi_no = $colorItem?->orderDetail?->piItem?->pi?->pi_no ?? null;
                                    $pi_id = $colorItem?->orderDetail?->piItem?->proforma_invoice_id ?? null;
                                    $pi_item_id = $colorItem?->orderDetail?->piItem?->id ?? null;
                                    $key = $pi_id.'__'.$colorItem->style_no.'__'.$colorItem->order_no.'__'.$colorItem->color_name;
                                @endphp
                                <div class="col-md-6 mb-2 style-item"
                                     data-key="{{ $key }}"
                                     data-style="{{ $colorItem->style_no }}"
                                     data-buyer="{{ $colorItem->orderDetail->buyer_name ?? '' }}"
                                     data-order_no="{{ $colorItem->order_no }}"
                                     data-qty="{{ $colorItem->qty }}"
                                     data-pi_id="{{ $pi_id }}"
                                     data-pi_item_id="{{ $pi_item_id }}"
                                     data-pi_no="{{ $pi_no }}"
                                     data-colors='[{"color_name": "{{ $colorItem->color_name }}", "qty": {{ $colorItem->qty }}}]'>
                                    <div class="border p-2 rounded bg-light h-100 style-card">
                                        <strong>{{ $colorItem->style_no }}</strong><br>
                                        Buyer: {{ $colorItem->orderDetail->buyer_name ?? '--' }}<br>
                                        Order: {{ $colorItem->order_no }}<br>
                                        PI No : {{ $pi_no ?? '--' }}<br>
                                        <span class="text-primary">Color: {{ $colorItem->color_name }}</span><br>
                                        Qty: {{ $colorItem->qty }}
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted">No colors available for planning.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- RIGHT : SELECTED STYLES WITH COLORS --}}
                    <div class="col-md-5" style="border:1px solid #dce00b;">
                        <h6 class="mb-2">Selected Styles (Color-wise)</h6>

                        <div class="row selected-style-list" style="max-height:55vh; overflow-y:auto;">
                            @if(isset($masterPlan))
                                @foreach($masterPlan->productions as $index => $p)
                                    @php
                                        $key = $p->style_no.'__'.$p->order_no;
                                    @endphp
                                    <div class="col-md-6 mb-2 selected-item"
                                         data-key="{{ $key }}">
                                        <div class="border p-2 rounded h-100 style-card selected">
                                            <button type="button"
                                                    class="remove-btn remove-style"
                                                    data-key="{{ $key }}">
                                                <i class="fa fa-times"></i>
                                            </button>

                                            <strong>{{ $p->style_no }}</strong><br>
                                            Buyer: {{ $p->orderDetailItems->buyer_name ?? '--' }}<br>
                                            Order: {{ $p->order_no }}<br>
                                            PI No: {{ $p->pi_no ?? '--' }}<br>
                                            @if($p->color_name)
                                                <span class="text-primary">Color: {{ $p->color_name }}</span><br>
                                                Qty: {{ $p->color_qty ?? $p->style_qty }}
                                            @else
                                                Qty: {{ $p->style_qty ?? 0 }}
                                            @endif

                                            <input type="hidden" name="styles[{{ $index }}][style_no]" value="{{ $p->style_no }}">
                                            <input type="hidden" name="styles[{{ $index }}][order_no]" value="{{ $p->order_no }}">
                                            <input type="hidden" name="styles[{{ $index }}][pi_id]" value="{{ $p->pi_id }}">
                                            <input type="hidden" name="styles[{{ $index }}][pi_item_id]" value="{{ $p->pi_item_id }}">
                                            @if($p->color_name)
                                                <input type="hidden" name="styles[{{ $index }}][colors][0][color_name]" value="{{ $p->color_name }}">
                                                <input type="hidden" name="styles[{{ $index }}][colors][0][color_qty]" value="{{ $p->color_qty ?? $p->style_qty }}">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- SUBMIT --}}
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-check"></i>
                            {{ isset($masterPlan) ? 'Update Plan' : 'Create Plan' }}
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

@push('css')
<style>
    .style-card {
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease-in-out; /* smooth animation */
    }

    .style-card:hover {
        transform: translateY(-3px) scale(1.02); /* subtle raise & zoom */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); /* soft shadow */
    }

    .style-card.selected {
        border: 2px solid #28a745 !important;
        background: #f6fff9;
    }

    .remove-btn {
        position: absolute;
        bottom: 4px;
        right: 6px;
        border: none;
        background: transparent;
        color: #dc3545;
        font-size: 14px;
        cursor: pointer;
    }

    /* Optional: smooth scrollbar for left grid */
    .style-grid::-webkit-scrollbar {
        width: 6px;
    }
    .style-grid::-webkit-scrollbar-thumb {
        background: #bbb;
        border-radius: 3px;
    }

    /* Month selector styles */
    .selected-months-container {
        min-height: 40px;
        padding: 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background: #fff;
    }

    .selected-month-badge {
        display: inline-flex !important;
        align-items: center;
        padding: 5px 10px !important;
        font-size: 13px !important;
    }

    .selected-month-badge i {
        margin-left: 5px;
    }

    .selected-month-badge:hover {
        background-color: #0056b3 !important;
    }

    .month-selector-dropdown select {
        margin-bottom: 5px;
    }
</style>

@endpush

@push('js')
<script>
$(document).ready(function () {

    let rowIndex = {{ isset($masterPlan) ? count($masterPlan->productions) : 0 }};

    // ================================
    // ADD STYLE (Color-wise)
    // ================================
    $(document).on('click', '.style-item', function () {

        let item       = $(this);
        let key        = item.data('key');
        let styleNo    = item.data('style');
        let buyer      = item.data('buyer');
        let order_no   = item.data('order_no');
        let pi_id      = item.data('pi_id');
        let pi_item_id = item.data('pi_item_id');
        let pi_no      = item.data('pi_no');
        let qty        = item.data('qty');
        let colorsData = item.data('colors');

        // Prevent duplicate
        if ($('.selected-item[data-key="'+key+'"]').length) {
            alert('Already added');
            return;
        }

        // If color exists
        if (colorsData && colorsData.length > 0) {

            colorsData.forEach(function(color) {

                let html = `
                    <div class="col-md-6 mb-2 selected-item"
                         data-key="${key}">
                        <div class="border p-2 rounded h-100 style-card selected">
                            <button type="button"
                                    class="remove-btn remove-style"
                                    data-key="${key}">
                                <i class="fa fa-times"></i>
                            </button>

                            <strong>${styleNo}</strong><br>
                            Buyer: ${buyer}<br>
                            Order: ${order_no}<br>
                            PI No: ${pi_no ?? '--'}<br>
                            <span class="text-primary">Color: ${color.color_name}</span><br>
                            Qty: ${color.qty}

                            <input type="hidden" name="styles[${rowIndex}][style_no]" value="${styleNo}">
                            <input type="hidden" name="styles[${rowIndex}][order_no]" value="${order_no}">
                            <input type="hidden" name="styles[${rowIndex}][pi_id]" value="${pi_id}">
                            <input type="hidden" name="styles[${rowIndex}][pi_item_id]" value="${pi_item_id}">
                            <input type="hidden" name="styles[${rowIndex}][colors][0][color_name]" value="${color.color_name}">
                            <input type="hidden" name="styles[${rowIndex}][colors][0][color_qty]" value="${color.qty}">
                        </div>
                    </div>
                `;

                $('.selected-style-list').append(html);
                rowIndex++;
            });

        } else {

            // No color case
            let html = `
                <div class="col-md-6 mb-2 selected-item"
                     data-key="${key}">
                    <div class="border p-2 rounded h-100 style-card selected">
                        <button type="button"
                                class="remove-btn remove-style"
                                data-key="${key}">
                            <i class="fa fa-times"></i>
                        </button>

                        <strong>${styleNo}</strong><br>
                        Buyer: ${buyer}<br>
                        Order: ${order_no}<br>
                        Qty: ${qty}

                        <input type="hidden" name="styles[${rowIndex}][style_no]" value="${styleNo}">
                        <input type="hidden" name="styles[${rowIndex}][order_no]" value="${order_no}">
                        <input type="hidden" name="styles[${rowIndex}][pi_id]" value="${pi_id}">
                        <input type="hidden" name="styles[${rowIndex}][pi_item_id]" value="${pi_item_id}">
                    </div>
                </div>
            `;

            $('.selected-style-list').append(html);
            rowIndex++;
        }

        // Hide from available
        item.fadeOut(150);
    });


    // ================================
    // REMOVE STYLE
    // ================================
    $(document).on('click', '.remove-style', function () {

        let key = $(this).data('key');

        // Remove selected item
        $('.selected-item[data-key="'+key+'"]').remove();

        // Show again in available
        $('.style-item[data-key="'+key+'"]').fadeIn(150);
    });


    // =================================
    // MONTH MULTI SELECTION
    // =================================
    let selectedMonths = [];

    const initialValue = $('#planningMonthsJson').val();
    if (initialValue) {
        try {
            selectedMonths = JSON.parse(initialValue);
        } catch(e) {
            selectedMonths = initialValue ? [initialValue] : [];
        }
    }

    const monthNames = [
        'January','February','March','April','May','June',
        'July','August','September','October','November','December'
    ];

    function getMonthDisplayName(monthValue) {
        const year  = monthValue.substring(0, 4);
        const month = parseInt(monthValue.substring(5, 7));
        return monthNames[month - 1] + ' ' + year;
    }

    function renderSelectedMonths() {

        const container = $('.selected-months-container');

        if (selectedMonths.length > 0) {

            let html = '';

            selectedMonths.forEach(function(month) {
                html += `
                    <span class="badge badge-primary badge-pill mr-1 mb-1 selected-month-badge"
                          data-month="${month}">
                        ${getMonthDisplayName(month)}
                        <i class="fa fa-times ml-1 remove-month"
                           style="cursor:pointer;"></i>
                    </span>
                `;
            });

            container.html(html);

        } else {
            container.html('<span class="text-muted">No months selected</span>');
        }

        $('#planningMonthsJson').val(JSON.stringify(selectedMonths));
    }

    // Add month
    $('#monthInput').on('change', function() {

        const monthValue = $(this).val().trim();

        if (!monthValue) return;

        if (!/^\d{4}-\d{2}$/.test(monthValue)) {
            alert('Invalid month format');
            return;
        }

        if (selectedMonths.includes(monthValue)) {
            alert('This month is already selected');
            $(this).val('');
            return;
        }

        selectedMonths.push(monthValue);

        // Sort chronologically
        selectedMonths.sort();

        renderSelectedMonths();

        $(this).val('');
    });

    // Remove month
    $(document).on('click', '.remove-month', function() {

        const month = $(this).closest('.selected-month-badge').data('month');

        selectedMonths = selectedMonths.filter(m => m !== month);

        renderSelectedMonths();
    });

});
</script>
@endpush

@endsection
