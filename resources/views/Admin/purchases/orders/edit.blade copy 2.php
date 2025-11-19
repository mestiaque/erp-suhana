@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Purchase Order Edit') }}</title>
@endsection

@push('css')
<style>
    .search-result-box{position:absolute;z-index:9;width:100%;background:#fff;border:1px solid #ddd;display:none;}
    .search-result-box li{padding:6px 10px;cursor:pointer;}
    .search-result-box li:hover{background:#f5f5f5;}

    .update-field{width:80px;}
    .orderTable tr th, .orderTable tr td{padding:5px;}
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Edit Purchase Order</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">Purchase Order</li>
    </ol>
</div>

<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Supplier / Company</label>
            <select id="supplier_id" class="form-control select2">
                <option value="">Select Company</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ $order->supplier_id==$s->id?'selected':'' }}>{{ $s->factory_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label>Order Date</label>
            <input type="date" class="form-control" id="order_date" value="{{ $order->created_at->format('Y-m-d') }}">
        </div>

        <div class="col-md-4">
            <label>Status</label>
            <select class="form-control" id="status">
                <option value="pending" {{ $order->status=="pending"?'selected':'' }}>Pending</option>
                <option value="approved" {{ $order->status=="approved"?'selected':'' }}>Approved</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <label>Note</label>
            <textarea class="form-control" id="order_note" rows="3">{{ $order->note }}</textarea>
        </div>
    </div>

    <button class="btn btn-sm btn-primary mb-3" id="addItem">+ Add Item</button>

    <div class="cardItems">
        @include(adminTheme().'purchases.orders.includes.items', ['order'=>$order])
    </div>

</div>

@endsection

@push('js')
<script>
$(document).ready(function(){

    // -------------------------
    // Update supplier / company
    // -------------------------
    $('#supplier_id').change(function(){
        ajaxUpdate({action:'add-supplier', supplier_id: $(this).val()});
    });

    // -------------------------
    // Add Item
    // -------------------------
    $('#addItem').click(function(){
        ajaxUpdate({action:'add-item'});
    });

    // -------------------------
    // Search Material
    // -------------------------
    $(document).on('keyup','.search-material', function(){
        let search = $(this).val();
        let item_id = $(this).data('item');
        if(search.length<1){
            $(this).siblings('.search-result-box').hide();
            return;
        }

        $.post("{{ route('admin.purchasesOrdersAction', ['search-item', $order->id]) }}",
            {_token:"{{ csrf_token() }}", search: search, item_id:item_id},
            function(res){
                $('.result-'+item_id).html(res.view).show();
            });
    });

    // -------------------------
    // Select Material
    // -------------------------
    $(document).on('click','.selectMaterial', function(){
        let id = $(this).data('id');
        let name = $(this).data('name');
        let item_id = $(this).data('item');

        ajaxUpdate({
            action:'update-item',
            item_id:item_id,
            material_id:id,
            material_name:name
        });
    });

    // -------------------------
    // Update QTY / UNIT / PRICE
    // -------------------------
    $(document).on('keyup change','.update-field', function(){
        let item_id = $(this).data('item');
        let qty = $('#qty-'+item_id).val();
        let unit = $('#unit-'+item_id).val();
        let price = $('#price-'+item_id).val();

        ajaxUpdate({
            action:'update-item',
            item_id:item_id,
            qty: qty,
            unit: unit,
            price: price
        });
    });

    // -------------------------
    // Remove Item
    // -------------------------
    $(document).on('click','.removeItem', function(){
        ajaxUpdate({
            action:'remove-item',
            item_id: $(this).data('item')
        });
    });

    // -------------------------
    // AJAX update function
    // -------------------------
    function ajaxUpdate(data){
        data._token = "{{ csrf_token() }}";

        $.post("{{ route('admin.purchasesOrdersAction',['update',$order->id]) }}", data, function(res){
            if(res.success){
                $('.cardItems').html(res.view);
            }
        });
    }

});
</script>
@endpush
