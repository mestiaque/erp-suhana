@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Edit Payment') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Edit Payment</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item">
                <a href="{{ route('admin.suppliers') }}">Creditor List</a>
            </li>
            <li class="item">
                <a href="{{ route('admin.suppliersAction', ['action' => 'bill-entry', 'id' => $user->id]) }}">Bill Entry</a>
            </li>
            <li class="item">Edit Payment</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Edit Payment - {{ $user->name }}</h3>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')
            
            <form action="{{ route('admin.suppliersAction', ['action' => 'bill-payment-update', 'id' => $transaction->id]) }}" method="POST" enctype="multipart/form-data" class="p-3 border rounded">
                @csrf
                <div class="row">
                    <div class="mb-2 col-md-6">
                        <label>Pay Amount</label>
                        <input type="number" placeholder="0.00" name="pay_amount" step="any" value="{{ old('pay_amount', $transaction->amount) }}" class="form-control" required>
                    </div>
                    <div class="mb-2 col-md-6">
                        <label>Select Account</label>
                        <select name="account_id" class="form-control" required>
                            <option value="">Select Account</option>
                            @foreach($accountMethods as $acc)
                                <option value="{{ $acc->id }}" {{ $transaction->account_id == $acc->id ? 'selected' : '' }}>{{ $acc->name }} - BDT {{ priceFormat($acc->amount) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2 col-md-6">
                        <label>Payment Method</label>
                        <select name="payment_method_id" class="form-control" required>
                            <option value="">Select Method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" {{ $transaction->payment_method_id == $method->id ? 'selected' : '' }}>{{ $method->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="name">Attachment</label>
                        <input type="file" class="form-control" name="attachment" accept="image/*,application/pdf" style="padding: 3px;">
                        @if($transaction->attachment)
                            <small class="text-success">Current file attached</small>
                        @endif
                    </div>

                    <div class="mb-2 col-md-12">
                        <label>Note</label>
                        <textarea name="note" placeholder="Write note here..." class="form-control" cols="30" rows="2">{{ old('note', $transaction->billing_note) }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-start mt-2">
                    <a href="{{ route('admin.suppliersAction', ['action' => 'bill-entry', 'id' => $user->id]) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
