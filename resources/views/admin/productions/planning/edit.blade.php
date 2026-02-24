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
            <li class="item"><a href="{{ route('admin.productionPlanning') }}">Master Planning</a></li>
            <li class="item">{{ isset($masterPlan) ? 'Edit Master Planning' : 'Create Master Planning' }}</li>
        </ol>
    </div>

    <div class="card mb-30 main-card">

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
                                <div class="col-md-4 mb-3 style-item"
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
                                        <table class="w-100 style-info-table">
                                            <tr><td class="text-muted">Style</td><td class="font-weight-bold">{{ $colorItem->style_no }}</td></tr>
                                            <tr><td class="text-muted">Buyer</td><td>{{ $colorItem->orderDetail->buyer_name ?? '--' }}</td></tr>
                                            <tr><td class="text-muted">Order</td><td>{{ $colorItem->order_no }}</td></tr>
                                            <tr><td class="text-muted">PI No</td><td>{{ $pi_no ?? '--' }}</td></tr>
                                            <tr><td class="text-muted">Color</td><td class="text-primary">{{ $colorItem->color_name }}</td></tr>
                                            <tr><td class="text-muted">Qty</td><td class="text-success font-weight-bold">{{ number_format($colorItem->qty) }}</td></tr>
                                        </table>
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
                                    <div class="col-md-4 mb-3 selected-item"
                                         data-key="{{ $key }}">
                                        <div class="border p-2 rounded h-100 style-card selected">
                                                <button type="button"
                                                        class="remove-btn remove-style"
                                                        data-key="{{ $key }}">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                <table class="w-100 style-info-table">
                                                    <tr><td class="text-muted">Style</td><td class="font-weight-bold">{{ $p->style_no }}</td></tr>
                                                    <tr><td class="text-muted">Buyer</td><td>{{ $p->orderDetailItems->buyer_name ?? '--' }}</td></tr>
                                                    <tr><td class="text-muted">Order</td><td>{{ $p->order_no }}</td></tr>
                                                    <tr><td class="text-muted">PI No</td><td>{{ $p->pi_no ?? '--' }}</td></tr>
                                                    @if($p->color_name)
                                                        <tr><td class="text-muted">Color</td><td class="text-primary">{{ $p->color_name }}</td></tr>
                                                        <tr><td class="text-muted">Qty</td><td class="text-success font-weight-bold">{{ number_format($p->color_qty ?? $p->style_qty) }}</td></tr>
                                                    @else
                                                        <tr><td class="text-muted">Qty</td><td class="text-success font-weight-bold">{{ number_format($p->style_qty ?? 0) }}</td></tr>
                                                    @endif
                                                </table>

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
                    <div class="col-md-12 mt-3 text-right">
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
    .main-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 18px 25px;
    }
    .card-header h3 {
        font-weight: 700;
        font-size: 1.5rem;
        color: #fff;
        margin: 0;
    }
    .row > div {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px 15px;
    }
    .col-md-5 {
        border: none !important;
    }
    .col-md-2.mb-3 {
        border: none !important;
    }
    .style-card {
        position: relative;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 14px;
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        border: 2px solid #e2e8f0;
        min-height: 140px;
        font-size: 14px;
        padding: 16px;
    }
    .style-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 12px 28px rgba(49, 130, 206, 0.25);
        border-color: #3182ce;
    }
    .style-card.selected {
        border: 2px solid #38a169 !important;
        background: linear-gradient(145deg, #f0fff4 0%, #c6f6d5 100%);
    }
    .style-info-table {
        font-size: 12px;
    }
    .style-info-table td {
        padding: 3px 5px;
        border-bottom: 1px solid #eee;
    }
    .style-info-table td:first-child {
        color: #718096;
        width: 25%;
    }
    .style-info-table td:last-child {
        color: #2d3748;
        font-weight: 600;
    }
    .remove-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: none;
        background: #fed7d7;
        color: #e53e3e;
        font-size: 14px;
        cursor: pointer;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .remove-btn:hover {
        background: #e53e3e;
        color: white;
        transform: scale(1.15);
    }
    .style-grid::-webkit-scrollbar {
        width: 6px;
    }
    .style-grid::-webkit-scrollbar-thumb {
        background: #bbb;
        border-radius: 3px;
    }
    .selected-months-container {
        min-height: 40px;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        background: #f9fafb;
        margin-bottom: 8px;
    }
    .selected-month-badge {
        display: inline-flex !important;
        align-items: center;
        padding: 6px 14px !important;
        font-size: 14px !important;
        border-radius: 8px;
        background: #3182ce !important;
        color: #fff !important;
        margin-right: 6px;
        margin-bottom: 6px;
        box-shadow: 0 2px 6px rgba(60,60,90,0.07);
    }
    .selected-month-badge i {
        margin-left: 7px;
        font-size: 11px;
    }
    .selected-month-badge:hover {
        background-color: #5a6fd6 !important;
    }
    .month-selector-dropdown select {
        margin-bottom: 5px;
    }
    .col-md-2.mb-3 label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }
    .col-md-5 h6 {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 15px;
        font-size: 1.15rem;
        padding-bottom: 10px;
        border-bottom: 2px solid #e2e8f0;
    }
    .btn.btn-success {
        font-size: 1.1rem;
        padding: 12px 32px;
        border-radius: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(72, 187, 120, 0.4);
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        border: none;
    }
    .btn.btn-success:hover {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(72, 187, 120, 0.5);
    }
    .main-card .row {
        margin-left: 0;
        margin-right: 0;
    }
    @media (max-width: 900px) {
        .main-card {
            padding: 12px 4px;
        }
        .row > div {
            padding: 8px 4px;
        }
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
                    <div class="col-md-4 mb-3 selected-item"
                         data-key="${key}">
                        <div class="border p-2 rounded h-100 style-card selected">
                            <button type="button"
                                    class="remove-btn remove-style"
                                    data-key="${key}">
                                <i class="fa fa-times"></i>
                            </button>
                            <table class="w-100 style-info-table">
                                <tr><td class="text-muted">Style</td><td class="font-weight-bold">${styleNo}</td></tr>
                                <tr><td class="text-muted">Buyer</td><td>${buyer}</td></tr>
                                <tr><td class="text-muted">Order</td><td>${order_no}</td></tr>
                                <tr><td class="text-muted">PI No</td><td>${pi_no ?? '--'}</td></tr>
                                <tr><td class="text-muted">Color</td><td class="text-primary">${color.color_name}</td></tr>
                                <tr><td class="text-muted">Qty</td><td class="text-success font-weight-bold">${color.qty}</td></tr>
                            </table>

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
                <div class="col-md-4 mb-3 selected-item"
                     data-key="${key}">
                    <div class="border p-2 rounded h-100 style-card selected">
                        <button type="button"
                                class="remove-btn remove-style"
                                data-key="${key}">
                            <i class="fa fa-times"></i>
                        </button>
                        <table class="w-100 style-info-table">
                            <tr><td class="text-muted">Style</td><td class="font-weight-bold">${styleNo}</td></tr>
                            <tr><td class="text-muted">Buyer</td><td>${buyer}</td></tr>
                            <tr><td class="text-muted">Order</td><td>${order_no}</td></tr>
                            <tr><td class="text-muted">Qty</td><td class="text-success font-weight-bold">${qty}</td></tr>
                        </table>

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
