@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Invoice View')}}</title>
@endsection @push('css')
<style type="text/css">
    
    
</style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Invoice View</h3>
         <div class="dropdown">
             <a href="{{route('admin.piInvoices')}}" class="btn-custom primary" style="padding:5px 15px;">
                 <i class="bx bx-left-arrow-alt"></i> Back List
             </a>
             @if($invoice->hasLcOrders->count()==0)
             @isset(json_decode(Auth::user()->permission->permission, true)['pi']['add'])
             <a href="{{route('admin.piInvoicesAction',['edit',$invoice->id])}}" class="btn-custom success" style="padding:5px 15px;">
                 <i class="bx bx-edit"></i> Edit
             </a>
             @endisset
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
                    
                    <div class="demo-info">
                            <img src="{{asset(general()->logo())}}" alt="company-logo" style="max-width:400px;max-height: 100px;">
                            <h6 style="font-size: 20px;border-top: 1px solid #cccccc;padding-top: 5px;margin:0;font-weight: bold;">PROFORMA INVOICE</h6>
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
                        <br>
                        <div class="invoice-products">
                            <table class="table table-bordered" style="width: 100% !important;">
                              <thead>
                                <tr>
                                  <th style="width: 60px;min-width: 60px;text-align: center;">SL.</th>
                                  <th style="width: 150px;min-width: 150px;text-align: center;">REF</th>
                                  <th style="width: 200px;min-width: 200px;text-align: center;">DESCRIPTION OF GOODS</th>
                                  <th style="width: 80px;min-width: 80px;text-align: center;">QUANTITY</th>
                                  <th style="width: 100px;min-width: 100px;text-align: center;">UNIT</th>
                                  <th style="width: 100px;min-width: 100px;text-align: center;">PRICE</th>
                                  <th style="width: 120px;min-width: 120px;text-align: center;">TOTAL PRICE</th>
                                </tr>
                              </thead>
                              <tbody>
                              	@foreach($invoice->items as $i=>$item)
                                    <tr>
                                      <td>{{$i+1}}</td>
                                      <td>{{$item->product_name}}</td>
                                      <td>{{$item->description}}</td>
                                      <td>{{$item->quantity}}</td>
                                      <td>{{$item->unit}}</td>
                                      <td>${{$item->itemPrice()}}</td>
                                      <td>${{$item->final_price}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                	<td></td>
                                	<td></td>
                                	<td></td>
                                	<td></td>
                                	<td></td>
                                	<th style="text-align: center;font-size:16px;">Subtotal</th>
                                	<th style="text-align: center;font-size:16px;">${{$invoice->total_price}}</th>
                                </tr>
                              </tbody>
                            </table>
                            
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