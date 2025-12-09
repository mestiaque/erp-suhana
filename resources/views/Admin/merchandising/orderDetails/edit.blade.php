@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Order Details Edit') }}</title>
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
        <h1>Edit Order Details</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.orderDetails') }}">Order Details</a></li>
            <li class="item">Edit Order Details</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')





            <form action="{{ route('admin.orderDetailsAction', ['update', $orderDetails->id]) }}" method="POST">
                @csrf
                <div class="row">

                    <!-- 1. Buyer Name -->
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-semibold">Buyer *</label>
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
                                <button class="btn-custom primary h-100 px-3" type="button" id="openAddBuyer">
                                    <i class="bx bx-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Brand / Customer -->
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-semibold">Brand / Customer *</label>
                        <input type="text" class="form-control updateHead" name="company_name"
                            value="{{ $orderDetails->company_name }}"
                            placeholder="Brand / Customer"
                            data-name="company_name" required
                            data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                    </div>

                    <!-- Merchandiser (Extra Field but kept) -->
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-semibold">Merchandiser *</label>
                        <select name="merchant" class="form-control updateHead"
                                data-name="merchant" required
                                data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                                required>
                            <option value="">-- Select Merchandiser --</option>
                            @foreach($merchandisers as $m)
                                <option value="{{ $m->id }}" {{ $orderDetails->merchant_id == $m->id ? 'selected':'' }}>
                                    {{ $m->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <!-- Style -->
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-semibold">Style *</label>
                        <input type="text" class="form-control updateHead" name="style_no"
                               value="{{ $orderDetails->style_no }}"
                               placeholder="Style"
                               data-name="style_no" required
                               data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}"
                               required>
                    </div>

                    <!-- 3. Order PO No -->
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-semibold">Order PO No *</label>
                        <input type="text" class="form-control updateHead" name="order_no"
                            value="{{ $orderDetails->order_no }}"
                            placeholder="Order PO No"
                            data-name="order_no" required
                            data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                    </div>

                    <!-- 4. Order Qty -->
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-semibold">Order Qty *</label>
                        <input type="number" class="form-control updateHead" name="total_qty"
                            value="{{ $orderDetails->total_qty }}"
                            data-name="total_qty" required
                            data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                    </div>

                    <!-- 5. Color Name -->
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-semibold">Color Name *</label>
                        <input type="text" class="form-control updateHead" name="color_name"
                            value="{{ $orderDetails->color_name }}"
                            placeholder="Color Name"
                            data-name="color_name" required
                            data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                    </div>

                    <!-- 7. Shipment Date -->
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-semibold">Shipment Date *</label>
                        <input type="date" class="form-control updateHead" name="shipment_date"
                            value="{{ $orderDetails->shipment_date?Carbon\Carbon::parse($orderDetails->shipment_date)->format('Y-m-d'):'' }}"
                            data-name="shipment_date" required
                            data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                    </div>

                    <!-- 8. Composition -->
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-semibold">Composition *</label>
                        <input type="text" class="form-control updateHead" name="composition"
                            value="{{ $orderDetails->composition }}" required
                            data-name="composition" placeholder="Composition "
                            data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                    </div>

                    <!-- 9. Fabrication -->
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-semibold">Fabrication *</label>
                        <input type="text" class="form-control updateHead" name="fabrication"
                            value="{{ $orderDetails->fabrication }}" required
                            data-name="fabrication" placeholder="Fabrication"
                            data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                    </div>

                    <!-- 🔟 GSM -->
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-semibold">GSM *</label>
                        <input type="text" class="form-control updateHead" name="gsm"
                            value="{{ $orderDetails->gsm }}"
                            placeholder="GSM" required
                            data-name="gsm"
                            data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                    </div>

                    <!-- 11. Remarks -->
                    <div class="col-md-8 mb-3">
                        <label class="font-weight-semibold">Remarks</label>
                        <textarea class="form-control updateHead" name="remarks" rows="1"
                                data-name="remarks"placeholder="Remarks"
                                data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">{{ $orderDetails->remarks }}</textarea>
                    </div>

                    <!-- 12. Status -->
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-semibold">Status *</label>
                        <select class="form-control updateHead" name="status"
                                data-name="status" required
                                data-url="{{ route('admin.orderDetailsAction',['update-head',$orderDetails->id]) }}">
                            <option value="pending" {{ $orderDetails->status=='pending'?'selected':'' }}>Pending</option>
                            <option value="confirmed" {{ $orderDetails->status=='confirmed'?'selected':'' }}>Confirmed</option>
                            <option value="completed" {{ $orderDetails->status=='completed'?'selected':'' }}>Completed</option>
                            <option value="cancelled" {{ $orderDetails->status=='cancelled'?'selected':'' }}>Cancelled</option>
                        </select>
                    </div>

                </div>

                <br>
                <button type="submit" class="btn btn-success">
                    <i class="bx bx-check"></i> Update Order Details
                </button>
            </form>




        </div>
    </div>

    <!-- Add Buyer Modal -->
    <div class="modal fade text-left" id="AddBuyer" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addBuyerForm" action="{{route('admin.buyersAction','create')}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Add Buyer</h4>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Buyer Name *</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter Buyer Name" required>
                        </div>
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" class="form-control" name="company_name" placeholder="Enter Company Name">
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter Email" required>
                        </div>
                        <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" class="form-control" name="mobile" placeholder="Enter Mobile">
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <select name="country" class="form-control">
                                <option value="">-- Select Country --</option>
                                @foreach (geoData(1) as $c)
                                    <option value="{{ $c->name }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" class="form-control" name="address" placeholder="Enter Address">
                            <input type="hidden" class="form-control" name="api" value="1" placeholder="Enter Address">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Buyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
<script>

    // Open modal when + icon is clicked
    $('#openAddBuyer').on('click', function () {
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

</script>
@endpush
