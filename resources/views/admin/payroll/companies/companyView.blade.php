@extends(adminTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Company View')}}</title>
@endsection 
@push('css')

<style type="text/css">
    .nav-tabs .nav-link {
        font-size: 16px;
        font-weight: bold;
    }
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #e9ecef;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    
    
    .status span {
        background: #f2f2f2;
        padding: 5px 15px;
        display: inline-block;
        margin-bottom: 5px;
        border-radius: 5px;
        color: #4c4a4a;
        font-weight: bold;
        border: 1px solid #c6c6c6;
    }
    
    .emiTable tr th{
        padding:2px 5px;
    }
    .emiTable tr td{
        padding:2px 5px;
    }
    
    @media only screen and (max-width: 678px) {
        .nav-tabs .nav-item {
            width: 50%;
            text-align: center;
            border: 1px solid #e7e7e7;
        }
    }
    
    
</style>
@endpush 
@section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-8 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Company View</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-4 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-outline-primary" href="{{route('admin.companies')}}">BACK</a>
            <a class="btn btn-outline-primary" href="{{route('admin.companiesAction',['view',$company->id])}}">
                <i class='bx bx-loader-circle' ></i>
            </a>
        </div>
    </div>
</div>


<section class="flex-grow-1">
@include(adminTheme().'alerts')
    
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                        <h4 class="card-title">Company View ({{$company->deed_serial}}/{{$company->concernShort()}} - {{$company->factory_name?:$company->owner_name}})</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="nav nav-tabs">
                              <li class="nav-item">
                                <a class="nav-link {{$action=='view'?'active':''}}" href="{{route('admin.companiesAction',['view',$company->id])}}">Information</a>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link {{$action=='sales'?'active':''}}" href="{{route('admin.companiesAction',['sales',$company->id])}}">Sales ({{$sales->count()}})</a>
                              </li>
                              <li class="nav-item" >
                                <a class="nav-link {{$action=='product'?'active':''}}" href="{{route('admin.companiesAction',['product',$company->id])}}">Product ({{$products->count()}})</a>
                              </li>
                              
                              @isset(json_decode(Auth::user()->permission->permission, true)['company']['service'])
                              <li class="nav-item">
                                <a class="nav-link {{$action=='service'?'active':''}}" href="{{route('admin.companiesAction',['service',$company->id])}}">Service ({{$services->count()}})</a>
                              </li>
                              @endisset
                              
                              <li class="nav-item">
                                <a class="nav-link {{$action=='quotation'?'active':''}}" href="{{route('admin.companiesAction',['quotation',$company->id])}}">Quotation ({{$quotations->count()}})</a>
                              </li>
                              <!--<li class="nav-item">-->
                              <!--  <a class="nav-link {{$action=='lcinvoice'?'active':''}}" href="{{route('admin.companiesAction',['lcinvoice',$company->id])}}">LC Invoice ({{$lcInvoices->count()}})</a>-->
                              <!--</li>-->
                              <li class="nav-item">
                                <a class="nav-link {{$action=='meeting'?'active':''}}" href="{{route('admin.companiesAction',['meeting',$company->id])}}">Meeting ({{$meetings->count()}})</a>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link {{$action=='visit'?'active':''}}" href="{{route('admin.companiesAction',['visit',$company->id])}}">Visit ({{$visits->total()}})</a>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link {{$action=='note'?'active':''}}" href="{{route('admin.companiesAction',['note',$company->id])}}">Note ({{$notes->total()}})</a>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link {{$action=='note'?'active':''}}" href="{{route('admin.companiesAction',['commitment',$company->id])}}">Commitment ({{$commitments->count()}})</a>
                              </li>
                            </ul>
                            <br>
                            
                            @if($action=='product')
                            
                            <div class="table-responsive">
                                <table class="table table-bordered profileTable">
                                    <tr>
                                        <th style="width: 130px;min-width: 130px;">Date</th>
                                        <th style="min-width:250px;">Name</th>
                                        <th style="width: 130px;min-width:130px;">Price</th>
                                        <th style="width: 120px;min-width:120px;">Action</th>
                                    </tr>
                                    @foreach($products as $product)
                                    <tr>
                                        <td>{{$product->order->created_at->format('d-m-Y')}}</td>
                                        <td>{{$product->description}} X {{$product->quantity}} {{$product->unit}}</td>
                                        <td>{{$product->order->currency}}{{number_format($product->final_price,2)}}</td>
                                        <td>
                                            @isset(json_decode(Auth::user()->permission->permission, true)['company']['service'])
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#AddSerice_{{$product->id}}" class="btn-custom success"><i class="bx bx-plus"></i> Service</a>
                                            
                                            <!-- Add Modal -->
                                            <div class="modal fade text-left" id="AddSerice_{{$product->id}}" tabindex="-1" role="dialog">
                                               <div class="modal-dialog" role="document">
                                            	 <div class="modal-content">
                                            	    <form action="{{route('admin.companiesAction',['service-add',$company->id])}}" method="post">
                                            	   	  @csrf
                                            	   	  <input type="hidden" value="{{$product->id}}" name="item_id">
                                                	   <div class="modal-header">
                                                		 <h4 class="modal-title">Add Service</h4>
                                                		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                		   <span aria-hidden="true">&times; </span>
                                                		 </button>
                                                	   </div>
                                                	   <div class="modal-body">
                                                	       <div class="row">
                                                	           <div class="col-md-6 form-group">
                                                    			    <label for="employee">Employee*</label>
                                                                    <select class="select29_{{$product->id}}" data-placeholder="Select Employee" name="employee" required="">
                                                                        @foreach($users as $user)
                                                                        <option value="{{$user->id}}" {{Auth::id()==$user->id?'selected':''}}>{{$user->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                    				@if ($errors->has('employee'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('employee') }}</p>
                                                    				@endif
                                                             	</div>
                                                    	       <div class="col-md-6 form-group">
                                                    			    <label for="engineer">Engineer</label>
                                                                    <select class="form-control" name="engineer">
                                                                        <option value="">Select Engineer</option>
                                                                        @foreach($engineers as $engineer)
                                                                        <option value="{{$engineer->id}}">{{$engineer->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                    				@if ($errors->has('engineer'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('engineer') }}</p>
                                                    				@endif
                                                             	</div>
                                                    	   		
                                                	       </div>
                                                	   		<div class="form-group">
                                                			    <label for="title">Service Title</label>
                                                                <input type="text" class="form-control {{$errors->has('title')?'error':''}}" name="title" placeholder="Enter title">
                                                				@if ($errors->has('title'))
                                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('title') }}</p>
                                                				@endif
                                                         	</div>
                                                	   		<div class="form-group">
                                                			    <label for="description">Description</label>
                                                                <textarea type="text" class="form-control {{$errors->has('description')?'error':''}}" name="description" placeholder="Enter Description"></textarea>
                                                				@if ($errors->has('description'))
                                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
                                                				@endif
                                                         	</div>
                                                	   		
                                                         	<div class="row">
                                                         	    <div class="form-group col-md-6">
                                                    			    <label for="created_at">Service Date*</label>
                                                                    <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{Carbon\Carbon::now()->format('Y-m-d')}}" name="created_at" required="">
                                                    				@if ($errors->has('created_at'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
                                                    				@endif
                                                             	</div>
                                                         	    <div class="form-group col-md-6">
                                                    			    <label for="status">Service Status*</label>
                                                                    <select class="form-control" name="status" required="" >
                                                                        <option value="open">Open</option>
                                                                        <option value="processing">Processing</option>
                                                                        <option value="completed">Completed</option>
                                                                        <option value="close">Close</option>
                                                                        <option value="cancelled">Cancelled</option>
                                                                    </select>
                                                    				@if ($errors->has('status'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
                                                    				@endif
                                                             	</div>
                                                         	</div>
                                                	   </div>
                                                	   <div class="modal-footer">
                                                		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                                		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Submit </button>
                                                	   </div>
                                            	   </form>
                                            	 </div>
                                               </div>
                                            </div>
                                            
                                            @endisset
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                            
                            @elseif($action=='service')
                            @isset(json_decode(Auth::user()->permission->permission, true)['company']['service'])
                            <div class="status">
                                <span>Open ({{$services->where('status','open')->count()}})</span>
                                <span>Processing ({{$services->where('status','processing')->count()}})</span>
                                <span>Completed ({{$services->where('status','completed')->count()}})</span>
                                <span>closed ({{$services->where('status','close')->count()}})</span>
                                <span>Cancelled ({{$services->where('status','cancelled')->count()}})</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered profileTable">
                                    <tr>
                                        <th style="min-width: 250px;width: 250px;">Service Title</th>
                                        <th style="min-width: 300px;">Description</th>
                                    </tr>
                                    @foreach($services as $service)
                                    <tr>
                                        <td>
                                            {{$service->title}}
                                            <br>
                                            <b>Date:</b> {{$service->created_at->format('d.m.Y')}}
                                            <br>
                                            <b>Status:</b> {{ucfirst($service->status)}}
                                            <br>
                                            <div>
                                                <a href="javascript:void(0)" class="btn-custom success" data-toggle="modal" data-target="#editSerice_{{$service->id}}">Edit</a>
                                                <a href="javascript:void(0)" class="btn-custom danger ml-5" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                                            </div>
                                            
                                            <!-- Add Modal -->
                                            <div class="modal fade text-left" id="editSerice_{{$service->id}}" tabindex="-1" role="dialog">
                                               <div class="modal-dialog" role="document">
                                            	 <div class="modal-content">
                                            	    <form action="{{route('admin.companiesAction',['service-update',$company->id])}}" method="post">
                                            	   	  @csrf
                                            	   	  <input type="hidden" value="{{$service->id}}" name="item_id">
                                                	   <div class="modal-header">
                                                		 <h4 class="modal-title">Edit Service</h4>
                                                		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                		   <span aria-hidden="true">&times; </span>
                                                		 </button>
                                                	   </div>
                                                	   <div class="modal-body">
                                                	        <div class="row">
                                                	            <div class="col-md-6 form-group">
                                                    			    <label for="employee">Employee*</label>
                                                                    <select class="select299_{{$service->id}}" data-placeholder="Select Employee" name="employee" required="">
                                                                        @foreach($users as $user)
                                                                        <option value="{{$user->id}}" {{$service->employee_id==$user->id?'selected':''}}>{{$user->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                    				@if ($errors->has('employee'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('employee') }}</p>
                                                    				@endif
                                                             	</div>
                                                    	        <div class="col-md-6 form-group">
                                                    			    <label for="engineer">Engineer</label>
                                                                    <select class="form-control" name="engineer">
                                                                        <option value="">Select Engineer</option>
                                                                        @foreach($engineers as $engineer)
                                                                        <option value="{{$engineer->id}}" {{$service->engineer_id==$engineer->id?'selected':''}} >{{$engineer->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                    				@if ($errors->has('engineer'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('engineer') }}</p>
                                                    				@endif
                                                             	</div>
                                                	        </div>
                                                         	
                                                	   		<div class="form-group">
                                                			    <label for="title">Service Title</label>
                                                                <input type="text" class="form-control {{$errors->has('title')?'error':''}}" name="title" value="{{$service->title}}" placeholder="Enter title" >
                                                				@if ($errors->has('title'))
                                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('title') }}</p>
                                                				@endif
                                                         	</div>
                                                	   		<div class="form-group">
                                                			    <label for="description">Description</label>
                                                                <textarea type="text" class="form-control {{$errors->has('description')?'error':''}}" rows="7" name="description" placeholder="Enter Description">{{$service->description}}</textarea>
                                                				@if ($errors->has('description'))
                                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
                                                				@endif
                                                         	</div>
                                                	   		
                                                         	<div class="row">
                                                         	    <div class="form-group col-md-6">
                                                    			    <label for="created_at">Service Date*</label>
                                                                    <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{$service->created_at->format('Y-m-d')}}" name="created_at" required="">
                                                    				@if ($errors->has('created_at'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
                                                    				@endif
                                                             	</div>
                                                         	    <div class="form-group col-md-6">
                                                    			    <label for="status">Service Status*</label>
                                                                    <select class="form-control" name="status" required="" >
                                                                        <option value="open" {{$service->status=='open'?'selected':''}} >Open</option>
                                                                        <option value="processing" {{$service->status=='processing'?'selected':''}} >Processing</option>
                                                                        <option value="completed" {{$service->status=='completed'?'selected':''}} >Completed</option>
                                                                        <option value="close" {{$service->status=='close'?'selected':''}} >Close</option>
                                                                        <option value="cancelled" {{$service->status=='cancelled'?'selected':''}} >Cancelled</option>
                                                                    </select>
                                                    				@if ($errors->has('status'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
                                                    				@endif
                                                             	</div>
                                                         	</div>
                                                	   </div>
                                                	   <div class="modal-footer">
                                                		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                                		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Update </button>
                                                	   </div>
                                            	   </form>
                                            	 </div>
                                               </div>
                                            </div>
                                            
                                            
                                        </td>
                                        <td>
                                            <b>Engineer:</b> {{$service->engineer?$service->engineer->name:''}}
                                        
                                            <div>
                                            {{$service->description}}
                                            </div>
                                            <b>Employee:</b> {{$service->employee?$service->employee->name:''}}
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                </table>
                            </div>
                            @endisset
                            
                            @elseif($action=='sales')
                            <div class="status">
                                <span>Total Sale :
                                    @if($company->sales->where('currency','USD')->sum('grand_total') > 0)
                                    USD {{number_format($company->sales->where('currency','USD')->sum('grand_total'),2)}} 
                                    @endif
                                    @if($company->sales->where('currency','<>','USD')->sum('grand_total') > 0)
                                    BDT {{number_format($company->sales->where('currency','<>','USD')->sum('grand_total'),2)}}
                                    @endif
                                </span>
                                <span>
                                    Paid :
                                    @if($company->sales->where('currency','USD')->sum('paid_amount') > 0)
                                    USD {{number_format($company->sales->where('currency','USD')->sum('paid_amount'),2)}} 
                                    @endif
                                    @if($company->sales->where('currency','<>','USD')->sum('paid_amount') > 0)
                                    BDT {{number_format($company->sales->where('currency','<>','USD')->sum('paid_amount'),2)}}
                                    @endif
                                </span>
                                <span style="background: #F44336;color: white;">
                                    Due :
                                    @if($company->sales->where('currency','USD')->sum('due_amount') > 0)
                                    USD {{number_format($company->sales->where('currency','USD')->sum('due_amount'),2)}} 
                                    @endif
                                    @if($company->sales->where('currency','<>','USD')->sum('due_amount') > 0)
                                    BDT {{number_format($company->sales->where('currency','<>','USD')->sum('due_amount'),2)}}
                                    @endif
                                </span>
                            </div>
                            <hr>
                            @isset(json_decode(Auth::user()->permission->permission, true)['company']['sales'])
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#AddSale" class="btn-custom success"><i class="bx bx-plus"></i> Sale</a>
                            <br>            
                            <br>            
                            <!-- Add Modal -->
                            <div class="modal fade text-left" id="AddSale" tabindex="-1" role="dialog">
                               <div class="modal-dialog" role="document">
                            	 <div class="modal-content">
                            	    <form action="{{route('admin.companiesAction',['sale-add',$company->id])}}" method="post">
                            	   	  @csrf
                                	   <div class="modal-header">
                                		 <h4 class="modal-title">Add Sale</h4>
                                		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                		   <span aria-hidden="true">&times; </span>
                                		 </button>
                                	   </div>
                                	   <div class="modal-body">
                                	       <div class="row">
                                    	   		<div class="col-md-12 form-group">
                                    			    <label for="created_at">Sale Date*</label>
                                    			    <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{Carbon\Carbon::now()->format('Y-m-d')}}" name="created_at" required="">
                                    				@if ($errors->has('created_at'))
                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
                                    				@endif
                                             	</div>
                                         	</div>
                                         	
                                         	<div class="form-group">
                                              <div class="input-group">
                                                <select class="form-control SelectItem_0">
                                                  <option value="">Select Product</option>
                                                  @foreach(App\Models\Post::latest()->where('type',3)->where('status','active')->get() as $goods)
                                                    <option value="{{$goods->id}}" data-price="{{$goods->item_price ?: 0}}">
                                                      {{$goods->name}}
                                                    </option>
                                                  @endforeach
                                                </select>
                                                <div class="input-group-text rounded-0 PlusSelectItem" data-id="0" style="cursor:pointer;">
                                                  Add
                                                </div>
                                              </div>
                                            </div>
                                            
                                            <div class="table">
                                              <table class="table table-bordered">
                                                <thead>
                                                  <tr style="background: #f4f4f4;">
                                                    <th style="padding: 5px;">Details</th>
                                                    <th style="padding: 5px;">Qty & Price</th>
                                                    <th style="padding:1px;width: 40px;">
                                                      <span class="btn btn-info btn-sm PlusNewItem" data-id="0"><i class="bx bx-plus"></i></span>
                                                    </th>
                                                  </tr>
                                                </thead>
                                                <tbody class="ItemBody_0">
                                                  <!-- Added rows will appear here -->
                                                </tbody>
                                                <tfoot>
                                                  <tr>
                                                    <th style="padding:2px;text-align:right;">Total</th>
                                                    <th style="background: #f4f4f4;padding: 2px;" class="totalSum">BDT 0.00</th>
                                                    <th></th>
                                                  </tr>
                                                </tfoot>
                                              </table>
                                            </div>

                                         	
                                         	<div class="row">
                                         	    <div class="form-group col-md-4">
                                    			    <label for="paid_amount">Down payment*</label>
                                                    <input type="number" class="form-control {{$errors->has('paid_amount')?'error':''}}" id="paid_amount" name="paid_amount" placeholder="Paid Amount" required="">
                                    				@if ($errors->has('paid_amount'))
                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('paid_amount') }}</p>
                                    				@endif
                                             	</div>
                                         	    <div class="form-group col-md-4">
                                    			    <label for="emi_amount">EMI Amount</label>
                                                    <input type="number" class="form-control {{$errors->has('emi_amount')?'error':''}}" id="emi_amount" name="emi_amount" placeholder="Sale Amount">
                                    				@if ($errors->has('emi_amount'))
                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('emi_amount') }}</p>
                                    				@endif
                                             	</div>
                                         	    <div class="form-group col-md-4">
                                    			    <label for="emi_duration">EMI (Month)</label>
                                                    <input type="number" class="form-control {{$errors->has('emi_duration')?'error':''}}" id="emi_duration" maxlength="2" max="99" oninput="this.value = Math.min(this.value, 99)"  name="emi_duration" placeholder="Duration">
                                    				@if ($errors->has('emi_duration'))
                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('emi_duration') }}</p>
                                    				@endif
                                             	</div>
                                         	</div>
                                	   </div>
                                	   <div class="modal-footer">
                                		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                		 <button type="submit" id="submit_button" class="btn btn-primary"><i class="bx bx-plus"></i> Submit </button>
                                	   </div>
                            	   </form>
                            	 </div>
                               </div>
                            </div>
                            @endisset
                            
                            <div class="salesArea table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width: 50px;min-width: 50px;">SL</th>
                                        <th style="width: 130px;min-width: 130px;">Date of Sale</th>
                                        <th style="width: 50px;min-width: 50px;">QTY</th>
                                        <th style="min-width: 300px;">Confuguration</th>
                                        <th style="width: 150px;min-width: 150px;">Sale Price</th>
                                        <th style="width: 150px;min-width: 150px;">Paid Amount</th>
                                        <th style="width: 150px;min-width: 150px;">EMI Amount</th>
                                        <th style="width: 150px;min-width: 150px;">EMI Paid</th>
                                        <th style="width: 130px;min-width: 130px;">Paid date</th>
                                        <th style="width: 150px;min-width: 150px;">Due Amount</th>
                                        <th style="width: 50px;min-width: 50px;"></th>
                                    </tr>
                                    @foreach($sales as $i=>$sale)
                                    <tr>
                                        <td>{{$i+1}}
                                        @isset(json_decode(Auth::user()->permission->permission, true)['company']['sales'])
                                        <br>
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#editSale_{{$sale->id}}" class="btn-custom"><i class="bx bx-edit"></i></a>
                                        
                                        <!-- Add Modal -->
                                        <div class="modal fade text-left" id="editSale_{{$sale->id}}" tabindex="-1" role="dialog">
                                           <div class="modal-dialog" role="document">
                                        	 <div class="modal-content">
                                        	    <form action="{{route('admin.companiesAction',['sale-update',$company->id])}}" method="post">
                                        	   	  @csrf
                                        	   	   <input type="hidden" value="{{$sale->id}}" name="sale_id">
                                            	   <div class="modal-header">
                                            		 <h4 class="modal-title">Edit Sale</h4>
                                            		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            		   <span aria-hidden="true">&times; </span>
                                            		 </button>
                                            	   </div>
                                            	   <div class="modal-body">
                                            	       <div class="row">
                                                	   		<div class="col-md-12 form-group">
                                                			    <label for="created_at">Sale Date*</label>
                                                			    <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{$sale->created_at->format('Y-m-d')}}" name="created_at" required="">
                                                				@if ($errors->has('created_at'))
                                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
                                                				@endif
                                                         	</div>
                                                     	</div>
                                                     	    
                                                     	    <div class="form-group">
                                                              <div class="input-group">
                                                                <select class="form-control SelectItem_{{$sale->id}}">
                                                                  <option value="">Select Product</option>
                                                                  @foreach(App\Models\Post::latest()->where('type',3)->where('status','active')->get() as $goods)
                                                                    <option value="{{$goods->id}}" data-price="{{$goods->item_price ?: 0}}">
                                                                      {{$goods->name}}
                                                                    </option>
                                                                  @endforeach
                                                                </select>
                                                                <div class="input-group-text rounded-0 PlusSelectItem" data-id="{{$sale->id}}" style="cursor:pointer;">
                                                                  Add
                                                                </div>
                                                              </div>
                                                            </div>
                                                            
                                                            <div class="table">
                                                              <table class="table table-bordered">
                                                                <thead>
                                                                  <tr style="background: #f4f4f4;">
                                                                    <th style="padding: 5px;">Details</th>
                                                                    <th style="padding: 5px;">Qty & Price</th>
                                                                    <th style="padding:1px;width: 40px;">
                                                                      <span class="btn btn-info btn-sm PlusNewItem" data-id="{{$sale->id}}"><i class="bx bx-plus"></i></span>
                                                                    </th>
                                                                  </tr>
                                                                </thead>
                                                            
                                                                <tbody class="ItemBody_{{$sale->id}}">
                                                                  @foreach($sale->items as $i => $item)
                                                                    @php
                                                                      $random = str_pad($item->src_id ?? ($i+1), 2, '0', STR_PAD_LEFT);
                                                                    @endphp
                                                                    <tr class="item_{{ $random }}" data-id="{{ $random }}">
                                                                      <td style="padding:2px;">
                                                                        <input type="hidden" name="itemId[]" value="{{ $item->src_id ?? '0'.$random }}">
                                                                        <textarea class="form-control" name="title[]" placeholder="Write Details">{{ $item->description }}</textarea>
                                                                      </td>
                                                            
                                                                      <td style="padding:2px;">
                                                                        <div class="input-group">
                                                                          <input type="number" class="form-control form-control-sm qty" name="qty[]" value="{{ $item->quantity }}" min="1" style="width:80px;max-width:80px;">
                                                                          <input type="number" class="form-control form-control-sm price" name="price[]" value="{{ $item->price }}" step="0.01" min="0">
                                                                        </div>
                                                                        <b>Price:</b> <span class="subTotal">{{ number_format($item->quantity * $item->price, 2) }}</span>
                                                                      </td>
                                                            
                                                                      <td style="padding:2px;width:40px;">
                                                                        <span class="btn btn-danger btn-sm removeItem"><i class="bx bx-trash"></i></span>
                                                                      </td>
                                                                    </tr>
                                                                  @endforeach
                                                                </tbody>
                                                            
                                                                <tfoot>
                                                                  <tr>
                                                                    <th style="padding:2px;text-align:right;">Total</th>
                                                                    <th style="background: #f4f4f4;padding: 2px;" class="totalSum">
                                                                      BDT {{ number_format($sale->items->sum(fn($x) => $x->quantity * $x->price), 2) }}
                                                                    </th>
                                                                    <th></th>
                                                                  </tr>
                                                                </tfoot>
                                                              </table>
                                                            </div>
                                                        
                                                        {{--
                                                     	<div class="row">
                                                     	    <div class="form-group col-md-12">
                                                			    <label for="sale_price">Sale Price*</label>
                                                                <input type="number" class="form-control {{$errors->has('sale_price')?'error':''}}" value="{{$sale->total_price}}" name="sale_price" placeholder="Sale Amount" required="">
                                                				@if ($errors->has('sale_price'))
                                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sale_price') }}</p>
                                                				@endif
                                                         	</div>
                                                     	    <div class="form-group col-md-6">
                                                			    <label for="paid_amount">Paid Amount*</label>
                                                                <input type="number" class="form-control {{$errors->has('paid_amount')?'error':''}}" value="{{$sale->pay_amount}}" name="paid_amount" placeholder="Paid Amount" required="">
                                                				@if ($errors->has('paid_amount'))
                                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('paid_amount') }}</p>
                                                				@endif
                                                         	</div>
                                                     	</div>
                                                     	
                                                     	<div class="row">
                                                     	    <div class="form-group col-md-6">
                                                			    <label for="emi_amount">EMI Amount</label>
                                                                <input type="number" class="form-control {{$errors->has('emi_amount')?'error':''}}" value="{{$sale->emi_amount}}" name="emi_amount" placeholder="Sale Amount">
                                                				@if ($errors->has('emi_amount'))
                                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('emi_amount') }}</p>
                                                				@endif
                                                         	</div>
                                                     	    <div class="form-group col-md-6">
                                                			    <label for="paid_amount">EMI Duration(Month)</label>
                                                                <input type="number" class="form-control {{$errors->has('emi_duration')?'error':''}}" maxlength="2" max="99" value="{{$sale->emi_time}}" name="emi_duration" placeholder="Paid Amount">
                                                				@if ($errors->has('emi_duration'))
                                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('emi_duration') }}</p>
                                                				@endif
                                                         	</div>
                                                     	</div>
                                                     	--}}
                                                     	
                                                     	<div class="emiTable emiTable_{{$sale->id}} table-responsive">
                                                     	    @include(adminTheme().'companies.includes.emiList')
                                                     	</div>
                                            	   </div>
                                            	   <div class="modal-footer">
                                            		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                            		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Submit </button>
                                            	   </div>
                                        	   </form>
                                        	 </div>
                                           </div>
                                        </div>
                                        
                                        @endisset
                                        
                                        </td>
                                        <td>{{$sale->created_at->format('d M, Y')}}
                                            <a href="{{route('admin.salesAction',['view',$sale->id])}}" target="_blank" class="badge btn-info" style="padding: 5px 10px;">Invoice</a>
                                        </td>
                                        <td>{{$sale->total_qty}}</td>
                                        <td>
                                            @foreach($sale->items as $item)
                                            {{$item->description}} - {{$item->quantity}}X {{$item->price}} <br>
                                            @endforeach
                                        </td>
                                        <td>{{$sale->currency}} {{number_format($sale->total_price)}}</td>
                                        <td>{{$sale->currency}} {{number_format($sale->paid_amount)}}</td>
                                        <td>{{$sale->currency}} {{number_format($sale->emi_amount)}}</td>
                                        <td>
                                            {{$sale->currency}} {{number_format($sale->transectionsSuccess()->where('type',0)->where('billing_reason','Installment Pay')->sum('amount'))}}
                                        </td>
                                        <td>
                                            @if($pay =$sale->transectionsSuccess()->latest()->where('type',0)->first())
                                             {{$pay->created_at->format('d M, Y')}}
                                            @endif
                                        </td>
                                        <td>
                                           {{$sale->currency}} {{number_format($sale->due_amount)}} 
                                        </td>
                                        <td>
                                            @isset(json_decode(Auth::user()->permission->permission, true)['company']['sales'])
                                            <a href="{{route('admin.companiesAction',['sale-delete',$company->id,'sale_id'=>$sale->id])}}" onclick="return confirm('Are You Want To Delete?')" class="btn-custom danger"><i class="bx bx-trash"></i></a>
                                            @endisset
                                        </td>
                                    </tr>
                                    @foreach($sale->transectionsAll()
                                    ->where('billing_reason','not like','%Installment%')
                                    ->whereIn('status',['pending','success'])->get() as $ins=>$installment)
                                    <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{$installment->billing_reason}}</td>
                                            <td>
                                                {{$installment->currency}} {{number_format($installment->amount)}}
                                            </td>
                                            <td>
                                                {{$installment->created_at->format('d M, Y')}}
                                            </td>
                                            <td>
                                                @if($installment->status=='success')
                                                <span  style="padding: 2px 15px;color: #17a2b8;font-weight: bold;">Received</span>
                                                @else
                                                @isset(json_decode(Auth::user()->permission->permission, true)['company']['duecollect'])
                                                <a href="#" data-toggle="modal" data-target="#Bill_{{$installment->id}}" class="btn btn-danger" style="padding: 2px 15px;">Receive Bill</a>
                            
                                                <!-- Add Modal -->
                                                <div class="modal fade text-left" id="Bill_{{$installment->id}}" tabindex="-1" role="dialog">
                                                   <div class="modal-dialog" role="document">
                                                	 <div class="modal-content">
                                                	    <form action="{{route('admin.companiesAction',['sale-emi-collect',$company->id,'sale_id'=>$sale->id,'emi_id'=>$installment->id])}}" method="post" enctype="multipart/form-data">
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
                                                                            <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{$installment->created_at->format('Y-m-d')}}" name="created_at"  required="">
                                                            				@if ($errors->has('created_at'))
                                                            				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
                                                            				@endif
                                                                     	</div>
                                                                    </div>
                                                             	    <div class="col-md-6">
                                                            	   		<div class="form-group">
                                                            			    <label for="amount">Amount* </label>
                                                                            <input type="number" class="form-control {{$errors->has('amount')?'error':''}}" readonly="" name="amount" value="{{$installment->amount}}" placeholder="Enter amount" required="">
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
                                                    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Receive Bill</button>
                                                    	   </div>
                                                	    </form>
                                                	 </div>
                                                   </div>
                                                </div>
                                                
                                                
                                                @endisset
                                                
                                                @endif
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @foreach($sale->transectionsAll()
                                    ->where('billing_reason','like','%Installment%')
                                    ->whereIn('status',['pending','success'])->get() as $ins=>$installment)
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{$ins+1}} Installment </td>
                                            <td>
                                                {{$installment->currency}} {{number_format($installment->amount)}}
                                            </td>
                                            <td>
                                                {{$installment->created_at->format('d M, Y')}}
                                            </td>
                                            <td>
                                                @if($installment->status=='success')
                                                <span style="padding: 2px 15px;color: #17a2b8;font-weight: bold;">Received</span>
                                                @else
                                                @isset(json_decode(Auth::user()->permission->permission, true)['company']['duecollect'])
                                                <a href="" data-toggle="modal" data-target="#Bill_{{$installment->id}}" class="btn btn-danger" style="padding: 2px 15px;">Receive Bill</a>
                            
                                                <!-- Add Modal -->
                                                <div class="modal fade text-left" id="Bill_{{$installment->id}}" tabindex="-1" role="dialog">
                                                   <div class="modal-dialog" role="document">
                                                	 <div class="modal-content">
                                                	    <form action="{{route('admin.companiesAction',['sale-emi-collect',$company->id,'sale_id'=>$sale->id,'emi_id'=>$installment->id])}}" method="post" enctype="multipart/form-data">
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
                                                                            <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{$installment->created_at->format('Y-m-d')}}" name="created_at"  required="">
                                                            				@if ($errors->has('created_at'))
                                                            				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
                                                            				@endif
                                                                     	</div>
                                                                    </div>
                                                             	    <div class="col-md-6">
                                                            	   		<div class="form-group">
                                                            			    <label for="amount">Amount* </label>
                                                                            <input type="number" class="form-control {{$errors->has('amount')?'error':''}}" readonly="" name="amount" value="{{$installment->amount}}" placeholder="Enter amount" required="">
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
                                                    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Receive Bill</button>
                                                    	   </div>
                                                	    </form>
                                                	 </div>
                                                   </div>
                                                </div>
                                                
                                                @endisset
                                                
                                                @endif
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    @endforeach
                                    @if($sales->count()==0)
                                    <tr>
                                        <td colspan="11" style="text-align:center;">No Sales</td>
                                    </tr>
                                    
                                    @endif
                                    
                                </table>
                            </div>
                            
                            @elseif($action=='quotation')
                            
                            <div class="table-responsive">
                                <table class="table table-bordered profileTable">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 100px;">Inv No</th>
                                            <th style="min-width: 150px;">Items</th>
                                            <th style="min-width: 130px;">Total</th>
                                            <th style="min-width: 100px;">Date</th>
                                            <th style="min-width: 100px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quotations as $i=>$invoice)
                                        <tr>
                                            <td><a href="{{route('admin.quotationsAction',['view',$invoice->id])}}" target="_blank">{{$invoice->invoice}}</a>
                                            </td>
                                            <td>{{$invoice->items()->count()}} Items</td>
                                            <td>{{$invoice->currency}} {{$invoice->grand_total}}</td>
                                            <td>{{$invoice->created_at->format('d.m.Y')}}</td>
                                            <td>
                                                {{ucfirst($invoice->order_status)}}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    
                                </table>
                            </div>
                            
                            @elseif($action=='lcinvoice')

                            
                            <div class="table-responsive">
                                <table class="table table-bordered profileTable">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 100px;">LC Number</th>
                                            <th style="min-width: 150px;">LC Opening Date</th>
                                            <th style="min-width: 130px;">LC Value - USD</th>
                                            <th style="min-width: 100px;">Exchange Rate Per $</th>
                                            <th style="min-width: 100px;">Shipment Date</th>
                                            <th style="min-width: 100px;">Estimated Arrival Date (ETA)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lcInvoices as $i=>$invoice)
                                        <tr>
                                            <td><a href="{{route('admin.lcInvoicesAction',['view',$invoice->id])}}" target="_blank">{{$invoice->invoice}}</a></td>
                                            <td>{{$invoice->created_at->format('d.m.Y')}}</td>
                                            <td>${{priceFormat($invoice->grand_total)}}</td>
                                            <td>${{priceFormat($invoice->lc_value_rate)}}</td>
                                            <td>{{$invoice->pending_at?Carbon\Carbon::parse($invoice->pending_at)->format('d.m.Y'):''}}</td>
                                            <td>{{$invoice->maturity_at?Carbon\Carbon::parse($invoice->maturity_at)->format('d.m.Y'):''}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    
                                </table>
                            </div>
                            
                            @elseif($action=='meeting')
                            
                            @isset(json_decode(Auth::user()->permission->permission, true)['meetings']['add'])
                            <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddMeeting" style="padding:5px 15px;">
                                 <i class="bx bx-plus"></i> Meeting
                            </a>
                            <!-- Add Modal -->
                             <div class="modal fade text-left" id="AddMeeting" tabindex="-1" role="dialog">
                               <div class="modal-dialog" role="document">
                            	 <div class="modal-content">
                            	 <form action="{{route('admin.companiesAction',['add-meeting',$company->id])}}" method="post">
                            	   	  @csrf
                                	   <div class="modal-header">
                                		 <h4 class="modal-title">Add Meeting</h4>
                                		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                		   <span aria-hidden="true">&times; </span>
                                		 </button>
                                	   </div>
                                	   <div class="modal-body">
                                	   		
                                	   		<div class="form-group">
                                			    <label for="name">Title/Subject* </label>
                                                <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Title/Subject" required="">
                                				@if ($errors->has('name'))
                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                				@endif
                                         	</div>
                                         	<div class="row">
                                    	   		<div class="col-md-12 form-group">
                                    			    <label for="date_time">Date & Time* </label>
                                                    <input type="datetime-local" class="form-control {{$errors->has('date_time')?'error':''}}" name="date_time" required="">
                                    				@if ($errors->has('date_time'))
                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('date_time') }}</p>
                                    				@endif
                                             	</div>
                                         	</div>
                                         	<div class="form-group">
                                			    <label for="host">Host Person*</label>
                                                <select class="select22" data-placeholder="Select Host" name="host">
                                                    @if(empty(json_decode(Auth::user()->permission->permission, true)['employees']['list']))
                                                        <option value="{{Auth::id()}}" selected="">{{Auth::user()->name}}</option>
                                                    @else
                                                        @foreach($users as $user)
                                                        <option value="{{$user->id}}" {{Auth::id()==$user->id?'selected':''}}>{{$user->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                				@if ($errors->has('host'))
                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('host') }}</p>
                                				@endif
                                         	</div>
                                	   		
                                         	<div class="row">
                                    	   		<div class="col-md-6 form-group">
                                    			    <label for="meeting_type">Meeting Type* </label>
                                                    <select class="form-control" name="meeting_type">
                                                        <option value="">Select Type</option>
                                                        <option value="In-person">In-person</option>
                                                        <option value="Zoom">Zoom</option>
                                                        <option value="Google Meet">Google Meet</option>
                                                        <option value="Phone">Phone</option>
                                                    </select>
                                    				@if ($errors->has('meeting_type'))
                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('meeting_type') }}</p>
                                    				@endif
                                             	</div>
                                    	   		<div class="col-md-6 form-group">
                                    			    <label for="location">Location* </label>
                                                    <input type="text" class="form-control {{$errors->has('location')?'error':''}}" name="location" placeholder="(physical or virtual link)" required="">
                                    				@if ($errors->has('location'))
                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('location') }}</p>
                                    				@endif
                                             	</div>
                                         	</div>
                                			 <div class="form-group">
                                				<label for="name">Description</label>
                            					<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description"></textarea>
                            					@if ($errors->has('description'))
                            					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
                            					@endif
                                         	</div>
                                	   </div>
                                	   <div class="modal-footer">
                                		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Meeting</button>
                                	   </div>
                            	   </form>
                            	 </div>
                               </div>
                             </div>
                            <hr>
                            @endisset
                            
                            <div class="table-responsive">
                                <table class="table mb-20">
                                    <thead>
                                        <tr class="table__title">
                                            <th>Meeting Title</th>
                                            <th>Meeting Date & Time</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Host</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table__body">
                                        @foreach($meetings as $meeting)
                                        <tr>
                                            <td>{{$meeting->name}}</td>
                                            <td>{{$meeting->created_at->format('F d, Y h:i A')}}</td>
                                            <td>{{ucfirst($meeting->meeting_type)}}</td>
                                            <td>
                                                @if($meeting->status=='In progress')
                                                <span class="badge" style="background: #ff108c;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                                @elseif($meeting->status=='Completed')
                                                <span class="badge" style="background: #13c238;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                                @elseif($meeting->status=='Canceled')
                                                <span class="badge" style="background: #ff2e37;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                                @elseif($meeting->status=='Rescheduled')
                                                <span class="badge" style="background: #f326eb;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                                @else
                                                <span class="badge" style="background: #2c66cb;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{$meeting->hostUser?$meeting->hostUser->name:'Not Found'}}
                                            </td>
                                            <td class="center">
                                                @isset(json_decode(Auth::user()->permission->permission, true)['meetings']['add'])
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditMeeting_{{$meeting->id}}" class="btn-custom success">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                
                                                <!-- Edit Modal -->
                                                <div class="modal fade text-left" id="EditMeeting_{{$meeting->id}}" tabindex="-1" role="dialog">
                                                   <div class="modal-dialog" role="document">
                                                	 <div class="modal-content">
                                                	 <form action="{{route('admin.companiesAction',['update-meeting',$company->id,'meeting_id'=>$meeting->id])}}" method="post" enctype="multipart/form-data">
                                                	   	  @csrf
                                                    	   <div class="modal-header">
                                                    		 <h4 class="modal-title">Edit Visit</h4>
                                                    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    		   <span aria-hidden="true">&times; </span>
                                                    		 </button>
                                                    	   </div>
                                                    	   <div class="modal-body">
                                                    	        <div class="form-group">
                                                    			    <label for="name">Title/Subject* </label>
                                                                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" value="{{$meeting->name}}" placeholder="Enter Title/Subject" required="">
                                                    				@if ($errors->has('name'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                                    				@endif
                                                             	</div>
                                                             	<div class="row">
                                                        	   		<div class="col-md-6 form-group">
                                                        			    <label for="date_time">Date & Time* </label>
                                                                        <input type="datetime-local" class="form-control {{$errors->has('date_time')?'error':''}}" name="date_time" value="{{ old('visit_date',$meeting->created_at->format('Y-m-d\TH:i')) }}" required="">
                                                        				@if ($errors->has('date_time'))
                                                        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('date_time') }}</p>
                                                        				@endif
                                                                 	</div>
                                                                 	<div class="col-md-6 form-group">
                                                        			    <label for="status">Status* </label>
                                                                        <select class="form-control" name="status">
                                                                            <option value="">Select Type</option>
                                                                            <option value="Scheduled" {{ old('status', $meeting->status ?? '') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                                            <option value="In progress" {{ old('status', $meeting->status ?? '') == 'In progress' ? 'selected' : '' }}>In progress</option>
                                                                            <option value="Completed" {{ old('status', $meeting->status ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                                            <option value="Canceled" {{ old('status', $meeting->status ?? '') == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                                                                            <option value="Rescheduled" {{ old('status', $meeting->status ?? '') == 'Rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                                                        </select>
                                                        				@if ($errors->has('status'))
                                                        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
                                                        				@endif
                                                                 	</div>
                                                             	</div>
                                                             	<div class="form-group">
                                                    			    <label for="host">Host Person*</label>
                                                                    <select class="select22" data-placeholder="Select Host" name="host">
                                                                        @if(empty(json_decode(Auth::user()->permission->permission, true)['employees']['list']))
                                                                            <option value="{{Auth::id()}}" selected="">{{Auth::user()->name}}</option>
                                                                        @else
                                                                            @foreach($users as $user)
                                                                            <option value="{{$user->id}}" {{ old('assignee', $meeting->host_id ?? '') == $user->id ? 'selected' : '' }} >{{$user->name}}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                    				@if ($errors->has('host'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('host') }}</p>
                                                    				@endif
                                                             	</div>
                                                    	   		
                                                             	<div class="row">
                                                        	   		<div class="col-md-6 form-group">
                                                        			    <label for="meeting_type">Meeting Type* </label>
                                                                        <select class="form-control" name="meeting_type">
                                                                            <option value="">Select Type</option>
                                                                            <option value="In-person" {{$meeting->meeting_type=='In-person'?'selected':''}} >In-person</option>
                                                                            <option value="Zoom" {{$meeting->meeting_type=='Zoom'?'selected':''}} >Zoom</option>
                                                                            <option value="Google Meet" {{$meeting->meeting_type=='Google Meet'?'selected':''}} >Google Meet</option>
                                                                            <option value="Phone" {{$meeting->meeting_type=='Phone'?'selected':''}} >Phone</option>
                                                                        </select>
                                                        				@if ($errors->has('meeting_type'))
                                                        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('meeting_type') }}</p>
                                                        				@endif
                                                                 	</div>
                                                        	   		<div class="col-md-6 form-group">
                                                        			    <label for="location">Location* </label>
                                                                        <input type="text" class="form-control {{$errors->has('location')?'error':''}}" name="location" value="{{$meeting->location}}" placeholder="(physical or virtual link)" required="">
                                                        				@if ($errors->has('location'))
                                                        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('location') }}</p>
                                                        				@endif
                                                                 	</div>
                                                             	</div>
                                                    			 <div class="form-group">
                                                    				<label for="name">Description</label>
                                                					<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{{$meeting->description}}</textarea>
                                                					@if ($errors->has('description'))
                                                					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
                                                					@endif
                                                             	</div>
                                                    	   </div>
                                                    	   <div class="modal-footer">
                                                    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                                    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Update Meeting</button>
                                                    	   </div>
                                                	   </form>
                                                	 </div>
                                                   </div>
                                                 </div>
                                                @endisset
                                                
                                                @isset(json_decode(Auth::user()->permission->permission, true)['meetings']['delete'])
                                                <a href="{{route('admin.companiesAction',['delete-meeting',$company->id,'meeting_id'=>$meeting->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                                                @endisset
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @elseif($action=='visit')
                            
                            @isset(json_decode(Auth::user()->permission->permission, true)['visits']['add'])
                            <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddVisit" style="padding:5px 15px;">
                                 <i class="bx bx-plus"></i> Visit
                            </a>
                            
                            <div class="modal fade text-left" id="AddVisit" tabindex="-1" role="dialog">
                               <div class="modal-dialog" role="document">
                            	 <div class="modal-content">
                            	 <form action="{{route('admin.companiesAction',['add-visit',$company->id])}}" method="post" enctype="multipart/form-data">
                            	   	  @csrf
                                	   <div class="modal-header">
                                		 <h4 class="modal-title">Add Visit</h4>
                                		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                		   <span aria-hidden="true">&times; </span>
                                		 </button>
                                	   </div>
                                	   <div class="modal-body">
                                	        <div class="row">
                                	            <div class="col-md-6 form-group">
                                                  <label>Visit Date*</label>
                                                  <input type="datetime-local" class="form-control {{$errors->has('visit_date')?'error':''}}" name="visit_date" required="">
                                                  @if ($errors->has('visit_date'))
                                        			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('visit_date') }}</p>
                                        		  @endif
                                                </div>
                                	            <div class="col-md-6 form-group">
                                                    <label>Location*</label>
                                                    <input type="text" class="form-control {{$errors->has('location')?'error':''}}" name="location" value="{{old('location')?:$company->fullAddress()}}" placeholder="(In office or Factory visit)" required="">
                                                    @if ($errors->has('location'))
                                        			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('location') }}</p>
                                        		  @endif
                                                </div>
                                                <div class="col-md-12 form-group">
                                    			    <label for="host">Assignee*</label>
                                                    <select class="select2" data-placeholder="Select Assignee" name="assignee" required="">
                                                        <option value="">Select User</option>
                                                        @if(empty(json_decode(Auth::user()->permission->permission, true)['employees']['list']))
                                                        <option value="{{Auth::id()}}" selected="">{{Auth::user()->name}}</option>
                                                        @else
                                                        @foreach($users as $user)
                                                        <option value="{{$user->id}}" >{{$user->name}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                    				@if ($errors->has('assignee'))
                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('assignee') }}</p>
                                    				@endif
                                             	</div>
                                         	</div>
                                         	<div class="form-group">
                                			<label for="name">Description</label>
                                			<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description"></textarea>
                                			@if ($errors->has('description'))
                                			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
                                			@endif
                                     	</div>
                                         	<div class="row">
                                                <div class="col-md-6 form-group">
                                    			    <label for="status">Status* </label>
                                                    <select class="form-control" name="status" required="">
                                                        <option value="">Select Type</option>
                                                        <option value="Not Potential">Not Potential</option>
                                                        <option value="Potential">Potential</option>
                                                        <option value="Very Potential">Very Potential</option>
                                                    </select>
                                    				@if ($errors->has('status'))
                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
                                    				@endif
                                             	</div>
                                             	<div class="col-md-6 form-group">
                                                    <label for="name">Attachment(Image)</label>
                                        	        <input type="file" name="attachment" class="form-control" accept="image/*" style="padding: 3px;">
                                                    @if ($errors->has('attachment'))
                                        			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
                                        		  @endif
                                                </div>
                                            </div>
                                	   </div>
                                	   <div class="modal-footer">
                                		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Visit</button>
                                	   </div>
                            	   </form>
                            	 </div>
                               </div>
                             </div>
                            <hr>
                            @endisset
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 100px;width: 100px;padding-right:0;">SL</th>
                                            <th style="min-width: 200px;">Visit Date</th>
                                            <th style="min-width: 100px;">Location</th>
                                            <th style="min-width: 100px;">Description</th>
                                            <th style="min-width: 100px;">Visit By</th>
                                            <th style="min-width: 100px;">Status</th>
                                            <th style="min-width: 100px;width:100px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visits as $i=>$visit)
                                        <tr>
                                            <td>
                                                <span style="margin:0 5px;">{{$visits->currentpage()==1?$i+1:$i+($visits->perpage()*($visits->currentpage() - 1))+1}}</span>
                                            </td>
                                            <td>
                                                {{$visit->visit_date?Carbon\Carbon::parse($visit->visit_date)->format('d-m-Y'):''}}
                                                @if($visit->imageFile)
                                                <a href="{{asset($visit->image())}}" download="" style="margin-left: 5px;color: #e1000a;"><i class="bx bx-file"></i></a>
                                                @endif
                                            </td>
                                            <td>{{$visit->location}}</td>
                                            <td>{{$visit->description}}</td>
                                            <td>{{$visit->assinee?$visit->assinee->name:''}}</td>
                                            <td>
                                                @if($visit->status=='Not Potential')
                                                <span class="badge" style="background: #9baaff;font-size: 14px;color: white;" >Not Potential</span>
                                                @elseif($visit->status=='Potential')
                                                <span class="badge" style="background: #5970f3;font-size: 14px;color: white;" >Potential</span>
                                                @elseif($visit->status=='Very Potential')
                                                <span class="badge" style="background: #0829e5;font-size: 14px;color: white;" >Very Potential</span>
                                                @endif
                                            </td>
                                            
                                            <td class="center">
                                                @isset(json_decode(Auth::user()->permission->permission, true)['visits']['add'])
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditVisit_{{$visit->id}}" class="btn-custom success">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                @endisset
                                                
                                                <!-- Edit Modal -->
                                                <div class="modal fade text-left" id="EditVisit_{{$visit->id}}" tabindex="-1" role="dialog">
                                                   <div class="modal-dialog" role="document">
                                                	 <div class="modal-content">
                                                	 <form action="{{route('admin.companiesAction',['update-visit',$company->id,'visit_id'=>$visit->id])}}" method="post" enctype="multipart/form-data">
                                                	   	  @csrf
                                                    	   <div class="modal-header">
                                                    		 <h4 class="modal-title">Edit Visit</h4>
                                                    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    		   <span aria-hidden="true">&times; </span>
                                                    		 </button>
                                                    	   </div>
                                                    	   <div class="modal-body">
                                                    	        <div class="row">
                                                    	            <div class="col-md-6 form-group">
                                                                      <label>Visit Date*</label>
                                                                      <input type="datetime-local" class="form-control {{$errors->has('visit_date')?'error':''}}" name="visit_date" value="{{ old('visit_date', Carbon\Carbon::parse($visit->visit_date)->format('Y-m-d\TH:i')) }}" required="">
                                                                      @if ($errors->has('visit_date'))
                                                            			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('visit_date') }}</p>
                                                            		  @endif
                                                                    </div>
                                                    	            <div class="col-md-6 form-group">
                                                                        <label>Location*</label>
                                                                        <input type="text" class="form-control {{$errors->has('location')?'error':''}}" name="location" value="{{old('location')?:$visit->location}}" placeholder="(In office or Factory visit)" required="">
                                                                        @if ($errors->has('location'))
                                                            			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('location') }}</p>
                                                            		  @endif
                                                                    </div>
                                                                    <div class="col-md-12 form-group">
                                                        			    <label for="host">Assignee*</label>
                                                                        <select class="select2_{{$visit->id}}" data-placeholder="Select Assignee" name="assignee" required="">
                                                                            <option value="">Select User</option>
                                                                            @foreach($users as $user)
                                                                            <option value="{{$user->id}}" {{ old('assignee', $visit->assignby_id ?? '') == $user->id ? 'selected' : '' }} >{{$user->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                        				@if ($errors->has('assignee'))
                                                        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('assignee') }}</p>
                                                        				@endif
                                                                 	</div>
                                                             	</div>
                                                             	<div class="form-group">
                                                        			<label for="name">Description</label>
                                                        			<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{{old('description', $visit->description ?? '')}}</textarea>
                                                        			@if ($errors->has('description'))
                                                        			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
                                                        			@endif
                                                             	</div>
                                                             	<div class="row">
                                                                    <div class="col-md-6 form-group">
                                                        			    <label for="status">Status* </label>
                                                                        <select class="form-control" name="status" required="">
                                                                            <option value="">Select Type</option>
                                                                            <option value="Not Potential" {{$visit->status=='Not Potential'?'selected':''}} >Not Potential</option>
                                                                            <option value="Potential" {{$visit->status=='Potential'?'selected':''}} >Potential</option>
                                                                            <option value="Very Potential" {{$visit->status=='Very Potential'?'selected':''}} >Very Potential</option>
                                                                        </select>
                                                        				@if ($errors->has('status'))
                                                        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
                                                        				@endif
                                                                 	</div>
                                                                 	<div class="col-md-6 form-group">
                                                                        <label for="name">Attachment(Image)</label>
                                                            	        <input type="file" name="attachment" class="form-control" accept="image/*" style="padding: 3px;">
                                                                        @if ($errors->has('attachment'))
                                                            			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
                                                            		  @endif
                                                                    </div>
                                                                </div>
                                                    	   </div>
                                                    	   <div class="modal-footer">
                                                    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                                    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Update Visit</button>
                                                    	   </div>
                                                	   </form>
                                                	 </div>
                                                   </div>
                                                 </div>
                                                
                                                @isset(json_decode(Auth::user()->permission->permission, true)['visits']['delete'])
                                                <a href="{{route('admin.companiesAction',['delete-visit',$company->id,'visit_id'=>$visit->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                                                @endisset
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{$visits->links('pagination')}}
                            </div>
                            
                            @elseif($action=='note')
                            
                            <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddNote" style="padding:5px 15px;">
                                 <i class="bx bx-plus"></i> Note
                            </a>
    
                            <div class="modal fade text-left" id="AddNote" tabindex="-1" role="dialog">
                               <div class="modal-dialog" role="document">
                            	 <div class="modal-content">
                            	 <form action="{{route('admin.companiesAction',['add-note',$company->id])}}" method="post" enctype="multipart/form-data">
                            	   	  @csrf
                                	   <div class="modal-header">
                                		 <h4 class="modal-title">Add Note</h4>
                                		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                		   <span aria-hidden="true">&times; </span>
                                		 </button>
                                	   </div>
                                	   <div class="modal-body">
                                         	<div class="form-group">
                                    			<textarea name="note" class="form-control {{$errors->has('note')?'error':''}}" placeholder="Write note"></textarea>
                                    			@if ($errors->has('note'))
                                    			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('note') }}</p>
                                    			@endif
                                         	</div>
                                	   </div>
                                	   <div class="modal-footer">
                                		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Note</button>
                                	   </div>
                            	   </form>
                            	 </div>
                               </div>
                             </div>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-bordered leadInfoTable">
                                    <tr>
                                        <th style="min-width: 60px;width:60px;">SL</th>
                                        <th style="min-width: 220px;">Note</th>
                                        <th style="min-width:150px;width:150px;">Date</th>
                                        <th style="min-width:100px;width:100px;">Action</th>
                                    </tr>
                                    @foreach($notes as $i=>$note)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td>
                                            {!!nl2br(e($note->description))!!}
                                        </td>
                                        <td>
                                            {{$note->created_at->format('d-m-Y')}}
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn-custom success" data-toggle="modal" data-target="#EditNote_{{$note->id}}"><i class="bx bx-edit" ></i></a>
                                            
                                            <!-- Edit Modal -->
                                            <div class="modal fade text-left" id="EditNote_{{$note->id}}" tabindex="-1" role="dialog">
                                               <div class="modal-dialog" role="document">
                                            	 <div class="modal-content">
                                            	 <form action="{{route('admin.companiesAction',['update-note',$company->id,'note_id'=>$note->id])}}" method="post" enctype="multipart/form-data">
                                            	   	  @csrf
                                                	   <div class="modal-header">
                                                		 <h4 class="modal-title">Edit Note</h4>
                                                		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                		   <span aria-hidden="true">&times; </span>
                                                		 </button>
                                                	   </div>
                                                	   <div class="modal-body">
                                                         	<div class="form-group">
                                                    			<textarea name="note" class="form-control {{$errors->has('note')?'error':''}}" placeholder="Write note">{{$note->description}}</textarea>
                                                    			@if ($errors->has('note'))
                                                    			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('note') }}</p>
                                                    			@endif
                                                         	</div>
                                                	   </div>
                                                	   <div class="modal-footer">
                                                		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                                		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Update Note</button>
                                                	   </div>
                                            	   </form>
                                            	 </div>
                                               </div>
                                             </div>
                                            
                                            <a href="{{route('admin.companiesAction',['delete-note',$company->id,'note_id'=>$note->id])}}" class="btn-custom danger" onclick="return confirm('Are you want to delete?')"><i class="bx bx-x"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                            @elseif($action=='commitment')
                            
                            <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddCommitment" style="padding:5px 15px;">
                                 <i class="bx bx-plus"></i> Commitment
                            </a>
    
                            <div class="modal fade text-left" id="AddCommitment" tabindex="-1" role="dialog">
                               <div class="modal-dialog" role="document">
                            	 <div class="modal-content">
                            	 <form action="{{route('admin.companiesAction',['add-commitment',$company->id])}}" method="post" enctype="multipart/form-data">
                            	   	  @csrf
                                	   <div class="modal-header">
                                		 <h4 class="modal-title">Add Commitment</h4>
                                		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                		   <span aria-hidden="true">&times; </span>
                                		 </button>
                                	   </div>
                                	   <div class="modal-body">
                                	        <div class="form-group">
                                			    <label for="date_time">Date & Time* </label>
                                                <input type="datetime-local" class="form-control {{$errors->has('date_time')?'error':''}}" name="date_time" required="">
                                				@if ($errors->has('date_time'))
                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('date_time') }}</p>
                                				@endif
                                         	</div>
                                         	<div class="row">
                                    	   		<div class="col-md-6 form-group">
                                    			    <label for="commitment_type">Commitment Time* </label>
                                                    <select class="form-control" name="commitment_type">
                                                        <option value="">Select Commitment</option>
                                                        <option value="1st Commitment">1st Commitment</option>
                                                        <option value="2nd Commitment">2nd Commitment</option>
                                                        <option value="3rd Commitment">3rd Commitment</option>
                                                        <option value="4th Commitment">4th Commitment</option>
                                                        <option value="5th Commitment">5th Commitment</option>
                                                        <option value="6th Commitment">6th Commitment</option>
                                                        <option value="7th Commitment">7th Commitment</option>
                                                        <option value="8th Commitment">8th Commitment</option>
                                                        <option value="9th Commitment">9th Commitment</option>
                                                        <option value="10th Commitment">10th Commitment</option>
                                                    </select>
                                    				@if ($errors->has('commitment_type'))
                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('commitment_type') }}</p>
                                    				@endif
                                             	</div>
                                    	   		<div class="col-md-6 form-group">
                                    			    <label for="payment_type">Payment Type* </label>
                                                    <select class="form-control" name="payment_type">
                                                        <option value="">Select Type</option>
                                                        <option value="Cash installment Cheque">Cash installment Cheque</option>
                                                        <option value="Cash from customer location">Cash from customer location</option>
                                                        <option value="Cash at Monpura office">Cash at Monpura office</option>
                                                        <option value="Deposit/Transfer to bank A/C">Deposit/Transfer to bank A/C</option>
                                                        <option value="Sent to Bkash A/C">Sent to Bkash A/C</option>
                                                    </select>
                                    				@if ($errors->has('payment_type'))
                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('payment_type') }}</p>
                                    				@endif
                                             	</div>
                                             	<div class="col-md-6 form-group">
                                    			    <label for="amount">Payment Amount* </label>
                                    			    <input type="number" class="form-control" name="amount" placeholder="Amount" step="any" required="">
                                    			</div>
                                    			<div class="col-md-6 form-group">
                                    			    <label for="assignee">Assignee*</label>
                                                    <select class="select23" data-placeholder="Select Assignee" name="assignee" required="">
                                                        <option value="">Select User</option>
                                                        @if(empty(json_decode(Auth::user()->permission->permission, true)['employees']['list']))
                                                        <option value="{{Auth::id()}}" selected="">{{Auth::user()->name}}</option>
                                                        @else
                                                        @foreach($users as $user)
                                                        <option value="{{$user->id}}" >{{$user->name}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                    				@if ($errors->has('assignee'))
                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('assignee') }}</p>
                                    				@endif
                                             	</div>
                                            </div>

                                         	<div class="form-group">
                                         	    <label>Remarks</label>
                                    			<textarea name="note" class="form-control {{$errors->has('note')?'error':''}}" placeholder="Write note"></textarea>
                                    			@if ($errors->has('note'))
                                    			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('note') }}</p>
                                    			@endif
                                         	</div>
                                	   </div>
                                	   <div class="modal-footer">
                                		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Commitment</button>
                                	   </div>
                            	   </form>
                            	 </div>
                               </div>
                             </div>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-bordered leadInfoTable">
                                    <tr>
                                        <th style="min-width: 60px;width:60px;">SL</th>
                                        <th style="min-width:175px;width:175px;">Created Date</th>
                                        <th style="min-width:175px;width:175px;">Date & time</th>
                                        <th style="min-width:200px;width:200px;">Commitment Time</th>
                                        <th style="min-width:250px;width:250px;">Payment Type</th>
                                        <th style="min-width:150px;width:150px;">Amount</th>
                                        <th style="min-width: 100px;">Note</th>
                                        <th style="min-width:150px;width:150px;">Assignee</th>
                                        <th style="min-width:150px;width:150px;">Status</th>
                                        <th style="min-width:100px;width:100px;">Action</th>
                                    </tr>
                                    @foreach($commitments as $i=>$commitment)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td>{{$commitment->created_at->format('d-m-Y h:i A')}}</td>
                                        <td>
                                            {{$commitment->date_time?Carbon\Carbon::parse($commitment->date_time)->format('d-m-Y h:i A'):''}}
                                        </td>
                                        <td>{{$commitment->commitment_type}}</td>
                                        <td>{{$commitment->payment_type}}</td>
                                        <td>{{priceFullFormat($commitment->amount)}}</td>
                                        <td>{{$commitment->note}}</td>
                                        <td>{{$commitment->assinee?->name}}</td>
                                        <td>{{$commitment->status}}</td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn-custom success" data-toggle="modal" data-target="#EditCommitment_{{$commitment->id}}"><i class="bx bx-edit" ></i></a>
                                            
                                            <!-- Edit Modal -->
                                            <div class="modal fade text-left" id="EditCommitment_{{$commitment->id}}" tabindex="-1" role="dialog">
                                               <div class="modal-dialog" role="document">
                                            	 <div class="modal-content">
                                            	 <form action="{{route('admin.companiesAction',['update-commitment',$company->id,'commitment_id'=>$commitment->id])}}" method="post" enctype="multipart/form-data">
                                            	   	  @csrf
                                                	   <div class="modal-header">
                                                		 <h4 class="modal-title">Edit commitment</h4>
                                                		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                		   <span aria-hidden="true">&times; </span>
                                                		 </button>
                                                	   </div>
                                                	   <div class="modal-body">
                                                         	<div class="form-group">
                                                			    <label for="date_time">Date & Time* </label>
                                                                <input type="datetime-local" class="form-control {{$errors->has('date_time')?'error':''}}" value="{{$commitment->date_time?Carbon\Carbon::parse($commitment->date_time)->format('Y-m-d\TH:i'):''}}" name="date_time" required="">
                                                				@if ($errors->has('date_time'))
                                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('date_time') }}</p>
                                                				@endif
                                                         	</div>
                                                         	<div class="row">
                                                    	   		<div class="col-md-6 form-group">
                                                    			    <label for="commitment_type">Commitment Time* </label>
                                                                    <select class="form-control" name="commitment_type">
                                                                        <option value="">Select Commitment</option>
                                                                        <option value="1st Commitment" {{$commitment->commitment_type=='1st Commitment'?'selected':''}} >1st Commitment</option>
                                                                        <option value="2nd Commitment" {{$commitment->commitment_type=='2nd Commitment'?'selected':''}} >2nd Commitment</option>
                                                                        <option value="3rd Commitment" {{$commitment->commitment_type=='3rd Commitment'?'selected':''}} >3rd Commitment</option>
                                                                        <option value="4th Commitment" {{$commitment->commitment_type=='4th Commitment'?'selected':''}} >4th Commitment</option>
                                                                        <option value="5th Commitment" {{$commitment->commitment_type=='5th Commitment'?'selected':''}} >5th Commitment</option>
                                                                        <option value="6th Commitment" {{$commitment->commitment_type=='6th Commitment'?'selected':''}} >6th Commitment</option>
                                                                        <option value="7th Commitment" {{$commitment->commitment_type=='7th Commitment'?'selected':''}} >7th Commitment</option>
                                                                        <option value="8th Commitment" {{$commitment->commitment_type=='8th Commitment'?'selected':''}} >8th Commitment</option>
                                                                        <option value="9th Commitment" {{$commitment->commitment_type=='9th Commitment'?'selected':''}} >9th Commitment</option>
                                                                        <option value="10th Commitment" {{$commitment->commitment_type=='10th Commitment'?'selected':''}} >10th Commitment</option>
                                                                    </select>
                                                    				@if ($errors->has('commitment_type'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('commitment_type') }}</p>
                                                    				@endif
                                                             	</div>
                                                    	   		<div class="col-md-6 form-group">
                                                    			    <label for="payment_type">Payment Type* </label>
                                                                    <select class="form-control" name="payment_type">
                                                                        <option value="">Select Type</option>
                                                                        <option value="Cash installment Cheque" {{$commitment->payment_type=='Cash installment Cheque'?'selected':''}} >Cash installment Cheque</option>
                                                                        <option value="Cash from customer location" {{$commitment->payment_type=='Cash from customer location'?'selected':''}} >Cash from customer location</option>
                                                                        <option value="Cash at Monpura office" {{$commitment->payment_type=='Cash at Monpura office'?'selected':''}} >Cash at Monpura office</option>
                                                                        <option value="Deposit/Transfer to bank A/C" {{$commitment->payment_type=='Deposit/Transfer to bank A/C'?'selected':''}} >Deposit/Transfer to bank A/C</option>
                                                                        <option value="Sent to Bkash A/C" {{$commitment->payment_type=='Sent to Bkash A/C'?'selected':''}} >Sent to Bkash A/C</option>
                                                                    </select>
                                                    				@if ($errors->has('payment_type'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('payment_type') }}</p>
                                                    				@endif
                                                             	</div>
                                                             	<div class="col-md-6 form-group">
                                                    			    <label for="amount">Payment Amount* </label>
                                                    			    <input type="number" class="form-control" name="amount" value="{{$commitment->amount}}" step="any" placeholder="Amount" required="">
                                                    			</div>
                                                    			<div class="col-md-6 form-group">
                                                    			    <label for="assignee">Assignee*</label>
                                                                    <select class="select23_{{$commitment->id}}" data-placeholder="Select Assignee" name="assignee" required="">
                                                                        <option value="">Select User</option>
                                                                        @if(empty(json_decode(Auth::user()->permission->permission, true)['employees']['list']))
                                                                        <option value="{{Auth::id()}}" selected="">{{Auth::user()->name}}</option>
                                                                        @else
                                                                        @foreach($users as $user)
                                                                        <option value="{{$user->id}}" {{$user->id==$commitment->assignby_id?'selected':''}} >{{$user->name}}</option>
                                                                        @endforeach
                                                                        @endif
                                                                    </select>
                                                    				@if ($errors->has('assignee'))
                                                    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('assignee') }}</p>
                                                    				@endif
                                                             	</div>
                                                            </div>
                
                                                         	<div class="form-group">
                                                         	    <label>Remarks</label>
                                                    			<textarea name="note" class="form-control {{$errors->has('note')?'error':''}}" placeholder="Write note">{{$commitment->note}}</textarea>
                                                    			@if ($errors->has('note'))
                                                    			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('note') }}</p>
                                                    			@endif
                                                         	</div>
                                                         	<div class="form-group">
                                                			    <label for="status">Status* </label>
                                                                <select class="form-control" name="status" required="">
                                                                    <option value="">Select Type</option>
                                                                    <option value="Scheduled" {{$commitment->status=='Scheduled'?'selected':''}} >Scheduled</option>
                                                                    <option value="Completed" {{$commitment->status=='Completed'?'selected':''}}>Completed</option>
                                                                    <option value="Cancelled" {{$commitment->status=='Cancelled'?'selected':''}}>Cancelled</option>
                                                                </select>
                                                				@if ($errors->has('status'))
                                                				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('status') }}</p>
                                                				@endif
                                                         	</div>
                                                	   </div>
                                                	   <div class="modal-footer">
                                                		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
                                                		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Update commitment</button>
                                                	   </div>
                                            	   </form>
                                            	 </div>
                                               </div>
                                             </div>
                                            
                                            <a href="{{route('admin.companiesAction',['delete-commitment',$company->id,'commitment_id'=>$commitment->id])}}" class="btn-custom danger" onclick="return confirm('Are you want to delete?')"><i class="bx bx-x"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                            
                            @else
                            
                            
                            
                            <a href="javascript:void(0)" id="PrintAction22" class="btn-custom yellow">
                                 <i class="bx bx-printer"></i> Print
                             </a>
                            @isset(json_decode(Auth::user()->permission->permission, true)['company']['add'])
                            <a href="{{route('admin.companiesAction',['edit',$company->id])}}" class="btn-custom success ml-5">
                                 <i class="bx bx-edit"></i> Edit
                            </a>
                            @endisset
                             
                                    <div class="PrintAreaContact">
                                        <style type="text/css">
                                            .profileTable {
                                                border-collapse: collapse;
                                                width:100%;
                                            }
                                            .profileTable tr th,.profileTable tr td{
                                                padding:5px;
                                                border: 1px solid #565656;
                                            }
                                            
                                            .bStatus{
                                                width: 140px;
                                                display: inline-flex; 
                                            }
                                            
                                            .bStatus i {
                                                font-size: 24px;
                                                color: #919191;
                                                margin-right: 5px;
                                            }
                                            .checkbox-box {
                                                width: 16px;
                                                height: 16px;
                                                border: 2px solid #b8b2b2;
                                                border-radius: 4px;
                                                display: inline-block;
                                                position: relative;
                                                margin-right: 5px;
                                                margin-top: 2px;
                                        
                                            }
                                            
                                            .checkbox-box.checked::after {
                                                content: '';
                                                position: absolute;
                                                left: 4px;
                                                top: 0px;
                                                width: 4px;
                                                height: 10px;
                                                border: solid #4CAF50;
                                                border-width: 0 2px 2px 0;
                                                transform: rotate(45deg);
                                            }
                                            .singnature {
                                                display: flex;
                                                justify-content: space-between;
                                                margin-top: 50px;
                                            }
                                            
                                            .singnature span {
                                                text-align: center;
                                            }
                                        </style>
                                        <div style="text-align: center;">
                                            <img src="{{asset(general()->logo())}}"  style="max-height:60px;">
                                        </div>
                                        <p style="display: flex;justify-content: space-between;">
                                            <span><b>Register Date:</b> {{$company->created_at->format('d-m-Y')}}</span>
                                            <span><b>Created By:</b> {{$company->user?$company->user->name:''}}</span>
                                        </p>
                                        <div class="table-responsive">
                                            <table class="table table-bordered profileTable">
                                                <tr>
                                                    <th colspan="2" style="text-align: center;">Company Information</th>
                                                </tr>
                                                <tr>
                                                    <td style="min-width: 170px;width: 170px;">Deed/CT Serial</td>
                                                    <td style="min-width: 530px;">{{$company->deed_serial}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Sister Concern</td>
                                                    <td>{{$company->concern}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Company Name</td>
                                                    <td>{{$company->factory_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Owner Name</td>
                                                    <td>{{$company->owner_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Designation</td>
                                                    <td>{{$company->owner_designation}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Mobile No</td>
                                                    <td>{{$company->owner_mobile}}</td>
                                                </tr>
                                                <tr>
                                                    <td>E-mail</td>
                                                    <td>{{$company->owner_email}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Address</td>
                                                    <td>{{$company->fullAddress()}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Customer Requirement</td>
                                                    <td>{{$company->requirement}}</td>
                                                </tr>
                                                @foreach($company->persons()->where('type',2)->get() as $i=>$partner)
                                                <tr style="background: #f8f8f8;">
                                                    <td>#{{$i+1}}</td>
                                                    <td>PARTNERS</td>
                                                </tr>
                                                 <tr>
                                                    <td>Company Name</td>
                                                    <td>{{$partner->company_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Owner Name</td>
                                                    <td>{{$partner->name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Designation</td>
                                                    <td>{{$partner->designation}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Mobile No</td>
                                                    <td>{{$partner->mobile}} {{$partner->mobile2?' - '.$partner->mobile2:''}}</td>
                                                </tr>
                                                <tr>
                                                    <td>E-mail</td>
                                                    <td>{{$partner->email}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Address</td>
                                                    <td>{{$partner->fullAddress()}}</td>
                                                </tr>
                                                @endforeach
                                                
                                                <tr style="background: #f8f8f8;">
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>Key Person Name</td>
                                                    <td>{{$company->key_parson_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Designation</td>
                                                    <td>{{$company->key_parson_designation}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Mobile No</td>
                                                    <td>{{$company->key_parson_mobile}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Whatsapp Mobile</td>
                                                    <td>{{$company->key_parson_whatsapp_mobile}}</td>
                                                </tr>
                                                <tr>
                                                    <td>E-mail</td>
                                                    <td>{{$company->key_parson_email}}</td>
                                                </tr>
                                                <tr style="background: #f8f8f8;">
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <!--<tr>-->
                                                <!--    <td>Partner Name</td>-->
                                                <!--    <td>{{$company->partner_name}}</td>-->
                                                <!--</tr>-->
                                                <!--<tr>-->
                                                <!--    <td>Partner Designation</td>-->
                                                <!--    <td>{{$company->partner_designation}}</td>-->
                                                <!--</tr>-->
                                                <!--<tr>-->
                                                <!--    <td>Partner Details</td>-->
                                                <!--    <td>{{$company->partner_details}}</td>-->
                                                <!--</tr>-->
                                                <!--<tr style="background: #f8f8f8;">-->
                                                <!--    <td></td>-->
                                                <!--    <td></td>-->
                                                <!--</tr>-->
                                                <tr>
                                                    <td>PM Name</td>
                                                    <td>{{$company->pm_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>PM Designation</td>
                                                    <td>{{$company->pm_designation}}</td>
                                                </tr>
                                                <tr>
                                                    <td>PM Details</td>
                                                    <td>{{$company->pm_details}}</td>
                                                </tr>
                                                
                                                <tr style="background: #f8f8f8;">
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>Engineer Name</td>
                                                    <td>{{$company->engineer_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Engineer Designation</td>
                                                    <td>{{$company->engineer_designation}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Engineer Details</td>
                                                    <td>{{$company->engineer_details}}</td>
                                                </tr>
                                                <tr style="background: #f8f8f8;">
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <!--<tr>-->
                                                <!--    <td>Customer Status</td>-->
                                                <!--    <td>-->
                                                <!--        <span class="bStatus">-->
                                                <!--            <div class="checkbox-box {{$company->customer_status=='Not Potential'?'checked':''}}"></div> -->
                                                <!--            Not Potential-->
                                                <!--        </span>-->
                                                <!--        <span class="bStatus">-->
                                                <!--            <div class="checkbox-box {{$company->customer_status=='Potential'?'checked':''}}"></div> -->
                                                <!--            Potential-->
                                                <!--        </span>-->
                                                <!--        <span class="bStatus">-->
                                                <!--            <div class="checkbox-box {{$company->customer_status=='Very Potential'?'checked':''}}"></div> -->
                                                <!--            Very Potential-->
                                                <!--        </span>-->
                                                <!--    </td>-->
                                                <!--</tr>-->
                                                <!--<tr>-->
                                                <!--    <td>Company Category</td>-->
                                                <!--    <td>-->
                                                <!--        <span class="bStatus">-->
                                                <!--            <div class="checkbox-box {{$company->company_category=='Small'?'checked':''}}"></div>-->
                                                <!--            Small-->
                                                <!--        </span>-->
                                                <!--        <span class="bStatus">-->
                                                <!--            <div class="checkbox-box {{$company->company_category=='Medium'?'checked':''}}"></div>-->
                                                <!--            Medium-->
                                                <!--        </span>-->
                                                <!--        <span class="bStatus">-->
                                                <!--            <div class="checkbox-box {{$company->company_category=='Large'?'checked':''}}"></div> -->
                                                <!--            Large-->
                                                <!--        </span>-->
                                                <!--    </td>-->
                                                <!--</tr>-->
                                                <!--<tr>-->
                                                <!--    <td>Company Status</td>-->
                                                <!--    <td>-->
                                                <!--        <span class="bStatus">-->
                                                <!--            <div class="checkbox-box {{$company->company_status=='Risky'?'checked':''}}"></div>-->
                                                <!--            Risky-->
                                                <!--        </span>-->
                                                <!--        <span class="bStatus">-->
                                                <!--            <div class="checkbox-box {{$company->company_status=='Stable'?'checked':''}}"></div>-->
                                                <!--            Stable-->
                                                <!--        </span>-->
                                                <!--        <span class="bStatus">-->
                                                <!--            <div class="checkbox-box {{$company->company_status=='Growing'?'checked':''}}"></div>-->
                                                <!--            Growing-->
                                                <!--        </span>-->
                                                <!--        <span class="bStatus">-->
                                                <!--            <div class="checkbox-box {{$company->company_status=='Booming'?'checked':''}}"></div>-->
                                                <!--            Booming-->
                                                <!--        </span>-->
                                                <!--    </td>-->
                                                <!--</tr>-->
                                                <!--<tr>-->
                                                <!--    <td>Number Of Employee</td>-->
                                                <!--    <td>-->
                                                <!--        {{$company->number_of_employee}}-->
                                                <!--    </td>-->
                                                <!--</tr>-->
                                                <!--<tr>-->
                                                <!--    <td>Next Visit </td>-->
                                                <!--    <td>{{$company->next_visit_day}} Days / {{Carbon\Carbon::parse($company->next_visit_date)->format('d-m-Y')}}</td>-->
                                                <!--</tr>-->
                                                
                                                <tr>
                                                    <td>Remarks</td>
                                                    <td>{{$company->remarks}}</td>
                                                </tr>
                                            </table>
                                            <h4>Company Machineries:</h4>
                                            <table class="table table-bordered profileTable">
                                                <tr>
                                                    <th style="width: 40px;min-width: 40px;">SL</th>
                                                    <th style="width: 200px;min-width: 200px;">Machine Name</th>
                                                    <th style="width: 200px;min-width: 200px;">Brand</th>
                                                    <th style="width: 60px;min-width: 60px;">Qty</th>
                                                    <th style="min-width: 200px;">Note</th>
                                                </tr>
                                                
                                                @foreach($company->machinery as $i=>$machine)
                                                <tr>
                                                    <td>{{$i+1}}</td>
                                                    <td>{{$machine->name}}</td>
                                                    <td>{{$machine->brand_name}}</td>
                                                    <td>{{$machine->quantity}}</td>
                                                    <td>{{$machine->note}}</td>
                                                </tr>
                                                @endforeach
                                                
                                                @if($company->machinery->count()==0)
                                                <tr>
                                                    <td colspan="5" style="text-align:center;color: #b1afaf;">No Machine</td>
                                                </tr>
                                                @endif
                                            </table>
                                            
                                        </div>
                                        <div class="singnature">
                                            <span>
                                                -----------------------<br>
                                                Signature - 01<br>
                                                Sales & Service Engineer
                                            </span>
                                            <span>
                                                -----------------------<br>
                                                Signature - 01<br>
                                                Sales & Service Engineer
                                            </span>
                                        </div>
                                    </div>
                            
                            @endif
                            
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    
</section>
<!-- Basic Inputs end -->


@endsection 
@push('js')
<script>
    $(document).ready(function(){
        
       
        
        $('.select2').select2({
            dropdownParent: $('#AddVisit'),
            placeholder: $('.select2').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        
        @foreach($visits as $i=>$data)
        $('.select2_{{$data->id}}').select2({
            dropdownParent: $('#EditVisit_{{$data->id}}'),
            placeholder: $('.select2_{{$data->id}}').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        @endforeach
        
        @foreach($products as $data)
        $('.select29_{{$data->id}}').select2({
            dropdownParent: $('#AddSerice_{{$data->id}}'),
            placeholder: $('.select29_{{$data->id}}').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        @endforeach
        
        @foreach($services as $data)
        $('.select299_{{$data->id}}').select2({
            dropdownParent: $('#editSerice_{{$data->id}}'),
            placeholder: $('.select299_{{$data->id}}').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        @endforeach
        
        
        $('.select22').select2({
             dropdownParent: $('#AddMeeting'),
            placeholder: $('.select22').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        
         $('.select23').select2({
            dropdownParent: $('#AddCommitment'),
            placeholder: $('.select23').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        
        @foreach($commitments as $data)
        $('.select23_{{$data->id}}').select2({
            dropdownParent: $('#EditCommitment_{{$data->id}}'),
            placeholder: $('.select23_{{$data->id}}').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        @endforeach
        
        $('#PrintAction22').on("click", function () {
            $('.PrintAreaContact').printThis({
              	importCSS: false,
              	loadCSS: "https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap-grid.min.css",
            });
        });
        
        
        
        $(document).on('click','.addEMI',function(){
            var url =$(this).data('url');
            var saleId =$(this).data('sale');
            
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              success : function(data){
                if(data.view){
                $('.emiTable_'+saleId).empty().append(data.view);
                }  
                
              },error: function () {
                  alert('error');
    
                }
            });
            
        });
        
        $(document).on('click','.removeEMI',function(){
            var url =$(this).data('url');
            var saleId =$(this).data('sale');
            
            if(confirm('Are you want to EMI Remove')){
                $.ajax({
                  url:url,
                  dataType: 'json',
                  cache: false,
                  success : function(data){
                    if(data.view){
                    $('.emiTable_'+saleId).empty().append(data.view);
                    }  
                    
                  },error: function () {
                      alert('error');
        
                    }
                });
            }
            
        });
        
        $(document).on('change','.emiUpdate',function(){
            var url =$(this).data('url');
            var saleId =$(this).data('sale');
            var key =$(this).val();
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              data: {'key':key},
              success : function(data){
                if(data.view){
                $('.emiTable_'+saleId).empty().append(data.view);
                }  
                
              },error: function () {
                  alert('error');
    
                }
            });
            
        });
        
        function checkConditions() {
            var salePrice = parseFloat($('#sale_price').val()) || 0;
            var emiAmount = parseFloat($('#emi_amount').val()) || 0;
            var paidAmount = parseFloat($('#paid_amount').val()) || 0;
            
            // Check the conditions to enable or disable the button
            // if (salePrice == 0) {
            //     $('#submit_button').prop('disabled', true);
            // }else if(salePrice < (emiAmount + paidAmount)){
            //     $('#submit_button').prop('disabled', true);
            // } else {
            //     $('#submit_button').prop('disabled', false);
            // }
        }
    
        // Attach event listeners to inputs
        $('#sale_price, #emi_amount, #paid_amount').on('input', function() {
            checkConditions();
        });
    
        // Initial check when the page loads

        checkConditions();
        
        
        
        // +++ Add new item from select dropdown
          $(document).on('click', '.PlusSelectItem', function(){
            let id = $(this).data('id');
            let select = $('.SelectItem_' + id);
            let itemId = select.val();
            let itemName = select.find('option:selected').text();
            let itemPrice = parseFloat(select.find('option:selected').data('price')) || 0;
        
            if(!itemId){
              alert('Please select a product first.');
              return;
            }
        
            addNewRow(id, itemId, itemName, itemPrice);
          });
        
          // +++ Add new empty item row (PlusNewItem button)
          $(document).on('click', '.PlusNewItem', function(){
            let id = $(this).data('id');
            addNewRow(id, '', '', 0);
          });
        
          // +++ Quantity or price change
          $(document).on('input', '.qty, .price', function(){
            let tr = $(this).closest('tr');
            let id = tr.closest('tbody').attr('class').split('_')[1];
            updateSubTotal(tr);
            updateTotal(id);
          });
        
          // +++ Remove item
          $(document).on('click', '.removeItem', function(){
            let tr = $(this).closest('tr');
            let id = tr.closest('tbody').attr('class').split('_')[1];
            tr.remove();
            updateTotal(id);
          });
        
          // --- Helper Functions ---
        
          function addNewRow(id, itemId = '', itemName = '', itemPrice = 0){
            let rand = Math.floor(Math.random() * 10000);
            let nameText = itemName || '';
            let html = `
              <tr class="item_${rand}" data-id="${rand}">
                <td style="padding:2px;">
                  <input type="hidden" name="itemId[]" value="${itemId ? itemId : '0' + rand}">
                  <textarea class="form-control" name="title[]" placeholder="Write Details">${nameText}</textarea>
                </td>
                <td style="padding:2px;">
                  <div class="input-group">
                    <input type="number" class="form-control form-control-sm qty" name="qty[]" value="1" min="1" style="width:80px;max-width:80px;">
                    <input type="number" class="form-control form-control-sm price" name="price[]" value="${itemPrice}" step="0.01" min="0">
                  </div>
                  <b>Price:</b> <span class="subTotal">${itemPrice.toFixed(2)}</span>
                </td>
                <td style="padding:2px;width:40px;">
                  <span class="btn btn-danger btn-sm removeItem"><i class="bx bx-trash"></i></span>
                </td>
              </tr>
            `;
            $('.ItemBody_' + id).append(html);
            updateTotal(id);
          }
        
          function updateSubTotal(tr){
            let qty = parseFloat(tr.find('.qty').val()) || 0;
            let price = parseFloat(tr.find('.price').val()) || 0;
            let sub = qty * price;
            tr.find('.subTotal').text(sub.toFixed(2));
          }
        
          function updateTotal(id){
            let total = 0;
            $('.ItemBody_' + id + ' tr').each(function(){
              let qty = parseFloat($(this).find('.qty').val()) || 0;
              let price = parseFloat($(this).find('.price').val()) || 0;
              total += qty * price;
            });
            $('.ItemBody_' + id).closest('table').find('.totalSum').text('BDT ' + total.toFixed(2));
          }

    
    });
</script>
@endpush
