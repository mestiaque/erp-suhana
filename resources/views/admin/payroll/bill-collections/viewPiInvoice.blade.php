@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Bill Collection View')}}</title>
@endsection @push('css')
<style type="text/css">
    
    
</style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Bill Collection View</h3>
         <div class="dropdown">
             <a href="{{route('admin.billCollection')}}" class="btn-custom primary" style="padding:5px 15px;">
                 <i class="bx bx-left-arrow-alt"></i> Back List
             </a>
             <a href="{{route('admin.billCollectionAction',['edit',$invoice->id])}}" class="btn-custom primary" style="padding:5px 15px;">
                 <i class="bx bx-edit"></i> Edit
             </a>
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
                    
                            <div class="demo-info">
                                <img src="{{asset(general()->logo())}}" alt="company-logo" style="max-width:400px;max-height: 100px;">
                                <h6 style="font-size: 20px;border-top: 1px solid #cccccc;padding-top: 5px;margin:0;font-weight: bold;">BILL COLLECTION</h6>
                            </div>
                        <div class="row">
                            <div class="col-12">
                                
                            </div>
                            <div class="col-4">
                                    @if($invoice->company)
                                     <b>{{$invoice->company->factory_name}}</b> / {{$invoice->company->owner_name}}<br>
                                    {{$invoice->company->company_address}}
                                    @endif
                            </div>
                            <div class="col-4"></div>
                            <div class="col-4">
                                <div class="invoice-info">
                                    <b>Date</b>: {{$invoice->created_at->format('d.m.Y')}}
                                    <p><b>Invoice No</b>: {{$invoice->invoice}}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="invoice-products">
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
                                            @php
                                                $downPayment =$invoice->grand_total - $invoice->transectionsAll()->where('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->sum('amount');
                                                $collectPayment =$invoice->transectionsAll()->where('billing_reason','not like','%Installment%')->whereIn('status',['success'])->sum('amount');
                                            @endphp
                                            @if($downPayment > 0)
                                            <tr>
                                                <td>{{$serial++}}</td>
                                                <td style="text-align: left;">Down payment</td>
                                                <td>{{$invoice->currency}} {{number_format($downPayment,2)}}</td>
                                                <td>
                                                    {{$invoice->created_at->format('d.m.Y')}}
                                                </td>
                                                <td>
                                                    @if($collectPayment >= $downPayment)
                                                    Paid
                                                    @elseif($collectPayment > 0)
                                                    Partial
                                                    @else
                                                    Due
                                                    @endif
                                                </td>
                                            </tr>
                                            @endif
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
                                        <b>FOR {{general()->title}}</b>
                                    </div>
                                </div>
                            </div>
                            <div class="footer-part">
                                <p>@if(general()->address_one)<i class="bx bx-map"></i> Office: {{general()->address_one}}, @endif @if(general()->mobile) <i class="bx bx-phone"></i> Phone: {{general()->mobile}} @endif @if(general()->email) <i class="bx bx-envelope"></i> {{general()->email}} @endif<br> @if(general()->address_two) <i class="bx bx-map"></i>Factory: {{general()->address_two}}, @endif @if(general()->mobile2) <i class="bx bx-phone"></i> Phone: {{general()->mobile2}} @endif @if(general()->email2)<i class="bx bx-envelope"></i> factory: {{general()->email2}} @endif @if(general()->website) <i class='bx bx-globe'></i> {{general()->website}} @endif</p>
                            </div>
                        </div>
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