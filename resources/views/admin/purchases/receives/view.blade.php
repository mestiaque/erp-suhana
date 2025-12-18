@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Purchase Receive View') }}</title>
@endsection

@push('css')

@endpush

@section('contents')
<div class="breadcrumb-area">
    <h1>Purchase Receive View</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">
            <a href="{{ route('admin.purchasesReceived') }}">Purchase Receive</a>
        </li>
        <li class="item">View Receive</li>
    </ol>
</div>

<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Receive Details</h3>
            <div class="dropdown">
                <a href="{{ route('admin.purchasesReceived') }}" class="btn-custom primary">
                    <i class="bx bx-left-arrow-alt"></i> Back List
                </a>
                <a href="javascript:void(0)" id="PrintReceive" class="btn-custom yellow">
                    <i class="bx bx-printer"></i> Print
                </a>
            </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <div class="invoice-container PrintAreaReceive">
                <style>
                    .invoice-container {
                        max-width: 900px;
                        margin: 0 auto;
                        background: #fff;
                        padding: 30px 40px;
                        box-shadow: 0 0 10px rgba(0,0,0,0.1);
                        position: relative;
                    }

                    .status-ribbon {
                        position: absolute;
                        top: 20px;
                        right: -60px;
                        background: #ffc107;
                        color: white;
                        font-size: 20px;
                        font-weight: bold;
                        padding: 10px 80px;
                        transform: rotate(45deg);
                        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                        text-align: center;
                        border-top: 3px solid #e0a800;
                        border-bottom: 3px solid #e0a800;
                    }

                    .invoice-header {
                        display: flex;
                        justify-content: space-between;
                        margin-bottom: 20px;
                    }

                    .company-info p, .receive-info p {
                        margin: 3px 0;
                        font-size: 14px;
                    }

                    .items-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 15px;
                    }

                    .items-table th, .items-table td {
                        border: 1px solid #dee2e6;
                        padding: 8px;
                        text-align: left;
                        font-size: 14px;
                    }

                    .items-table th {
                        background-color: #f5f5f5;
                    }

                    @media print {
                        .invoice-container { box-shadow: none; padding: 10px; }
                        .btn, .breadcrumb-area { display: none; }
                    }
                    </style>

                <div class="invoice-header">
                    <div class="company-info">
                        <p><b>Branch:</b> {{ $receive->branch?->name ?? 'N/A' }}</p>
                        <p><b>Address:</b> {{ $receive->branch?->description ?? 'N/A' }}</p>
                        <p><b>Supplier Name:</b> {{ $receive->purchase?->name ?? '' }}</p>
                        <p><b>Supplier Company:</b> {{ $receive->purchase?->company_name ?? '' }}</p>
                        <p><b>Address:</b> {{  $receive->purchase?->supplier_address ?? '' }}</p>
                    </div>
                    <div class="receive-info">
                        <p><b>Receive No:</b> {{ $receive->purchase_receive_no }}</p>
                        <p><b>Purchase No:</b> {{ $receive->purchase_no ?? '--' }}</p>
                        <p><b>Challan No:</b> {{ $receive->challan_no ?? '--' }}</p>
                        <p><b>Date:</b> {{ $receive->created_at->format('d.m.Y') }}</p>
                    </div>
                </div>

                <h5>Received Items</h5>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;min-width: 50px;">SL</th>
                            <th style="min-width: 250px;">Product</th>
                            <th style="width: 120px;min-width: 120px;text-align:center;">Ordered Qty</th>
                            <th style="width: 120px;min-width: 120px;text-align:center;">Received Qty</th>
                            <th style="width: 60px;min-width: 60px;" >Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receive->items as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->material_name ?? 'N/A' }}</td>
                                <td style="text-align:center;">{{ numberFormat($item->orderItem?->qty ?? 0,1) }}</td>
                                <td style="text-align:center;">{{ numberFormat($item->received_qty ?? 0,1) }}</td>
                                <td>{{ $item->orderItem?->unit ?? '--' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center;color:#aaa;">No Items Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($receive->note)
                    <div class="mt-3">
                        <p><b>Note:</b> {!! $receive->note !!}</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function(){
    $('#PrintReceive').on("click", function () {
        $('.PrintAreaReceive').printThis({
            importCSS: false,
            loadCSS: "https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap-grid.min.css",
        });
    });
});
</script>
@endpush
