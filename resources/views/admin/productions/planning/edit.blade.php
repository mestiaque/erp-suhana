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

                    {{-- LEFT : AVAILABLE STYLES --}}
                    <div class="col-md-6">
                        <h6 class="mb-2">Available Styles</h6>

                        <div class="row style-grid" style="max-height:55vh; overflow-y:auto;">
                            @foreach($styles as $style)
                                @php
                                    $key = $style->style_no.'__'.$style->order_no;
                                @endphp
                                <div class="col-md-6 mb-2 style-item"
                                     data-key="{{ $key }}"
                                     data-style="{{ $style->style_no }}"
                                     data-buyer="{{ $style->buyer_name }}"
                                     data-order_no="{{ $style->order_no }}"
                                     data-qty="{{ $style->total_qty }}">
                                    <div class="border p-2 rounded bg-light h-100 style-card">
                                        <strong>{{ $style->style_no }}</strong><br>
                                        Buyer: {{ $style->buyer_name }}<br>
                                        Order: {{ $style->order_no }}<br>
                                        Qty: {{ $style->total_qty }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- RIGHT : SELECTED STYLES --}}
                    <div class="col-md-6">
                        <h6 class="mb-2">Selected Styles</h6>

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
                                            Buyer: {{ $p->orderDetailItems->buyer_name }}<br>
                                            Order: {{ $p->order_no }}<br>
                                            Qty: {{ $p->style_qty ?? 0 }}

                                            <input type="hidden" name="styles[{{ $index }}][style_no]" value="{{ $p->style_no }}">
                                            <input type="hidden" name="styles[{{ $index }}][order_no]" value="{{ $p->order_no }}">
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
</style>

@endpush

@push('js')
<script>
$(document).ready(function () {

    let rowIndex = {{ isset($masterPlan) ? count($masterPlan->productions) : 0 }};

    // ADD
    $(document).on('click', '.style-item', function () {

        let item = $(this);
        let key = item.data('key');
        let styleNo = item.data('style');
        let buyer = item.data('buyer');
        let order_no = item.data('order_no');
        let qty = item.data('qty');

        if ($('.selected-item[data-key="'+key+'"]').length) {
            alert('Already added');
            return;
        }

        let html = `
            <div class="col-md-6 mb-2 selected-item" data-key="${key}">
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
                </div>
            </div>
        `;

        $('.selected-style-list').append(html);
        item.hide();
        rowIndex++;
    });

    // REMOVE
    $(document).on('click', '.remove-style', function () {
        let key = $(this).data('key');
        $('.selected-item[data-key="'+key+'"]').remove();
        $('.style-item[data-key="'+key+'"]').show();
    });

});
</script>
@endpush

@endsection
