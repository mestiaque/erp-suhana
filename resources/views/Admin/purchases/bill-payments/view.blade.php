@extends(adminTheme().'layouts.app')

@section('title')
<title>Bill Payment - Order #{{ $purchase->order_no }}</title>
@endsection

@section('contents')

<div class="breadcrumb-area">
    <h1>Purchase Wise Bill Payment</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">
            <a href="{{ route('admin.billPayment') }}">Bill Payment</a>
        </li>
        <li class="item"> #{{ $purchase->order_no }} </li>
    </ol>
</div>

<div class="flex-grow-1">

    <div class="card mb-30">
        <div class="card-header mb-2">
            <h3>Bill Payment - Order #<a href="{{ route('admin.purchasesOrdersAction',['pay',$purchase->id]) }}">{{ $purchase->order_no }}</a></h3>
        </div>

        <div class="card-body">
            <div class="row">
                {{-- Payment History --}}
                <div class="col-md-8">
                    <div class="row mb-4">
                        <div class="col-md-6 d-flex">
                            <div class="card shadow-sm mb-3 flex-fill">
                                <div class="e">
                                    <h5 class="mb-0">Supplier Info</h5>
                                    <hr>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Company:</strong> {{ $purchase->company_name }}</li>
                                        <li><strong>Name:</strong> {{ $purchase->supplier_name }}</li>
                                        <li><strong>Email:</strong> {{ $purchase->supplier_email }}</li>
                                        <li><strong>Mobile:</strong> {{ $purchase->supplier_mobile }}</li>
                                        <li><strong>Address:</strong> {{ $purchase->supplier_address }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex">
                            <div class="card shadow-sm mb-3 flex-fill">
                                <div class="">
                                    <h5 class="mb-0">Purchase Info</h5>
                                    <hr>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Order Number:</strong> <span class="text-">{{ $purchase->order_no }}</span></li>
                                        <li><strong>Grand Total:</strong> <span class="text-primary">{{ number_format($purchase->grand_total,2) }}</span></li>
                                        <li><strong>Paid Amount:</strong> <span class="text-success">{{ number_format($purchase->paid_amount,2) }}</span></li>
                                        <li><strong>Due Amount:</strong> <span class="text-danger">{{ number_format($purchase->due_amount,2) }}</span></li>
                                        <li>
                                            <strong>Payment Status:</strong>
                                            <span class="badge bg-{{ $purchase->payment_status == 'paid' ? 'success' : ($purchase->payment_status == 'partial' ? 'warning' : 'danger') }} text-white">
                                                {{ ucfirst($purchase->payment_status) }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>


                    <h5>Payment History</h5>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Amount</th>
                                    <th>Paid At</th>
                                    <th>Note</th>
                                    <th>Account</th>
                                    <th>Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @forelse($purchase->payments as $i => $pay)
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ number_format($pay->pay_amount,2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($pay->payment_date)->format('d-m-Y') }}</td>
                                        <td>{{ $pay->note ?? 'N/A' }}</td>
                                        <td>{{ $pay->account?->name ?? 'N/A' }}</td>
                                        <td>{{ $pay->payment_method?->name ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No payment history found</td>
                                    </tr>
                                @endforelse --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Payment Form --}}
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Make Payment</h5>
                            <hr>
                            <form action="{{ route('admin.billPaymentAction',['save',$purchase->id]) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <label>Pay Amount</label>
                                    <input type="number" name="pay_amount" step="any" max="{{ $purchase->due_amount }}" class="form-control" required>
                                </div>

                                <div class="mb-2">
                                    <label>Payment Date</label>
                                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                                </div>

                                {{-- <div class="mb-2">
                                    <label>Select Account</label>
                                    <select name="account_id" class="form-control" required>
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}

                                <div class="mb-2">
                                    <label>Payment Method</label>
                                    <select name="payment_method_id" class="form-control" required>
                                        <option value="">Select Method</option>
                                        {{-- @foreach($paymentMethods as $method)
                                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label>Note</label>
                                    <input type="text" name="note" class="form-control">
                                </div>

                                <button type="submit" class="btn btn-success w-50 mt-2">Submit Payment</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@endsection
