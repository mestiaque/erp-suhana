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
            <h3>Sample #<span class="text-primary">{{ $sample->id }}</span></h3>
        </div> --}}

        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.proformaInvoiceAction', ['update', $sample->id]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Buyer</label>
                        <input type="text" readonly class="form-control" value="{{ $sample->buyer_name }}{{ $sample->buyer->company_name ?? '' ? ' - ' . $sample->buyer->company_name : '' }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Merchandiser</label>
                        <input type="text" readonly class="form-control" value="{{ $sample->merchant_name }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Style</label>
                        <input type="text" readonly class="form-control" value="{{ $sample->style }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Type</label>
                        <input type="text" readonly class="form-control" value="{{ $sample->type }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Received Date *</label>
                        <input type="text" readonly class="form-control" value="{{ $sample?->received_at?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Delivery Date *</label>
                        <input type="text" readonly class="form-control" value="{{ $sample?->delivery_at?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Currency *</label>
                        <input type="text" readonly class="form-control" value="{{ $sample->currency }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>PI Status</label>
                        <select name="pi_status" class="form-control" data-url="{{ route('admin.proformaInvoiceAction',['update-head',$sample->id]) }}" required>
                            <option value="pending" {{$sample->pi_status=='pending'?'selected':''}} >Pending</option>
                            <option value="confirmed" {{$sample->pi_status=='confirmed'?'selected':''}} {{$sample->pi_status=='pending'?'selected':''}}>Confirmed</option>
                            <option value="approved" {{$sample->pi_status=='approved'?'selected':''}}>Approved</option>
                            <option value="cancel" {{$sample->pi_status=='cancel'?'selected':''}}>Cancel</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Invoice No *</label>
                        <input type="text" name="invoice_no" class="form-control updateHead" value="{{ $sample->invoice_no }}" placeholder="Payment Bank Details" data-name="invoice_no" data-url="{{ route('admin.proformaInvoiceAction',['update-head',$sample->id]) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>BIN Number *</label>
                        <input type="text" name="bin_number" class="form-control updateHead" value="{{ $sample->bin_number }}" placeholder="Payment Bank Details" data-name="bin_number" data-url="{{ route('admin.proformaInvoiceAction',['update-head',$sample->id]) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>JOB Number *</label>
                        <input type="text" name="job_number" class="form-control updateHead" value="{{ $sample->job_number }}" placeholder="Payment Bank Details" data-name="job_number" data-url="{{ route('admin.proformaInvoiceAction',['update-head',$sample->id]) }}" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Payment Bank Details *</label>
                        <input type="text" name="payment_bank_details" class="form-control updateHead" value="{{ $sample->payment_bank_details }}" placeholder="Payment Bank Details" data-name="payment_bank_details" data-url="{{ route('admin.proformaInvoiceAction',['update-head',$sample->id]) }}" required>
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
                        @if($sample->payment_terms)
                        {!! $sample->payment_terms !!}
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
