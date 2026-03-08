@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Commercial Invoice List') }}</title>
@endsection

@push('css')
<style type="text/css">
    table.table a {
        color: #000;
    }
    .badge-warning {
        color: #000;
        background-color: #d9a50c4d;
    }
    .badge-success {
        color: #035415;
        background-color: #17e64642;
    }
    .badge-info {
        color: #000;
        background-color: #0dcaf026;
    }
    .badge-primary {
        color: #000;
        background-color: #0d6efd2b;
    }
    .statuslist {
        list-style: none;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .statuslist li a {
        padding: 5px 15px;
        border-radius: 20px;
        background: #f0f0f0;
        color: #333;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.3s;
    }
    .statuslist li a:hover, .statuslist li a.active {
        background: #4c4a4a;
        color: #fff;
    }
</style>
@endpush

@section('contents')

<div class="flex-grow-1">
    <!-- Breadcrumb Area -->
    <div class="breadcrumb-area">
        <h1>Commercial Invoices</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item">Commercial</li>
            <li class="item">Commercial Invoices</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Commercial Invoice List</h3>
             <div class="dropdown">
                <a href="{{ route('admin.commercial.commercial.invoiceCreate') }}" class="btn-custom primary" style="padding:5px 15px;">
                    <i class="bx bx-plus"></i> Add New Invoice
                </a>
                <a href="{{ route('admin.commercial.invoice') }}" class="btn-custom yellow">
                    <i class="bx bx-rotate-left"></i>
                </a>
            </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{ route('admin.commercial.invoice') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate ? \Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') : '' }}" class="form-control" placeholder="Start Date" />
                            <input type="date" name="endDate" value="{{ request()->endDate ? \Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') : '' }}" class="form-control" placeholder="End Date" />
                        </div>
                    </div>
                    <div class="col-md-3 mb-1">
                        <select name="buyer_id" class="form-control select2">
                            <option value="">Select Buyer</option>
                            @foreach($buyers as $buyer)
                                <option value="{{ $buyer->id }}" {{ request()->buyer_id == $buyer->id ? 'selected' : '' }}>
                                    {{ $buyer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-1">
                        <select name="status" class="form-control">
                            <option value="all">All Status</option>
                            <option value="1" {{ request()->status == '1' ? 'selected' : '' }}>Pending</option>
                            <option value="2" {{ request()->status == '2' ? 'selected' : '' }}>Approved</option>
                            <option value="3" {{ request()->status == '3' ? 'selected' : '' }}>Shipped</option>
                            <option value="4" {{ request()->status == '4' ? 'selected' : '' }}>Delivered</option>
                            <option value="5" {{ request()->status == '5' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search Invoice No, Buyer, LC No" class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>
            <br>

            <!-- Status Tabs -->
            <div class="row">
                <div class="col-md-12">
                    <ul class="statuslist">
                        <li><a href="{{ route('admin.commercial.invoice') }}" class="{{ !request()->status || request()->status == 'all' ? 'active' : '' }}">All ({{ $statusCounts['total'] }})</a></li>
                        <li><a href="{{ route('admin.commercial.invoice', ['status' => 1]) }}" class="{{ request()->status == '1' ? 'active' : '' }}">Pending ({{ $statusCounts['pending'] }})</a></li>
                        <li><a href="{{ route('admin.commercial.invoice', ['status' => 2]) }}" class="{{ request()->status == '2' ? 'active' : '' }}">Approved ({{ $statusCounts['approved'] }})</a></li>
                        <li><a href="{{ route('admin.commercial.invoice', ['status' => 3]) }}" class="{{ request()->status == '3' ? 'active' : '' }}">Shipped ({{ $statusCounts['shipped'] }})</a></li>
                        <li><a href="{{ route('admin.commercial.invoice', ['status' => 4]) }}" class="{{ request()->status == '4' ? 'active' : '' }}">Delivered ({{ $statusCounts['delivered'] }})</a></li>
                        <li><a href="{{ route('admin.commercial.invoice', ['status' => 5]) }}" class="{{ request()->status == '5' ? 'active' : '' }}">Cancelled ({{ $statusCounts['cancelled'] }})</a></li>
                    </ul>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="min-width: 50px;">SL</th>
                            <th style="min-width: 150px;">Invoice No</th>
                            <th style="min-width: 200px;">Buyer</th>
                            <th style="min-width: 120px;">Invoice Date</th>
                            <th style="min-width: 120px;">LC No</th>
                            <th style="min-width: 120px;">PI No</th>
                            <th style="min-width: 100px;">Total Qty</th>
                            <th style="min-width: 150px;">Amount</th>
                            <th style="min-width: 100px;">Status</th>
                            <th style="min-width: 150px;">Created By</th>
                            <th style="min-width: 180px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $index => $invoice)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('admin.commercial.invoiceAction', ['view', $invoice->id]) }}" class="text-primary font-weight-bold">
                                    {{ $invoice->invoice_no }}
                                </a>
                            </td>
                            <td>{{ $invoice->buyer_name }}</td>
                            <td>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : '' }}</td>
                            <td>{{ $invoice->lc_no }}</td>
                            <td>{{ $invoice->pi_no }}</td>
                            <td>{{ number_format($invoice->total_qty, 2) }}</td>
                            <td>
                                {{ $invoice->currency }} {{ number_format($invoice->grand_total, 2) }}
                                @if($invoice->currency != 'BDT' && $invoice->total_in_bdt > 0)
                                <br><small class="text-muted">= {{ number_format($invoice->total_in_bdt, 2) }} BDT</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $invoice->status_class }}">
                                    {{ $invoice->status_label }}
                                </span>
                            </td>
                            <td>{{ $invoice->creator->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.commercial.invoiceAction', ['view', $invoice->id]) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="{{ route('admin.commercial.invoiceAction', ['print', $invoice->id]) }}" class="btn btn-sm btn-secondary" target="_blank" title="Print">
                                    <i class="bx bx-printer"></i>
                                </a>
                                <a href="{{ route('admin.commercial.invoiceAction', ['edit', $invoice->id]) }}" class="btn btn-sm btn-success" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{ route('admin.commercial.invoiceAction', ['delete', $invoice->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this invoice?')" title="Delete">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">No invoices found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="row">
                <div class="col-md-12">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endpush
