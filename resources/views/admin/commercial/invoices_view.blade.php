@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('View Commercial Invoice') }}</title>
@endsection

@push('css')
<style>
    .invoice-header {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .invoice-info-table th {
        width: 180px;
        background: #f8f9fa;
    }
    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
    }
    .items-table th {
        background: #343a40;
        color: #fff;
    }
    .total-summary {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush

@section('contents')

<div class="flex-grow-1">
    <!-- Breadcrumb Area -->
    <div class="breadcrumb-area">
        <h1>Commercial Invoice</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item">
                <a href="{{ route('admin.commercial.invoice') }}">Commercial Invoices</a>
            </li>
            <li class="item">View Invoice</li>
        </ol>
    </div>

    @include(adminTheme().'alerts')

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center no-print">
            <h3>Invoice #{{ $invoice->invoice_no }}</h3>
            <div>
                <a href="{{ route('admin.commercial.invoice') }}" class="btn btn-secondary btn-sm">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <a href="{{ route('admin.commercial.invoiceAction', ['print', $invoice->id]) }}" class="btn btn-primary btn-sm" target="_blank">
                    <i class="bx bx-printer"></i> Print
                </a>
                <a href="{{ route('admin.commercial.invoiceAction', ['edit', $invoice->id]) }}" class="btn btn-success btn-sm">
                    <i class="bx bx-edit"></i> Edit
                </a>
                <a href="{{ route('admin.commercial.invoiceAction', ['delete', $invoice->id]) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this invoice?')">
                    <i class="bx bx-trash"></i> Delete
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Invoice Header -->
            <div class="invoice-header">
                <div class="row">
                    <div class="col-md-6">
                        <h2>COMMERCIAL INVOICE</h2>
                        <p class="mb-1"><strong>Invoice No:</strong> {{ $invoice->invoice_no }}</p>
                        <p class="mb-1"><strong>Invoice Date:</strong> {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : '' }}</p>
                        <span class="status-badge badge-{{ $invoice->status_class }}">{{ $invoice->status_label }}</span>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <h4>{{ general()->name }}</h4>
                        <p class="mb-1">{{ general()->address }}</p>
                        <p class="mb-1">Phone: {{ general()->mobile }}</p>
                        <p class="mb-1">Email: {{ general()->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Buyer & Shipment Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered invoice-info-table">
                        <tr>
                            <th colspan="2" class="bg-dark text-white">Buyer Information</th>
                        </tr>
                        <tr>
                            <th>Name:</th>
                            <td>{{ $invoice->buyer_name }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $invoice->buyer_address }}</td>
                        </tr>
                        <tr>
                            <th>Contact:</th>
                            <td>{{ $invoice->buyer_contact }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered invoice-info-table">
                        <tr>
                            <th colspan="2" class="bg-dark text-white">Shipment Details</th>
                        </tr>
                        <tr>
                            <th>LC No:</th>
                            <td>{{ $invoice->lc_no }}</td>
                        </tr>
                        <tr>
                            <th>LC Date:</th>
                            <td>{{ $invoice->lc_date ? \Carbon\Carbon::parse($invoice->lc_date)->format('d M Y') : '' }}</td>
                        </tr>
                        <tr>
                            <th>PI No:</th>
                            <td>{{ $invoice->pi_no }}</td>
                        </tr>
                        <tr>
                            <th>Shipment Date:</th>
                            <td>{{ $invoice->shipment_date ? \Carbon\Carbon::parse($invoice->shipment_date)->format('d M Y') : '' }}</td>
                        </tr>
                        <tr>
                            <th>Delivery Date:</th>
                            <td>{{ $invoice->delivery_date ? \Carbon\Carbon::parse($invoice->delivery_date)->format('d M Y') : '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Shipping Route -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <table class="table table-bordered invoice-info-table">
                        <tr>
                            <th colspan="6" class="bg-dark text-white">Shipping Route</th>
                        </tr>
                        <tr>
                            <th>Shipment From:</th>
                            <td>{{ $invoice->shipment_from }}</td>
                            <th>Shipment To:</th>
                            <td>{{ $invoice->shipment_to }}</td>
                            <th>Country of Origin:</th>
                            <td>{{ $invoice->country_of_origin }}</td>
                        </tr>
                        <tr>
                            <th>Destination:</th>
                            <td>{{ $invoice->destination_country }}</td>
                            <th>Carrier:</th>
                            <td>{{ $invoice->carrier }}</td>
                            <th>Vessel/Flight No:</th>
                            <td>{{ $invoice->vessel_flight_no }}</td>
                        </tr>
                        <tr>
                            <th>Container No:</th>
                            <td>{{ $invoice->container_no }}</td>
                            <th>Seal No:</th>
                            <td>{{ $invoice->seal_no }}</td>
                            <th>Marks & No:</th>
                            <td>{{ $invoice->marks_no }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered items-table">
                    <thead>
                        <tr>
                            <th style="min-width: 50px;">SL</th>
                            <th style="min-width: 250px;">Description</th>
                            <th style="min-width: 100px;">HS Code</th>
                            <th style="min-width: 80px;">Unit</th>
                            <th style="min-width: 100px;">Quantity</th>
                            <th style="min-width: 100px;">Unit Price</th>
                            <th style="min-width: 120px;">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->hs_code }}</td>
                            <td>{{ $item->unit->name ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No items found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Amount Summary -->
            <div class="row">
                <div class="col-md-8">
                    @if($invoice->remarks)
                    <div class="form-section">
                        <h5>Remarks</h5>
                        <p>{{ $invoice->remarks }}</p>
                    </div>
                    @endif
                </div>
                <div class="col-md-4">
                    <div class="total-summary">
                        <table class="table table-borderless">
                            <tr>
                                <td>Total Quantity:</td>
                                <td class="text-right"><strong>{{ number_format($invoice->total_qty, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td>Total Amount:</td>
                                <td class="text-right"><strong>{{ number_format($invoice->total_amount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td>Discount:</td>
                                <td class="text-right">(-) {{ number_format($invoice->discount, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Tax:</td>
                                <td class="text-right">(+) {{ number_format($invoice->tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Shipping Cost:</td>
                                <td class="text-right">(+) {{ number_format($invoice->shipping_cost, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Insurance:</td>
                                <td class="text-right">(+) {{ number_format($invoice->insurance, 2) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Grand Total:</strong></td>
                                <td class="text-right">
                                    <strong>{{ $invoice->currency }} {{ number_format($invoice->grand_total, 2) }}</strong>
                                </td>
                            </tr>
                            @if($invoice->currency != 'BDT')
                            <tr>
                                <td>Exchange Rate:</td>
                                <td class="text-right">1 {{ $invoice->currency }} = {{ number_format($invoice->exchange_rate, 2) }} BDT</td>
                            </tr>
                            <tr>
                                <td><strong>Total in BDT:</strong></td>
                                <td class="text-right"><strong>{{ number_format($invoice->total_in_bdt, 2) }} BDT</strong></td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="row mt-4 no-print">
                <div class="col-md-12">
                    <hr>
                    <p class="text-muted">
                        <strong>Created by:</strong> {{ $invoice->creator->name ?? 'N/A' }} | 
                        <strong>Created at:</strong> {{ $invoice->created_at->format('d M Y, h:i A') }}
                        @if($invoice->edited_by)
                        | <strong>Edited by:</strong> {{ $invoice->editor->name ?? 'N/A' }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
