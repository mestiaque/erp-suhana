
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

.item-row {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
}

.item-row input,
.item-row select {
    flex: 1;
}

.item-row .btn-custom.danger {
    flex: 0 0 auto;
}

.item-headers {
    font-weight: 600;
    background: #e9ecef;
    padding: 4px;
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
}

.item-headers div { flex: 1; text-align: center; }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area mb-3">
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

                <div class="section-title">Main Order Information</div>
                <div class="row g-2 mb-3">
                    <!-- Buyer -->
                    <div class="col-md-6 mb-2">
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

                    <!-- Brand / Customer -->
                    <div class="col-md-6 mb-2">
                        <label>Brand / Customer *</label>
                        <input type="text" name="company_name" class="form-control updateHead"
                               value="{{ $orderDetails->company_name }}"
                               data-name="company_name"
                               data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                               required>
                    </div>

                    <!-- Merchandiser -->
                    <div class="col-md-6 mb-2">
                        <label>Merchandiser * <a target="_blank" href="{{ route('admin.merchandisers') }}"><i class="fa fa-external-link"></i></a></label>
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
                    <div class="col-md-6 mb-2">
                        <label>Style *</label>
                        <input type="text" name="style_no" class="form-control updateHead"
                               value="{{ $orderDetails->style_no }}"
                               data-name="style_no"
                               data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                               required>
                    </div>

                    <!-- Order No -->
                    <div class="col-md-6 mb-2">
                        <label>Order / PO No *</label>
                        <input type="text" name="order_no" class="form-control updateHead"
                               value="{{ $orderDetails->order_no }}"
                               data-name="order_no"
                               data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                               required>
                    </div>

                    <!-- Total Qty -->
                    <div class="col-md-6 mb-2">
                        <label>Total Qty *</label>
                        <input type="number" name="total_qty" class="form-control total_qty updateHead"
                               value="{{ $orderDetails->total_qty }}" readonly
                               data-name="total_qty"
                               data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                               required>
                    </div>

                    <!-- Shipment Date -->
                    <div class="col-md-6 mb-2">
                        <label>Shipment Date *</label>
                        <input type="date" name="shipment_date" class="form-control updateHead"
                               value="{{ $orderDetails->shipment_date ? \Carbon\Carbon::parse($orderDetails->shipment_date)->format('Y-m-d') : '' }}"
                               data-name="shipment_date"
                               data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                               required>
                    </div>

                    <!-- Composition -->
                    <div class="col-md-6 mb-2">
                        <label>Composition *</label>
                        <select name="composition" class="form-control updateHead"
                                data-name="composition"
                                data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                                required>
                            <option value="">-- Select Composition --</option>
                            @foreach($compositions as $comp)
                                <option value="{{ $comp }}" {{ $orderDetails->composition == $comp ? 'selected':'' }}>
                                    {{ $comp }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Fabrication -->
                    <div class="col-md-6 mb-2">
                        <label>Fabrication *</label>
                        <select name="fabrication" class="form-control updateHead"
                                data-name="fabrication"
                                data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                                required>
                            <option value="">-- Select Fabrication --</option>
                            @foreach($fabrications as $fab)
                                <option value="{{ $fab }}" {{ $orderDetails->fabrication == $fab ? 'selected':'' }}>
                                    {{ $fab }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- GSM -->
                    <div class="col-md-6 mb-2">
                        <label>GSM *</label>
                        <input type="text" name="gsm" class="form-control updateHead"
                               value="{{ $orderDetails->gsm }}"
                               data-name="gsm"
                               data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                               required>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mb-2">
                        <label>Status *</label>
                        <select name="status" class="form-control updateHead"
                                data-name="status"
                                data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                                required>
                            <option value="pending" {{ $orderDetails->status=="pending"?'selected':'' }}>Pending</option>
                            <option value="confirmed" {{ $orderDetails->status=="confirmed"?'selected':'' }}>Confirmed</option>
                            <option value="completed" {{ $orderDetails->status=="completed"?'selected':'' }}>Completed</option>
                            <option value="cancelled" {{ $orderDetails->status=="cancelled"?'selected':'' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Remarks -->
                    <div class="col-md-6 mb-2">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control updateHead" rows="1"
                                  data-name="remarks"
                                  data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">{{ $orderDetails->remarks }}</textarea>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="section-title mt-3">Order Items</div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead >
                            <tr>
                                <th style="width:20%">Item</th>
                                <th style="width:25%">Composition</th>
                                <th style="width:25%">Color Name</th>
                                <th style="width:15%">Qty</th>
                                <th style="width:5%"></th>
                            </tr>
                        </thead>

                        <tbody id="colorQtyArea">
                        @if($orderDetails->items && $orderDetails->items->count())
                            @foreach($orderDetails->items as $item)
                                <tr class="color-row">
                                    {{-- ITEM NAME --}}
                                    <td>
                                        <input type="text"
                                            name="item_name[{{$item->id}}]"
                                            class="form-control updateItem"
                                            data-name="item_name"
                                            data-url="{{ route('admin.orderDetailsAction',['update-item',$item->id]) }}"
                                            value="{{ $item->item_name }}"
                                            placeholder="Item Name">
                                    </td>

                                    {{-- COMPOSITION --}}
                                    <td>
                                        <select name="compositions[{{$item->id}}]"
                                            class="form-control updateItem color-composition"
                                            data-name="composition"
                                            data-url="{{ route('admin.orderDetailsAction',['update-item',$item->id]) }}">
                                            <option value="">-- Select --</option>
                                            @foreach($compositions as $comp)
                                                <option value="{{ $comp }}"
                                                    {{ $item->composition == $comp ? 'selected' : ($orderDetails->composition==$comp?'selected':'') }}>
                                                    {{ $comp }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- COLOR --}}
                                    <td>
                                        <input type="text"
                                            name="colors[{{$item->id}}]"
                                            class="form-control updateItem"
                                            data-name="color_name"
                                            data-url="{{ route('admin.orderDetailsAction',['update-item',$item->id]) }}"
                                            value="{{ $item->color_name }}"
                                            placeholder="Color">
                                    </td>

                                    {{-- QTY --}}
                                    <td>
                                        <input type="number"
                                            name="qtys[{{$item->id}}]"
                                            class="form-control updateItem"
                                            data-name="qty"
                                            data-url="{{ route('admin.orderDetailsAction',['update-item',$item->id]) }}"
                                            value="{{ $item->qty > 0 ? $item->qty : '' }}"
                                            placeholder="Qty">
                                    </td>

                                    {{-- ACTION --}}
                                    <td class="text-center">
                                        <button type="button"
                                            class="btn btn-sm btn-danger removeRow"
                                            data-url="{{ route('admin.orderDetailsAction',['remove-item',$item->id]) }}">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>

                <div class="text-right mb-3">
                    <a id="addColorRow"
                        class="btn-custom btn-sm"
                        data-url="{{ route('admin.orderDetailsAction',['add-item',$orderDetails->id]) }}">
                        <i class="bx bx-plus"></i> Add Item
                    </a>
                </div>

                <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Save Updated Order</button>
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
    let mainComposition = $('select[name="composition"]').val() || "{{ $orderDetails->composition }}";

    $.get(url, function(res){
        if(res.success){
            let removeUrl = `/admin/order-details/remove-item/${res.id}`;
            let updateUrl = `/admin/order-details/update-item/${res.id}`;

            let compOptions = `
                <option value="">-- Select --</option>
                @foreach($compositions as $comp)
                    <option value="{{ $comp }}" ${mainComposition == "{{ $comp }}" ? 'selected' : ''}>
                        {{ $comp }}
                    </option>
                @endforeach
            `;

            let row = `
                <tr class="color-row">
                    <td>
                        <input type="text" name="item_name[${res.id}]"
                            class="form-control updateItem"
                            data-name="item_name"
                            data-url="${updateUrl}"
                            placeholder="Item Name">
                    </td>
                    <td>
                        <select name="compositions[${res.id}]"
                            class="form-control updateItem color-composition"
                            data-name="composition"
                            data-url="${updateUrl}">
                            ${compOptions}
                        </select>
                    </td>
                    <td>
                        <input type="text" name="colors[${res.id}]"
                            class="form-control updateItem"
                            data-name="color_name"
                            data-url="${updateUrl}"
                            placeholder="Color">
                    </td>
                    <td>
                        <input type="number" name="qtys[${res.id}]"
                            class="form-control updateItem"
                            data-name="qty"
                            data-url="${updateUrl}"
                            placeholder="Qty">
                    </td>
                    <td class="text-center">
                        <button type="button"
                            class="btn btn-sm btn-danger removeRow"
                            data-url="${removeUrl}">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $("#colorQtyArea").append(row);
        }
    });
});

// Remove row
$(document).on('click', '.removeRow', function () {
    let button = $(this);
    let url = button.data('url');
    $.get(url, function(res){
        if(res.success){
            button.closest('.color-row').remove();
            calculateTotalQty();
        }
    });
});

// Update header fields
$(document).on('change','.updateHead', function(){
    let url = $(this).data('url');
    $.get(url, { field: $(this).data('name'), value: $(this).val() });
});

// Update item fields
$(document).on('change','.updateItem', function(){
    let url = $(this).data('url');
    $.get(url, { field: $(this).data('name'), value: $(this).val() });
});

// Buyer Modal
$('#openAddBuyer').click(function(){ $('#AddBuyer').modal('show'); });

$('#addBuyerForm').on('submit', function(e){
    e.preventDefault();
    let form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),
        success: function(response){
            if(response.id && response.name){
                let newOption = new Option(response.name, response.id, true, true);
                $('#buyerSelect').append(newOption).trigger('change');
                $('#AddBuyer').modal('hide');
                form[0].reset();
            } else {
                $('#AddBuyer').modal('hide');
                form[0].reset();
                alert(response.msg)
            }
        },
        error: function(){ console.log('Error adding buyer'); }
    });
});

// Update all item compositions when main composition changes
$('select[name="composition"]').on('change', function(){
    let mainComp = $(this).val();
    $('.color-composition').each(function(){ $(this).val(mainComp).trigger('change'); });
});

// Calculate total qty
$(document).on('keyup input change', 'input[name^="qtys"]', function () { calculateTotalQty(); });

function calculateTotalQty() {
    let totalQty = 0;
    $('input[name^="qtys"]').each(function(){
        let val = parseInt($(this).val());
        if(!isNaN(val)) totalQty += val;
    });
    $('.total_qty').val(totalQty).trigger('change');
    console.log('Total Order Qty:', totalQty);
}
</script>
@endpush
