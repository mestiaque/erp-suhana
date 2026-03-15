@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Suppliers Trading')}}</title>
@endsection @push('css')
<style type="text/css">
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
</style>
@endpush @section('contents')

<div class="flex-grow-1" >
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Suppliers Trading</h3>
             <div class="dropdown">
                 <a href="javascript:void(0)" class="btn-custom success" data-toggle="modal" data-target="#AddGood" style="padding:5px 15px;">
                     <i class="bx bx-plus"></i> Add Trade
                 </a>
                 <a href="{{route('admin.supplierTrading')}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')
            <div class="accordion-box">
                <div class="accordion">
                    <div class="accordion-item">
                     <a class="accordion-title" href="javascript:void(0)">
                         <i class="bx bx-filter-alt"></i>
                        Search click Here..
                     </a>
                     <div class="accordion-content" style="border:1px solid #e1000a;border-top:0;">
                        <form action="{{route('admin.supplierTrading')}}">
                            <div class="row">
                                <div class="col-md-5 mb-0">
                                    <div class="input-group">
                                        <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                                        <input type="date" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                                    </div>
                                </div>
                                <div class="col-md-3 mb-0">
                                    <select class="form-control" name="supplier">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}" {{request()->supplier==$supplier->id?'selected':''}}  >{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-0">
                                    <div class="input-group">
                                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Reff Name" class="form-control {{$errors->has('search')?'error':''}}" />
                                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
            <br>
            
             <form action="{{route('admin.supplierTrading')}}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group mb-1">
                            <select class="form-control form-control-sm rounded-0" name="action" required="">
                                <option value="">Select Action</option>
                                <option value="5">Delete</option>
                            </select>
                            <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        
                    </div>
                </div>
            
             <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px;width: 100px;padding-right:0;">
                                <div class="checkbox mr-3">
                                 <input class="inp-cbx" id="checkall" type="checkbox" style="display: none;" />
                                 <label class="cbx" for="checkall">
                                     <span>
                                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                         </svg>
                                     </span>
                                     All <span class="checkCounter"></span> 
                                 </label>
                                </div>
                            </th>
                            <th style="min-width: 100px;">Date</th>
                            <th style="min-width: 150px;">Supplier</th>
                            <th style="min-width: 150px;">Reff</th>
                            <th style="min-width: 100px;">Debit</th>
                            <th style="min-width: 100px;">Credit</th>
                            <th style="min-width: 100px;">Balance</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($traddings as $i=>$tradding)
                        <tr>
                            <td>
                                <div class="checkbox">
                                     <input class="inp-cbx" id="cbx_{{$tradding->id}}" type="checkbox" name="checkid[]" value="{{$tradding->id}}" style="display: none;" />
                                     <label class="cbx" for="cbx_{{$tradding->id}}">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                     </label>
                                 </div>
                                <span style="margin:0 5px;">{{$traddings->currentpage()==1?$i+1:$i+($traddings->perpage()*($traddings->currentpage() - 1))+1}}</span>
                                @if($tradding->status=='active')
                                <span style="color: #43d39e;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                @else
                                <span style="color: #FF9800;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-analyse"></i>
                                </span>
                                @endif
                            </td>
                            <td>{{$tradding->created_at->format('d-m-Y')}}</td>
                            <td>{{$tradding->supplier?$tradding->supplier->name:'Not Found'}}</td>
                            <td>
                                <span>{{$tradding->title}}</span>
                                @if($tradding->imageFile) <a href="{{asset($tradding->imageFile->file_url)}}" target="_blank"><i class="bx bx-file"></i></a> @endif
                            </td>
                            <td>{{$tradding->type==1?priceFormat($tradding->amount):'-'}}</td>
                            <td>{{$tradding->type==2?priceFormat($tradding->amount):'-'}}</td>
                            <td>
                                @if($tradding->supplier)
                                @if($tradding->supplier->amount >=0) <span style="color:green;"> @else <span style="color:red;"> @endif
                                @else
                                <span style="color:red;">
                                @endif
                                {{priceFormat($tradding->balance)}}
                                </span>
                            </td>
                            <td class="center">

                                <a href="{{route('admin.supplierTradingAction',['edit',$tradding->id])}}"  class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#ViewTrading_{{$tradding->id}}" class="btn-custom yellow">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$traddings->links('pagination')}}
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
 <div class="modal fade text-left" id="AddGood" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	   <div class="modal-body">
	   		<label for="name">Search Supplier </label>
            <div class="input-group">
                <input type="text" class="form-control {{$errors->has('name')?'error':''}} SearchQuery" data-type="received" data-url="{{route('admin.supplierTradingAction','search-supplier')}}" placeholder="Search Name.." required="">
			    <div class="input-group-append">
                    <span class="input-group-text"><i class='bx bx-search'></i></span>
                </div>
			</div>
			<div class="employeeSearch searchlist" style="height:200px;overflow:auto;">
		    </div>
	   </div>
	 </div>
   </div>
 </div>
 
 
 
<!--View Modal -->
@foreach($traddings as $i=>$dpm)
 <div class="modal fade text-left" id="ViewTrading_{{$dpm->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
    	   <div class="modal-header">
    		 <h4 class="modal-title">View @if($dpm->type==1)
                Received Goods
                @elseif($dpm->type==2)
                Pay Bill
                @endif</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	       <div class="table-responsive">
    	           <table class="table table-borderless expenseTableView">
    	               <tr>
    	                   <th style="width:150px;min-width:150px;">Date</th>
    	                   <th style="width:25px;min-width:25px;">:</th>
    	                   <td>{{$dpm->created_at->format('Y-m-d')}}</td>
    	               </tr>
    	               <tr>
    	                   <th>Ref/Title</th>
    	                   <th>:</th>
    	                   <td>{{$dpm->title}}</td>
    	               </tr>
    	               @if($dpm->type==2)
    	               <tr>
    	                   <th>Account Method</th>
    	                   <th>:</th>
    	                   <td>{{$dpm->method?$dpm->method->name:''}}</td>
    	               </tr>
    	               @endif
    	               <tr>
    	                   <th>Amount</th>
    	                   <th>:</th>
    	                   <td>{{priceFormat($dpm->amount)}}</td>
    	               </tr>
    	                <tr>
    	                   <th>Attachment</th>
    	                   <th>:</th>
    	                   <td>
    	                       @if($dpm->imageFile)
    	                       <a href="{{asset($dpm->imageFile->file_url)}}" class="btn-custom primary" target="_blank">View Attachment</a>
    	                       @else
    	                       <span>No Attachment</span>
    	                       @endif
    	                       
    	                   </td>
    	               </tr>
    	               <tr>
    	                   <th>Description</th>
    	                   <th>:</th>
    	                   <td>{!!$dpm->description!!}</td>
    	               </tr>
    	           </table>
    	       </div>
    	   </div>
	 </div>
   </div>
 </div>
@endforeach

@endsection @push('js')

<script>
    
    $(document).on('keyup','.SearchQuery',function(){
            var url =$(this).data('url');
            var search =$(this).val();
            
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              data: {'search':search},
              success : function(data){
                    $('.employeeSearch').empty().append(data.view);
              },error: function () {
                  alert('error');
    
                }
            });
                
        });
    
</script>

@endpush

