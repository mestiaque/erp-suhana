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
                                    <option value="{{ $account->id }}"
                                        {{ $account->id == request()->account_id || (isset($method) && $account->id == $method->id) ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
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
                            {!!general()->address_one!!}<br>
                            <b>Phone:</b> {{general()->mobile}}
                            <b>Email:</b> {{general()->email}}<br>
                            <b>Date:</b> {{ date('d M, Y') }}
                        </p>
                        <span style="display: inline-block;padding: 1px 25px;border: 1px solid #e3cfcf;border-radius: 5px;background: #fbfbfb;">{{$method->name}} Statement</span>
                    </div>

                    @php
                        $types = [
                            0 => 'Sales',
                            1 => 'Deposit',
                            3 => 'Supplier Bill',
                            4 => 'Transfer Balance',
                            5 => 'Expense',
                            6 => 'Withdrawal',
                            7 => 'I.O.U',
                        ];

                        // Calculate total per type
                        $typeTotals = [];
                        foreach ($types as $tId => $tName) {
                            $typeTotals[$tId] = $transections->where('type', $tId)->sum('amount');
                        }

                        // Sum of all type totals
                        $sumOfAllTypes = array_sum($typeTotals);

                        // Previous balance = starting amount + sum of all type totals
                        $previousBalance = ($method->amount ?? 0) + $sumOfAllTypes;
                    @endphp

                    {{-- Per-Type Transaction Tables --}}
                    @foreach($types as $typeId => $typeName)
                        @php
                            $group = $transections->where('type', $typeId);
                            $subTotal = 0;
                        @endphp
                        </br>
                        <h5 style="margin-top:20px;">{{ $typeName }} Transactions</h5>
                        <div class="table-responsive">
                            <table class="table tableReport table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:120px;">Date</th>
                                        <th style="width:130px;">Method</th>
                                        <th>Concern Person</th>
                                        <th style="width:130px;">Type</th>
                                        <th style="width:130px;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($group->count() > 0)
                                        @foreach($group as $tran)
                                            <tr>
                                                <td>{{ $tran->created_at->format('d-m-Y') }}</td>
                                                <td>{{ $tran->paymentMethod->name ?? '' }}</td>
                                                <td>
                                                    @if($tran->type == 0)
                                                        {{ $tran->sale->name ?? '' }} {{ $tran->billing_note ? '- '.$tran->billing_note : '' }}
                                                    @elseif($tran->type == 1)
                                                        <b>TNX ID:</b> {{ $tran->transection_id }} - <b>Account:</b> {{ $tran->account->name ?? 'N/A' }}
                                                    @elseif($tran->type == 3)
                                                        {{ $tran->purchase ? 'Invoice: '.$tran->purchase->order_no.' - Supplier: '.$tran->purchase->supplier_name : 'N/A' }}
                                                    @elseif($tran->type == 4)
                                                        Transfer
                                                    @elseif($tran->type == 5)
                                                        {{ $tran->expense ? 'Company: '.$tran->expense->company_name.' - Receiver: '.$tran->expense->receiver_name : 'N/A' }}
                                                    @elseif($tran->type == 6)
                                                        <b>TNX ID:</b> {{ $tran->transection_id }} - <b>Account:</b> {{ $tran->account->name ?? 'N/A' }}
                                                    @elseif($tran->type == 7)
                                                        {{ $tran->expenseIou ? 'Company: '.$tran->expenseIou->company_name.' - Receiver: '.$tran->expenseIou->receiver_name : 'N/A' }}
                                                    @else
                                                        {{ $tran->transection_id }}
                                                    @endif
                                                </td>
                                                <td>{{ $typeName }}</td>
                                                <td>
                                                    {{ priceFormat($tran->amount) }}
                                                    @php $subTotal += $tran->amount; @endphp
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" style="text-align:center;">No Record Found</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr style="font-weight:bold; background:#f0f0f0;">
                                        <td colspan="4" style="text-align:right;">Subtotal</td>
                                        <td>{{ priceFormat($subTotal) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endforeach

                    {{-- Grand Total --}}
                    @php
                        $grandDebit = $transections->whereIn('type',[3,4,5,6,7])->sum('amount');
                        $grandCredit = $transections->whereIn('type',[0,1])->sum('amount');
                    @endphp
                    <div class="" style="margin-top:20px; font-size:25px; font-weight:bold; text-align:center;border: 1px solid lightgray;">
                        Grand Total: {{ priceFormat($grandDebit) }}
                    </div>


                    {{-- Summary Table --}}
                    <h4 class="mt-4">Account Summary</h4>
                    <div class="table-responsive">
                        <table class="table tableReport table-bordered">
                            <thead>
                                <tr>
                                    <th>Previous Balance</th>
                                    @foreach($types as $typeName)
                                        <th>{{ $typeName }}</th>
                                    @endforeach
                                    <th>Total Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight:bold;">{{ priceFormat($previousBalance) }}</td>
                                    @foreach($types as $tId => $typeName)
                                        <td>{{ priceFormat($typeTotals[$tId] ?? 0) }}</td>
                                    @endforeach
                                    <td style="font-weight:bold;">{{ priceFormat($method->amount ?? 0) }}</td>
                                </tr>
                            </tbody>
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
