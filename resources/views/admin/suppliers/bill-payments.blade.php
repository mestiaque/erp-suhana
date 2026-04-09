@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Bill Payment Ledger') }}</title>
@endsection

@section('contents')

<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Bill Payment</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.billPaymentPrint', request()->query()) }}" target="_blank" class="btn btn-sm mr-2 btn-success">
                    <i class="bx bx-printer"></i> Print
                </a>

            </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            {{-- ================= FILTER FORM ================= --}}
            <form action="{{ route('admin.billPayment') }}" method="GET">
                <div class="row">

                    <div class="col-md-3 mb-1">
                        <input type="text" name="title"
                            value="{{ request('title') }}"
                            class="form-control form-control-sm"
                            placeholder="Bill Title / Transaction ID">
                    </div>

                    <div class="col-md-2 mb-1">
                        <input type="text" name="creditor_name"
                            value="{{ request('creditor_name') }}"
                            class="form-control form-control-sm"
                            placeholder="Creditor Name">
                    </div>

                    <div class="col-md-2 mb-1">
                        <input type="text" name="creditor_code"
                            value="{{ request('creditor_code') }}"
                            class="form-control form-control-sm"
                            placeholder="Creditor Code">
                    </div>

                    <div class="col-md-2 mb-1">
                        <select name="account_id" class="form-control form-control-sm">
                            <option value="">All Accounts</option>
                            @foreach($filterAccounts as $account)
                            <option value="{{$account->id}}" {{request('account_id') == $account->id ? 'selected' : ''}}>{{$account->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate"
                                value="{{ request('startDate') }}"
                                class="form-control form-control-sm">
                            <input type="date" name="endDate"
                                value="{{ request('endDate') }}"
                                class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="col-md-1 mb-1 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-success w-100 mr-2">
                            Search
                        </button>
                        <a href="{{ route('admin.billPayment') }}" class="btn btn-sm btn-custom yellow ">
                            Reset
                        </a>
                    </div>

                </div>
            </form>

            <br>

            {{-- ================= LEDGER TABLE ================= --}}
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="50">SL</th>
                            <th>Date</th>
                            <th>Creditor</th>
                            <th>Code</th>
                            <th>Title / Ref</th>
                            <th>Details</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($ledgerEntries as $i => $row)
                        <tr>
                            <td>{{ $ledgerEntries->firstItem() + $i }}</td>

                            <td>{{ $row->date->format('d.m.Y') }}</td>

                            <td>{{ $row->user?->name ?? '-' }}</td>
                            <td>{{ $row->user?->employee_id ?? '-' }}</td>

                            <td>{{ $row->title }}</td>

                            <td>
                                <small class="text-muted">{{ $row->description ?? '-' }}</small>
                            </td>
                            <td class="text-end @if($row->credit > 0) text-success @else text-danger @endif">
                                @if($row->credit > 0)+ @endif
                                @if($row->debit > 0)- @endif
                                {{ number_format($row->credit > 0 ? $row->credit : $row->debit, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No payment records found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            {{-- ================= PAGINATION ================= --}}
            {{ $ledgerEntries->links('pagination') }}

        </div>
    </div>
</div>

@endsection
