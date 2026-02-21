@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Bill Payment Ledger') }}</title>
@endsection

@section('contents')

<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Bill Payment</h3>
            <a href="{{ route('admin.billPayment') }}" class="btn-custom yellow">
                <i class="bx bx-rotate-left"></i>
            </a>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            {{-- ================= FILTER FORM ================= --}}
            <form action="{{ route('admin.billPayment') }}" method="GET">
                <div class="row">

                    <div class="col-md-3 mb-1">
                        <input type="text" name="title"
                            value="{{ request('title') }}"
                            class="form-control"
                            placeholder="Bill Title / Transaction ID">
                    </div>

                    <div class="col-md-2 mb-1">
                        <input type="text" name="creditor_name"
                            value="{{ request('creditor_name') }}"
                            class="form-control"
                            placeholder="Creditor Name">
                    </div>

                    <div class="col-md-2 mb-1">
                        <input type="text" name="creditor_code"
                            value="{{ request('creditor_code') }}"
                            class="form-control"
                            placeholder="Creditor Code">
                    </div>

                    <div class="col-md-3 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate"
                                value="{{ request('startDate') }}"
                                class="form-control">
                            <input type="date" name="endDate"
                                value="{{ request('endDate') }}"
                                class="form-control">
                        </div>
                    </div>

                    <div class="col-md-2 mb-1">
                        <button type="submit" class="btn btn-success w-100">
                            Search
                        </button>
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

                            {{-- Type is always Payment --}}


                            <td>{{ $row->user?->name ?? '-' }}</td>
                            <td>{{ $row->user?->employee_id ?? '-' }}</td>

                            <td>{{ $row->title }}</td>

                            <td>
                                <small class="text-muted">{{ $row->description ?? '-' }}</small>
                            </td>
                            <td class="text-end text-">{{ number_format($row->debit, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
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
