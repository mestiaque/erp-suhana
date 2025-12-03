@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('PI Edit') }}</title>
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
        <h1>Edit PI</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.proformaInvoice') }}">Proforma Invoices</a></li>
            <li class="item">Edit PI</li>
        </ol>
    </div>

    <div class="card mb-30">
        {{-- <div class="card-header">
            <h3>Sample #<span class="text-primary">{{ $order->id }}</span></h3>
        </div> --}}

        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.proformaInvoiceAction', ['update', $order->id]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>PO Number</label>
                        <select class="form-control updateHead2" po_number>
                            <option value=""> Select Number</option>
                            <option value="02154"> 01251</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Status</label>
                        <select name="pi_status" class="form-control" data-url="{{ route('admin.proformaInvoiceAction',['update-head',$order->id]) }}" required>
                            <option value="pending" {{$order->pi_status=='pending'?'selected':''}} >Pending</option>
                            <option value="confirmed" {{$order->pi_status=='confirmed'?'selected':''}} {{$order->pi_status=='pending'?'selected':''}}>Confirmed</option>
                            <option value="approved" {{$order->pi_status=='approved'?'selected':''}}>Approved</option>
                            <option value="cancel" {{$order->pi_status=='cancel'?'selected':''}}>Cancel</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Created By*</label>
                        <input type="text"  readonly="" class="form-control" value="{{ $order->user?->name?? '' }}" placeholder="Created By">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Payment Bank Details *</label>
                        <input type="text" name="payment_bank_details" class="form-control updateHead" value="{{ $order->payment_bank_details }}" placeholder="Payment Bank Details" data-name="payment_bank_details" data-url="{{ route('admin.proformaInvoiceAction',['update-head',$order->id]) }}" required>
                    </div>
                </div>

                <br>
                <h5><b>PI Items</b></h5>

                <div class="cardItems">
                    @include(adminTheme().'merchandising.pi.includes.items')
                </div>

                <div style="" class="mt-4">
                    <h5 style=""><u>Payment Terms:</u></h5>
                    <div style="border: 1px solid #80808045; padding: 1rem 2rem;">
                        @if($order->payment_terms)
                        {!! $order->payment_terms !!}
                        @else
                        <p class="m-0 w-100 text-center"><i>No terms found</i></p>
                        @endif
                    </div>
                </div>

                <br>
                <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Update PI</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // === Live calculation & total quantity update ===
    function updateTotalSummary() {
        let totalQty = 0;
        let totalAmount = 0;
        let totalDiscount = 0;

        $('.itemRow').each(function() {
            let qty = parseFloat($(this).find('.qty').val()) || 0;
            let unitPrice = parseFloat($(this).find('.unit-price').val()) || 0;
            let discount = parseFloat($(this).find('.discount').val()) || 0;

            // calculate amount = qty * unitPrice
            let amount = qty * unitPrice;

            // set amount field (readonly)
            $(this).find('.amount').val(amount.toFixed(2));

            // sum totals
            totalQty += qty;
            totalAmount += amount;
            totalDiscount += discount;
        });

        // update totals in table footer
        $('.totalQty').text(totalQty);
        $('.totalAmount').text(totalAmount.toFixed(2));
        $('.totalDiscount').text(totalDiscount.toFixed(2));
    }

    // call on page load
    updateTotalSummary();

    // -------------------------
    // Update Item Fields
    // -------------------------
    $(document).on('change','.updateHead', function(){
        let url = $(this).data('url');
        let name = $(this).data('name');
        let value = $(this).val();
        $.get(url, {field: name, value: value}, function(res){
            if(res.success){
            }else{
                alert(res.message)
                if(res.field){
                    $('input[name="'+res.field+'"]').val('');
                }
            }
        });
    });

    // -------------------------
    // Update Item Fields
    // -------------------------
    $(document).on('change','.updateItem', function(){
        let url = $(this).data('url');
        let name = $(this).data('name');
        let value = $(this).val();
        $.get(url, {field: name, value: value}, function(res){
            updateTotalSummary();
            if(res.success){
            }
        });
    });


</script>
@endpush
