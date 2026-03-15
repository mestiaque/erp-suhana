@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Invoices Edit')}}</title>
@endsection @push('css')

<style>
    
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 5px;
    }
    .select2-container .select2-selection--single{
        height: 35px;
        padding: 3px;
    }
    
    .searchlist ul {
        list-style: none;
        padding: 5px;
    }
    
    .searchlist ul li {
        border-top: 1px solid #dbd6d6;
        padding: 5px 0;
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
    
    .invoiceTable tr th {
        padding: 5px;
    }
    
    .invoiceTable tr td {
        padding: 5px;
    }
    
</style>
@endpush @section('contents')

<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>LC Invoice</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item"><a href="{{route('admin.purchases')}}">Purchases Invoices List</a></li>
        <li class="item">Purchase Invoice</li>
    </ol>
</div>

<div class="flex-grow-1">
    
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Purchase Invoice</h3>
             <div class="dropdown">
                 @isset(json_decode(Auth::user()->permission->permission, true)['purchases']['view'])
                 <a href="{{route('admin.purchasesAction',['view',$invoice->id])}}" target="_blank" class="btn-custom primary">
                     <i class="bx bx-file"></i> View Purchase
                 </a>
                 @endisset
             </div>
        </div>
        <div class="card-body">
            <form action="{{route('admin.purchasesAction',['update',$invoice->id])}}">
            @csrf
            <div class="cardItems">
                @include(adminTheme().'purchases.includes.orderItems')
            </div>
            
            <br><br><br>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Purchase Note:</label>
                        <textarea class="form-control" rows="5" name="note" placeholder="Write Note">{!!$invoice->note!!}</textarea>
                    </div>
                </div>
            </div>
            <button type="type" class="btn btn-success"><i class="bx bx-check"></i> Update</button>

            
            </form>
            
            
            
        </div>
    </div>
</div>

@endsection 
@push('js') 


<script>
    $(document).ready(function() {
        
        $(document).on('keyup change','.calculate',function(){
            var id =$(this).data('id');
            var quantity =$('.quantity_'+id).val();
            if (isNaN(parseFloat(quantity))) {
                quantity =0;
            }
            
            var price =$('.price_'+id).val();
            if (isNaN(parseFloat(price))) {
                price =0;
            }
            var total =quantity*price;
            $('.priceTotal_'+id).empty().append(total.toFixed(2));
            
        });
        
        $(document).on('keyup','.SearchQuery',function(){
            var url =$(this).data('url');
            var type =$(this).data('type');
            var search =$(this).val();
            
            $.ajax({
                url:url,
                dataType: 'json',
                cache: false,
                data: {'search':search},
                success : function(data){
                    $('.searchlist').empty().append(data.view);
                },error: function () {
                  alert('error');
    
                }
            });
                
        });
        
        
        $(document).on('click','.addDataQuery',function(){
            var url =$(this).data('url');
            var type =$(this).data('type');
            var that =$(this);
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              success : function(data){
                $('.cardItems').empty().append(data.view);
                $('.SearchPiSummery').empty().append(data.viewSummery);
                $('#AddBank').modal('hide');
                if(type=='pinumbers'){
                    that.parent().remove();
                }
                
              },error: function () {
                  alert('error');
    
                }
            });
        });
       
        $(document).on('change','.selectSupplier',function(){
            var url =$(this).data('url');
            var id =$(this).val();
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              data:{'supplier_id':id},
              success : function(data){
                $('.cardItems').empty().append(data.view);
              },error: function () {
                  alert('error');
    
                }
            });
        });
        
        $(document).on('click','.addItem,.removeItem',function(){
            var url =$(this).data('url');
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              success : function(data){
                $('.cardItems').empty().append(data.view);
              },error: function () {
                  alert('error');
    
                }
            });
        });
        
        $(document).on('keyup','.updateItem',function(){
            var url =$(this).data('url');
            var name =$(this).data('name');
            var data =$(this).val();
            updateItemAjax(url,name,data);
        });
        
        $(document).on('change','.updateItem, .updateInfo',function(){
            var url =$(this).data('url');
            var name =$(this).data('name');
            var data =$(this).val();
            updateItemAjax(url,name,data);
        });
        
        function updateItemAjax(url,name,data){
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              data: {'name':name,'data':data},
              success : function(data){
                //$('.cardItems').empty().append(data.view);
              },error: function () {
                  alert('error');
    
                }
            });
        }
        
        $(document).on('focus', '.searchGrid input', function() {
            $('.itemSearch').show();
        });
        
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.searchGrid').length) {
                $('.itemSearch').hide();
            }
        });
        
        
    });
</script>

@endpush