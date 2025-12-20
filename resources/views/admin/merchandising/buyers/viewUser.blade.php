@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Buyer Profile')}}</title>
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
        <h1>Buyer Profile</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item"><a href="{{route('admin.buyers')}}">Buyer List</a></li>
            <li class="item">Profile</li>
        </ol>
    </div>

    @include(adminTheme().'alerts')

    <div class="row mb-30">

        <!-- Left Profile -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-3">

                    <div class="mb-3 position-relative d-inline-block">
                        <img src="{{ asset($user->image()) }}"
                             class="rounded-circle img-fluid"
                             alt="Profile" style="width: 6rem; height:6rem">

                        <span class="position-absolute bottom-0 end-0 translate-middle p-1 rounded-circle"
                            style="background-color: {{ $user->status ? '#28a745' : '#dc3545' }};
                                   width: 20px; height: 20px; border: 2px solid #fff;right:5px">
                        </span>
                    </div>

                    <h5 class="mb-1"><strong>{{$user->name}}</strong></h5>
                    @if($user->mobile)
                        <p class="mb-1"><i class="fas fa-phone me-1"></i> {{$user->mobile}}</p>
                    @endif
                    @if($user->email)
                        <p class="mb-1"><i class="fas fa-envelope me-1"></i> {{$user->email}}</p>
                    @endif
                    @if($user->country_text)
                        <p class="mb-3"><i class="fa-solid fa-flag me-1"></i> {{$user->country_text}}</p>
                    @endif
                    @if($user->company_name)
                        <p class="mb-3"><i class="fa-solid fa-building"></i> {{$user->company_name}}</p>
                    @endif

                    <!-- Info -->
                    <ul class="list-group list-group-flush text-start">
                        @if($user->fullAddress())
                        <li class="list-group-item py-1"><strong>Address:</strong> {{$user->fullAddress()}}</li>
                        @endif
                        <li class="list-group-item py-1"><strong>Start Date:</strong> {{$user->created_at->format('d M, Y')}}</li>

                        <li class="list-group-item py-1">
                            <strong>Total Orders:</strong>
                            <span class="text-success">{{ priceFullFormat($user?->sales?->sum('grand_total')) }}</span>
                        </li>

                        <li class="list-group-item py-1">
                            <strong>Total Received:</strong>
                            <span class="text-info">{{ priceFullFormat($user?->sales?->sum('paid_amount')) }}</span>
                        </li>

                        <li class="list-group-item py-1">
                            <strong>Total Due:</strong>
                            <span class="text-danger">{{ priceFullFormat($user?->sales?->sum('due_amount')) }}</span>
                        </li>
                    </ul>

                </div>
            </div>
        </div>

        <!-- Right Sales List -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header mb-3" style="border-bottom: 1px solid #e3ebf3;">
                    <h4 class="card-title">Orders List</h4>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Invoice No.</th>
                                    <th class="text-right">Total</th>
                                    <th class="text-right">Received</th>
                                    <th class="text-right">Due</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                {{-- @foreach($sales as $i=>$sale)
                                <tr>
                                    <td>{{$i+1}}</td>
                                    <td>
                                        <a href="{{route('admin.sales.view',$sale->id)}}">{{$sale->invoice_no}}</a>
                                    </td>

                                    <td class="text-right">{{ priceFullFormat($sale->grand_total) }}</td>
                                    <td class="text-right">{{ priceFullFormat($sale->paid_amount) }}</td>
                                    <td class="text-right">{{ priceFullFormat($sale->due_amount) }}</td>
                                    <td>{{ $sale->created_at->format('d M, Y') }}</td>

                                    <td>
                                        @if($sale->payment_status == 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @elseif($sale->payment_status == 'due')
                                            <span class="badge badge-danger">Due</span>
                                        @elseif($sale->payment_status == 'partial')
                                            <span class="badge badge-warning">Partial</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach

                                @if($sales->count()==0)
                                    <tr><td colspan="10" class="text-center">No Sales Found</td></tr>
                                @endif --}}
                            </tbody>
                        </table>

                        {{-- {{ $sales->appends(request()->except('sales_page'))->links('pagination') }} --}}
                    </div>

                </div>
            </div>
        </div>

    </div>


    <!-- Payment Section -->
    @php
        $totalDue = $user?->sales?->sum('due_amount');
    @endphp

    <div class="card mb-30 mt-30 p-0 d-none">
        <div class="card-body">

            <div class="px-3 py-2 bg-info text-white">
                <h5 class="m-0 text-white">Payment Transactions</h5>
            </div>

            <div class="row">

                <!-- Payment History -->
                <div class="{{ can('buyer.payment') ? 'col-md-8': 'col-md-12' }}">
                    <div class="card">
                        <div class="card-body">
                            <h5>Transaction History</h5>
                            <hr>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Transaction ID</th>
                                            <th>Invoice</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Note</th>
                                            <th>Account</th>
                                            <th>Method</th>
                                            <th>Attachment</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {{-- @forelse($transactions as $i=>$trans)
                                            <tr>
                                                <td>{{ $i+1 }}</td>
                                                <td>{{ $trans->transection_id }}</td>

                                                <td>
                                                    <a href="{{route('admin.sales.view',$trans->src_id)}}">
                                                        {{ $trans->sale?->invoice_no }}
                                                    </a>
                                                </td>

                                                <td class="text-right">{{ number_format($trans->amount,2) }}</td>
                                                <td>{{ $trans->created_at->format('d-m-Y') }}</td>
                                                <td>{{ $trans->billing_note ?? 'N/A' }}</td>
                                                <td>{{ $trans->account?->name ?? 'N/A' }}</td>
                                                <td>{{ $trans->payment_method ?? 'N/A' }}</td>

                                                <td class="text-center">
                                                    @if($trans->imageFile)
                                                        <a href="{{asset($trans->imageFile->file_url)}}" target="_blank">
                                                            <i class="bx bx-file"></i>
                                                        </a>
                                                    @else --
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">No payment history found</td>
                                            </tr>
                                        @endforelse --}}
                                    </tbody>

                                </table>
                                {{-- {{ $transactions->appends(request()->except('trans_page'))->links('pagination') }} --}}
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Receive Payment Form -->
                @can('buyer.payment')
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Receive Payment</h5>
                            <hr>

                            <form action="{{ route('admin.buyersAction',['payment',$buyer->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="buyer_id" value="{{ $buyer->id }}">

                                <div class="mb-2">
                                    <label>Receive Amount</label>
                                    <input type="number" placeholder="{{ $totalDue }}" name="amount" step="any" max="{{ $totalDue }}" class="form-control" required>
                                </div>

                                <div class="mb-2">
                                    <label>Select Account</label>
                                    <select name="account_id" class="form-control">
                                        <option value="">Select Account</option>
                                        @foreach($accountMethods as $acc)
                                            <option value="{{$acc->id}}">{{$acc->name}} - {{priceFormat($acc->amount)}}</option>
                                        @endforeach
                                    </select>
                                </div>

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
                                    <label>Attachment</label>
                                    <input type="file" name="attachment" class="form-control" accept="image/*,application/pdf">
                                </div>

                                <div class="mb-2">
                                    <label>Note</label>
                                    <textarea name="note" class="form-control" rows="1"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bx bx-plus"></i> Receive Payment
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
                @endcan

            </div>

        </div>
    </div>

</div>

@endsection
@push('js')

@endpush
