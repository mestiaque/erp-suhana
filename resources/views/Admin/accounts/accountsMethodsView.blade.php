@extends(adminTheme().'layouts.app') @section('title')
<title>{{$method->name}} Account Statement Report</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Account View</h3>
         <div class="dropdown">
             <a href="{{route('admin.accountsMethods')}}" class="btn-custom primary"  style="padding:5px 15px;">
                  Account List
             </a>
             <a href="{{route('admin.accountsMethodsAction',['view',$method->id])}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <div class="row">
            <div class="col-md-6">
                <form action="{{route('admin.accountsMethodsAction',['view',$method->id])}}">
                    <div class="row">
                        <div class="col-md-12 mb-0">
                            <label>Seach Here..</label>
                            <div class="input-group">
                                <input type="date" name="startDate" value="{{$from->format('Y-m-d')}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                                <input type="date" value="{{$to->format('Y-m-d')}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                                <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <div class="single-stats-card-box">
                     <div class="icon">
                         <i class="bx bxs-badge-dollar"></i>
                     </div>
                     <span class="sub-title">{{$method->name}} </span>
                     <h3>BDT {{priceFormat($method->amount)}} <span class="badge"></h3>
                     <!--<h3>USD {{priceFormat($method->usd_amount)}} <span class="badge"></h3>-->
                 </div>
            </div>
        </div>
        
        
        <br>
        <div class="table-responsive">
            <table id="example" class="display nowrap" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Concern Person</th>
                        <th>Reff</th>
                        <th>Type</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Concern Person</th>
                        <th>Reff</th>
                        <th>Type</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($transections as $i=>$transection)
                    <tr>
                        <td>{{$transection->created_at->format('d-m-Y')}}</td>
                        <td>
                            {{$transection->paymentMethod?$transection->paymentMethod->name:''}}
                        </td>
                        <td>
                            @if($transection->type==0)
                            {{$transection->sale?$transection->sale->name:''}}
                            @else
                            {{$transection->transection_id}}
                            @endif
                            
                        </td>
                        <td>
                            @if($transection->type==0)
                            <span>{{$transection->expense?$transection->expense->description:''}}</span>
                            @elseif($transection->type==2)
                            {{$transection->billing_note}}
                            @elseif($transection->type==3)
                            @if($payBill =$transection->traddingBill)
                            {{$payBill->title}}
                            @endif
                            @elseif($transection->type==4)
                            {{$transection->billing_note}}
                            @else
                            {{$transection->billing_note}}
                            @endif
                        </td>
                        <td>
                            @if($transection->type==0)
                            Sales
                            @elseif($transection->type==1)
                            Deposit
                            @elseif($transection->type==6)
                            Withdrawal
                            @endif
                        </td>
                        <td>
                            @if($transection->type==6)
                            {{priceFormat($transection->amount)}}
                            @endif
                        </td>
                        <td>
                            @if($transection->type==0 || $transection->type==1)
                            {{priceFormat($transection->amount)}}
                            @endif
                        </td>
                        <td>{{priceFormat($transection->running_balance)}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>




@endsection @push('js') 
<script>
    $(document).ready(function () {
        
        $('#example').DataTable( {
	        dom: 'Bfrtip',
	        buttons: [
	            'excel', 'pdf', 'print'
	        ]
	    } );
        
    });
</script>

@endpush