@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Quotation Edit')}}</title>
@endsection @push('css')

<style>
    textarea::-webkit-scrollbar {
        width: 8px;
    }
    textarea::-webkit-scrollbar-track {
        -webkit-box-shadow: gray;
    }
    textarea::-webkit-scrollbar-thumb {
      background-color: darkgrey;
    }
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
    .note-editable p {
        font-size: 10px;
        font-family: times new romance;
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
    
    .reInvoiceTable tr th{
        padding:3px;
        background: #f0f0f0;
    }
    .reInvoiceTable tr td{
        padding:3px;
    }
    
</style>
@endpush @section('contents')

<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>Quotation</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item"><a href="{{route('admin.quotations')}}">Quotations List</a></li>
        <li class="item">Quotation</li>
    </ol>
</div>

<div class="flex-grow-1">
    
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Quotation</h3>
             <div class="dropdown">

                 @isset(json_decode(Auth::user()->permission->permission, true)['quotation']['view'])
                 <a href="{{route('admin.quotationsAction',['view',$invoice->id])}}" target="_blank" class="btn-custom primary">
                     <i class="bx bx-file"></i> View Quotation
                 </a>
                 @endisset
             </div> 
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')
            <form action="{{route('admin.quotationsAction',['update',$invoice->id])}}">
            @csrf
            <div class="cardItems">
                @include(adminTheme().'quotations.includes.piOrderItems')
            </div>
        
            <div class="row">
                <div class="col-md-12">
                    <br><br>
                    <div class="form-group">
                        <label>Remarks:</label>
                        <textarea class="form-control" name="remark" placeholder="Write Remark here..">{{old('remark')?:$invoice->remark}}</textarea>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Terms & Conditions</label>
                                <textarea class="form-control summernote" rows="12" name="terms_conditions" placeholder="Write Terms and Conditions">{!!$invoice->note!!}</textarea>
                            </div>
                        </div>
                    </div>
                    <button type="type" class="btn btn-success"><i class="bx bx-check"></i> Update</button>
                </div>
            </div>
            </form>
        </div>
    </div>

</div>




 <!-- Modal -->
 <div class="modal fade text-left" id="AddCompany" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	   <div class="modal-body">
	   		<label for="name">Search Company </label>
            <div class="input-group">
                <input type="text" class="form-control {{$errors->has('name')?'error':''}} SearchQuery" data-type="company" data-url="{{route('admin.quotationsAction',['search-company',$invoice->id])}}" placeholder="Search name.." required="">
			    <div class="input-group-append">
                    <span class="input-group-text"><i class='bx bx-search'></i></span>
                </div>
			</div>
			<div class="customeSearch searchlist" style="height:200px;overflow:auto;">
		        @include(adminTheme().'quotations.includes.searchCompany')
		    </div>
	   </div>
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
                if(type=='company'){
                    $('.customeSearch').empty().append(data.view);
                }else if(type=='goods'){
                    $('.itemSearch').empty().append(data.view);
                }else{
                    $('.marchantizerSearch').empty().append(data.view);
                }
    
              },error: function () {
                  alert('error');
    
                }
            });
                
        });
        
        
        $(document).on('click','.addDataQuery',function(){
            var url =$(this).data('url');
            var type =$(this).data('type');
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              success : function(data){
                $('.cardItems').empty().append(data.view);
                $('#AddMarchantizer').modal('hide');
                $('#AddCompany').modal('hide');
                
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
        
        $(document).on('change','.updateItem',function(){
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
<script>
    $(".summernote").summernote({
        placeholder: "Write Terms and Conditions",
        tabsize: 2,
        height: 300,
        toolbar: [
            ["font", ["bold", "underline"]],
            ["para", ["ul", "ol", "paragraph"]],
        ],
    });
</script>
@endpush