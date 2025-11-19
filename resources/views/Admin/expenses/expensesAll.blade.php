@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Expenses List')}}</title>
@endsection @push('css')
<style type="text/css">
    
    .expenseTableView tr th{
        padding:5px;
    }
    
    .expenseTableView tr td{
        padding:5px;
    }


    /* slip css start */



      .slip-container {
                background: #fff;
                border-radius: 4px;
                padding: 20px;
                position: relative;
        }
        
        .header {
            text-align: center;
        }
        
        .company-name {
            font-size: 30px;
            font-weight: bold;
            color: #000;
            margin: 0;
            letter-spacing: 1px;
            font-family: serif;
        }
        
        .subtitle {
            font-size: 13px;
            font-weight: bold;
            color: #000 !important;
            margin: 5px 0;
        }
        
        .contact-info {
                font-size: 12px;
                color: #000 !important;
                margin: 0px 0;
        }
        
        .transaction-badge {
            background: #000;
            color: white;
            padding: 5px 15px;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 0;
            font-size: 12px;
            font-weight: bold;
        }
        
        .date-field {
            position: absolute;
            top: 135px;
            right: 40px;
            font-size: 14px;
        }
        
        .date-label {
            font-weight: bold;
        }
        
        .form-section {
            margin: 20px 0;
        }
        
        .form-label {
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 0 !important;
        }
        
        .handwritten {
            font-size: 18px;
            color: #1a1a1a;
            font-style: italic;
        }
        
        .slip-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        
        .slip-table td {
            padding: 10px;
            border-bottom: 1px solid #5d8a3a;
        }
        
        .amount-column {
            text-align: right;
            font-weight: bold;
            width: 150px;
        }
        
        .total-row {
            border-top: 2px solid #2d5016;
            font-weight: bold;
            font-size: 16px;
        }
        
        .amount-words {
            margin: 15px 0;
            font-style: italic;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
        }
        
        .signature-box {
            text-align: center;
            flex: 1;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin: 40px 20px 5px 20px;
            position: relative;
        }
        
        .signature-text {
            font-family: 'Brush Script MT', cursive;
            font-size: 24px;
            margin-top: -35px;
            color: #1a3d0a;
        }
        
        .input-underline {
            border: none;
            border-bottom: 1px solid #000;
            background: transparent;
            width: 100%;
            font-size: 14px;
        }
        
        .input-underline:focus {
            outline: none;
            border-bottom-color: #2d5016;
        }
        .amountWriteText {
            display: flex;
            align-items: end;
        }

        .siral {
            left: 40px;
        }

    /* slip css end */
    
    
    
</style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Expenses List</h3>
         <div class="dropdown">
             @isset(json_decode(Auth::user()->permission->permission, true)['expenses']['add'])
             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddExpense" style="padding:5px 15px;">
                 <i class="bx bx-plus"></i> Expense
             </a>
             @endisset
             
             <a href="{{route('admin.expenses')}}" class="btn-custom yellow">
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
                        <form action="{{route('admin.expenses')}}">
                            <div class="row">
                                <div class="col-md-12 mb-0">
                                    <div class="input-group">
                                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Expence Title" class="form-control {{$errors->has('search')?'error':''}}" />
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
        <form action="{{route('admin.expenses')}}">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group mb-1">
                        <select class="form-control form-control-sm rounded-0" name="action" required="">
                            <option value="">Select Action</option>
                            @isset(json_decode(Auth::user()->permission->permission, true)['expenses']['add'])
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                            @endisset
                            @isset(json_decode(Auth::user()->permission->permission, true)['expenses']['delete'])
                            <option value="5">Delete</option>
                            @endisset
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
                            <th style="min-width: 150px;">Description</th>
                            <th style="min-width: 100px;">Amount</th>
                            <th style="min-width: 120px;">Type</th>
                            <th style="min-width: 100px;">Account</th>
                            <th style="min-width: 100px;">Date</th>
                            <th style="min-width: 130px;width:130px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $i=>$expense)
                        <tr>
                            <td>
                                <div class="checkbox">
                                     <input class="inp-cbx" id="cbx_{{$expense->id}}" type="checkbox" name="checkid[]" value="{{$expense->id}}" style="display: none;" />
                                     <label class="cbx" for="cbx_{{$expense->id}}">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                     </label>
                                 </div>
                                <span style="margin:0 5px;">{{$expenses->currentpage()==1?$i+1:$i+($expenses->perpage()*($expenses->currentpage() - 1))+1}}</span>
                                @if($expense->status=='active')
                                <span style="color: #43d39e;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                @else
                                <span style="color: #FF9800;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-analyse"></i>
                                </span>
                                @endif
                            </td>
                            <td>
                                <span>{{$expense->description}}</span>
                                @if($expense->imageFile) <a href="{{asset($expense->imageFile->file_url)}}" target="_blank"><i class="bx bx-file"></i></a> @endif
                            </td>
                            <td>{{priceFormat($expense->amount)}}</td>
                            <td>{{$expense->category?$expense->category->name:''}}</td>
                            <td>{{$expense->account?$expense->account->name:''}}</td>
                            <td>{{$expense->created_at->format('d-m-Y')}}</td>
                            <td class="center">
                                @isset(json_decode(Auth::user()->permission->permission, true)['expenses']['add'])
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditExpense_{{$expense->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @endisset
                                
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#ViewExpense_{{$expense->id}}" class="btn-custom yellow">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$expenses->links('pagination::bootstrap-4')}}
            </div>
        </form>
        
        
    </div>
</div>
</div>

@isset(json_decode(Auth::user()->permission->permission, true)['expenses']['add'])
<!-- Add Modal -->
 <div class="modal fade text-left" id="AddExpense" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.expensesAction','create')}}" method="post" enctype="multipart/form-data">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Expense</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	       <div class="row">
    	           <div class="col-md-6 form-group">
        			    <label for="name">Date* </label>
                        <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" name="created_at" value="{{Carbon\Carbon::now()->format('Y-m-d')}}"  required="">
        				@if ($errors->has('created_at'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
        				@endif
                 	</div>
                 	<div class="col-md-6 form-group">
        			    <label for="name">Expense Type*</label>
                        <select class="form-control" name="expense_type">
                            <option value="">Select Type</option>
                            @foreach($expenseTypes as $type)
                            <option value="{{$type->id}}">{{$type->name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('expense_type'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('expense_type') }}</p>
        				@endif
                 	</div>
    	            
    	       </div>
    	       <div class="row">
    	           <div class="col-md-6 form-group">
        			    <label for="name">Payment Method *</label>
                        <select class="form-control" name="payment" required="">
                            <option value="">Select Method</option>
                            @foreach($paymentMethods as $method)
                            <option value="{{$method->id}}">{{$method->name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('payment'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('payment') }}</p>
        				@endif
                 	</div>
    	           
    	            <div class="col-md-6 form-group">
        			    <label for="name">Amount* </label>
                        <input type="number" step="any" class="form-control {{$errors->has('amount')?'error':''}}" name="amount" placeholder="Amount"  required="">
        				@if ($errors->has('amount'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('amount') }}</p>
        				@endif
                 	</div>
                 	<div class="col-md-12 form-group">
        			    <label for="name">Account Method *</label>
                        <select class="form-control" name="account" required="">
                            <option value="">Select Account</option>
                            @foreach($accountMethods as $method)
                            <option value="{{$method->id}}">{{$method->name}} - BDT {{priceFormat($method->amount)}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('payment'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('payment') }}</p>
        				@endif
                 	</div>
    	       </div>
    	       <div class="form-group">
    				<label for="name">Attachtment</label>
					<input type="file" class="form-control {{$errors->has('attachment')?'error':''}}" name="attachment" accept="image/*"  style="padding: 3px;">
					@if ($errors->has('attachment'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
					@endif
             	</div>

             	
    			<div class="form-group">
    				<label for="name">Description</label>
					<textarea name="description" rows="5" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description"></textarea>
					@if ($errors->has('description'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
					@endif
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Expense</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>

<!--Edit Modal -->
@foreach($expenses as $i=>$dpm)
 <div class="modal fade text-left" id="EditExpense_{{$dpm->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.expensesAction',['update',$dpm->id])}}" method="post" enctype="multipart/form-data" >
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Expense</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	       <div class="row">
    	           <div class="col-md-6 form-group">
        			    <label for="name">Date* </label>
                        <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" name="created_at" value="{{$dpm->created_at->format('Y-m-d')}}"  required="">
        				@if ($errors->has('created_at'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
        				@endif
                 	</div>
                 	<div class="col-md-6 form-group">
        			    <label for="name">Expense Type*</label>
                        <select class="form-control" name="expense_type">
                            <option value="">Select Type</option>
                            @foreach($expenseTypes as $type)
                            <option value="{{$type->id}}" {{$dpm->category_id==$type->id?'selected':''}} >{{$type->name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('expense_type'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('expense_type') }}</p>
        				@endif
                 	</div>
    	            
    	       </div>
    	       <div class="row">
    	           <div class="col-md-6 form-group">
        			    <label for="name">Payment Method *</label>
                        <select class="form-control" name="payment" required="">
                            <option value="">Select Method</option>
                            @foreach($paymentMethods as $method)
                            <option value="{{$method->id}}" {{$dpm->method_id==$method->id?'selected':''}}>{{$method->name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('payment'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('payment') }}</p>
        				@endif
                 	</div>
    	            <div class="col-md-6 form-group">
        			    <label for="name">Amount* </label>
                        <input type="number" disabled="" class="form-control {{$errors->has('amount')?'error':''}}" step="any" value="{{$dpm->amount}}" placeholder="Amount" >
        				@if ($errors->has('amount'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('amount') }}</p>
        				@endif
                 	</div>
                 	<div class="col-md-12 form-group">
        			    <label for="name">Account Method *</label>
                        <select class="form-control" disabled="">
                            <option value="{{$dpm->account_id}}">{{$dpm->account?$dpm->account->name:''}}</option>
                        </select>
        				@if ($errors->has('payment'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('payment') }}</p>
        				@endif
                 	</div>
    	       </div>
    	       <div class="form-group">
    				<label for="name">Attachtment</label>
					<input type="file" class="form-control {{$errors->has('attachment')?'error':''}}" name="attachment" style="padding: 3px;">
					@if ($errors->has('attachment'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
					@endif
             	</div>
    	   		
    			 <div class="form-group">
    				<label for="name">Description</label>
					<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{!!$dpm->description!!}</textarea>
					@if ($errors->has('description'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
					@endif
             	</div>
             	<div class="row">
                 	<div class="col-md-6 form-group">
                 	    <label for="name">Status</label><br>
                 	    <div class="checkbox">
                             <input class="inp-cbx" id="status_{{$dpm->id}}" type="checkbox" name="status" style="display: none;" {{$dpm->status=='active'?'checked':''}} />
                             <label class="cbx" for="status_{{$dpm->id}}">
                                 <span>
                                     <svg width="12px" height="10px" viewbox="0 0 12 10">
                                         <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                     </svg>
                                 </span>
                                 Active
                             </label>
                         </div>
                 	</div>
                    <div class="col-md-6 form-group">
                        <label for="name">Publish Date*</label>
                        <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{$dpm->created_at->format('Y-m-d')}}" name="created_at" required="">
                        @if ($errors->has('created_at'))
    					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
    					@endif
                    </div>
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Expense</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>
@endforeach
@endisset
<!--View Modal -->
@foreach($expenses as $i=>$dpm)
 <div class="modal fade text-left" id="ViewExpense_{{$dpm->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
	 <div class="modal-content">


	 {{--<form action="{{route('admin.expensesAction',['update',$dpm->id])}}" method="post" enctype="multipart/form-data" >
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">View Expense</h4>
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
    	                   <th>Expense Type</th>
    	                   <th>:</th>
    	                   <td>{{$dpm->category?$dpm->category->name:''}}</td>
    	               </tr>
    	               <tr>
    	                   <th>Payment Method</th>
    	                   <th>:</th>
    	                   <td>{{$dpm->method?$dpm->method->name:''}}</td>
    	               </tr>
    	               <tr>
    	                   <th>Account Method</th>
    	                   <th>:</th>
    	                   <td>{{$dpm->account?$dpm->account->name:''}}</td>
    	               </tr>
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
    	               <!-- <tr>
    	                   <th>Title/Ref</th>
    	                   <th>:</th>
    	                   <td>{{$dpm->name}}</td>
    	               </tr> -->
    	               <tr>
    	                   <th>Description</th>
    	                   <th>:</th>
    	                   <td>{!!$dpm->description!!}</td>
    	               </tr>
    	           </table>
    	       </div>
    	   </div>
	   </form>--}}




         <div class="slip-container">

          <div class="date-field siral">
            <span class="date-label">SL:</span>
            <input type="text" class="input-underline" style="width: 100px;" value="123748893457498">
        </div>

        <div class="header">
            <h1 class="company-name">ANR Fashion Wear Limited.</h1>
            <p class="subtitle">(100% Export Oriented Garments Manufacturing Factory)</p>
            <p class="contact-info">Office: 3 Kazi Nazrul Islam Road, National University, Gazipur-1704, Bangladesh</p>
            <p class="contact-info">Mobile: 01842 481023, info@anrfashion.com</p>
            
            <div class="transaction-badge">TRANSACTION SLIP</div>
        </div>
        
        <div class="date-field">
            <span class="date-label">Date:</span>
            <input type="text" class="input-underline" style="width: 100px;" value="{{$dpm->created_at->format('Y-m-d')}}">
        </div>
        
        <div class="form-section">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="amountWriteText">
                        <label class="form-label">Account: </label>
                        <input type="text" class="input-underline handwritten" value="S/o Bell Exp (Process) Sewing">
                    </div>
               
                </div>
            </div>
            
           
        </div>
        

     <table class="table table-bordered">
        <tbody>
            <tr>
                <td colspan="8"><b>Cash Paid To </b></td>
                <td><b>Taka</b></td>
            </tr>
            <tr>
                <td colspan="8">{!!$dpm->description!!}</td>
                <td>{{priceFormat($dpm->amount)}}</td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="8" style="text-align: right;"><b>Total </b></td>
                <td>{{priceFormat($dpm->amount)}}</td>
            </tr>
        </tbody>
    </table>

   <div class="form-section">
        <div class="row mb-3">
            <div class="col-12">
                <div class="amountWriteText">
                    <label class="form-label" style="min-width: 100px;">Taka in word:</label>
                    <input type="text" class="input-underline handwritten" value="Five Thousand Only">
                </div>
            
            </div>
        </div>
    </div>
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-text">Athlas</div>
                </div>
                <small>Receiver</small>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-text">Athlas</div>
                </div>
                <small>Accountant</small>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                     <div class="signature-text">Athlas</div>
                </div>
                <small>Approved by</small>
            </div>
        </div>
    </div>







	 </div>
   </div>
 </div>
@endforeach



@endsection 
@push('js')



@endpush