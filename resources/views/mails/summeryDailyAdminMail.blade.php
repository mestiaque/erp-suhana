<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Daily ERP Report</title>
</head>

<body style="margin:0; padding:15px; background:#f1f1f1; font-family:Arial, sans-serif;">

<div style="margin:25px auto; max-width:1000px; background:#ffffff; padding:15px;">

    <!-- HEADER -->
    <p style="text-align:center;">
        <a href="{{route('index')}}" target="_blank">
            <img src="{{URL::asset(general()->logo())}}" style="max-width:200px;">
        </a>
    </p>

    <p style="text-align:center; margin-bottom:20px;">
        <b>Mobile:</b> {{general()->mobile}},
        <b>Email:</b> {{general()->email}} <br>
        <b>Address:</b> {{general()->address_one}}
    </p>

    <!-- TEXT -->
    <div>
        <h2 style="margin:0 0 10px;">Dear Sir,</h2>

        <p>
            Welcome to the {{general()->title}}! Here’s your <b>Daily ERP Report</b>.
        </p>

        <h3 style="margin:15px 0;">
            Today Reports – {{Carbon\Carbon::now()->format('d M, Y')}}
        </h3>

        <!-- ACCOUNTS LOOP -->
        @foreach($datas['accounts'] as $method)

        <span style="display:inline-block; padding:4px 25px; border:1px solid #e3cfcf; border-radius:5px; background:#fbfbfb; margin-bottom:10px;">
            {{$method->name}} Statement
        </span>

        <!-- FIXED TABLE WRAPPER -->
        <div style="width:100%; overflow-x:auto; margin-bottom:20px;">
            <table style="width:1000px; border-collapse:collapse; border:1px solid #dddddd;">

                <thead>
                    <tr>
                        <th style="border:1px solid #dddddd; padding:8px; background:#f7f7f7;">Date</th>
                        <th style="border:1px solid #dddddd; padding:8px; background:#f7f7f7;">Reference</th>
                        <th style="border:1px solid #dddddd; padding:8px; background:#f7f7f7;">Particulars</th>
                        <th style="border:1px solid #dddddd; padding:8px; background:#f7f7f7;">Debit</th>
                        <th style="border:1px solid #dddddd; padding:8px; background:#f7f7f7;">Credit</th>
                        <th style="border:1px solid #dddddd; padding:8px; background:#f7f7f7;">Balance</th>
                        <th style="border:1px solid #dddddd; padding:8px; background:#f7f7f7;">Accounts</th>
                    </tr>
                </thead>

                <tbody>

                    <!-- OPENING BALANCE -->
                    <tr>
                        <td style="border:1px solid #dddddd; padding:8px;"></td>
                        <td style="border:1px solid #dddddd; padding:8px;"></td>
                        <td style="border:1px solid #dddddd; padding:8px;"><b>Opening Balance</b></td>
                        <td style="border:1px solid #dddddd; padding:8px;"></td>
                        <td style="border:1px solid #dddddd; padding:8px;"></td>
                        <td style="border:1px solid #dddddd; padding:8px;">{{ priceFormat($method->opening_balance) }}</td>
                        <td style="border:1px solid #dddddd; padding:8px;">{{ $method->name }}</td>
                    </tr>

                    <!-- TRANSACTIONS -->
                    @forelse($method->transactions as $tran)
                    <tr>
                        <td style="border:1px solid #dddddd; padding:8px;">
                            {{ $tran->created_at->format('d-m-Y') }}
                        </td>

                        <td style="border:1px solid #dddddd; padding:8px;">
                            @if($tran->type==0) Sales
                            @elseif($tran->type==1) Deposit
                            @elseif($tran->type==3) Supplier Bill
                            @elseif($tran->type==4) Transfer
                            @elseif($tran->type==5) Expense
                            @elseif($tran->type==6) Withdrawal
                            @elseif($tran->type==7) I.O.U
                            @else Unknown @endif
                        </td>

                        <td style="border:1px solid #dddddd; padding:8px; word-break:break-word;">
                            @if($tran->type == 0)
                                {{ $tran->sale->name ?? '' }}
                                {{ $tran->billing_note ? '- '.$tran->billing_note : '' }}

                            @elseif($tran->type==1 || $tran->type==6)
                                <b>TNX ID:</b> {{ $tran->transection_id }} -
                                <b>Account:</b> {{ $tran->account->name ?? 'N/A' }}
                                {{ $tran->billing_note ? '- '.$tran->billing_note : '' }}

                            @elseif($tran->type==5 && $tran->expense)
                                <b>Company:</b> {{ $tran->expense->company_name }} -
                                <b>Receiver:</b> {{ $tran->expense->receiver_name }}
                                {{ $tran->expense->description ? '- '.$tran->expense->description : '' }}

                            @elseif($tran->type==7 && $tran->expenseIou)
                                <b>Company:</b> {{ $tran->expenseIou->company_name }} -
                                <b>Receiver:</b> {{ $tran->expenseIou->receiver_name }}
                                {{ $tran->expenseIou->description ? '- '.$tran->expenseIou->description : '' }}

                            @elseif($tran->type==3 && $tran->purchase)
                                <b>Invoice:</b> {{ $tran->purchase->order_no }} -
                                <b>Supplier:</b> {{ $tran->purchase->supplier_name }}

                            @else
                                {{ $tran->transection_id }}
                            @endif
                        </td>

                        <td style="border:1px solid #dddddd; padding:8px;">
                            @if(in_array($tran->type,[3,4,5,6,7]))
                                {{ priceFormat($tran->amount) }}
                            @endif
                        </td>

                        <td style="border:1px solid #dddddd; padding:8px;">
                            @if(in_array($tran->type,[0,1]))
                                {{ priceFormat($tran->amount) }}
                            @endif
                        </td>

                        <td style="border:1px solid #dddddd; padding:8px;">
                            {{ priceFormat($tran->running_balance) }}
                        </td>

                        <td style="border:1px solid #dddddd; padding:8px;">
                            {{ $method->name }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="border:1px solid #dddddd; padding:8px; text-align:center;">
                            No Record
                        </td>
                    </tr>
                    @endforelse

                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="4" style="border:1px solid #dddddd; padding:8px;"></td>
                        <td style="border:1px solid #dddddd; padding:8px;">Available</td>
                        <td style="border:1px solid #dddddd; padding:8px;">{{ priceFormat($method->available_balance) }}</td>
                        <td style="border:1px solid #dddddd; padding:8px;"></td>
                    </tr>
                </tfoot>

            </table>
        </div>

        @endforeach
    </div>

    <br><br>

    Best regards, <br>
    The {{general()->title}} Team <br>
    {{general()->website}}

</div>

<!-- FOOTER -->
<div style="padding:10px; margin:10px 0; border-top:1px solid #ededed; text-align:center;">
    You received this email as a registered user of
    <a href="{{route('index')}}">{{general()->website}}</a>.  
    <a href="{{route('index')}}">Unsubscribe</a>
</div>

</body>
</html>
