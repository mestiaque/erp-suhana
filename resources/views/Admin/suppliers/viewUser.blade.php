@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Supplier Profile')}}</title>
@endsection

@push('css')
<style type="text/css">
    .showPassword {
    right: 0 !important;
    cursor: pointer;
    }
    .ProfileImage{
        max-width: 64px;
        max-height: 64px;
    }
</style>
@endpush
@section('contents')

<div class="flex-grow-1">
    <!-- Breadcrumb Area -->
    <div class="breadcrumb-area">
        <h1>Profile</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item"><a href="{{route('admin.suppliers')}}">Supplier List</a></li>
            <li class="item">Profile</li>
        </ol>
    </div>

    @include(adminTheme().'alerts')

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                    <h4 class="card-title">Supplier Profile</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="min-width: 120px;width: 120px;" >Name</th>
                                    <td style="min-width: 200px;">{{$user->name}}</td>
                                </tr>
                                <tr>
                                    <th>Mobile</th>
                                    <td>{{$user->mobile}}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{$user->email}}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{$user->fullAddress()}}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($user->status)
                                        <span class="badge badge-success">Active </span>
                                        @else
                                        <span class="badge badge-danger">Inactive </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Join Date</th>
                                    <td>{{$user->created_at->format('d M, Y')}}</td>
                                </tr>
                                <tr>
                                    <th>Total Sale</th>
                                    <td>{{priceFullFormat($user->orders->where('order_status','delivered')->whereIn('order_type',['purchase_order'])->sum('grand_total'))}}</td>
                                </tr>
                                <tr>
                                    <th>Total Due</th>
                                    <td>{{priceFullFormat($user->orders->where('order_status','delivered')->whereIn('order_type',['purchase_order'])->sum('due_amount'))}}
                                        
                                        <a href="{{route('admin.usersCustomerAction',['view',$user->id,'payment_status'=>'due'])}}"><i class="fas fa-external-link-alt"></i></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                    <h4 class="card-title">Purchases List</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr>
                                        <th style="width:60px;min-width:60px;" >SL</th>
                                        <th style="width:120px;min-width:120px;">Invoice</th>
                                        <th style="min-width:200px;">Customer</th>
                                        <th style="width:200px;min-width:200px;">Total Bill</th>
                                        <th style="width:200px;min-width:150px;">Due Bill</th>
                                        <th style="width:130px;min-width:130px;">Date</th>
                                        <th style="width:100px;min-width:100px;">Status</th>
        
                                        <th style="width:80px;min-width:80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $i=>$order)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td>
                                            <a href="{{route('admin.purchasesAction',['invoice',$order->id])}}">
                                            {{$order->invoice}}</a>
                                        </td>
                                        <td>{{$order->company_name}}
                                        @if($order->company_name && $order->name)
                                        - 
                                        @endif
                                        {{$order->name}}
                                        </td>
                                        <td>
                                            {{priceFullFormat($order->grand_total)}}
                                        </td>
                                        <td>
                                            {{priceFullFormat($order->due_amount)}}
                                        </td>
                                        <td>{{$order->created_at->format('d M, Y')}}</td>
                                        <td>
                                            @if($order->order_status=='confirmed')
                                            <span class="badge badge-success" style="background:#e91e63;">{{ucfirst($order->order_status)}}</span>
                                            @elseif($order->order_status=='shipped')
                                            <span class="badge badge-success" style="background:#673ab7;">{{ucfirst($order->order_status)}}</span>
                                            @elseif($order->order_status=='delivered')
                                            <span class="badge badge-success" style="background:#1c84c6;">{{ucfirst($order->order_status)}}</span>
                                            @elseif($order->order_status=='cancelled')
                                            <span class="badge badge-success" style="background:#f44336;">{{ucfirst($order->order_status)}}</span>
                                            @else
                                            <span class="badge badge-success" style="background:#ff9800;">{{ucfirst($order->order_status)}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{route('admin.purchasesAction',['view',$order->id])}}" class="btn btn-sm btn-success">Manage</a>
                                        </td>
                                    </tr>
                                    
                                    @endforeach
                                    @if($orders->count()==0)
                                    <tr><td colspan="8" style="text-align:center;">No Order Found</td></tr>
                                    @endif
                                </tbody>
        
                            </table>
        
                            {{$orders->links('pagination')}}
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')



@endpush