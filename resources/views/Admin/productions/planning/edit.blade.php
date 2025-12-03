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
    .lineCheck {
        border: 1px solid #bebebe;
        padding: 5px 10px;
        border-radius: 3px;
        margin: 0;
        cursor: pointer;
        margin: 3px 1px;
    }
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
        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.samplesAction', ['update', $plan->id]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm mb-3 flex-fill">
                            <div class="card-header">
                                <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">1.Cutting Section</span></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="padding:5px;">Starting Date</th>
                                            <td style="padding:1px;">
                                                <input type="date" class="form-control form-control-sm" name="production_date">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Ending Date</th>
                                            <td style="padding:1px;">
                                                <input type="date" class="form-control form-control-sm" name="production_date">
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
                                <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">2.Sewing Section</span></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="padding:5px;">Starting Date</th>
                                            <td style="padding:1px;">
                                                <input type="date" class="form-control form-control-sm" name="production_date">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Ending Date</th>
                                            <td style="padding:1px;">
                                                <input type="date" class="form-control form-control-sm" name="production_date">
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
                                <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">3.Packing Section</span></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="padding:5px;">Starting Date</th>
                                            <td style="padding:1px;">
                                                <input type="date" class="form-control form-control-sm" name="production_date">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Ending Date</th>
                                            <td style="padding:1px;">
                                                <input type="date" class="form-control form-control-sm" name="production_date">
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
                                <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">4.Shipment Section</span></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="padding:5px;">Starting Date</th>
                                            <td style="padding:1px;">
                                                <input type="date" class="form-control form-control-sm" name="production_date">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Ending Date</th>
                                            <td style="padding:1px;">
                                                <input type="date" class="form-control form-control-sm" name="production_date">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="card shadow-sm mb-3 flex-fill">
                            <div class="card-header">
                                <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">5. Sewing Production Planning </span></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="padding:5px;min-width:250px;width:250px;">Style No</th>
                                            <th style="padding:5px;min-width:400px;">Floor/Line</th>
                                            <th style="padding:5px;min-width:250px;width:250px;">Output </th>
                                            <th style="padding:5px;min-width:250px;width:250px;">
                                                Setup
                                            </th>
                                        </tr>
                                        <tr>
                                            <td style="padding:5px;">
                                                <select class="form-control form-control-sm mb-2">
                                                    <option value="">Select</option>
                                                    @foreach(App\Models\OrderDetails::orderBy('id', 'desc')->where('status','pending')->get() as $style)
                                                    <option value="{{$style->style_no}}">{{$style->style_no}}</option>
                                                    @endforeach
                                                </select>
                                                <p>
                                                    Order Qty :<b>2,225 pcs </b> <br>
                                                    Buyer :<b>H.M</b> <br>
                                                    Merchandiser :<b>Md Mijun</b> <br>
                                                </p>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @php
                                                        $attributes = App\Models\Attribute::where('type', 4)
                                                            ->where('status', 'active')
                                                            ->get()
                                                            ->groupBy('name');
                                                        @endphp

                                                        @foreach($attributes as $name => $items)
                                                            <b>{{ $name }}</b>
                                                            <br>

                                                            @foreach($items as $line)
                                                                <label class="lineCheck">
                                                                    <input type="checkbox" name="floor[]" value="{{ $line->slug }}">
                                                                    Line - <b>{{ $line->slug }} / </b> C: {{ $line->capacity }}
                                                                </label>
                                                            @endforeach

                                                            <hr>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p>
                                                    P. Start:<b> 03.12.2025 10.00 AM</b> <br>
                                                    Total Hours:<b>13h - 30m  </b> <br>
                                                    Hourly Target :<b>200pcs</b> <br>
                                                    Per Day/Hours :<b>10h</b> <br>
                                                    P. End:<b> 03.12.2025 10.00 AM</b> <br>
                                                </p>
                                            </td>
                                            <td>
                                                <label>Lose Time (In Minite)</label>
                                                <input type="text" class="form-control form-control-sm" placeholder="Lose Hour (In Minite)">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                  
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
