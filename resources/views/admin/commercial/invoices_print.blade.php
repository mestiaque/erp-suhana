<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ websiteTitle('Commercial Invoice - ' . $invoice->invoice_no) }}</title>
    <link rel="apple-touch-icon" href="{{ asset(general()->favicon()) }}" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset(general()->favicon()) }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f2f2f2;
            font-size: 12px;
            font-family: 'Times New Roman', Times, serif;
        }
        p {
            margin: 2px;
        }
        
        .print-container {
            width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: 10px auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        
        .no-print-container {
            width: 210mm;
            padding: 10px;
            margin: 10px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        table {
            width: 100%;
            border-collapse: collapse !important;
        }
        table th, table td {
            border: 1px solid #000 !important;
            padding: 4px 6px;
        }
        thead th {
            background: #e9ecef !important;
            color: #000;
        }
        
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .invoice-header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .info-table th {
            width: 150px;
            background: #f8f8f8 !important;
        }
        
        .items-table th {
            background: #333 !important;
            color: #fff !important;
            text-align: center;
        }
        .items-table td {
            text-align: center;
        }
        .items-table td:first-child,
        .items-table th:first-child {
            width: 40px;
        }
        
        .total-section {
            margin-top: 20px;
        }
        
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-box .line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
        
        @media print {
            body {
                background: none;
                font-size: 12px;
            }
            .print-container {
                margin: 0;
                width: 100%;
                min-height: auto;
                box-shadow: none;
                padding: 5mm;
            }
            @page {
                size: A4;
                margin: 5mm;
            }
            .no-print-container {
                display: none !important;
            }
        }
        
        .btn-print {
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-print:hover {
            background: #0056b3;
        }
        .btn-back {
            padding: 10px 20px;
            background: #6c757d;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }
        .btn-back:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>

<div class="no-print-container" style="position:sticky; top:0; z-index:999;">
    <a href="{{ route('admin.commercial.invoiceAction', ['view', $invoice->id]) }}" class="btn-back">
        <i class="fa fa-arrow-left"></i> Back
    </a>
    <button onclick="window.print()" class="btn-print">
        <i class="fa fa-print"></i> Print
    </button>
</div>

<div class="print-container">
    <!-- Invoice Header -->
    <div class="invoice-header">
        <h1>COMMERCIAL INVOICE</h1>
        <p><strong>Invoice No:</strong> {{ $invoice->invoice_no }}</p>
        <p><strong>Date:</strong> {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : '' }}</p>
    </div>
    
    <!-- Company Info -->
    <div class="company-info">
        <h3>{{ general()->name }}</h3>
        <p>{{ general()->address }}</p>
        <p>Phone: {{ general()->mobile }} | Email: {{ general()->email }}</p>
    </div>
    
    <!-- Buyer & Shipment Info -->
    <table class="info-table mb-3">
        <tr>
            <th>Buyer Name:</th>
            <td>{{ $invoice->buyer_name }}</td>
            <th>Invoice Date:</th>
            <td>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : '' }}</td>
        </tr>
        <tr>
            <th>Buyer Address:</th>
            <td colspan="3">{{ $invoice->buyer_address }}</td>
        </tr>
        <tr>
            <th>Buyer Contact:</th>
            <td>{{ $invoice->buyer_contact }}</td>
            <th>LC No:</th>
            <td>{{ $invoice->lc_no }}</td>
        </tr>
        <tr>
            <th>PI No:</th>
            <td>{{ $invoice->pi_no }}</td>
            <th>LC Date:</th>
            <td>{{ $invoice->lc_date ? \Carbon\Carbon::parse($invoice->lc_date)->format('d M Y') : '' }}</td>
        </tr>
    </table>
    
    <!-- Shipping Details -->
    <table class="info-table mb-3">
        <tr>
            <th colspan="4" style="background: #e9ecef; text-align: center;">Shipment Details</th>
        </tr>
        <tr>
            <th>Shipment From:</th>
            <td>{{ $invoice->shipment_from }}</td>
            <th>Shipment To:</th>
            <td>{{ $invoice->shipment_to }}</td>
        </tr>
        <tr>
            <th>Country of Origin:</th>
            <td>{{ $invoice->country_of_origin }}</td>
            <th>Destination:</th>
            <td>{{ $invoice->destination_country }}</td>
        </tr>
        <tr>
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
        </tr>
    </table>
    
    <!-- Items Table -->
    <table class="items-table mb-3">
        <thead>
            <tr>
                <th>SL</th>
                <th>Description of Goods</th>
                <th>HS Code</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left;">{{ $item->description }}</td>
                <td>{{ $item->hs_code }}</td>
                <td>{{ $item->unit->name ?? 'PCS' }}</td>
                <td>{{ number_format($item->quantity, 2) }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No items found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Amount Summary -->
    <div class="row total-section">
        <div class="col-md-8">
            @if($invoice->description_of_goods)
            <div class="mb-3">
                <strong>Description of Goods:</strong>
                <p>{{ $invoice->description_of_goods }}</p>
            </div>
            @endif
            @if($invoice->remarks)
            <div class="mb-3">
                <strong>Remarks:</strong>
                <p>{{ $invoice->remarks }}</p>
            </div>
            @endif
        </div>
        <div class="col-md-4">
            <table class="info-table">
                <tr>
                    <th>Total Quantity:</th>
                    <td class="text-right">{{ number_format($invoice->total_qty, 2) }}</td>
                </tr>
                <tr>
                    <th>Total Amount:</th>
                    <td class="text-right">{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                @if($invoice->discount > 0)
                <tr>
                    <th>Discount:</th>
                    <td class="text-right">(-) {{ number_format($invoice->discount, 2) }}</td>
                </tr>
                @endif
                @if($invoice->tax > 0)
                <tr>
                    <th>Tax:</th>
                    <td class="text-right">(+) {{ number_format($invoice->tax, 2) }}</td>
                </tr>
                @endif
                @if($invoice->shipping_cost > 0)
                <tr>
                    <th>Shipping Cost:</th>
                    <td class="text-right">(+) {{ number_format($invoice->shipping_cost, 2) }}</td>
                </tr>
                @endif
                @if($invoice->insurance > 0)
                <tr>
                    <th>Insurance:</th>
                    <td class="text-right">(+) {{ number_format($invoice->insurance, 2) }}</td>
                </tr>
                @endif
                <tr style="background: #e9ecef; font-weight: bold;">
                    <th>Grand Total ({{ $invoice->currency }}):</th>
                    <td class="text-right">{{ number_format($invoice->grand_total, 2) }}</td>
                </tr>
                @if($invoice->currency != 'BDT')
                <tr>
                    <th>Exchange Rate:</th>
                    <td class="text-right">1 {{ $invoice->currency }} = {{ number_format($invoice->exchange_rate, 2) }} BDT</td>
                </tr>
                <tr style="font-weight: bold;">
                    <th>Total in BDT:</th>
                    <td class="text-right">{{ number_format($invoice->total_in_bdt, 2) }} BDT</td>
                </tr>
                @endif
            </table>
        </div>
    </div>
    
    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="line">Prepared By</div>
        </div>
        <div class="signature-box">
            <div class="line">Checked By</div>
        </div>
        <div class="signature-box">
            <div class="line">Approved By</div>
        </div>
    </div>
</div>

</body>
</html>
