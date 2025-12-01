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
                        <label>Style *</label>
                        <input type="text" name="style" value="{{ $sample->style }}" data-name="style" class="form-control updateHead" data-url="{{ route('admin.samplesAction',['update-head',$sample->id]) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Type *</label>
                        <select name="type" id="" class="form-control updateHead" data-name="type"  data-url="{{ route('admin.samplesAction',['update-head',$sample->id]) }}" required>
                            <option value="">-- Select Type --</option>
                            <option value="Type 1" {{$sample->type=='Type 1'?'selected':''}}>Type 1</option>
                            <option value="Type 2" {{$sample->type=='Type 2'?'selected':''}}>Type 2</option>
                            <option value="Type 3" {{$sample->type=='Type 3'?'selected':''}}>Type 3</option>
                            <option value="Type 4" {{$sample->type=='Type 4'?'selected':''}}>Type 4</option>
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
