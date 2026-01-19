@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Account Summary') }}</title>
@endsection

@push('css')
<style>
    /* আধুনিক ডিজাইন এলিমেন্টস */
    .stat-widget { border: none; border-radius: 12px; transition: 0.3s; background: #fff; }
    .stat-widget:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    .table-modern { border-collapse: separate; border-spacing: 0 8px; }
    .table-modern thead th { border: none; background: #f8f9fa; color: #495057; font-weight: 700; text-transform: uppercase; font-size: 11px; padding: 15px; }
    .table-modern tbody tr { box-shadow: 0 2px 5px rgba(0,0,0,0.02); border-radius: 8px; }
    .table-modern tbody td { background: #fff; border: none; padding: 15px; vertical-align: middle; }
    .table-modern tbody td:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .table-modern tbody td:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }

    /* কালার ইন্ডিকেটর */
    .text-credit { color: #00a65a; font-family: 'Inter', sans-serif; font-weight: 600; }
    .text-debit { color: #d9534f; font-family: 'Inter', sans-serif; font-weight: 600; }
    .balance-badge { background: #f0f4f8; color: #1a202c; padding: 6px 12px; border-radius: 6px; font-weight: 700; }
    .border-left-primary { border-left: 5px solid #0d6efd; }
    .border-left-success { border-left: 5px solid #198754; }
    .border-left-danger { border-left: 5px solid #dc3545; }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Account Summary</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item">Accounts</li>
            <li class="item">{{ $method->name }}</li>
        </ol>
    </div>

    @include(adminTheme().'alerts')

    {{-- কুইক সামারি উইজেটস --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-widget shadow-sm border-left-primary">
                <div class="card-body">
                    <small class="text-muted text-uppercase fw-bold">Opening Balance</small>
                    <h4 class="mb-0 mt-1">{{ priceFullFormat($openingBalance ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-widget shadow-sm border-left-success">
                <div class="card-body">
                    <small class="text-muted text-uppercase fw-bold">Total Inflow (+)</small>
                    <h4 class="mb-0 mt-1 text-success">{{ priceFullFormat($total ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-widget shadow-sm border-left-danger">
                <div class="card-body">
                    <small class="text-muted text-uppercase fw-bold">Total Outflow (-)</small>
                    <h4 class="mb-0 mt-1 text-danger">{{ priceFullFormat($totalExpense ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-widget shadow-sm bg-primary text-white">
                <div class="card-body">
                    <small class="text-white-50 text-uppercase fw-bold">Current Balance</small>
                    <h4 class="mb-0 mt-1 text-white">{{ priceFullFormat($nowBalance ?? 0) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ফিল্টার সেকশন --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{route('admin.accountsAction', ['daily-account-summary', $method->id])}}" method="GET" class="row align-items-end g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Statement Date Range</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-calendar"></i></span>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date', $fromDate->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Update Report</button>
                </div>
            </form>
        </div>
    </div>

    {{-- স্টেটমেন্ট টেবিল --}}
    <div class="table-responsive">
        <table class="table table-modern align-middle">
            <thead>
                <tr>
                    <th width="40%">Source / Description</th>
                    <th class="text-end">Credit (In)</th>
                    <th class="text-end">Debit (Out)</th>
                    <th class="text-end">Net Balance</th>
                </tr>
            </thead>
            <tbody>
                {{-- Opening --}}
                <tr>
                    <td><i class="bx bx-archive-in me-2 text-primary"></i> <strong>Opening Balance</strong></td>
                    <td class="text-end text-muted">-</td>
                    <td class="text-end text-muted">-</td>
                    <td class="text-end"><span class="balance-badge">{{ priceFullFormat($openingBalance ?? 0) }}</span></td>
                </tr>

                {{-- Fund Transfer --}}
                <tr>
                    <td><i class="bx bx-transfer-alt me-2 text-success"></i> Fund Transfer</td>
                    <td class="text-end text-credit">+ {{ priceFullFormat($fundTransfer ?? 0) }}</td>
                    <td class="text-end text-muted">-</td>
                    <td class="text-end"><span class="balance-badge">{{ priceFullFormat(($openingBalance ?? 0) + ($fundTransfer ?? 0)) }}</span></td>
                </tr>

                {{-- IOU --}}
                <tr>
                    <td><i class="bx bx-receipt me-2 text-success"></i> Adjust IOU</td>
                    <td class="text-end text-credit">+ {{ priceFullFormat($adjustIou ?? 0) }}</td>
                    <td class="text-end text-muted">-</td>
                    <td class="text-end"><span class="balance-badge">{{ priceFullFormat(($openingBalance ?? 0) + ($fundTransfer ?? 0) + ($adjustIou ?? 0)) }}</span></td>
                </tr>

                {{-- Expense --}}
                <tr>
                    <td><i class="bx bx-wallet me-2 text-danger"></i> Expenses</td>
                    <td class="text-end text-muted">-</td>
                    <td class="text-end text-debit">- {{ priceFullFormat($totalExpense ?? 0) }}</td>
                    <td class="text-end"><span class="balance-badge text-danger">{{ priceFullFormat(($total ?? 0) - ($totalExpense ?? 0)) }}</span></td>
                </tr>

                {{-- Final Row --}}
                <tr class="fw-bold bg-light">
                    <td><i class="bx bx-check-double me-2 text-primary"></i> Closing Statement Total</td>
                    <td class="text-end text-success">{{ priceFullFormat($total ?? 0) }}</td>
                    <td class="text-end text-danger">{{ priceFullFormat($totalExpense ?? 0) }}</td>
                    <td class="text-end"><span class="balance-badge bg-primary text-white">{{ priceFullFormat($nowBalance ?? 0) }}</span></td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
@endsection


