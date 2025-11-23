@extends(adminTheme().'layouts.app') @section('title')
<title>Account Statement Report</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Account View</h3>
            <div class="dropdown">
                <a href="javascript:void(0)" class="btn-custom danger" style="padding:5px 15px;" id="ExportAction" ><i class="fa-solid fa-file-excel"></i> Export</a>
                <a href="javascript:void(0)" class="btn-custom primary" style="padding:5px 15px;" id="PrintAction" >
                    <i class="fa fa-print"></i> Print
                </a>
                <a href="{{route('admin.accountsStatement')}}" class="btn-custom yellow">
                    <i class="bx bx-rotate-left"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')
            <div class="row">
                <div class="col-md-8">
                    <form action="{{route('admin.accountsStatement')}}">
                        <div class="row">
                            <div class="col-md-4 mb-0">
                                <label>Select Account</label>
                                <select class="form-control" name="account_id">
                                    <option value="">Select Method</option>
                                    @foreach($accounts as $account)
                                    <option value="{{$account->id}}" {{$account->id==request()->account_id?'selected':''}}>{{$account->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8 mb-0">
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
                <div class="col-md-4">

                </div>
            </div>
            
            
            <br>
            @if($method)
            <div class="PrintAreaContact">
                <style>
                    .tableReport tr th{
                        padding: 5px 10px;
                        border: 1px solid #dee2e6;  
                    }
                    .tableReport tr td{
                        padding: 5px 10px;
                        border: 1px solid #dee2e6;  
                    }
                </style>
                <div class="text-center mb-4">
                    <img src="{{asset(general()->logo())}}" alt="logo" style="max-height: 80px;">
                    <h2>{{general()->title}}</h2>
                    <p>
                        {!!general()->address_one!!}
                        <br>
                        <b>Phone:</b> {{general()->mobile}}
                        <b>Email:</b> {{general()->email}}
                        <br>
                        <b>Date:</b>
                        {{ date('d M, Y') }}
                    </p>
                    <span style="display: inline-block;padding: 1px 25px;border: 1px solid #e3cfcf;border-radius: 5px;background: #fbfbfb;">{{$method->name}} Statement</span>
                </div>
                <div class="table-responsive">
                    <table  class="table tableReport" >
                        <thead>
                            <tr>
                                <th style="width: 120px;min-width: 120px;">Date</th>
                                <th style="width: 130px;min-width: 130px;">Method</th>
                                <th style="min-width: 200px;">Concern Person</th>
                                <th style="width: 130px;min-width: 130px;">Type</th>
                                <th style="width: 130px;min-width: 130px;">Debit</th>
                                <th style="width: 130px;min-width: 130px;">Credit</th>
                                <th style="width: 150px;min-width: 150px;">Balance</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($transections as $tran)
                                <tr>
                                    <td>{{ $tran->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $tran->paymentMethod->name ?? '' }}</td>
                                    <td>
                                        @if($tran->type == 0)
                                            {{ $tran->sale->name ?? '' }} 
                                            {{ $tran->billing_note?'- '.$tran->billing_note:'' }}
                                        @elseif($tran->type==1)
                                            <b>TNX ID:</b> {{ $tran->transection_id }} - <b>Account:</b> {{$tran->account?$tran->account->name:'N/A'}} {{ $tran->billing_note?'- '.$tran->billing_note:'' }}
                                        @elseif($tran->type==5)
                                           @if($tran->expense)
                                            <b>Company:</b> {{ $tran->expense->company_name}} - <b>Receiver:</b> {{ $tran->expense->receiver_name}} {{ $tran->expense->description?'- '.$tran->expense->description:'' }}
                                            @else
                                            <span>N/A</span>
                                            @endif
                                        @elseif($tran->type==3)

                                            @if($tran->purchase)
                                               <b>Invoice:</b> {{$tran->purchase->order_no}}
                                               <b>Supplier:</b> {{$tran->purchase->supplier_name}}
                                            @else
                                            <span>N/A</span>
                                            @endif
                                        @else
                                            {{ $tran->transection_id }}

                                        @endif
                                        
                                    </td>
                                    <td>

                                            @if($tran->type==0)
                                                Sales
                                            @elseif($tran->type==1)
                                                Deposit
                                            @elseif($tran->type==3)
                                                Supplier Bill
                                            @elseif($tran->type==4)
                                                Transfer Balance
                                            @elseif($tran->type==5)
                                                 Expense
                                            @elseif($tran->type==6)
                                                Withdrawal
                                            @elseif($tran->type==7)
                                                Expense IOU
                                            @else
                                                Unknown
                                            @endif

                                    </td>
                                    <td>
                                        @if(in_array($tran->type, [3,4,5,6,7]))
                                            {{ priceFormat($tran->amount) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(in_array($tran->type, [0,1]))
                                            {{ priceFormat($tran->amount) }}
                                        @endif
                                    </td>
                                    <td>{{ priceFormat($tran->running_balance) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" style="text-align:center;">No Record</td>
                                </tr>
                                @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"></td>
                                <td>Available</td>
                                <td>{{ priceFormat($availableBalance ?? 0) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif
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