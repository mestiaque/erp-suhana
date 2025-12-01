@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Garment Order Create/Edit') }}</title>
@endsection

@push('css')
<style>
    .search-result-box{position:absolute;z-index:9;width:100%;background:#fff;border:1px solid #ddd;display:none;}
    .search-result-box li{padding:6px 10px;cursor:pointer;}
    .searchlist ul {list-style: none;margin: 0;padding: 0;}
    .searchlist ul li {border-top: 1px solid #dbd6d6;padding: 5px 10px;cursor: pointer;}
    .searchlist ul li:hover {background: #f2f2f2;}
    .searchlist ul li img {width: 35px;height: 35px;border-radius: 100%;border: 1px solid #dbd6d6;padding: 2px;margin-right: 10px;}
    .searchGrid {position: relative;}
    .itemSearch {height: 200px;overflow: auto;position: absolute;width: 100%;background: white;border: 1px solid #dfdfdf;border-top: 0;display:none;}
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Edit Garment Order</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item">
                <a href="{{ route('admin.orders') }}">Garment Orders</a>
            </li>
            <li class="item">Edit Garment Order</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Garment Order #<span class="text-primary">{{ $order->order_no }}</span></h3>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.ordersAction', ['update', $order->id]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Buyer*</label>
                        <select id="buyer_id" name="buyer_id" class="form-control" required>
                            <option value="">Select Buyer</option>
                            @foreach($buyers as $b)
                                <option value="{{ $b->id }}" {{ $order->buyer_id == $b->id ? 'selected':'' }}>
                                    {{ $b->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('buyer_id'))
                            <p style="color: red; margin: 0;">{{ $errors->first('buyer_id') }}</p>
                        @endif
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Order Date*</label>
                        <input type="date" name="created_at" value="{{ $order->created_at?->format('Y-m-d') }}" class="form-control" required>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Currency*</label>
                        <select class="form-control" name="currency" required>
                            <option value="BDT" {{ $order->currency=='BDT'?'selected':'' }}>BDT</option>
                            <option value="USD" {{ $order->currency=='USD'?'selected':'' }}>USD</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Order Number</label>
                        <input type="text" class="form-control" value="{{ $order->order_no }}" readonly>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="pending" {{ $order->status=='pending'?'selected':'' }}>Pending</option>
                            <option value="approved" {{ $order->status=='approved'?'selected':'' }}>Approved</option>
                            <option value="cancelled" {{ $order->status=='cancelled'?'selected':'' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Note</label>
                        <textarea class="form-control" name="note" placeholder="Write note">{{ $order->note }}</textarea>
                    </div>
                </div>

                <br>
                <h5><b>Order Items</b></h5>

                <div class="cardItems">
                    @include(adminTheme().'orders.includes.items')
                </div>

                <br>
                <button type="submit" class="btn btn-success">
                    <i class="bx bx-check"></i> Submit Order
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Live total calculation
    function updateTotalSummary() {
        let totalQty = 0;
        let totalPrice = 0;
        $('.itemRow').each(function(){
            let qty = parseFloat($(this).find('.qty').val()) || 0;
            let price = parseFloat($(this).find('.price').val()) || 0;
            let rowTotal = qty * price;
            $(this).find('.priceTotal').text(rowTotal.toFixed(2));
            totalQty += qty;
            totalPrice += rowTotal;
        });
        $('.totalQty').text(totalQty);
        $('.totalPrice').text(totalPrice.toFixed(2));
    }

    // Buyer change
    $('#buyer_id').change(function(){
        ajaxUpdate({action:'add-buyer', buyer_id: $(this).val()});
    });

    // Add Item
    $(document).on('click','.addItem', function(){
        let url = $(this).data('url');
        $.get(url, function(res){
            if(res.success) $('.cardItems').html(res.view);
        });
    });

    // Search Product
    $(document).on('keyup','.search-product', function(){
        let search = $(this).val();
        $.get("{{ route('admin.ordersAction',['search-item',$order->id]) }}", {search: search}, function(res){
            $('.searchlist').html(res.view).show();
        });
    });

    // Select Product
    $(document).on('click','.selectProduct', function(){
        let id = $(this).data('id');
        let name = $(this).data('name');
        let item_id = $(this).data('item');
        let url = $(this).data('url');
        ajaxUpdate(url, {action:'update-item', item_id:item_id, product_id:id, product_name:name});
    });

    // Update qty/unit/price
    $(document).on('keyup change','.update-field', function(){
        updateTotalSummary();
        let url = $(this).data('url');
        let value = $(this).val();
        $.get(url, {data:value});
    });

    // Remove Item
    $(document).on('click','.removeItem', function(e){
        e.preventDefault();
        let url = $(this).data('url');
        ajaxUpdate(url, {action:'remove-item', item_id: $(this).data('item')});
    });

    // AJAX helper
    function ajaxUpdate(url, data){
        $.get("{{ route('admin.ordersAction',['edit',$order->id]) }}", data, function(res){
            if(res.success) $('.cardItems').html(res.view);
        });
    }

    $(document).on('click','.addDataQuery',function(){
        var url  = $(this).data('url');
        $.get(url, function(data){
            if(data.success) $('.cardItems').html(data.view);
        });
    });

    $(document).on('focus', '.searchGrid input', function() {
        $('.itemSearch').show();
    });
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.searchGrid').length) {
            $('.itemSearch').hide();
        }
    });
</script>
@endpush
