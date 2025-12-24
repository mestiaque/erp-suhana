
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
                            <div type="text" value="" class="form-control " readonly>{{ $pi->buyer_name }}</div>
                            <input type="hidden" hidden value="{{ $pi->buyer_id }}" id="buyer_select" name="buyer_id" data-url="{{ route('admin.proformaInvoiceAction', ['buyer-select', $pi->id]) }}" class="form-control d-none" readonly>
                        @endif
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Order Numbers</label>
                        <input type="text" name="" class="order_no_show form-control" value="{{ $pi->order_no ?? '--' }}"  readonly>
                        <input type="text" name="order_no" class="order_no_show form-control" value="{{ $pi->order_no ?? '--' }}" hidden>
                    </div>

                    @include(adminTheme().'merchandising.pi.includes.defHead')

                </div>

                <br>
                <h5 class="d-flex align-items-center gap-2 mb-0">
                    <b>Proforma Invoice Items</b>

                    <select class="form-control form-control-sm" style="width: 400px; margin-left:20px"
                            name="order_no" placeholder="--select--"
                            id="order_no_select"
                            data-url="{{ route('admin.proformaInvoiceAction', ['po-select', $pi->id]) }}">
                        <option value="">-- Select Order --</option>
                    </select>
                    <div id="selectedPos" style="margin-left: 1rem">

                    </div>
                </h5>



                <div class="table-responsive" style="min-height: 100px;">
                    <table class="table table-bordered orderTable">
                        <thead>
                            <tr>
                                <th class="px-2 pb-1" style="width: 20px;">SL</th>
                                <th class="px-2 pb-1" style="width: 100px;">Order No</th>
                                <th class="px-2 pb-1" style="width: 100px;">Style No</th>
                                <th class="px-2 pb-1" style="width: 150px;">Fabrication</th>
                                <th class="px-2 pb-1" style="width: 80px;">Qnty</th>
                                <th class="px-2 pb-1" style="width: 80px;">Unit of Measurement</th>
                                <th class="px-2 pb-1" style="width: 80px;">Unit Price</th>
                                <th class="px-2 pb-1" style="width: 80px;">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody class="cardItems">
                            @include(adminTheme().'merchandising.pi.includes.items', ['items' => $pi->items ?? []])
                        </tbody>
                    </table>
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
    let selectedOrders = [];
    let existingOrders = $('.order_no_show').val();
    let buyer_id = $('#buyer_select').val();


    if (buyer_id) {
        loadBuyer(buyer_id, true);
    }

    if (existingOrders && existingOrders !== '--') {
        selectedOrders = existingOrders.split(',').map(o => o.trim());
        setTimeout(function () {
            selectedOrders.forEach(orderNo => {
                $('#order_no_select option[value="' + orderNo + '"]').remove();
                addOrderBadge(orderNo);
            });
        }, 500);
    }

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
        loadBuyer(buyer_id);
    });



    function loadBuyer(buyer_id, autoLoad=false) {
        if (!buyer_id) return;
        let $buyer = $('#buyer_select');
        let url = $buyer.data('url');

        if(autoLoad == false){
            // reset items table
            $('.cardItems').html(
                `<tr class="forced_hide">
                    <td colspan="8" class="text-center text-muted">No Items Found</td>
                </tr>`
            );
        }


        $('.forced_hide').removeClass('d-none');

        // reset order dropdown
        $('#order_no_select').html('<option value="">-- Select Order --</option>');

        $.get(url, { buyer_id: buyer_id }, function (res) {
            if (res.success) {
                $('#order_no_select').html(res.html);
            } else {
                // alert('No order found for this buyer');
                $('#order_no_select').html('<option value="">No order found</option>');
            }
        });
    }

    $(document).on('change', '#order_no_select', function () {
        let $select = $(this);
        let order_no = $select.val();
        if (!order_no) return;

        let url = $select.data('url');

        $.get(url, { order_no: order_no }, function (res) {
            if (res.success) {

                selectedOrders.push(order_no);

                $select.find('option[value="' + order_no + '"]').remove();

                $('.merchant_name').val(res.order.merchant_name);
                $('.order_date').val(res.order.order_date);

                $('.cardItems').append(res.html);
                reIndexItems();

                $('.order_no_show').val(selectedOrders.join(', '));
                $('.forced_hide').addClass('d-none');

                addOrderBadge(order_no);
                updateAllItems();

            } else {
                alert('Order not found');
            }
        });

        $select.val('');
    });


    function addOrderBadge(orderNo) {
        if ($('#selectedPos').find('[data-order="' + orderNo + '"]').length) return;

        let badge = `
            <span class="badge badge-info mr-2 mb-1" data-order="${orderNo}">
                ${orderNo}
                <a href="#" class="text-white ml-1 remove-po" data-order="${orderNo}">&times;</a>
            </span>
        `;
        $('#selectedPos').append(badge);
    }

    $(document).on('click', '.remove-po', function (e) {
        e.preventDefault();

        let orderNo = $(this).data('order');

        // 🔥 remove related rows (multiple tr possible)
        $('.itemRow').each(function () {
            let rowOrderNo = $(this).find('.itemRowOrderNo').val();
            console.log(rowOrderNo, orderNo);
            if (rowOrderNo === orderNo) {
                $(this).remove();
            }
        });

        // 🔁 selectedOrders array update
        selectedOrders = selectedOrders.filter(o => o !== orderNo);

        // update hidden/show input
        $('.order_no_show').val(selectedOrders.length ? selectedOrders.join(', ') : '--');

        // ❌ remove badge
        $(this).closest('span').remove();

        // 🔥 dropdown-এ আবার add করো (if not exists)
        if ($('#order_no_select option[value="' + orderNo + '"]').length === 0) {
            $('#order_no_select').append(
                `<option value="${orderNo}">${orderNo}</option>`
            );
        }

        // 🔥 সব PO remove হয়ে গেলে
        if ($('.itemRow').length === 0) {
            $('.cardItems').html(
                `<tr class="forced_hide">
                    <td colspan="8" class="text-center text-muted">No Items Found</td>
                </tr>`
            );
        } else {
            // reindex only if items exist
            reIndexItems();
        }
    });

    function reIndexItems() {
        $('.itemRow').each(function (index) {

            // serial
            $(this).find('td:first').text(index + 1);

            // input/select reindex
            $(this).find('input, select').each(function () {
                let name = $(this).attr('name');
                if (name) {
                    $(this).attr(
                        'name',
                        name.replace(/items\[\d+]/, 'items[' + index + ']')
                    );
                }
            });
        });
    }



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


