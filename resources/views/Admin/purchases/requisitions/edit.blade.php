@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Requision Create') }}</title>
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

<div class="breadcrumb-area">
    <h1>Edit Purchase Requisition</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">
            <a href="{{ route('admin.purchasesRequisitions') }}">Purchase Requisition</a>
        </li>
        <li class="item">Edit Purchase Requisition</li>
    </ol>
</div>

<div class="flex-grow-1">

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Requision Form #<span class="text-primary">{{ $requisition->requisition_no }}</span></h3>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.purchasesRequisitionsAction', ['update',$requisition->id]) }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Department*</label>
                        <select class="form-control select2" name="department_id" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dep)
                                <option value="{{ $dep->id }}" {{ old('department_id', $requisition->department_id) == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('department_id'))
                        <p style="color: red; margin: 0;">{{ $errors->first('department_id') }}</p>
                        @endif
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Designation*</label>
                        <select class="form-control select2" name="designation_id" required>
                            <option value="">Select Designation</option>
                            @foreach($designations as $desg)
                                <option value="{{ $desg->id }}" {{ old('designation_id', $requisition->designation_id) == $desg->id ? 'selected' : '' }}>{{ $desg->name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('designation_id'))
                        <p style="color: red; margin: 0;">{{ $errors->first('designation_id') }}</p>
                        @endif
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Requision Date*</label>
                        <input type="date" class="form-control" name="created_at" value="{{$requisition->created_at->format('Y-m-d')}}" required>
                        @if ($errors->has('created_at'))
                        <p style="color: red; margin: 0;">{{ $errors->first('created_at') }}</p>
                        @endif
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Expected Receive Date*</label>
                        <input type="date" class="form-control" name="expected_date" value="{{$requisition->expected_date?Carbon\Carbon::parse($requisition->expected_date)->format('Y-m-d'):old('expected_date')}}" required>
                        @if ($errors->has('expected_date'))
                        <p style="color: red; margin: 0;">{{ $errors->first('expected_date') }}</p>
                        @endif
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Name * </label>
                        <input type="text" class="form-control" name="name" value="{{$requisition->name?:old('name')}}" placeholder="Enter name" required="">
                        @if ($errors->has('name'))
                        <p style="color: red; margin: 0;">{{ $errors->first('name') }}</p>
                        @endif
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>ID Number </label>
                        <input type="text" class="form-control" name="employe_number" value="{{$requisition->employe_number?:old('employe_number')}}" placeholder="Enter  ID Number">
                        @if ($errors->has('employe_number'))
                        <p style="color: red; margin: 0;">{{ $errors->first('employe_number') }}</p>
                        @endif
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Status*</label>
                        <select class="form-control" name="status" required="" >
                            <option value="pending" {{$requisition->status=='pending'?'selected':''}} >Pending</option>
                            <option value="approved" {{$requisition->status=='approved'?'selected':''}} > Approved</option>
                            <option value="cancelled" {{$requisition->status=='cancelled'?'selected':''}} > Cancelled</option>
                        </select>
                        @if ($errors->has('status'))
                        <p style="color: red; margin: 0;">{{ $errors->first('status') }}</p>
                        @endif
                    </div>

                    <div class="col-md-12 mt-3">
                        <label>Notes</label>
                        <textarea class="form-control" name="note" rows="3"></textarea>
                        @if ($errors->has('note'))
                        <p style="color: red; margin: 0;">{{ $errors->first('note') }}</p>
                        @endif
                    </div>
                </div>

                <br><br>

                <h5><b>Requision Items</b></h5>

                <div class="cardItems">
                    @include(adminTheme().'purchases.requisitions.includes.items')
                </div>

                <br>
                <button type="submit" class="btn btn-success">
                    <i class="bx bx-check"></i> Submit Requision
                </button>
            </form>

        </div>
    </div>
</div>

@endsection

@push('js')

<script>
$(document).ready(function(){

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

    $(document).on('keyup change', '.qty, .price', function(){
        updateTotalSummary();
    });

    // === Update individual item via AJAX ===
    $(document).on('keyup change', '.updateItem, .changeMode', function(){
        let url = $(this).data('url');
        let name = $(this).data('name');
        let value = $(this).val();

        $.ajax({
            url: url,
            type: 'POST',
            data: {'name': name, 'data': value},
            dataType: 'json',
            success: function(res){
                if(res.view){
                    // $('.cardItems').html(res.view);
                    updateTotalSummary();
                }
            },
            error: function(){
                alert('Error updating item.');
            }
        });
    });

    // === Add / Remove item ===
    $(document).on('click', '.addItem, .removeItem', function(){
        let url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(res){
                if(res.view){
                    $('.cardItems').html(res.view);
                    updateTotalSummary();
                }
            },
            error: function(){
                alert('Error updating items.');
            }
        });
    });

    // === Item search ===
    $(document).on('keyup', '.SearchQuery', function(){
        let url = $(this).data('url');
        let type = $(this).data('type');
        let search = $(this).val();

        $.ajax({
            url: url,
            type: 'GET',
            data: {'search': search},
            dataType: 'json',
            success: function(res){
                if(type == 'goods'){
                    $('.itemSearch').html(res.view).show();
                }
            },
            error: function(){
                alert('Search error');
            }
        });
    });

    // === Add item from search ===
    $(document).on('click', '.addDataQuery', function(){
        let url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(res){
                if(res.view){
                    $('.cardItems').html(res.view);
                    updateTotalSummary();
                    $('.itemSearch').hide();
                }
            },
            error: function(){
                alert('Error adding item');
            }
        });
    });

    // === Hide search dropdown on click outside ===
    $(document).on('click', function(e){
        if(!$(e.target).closest('.searchGrid').length){
            $('.itemSearch').hide();
        }
    });

});

</script>

@endpush

