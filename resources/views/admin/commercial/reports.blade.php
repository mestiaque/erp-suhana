@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Commercial Reports') }}</title>
@endsection

@push('css')
<style>
    .report-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .report-card h4 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 15px; }
    .report-card ul { list-style: none; padding: 0; }
    .report-card ul li { padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
    .report-card ul li a { color: #333; text-decoration: none; display: block; }
    .report-card ul li a:hover { color: #007bff; }
    .report-card ul li span { float: right; color: #999; }
    .summary-box { display: inline-block; padding: 15px 25px; margin: 5px; border-radius: 8px; background: #f8f9fa; }
    .summary-box h3 { margin: 0; font-size: 24px; color: #007bff; }
    .summary-box p { margin: 0; color: #666; }
</style>
@endpush

@section('contents')
<div class="breadcrumb-area">
    <h1>Commercial Reports</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Commercial</li>
        <li class="item">Reports</li>
    </ol>
</div>

<div class="card mb-30">
    <div class="card-header"><h3>Commercial Reports Dashboard</h3></div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-12">
                <h4>Summary</h4>
                <div class="summary-box">
                    <h3>{{ $totalInvoices }}</h3>
                    <p>Total Invoices</p>
                </div>
                <div class="summary-box">
                    <h3>{{ $currency }}{{ number_format($totalInvoiceValue, 2) }}</h3>
                    <p>Total Invoice Value</p>
                </div>
                <div class="summary-box">
                    <h3>{{ $totalBTBLCs }}</h3>
                    <p>BTB LC</p>
                </div>
                <div class="summary-box">
                    <h3>{{ $totalExportLCs }}</h3>
                    <p>Export LC</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="report-card">
                    <h4>Invoice Reports</h4>
                    <ul>
                        <li><a href="{{ route('admin.commercial.invoice') }}">All Invoices <span>{{ $invoiceCounts['all'] }}</span></a></li>
                        <li><a href="{{ route('admin.commercial.invoice', ['status' => 1]) }}">Pending Invoices <span>{{ $invoiceCounts['pending'] }}</span></a></li>
                        <li><a href="{{ route('admin.commercial.invoice', ['status' => 2]) }}">Confirmed Invoices <span>{{ $invoiceCounts['confirmed'] }}</span></a></li>
                        <li><a href="{{ route('admin.commercial.invoice', ['status' => 3]) }}">Shipped Invoices <span>{{ $invoiceCounts['shipped'] }}</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <h4>LC Reports</h4>
                    <ul>
                        <li><a href="{{ route('admin.commercial.btbLc') }}">Bank BTB LC <span>{{ $totalBTBLCs }}</span></a></li>
                        <li><a href="{{ route('admin.commercial.exportLc') }}">Export LC <span>{{ $totalExportLCs }}</span></a></li>
                        <li><a href="{{ route('admin.commercial.purchaseOrders') }}">Purchase Orders <span>{{ $totalPOs }}</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <h4>Export Reports</h4>
                    <ul>
                        <li><a href="{{ route('admin.commercial.pi') }}">Proforma Invoices <span>{{ $totalPIs }}</span></a></li>
                        <li><a href="{{ route('admin.commercial.packingList') }}">Packing Lists <span>{{ $totalPLs }}</span></a></li>
                        <li><a href="{{ route('admin.commercial.shippingDocs') }}">Shipping Documents <span>{{ $totalSBs }}</span></a></li>
                        <li><a href="{{ route('admin.commercial.realization') }}">Export Realization <span>{{ $totalRealizations }}</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="report-card">
                    <h4>Filter by Date Range</h4>
                    <form action="{{ route('admin.commercial.reports') }}" method="GET">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" name="from_date" value="{{ request()->from_date }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" name="to_date" value="{{ request()->to_date }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-success form-control">Filter</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="report-card">
                    <h4>Export Options</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.commercial.reports', ['export' => 'invoices']) }}" class="btn btn-primary btn-block mb-2"><i class="bx bx-download"></i> Export Invoices</a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.commercial.reports', ['export' => 'summary']) }}" class="btn btn-info btn-block mb-2"><i class="bx bx-download"></i> Export Summary</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
