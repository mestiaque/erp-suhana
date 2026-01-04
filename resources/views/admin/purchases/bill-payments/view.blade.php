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
            <h3>Bill Payment - Order #<a href="{{ route('admin.purchasesOrdersAction',['view',$purchase->id]) }}">{{ $purchase->order_no }}</a></h3>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')
            <div class="row">
                {{-- Payment History --}}
                <div class="{{ auth()->user()->hasPermission('bill_payments.add') ? 'col-md-8' : 'col-md-12' }}">
                    <div class="row mb-4">
                        <div class="col-md-6 d-flex">
                            <div class="card shadow-sm mb-3 flex-fill">
                                <div class="e">
                                    <h5 class="mb-0">Supplier Info</h5>
                                    <hr>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush text-start mb-0">
                                        <li class="list-group-item py-1"><strong>Company:</strong> {{ $purchase->company_name }}</li>
                                        <li class="list-group-item py-1"><strong>Name:</strong> {{ $purchase->supplier_name }}</li>
                                        <li class="list-group-item py-1"><strong>Email:</strong> {{ $purchase->supplier_email }}</li>
                                        <li class="list-group-item py-1"><strong>Mobile:</strong> {{ $purchase->supplier_mobile }}</li>
                                        <li class="list-group-item py-1"><strong>Address:</strong> {{ $purchase->supplier_address }}</li>
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
                                    <ul class="list-group list-group-flush text-start mb-0">
                                        <li class="list-group-item py-1"><strong>Order Number:</strong> <span class="text-">{{ $purchase->order_no }}</span></li>
                                        <li class="list-group-item py-1"><strong>Grand Total:</strong> <span class="text-primary">{{ number_format($purchase->grand_total,2) }}</span></li>
                                        <li class="list-group-item py-1"><strong>Paid Amount:</strong> <span class="text-success">{{ number_format($purchase->paid_amount,2) }}</span></li>
                                        <li class="list-group-item py-1"><strong>Due Amount:</strong> <span class="text-danger">{{ number_format($purchase->due_amount,2) }}</span></li>
                                        <li class="list-group-item py-1">
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
                            <thead >
                                <tr>
                                    <th>#</th>
                                    <th style="min-width: 140px;">Transaction ID</th>
                                    <th style="min-width: 110px;">Amount</th>
                                    <th style="min-width: 120px;">Paid Date</th>
                                    <th style="min-width: 150px;">Note</th>
                                    <th style="min-width: 150px;">Account</th>
                                    <th style="min-width: 150px;">Method</th>
                                    <th style="min-width: 110px;">Attachtment</th>
                                    <th style="min-width: 110px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $i => $trans)
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $trans->transection_id }}</td>
                                        <td class="text-right">{{ number_format($trans->amount,2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($trans->created_at)->format('d-m-Y') }}</td>
                                        <td style="max-width: 10rem">{{ $trans->billing_note ?? '--' }}</td>
                                        <td>{{ $trans->account?->name ?? '--' }}</td>
                                        <td>{{ $trans->payment_method ?? '--' }}</td>
                                        <td class="text-center">
                                            @if($trans->imageFile)
                                                <span style="border: 1px solid #dadada;display: inline-block;padding: 0px 10px;border-radius: 5px;">
                                                    <a href="{{asset($trans->imageFile->file_url)}}" target="_blank"><i class="bx bx-file"></i></a>
                                                    <a href="{{route('admin.mediesDelete',$trans->imageFile->id)}}" class="mediaDelete" style="padding-left: 5px;color: #dc3545;display: inline-block;border-left: 1px solid #d2d2d2;"><i class="bx bx-trash"></i></a>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="">
                                            <div class="text-center center">
                                                @if(auth()->user()->hasPermission('bill_payments.edit') || auth()->user()->hasPermission('bill_payments.delete'))
                                                    @can('bill_payments.edit')
                                                        <button
                                                            class="btn-custom success edit-btn"
                                                            data-id="{{ $trans->id }}"
                                                            data-amount="{{ $trans->amount }}"
                                                            data-date="{{ $trans->created_at }}"
                                                            data-account="{{ $trans->account_id }}"
                                                            data-method="{{ $trans->payment_method_id }}"
                                                            data-note="{{ $trans->billing_note }}"
                                                            >
                                                            <i class="bx bx-edit"></i>
                                                        </button>
                                                    @endcan
                                                    @can('bill_payments.delete')
                                                        <a href="{{route('admin.billPaymentAction',['delete',$trans->id])}}" onclick="return confirm('Are You Want To Delete')" class="btn-custom danger">
                                                            <i class="bx bx-trash"></i>
                                                        </a>
                                                    @endcan
                                                @else --  @endif
                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No payment history found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Payment Form --}}
                @can('bill_payments.add')
                <div class="col-md-4">
                    <div class="card shadow payment-form-card">
                        <div class="card-body">
                            <h5 class="form-title">Make Payment</h5>
                            <hr>
                            <form id="paymentForm" action="{{ route('admin.billPaymentAction',['save',$purchase->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $purchase->id }}" name="purchase_id" hidden readonly>
                                <div class="mb-2">
                                    <label>Pay Amount</label>
                                    <input type="number" placeholder="{{ $purchase->due_amount }}" name="pay_amount" step="any" max="{{ $purchase->due_amount }}" class="form-control" required>
                                </div>

                                <div class="mb-2">
                                    <label>Select Account</label>
                                    <select name="account_id" class="form-control" required>
                                        <option value="">Select Account</option>
                                        @foreach($accountMethods as $acc)
                                            <option value="{{ $acc->id }}">{{$acc->name}} - BDT {{priceFormat($acc->amount)}}</option>
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
                                <div class="form-group">
                                    <label for="name">Attachtment</label>
                                    <input type="file" class="form-control {{$errors->has('attachment')?'error':''}}" name="attachment" accept="image/*,application/pdf"  style="padding: 3px;">
                                    @if ($errors->has('attachment'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
                                    @endif
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
                @endcan
            </div>

        </div>
    </div>
</div>


@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.querySelector("#paymentForm");
        const submitBtn = document.querySelector("#submitBtn");
        const cancelBtn = document.querySelector("#cancelEditBtn");

        // ---------- Handle Edit ----------
        document.querySelectorAll(".edit-btn").forEach(btn => {
            btn.addEventListener('click', function () {

                const id = this.dataset.id;
                let url = "{{ route('admin.billPaymentAction', ['update', ':id']) }}";
                url = url.replace(':id', id);
                form.action = url;

                submitBtn.textContent = "Update Payment";

                // Fill form fields
                document.querySelector("[name='pay_amount']").value = this.dataset.amount;
                document.querySelector("[name='account_id']").value = this.dataset.account;
                document.querySelector("[name='payment_method_id']").value = this.dataset.method;
                document.querySelector("[name='note']").value = this.dataset.note;

                cancelBtn.classList.remove('d-none');
                $('.form-title').html('Edit Payment');
                $('.payment-form-card').addClass('border border-danger');
                document.querySelector("[name='pay_amount']").focus();

                // window.scrollIntoView({ behavior: 'smooth', block: 'start' });
                const formTop = form.getBoundingClientRect().top + window.pageYOffset;
                const extraOffset = 200; // move higher above the form
                window.scrollTo({ top: formTop - extraOffset, behavior: 'smooth' });
            });
        });

        // ---------- Cancel Edit ----------
        cancelBtn.addEventListener("click", function () {
            form.reset();
            form.action = "{{ route('admin.billPaymentAction',['save',$purchase->id]) }}";
            submitBtn.textContent = "Submit Payment";
            cancelBtn.classList.add('d-none');
        });

    });
</script>
@endpush
