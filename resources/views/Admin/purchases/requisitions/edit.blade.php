@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Requision Create') }}</title>
@endsection

@push('css')
<style>
    textarea::-webkit-scrollbar { width: 8px; }
    textarea::-webkit-scrollbar-thumb { background-color: darkgrey; }
    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { top: 5px; }
    .select2-container .select2-selection--single { height: 35px; padding: 3px; }

    .itemSearch{
        height: 200px; overflow: auto;
        position: absolute; width: 100%; background: #fff;
        border: 1px solid #ccc; border-top: 0; display:none;
    }
    .reInvoiceTable tr th,
    .reInvoiceTable tr td{ padding: 5px; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Create Requision</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">Requision</li>
    </ol>
</div>

<div class="flex-grow-1">

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Requision Form</h3>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.purchasesRequisitionsAction', ['create']) }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <label>Department</label>
                        <select class="form-control select2" name="department_id" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dep)
                                <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Requision Date</label>
                        <input type="date" class="form-control" name="requision_date" required>
                    </div>

                    <div class="col-md-4">
                        <label>Expected Receive Date</label>
                        <input type="date" class="form-control" name="expected_date" required>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label>Notes</label>
                        <textarea class="form-control" name="note" rows="3"></textarea>
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
            data: {'name': name, 'data': value, '_token': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            success: function(res){
                if(res.view){
                    $('.cardItems').html(res.view);
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

