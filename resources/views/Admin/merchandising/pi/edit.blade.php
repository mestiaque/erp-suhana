@php
$advisingBank =
    'Beneficiary Bank :' . "\n" .
    'MODHUMOTI BANK PLC' . "\n" .
    'Uttara Branch' . "\n" .
    'Siaam Tower (Level-3)' . "\n" .
    'Plot : 15, Road : 02, Sector : 03' . "\n" .
    'Uttara, Dhaka-1230' . "\n" .
    'Bangladesh' . "\n" .
    'SWIFT CODE : MODHBDDHUT' . "\n" .
    'A/C NO. : 111011100000878';

@endphp


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
                                @if(count($orders) > 0)
                                    @foreach ($orders as $order)
                                    <option value="{{ $order->order_no }}" {{ $pi->order_no == $order->order_no ? 'selected' : '' }}>
                                        {{ $order->order_no }}
                                    </option>
                                    @endforeach
                                @else
                                    <option class="text-c" value="" disabled>No order found</option>
                                @endif
                            </select>
                        @else
                            <div type="text" value="" class="form-control " readonly>{{ $pi->order_no }}</div>
                            <input type="hidden" hidden value="{{ $pi->order_no }}" name="order_no" class="form-control d-none" readonly>
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Buyer Name</label>
                        <input type="text" readonly class="form-control buyer_name" placeholder="--" value="{{ $pi->buyer?->name ?? '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Merchant Name</label>
                        <input type="text" readonly class="form-control merchant_name"  placeholder="--" value="{{ $pi->merchant?->name ?? '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Proforma Invoice No</label>
                        <input type="text" class="form-control " value="{{ $pi->pi_no ?? '' }}" placeholder="Proforma Invoice No" name="pi_no" >
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Proforma Invoice Date</label>
                        <input type="date" class="form-control " value="{{ $pi->created_at->format('Y-m-d') ?? '' }}" placeholder="Proforma Invoice No" name="created_at">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Order Date</label>
                        <input type="date" class="form-control " value="{{ $pi?->order_date?->format('Y-m-d') ?? '' }}" placeholder="Proforma Invoice No" name="order_date">
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
                    <div class="col-md-12 mb-3">

                        <label>Advising Bank</label>
                        <textarea name="advising_bank" class="form-control advising_bank" rows="9" placeholder="Advising Bank readonly">{{ $pi->advising_bank ?? $advisingBank }}</textarea>
                    </div>
                </div>

                <br>
                <h5><b>Proforma Invoice Items</b></h5>
                <div class="cardItems">
                    @include(adminTheme().'merchandising.pi.includes.items', ['items' => $pi->items ?? []])
                </div>

                <div class="mt-4">
                    <label>Terms & Conditions</label>
                    <div id="payment-terms-list">
                        @php
                            // Predefined terms
                            $defaultTerms = [
                                'PAYMENT'           => 'LC AT SIGHT',
                                'BUYING HOUSE'      => 'SERVICE CHARGE : 3.5%',
                                'TRADE TERM'        => 'FOB, BY SEA',
                                'PORT OF LOADING'   => 'CHOTTROGRAM PORT, BANGLADESH',
                                'PORT OF DISCHARGE' => 'ANY PORT IN JAPAN',
                                'FINAL DESTINATION' => 'JAPAN',
                                'BILL OF LADING'    => 'FULL SET 3/3 SHIPPED ON BOARD CLEAN OCEAN BILL OF LADING OUT OF THE ORDER OF ANY BANK IN BANGLADESH AND ENDORSED TO THE LC ISSUING BANK MARKED FREIGHT COLLECT',
                                'PARTIAL SHIPMENT'  => 'ALLOWED',
                                'TRANSSHIPMENT'     => 'ALLOWED',
                                'TOLERANCE'         => '+/- 5%',
                                'DOCUMENTATION'     => 'AS PER LC TERMS',
                                'COUNTRY OF ORIGIN' => 'BANGLADESH'
                            ];

                            // Previously saved terms
                            $savedTerms = json_decode($pi->terms ?? '{}', true);
                            $allTerms = $defaultTerms;
                            foreach ($savedTerms as $key => $value) {
                                $allTerms[$key] = $value;
                            }
                            $termIndex = count($allTerms);
                        @endphp

                        <ul class="list-group" style="list-style:none;" id="termsUl">
                            @foreach($allTerms as $key => $value)
                                <li class="mb-2">
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox"
                                            name="terms[{{ $loop->index }}][checked]"
                                            class="form-control form-control-sm"
                                            style="width: 20px !important"
                                            {{ isset($savedTerms[$key]) ? 'checked' : '' }}> &nbsp;&nbsp;

                                        <input type="text"
                                            name="terms[{{ $loop->index }}][key]"
                                            value="{{ $key }}"
                                            class="form-control form-control-sm"
                                            style="width:30%; height: 24px;"> &nbsp;&nbsp;:&nbsp;&nbsp;

                                        <input type="text"
                                            name="terms[{{ $loop->index }}][value]"
                                            value="{{ $value }}"
                                            class="form-control form-control-sm"
                                            style="width:70%; height: 24px;">
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                    </div>

                    <button type="button" id="add-term-btn" class="btn btn-sm btn-success mt-2">+ Add Term</button>
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

<script>
$(document).ready(function() {
    let termIndex = {{ $termIndex }}; // শুরু index

    $('#add-term-btn').click(function() {
        const newLi = `
        <li class="mb-2">
            <div class="d-flex gap-2 align-items-center">
                <!-- checkbox -->
                <input type="checkbox"
                    name="terms[${termIndex}][checked]"
                    class="form-control form-control-sm" checked
                    style="width: 20px !important;" /> &nbsp;&nbsp;

                <!-- editable key -->
                <input type="text"
                    name="terms[${termIndex}][key]"
                    class="form-control form-control-sm"
                    style="width:30%; height:24px;"
                    placeholder="Key" /> &nbsp;&nbsp;:&nbsp;&nbsp;

                <!-- editable value -->
                <input type="text"
                    name="terms[${termIndex}][value]"
                    class="form-control form-control-sm"
                    style="width:70%; height:24px;"
                    placeholder="Value" />
            </div>
        </li>
        `;

        $('#termsUl').append(newLi); // prepend করা, list এর শুরুতে যাবে
        termIndex++; // index increment
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


