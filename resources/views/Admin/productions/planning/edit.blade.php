@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Production Planning Edit') }}</title>
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
    .table-striped tr th{padding:3px;}
    .table-striped tr td{padding:3px;}
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Edit Planning</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.samples') }}">Planning</a></li>
            <li class="item">Edit Planning</li>
        </ol>
    </div>

    <div class="card mb-30">
        {{-- <div class="card-header">
            <h3>Sample #<span class="text-primary">{{ $order->id }}</span></h3>
        </div> --}}

        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.samplesAction', ['update', $order->id]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm mb-3 flex-fill">
                            <div class="card-body">
                                <ul class="list-group list-group-flush text-start mb-0">
                                    <li class="list-group-item py-1"><strong>Order No:</strong> {{ str_pad($order->id, 10, '0', STR_PAD_LEFT) }}</li>
                                    <li class="list-group-item py-1"><strong>Marchent:</strong> {{$order->merchant_name}}</li>
                                    <li class="list-group-item py-1"><strong>Buyer:</strong> {{$order->buyer_name}}</li>
                                    <li class="list-group-item py-1"><strong>Invoice No:</strong> </li>
                                    <li class="list-group-item py-1"><strong>LC:</strong> </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm mb-3 flex-fill">
                            <div class="card-body">
                                <ul class="list-group list-group-flush text-start mb-0">
                                    <li class="list-group-item py-1"><strong>Order Date:</strong> {{ $order->created_at->format('d.m.Y') }}</li>
                                    <li class="list-group-item py-1"><strong>Status:</strong> 
                                        @if($order->pi_status=='temp')
                                            <span class="badge badge-secondary">Temp</span>
                                        @elseif($order->pi_status=='pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($order->pi_status=='confirmed')
                                            <span class="badge badge-info">Confirmed</span>
                                        @elseif($order->pi_status=='completed')
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($order->pi_status=='cancel')
                                            <span class="badge badge-danger">Cancelled</span>
                                        @endif
                                    </li>
                                    <li class="list-group-item py-1"><strong>Total Qty:</strong> {{number_format($order->total_qty)}}</li>
                                    <li class="list-group-item py-1"><strong>Total Price:</strong> {{ numberFormat($order->total_bill,2,$order->currency) }}</li>
                                    <li class="list-group-item py-1"><strong>BIN Number:</strong> </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="card shadow-sm mb-3 flex-fill">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th width="50">#</th>
                                                <th>Composition</th>
                                                <th>GSM</th>
                                                <th>Color</th>
                                                <th>Size</th>
                                                <th>Quantity</th>
                                                <th>Comments</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse($order->items as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->composition }}</td>
                                                <td>{{ $item->gsm }}</td>
                                                <td>{{ $item->color }}</td>
                                                <td>{{ $item->size }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ $item->comments }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No items found</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm mb-3 flex-fill">
                            <div class="card-header">
                                <h3>Plan Input</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="padding:5px;">Production Start</th>
                                            <td style="padding:1px;">
                                                <input type="date" class="form-control form-control-sm" name="production_date">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Production Day</th>
                                            <td style="padding:1px;">
                                                <input type="number" class="form-control form-control-sm" name="product_day" placeholder="Enter Day">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Cutting</th>
                                            <td style="padding:1px;">
                                                <input type="text" class="form-control form-control-sm" placeholder="Enter ">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Sewing</th>
                                            <td style="padding:1px;">
                                                <input type="text" class="form-control form-control-sm" placeholder="Enter ">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Dyeing</th>
                                            <td style="padding:1px;">
                                                <input type="text" class="form-control form-control-sm" placeholder="Enter ">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Finishing / Pressing</th>
                                            <td style="padding:1px;">
                                                <input type="text" class="form-control form-control-sm" placeholder="Enter ">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm mb-3 flex-fill">
                            <div class="card-header">
                                <h3>Plan Output </h3>
                            </div>
                            <div class="card-body">
                                Pending..
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-3 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control" data-url="{{ route('admin.samplesAction',['update-head',$order->id]) }}" required>
                            <option value="temp">Temp</option>
                            <option value="pending" {{$order->status=='pending'?'selected':''}}  {{$order->status=='temp'?'selected':''}}>Pending</option>
                            <option value="confirmed" {{$order->status=='confirmed'?'selected':''}}>Confirmed</option>
                            <option value="completed" {{$order->status=='completed'?'selected':''}}>Completed</option>
                            <option value="cancel" {{$order->status=='cancel'?'selected':''}}>Cancel</option>
                        </select>
                    </div> -->
                </div>

                <!-- <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Update Sample</button> -->
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
        $('.itemRow').each(function(){
            let qty = parseFloat($(this).find('.qty').val()) || 0;
            totalQty += qty;
        });
        $('.totalQty').text(totalQty);
    }

    // -------------------------
    // Update Item Fields
    // -------------------------
    $(document).on('change','.updateHead', function(){
        let url = $(this).data('url');
        let name = $(this).data('name');
        let value = $(this).val();
        $.get(url, {field: name, value: value}, function(res){
            if(res.success){
                // console.log('success');
            }else{
                    alert(res.message)
                if(res.field){
                    $('input[name="'+res.field+'"]').val('');
                }
            }
        });
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
    });

    // -------------------------
    // Update Item Fields
    // -------------------------
    $(document).on('change','.updateItem', function(){
        let url = $(this).data('url');
        let name = $(this).data('name');
        let value = $(this).val();
        $.get(url, {field: name, value: value}, function(res){
            if(res.success){
                updateTotalSummary();
            }
        });
    });

    // -------------------------
    // Remove Item
    // -------------------------
    $(document).on('click','.removeItem', function(e){
        e.preventDefault();
        let url = $(this).data('url');
        $.get(url, {action:'remove-item'}, function(res){
            if(res.success){
                $('.cardItems').html(res.view);
                updateTotalSummary();
            }
        });
    });

</script>
@endpush
