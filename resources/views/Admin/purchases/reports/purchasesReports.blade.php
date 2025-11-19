@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Purchases Reports')}}</title>
@endsection

@push('css')
<style type="text/css"></style>
@endpush

@section('contents')

<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Purchases Reports</h3>
             <div class="dropdown">
                 <a href="javascript:void(0)" class="btn-custom primary" style="padding:5px 15px;" id="PrintAction" >
                     <i class="fa fa-print"></i> Print
                 </a>
                 <a href="{{route('admin.purchasesReports')}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{route('admin.purchasesReports')}}">
                <div class="row">
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control" />
                            <input type="date" name="endDate" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Requisition, Department" class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>
            <br>



            <div class="printableArea PrintAreaContact">
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
                        <b>Report Date:</b>
                        {{ date('d M, Y') }}
                    </p>
                </div>
                <div class="table-responsive">

                    @foreach($orders as $order)
                    <span>
                        <b>Invoice:</b><a href="{{ route('admin.purchasesOrdersAction',['view',$order->id]) }}" target="_blank">{{$order->order_no}}</a>
                        <b>Date:</b> {{$order->created_at?->format('d-m-Y')}}
                        <b>Supplier:</b> {{$order->supplier->name}} {{$order->supplier->company_name?'- '.$order->supplier->company_name:''}}   

                        <span style="float:right;">
                            <b>Creaqted By:</b> {{$order->user?$order->user->name:'-'}}
                        </span>
                    </span>
                    <table class="table tableReport">
                        <thead>
                            <tr>
                                <th style="min-width: 50px;width:50px;">SL</th>
                                <th style="min-width: 150px;">Item name</th>
                                <th style="min-width: 100px;width:100px;text-align:right;">Qty</th>
                                <th style="min-width: 100px;width:100px;">Unit</th>
                                <th style="min-width: 150px;width:150px;">Price</th>
                                <th style="min-width: 150px;width:150px;text-align:right;">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $key=>$item)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $item->material_name }}</td>
                                    <td style="text-align:right">{{ numberFormat($item->qty,1)}}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td>{{ numberFormat($item->price,3) }}</td>
                                    <td style="text-align:right">{{ numberFormat($item->total_price,3) }}</td>
                                </tr>
                            @endforeach
                        
                            <tr>
                                <td></td>
                                <td><b>Total</b></td>
                                <td style="text-align:right"><b>{{numberFormat($order->total_qty,1)}}</b></td>
                                <td></td>
                                <td></td>
                                <td style="text-align:right"><b>{{numberFormat($order->grand_total,3)}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                    @endforeach

                    <div>
                        <p style="font-size: 24px;text-align: center;">
                            <b>Total Qty:</b> {{number_format($orders->sum('total_qty'))}}
                            <b>Total Amount:</b> {{number_format($orders->sum('grand_total'),2)}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
@endpush

