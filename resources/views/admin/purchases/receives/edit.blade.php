@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Purchase Receive Edit') }}</title>
@endsection

@push('css')
<style>
.search-result-box{position:absolute;z-index:9;width:100%;background:#fff;border:1px solid #ddd;display:none;}
.search-result-box li{padding:6px 10px;cursor:pointer;}
.search-result-box li:hover{background:#f5f5f5;}
.invoiceTable tr th,
.invoiceTable tr td{padding:5px;}
</style>
@endpush

@section('contents')
<div class="breadcrumb-area">
    <h1>Edit Purchase Receive</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">
            <a href="{{ route('admin.purchasesReceived') }}">Purchase Receive</a>
        </li>
        <li class="item">Edit Purchase Receive</li>
    </ol>
</div>

<div class="flex-grow-1">

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Receive Form</h3>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.purchasesReceivedAction', ['update', $receive->id]) }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Branch/Factory</label>
                        <input type="text" class="form-control" value="{{ $receive->branch?$receive->branch->name:'' }}" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Purchase No</label>
                        <input type="text" class="form-control" value="{{ $receive->purchase_no }}" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Purchase Receive No</label>
                        <input type="text" class="form-control" value="{{ $receive->purchase_receive_no }}" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Challan No*</label>
                        <input type="text" class="form-control" name="challan_no" value="{{ $receive->challan_no }}" placeholder="Enter challan no" required="">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Note</label>
                        <textarea name="note" class="form-control" placeholder="Write note" >{{ $receive->note }}</textarea>
                    </div>
                </div>

                <h5><b>Received Items</b> @if($receive->status=='approved')
                                            <span class="badge bg-success text-white">Approved</span> @endif </h5>
                <div class="cardItems">
                    @include(adminTheme().'purchases.receives.includes.items', ['receive' => $receive])
                </div>

                <br>
                <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Update Receive</button>
            </form>

        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function(){

    // Update received_qty dynamically
    $(document).on('keyup change', '.updateItemQty', function(){
        let item_id = $(this).data('item');
        let value = $(this).val();

        $.ajax({
            url: "{{ route('admin.purchasesReceivedAction',['update-item',$receive->id]) }}",
            type: "POST",
            data: {_token:"{{ csrf_token() }}", item_id:item_id, received_qty:value},
            success: function(res){
                if(res.view){
                    // $('.cardItems').html(res.view);
                }
            },
            error: function(){
                alert('Error updating received quantity.');
            }
        });
    });

    $(document).on('keyup change', '.update-challan', function(){
        let item_id = $(this).data('item');
        let value = $(this).val();

        $.ajax({
            url: "{{ route('admin.purchasesReceivedAction',['update',$receive->id]) }}",
            type: "POST",
            data: {_token:"{{ csrf_token() }}", item_id:item_id, received_qty:value},
            success: function(res){
                if(res.view){
                    $('.cardItems').html(res.view);
                }
            },
            error: function(){
                alert('Error updating received quantity.');
            }
        });
    });

});
</script>
@endpush
