@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Edit Order Details') }}</title>
@endsection

@push('css')
<style>

    .section-title{
        background:#f4f4f4;
        padding:8px 12px;
        font-weight:600;
        margin-bottom:10px;
        border-left:4px solid #007bff;
        border-radius:4px;
    }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Edit Order Details</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.orderDetails') }}">Order Details</a></li>
            <li class="item">Edit</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')
            @php
                $fabrications = App\Models\Attribute::where('type', 11)->where('status', 'active')->pluck('name');
                $compositions = App\Models\Attribute::where('type', 12)->where('status', 'active')->pluck('name');
            @endphp

            <form action="{{ route('admin.orderDetailsAction',['update',$orderDetails->id]) }}" method="POST">
                @csrf

                <div class="row">

                    <!-- LEFT SIDE -->
                    <div class="col-md-6">

                        <div class="section-title">Order Main Information</div>

                        <div class="row">

                            <!-- Buyer -->
                            <div class="col-md-6 mb-3">
                                <label>Buyer *</label>
                                <div class="input-group">
                                    <select name="buyer" id="buyerSelect" class="form-control updateHead"
                                        data-name="buyer"
                                        data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                                        required>
                                        <option value="">-- Select Buyer --</option>
                                        @foreach($buyers as $s)
                                            <option value="{{ $s->id }}" {{ $orderDetails->buyer_id == $s->id ? 'selected':'' }}>
                                                {{ $s->name }} {{ $s->company_name?'- '.$s->company_name:'' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" id="openAddBuyer" class="btn btn-primary px-3">
                                            <i class="bx bx-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Brand -->
                            <div class="col-md-6 mb-3">
                                <label>Brand / Customer *</label>
                                <input type="text" name="company_name" class="form-control updateHead"
                                       value="{{ $orderDetails->company_name }}"
                                       data-name="company_name" placeholder="Brand / Customer"
                                       data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                                       required>
                            </div>

                            <!-- Merchandiser -->
                            <div class="col-md-6 mb-3">
                                <label>Merchandiser * <a href="{{ route('admin.merchandisers') }}" target="_blank"><i class="fa fa-external-link"></i></a></label>
                                <select name="merchant" class="form-control updateHead"
                                        data-name="merchant"
                                        data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                                        required>
                                    <option value="">-- Select Merchandiser --</option>
                                    @foreach($merchandisers as $m)
                                        <option value="{{ $m->id }}" {{ $orderDetails->merchant_id==$m->id?'selected':'' }}>
                                            {{ $m->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Style -->
                            <div class="col-md-6 mb-3">
                                <label>Style *</label>
                                <input type="text" name="style_no" class="form-control updateHead"
                                       value="{{ $orderDetails->style_no }}"
                                       data-name="style_no" placeholder="Style"
                                       required
                                       data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                            </div>

                            <!-- Order No -->
                            <div class="col-md-6 mb-3">
                                <label>Order/PO No *</label>
                                <input type="text" name="order_no" class="form-control updateHead"
                                       value="{{ $orderDetails->order_no }}"
                                       data-name="order_no" placeholder="Order/PO No"
                                       required
                                       data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                            </div>

                            <!-- Order Qty -->
                            <div class="col-md-6 mb-3">
                                <label>Order Qty *</label>
                                <input type="number" name="total_qty" class="form-control total_qty updateHead"
                                       value="{{ $orderDetails->total_qty }}" readonly
                                       data-name="total_qty" placeholder="Order Quantity"
                                       required
                                       data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                            </div>

                            <!-- Dates / Fields -->
                            <div class="col-md-6 mb-3">
                                <label>Shipment Date *</label>
                                <input type="date" name="shipment_date" class="form-control updateHead"
                                       value="{{ $orderDetails->shipment_date ? \Carbon\Carbon::parse($orderDetails->shipment_date)->format('Y-m-d') : '' }}"
                                       data-name="shipment_date"
                                       required
                                       data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Composition *</label>
                                <select name="composition" class="form-control updateHead"
                                        data-name="composition"
                                        required
                                        data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                                    <option value="">-- Select Composition --</option>
                                    @foreach($compositions as $comp)
                                        <option value="{{ $comp }}" {{ $orderDetails->composition == $comp ? 'selected' : '' }}>
                                            {{ $comp }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Fabrication *</label>
                                <select name="fabrication" class="form-control updateHead"
                                        data-name="fabrication"
                                        required
                                        data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                                    <option value="">-- Select fabrication --</option>
                                    @foreach($fabrications as $comp)
                                        <option value="{{ $comp }}" {{ $orderDetails->fabrication == $comp ? 'selected' : '' }}>
                                            {{ $comp }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- GSM -->
                            <div class="col-md-6 mb-3">
                                <label>GSM *</label>
                                <input type="text" name="gsm" class="form-control updateHead"
                                       value="{{ $orderDetails->gsm }}"
                                       data-name="gsm" placeholder="GSM"
                                       required
                                       data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label>Status *</label>
                                <select name="status" class="form-control updateHead"
                                        data-name="status"
                                        required
                                        data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                                    <option value="pending" {{ $orderDetails->status=="pending"?'selected':'' }}>Pending</option>
                                    <option value="confirmed" {{ $orderDetails->status=="confirmed"?'selected':'' }}>Confirmed</option>
                                    <option value="completed" {{ $orderDetails->status=="completed"?'selected':'' }}>Completed</option>
                                    <option value="cancelled" {{ $orderDetails->status=="cancelled"?'selected':'' }}>Cancelled</option>
                                </select>
                            </div>

                                                        <!-- Remarks -->
                            <div class="col-md-6 mb-3">
                                <label>Remarks</label>
                                <textarea name="remarks" rows="1" class="form-control updateHead"
                                          data-name="remarks" placeholder="Remarks"
                                          data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">{{ $orderDetails->remarks }}</textarea>
                            </div>


                        </div>
                        <br>
                        <button type="submit" class="btn btn-success ">
                            <i class="bx bx-check"></i> Save Updated Order
                        </button>
                    </div>

                    <!-- RIGHT SIDE: COLORS -->
                    <div class="col-md-6">

                        <div class="section-title" style="margin-bottom: 9px !important;">Color & Quantity</div>


                        <div id="colorQtyArea">
                                    <div class="color-row d-flex align-items-center" style="margin-bottom: 12px !important;">

                                        <input type="text" value="Composition*" class="form-control mr-2 p-0" style="height: 20px; border:none; color:#2a2a2a">
                                        <input type="text" value="Color Name*" class="form-control mr-2 p-0" style="height: 20px; border:none; color:#2a2a2a">
                                        <input type="text" value="Color Qty*" class="form-control mr-2 p-0" style="height: 20px; border:none; color:#2a2a2a">
                                    </div>
                                @if($orderDetails->items && $orderDetails->items->count())
                                    @foreach($orderDetails->items as $item)
                                        <div class="color-row mb-2 d-flex align-items-center">

                                            <select name="compositions[{{$item->id}}]" class="form-control mr-2 updateItem color-composition"
                                                    data-name="composition"
                                                    data-url="{{ route('admin.orderDetailsAction',['update-item',$item->id]) }}">
                                                <option value="">-- Select Composition --</option>
                                                @foreach($compositions as $comp)
                                                    <option value="{{ $comp }}"
                                                        {{ $item->composition ? ($item->composition == $comp ? 'selected' : '') : ($orderDetails->composition == $comp ? 'selected' : '') }}>
                                                        {{ $comp }}
                                                    </option>
                                                @endforeach
                                            </select>


                                            <input type="text" name="colors[{{$item->id}}]" class="form-control mr-2 updateItem" data-name="color_name" data-url="{{route('admin.orderDetailsAction',['update-item',$item->id]) }}"
                                                value="{{ $item->color_name }}" placeholder="Color" required>

                                            <input type="number" name="qtys[{{$item->id}}]" class="form-control mr-2 updateItem" data-name="qty" data-url="{{ route('admin.orderDetailsAction',['update-item',$item->id]) }}"
                                                value="{{ ($item->qty && $item->qty > 0) ? $item->qty : '' }}" placeholder="Qty" required>

                                            <button type="button" class="btn-custom danger removeRow" data-url="{{ route('admin.orderDetailsAction',['remove-item',$item->id]) }}">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else

                                @endif

                        </div>

                        <div class="w-100 text-right">
                            <a type="button" id="addColorRow" class="btn-custom btn-sm" data-url="{{ route('admin.orderDetailsAction',['add-item',$orderDetails->id]) }}">
                                <i class="bx bx-plus"></i>
                            </a>
                        </div>

                    </div>

                </div>



            </form>

        </div>
    </div>

    <!-- Buyer Modal -->
     @include(adminTheme().'merchandising.orderDetails.add-buyer')

</div>
@endsection


@push('js')
<script>

$("#addColorRow").click(function () {
    let url = $(this).data('url');
    let mainComposition = "{{ $orderDetails->composition }}"; // main order composition
    $.get(url, function(res){
        if(res.success){
            let removeUrl = `/admin/order-details/remove-item/${res.id}`;
            let updateUrl = `/admin/order-details/update-item/${res.id}`;
            let compOptions = `
                <option value="">-- Select Composition --</option>
                @foreach($compositions as $comp)
                    <option value="{{ $comp }}" ${mainComposition == "{{ $comp }}" ? 'selected' : ''}>{{ $comp }}</option>
                @endforeach
            `;
            let row = `
                <div class="color-row mb-2 d-flex align-items-center">
                    <select name="compositions[${res.id}]" class="form-control mr-2 updateItem color-composition" data-name="composition" data-url="${updateUrl}">
                        ${compOptions}
                    </select>
                    <input type="text" name="colors[${res.id}]" class="form-control mr-2 updateItem" placeholder="Color" data-name="color_name" data-url="${updateUrl}" required>
                    <input type="number" name="qtys[${res.id}]" class="form-control mr-2 updateItem" placeholder="Qty" data-name="qty" data-url="${updateUrl}" required>
                    <button type="button" class="btn-custom danger removeRow" data-url="${removeUrl}">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            `;
            $("#colorQtyArea").append(row);
        }
    });
});


// REMOVE ROW
$(document).on('click', '.removeRow', function () {
    let button = $(this); // store reference
    let url = button.data('url');
    $.get(url, function(res){
        if(res.success){
            button.closest('.color-row').remove();
            calculateTotalQty();
        }
    });
});


// Update header fields via AJAX
$(document).on('change','.updateHead', function(){
    let url = $(this).data('url');
    $.get(url, {
        field: $(this).data('name'),
        value: $(this).val()
    });
});


$(document).on('change','.updateItem', function(){
    let url = $(this).data('url');
    $.get(url, {
        field: $(this).data('name'),
        value: $(this).val()
    });
});

// Open Add Buyer Modal
$('#openAddBuyer').click(function(){
    $('#AddBuyer').modal('show');
});

// Submit Add Buyer form via AJAX
$('#addBuyerForm').on('submit', function(e) {
    e.preventDefault();
    let form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),
        success: function(response) {
            if(response.id && response.name) {
                let newOption = new Option(response.name, response.id, true, true);
                $('#buyerSelect').append(newOption).trigger('change');
                $('#AddBuyer').modal('hide');
                form[0].reset();
            } else {
                console.log('111111');
            }
        },
        error: function() {
            console.log('22222');
        }
    });
});

// When main composition changes
$('select[name="composition"]').on('change', function(){
    let mainComp = $(this).val();

    // Update all color row compositions
    $('.color-composition').each(function(){
        $(this).val(mainComp).trigger('change'); // updates value & triggers AJAX
    });
});

$(document).on('keyup input change', 'input[name^="qtys"]', function () {
    calculateTotalQty();
});

function calculateTotalQty() {
    let totalQty = 0;

    $('input[name^="qtys"]').each(function () {
        let val = parseInt($(this).val());
        if (!isNaN(val)) {
            totalQty += val;
        }
    });

    $('.total_qty').val(totalQty).trigger('change');

    console.log('Total Order Qty:', totalQty);
}



</script>
@endpush
