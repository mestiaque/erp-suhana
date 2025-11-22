@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Purchase Requisition View') }}</title>
@endsection

@push('css')

@endpush

@section('contents')

<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Requisition  View</h3>
            <div class="dropdown">
                <a href="{{ route('admin.purchasesRequisitions') }}" class="btn-custom primary">
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
                        top: 50px;
                        right: -110px;
                        background: linear-gradient(135deg, #20df27 0%, #31b83f 100%);
                        color: white;
                        font-size: 26px;
                        font-weight: bold;
                        letter-spacing: 5px;
                        padding: 15px 50px;
                        transform: rotate(45deg);
                        box-shadow: 0 4px 12px rgba(139,195,74,0.4);
                        z-index: 10;
                        text-align: center;
                        border-top: 3px solid #067e28;
                        border-bottom: 3px solid #067e28;
                        width: 380px;
                        height: 70px;
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
                        margin-right: 160px;
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
                        margin-bottom: 12px;
                    }

                    .signature-section {
                        display: flex;
                        justify-content: space-between;
                        margin-top: 0;
                        padding-top: 20px;
                    }
                    
                    .signature-box {
                        text-align: center;
                        flex: 1;
                    }
                    
                    .signature-line {
                        border-top: 1px solid #000;
                        margin: 40px 20px 5px 20px;
                        position: relative;
                    }
                    
                    .signature-text {
                        font-family: 'Brush Script MT', cursive;
                        font-size: 24px;
                        margin-top: -35px;
                        color: #1a3d0a;
                    }
                    
                    .input-underline {
                        border: none;
                        border-bottom: 1px solid #000;
                        background: transparent;
                        width: 100%;
                        font-size: 14px;
                    }
                    
                    .input-underline:focus {
                        outline: none;
                        border-bottom-color: #2d5016;
                    }

                    @media print {
                        .invoice-container {
                            box-shadow: none;
                            padding: 20px;
                        }
                    }
                    </style>
                <div class="invoice-container">
                    @if($requisition->status == 'approved')
                        <div class="paid-ribbon">APPROVED</div>
                    @elseif($requisition->status == 'pending')
                        <div class="paid-ribbon" style="background: #ffc107;">PENDING</div>
                    @endif

                    <div class="iHeader">
                        <div>
                            <img src="{{ asset(general()->logo()) }}" alt="company-logo" style="max-width: 350px; max-height: 100px" />
                        </div>
                        <div class="company-header">
                            <p> {!!general()->address_one!!}</p>
                            <p>Phone: {{general()->mobile}}</p>
                            <p>Email: {{general()->email}}</p>
                        </div>
                    </div>

                    <div class="inviceTitleId">
                        <div class="invoice-title">Requisition #{{ $requisition->requisition_no}}</div>
                        <div class="invoice-dates">
                            <p>
                                <b>Request Date:</b> {{ $requisition->created_at->format('d.m.Y') }} <br>
                                <b>Assinge By:</b> {{$requisition->user?->name}} <br>
                                <b>Department:</b> {{$requisition->department?->name ?? 'N/A'}}
                            </p>
                        </div>
                    </div>

                    <div class="invoiced-to-title"><b>Expected Date :</b> {{$requisition->expected_date?Carbon\Carbon::parse($requisition->expected_date)->format('d.m.Y'):old('expected_date')}}
                    </div>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;min-width: 50px;">SL</th>
                                <th style="min-width: 200px;">Meterial</th>
                                <th style="width: 60px;min-width: 60px;">Qty</th>
                                <th style="width: 80px;min-width: 80px;">Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requisition->items as $i => $item)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $item->material_name }}</td>
                                    <td>{{ numberFormat($item->qty,1) }}</td>
                                    <td>{{ $item->unit }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center;">No Items</td>
                                </tr>
                            @endforelse
                            <tr>
                                <th></th>
                                <th></th>
                                <th>{{ numberFormat($requisition->total_qty,1) }}</th>
                                <th></th>
                            </tr>
                        </tbody>
                    </table>

                    @if($requisition->note)
                        <div class="terms-details">
                            <span><b>Remarks / Notes</b></span><br>
                            <p>{!! $requisition->note !!}</p>
                        </div>
                    @endif

                     <div class="signature-section">
                        <div class="signature-box">
                            <div class="signature-line">
                                <div class="signature-text" style="height: 1px;"></div>
                            </div>
                            <small>Receiver</small>
                        </div>
                        <div class="signature-box">
                            <div class="signature-line">
                                <div class="signature-text" style="height: 1px;"></div>
                            </div>
                            <small>Accountant</small>
                        </div>
                        <div class="signature-box">
                            <div class="signature-line">
                                <div class="signature-text" style="height: 1px;" ></div>
                            </div>
                            <small>Approved by</small>
                        </div>
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
