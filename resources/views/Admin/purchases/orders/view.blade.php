@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Purchase Requisition View') }}</title>
@endsection

@push('css')
<style>
/* Similar styling as your Sale view */
.invoice-container {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 40px 50px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
}

.paid-ribbon {
    position: absolute;
    top: 40px;
    right: -71px;
    background: linear-gradient(135deg, #20df27 0%, #31b83f 100%);
    color: white;
    font-size: 26px;
    font-weight: bold;
    letter-spacing: 5px;
    padding: 15px 109px;
    transform: rotate(45deg);
    box-shadow: 0 4px 12px rgba(139,195,74,0.4);
    z-index: 10;
    text-align: center;
    border-top: 3px solid #067e28;
    border-bottom: 3px solid #067e28;
}

.iHeader {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.company-header p {
    margin: 3px 0;
    font-size: 14px;
    color: #000;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.items-table th, .items-table td {
    border: 1px solid #dee2e6;
    padding: 8px;
    font-size: 14px;
    text-align: left;
}

.items-table th:last-child, .items-table td:last-child {
    text-align: right;
}

.total-row {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    margin-top: 10px;
}

.terms-details p {
    margin: 0;
    font-size: 12px;
    font-family: times new roman;
}

@media print {
    .invoice-container {
        box-shadow: none;
        padding: 20px;
    }
}
</style>
@endpush

@section('contents')

<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Purchase Order View</h3>
            <div class="dropdown">
                <a href="{{ route('admin.purchasesOrders') }}" class="btn-custom primary">
                    <i class="bx bx-left-arrow-alt"></i> Back List
                </a>
                <a href="javascript:void(0)" id="PrintRequisition" class="btn-custom yellow">
                    <i class="bx bx-printer"></i> Print
                </a>
            </div>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')

            <div class="invoice-inner PrintAreaContact">
                <div class="invoice-container">
                    @if($order->status == 'approved')
                        <div class="paid-ribbon">APPROVED</div>
                    @elseif($order->status == 'pending')
                        <div class="paid-ribbon" style="background: #ffc107;">PENDING</div>
                    @endif

                    <div class="iHeader">
                        <div>
                            <img src="{{ asset(general()->logo()) }}" alt="company-logo" style="max-width: 350px; max-height: 100px" />
                        </div>
                        <div class="company-header">
                            <p>House: 7</p>
                            <p>Road: 14/C, Sector: 4,</p>
                            <p>Uttara-1230 Dhaka, Bangladesh</p>
                            <p>Phone: +8801628 092045</p>
                            <p>Web: www.natoreit.com</p>
                        </div>
                    </div>

                    <div class="inviceTitleId">
                        <div class="invoice-title">Requisition #{{ $order->requisition_no }}</div>
                        <div class="invoice-dates">
                            <p><b>Request Date:</b> {{ $order->created_at->format('d.m.Y') }}</p>
                            <p><b>Expected Delivery:</b> {{ $order->expected_delivery_date ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="invoiced-to-title">Requested By</div>
                    <div class="client-details">
                        <p>{{ $order->user->name ?? 'N/A' }}</p>
                        <p>Department: {{ $order->department->name ?? 'N/A' }}</p>
                    </div>

                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Expected Date</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->items as $i => $item)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td>{{ $item->expected_date ?? 'N/A' }}</td>
                                    <td>{{ $item->note }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center;">No Items</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($order->note)
                        <div class="terms-details">
                            <span><b>Remarks / Notes</b></span><br>
                            <p>{!! $order->note !!}</p>
                        </div>
                    @endif

                    <div class="footer-part">
                        <p>@if(general()->address_one) Office: {{ general()->address_one }}, @endif
                        @if(general()->mobile) Phone: {{ general()->mobile }} @endif
                        @if(general()->email) Email: {{ general()->email }} @endif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
$(document).ready(function(){
    $('#PrintRequisition').on("click", function () {
        $('.PrintAreaContact').printThis({
            importCSS: false,
            loadCSS: "https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap-grid.min.css",
        });
    });
});
</script>
@endpush
