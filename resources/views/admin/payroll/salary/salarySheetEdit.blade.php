@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Salary Sheet view')}}</title>
@endsection @push('css')
<style type="text/css">
    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        position: absolute;
        right: 0;
    }
    .select2-container .select2-search--inline .select2-search__field {
        padding: 0px 10px;
    }
    .select2-container--default .select2-selection--multiple {
        border-radius: 0;
    }
</style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Salary Sheet view</h3>
        <div class="dropdown">
            @isset(json_decode(Auth::user()->permission->permission, true)['salarySheet']['view'])
            <a href="{{route('admin.salarySheetAction',['export',$action])}}" class="btn-custom danger"><i class="bx bx-export"></i> Export</a>
            @endisset
            <!--<a href="{{route('admin.salarySheetAction',['print',$action])}}" class="btn-custom yellow"><i class="bx bx-print"></i> Print</a>-->
            <a href="{{route('admin.salarySheet')}}" class="btn-custom success"><i class="bx bx-list"></i> Back</a>
            <a href="{{route('admin.salarySheetAction',$action)}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
            </a>
        </div>
    </div>
    <div class="card-body">
            @include(adminTheme().'alerts')
            
            <div class="salaryHead" style="text-align:center;">
                <img src="{{asset(general()->logo())}}" style="max-height: 60px;">
                <h2>Salary Sheet</h2>
                <h5>{{$createDate->format('F')}} - {{$createDate->format('Y')}}</h5>
                <p><b>Employee:</b> {{$corporateSalaries->count()+$salaries->count()}} <b>Salary:</b>BDT {{priceFormat($salaries->sum('net_salary_amount')+$corporateSalaries->sum('net_salary_amount'))}}</p>
            </div>
            
            <form action="{{route('admin.salarySheetAction',$action)}}">
                <div class="row">
                    <div class="col-md-10 mb-0">
                        <select class="form-control select2" id="multiple" multiple name="employee_id[]">
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                            <option value="{{$employee->id}}">{{$employee->name}} {{$employee->employee_id?' - ID'.$employee->employee_id:''}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-0">
                        <button type="submit" class="btn btn-success btn-sm rounded-0"><i class="bx bx-plus"></i> Add</button>
                    </div>
                </div>
            </form>

        <br>
            <form action="{{route('admin.salarySheetAction','update')}}">
                <div class="row">
                <div class="col-md-4">
                    <div class="input-group mb-1">
                        <select class="form-control form-control-sm rounded-0" name="action" required="">
                            <option value="">Select Action</option>
                            <option value="1">Paid</option>
                            <option value="2">Unpaid</option>
                            <option value="3">Remove</option>
                        </select>
                        <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    
                </div>
                <div class="col-md-4">
   
                </div>
            </div>
            <div class="table-responsive">
                <h5>Corporate Employee: {{$corporateSalaries->count()}}</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px; width: 100px;padding-right:0;">
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
                            <th style="min-width: 200px;">Name</th>
                            <th style="min-width: 120px;">Basic (BDT)</th>
                            <th style="min-width: 120px;">DPS/Home</th>
                            <th style="min-width: 100px;">Phone</th>
                            <th style="min-width: 100px;">Salary</th>
                            <th style="min-width: 100px;">Allowances</th>
                            <th style="min-width: 100px;">Bonus</th>
                            <th style="min-width: 100px;">Deduction</th>
                            <th style="min-width: 100px;">Due/Loan</th>
                            <th style="min-width: 120px;width:120px;">Net Salary</th>
                            <th style="min-width: 120px;width:120px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($corporateSalaries as $i=>$salary)
                        <tr>
                            <td>
                                <div class="checkbox">
                                     <input class="inp-cbx" id="cbx_{{$salary->id}}" type="checkbox" name="checkid[]" value="{{$salary->id}}" style="display: none;" />
                                     <label class="cbx" for="cbx_{{$salary->id}}">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                     </label>
                                 </div>
                                 {{$i+1}}
                                 @if($salary->status=='paid')
                                <span class="bx bx-check text-success"></span>
                                @else
                                <span class="bx bx-x text-danger"></span>
                                @endif
                            </td>
                            <td><a href="javascript:void(0)" data-toggle="modal" data-target="#editSalary_{{$salary->id}}" >{{$salary->user?$salary->user->name:''}}</a></td>
                            <td>{{priceFormat($salary->salary_amount)}}</td>
                            <td>{{priceFormat($salary->home_bill)}}</td>
                            <td>{{priceFormat($salary->mobile_bill)}}</td>
                            <td>{{priceFormat($salary->salary_amount+$salary->mobile_bill+$salary->home_bill)}}</td>
                            <td>{{priceFormat($salary->allowances_amount)}}</td>
                            <td>{{priceFormat($salary->bonus_amount)}}</td>
                            <td>{{priceFormat($salary->deduction_amount)}}</td>
                            <td>{{priceFormat($salary->user?$salary->user->loans()->where('status','<>','paid')->sum('balance'):0)}}</td>
                            <td>{{priceFormat($salary->net_salary_amount)}}</td>
                            <td>{!!$salary->remarks!!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <h5>Factory Employee: {{$salaries->count()}}</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px; width: 100px;padding-right:0;">
                                
                            </th>
                            <th style="min-width: 200px;">Employee Name</th>
                            <th style="min-width: 120px;">Basic (BDT)</th>
                            <th style="min-width: 120px;">Working Day</th>
                            <th style="min-width: 100px;">Att. Bouns</th>
                            <th style="min-width: 100px;">Amount/day</th>
                            <th style="min-width: 100px;">Salary</th>
                            <th style="min-width: 120px;">O.T Hours.</th>
                            <th style="min-width: 120px;">Amont/Hours.</th>
                            <th style="min-width: 120px;">O.T Value.</th>
                            <th style="min-width: 100px;">Allowances</th>
                            <th style="min-width: 100px;">Bonus</th>
                            <th style="min-width: 100px;">Deduction</th>
                            <th style="min-width: 100px;">Due/Loan</th>
                            <th style="min-width: 120px;width:120px;">Net Salary</th>
                            <th style="min-width: 120px;width:120px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salaries as $ii=>$salary2)
                        <tr>
                            <td>
                                <div class="checkbox">
                                     <input class="inp-cbx" id="cbx_{{$salary2->id}}" type="checkbox" name="checkid[]" value="{{$salary2->id}}" style="display: none;" />
                                     <label class="cbx" for="cbx_{{$salary2->id}}">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                     </label>
                                 </div>
                                 {{$ii+1}}
                                 @if($salary2->status=='paid')
                                <span class="bx bx-check text-success"></span>
                                @else
                                <span class="bx bx-x text-danger"></span>
                                @endif
                            </td>
                            <td><a href="javascript:void(0)" data-toggle="modal" data-target="#editSalary_{{$salary2->id}}" >{{$salary2->user?$salary2->user->name:''}}</a></td>
                            <td>{{priceFormat($salary2->salary_amount)}}</td>
                            <td>{{$salary2->employee_working_day}} - <small>({{$salary2->total_working_day}})</small></td>
                            <td>{{$salary2->bonus_working_day}}</td>
                            <td>{{priceFormat($salary2->working_day_rate)}}</td>
                            <td>{{priceFormat($salary2->woking_salary_amount)}}</td>
                            <td>{{$salary2->over_time_hour}} Hours</td>
                            <td>{{priceFormat($salary2->over_time_hour_rate)}}</td>
                            <td>{{priceFormat($salary2->over_time_amount)}}</td>
                            <td>{{priceFormat($salary2->allowances_amount)}}</td>
                            <td>{{priceFormat($salary2->bonus_amount)}}</td>
                            <td>{{priceFormat($salary2->deduction_amount)}}</td>
                            <td>{{priceFormat($salary2->user?$salary2->user->loans()->where('status','<>','paid')->sum('balance'):0)}}</td>
                            <td>{{priceFormat($salary2->net_salary_amount)}}</td>
                            <td>{!!$salary2->remarks!!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </form>
    </div>
</div>
</div>




@foreach($corporateSalaries as $data)
 <!-- Modal -->
 <div class="modal fade text-left" id="editSalary_{{$data->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
	 <div class="modal-content">
	     <div class="modal-header">
            <h4 class="modal-title">Salary Edit</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times; </span>
            </button>
        </div>
	   <div class="modal-body">
	       <form id="SalaryUpdate_{{$data->id}}" action="{{route('admin.salarySheetAction',['salary-update',$data->id])}}" method="post">
	           @csrf
	   		<div class="table-responsive">
	   		    <table class="table table-borderless SalaryEditTable" >
	   		        <tr>
	   		            <th style="width: 200px;min-width: 150px;">Name</th>
	   		            <th style="width: 30px;min-width: 30px;">:</th>
	   		            <td style="min-width: 200px;">
	   		                @if($data->user)
	   		                {{$data->user->name}} <a href="{{route('admin.usersCustomerAction',['view',$data->user->id])}}" target="_blank" class="btn-custom yellow"><i class="bx bx-link"></i></a>
	   		                @else
	   		                <span>Not Found</span>
	   		                @endif
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Basic Salary</th>
	   		            <th>:</th>
	   		            <td>{{$data->salary_amount}}</td>
	   		        </tr>
	   		        <tr>
	   		            <th>DPS/Home (BDT)</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <input type="number" name="home_bill" value="{{$data->home_bill}}" class="form-control form-control-sm" placeholder="Amount" >
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Phone (BDT)</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <input type="number" name="mobile_bill" value="{{$data->mobile_bill}}" class="form-control form-control-sm" placeholder="Amount" >
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Allowances (BDT)</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <input type="number" name="allowances_amount" value="{{$data->allowances_amount}}" class="form-control form-control-sm" placeholder="Amount" >
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Deduction (BDT)</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <input type="number" name="deduction_amount" value="{{$data->deduction_amount}}" class="form-control form-control-sm" placeholder="Amount" >
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Due/Loans</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                @if($data->user)
	   		                
	   		                <table class="table table-bordered">
	   		                    <tr>
	   		                        <th>Date</th>
	   		                        <th>Loan Amount</th>
	   		                        <th>Due</th>
	   		                        <th style="width: 200px;min-width:200px;">Pay Now</th>
	   		                    </tr>
	   		                    @if($data->user->loans()->where('status','<>','paid')->count() > 0)
	   		                    @foreach($data->user->loans()->where('status','<>','paid')->get() as $loan)
	   		                    <tr>
	   		                        <td style="padding: 10px 5px;" >{{$loan->created_at->format('d M, Y')}}
	   		                        <input type="hidden" name="loan_id[]" value="{{$loan->id}}">
	   		                        </td>
	   		                        <td style="padding: 10px 5px;" >BDT {{priceFormat($loan->amount)}}</td>
	   		                        <td style="padding: 10px 5px;" >BDT {{priceFormat($loan->amount-$loan->paid_balance)}}</td>
	   		                        <td style="padding:5px;">
	   		                           <div class="input-group">
            	   		                    <input type="number" name="due_loan[]" class="form-control form-control-sm loanValue loanValue_{{$loan->id}}" data-due="{{$loan->amount-$loan->paid_balance}}" placeholder="Amount" >
            	   		                    <div class="input-group-append">
                                                <span class="input-group-text" style="padding:2px 5px;"><input type="checkbox" class="loanCheck loanCheck_{{$loan->id}}" data-id="{{$loan->id}}" id="laon_{{$loan->id}}" style="margin-right:5px;"> <label for="laon_{{$loan->id}}" data-id="{{$loan->id}}" style="margin:0;">Full Pay</label></span>
                                            </div>
	   		                           </div>
            	   		            </td>
	   		                    </tr>
	   		                    @endforeach
	   		                    @else
	   		                    <tr>
	   		                        <td colspan="4" style="text-align:center;">No Loan</td>
	   		                    </tr>
	   		                    @endif
	   		                </table>
	   		                
	   		                @endif
	   		                
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Remarks</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <textarea type="number" name="remarks" class="form-control form-control-sm" placeholder="Write Remarks" >{!!$data->remarks!!}</textarea>
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Status</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <select class="form-control" name="status" required="">
	   		                    <option value="paid" {{$data->status=='paid'?'selected':''}} >Paid</option>
	   		                    <option value="unpaid" {{$data->status=='unpaid'?'selected':''}} >Unpaid</option>
	   		                </select>
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Action</th>
	   		            <th>:</th>
	   		            <td>
	   		                <button type="submit" class="btn btn-sm btn-success salaryUpdate"><i class="bx bx-check"></i> Update</button>
	   		            </td>
	   		        </tr>
	   		    </table>
	   		</div>
	   		</form>
	   </div>
	 </div>
   </div>
 </div>
@endforeach

@foreach($salaries as $data)
 <!-- Modal -->
 <div class="modal fade text-left" id="editSalary_{{$data->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
	 <div class="modal-content">
	     <div class="modal-header">
            <h4 class="modal-title">Salary Edit</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times; </span>
            </button>
        </div>
	   <div class="modal-body">
	       <form id="SalaryUpdate_{{$data->id}}" action="{{route('admin.salarySheetAction',['salary-update',$data->id])}}" method="post">
	           @csrf
	   		<div class="table-responsive">
	   		    <table class="table table-borderless SalaryEditTable" >
	   		        <tr>
	   		            <th style="width: 200px;min-width: 150px;">Name</th>
	   		            <th style="width: 30px;min-width: 30px;">:</th>
	   		            <td style="min-width: 200px;">
	   		                @if($data->user)
	   		                {{$data->user->name}} <a href="{{route('admin.usersCustomerAction',['view',$data->user->id])}}" target="_blank" class="btn-custom yellow"><i class="bx bx-link"></i></a>
	   		                @else
	   		                <span>Not Found</span>
	   		                @endif
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Basic Salary</th>
	   		            <th>:</th>
	   		            <td>{{$data->salary_amount}}</td>
	   		        </tr>
	   		        <tr>
	   		            <th>Working Day ({{$data->total_working_day}})</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <input type="number" name="working_day" value="{{$data->employee_working_day}}" class="form-control form-control-sm" placeholder="Day" >
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Att. Bonus Day</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <input type="number" name="bonus_day" value="{{$data->bonus_working_day}}" class="form-control form-control-sm" placeholder="Day" >
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Over Time Hours.</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <input type="number" name="overtime_hours" value="{{$data->over_time_hour}}" class="form-control form-control-sm" placeholder="Hour" >
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Allowances (BDT)</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <input type="number" name="allowances_amount" value="{{$data->allowances_amount}}" class="form-control form-control-sm" placeholder="Amount" >
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Deduction (BDT)</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <input type="number" name="deduction_amount" value="{{$data->deduction_amount}}" class="form-control form-control-sm" placeholder="Amount" >
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Due/Loans</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                @if($data->user)
	   		                
	   		                <table class="table table-bordered">
	   		                    <tr>
	   		                        <th>Date</th>
	   		                        <th>Loan Amount</th>
	   		                        <th>Due</th>
	   		                        <th style="width: 200px;min-width:200px;">Pay Now</th>
	   		                    </tr>
	   		                    @if($data->user->loans()->where('status','<>','paid')->count() > 0)
	   		                    @foreach($data->user->loans()->where('status','<>','paid')->get() as $loan)
	   		                    <tr>
	   		                        <td style="padding: 10px 5px;">{{$loan->created_at->format('d M, Y')}}
	   		                        <input type="hidden" name="loan_id[]" value="{{$loan->id}}">
	   		                        </td>
	   		                        <td style="padding: 10px 5px;">{{priceFormat($loan->amount)}}</td>
	   		                        <td style="padding: 10px 5px;">{{priceFormat($loan->amount-$loan->paid_balance)}}</td>
	   		                       <td style="padding:5px;">
	   		                           <div class="input-group">
            	   		                    <input type="number" name="due_loan[]" class="form-control form-control-sm loanValue loanValue_{{$loan->id}}" data-due="{{$loan->amount-$loan->paid_balance}}" placeholder="Amount" >
            	   		                    <div class="input-group-append">
                                                <span class="input-group-text" style="padding:2px 5px;"><input type="checkbox" class="loanCheck loanCheck_{{$loan->id}}" data-id="{{$loan->id}}" id="laon_{{$loan->id}}" style="margin-right:5px;" > <label for="laon_{{$loan->id}}" data-id="{{$loan->id}}" style="margin:0;">Full Pay</label></span>
                                            </div>
	   		                           </div>
            	   		            </td>
	   		                    </tr>
	   		                    @endforeach
	   		                    @else
	   		                    <tr>
	   		                        <td colspan="4" style="text-align:center;" >No Loan</td>
	   		                    </tr>
	   		                    @endif
	   		                </table>
	   		                
	   		                @endif
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Remarks</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <textarea type="number" name="remarks" class="form-control form-control-sm" placeholder="Write Remarks" >{!!$data->remarks!!}</textarea>
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Status</th>
	   		            <th>:</th>
	   		            <td style="padding:5px;">
	   		                <select class="form-control" name="status" required="">
	   		                    <option value="paid" {{$data->status=='paid'?'selected':''}} >Paid</option>
	   		                    <option value="unpaid" {{$data->status=='unpaid'?'selected':''}} >Unpaid</option>
	   		                </select>
	   		            </td>
	   		        </tr>
	   		        <tr>
	   		            <th>Action</th>
	   		            <th>:</th>
	   		            <td>
	   		                <button type="submit" class="btn btn-sm btn-success salaryUpdate"><i class="bx bx-check"></i> Update</button>
	   		            </td>
	   		        </tr>
	   		    </table>
	   		</div>
	   		</form>
	   </div>
	 </div>
   </div>
 </div>
 @endforeach


@endsection 
@push('js') 
<script>
    $(document).ready(function(){
        $("#multiple").select2({
              placeholder: "Select Employee ID",
              allowClear: true
          });
          
        
        $('.loanCheck').click(function(){
            var id =$(this).data('id');
            var dueAmount =$('.loanValue_'+id).data('due');
            $('.loanValue_'+id).val(dueAmount);
            if($('.loanCheck_'+id).prop('checked')){
                $('.loanValue_'+id).val(dueAmount);
            }else{
                $('.loanValue_'+id).val(0);
            }
            
        }); 
         
    });
</script>
@endpush