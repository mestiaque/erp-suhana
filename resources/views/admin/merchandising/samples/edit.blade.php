@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Sample Edit') }}</title>
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
        <h1>Edit Sample</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.samples') }}">Samples</a></li>
            <li class="item">Edit Sample</li>
        </ol>
    </div>

    <div class="card mb-30">
        {{-- <div class="card-header">
            <h3>Sample #<span class="text-primary">{{ $sample->id }}</span></h3>
        </div> --}}

        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.samplesAction', ['update', $sample->id]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Buyer *</label>
                        <select name="buyer" id="" class="form-control updateHead" data-name="buyer" data-url="{{ route('admin.samplesAction',['update-head',$sample->id]) }}" required>
                            <option value="">-- Select Buyer --</option>
                            @foreach($buyers as $s)
                                <option value="{{ $s->id }}" {{ $sample->buyer_id == $s->id ? 'selected':'' }}>
                                    {{ $s->name }} {{ $s->company_name?'- '.$s->company_name:'' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Merchandiser *</label>
                        <select name="merchant" id="" class="form-control updateHead" data-name="merchant" data-url="{{ route('admin.samplesAction',['update-head',$sample->id]) }}" required>
                            <option value="">-- Select Merchandiser --</option>
                            @foreach($merchandisers as $m)
                                <option value="{{ $m->id }}" {{ $sample->merchant_id == $m->id ? 'selected':'' }}>
                                    {{ $m->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Style *</label>
                        <input type="text" placeholder="Style" name="style" value="{{ $sample->style }}" data-name="style" class="form-control updateHead" data-url="{{ route('admin.samplesAction',['update-head',$sample->id]) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Type *</label>
                        <select name="type" class="form-control updateHead" data-name="type"
                            data-url="{{ route('admin.samplesAction',['update-head',$sample->id]) }}" required>

                            <option value="">-- Select Type --</option>

                            <!-- Development Samples -->
                            <option value="Proto Sample" {{ $sample->type=='Proto Sample'?'selected':'' }}>Proto Sample</option>
                            <option value="Mock-up Sample" {{ $sample->type=='Mock-up Sample'?'selected':'' }}>Mock-up Sample</option>
                            <option value="Fit Sample" {{ $sample->type=='Fit Sample'?'selected':'' }}>Fit Sample</option>
                            <option value="Size Set Sample" {{ $sample->type=='Size Set Sample'?'selected':'' }}>Size Set Sample</option>
                            <option value="Photo Sample" {{ $sample->type=='Photo Sample'?'selected':'' }}>Photo Sample</option>
                            <option value="Sealing Sample" {{ $sample->type=='Sealing Sample'?'selected':'' }}>Sealing Sample</option>
                            <option value="Pre-Production Sample" {{ $sample->type=='Pre-Production Sample'?'selected':'' }}>Pre-Production Sample</option>

                            <!-- Buyer Approval Samples -->
                            <option value="Counter Sample" {{ $sample->type=='Counter Sample'?'selected':'' }}>Counter Sample</option>
                            <option value="Salesman Sample" {{ $sample->type=='Salesman Sample'?'selected':'' }}>Salesman Sample (SMS)</option>
                            <option value="Red Seal Sample" {{ $sample->type=='Red Seal Sample'?'selected':'' }}>Red Seal Sample</option>
                            <option value="Gold Seal Sample" {{ $sample->type=='Gold Seal Sample'?'selected':'' }}>Gold Seal Sample</option>

                            <!-- Production Samples -->
                            <option value="PPS Sample" {{ $sample->type=='PPS Sample'?'selected':'' }}>PPS Sample (Pre-Production Sample)</option>
                            <option value="TOP Sample" {{ $sample->type=='TOP Sample'?'selected':'' }}>TOP Sample (Top of Production)</option>
                            <option value="Pilot Sample" {{ $sample->type=='Pilot Sample'?'selected':'' }}>Pilot Sample</option>
                            <option value="Bulk Sample" {{ $sample->type=='Bulk Sample'?'selected':'' }}>Bulk Sample</option>

                            <!-- Testing & Quality Samples -->
                            <option value="Wash Sample" {{ $sample->type=='Wash Sample'?'selected':'' }}>Wash Sample</option>
                            <option value="Lab Dip" {{ $sample->type=='Lab Dip'?'selected':'' }}>Lab Dip</option>
                            <option value="Handloom Sample" {{ $sample->type=='Handloom Sample'?'selected':'' }}>Handloom Sample</option>
                            <option value="Shrinkage Test Sample" {{ $sample->type=='Shrinkage Test Sample'?'selected':'' }}>Shrinkage Test Sample</option>
                            <option value="Print/Embroidery Sample" {{ $sample->type=='Print/Embroidery Sample'?'selected':'' }}>Print/Embroidery Sample</option>

                            <!-- Other -->
                            <option value="Fit Confirmation Sample" {{ $sample->type=='Fit Confirmation Sample'?'selected':'' }}>Fit Confirmation Sample</option>
                            <option value="Fabric Approval Sample" {{ $sample->type=='Fabric Approval Sample'?'selected':'' }}>Fabric Approval Sample</option>
                            <option value="Trims Approval Sample" {{ $sample->type=='Trims Approval Sample'?'selected':'' }}>Trims Approval Sample</option>
                            <option value="Measurement Sample" {{ $sample->type=='Measurement Sample'?'selected':'' }}>Measurement Sample</option>
                            <option value="Final Approval Sample" {{ $sample->type=='Final Approval Sample'?'selected':'' }}>Final Approval Sample</option>

                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Received Date *</label>
                        <input type="date" name="received_at" value="{{ $sample?->received_at?->format('Y-m-d') }}" data-name="received_at" class="form-control updateHead" data-url="{{ route('admin.samplesAction',['update-head',$sample->id]) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Delivery Date *</label>
                        <input type="date" name="delivery_at" value="{{ $sample?->delivery_at?->format('Y-m-d') }}" data-name="delivery_at" class="form-control updateHead" data-url="{{ route('admin.samplesAction',['update-head',$sample->id]) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Currency *</label>
                        <select name="currency" id="" class="form-control updateHead" data-name="currency"  data-url="{{ route('admin.samplesAction',['update-head',$sample->id]) }}" required>
                            <option value="">-- Select Currency --</option>
                            <option value="BDT" {{$sample->currency=='BDT'?'selected':''}}>BDT</option>
                            <option value="USD" {{$sample->currency=='USD'?'selected':''}}>USD</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control" data-url="{{ route('admin.samplesAction',['update-head',$sample->id]) }}" required>
                            <option value="temp">Temp</option>
                            <option value="pending" {{$sample->status=='pending'?'selected':''}}  {{$sample->status=='temp'?'selected':''}}>Pending</option>
                            <option value="confirmed" {{$sample->status=='confirmed'?'selected':''}}>Confirmed</option>
                            <option value="completed" {{$sample->status=='completed'?'selected':''}}>Completed</option>
                            <option value="cancel" {{$sample->status=='cancel'?'selected':''}}>Cancel</option>
                        </select>
                    </div>
                </div>

                <br>
                <h5><b>Sample Items</b></h5>

                <div class="cardItems">
                    @include(adminTheme().'merchandising.samples.includes.items')
                </div>

                <div class="mt-4">
                    <label>Payment Terms</label>
                    <textarea name="payment_terms" id="" cols="30" rows="10" class="summernote form-control updateHead" data-name="payment_terms" data-url="{{ route('admin.samplesAction',['update-head',$sample->id]) }}">{{ $sample->payment_terms }}</textarea>
                </div>

                <br>
                <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Update Sample</button>
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
    // Update summernote Fields
    // -------------------------
    // Flag to track if toolbar was clicked
    let toolbarClicked = false;

    // Detect clicks on the Summernote toolbar
    $(document).on('mousedown', '.note-toolbar', function() {
        toolbarClicked = true;
    });

    $('.summernote').summernote({
        height: 200,
        callbacks: {
            onBlur: function(e) {
                // If toolbar was clicked, ignore this blur
                if (toolbarClicked) {
                    toolbarClicked = false; // reset for next blur
                    return;
                }

                let textarea = $(this);
                let url = textarea.data('url');
                let name = textarea.data('name');
                let value = textarea.summernote('code').trim(); // get content

                $.get(url, { field: name, value: value }, function(res) {
                    if (res.success) {
                    } else {
                        alert(res.message);
                    }
                });
            }
        }
    });

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
            updateTotalSummary();
            if(res.success){
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
