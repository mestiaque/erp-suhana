
@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Proforma Invoice Edit') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Edit Proforma Invoice</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.proformaInvoice') }}">Proforma Invoices</a></li>
            <li class="item">Edit Proforma Invoice</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')
            <form action="{{ route('admin.proformaInvoiceAction', ['update', $pi->id]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Buyer</label>
                        @if(is_null($pi->buyer_id))
                            <select class="form-control" name="buyer_id" id="buyer_select" data-url="{{ route('admin.proformaInvoiceAction', ['buyer-select', $pi->id]) }}">
                                <option value="">-- Select Buyer --</option>
                                @if(count($buyers) > 0)
                                    @foreach ($buyers as $buyer)
                                    <option value="{{ $buyer->id }}" {{ $pi->buyer_id == $buyer->id ? 'selected' : '' }}>
                                        {{ $buyer->name }}
                                    </option>
                                    @endforeach
                                @else
                                    <option class="text-c" value="" disabled>No buyer found</option>
                                @endif
                            </select>
                        @else
                            <div type="text" value="" class="form-control " readonly>{{ $pi->buyer_id }}</div>
                            <input type="hidden" hidden value="{{ $pi->buyer_id }}" name="buyer_id" class="form-control d-none" readonly>
                        @endif
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Order Number</label>
                        @if(is_null($pi->order_no))
                        <select class="form-control" name="order_no" id="order_no_select"
                                data-url="{{ route('admin.proformaInvoiceAction', ['po-select', $pi->id]) }}">
                            <option value="">-- Select Order Number --</option>
                        </select>
                        @else
                            <div type="text" value="" class="form-control " readonly>{{ $pi->order_no }}</div>
                            <input type="hidden" hidden value="{{ $pi->order_no }}" name="order_no" class="form-control d-none" readonly>
                        @endif
                        {{-- <select class="form-control" name="order_no" id="order_no_select"
                                data-url="{{ route('admin.proformaInvoiceAction', ['po-select', $pi->id]) }}">
                            <option value="">-- Select Order Number --</option>
                        </select> --}}

                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Merchant Name</label>
                        <input type="text" readonly class="form-control merchant_name"  placeholder="--" value="{{ $pi->merchant?->name ?? '' }}">
                    </div>
                    @include(adminTheme().'merchandising.pi.includes.defHead')

                </div>

                <br>
                <h5><b>Proforma Invoice Items</b></h5>
                <div class="cardItems">
                    @include(adminTheme().'merchandising.pi.includes.items', ['items' => $pi->items ?? []])
                </div>

                @include(adminTheme().'merchandising.pi.includes.terms')


                <br>
                <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Update Proforma Invoice</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {

    // === Update single row calculation ===
    function updateItemRow(row) {
        let qty = parseFloat(row.find('.qty').val()) || 0;
        let unitPrice = parseFloat(row.find('input[name*="[unit_price]"]').val()) || 0;
        let commissionType = row.find('.commission_type').val();

        // Total Price
        let totalPrice = qty * unitPrice;
        row.find('input[name*="[total_price]"]').val(totalPrice.toFixed(2));

    }

    // === Update all rows and summary ===
    function updateAllItems() {
        let totalQty = 0;
        let totalAmount = 0;

        $('.itemRow').each(function() {
            let row = $(this);
            updateItemRow(row);

            totalQty += parseFloat(row.find('.qty').val()) || 0;
            totalAmount += parseFloat(row.find('input[name*="[total_price]"]').val()) || 0;
        });

        $('.totalQty').text(totalQty);
        $('.totalAmount').text(totalAmount.toFixed(2));
    }

    // === Initial calculation on page load ===
    updateAllItems();

    // === Trigger recalculation when any relevant input changes ===
    $(document).on('input change', '.updateItem, .qty', function() {
        updateAllItems();
    });

    // === Load items when PO changes ===
    $(document).on('change', '#buyer_select', function () {
        let buyer_id = $(this).val();
        if (!buyer_id) return;

        let url = $(this).data('url');

        $.get(url, { buyer_id: buyer_id }, function (res) {
            if (res.success) {
                $('#order_no_select').html(res.html);
                // $('.cardItems').html('');
            } else {
                alert('No order found for this buyer');
                $('#order_no_select').html('<option value="">No order found</option>');
            }
        });
    });


    $(document).on('change', '#order_no_select', function () {
        let order_no = $(this).val();
        if (!order_no) return;

        let url = $(this).data('url');

        $.get(url, { order_no: order_no }, function (res) {
            if (res.success) {
                $('.merchant_name').val(res.order.merchant_name);
                $('.order_date').val(res.order.order_date);
                $('.cardItems').html(res.html);
                updateAllItems();
            } else {
                alert('Order not found');
            }
        });
    });


});
</script>


@endpush

@push('css')
<style>
    #termsUl li:nth-child(odd) {
        background-color: #f8f9fa; /* হালকা ধূসর */
    }

    #termsUl li:nth-child(even) {
        background-color: #ffffff; /* সাদা */
    }
</style>
@endpush


