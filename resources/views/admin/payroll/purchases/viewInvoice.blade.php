@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Purchase Invoice View')}}</title>
@endsection @push('css')
<style type="text/css">
    
    
</style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Purchase Invoice</h3>
         <div class="dropdown">
             <a href="{{route('admin.purchases')}}" class="btn-custom primary" style="padding:5px 15px;">
                 <i class="bx bx-left-arrow-alt"></i> Back List
             </a>
              @isset(json_decode(Auth::user()->permission->permission, true)['purchases']['add'])
             <a href="{{route('admin.purchasesAction',['edit',$invoice->id])}}" class="btn-custom success" style="padding:5px 15px;">
                 <i class="bx bx-edit"></i> Edit
             </a>
             @endisset
             <a href="javascript:void(0)" id="PrintAction22" class="btn-custom yellow">
                 <i class="bx bx-printer"></i> Print
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        
                <div class="invoice-inner invoicePage PrintAreaContact">
                    <style>
                        table {
                            border-collapse: collapse;
                        }
                        .demo-info {
                            text-align: center;
                        }
                        .invoice-products table tr th {
                            padding: 5px;
                            text-align: center;
                        }
                        .invoice-products table tr td {
                            padding: 2px 5px;
                            text-align: center;
                        }
                        .table-bordered td, .table-bordered th {
                            border: 1px solid #dee2e6;
                        }
                        .terms-details p {
                            margin: 0;
                            font-size: 12px;
                            font-weight: bold;
                        }
                        .invoice-info {
                            text-align: right;
                        }
                        .footer-part{
                            /*border-bottom: 10px solid gray;*/
                            margin-top: 20px;
                            text-align:center;
                        }
                        .footer-part p {
                            font-size: 12px;
                        }
                        .footer-part p i.bx {
                            font-weight: bold;
                        }
                    </style>
                        <div class="row">
                            <div class="col-12">
                                <div class="demo-info">
                                    <img src="{{asset(general()->logo())}}" alt="company-logo" style="max-width:250px;max-height: 60px;">
                                    <h6 style="border-top: 1px solid #cccccc;padding-top: 5px;margin:0;">PURCHASE INVOICE</h6>
                                </div>
                            </div>
                            <div class="col-4">
                                <b>Purchase NO:</b>{{$invoice->invoice}}
                                <br><b>Name:</b> {{$invoice->name}}
                                <br><b>Mobile/Email:</b> {{$invoice->mobile?:$invoice->email}}
                                <br><b>Address: </b> {{$invoice->address}}
                            </div>
                            <div class="col-4"></div>
                            <div class="col-4">
                                <div class="invoice-info">
                                    <b>Purchase Date</b>: {{$invoice->created_at->format('d.m.Y')}}<br>
                                    <b>Purchase Status</b>: {{ucfirst($invoice->order_status)}}<br>
                                    <b>Payment Status</b>: {{ucfirst($invoice->payment_status)}}<br>
                                </div>
                            </div>
                        </div>
                        
                        <div class="invoice-products">
                            <br>
                            <table class="table table-bordered" style="width: 100% !important;">
                              <thead>
                                <tr>
                                  <th style="width: 60px;min-width: 60px;text-align: center;">SL.</th>
                                  <!--<th style="width: 150px;min-width: 150px;text-align: center;">REF</th>-->
                                  <th style="min-width: 200px;text-align: left;">DESCRIPTION OF GOODS</th>
                                  <th style="width: 100px;min-width: 100px;text-align: center;">QUANTITY</th>
                                  <th style="width: 120px;min-width: 120px;text-align: center;">PRICE</th>
                                  <th style="width: 150px;min-width: 150px;text-align: center;">TOTAL PRICE</th>
                                </tr>
                              </thead>
                              <tbody>
                              	@foreach($invoice->items as $i=>$item)
                                    <tr>
                                      <td>{{$i+1}}</td>
                                      <!--<td>{{$item->product_name}}</td>-->
                                      <td style="text-align: left;">{{$item->description}}</td>
                                      <td>{{$item->quantity}} {{$item->unit}}</td>
                                      <td>{{$item->itemPrice()}}</td>
                                      <td>{{$item->final_price}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                	<td></td>
                                	<td></td>
                                	<td></td>
                                	<th style="text-align: center;font-size:16px;">Total Amount</th>
                                	<th style="text-align: center;font-size:16px;">{{$invoice->total_price}}</th>
                                </tr>
                                <tr>
                                	<td></td>
                                	<td></td>
                                	<td></td>
                                	<th style="text-align: center;font-size:16px;">Paid Amount</th>
                                	<th style="text-align: center;font-size:16px;">{{$invoice->paid_amount}}</th>
                                </tr>
                                <tr>
                                	<td></td>
                                	<td></td>
                                	<td></td>
                                	<th style="text-align: center;font-size:16px;">Due  Amount</th>
                                	<th style="text-align: center;font-size:16px;">{{$invoice->due_amount}}</th>
                                </tr>
                              </tbody>
                            </table>
                            
                            <div class="terms-details">
                                <span><b>NOTE:</b></span>
                                <br>
                                {!!$invoice->note!!}
                            </div>
                            
                            <div class="signature-part">
                                <div class="row">
                                    <div class="col-6"><br><br>
                                        ------------------<br>
                                        <span><b>Receiver’s Signature</b></span>
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