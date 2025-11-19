@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Purchase Order Create') }}</title>
@endsection

@push('css')
<style>
    .search-result-box{position:absolute;z-index:9;width:100%;background:#fff;border:1px solid #ddd;display:none;}
    .search-result-box li{padding:6px 10px;cursor:pointer;}
    .search-result-box li:hover{background:#f5f5f5;}
</style>
@endpush


@section('contents')

    <div class="breadcrumb-area">
        <h1>Edit Purchase Order</h1>
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
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Company</label>
                        <select id="supplier_id" name="supplier_id" class="form-control" required>
                            <option value="">Select Company</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ $order->supplier_id == $s->id ? 'selected':'' }}>
                                    {{ $s->factory_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Order Date</label>
                        <input type="date" name="order_date" value="{{ $order->created_at?->format('Y-m-d') }}" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label>Expected Date</label>
                        <input type="date" name="expected_date" value="{{ $order->expected_date?->format('Y-m-d') }}" class="form-control" required>
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
@endsection


@push('js')
<script>

    // -------------------------
    // Company Update
    // -------------------------
    $('#supplier_id').change(function(){
        ajaxUpdate({action:'add-supplier', supplier_id:$(this).val()});
    });

    // -------------------------
    // Add Item
    // -------------------------
    $('#addItem').click(function(){
        ajaxUpdate({action:'add-item'});
    });

    // -------------------------
    // Material Search
    // -------------------------
    $(document).on('keyup','.search-material', function(){
        let search = $(this).val();
        let item_id = $(this).data('item');

        if(search.length < 1){
            $(this).siblings('.search-result-box').hide();
            return;
        }

        $.post("{{ route('admin.purchasesOrdersAction',['search-item',$order->id]) }}",
        {_token:"{{ csrf_token() }}",search:search,item_id:item_id},
        function(res){
            $('.result-'+item_id).html(res.view).show();
        });
    });

    // -------------------------
    // Material Select
    // -------------------------
    $(document).on('click','.selectMaterial', function(){
        let id = $(this).data('id');
        let name = $(this).data('name');
        let item_id = $(this).data('item');

        ajaxUpdate({
            action:'update-item',
            item_id:item_id,
            material_id:id,
            material_name:name,
        });
    });

    // -------------------------
    // Update QTY / UNIT
    // -------------------------
    $(document).on('keyup change','.update-field', function(){
        let item_id = $(this).data('item');
        let qty = $('#qty-'+item_id).val();
        let unit = $('#unit-'+item_id).val();

        ajaxUpdate({
            action:'update-item',
            item_id:item_id,
            qty:qty,
            unit:unit
        });
    });

    // -------------------------
    // Remove Item
    // -------------------------
    $(document).on('click','.removeItem', function(){
        ajaxUpdate({
            action:'remove-item',
            item_id:$(this).data('item')
        });
    });

    // -------------------------
    // AJAX FUNCTION
    // -------------------------
    function ajaxUpdate(data){
        data._token = "{{ csrf_token() }}";

        $.post("{{ route('admin.purchasesOrdersAction',['update',$order->id]) }}", data, function(res){
            if(res.success){
                $('#itemArea').html(res.view);
            }
        });
    }

</script>
@endpush
