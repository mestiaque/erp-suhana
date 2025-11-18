@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Sale View')}}</title>
@endsection @push('css')
<style type="text/css">
       
</style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Sale View</h3>
         <div class="dropdown">
             <a href="{{route('admin.sales')}}" class="btn-custom primary" style="padding:5px 15px;">
                 <i class="bx bx-left-arrow-alt"></i> Back List
             </a>

             @isset(json_decode(Auth::user()->permission->permission, true)['sales']['add'])
             <a href="{{route('admin.salesAction',['edit',$invoice->id])}}" class="btn-custom success" style="padding:5px 15px;">
                 <i class="bx bx-edit"></i> Edit
             </a>
             
             @endisset
             @if($invoice->due_amount > 0)
             <a href="{{route('admin.billCollectionAction',['edit',$invoice->id])}}" class="btn-custom success" style="padding:5px 15px;">
                 <i class="bx bx-money"></i> Bill Collect 
             </a>
             @endif
             <a href="javascript:void(0)" id="PrintAction22" class="btn-custom yellow">
                 <i class="bx bx-printer"></i> Print
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        
        <div class="invoice-inner invoicePage PrintAreaContact">
            <style>
                .demo-info {
                    text-align: center;
                }
            
                .invoice-products table tr th {
                    padding: 5px;
                    background: #f2f2f2 !important;
                    font-size: 12px;
                }
                .invoice-products table tr td {
                    padding: 2px 5px;
                    text-align: center;
                    word-break: break-word;
                }
                .terms-details{
                    font-size: 14px;
                }
                .terms-details p {
                    margin: 0;
                    font-size: 11px;
                    font-family: times new romance;
                }
                .invoice-info {
                    text-align: right;
                }
                .footer-part{
                    border-bottom: 10px solid gray;
                    margin-top: 20px;
                    text-align:center;
                }
                .footer-part p {
                    font-size: 11px;
                }
                .footer-part p i.bx {
                    font-weight: bold;
                }
                
                @media print {
                    .table{
                        border-collapse: collapse !important;
                        margin-bottom: 15px;
                    }
                    .table-bordered td, .table-bordered th {
                        border: 1px solid #dee2e6 !important;
                    }
                    .footer-part p {
                        font-size: 10px !important;
                    }
                }
                    
            </style>
            
            
            
            
            <style>
                .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px 50px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
             position: relative;
            overflow: hidden;
        }

        .paid-ribbon {
            position: absolute;
            top: 40px;
            right: -71px;
            background: linear-gradient(135deg, #20df27 0%, #31b83f 100%);
            color: white;
            font-size: 26px;
            font-weight: bold;
            letter-spacing: 5px;
            padding: 15px 109px;
            transform: rotate(45deg);
            box-shadow: 0 4px 12px rgba(139, 195, 74, 0.4);
            z-index: 10;
            text-align: center;
            border-top: 3px solid #067e28;
            border-bottom: 3px solid #067e28;
        }
        .iHeader {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .company-header {
            margin-bottom: 0px;
            z-index: 9999;
            text-align: right;
            margin-top: 135px;
        }

        .company-header p {
            margin: 3px 0;
            font-size: 14px;
            color: #000;
            line-height: 15px;
        }
        
        .inviceTitleId {
            background: #eee;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .invoice-dates p {
            margin: 0;
            font-size: 14px;
            color: #000;
        }

        .invoiced-to-title {
            font-weight: bold;
            margin: 15px 0 0 0;
            font-size: 14px;
            color: #000;
        }

        .client-details {
            margin-bottom: 20px;
            font-size: 14px;
            color: #000;
        }

        .client-details p {
            margin: 3px 0;
            line-height: 16px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table thead {
            background: #f8f8f8;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }

        .items-table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
            color: #000;
        }

        .items-table th:last-child {
            text-align: right;
        }

        .items-table td {
            padding: 10px;
            font-size: 14px;
            color: #000;
            border-bottom: 1px solid #eee;
        }

        .items-table td:last-child {
            text-align: right;
        }

        .totals-section {
            margin-top: 20px;
            border-top: 1px solid #ddd;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            font-size: 14px;
            color: #000;
        }

        .total-row:last-child {
            font-weight: bold;
            background: #f8f8f8;
            margin-top: 5px;
            border: 1px solid #ddd;
        }

        .transactions-title {
            font-weight: bold;
            margin: 20px 0 10px 0;
            font-size: 14px;
            color: #000;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
        }

        .transactions-table thead {
            background: #f8f8f8;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }

        .transactions-table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
            color: #000;
        }

        .transactions-table th:last-child {
            text-align: right;
        }

        .transactions-table td {
            padding: 10px;
            font-size: 13px;
            color: #333;
            border-bottom: 1px solid #eee;
        }

        .transactions-table td:last-child {
            text-align: right;
        }

        .balance-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: bold;
            background: #f8f8f8;
            border: 1px solid #ddd;
            margin-top: 10px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #666;
            text-align: center;
        }

        @media print {
            .paid-ribbon {
            position: absolute;
            top: 40px;
            right: -71px;
            background: linear-gradient(135deg, #20df27 0%, #31b83f 100%);
            color: white;
            font-size: 26px;
            font-weight: bold;
            letter-spacing: 5px;
            padding: 15px 109px;
            transform: rotate(45deg);
            box-shadow: 0 4px 12px rgba(139, 195, 74, 0.4);
            z-index: 10;
            text-align: center;
            border-top: 3px solid #067e28;
            border-bottom: 3px solid #067e28;
        }
            body {
                background: white;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                padding: 20px;
            }
        }
    
            </style>
            
                <!--new invoice start-->
              <div class="invoice-container">
                    <div class="paid-ribbon">PAID</div>
                
                    <div class="iHeader">
                        <div>
                            <img src="{{asset(general()->logo())}}" alt="company-logo" style="max-width: 350px; max-height: 100px" />
                        </div>
                        <div class="company-header">
                            <p>House: 7</p>
                            <p>Road: 14/C, Sector: 4,</p>
                            <p>Uttara-1230 Dhaka, Bangladesh</p>
                            <p>Phone: +8801628 092045</p>
                            <p>Web: www.natoreit.com</p>
                        </div>
                    </div>
                
                    <div class="inviceTitleId">
                        <div class="invoice-title">Invoice #5262</div>
                
                        <div class="invoice-dates">
                            <p><b>Invoice Date:</b> Monday, November 3rd, 2025</p>
                            <p><b>Due Date:</b> Monday, November 3rd, 2025</p>
                        </div>
                    </div>
                
                    <div class="invoiced-to-title">Invoiced To</div>
                    <div class="client-details">
                        <p>TexTownSourcing</p>
                        <p>ATTN: textownsourcing textownsourcing</p>
                        <p>House 32, Road 02, Rahim Tower,</p>
                        <p>Arab Ali Member Road, Uttara 15, Dhaka,</p>
                        <p>Dhaka, Dhaka, 1230</p>
                        <p>Bangladesh</p>
                    </div>
                
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Business Pro Hosting - textownsourcing.com (03/11/2025 - 02/11/2026)</td>
                                <td>$25.00 USD</td>
                            </tr>
                            <tr>
                                <td>Domain Registration - textownsourcing.com - 1 Year/s (03/11/2025 - 02/11/2026)</td>
                                <td>$11.66 USD</td>
                            </tr>
                        </tbody>
                    </table>
                
                    <div class="totals-section">
                        <div class="total-row">
                            <span>Sub Total</span>
                            <span>$36.66 USD</span>
                        </div>
                        <div class="total-row">
                            <span>Credit</span>
                            <span>$0.00 USD</span>
                        </div>
                        <div class="total-row">
                            <span>Total</span>
                            <span>$36.66 USD</span>
                        </div>
                    </div>
                
                    <div class="transactions-title">Transactions</div>
                    <table class="transactions-table">
                        <thead>
                            <tr>
                                <th>Transaction Date</th>
                                <th>Gateway</th>
                                <th>Transaction ID</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Tuesday, November 4th, 2025</td>
                                <td>bKash | Nagad|Credit Card | Online Banking</td>
                                <td>-</td>
                                <td>$36.66 USD</td>
                            </tr>
                        </tbody>
                    </table>
                
                    <div class="balance-row">
                        <span>Balance</span>
                        <span>$0.00 USD</span>
                    </div>
                
                    <div class="footer">
                        <p>PDF Generated on Tuesday, November 5th, 2025</p>
                    </div>
                </div>

                
                <!--new invoice end-->
            
            


            
                {{--<div class="demo-info">
                    <img src="{{asset(general()->logo())}}" alt="company-logo" style="max-width:400px;max-height: 100px;">
                    <h6 style="font-size: 20px;border-top: 1px solid #cccccc;padding-top: 5px;margin:0;font-weight: bold;">PROFORMA INVOICE</h6>
                </div>--}}
                {{--<div class="row">
                    <div class="col-12">
                        
                    </div>
                    <div class="col-4">
                            @if($invoice->company)
                             <b>{{$invoice->company->factory_name}}</b> / {{$invoice->company->owner_name}}<br>
                            {{$invoice->company->company_address}}
                            </br><b>Mobile:</b> {{$invoice->mobile}}
                            </br><b>Email:</b> {{$invoice->email}}
                            @endif
                    </div>
                    <div class="col-4"></div>
                    <div class="col-4">
                        <div class="invoice-info">
                            <b>Date</b>: <span style="width: 150px;display: inline-block;text-align: left;"> {{$invoice->created_at->format('d.m.Y')}} </span><br>
                            <b>Invoice No</b>: <span style="width: 150px;display: inline-block;text-align: left;">{{$invoice->invoice}} </span><br>
                            <b>Payment Status</b>:
                            <span style="width: 150px;display: inline-block;text-align: left;">
                            @if($invoice->payment_status=='paid')
                            <span style="color: #0cd836;font-weight: bold;">{{ucfirst($invoice->payment_status)}}</span>
                            @elseif($invoice->payment_status=='partial')
                            <span style="color: #ffc310;font-weight: bold;">{{ucfirst($invoice->payment_status)}}</span>
                            @else
                            <span style="color: #e1000a;font-weight: bold;">{{ucfirst($invoice->payment_status)}}</span>
                            @endif
                            </span>
                        </div>
                    </div>
                </div>--}}
                {{--<br>--}}
                {{--<div class="invoice-products">
                    <table class="table table-bordered" style="width: 100% !important;">
                      <thead>
                        <tr>
                          <th style="width: 60px;min-width: 60px;text-align: center;">SL.</th>
                          <th style="min-width: 200px;text-align: left;">DESCRIPTION OF GOODS</th>
                          <th style="width: 80px;min-width: 80px;text-align: center;">QUANTITY</th>
                          <th style="width: 150px;min-width: 150px;text-align: center;">PRICE</th>
                          <th style="width: 160px;min-width: 160px;text-align: center;">TOTAL PRICE</th>
                        </tr>
                      </thead>
                      <tbody>
                      	@foreach($invoice->items as $i=>$item)
                            <tr>
                              <td>{{$i+1}}</td>
                              <td style="text-align: left;">{{$item->description}}</td>
                              <td>{{$item->quantity}} {{$item->unit}}</td>
                              <td>{{$invoice->currency}} {{priceFormat($item->itemPrice())}}
                              </td>
                              <td>{{$invoice->currency}} {{priceFormat($item->final_price)}}</td>
                            </tr>
                        @endforeach
                        <tr>
                        	<td></td>
                        	<td></td>
                        	<td></td>
                        	<th style="text-align: center;font-size:16px;">Subtotal</th>
                        	<th style="text-align: center;font-size:16px;">{{$invoice->currency}} {{priceFormat($invoice->total_price)}}</th>
                        </tr>
                        <tr>
                        	<td></td>
                        	<td></td>
                        	<td></td>
                        	<th style="text-align: center;font-size:16px;">Paid</th>
                        	<th style="text-align: center;font-size:16px;">{{$invoice->currency}} {{priceFormat($invoice->paid_amount)}}</th>
                        </tr>
                        <tr>
                        	<td></td>
                        	<td></td>
                        	<td></td>
                        	<th style="text-align: center;font-size:16px;">Due</th>
                        	<th style="text-align: center;font-size:16px;">{{$invoice->currency}} {{priceFormat($invoice->due_amount)}}</th>
                        </tr>
                      </tbody>
                    </table>
                    
                    <div class="billingInfo" style="max-width: 800px;">
                        <h3>Billing</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>SL</th>
                                    <th>Title</th>
                                    <th style="text-align: center;">Amount</th>
                                    <th style="text-align: center;">Date</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                                @php
                                    $serial = 1;
                                @endphp
                                @if($invoice->emi_status)
                                    
                                    @foreach($invoice->transectionsAll()->whereNot('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->get() as $l=>$installment)
                                    <tr>
                                        <td>{{$serial++}}</td>
                                        <td style="text-align: left;">Down payment</td>
                                        <td>{{$installment->currency}} {{number_format($installment->amount,2)}}</td>
                                        <td>
                                            {{$installment->created_at->format('d.m.Y')}}
                                        </td>
                                        <td>
                                            @if($installment->status=='pending')
                                            Due
                                            @else
                                            Paid
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @foreach($invoice->transectionsAll()->where('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->get() as $l=>$installment)
                                    <tr>
                                        <td>{{$serial++}}</td>
                                        <td style="text-align: left;">{{serial($l+1)}} Installment</td>
                                        <td>{{$installment->currency}} {{number_format($installment->amount,2)}}</td>
                                        <td>
                                            {{$installment->created_at->format('d.m.Y')}}
                                        </td>
                                        <td>
                                            @if($installment->status=='pending')
                                            Due
                                            @else
                                            Paid
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                
                                @else
                                    @foreach($invoice->transectionsSuccess()->get() as $transection)
                                    <tr>
                                        <td>{{$serial++}}</td>
                                        <td>{{$transection->billing_reason}}</td>
                                        <td>{{$transection->currency}} {{number_format($transection->amount)}}</td>
                                        <td>{{$transection->created_at->format('d.m.Y')}}</td>
                                        <td>
                                            @if($transection->status=='pending')
                                            Due
                                            @else
                                            Paid
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    <div class="terms-details" >
                        @if($invoice->remark)
                        <span><b>Remarks</b></span><br>
                        <p>
                        {!!$invoice->remark!!}    
                        </p>
                        @endif
                        @if($invoice->note)
                        <span><b>TERMS & CONDITIONS</b></span>
                        <br>
                        {!!$invoice->note!!}
                        @endif
                    </div>
                    
                    <div class="signature-part">
                        <div class="row">
                            <div class="col-6"><br><br>
                                ------------------<br>
                                <span style="font-size: 12px;"><b>RECEIVER'S SIGNATURE</b></span>
                            </div>
                            <div class="col-6" style="text-align: end;">
                                <img src="{{asset(general()->signature)}}" style="max-height:70px;"><br>
                                <span style="font-size: 12px;">
                                <b>FOR {{general()->title}}</b>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="footer-part">
                        <p>@if(general()->address_one)<i class="bx bx-map"></i> Office: {{general()->address_one}}, @endif @if(general()->mobile) <i class="bx bx-phone"></i> Phone: {{general()->mobile}} @endif @if(general()->email) <i class="bx bx-envelope"></i> {{general()->email}} @endif<br> @if(general()->address_two) <i class="bx bx-map"></i>Factory: {{general()->address_two}}, @endif @if(general()->mobile2) <i class="bx bx-phone"></i> Phone: {{general()->mobile2}} @endif @if(general()->email2)<i class="bx bx-envelope"></i> factory: {{general()->email2}} @endif @if(general()->website) <i class='bx bx-globe'></i> {{general()->website}} @endif</p>
                    </div>
                </div>--}}
            </div>
    </div>
</div>
</div>



@endsection 


@push('js') 

<script>
    $(document).ready(function(){
        $('#PrintAction22').on("click", function () {
            $('.PrintAreaContact').printThis({
              	importCSS: false,
              	loadCSS: "https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap-grid.min.css",
            });
        });
    });
</script>

@endpush