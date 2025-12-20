<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>{{websiteTitle('LC Invoices View')}}</title>
        <link rel="apple-touch-icon" href="{{asset(general()->favicon())}}" />
        <link rel="shortcut icon" type="image/x-icon" href="{{asset(general()->favicon())}}" />
        <meta name="robots" content="noindex,nofollow" />
        <meta name="viewport" content="width=device-width; initial-scale=1.0;" />

        <style type="text/css">
            
            html { 
                width: 100%;
            }
            
            div, p, a, li, td {
                -webkit-text-size-adjust: none;
            }
            
            body {
                width: 100%;
                height: 100%;
                background-color: #fff;
                margin: 0;
                padding: 0;
                background: #fff;
                -webkit-font-smoothing: antialiased;
                font-size: 15px;
                font-family: "Nunito", sans-serif;
            }
            .invoiceArea {
                padding: 0 10px;
            }
            .row {
                display: -ms-flexbox;
                display: flex;
                -ms-flex-wrap: wrap;
                flex-wrap: wrap;
                margin:0;
            }
            
            .col-12 {
                -ms-flex: 0 0 100%;
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .col-4 {
                -ms-flex: 0 0 33.333333%;
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }
            
            .col-12,.col-4{
                position: relative;
                width: 100%;
                padding:0;
            }
            .demo-info {
                text-align: center;
            }
            .footer-part {
                border-bottom: 10px solid gray;
                margin-top: 20px;
                text-align: center;
            }
            
            .table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            
            .table-bordered{
                border: 1px solid #dee2e6;
            }
            
            .table-bordered thead tr th {
                border: 1px solid #dee2e6;
                background: #0000000d;
                font-size:12px;
            }
            
            .table-bordered tr td {
                border: 1px solid #dee2e6;
                font-size:12px;
            }
            
            .table thead tr th {
                padding: 5px;
                text-align: center;
                
            }
            
            .table tr td {
                padding: 5px;
                text-align: center;
            }
            
          @media only screen and (max-width: 600px) {
            body {
                width: auto!important;
            }
            table[class=fullTable] {
                width: 96% !important;
                clear: both;
            }
            table[class=fullPadding] {
                width: 85% !important;
                clear: both;
            }
            table[class=col] {
                width: 45% !important;
            }
            
          }
        </style>
    </head>
    <body>
        
        <div class="invoiceArea">
            @foreach($invoices as $i=>$invoice)
            <div class="test">
                <table class="table">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <div class="demo-info">
                                    <img src="{{asset(general()->logo())}}" alt="company-logo" style="max-width:250px;max-height: 80px;">
                                    <h2 style="border-top: 1px solid #cccccc;padding-top: 5px;">LC INVOICE</h2>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:50%;text-align: left;">
                                @if($invoice->company)
                                 <b>{{$invoice->company->name}}</b> <br>
                                 {{$invoice->company->description}}
                                @endif
                                <p>
                                <b>ATTN:</b> 
                                
                                @if($invoice->marchantize)
                                    <span>{{$invoice->marchantize->name}}</span> 
                                @endif
                                
                                </p>
                            </td>
                            <td style="width:50%;text-align: right;">
                                <b>Date</b>: {{$invoice->created_at->format('d.m.Y')}}
                                <p><b>PI NO</b>: {{$invoice->invoice}}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="invoice-products">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 5%;min-width: 5%;">SL.</th>
                      <th style="width: 20%;min-width: 20%;">REF</th>
                      <th style="width: 20%;min-width: 28%;">DESCRIPTION OF GOODS</th>
                      <th style="width: 15%;min-width: 10%;">QUANTITY</th>
                      <th style="width: 15%;min-width: 12%;">UNIT PRICE</th>
                      <th style="width: 15%;min-width: 15%;">TOTAL PRICE</th>
                      <th style="width: 10%;min-width: 10%;">REMARKS</th>
                    </tr>
                  </thead>
                  <tbody>
                  	@foreach($invoice->items as $i=>$item)
                        <tr>
                          <td>{{$i+1}}</td>
                          <td>{{$item->product_name}}</td>
                          <td>{{$item->description}}</td>
                          <td>{{$item->quantity}}</td>
                          <td>{{priceFormat($item->price)}}</td>
                          <td>{{priceFormat($item->final_price)}}</td>
                          @if($i==0)
                          <td rowspan="{{$invoice->items->count()}}">sdflksd</td>
                          @endif
                        </tr>
                    @endforeach
                    <tr>
                    	<td></td>
                    	<td></td>
                    	<td></td>
                    	<td></td>
                    	<th style="text-align: center;">Subtotal</th>
                    	<th style="text-align: center;">{{priceFormat($invoice->grand_total)}}</th>
                    	<td></td>
                    </tr>
                  </tbody>
                </table>
                
                <div class="terms-details">
                    <span><b>NOTE:</b></span>
                    <br>
                    {!!$invoice->note!!}
                </div>
                
                <table class="table">
                    <tbody>
                        <tr>
                            <td style="width:33.33333%;text-align:left;">
                                <br><br><br>
                            ------------------<br>
                            <span><b>Receiver’s Signature</b></span>
                            </td>
                            <td style="width:33.33333%;">
                                
                            </td>
                            <td style="width:33.33333%;text-align:right;"> 
                                <img src="{{asset('public/medies/sign1.png')}}"><br>
                                <span><b>FOR ARTISAN INVENTION LTD</b></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="footer-part">
                    <p>@if(general()->address_one)<i class="fa fa-map-marker"></i> Office: {{general()->address_one}}, @endif @if(general()->mobile) <i class="fa fa-phone"></i> Phone: {{general()->mobile}} @endif @if(general()->email) <i class="fa fa-envelope"></i> {{general()->email}} @endif<br> @if(general()->address_two) <i class="fa fa-map-marker"></i>Factory: {{general()->address_two}}, @endif @if(general()->mobile2) <i class="fa fa-phone"></i> Phone: {{general()->mobile2}} @endif @if(general()->email2)<i class="fa fa-envelope"></i> factory: {{general()->email2}} @endif @if(general()->website) <i class='fa fa-globe'></i> {{general()->website}} @endif</p>
                </div>
            </div>
            @if($i < (count($invoices)-1))
            <div style="page-break-before:always">&nbsp;</div> 
            @endif
            @endforeach
        </div>
    
    
    </body>
   
</html>
