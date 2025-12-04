@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Proforma Invoice Edit') }}</title>
@endsection

@push('css')
<style>
    .search-result-box{position:absolute;z-index:9;width:100%;background:#fff;border:1px solid #ddd;display:none;}
    .search-result-box li{padding:6px 10px;cursor:pointer;}
    .searchlist ul {list-style:none;margin:0;padding:0;}
    .searchlist ul li{border-top:1px solid #dbd6d6;padding:5px 10px;cursor:pointer;}
    .searchlist ul li:hover{background:#f2f2f2;}
    .searchGrid {position:relative;}
    .itemSearch {height:200px;overflow:auto;position:absolute;width:100%;background:white;border:1px solid #dfdfdf;border-top:0;display:none;}
</style>
@endpush

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
                        <label>Order Number</label>
                        @if(is_null($pi->order_no))
                            <select class="form-control" name="order_no" id="order_no_select" data-url="{{ route('admin.proformaInvoiceAction', ['po-select', $pi->id]) }}">
                                <option value="">-- Select Order Number --</option>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->order_no }}" {{ $pi->order_no == $order->order_no ? 'selected' : '' }}>
                                        {{ $order->order_no }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div type="text" value="" class="form-control " readonly>{{ $pi->order_no }}</div>
                            <input type="hidden" hidden value="{{ $pi->order_no }}" name="order_no" class="form-control d-none" readonly>
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Buyer Name</label>
                        <input type="text" readonly class="form-control buyer_name" value="{{ $pi->buyer?->name ?? '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Merchant Name</label>
                        <input type="text" readonly class="form-control merchant_name" value="{{ $pi->merchant?->name ?? '' }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Remarks</label>
                        {{-- <input type="text" name="remarks" class="form-control remarks" value="{{ $pi->remarks ?? '' }}"> --}}
                        <textarea name="remarks" class="form-control remarks" rows="1" placeholder="Remarks">{{ $pi->remarks ?? '' }}</textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Created By*</label>
                        <input type="text" readonly class="form-control" value="{{ $pi->user?->name ?? '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="pending" {{ $pi->status=='pending'?'selected':'' }}>Pending</option>
                            <option value="confirmed" {{ $pi->status=='confirmed'?'selected':'' }}>Confirmed</option>
                            <option value="approved" {{ $pi->status=='approved'?'selected':'' }}>Approved</option>
                            <option value="cancel" {{ $pi->status=='cancel'?'selected':'' }}>Cancel</option>
                        </select>
                    </div>
                </div>

                <br>
                <h5><b>Proforma Invoice Items</b></h5>
                <div class="cardItems">
                    @include(adminTheme().'merchandising.pi.includes.items', ['items' => $pi->items ?? []])
                </div>

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
        let commission = parseFloat(row.find('input[name*="[commission]"]').val()) || 0;
        let commissionType = row.find('.commission_type').val();

        // Total Price
        let totalPrice = qty * unitPrice;
        row.find('input[name*="[total_price]"]').val(totalPrice.toFixed(2));

        // Total Commission
        let totalCommission = 0;
        if (commissionType === 'percentage') {
            totalCommission = totalPrice * (commission / 100);
        } else if (commissionType === 'per_pcs') {
            totalCommission = qty * commission;
        }
        row.find('input[name*="[total_commission]"]').val(totalCommission.toFixed(2));
    }

    // === Update all rows and summary ===
    function updateAllItems() {
        let totalQty = 0;
        let totalAmount = 0;
        let totalCommission = 0;

        $('.itemRow').each(function() {
            let row = $(this);
            updateItemRow(row);

            totalQty += parseFloat(row.find('.qty').val()) || 0;
            totalAmount += parseFloat(row.find('input[name*="[total_price]"]').val()) || 0;
            totalCommission += parseFloat(row.find('input[name*="[total_commission]"]').val()) || 0;
        });

        $('.totalQty').text(totalQty);
        $('.totalAmount').text(totalAmount.toFixed(2));
        $('.totalCommission').text(totalCommission.toFixed(2));
    }

    // === Initial calculation on page load ===
    updateAllItems();

    // === Trigger recalculation when any relevant input changes ===
    $(document).on('input change', '.updateItem, .commission_type, .qty', function() {
        updateAllItems();
    });

    // === Load items when PO changes ===
    $(document).on('change', '#order_no_select', function() {
        let order_no = $(this).val();
        if (!order_no) return;

        let url = $(this).data('url');

        $.get(url, { order_no: order_no }, function(res) {
            if (res.success) {
                $('.buyer_name').val(res.order.buyer_name)
                $('.merchant_name').val(res.order.merchant_name)
                $('.cardItems').html(res.html);
                updateAllItems();
            } else {
                alert('Order not found');
                $('.cardItems').html('<tr><td colspan="13" class="text-center">No Items</td></tr>');
            }
        });
    });

});
</script>


@endpush
