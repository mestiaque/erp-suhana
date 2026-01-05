@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Purchase Order Create') }}</title>
@endsection

@push('css')
<style>
    .search-result-box{position:absolute;z-index:9;width:100%;background:#fff;border:1px solid #ddd;display:none;}
    .search-result-box li{padding:6px 10px;cursor:pointer;}
    .search-result-box li:hover{background:#f5f5f5;}
    .searchlist ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .searchlist ul li {
        border-top: 1px solid #dbd6d6;
        padding: 5px 10px;
        cursor: pointer;
    }
    .searchlist ul li:hover {
        background: #f2f2f2;
    }
    .searchlist ul li img {
        width: 35px;
        height: 35px;
        border-radius: 100%;
        border: 1px solid #dbd6d6;
        padding: 2px;
        margin-right: 10px;
    }

    .searchGrid {
        position: relative;
    }

    .itemSearch {
        height: 200px;
        overflow: auto;
        position: absolute;
        width: 100%;
        background: white;
        border: 1px solid #dfdfdf;
        border-top: 0;
        display:none;
    }
</style>
@endpush


@section('contents')

<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Edit Purchase</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item">
                <a href="{{ route('admin.purchasesOrders') }}">Purchase Order</a>
            </li>
            <li class="item">Edit Purchase Order</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Purchase Order #<span class="text-primary">{{ $order->order_no }}</span></h3>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.purchasesOrdersAction', ['update', $order->id]) }}" method="POST">
                @csrf
                <div class="row ">
                    <div class="col-md-3 mb-3">
                        <label>Company /Creditor*</label>
                        <select id="supplier_id" name="supplier_id" class="form-control" required>
                            <option value="">Select Company</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ $order->supplier_id == $s->id ? 'selected':'' }}>
                                    {{ $s->name }} {{ $s->company_name?'- '.$s->company_name:'' }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('supplier_id'))
                        <p style="color: red; margin: 0;">{{ $errors->first('supplier_id') }}</p>
                        @endif
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Order Date*</label>
                        <input type="date" name="created_at" value="{{ $order->created_at?->format('Y-m-d') }}" class="form-control" required>
                        @if ($errors->has('created_at'))
                        <p style="color: red; margin: 0;">{{ $errors->first('created_at') }}</p>
                        @endif
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Currency</label>
                        <select class="form-control" name="currency" required="">
                            <option value="BDT" {{$order->currency=='BDT'?'selected':''}} >BDT</option>
                            <option value="USD" {{$order->currency=='USD'?'selected':''}} > USD</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Order Number</label>
                        <input type="text"  value="{{ $order->order_no }}" class="form-control" readonly>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Status</label>
                        <select class="form-control" name="status" required="">
                            <option value="pending" {{$order->status=='pending'?'selected':''}} >Pending</option>
                            <option value="approved" {{$order->status=='approved'?'selected':''}} > Approved</option>
                            <option value="cancelled" {{$order->status=='cancelled'?'selected':''}} > Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Write Note</label>
                        <textarea class="form-control" name="note" placeholder="Write note" >{{ $order->note}}</textarea>
                    </div>
                </div>

                <br>
                <h5><b>Order Items</b></h5>

                <div class="cardItems">
                    @include(adminTheme().'purchases.orders.includes.items')
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

    // === Live calculation & total update ===
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

    // -------------------------
    // Company Update
    // -------------------------
    $('#supplier_id').change(function(){
        ajaxUpdate({action:'add-supplier', supplier_id:$(this).val()});
    });

    // -------------------------
    // Add Item
    // -------------------------
    $(document).on('click','.addItem', function(){
        let url = $(this).data('url');
        $.get(url, function(res){
            if(res.success){
                $('.cardItems').html(res.view);
            }
        });
        // ajaxUpdate({action:'add-item'});
    });

    // -------------------------
    // Material Search
    // -------------------------
    $(document).on('keyup','.search-material', function(){
        let search = $(this).val();

        // if(search.length < 1){
        //     $(this).siblings('.search-result-box').hide();
        //     return;
        // }

        $.get("{{ route('admin.purchasesOrdersAction',['search-item',$order->id]) }}",
        {search:search},
        function(res){
            $('.searchlist').html(res.view).show();
        });
    });

    // -------------------------
    // Material Select
    // -------------------------
    $(document).on('click','.selectMaterial', function(){
        let id = $(this).data('id');
        let name = $(this).data('name');
        let item_id = $(this).data('item');
        let url = $(this).data('url');

        ajaxUpdate(url,{
            action:'update-item',
            item_id:item_id,
            material_id:id,
            material_name:name,
        });
    });

    // -------------------------
    // Update QTY / UNIT
    // -------------------------
    $(document).on('keyup','.update-field', function(){
         updateTotalSummary();
    });
    $(document).on('change','.update-field', function(){
        let url = $(this).data('url');
        let value = $(this).val();
        $.ajax({
            url: url,
            data: {'data': value},
            dataType: 'json',
            success: function(res){
                if(res.view){
                    // $('.cardItems').html(res.view);

                }
            },
            error: function(){
                alert('Error updating item.');
            }
        });
        // let name = $(this).data('name');
        // let name = $(this).data('id');
        // let value = $(this).val();
        // var data ={name:name, data:value};
        // $.get(url, data, function(res){
        //     if(res.success){
        //         // $('.cardItems').html(res.view);
        //     }
        // });
        // updateTotalSummary();
    });

    // -------------------------
    // Remove Item
    // -------------------------
    $(document).on('click','.removeItem', function(e){
        e.preventDefault();
        let url = $(this).data('url');
        ajaxUpdate(url,{
            action:'remove-item',
            item_id:$(this).data('item')
        });
    });

    // -------------------------
    // AJAX FUNCTION
    // -------------------------
    function ajaxUpdate(url,data){
        $.get(url, data, function(res){
            if(res.success){
                $('.cardItems').html(res.view);
            }
        });
    }

     $(document).on('click','.addDataQuery',function(){
        var url  =$(this).data('url');
        $.ajax({
            url:url,
            dataType: 'json',
            cache: false,
            success : function(data){
            $('.cardItems').html(data.view);
            $('.itemSearch').hide();
            },error: function () {
                alert('error');

            }
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
