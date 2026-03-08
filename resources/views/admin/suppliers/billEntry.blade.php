@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Creditor Profile - ' . $user->name)}}</title>
@endsection

@push('css')
<style>
    /* Profile & Cards */
    .ProfileImage { width: 90px; height: 90px; border: 3px solid #fff; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
    .stat-card { border: none; border-radius: 10px; transition: 0.3s; }
    .stat-card:hover { transform: translateY(-5px); }

    /* Tab Styling */
    .nav-pills .nav-link { color: #555; font-weight: 600; border: 1px solid #eee; margin-right: 5px; }
    .nav-pills .nav-link.active { background-color: #0d6efd !important; color: #fff !important; }

    /* Ledger Table Styling */
    .table-ledger thead th { background: #ffffffad; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: #555; }
    .table-ledger thead tr {border-left: 1px solid #55555570; }
    .credit-row { border-left: 1px solid #28a745; } /* Green for Bill */
    .debit-row { border-left: 1px solid #dc3545; }  /* Red for Payment */
    .amount-text { font-family: 'Courier New', Courier, monospace; font-weight: bold; }
</style>
<style>
    .amount-card {
        border-left: 4px solid #198754; /* Default green, overridden dynamically */
        transition: all 0.25s ease-in-out;
        margin-bottom: 0.6rem;
        padding: 0.6rem 2rem;
    }

    .amount-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.12) !important;
    }

    .c1 { background: #00ff3c18 !important; }
    .c2 { background: #00d5ff1e !important; }
    .c3 { background: #ff001913 !important; }
</style>
@endpush
@section('contents')
<div class="flex-grow-1">

    {{-- Breadcrumb --}}
    <div class="breadcrumb-area">
        <h1>Profile</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item"><a href="{{route('admin.suppliers')}}">Creditor List</a></li>
            <li class="item">Profile</li>
        </ol>
    </div>

    @include(adminTheme().'alerts')

    <div class="row">
        {{-- Profile Info --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center">
                    <img src="{{ asset($user->image()) }}" class="rounded-circle mb-3 ProfileImage" alt="User">
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <h6 class="badge badge-info" style="font-size: 88%"> {{ $user->employee_id }} </h6>
                    <p class="text-muted small mb-3">{{ $user->mobile }}</p>
                    <div class="text-start border-top pt-3 smallx">
                        <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                        <p class="mb-1"><strong>Address:</strong> {{ $user->fullAddress() }}</p>
                        <p class="mb-0"><strong>Creditor Since:</strong> {{ $user->created_at->format('d M, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="card amount-card c1 shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-muted mb-1 text-uppercase fw-semibold">Total Purchases</h6>
                            <h3 class="text-success fw-bold mb-0">{{ priceFullFormat($user->creditorBill->sum('amount')) }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card amount-card c2 shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-muted mb-1 text-uppercase fw-semibold">Total Paid</h6>
                            <h3 class="text-info fw-bold mb-0">{{ priceFullFormat($totalPaid) }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card amount-card c3 shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-muted mb-1 text-uppercase fw-semibold">Net Due Balance</h6>
                            <h3 class="text-danger fw-bold mb-0">{{ priceFullFormat($user->creditorBill->sum('amount') - $totalPaid) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Tabs for Bill Entry and Payment --}}
        <div class="col-lg-9">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white pt-0">
                    <ul class="nav nav-pills card-header-pills ms-2" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-history-tab" data-bs-toggle="pill" data-open-tab="#pills-history" data-bs-target="#pills-history" type="button" role="tab" aria-controls="pills-history" aria-selected="true"> Statement / History </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-bill-tab" data-bs-toggle="pill" data-open-tab="#pills-bill" data-bs-target="#pills-bill" type="button" role="tab" aria-controls="pills-bill" aria-selected="false"> Add Bill </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-payment-tab" data-bs-toggle="pill" data-open-tab="#pills-payment" data-bs-target="#pills-payment" type="button" role="tab" aria-controls="pills-payment" aria-selected="false"> Make Payment </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">

                        {{-- Tab 1: Transaction History (Ledger) --}}
                        <div class="tab-pane fade show active" id="pills-history" role="tabpanel">

                            {{-- Filter Form and Print Button --}}
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <form method="GET" action="{{ route('admin.suppliersAction', ['action' => 'bill-entry', 'id' => $user->id]) }}" class="mb-0">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Title/Invoice/Transaction ID" value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-6 d-flex gap-2">
                                            <input type="date" name="startDate" class="form-control form-control-sm" placeholder="Start Date" value="{{ request('startDate') }}">
                                            <span class="text-muted" style="padding: 0.5rem"><i>TO</i></span>
                                            <input type="date" name="endDate" class="form-control form-control-sm" placeholder="End Date" value="{{ request('endDate') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-sm btn-primary me-2"><i class="bx bx-filter"></i> Filter</button>
                                            <a href="{{ route('admin.suppliersAction', ['action' => 'bill-entry', 'id' => $user->id]) }}" class="btn btn-sm btn-secondary"><i class="bx bx-reset"></i> Reset</a>
                                        </div>
                                    </div>
                                </form>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.suppliersAction', ['action' => 'print-bill-entry', 'id' => $user->id]) }}" target="_blank" class="btn btn-sm btn-success"><i class="bx bx-printer"></i> Print</a>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle table-ledger">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Title/Invoice/Transaction ID</th>
                                            <th>Description/Note</th>
                                            <th class="text-right">Credit (+)</th>
                                            <th class="text-right">Debit (-)</th>
                                            <th class="text-right">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ledgerEntries as $item)
                                        <tr class="{{ $item->type == 'payment' ? 'credit-row' : 'debit-row' }}">
                                            <td>{{ $item->date ? $item->date->format('d-m-Y') : '' }}</td>
                                            <td>
                                                {{ $item->title }}
                                                @if($item->type == 'bill')
                                                    @can('creditor.edit')
                                                    <a href="{{ route('admin.suppliersAction', ['action' => 'bill-entry-edit', 'id' => $item->id]) }}" class="btn btn-sm btn-link text-primary" title="Edit Bill"><i class="bx bx-edit"></i></a>
                                                    @endcan
                                                    @can('creditor.delete')
                                                    <a href="{{ route('admin.suppliersAction', ['action' => 'bill-entry-delete', 'id' => $item->id]) }}" class="btn btn-sm btn-link text-danger" title="Delete Bill" onclick="return confirm('Are you sure you want to delete this bill?')"><i class="bx bx-trash"></i></a>
                                                    @endcan
                                                @else
                                                    @can('creditor.edit')
                                                    <a href="{{ route('admin.suppliersAction', ['action' => 'bill-payment-edit', 'id' => $item->id]) }}" class="btn btn-sm btn-link text-primary" title="Edit Payment"><i class="bx bx-edit"></i></a>
                                                    @endcan
                                                    @can('creditor.delete')
                                                    <a href="{{ route('admin.suppliersAction', ['action' => 'bill-payment-delete', 'id' => $item->id]) }}" class="btn btn-sm btn-link text-danger" title="Delete Payment" onclick="return confirm('Are you sure you want to delete this payment?')"><i class="bx bx-trash"></i></a>
                                                    @endcan
                                                @endif
                                            </td>
                                            <td>{{ $item->note ?? '-' }}</td>
                                            <td class="text-right text-success">{{ $item->credit > 0 ? priceFullFormat($item->credit) : '-' }}</td>
                                            <td class="text-right text-danger">{{ $item->debit > 0 ? priceFullFormat($item->debit) : '-' }}</td>
                                            <td class="text-right {{ $loop->first ? 'font-weight-bold' : '' }}">{{ priceFullFormat($item->balance) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $ledgerEntries->links('pagination') }}</div>

                        </div>

                        {{-- Tab 2: Bill Entry Form --}}
                        <div class="tab-pane fade" id="pills-bill" role="tabpanel">
                            <form action="{{ route('admin.suppliersAction', ['action' => 'bill-entry-post', 'id' => $user->id]) }}" method="POST" class="p-3 border rounded">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Bill Title/Invoice No</label>
                                        <input type="text" name="title" class="form-control" placeholder="Enter title" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Amount</label>
                                        <input type="number" step="any" name="amount" class="form-control" placeholder="0.00" required>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold">Description</label>
                                        <textarea name="description" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Save Bill Entry</button>
                            </form>
                        </div>

                        {{-- Tab 3: Payment Form --}}
                        <div class="tab-pane fade" id="pills-payment" role="tabpanel">
                            {{-- @can('creditor.payment') --}}
                            <form id="paymentForm" action="{{ route('admin.suppliersAction', ['action' => 'bill-payment-store', 'id' => $user->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $user->id }}" name="user_id" hidden readonly>
                                <div class="row">
                                    <div class="mb-2 col-md-6">
                                        <label>Pay Amount</label>
                                        <input type="number" placeholder="0.00" name="pay_amount" step="any" max="" class="form-control" required>
                                    </div>
                                    <div class="mb-2 col-md-6">
                                        <label>Select Account</label>
                                        <select name="account_id" class="form-control" required>
                                            <option value="">Select Account</option>
                                            @foreach($accountMethods as $acc)
                                                <option value="{{ $acc->id }}">{{$acc->name}} - BDT {{priceFormat($acc->amount)}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-2 col-md-6">
                                        <label>Payment Method</label>
                                        <select name="payment_method_id" class="form-control" required>
                                            <option value="">Select Method</option>
                                            @foreach($paymentMethods as $method)
                                                <option value="{{$method->id}}">{{$method->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="name">Attachtment</label>
                                        <input type="file" class="form-control {{$errors->has('attachment')?'error':''}}" name="attachment" accept="image/*,application/pdf"  style="padding: 3px;">
                                        @if ($errors->has('attachment'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
                                        @endif
                                    </div>

                                    <div class="mb-2 col-md-12">
                                        <label>Note</label>
                                        <textarea name="note" placeholder="Write note here..." class="form-control" cols="30" rows="2"></textarea>
                                    </div>

                                </div>


                                {{-- <button type="submit" class="btn btn-success w-50 mt-2">Submit Payment</button> --}}

                                <div class="d-flex gap-2 justify-content-start mt-2 w-100">
                                    <button type="button"  id="cancelEditBtn" class="btn grey btn-outline-secondary d-none mr-1">Close </button>
                                    <button type="submit" id="submitBtn" class="btn btn-primary"><i class="bx bx-plus"></i> Add Payment</button>
                                </div>
                            </form>
                            {{-- @endcan --}}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
$(document).ready(function() {

    // On page load, check localStorage
    var activeTab = localStorage.getItem('activeCreditorTab');

    if(activeTab) {
        // Remove active/show from all
        $('#pills-tab button').removeClass('active');
        $('#pills-tabContent .tab-pane').removeClass('show active');

        // Add manually
        $('#pills-tab button[data-bs-target="' + activeTab + '"]').addClass('active');
        $(activeTab).addClass('show active');
    } else {
        // Default: make first tab active
        $('#pills-tab button:first').addClass('active');
        $('#pills-tabContent .tab-pane:first').addClass('show active');
    }

    // Save tab to localStorage when switched
    $('#pills-tab button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).data('bs-target');
        localStorage.setItem('activeCreditorTab', target);
    });

    // Buttons that open specific tab
    $('[data-open-tab]').on('click', function() {
        var target = $(this).data('open-tab');

        // Remove current active
        $('#pills-tab button').removeClass('active');
        $('#pills-tabContent .tab-pane').removeClass('show active');

        // Add manually
        $('#pills-tab button[data-bs-target="' + target + '"]').addClass('active');
        $(target).addClass('show active');

        localStorage.setItem('activeCreditorTab', target);
    });

});
</script>


@endpush
@endsection


