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

    <div class="row mb-30">
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
                                    <td>{{priceFullFormat($user->orders->where('status','approved')->sum('grand_total'))}}</td>
                                </tr>
                                <tr>
                                    <th>Total Due</th>
                                    <td>{{priceFullFormat($user->orders->where('status','approved')->sum('due_amount'))}}
                                        {{-- <a href="{{route('admin.usersCustomerAction',['view',$user->id,'payment_status'=>'due'])}}"><i class="fas fa-external-link-alt"></i></a> --}}
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
                                        <th style="width:120px;min-width:120px;">Purchase No.</th>
                                        <th style="min-width:200px;">Customer</th>
                                        <th style="width:200px;min-width:200px;">Total Bill</th>
                                        <th style="width:200px;min-width:200px;">Paid Bill</th>
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
                                            <a href="{{route('admin.purchasesOrdersAction',['view',$order->id])}}">
                                            {{$order->order_no}}</a>
                                        </td>
                                        <td>{{$order->company_name}}
                                        @if($order->company_name && $order->name)
                                        -
                                        @endif
                                        {{$order->name}}
                                        </td>
                                        <td class="text-right">
                                            {{priceFullFormat($order->grand_total)}}
                                        </td>
                                        <td class="text-right">
                                            {{priceFullFormat($order->paid_amount)}}
                                        </td>
                                        <td class="text-right">
                                            {{priceFullFormat($order->due_amount)}}
                                        </td>
                                        <td>{{$order->created_at->format('d M, Y')}}</td>
                                        <td>
                                            @if($order->payment_status == 'paid')
                                                <span class="badge badge-success" style="background:#4caf50;">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>

                                            @elseif($order->payment_status == 'due')
                                                <span class="badge badge-warning" style="background:#f44336;">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>

                                            @elseif($order->payment_status == 'partial')
                                                <span class="badge badge-info" style="background:#ff9800;">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>

                                            @else
                                                <span class="badge badge-secondary">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($order->can_pay == 1)
                                                <a href="{{ route('admin.billPaymentAction',['pay',$order->id]) }}" class="btn-custom success">
                                                    <i class="bx bx-money"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>

                                    @endforeach
                                    @if($orders->count()==0)
                                    <tr><td colspan="9" style="text-align:center;">No Order Found</td></tr>
                                    @endif
                                </tbody>

                            </table>

                            {{ $orders->appends(request()->except('orders_page'))->links('pagination') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card mb-30 mt-30 p-0">
        @php
            $totalDue = $user->orders->where('status','approved')->sum('due_amount');
        @endphp
        <div class="card-body">
            <div class=" px-3 py-2 m-0 bg-info text-white">
                <h5 class="text-white m-0">Payment Transactions</h5>
            </div>

            <div class="row">
                {{-- Payment History --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5>Transaction History</h5>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead >
                                        <tr>
                                            <th>#</th>
                                            <th>Transaction ID</th>
                                            <th>Amount</th>
                                            <th>Paid Date</th>
                                            <th>Note</th>
                                            {{-- <th>Account</th> --}}
                                            <th>Method</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $i => $trans)
                                            <tr>
                                                <td>{{ $i+1 }}</td>
                                                <td style="max-width: 5rem">{{ $trans->transection_id }}</td>
                                                <td class="text-right">{{ number_format($trans->amount,2) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($trans->created_at)->format('d-m-Y') }}</td>
                                                <td style="max-width: 10rem">{{ $trans->billing_note ?? 'N/A' }}</td>
                                                {{-- <td>{{ $trans->accountMethod?->name ?? 'N/A' }}</td> --}}
                                                <td>{{ $trans->payment_method ?? 'N/A' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No payment history found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                {{ $transactions->appends(request()->except('trans_page'))->links('pagination') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Form --}}
                <div class="col-md-4">
                    <div class="card shadow payment-form-card">
                        <div class="card-body">
                            <h5 class="form-title">Make Payment</h5>
                            <hr>
                            <form id="paymentForm" action="{{ route('admin.suppliersAction',['payment',$user->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" value="{{ $user->id }}" name="user_id" hidden readonly>
                                <div class="mb-2">
                                    <label>Pay Amount</label>
                                    <input type="number" placeholder="{{ $totalDue }}" name="pay_amount" step="any" max="{{ $totalDue }}" class="form-control" required>
                                </div>

                                {{-- <div class="mb-2">
                                    <label>Select Account</label>
                                    <select name="account_id" class="form-control" >
                                        <option value="">Select Account</option>
                                        @foreach($accountMethods as $acc)
                                            <option value="{{ $acc->id }}">{{$acc->name}} - BDT {{priceFormat($acc->amount)}}</option>
                                        @endforeach
                                    </select>
                                </div> --}}

                                <div class="mb-2">
                                    <label>Payment Method</label>
                                    <select name="payment_method_id" class="form-control" required>
                                        <option value="">Select Method</option>
                                        @foreach($paymentMethods as $method)
                                            <option value="{{$method->id}}">{{$method->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label>Note</label>
                                    <textarea name="note" placeholder="Write note here..." class="form-control" cols="30" rows="1"></textarea>
                                </div>

                                {{-- <button type="submit" class="btn btn-success w-50 mt-2">Submit Payment</button> --}}

                                <div class="d-flex gap-2 justify-content-end mt-2 w-100">
                                    <button type="button"  id="cancelEditBtn" class="btn grey btn-outline-secondary d-none mr-1">Close </button>
                                    <button type="submit" id="submitBtn" class="btn btn-primary"><i class="bx bx-plus"></i> Add Payment</button>
                                </div>
                            </form>
                        </div>
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
