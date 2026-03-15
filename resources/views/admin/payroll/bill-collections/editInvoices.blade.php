@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Bill Collection Edit')}}</title>
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
    <h1>Bill</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item"><a href="{{route('admin.billCollection')}}">Bill Collection List</a></li>
        <li class="item">Bill Collection</li>
    </ol>
</div>

<div class="flex-grow-1">
    
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Bill Collection</h3>
             <div class="dropdown">

                 @isset(json_decode(Auth::user()->permission->permission, true)['sales']['view'])
                 <a href="{{route('admin.billCollectionAction',['view',$invoice->id])}}" target="_blank" class="btn-custom primary">
                     <i class="bx bx-file"></i> View Bill
                 </a>
                 @endisset
             </div> 
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')
            <div class="cardItems">
                @include(adminTheme().'bill-collections.includes.orderItems')
            </div>
        </div>
    </div>

</div>
 
<!-- Add Modal -->
<div class="modal fade text-left" id="AddBill" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	    <form action="{{route('admin.billCollectionAction',['payment-create',$invoice->id])}}" method="post" enctype="multipart/form-data">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Bill</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	        <div class="row">
             	    <div class="col-md-6">
            	        <div class="form-group">
            			    <label for="created_at">Date*</label>
                            <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{Carbon\Carbon::now()->format('Y-m-d')}}" name="created_at"  required="">
            				@if ($errors->has('created_at'))
            				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
            				@endif
                     	</div>
                    </div>
             	    <div class="col-md-6">
            	   		<div class="form-group">
            			    <label for="amount">Amount* </label>
                            <input type="number" class="form-control {{$errors->has('amount')?'error':''}}" name="amount" placeholder="Enter amount" required="">
            				@if ($errors->has('amount'))
            				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('amount') }}</p>
            				@endif
                     	</div>
                 	</div>
                </div>
             	<div class="row">
             	    <div class="col-md-6">
            	   		<div class="form-group">
            			    <label for="account">Account* </label>
                            <select class="form-control" name="account" required="" >
                                <option value="">Select Account</option>
                                @foreach($accountMethods as $method)
                                <option value="{{$method->id}}" {{request()->account==$method->id?'selected':''}}>{{$method->name}}</option>
                                @endforeach
                            </select>
            				@if ($errors->has('account'))
            				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('account') }}</p>
            				@endif
                     	</div>
                 	</div>
             	    <div class="col-md-6">
            	   		<div class="form-group">
            			    <label for="method">Payment Method* </label>
                            <select class="form-control" name="method" required="" >
                                <option value="">Select Method</option>
                                @foreach($paymentMethods as $method)
                                <option value="{{$method->id}}" {{request()->payment==$method->id?'selected':''}}>{{$method->name}}</option>
                                @endforeach
                            </select>
            				@if ($errors->has('method'))
            				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('method') }}</p>
            				@endif
                     	</div>
                 	</div>
             	</div>
    	   		<div class="form-group">
    			    <label for="attachment">Attachment <small>(Image/max 2mb)</small> </label>
                    <input type="file" class="form-control {{$errors->has('attachment')?'error':''}}" accept="image/*" name="attachment" style="padding: 3px;">
    				@if ($errors->has('attachment'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
    				@endif
             	</div>
    	   		
             	<div class="form-group">
    			    <label for="note">Note</label>
                    <input type="text" class="form-control {{$errors->has('note')?'error':''}}" name="note" placeholder="Enter Note">
    				@if ($errors->has('note'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('note') }}</p>
    				@endif
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Bill</button>
    	   </div>
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