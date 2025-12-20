@extends(adminTheme().'layouts.app')

@section('title')
<title>Proforma Invoice View</title>
@endsection

@section('contents')

<div class="flex-grow-1">

    <!-- Breadcrumb -->
    <div class="breadcrumb-area">
        <h1>Proforma Invoice</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.proformaInvoice') }}">Proforma Invoice List</a></li>
            <li class="item">View Proforma Invoice</li>
        </ol>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            @include(adminTheme().'alerts')

            <h4 class="mb-0">Proforma Invoice Details</h4>
            <div class="d-flex flex-wrap gap-3 mb-4">

                <ul class="list-group flex-grow-1">
                    <li class="list-group-item"><strong>Order No:</strong> {{ $pi->order_no }}</li>
                    <li class="list-group-item">
                        <strong>Status:</strong>
                        <span class="badge
                            @if($pi->status=='pending') badge-warning
                            @elseif($pi->status=='confirmed') badge-info
                            @elseif($pi->status=='approved') badge-success
                            @elseif($pi->status=='cancel') badge-danger
                            @endif">
                            {{ ucfirst($pi->status) }}
                        </span>
                    </li>
                    <li class="list-group-item"><strong>Created At:</strong> {{ $pi->created_at->format('d.m.Y') }}</li>
                    <li class="list-group-item"><strong>Remarks:</strong> {{ $pi->remarks ?? '--' }}</li>
                </ul>

                <ul class="list-group flex-grow-1">
                    <li class="list-group-item"><strong>Buyer Name:</strong> {{ $pi->buyer_name }}</li>
                    <li class="list-group-item"><strong>Merchant Name:</strong> {{ $pi->merchant_name }}</li>
                    <li class="list-group-item"><strong>Added By:</strong> {{ $pi->user?->name ?? '--' }}</li>
                </ul>

                <ul class="list-group flex-grow-1">
                    <li class="list-group-item"><strong>Total Qnty:</strong> {{ number_format($pi->total_qty) }}</li>
                    <li class="list-group-item"><strong>Total Bill:</strong> {{ number_format($pi->total_bill,2) }}</li>
                    <li class="list-group-item"><strong>Total Commission:</strong> {{ number_format($pi->total_commission,2) }}</li>
                </ul>

            </div>

            <h4>Proforma Invoice Items</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Composition</th>
                            <th>Fabrication</th>
                            <th>GSM</th>
                            <th>Style No</th>
                            <th>Color</th>
                            <th>Qnty</th>
                            <th>Unit Price</th>
                            <th>Unit of Measurement</th>
                            <th>Total Price</th>
                            <th>Commission Type</th>
                            <th>Commission</th>
                            <th>Total Commission</th>
                            <th>Shipment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pi->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->composition }}</td>
                            <td>{{ $item->fabrication }}</td>
                            <td>{{ $item->gsm }}</td>
                            <td>{{ $item->style_no }}</td>
                            <td>{{ $item->color_name }}</td>
                            <td>{{ number_format($item->color_qty) }}</td>
                            <td>{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->uom }}</td>
                            <td>{{ number_format($item->total_price, 2) }}</td>
                            <td>{{ ucfirst($item->commission_type) }}</td>
                            <td>{{ number_format($item->commission, 2) }}</td>
                            <td>{{ number_format($item->total_commission, 2) }}</td>
                            <td>{{ $item->shipment_date?->format('d M, Y') ?? '--' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted">No items found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="">
                        <tr>
                            <th colspan="6" class="text-right">Total</th>
                            <th>{{ number_format($pi->items->sum('color_qty')) }}</th>
                            <th></th>
                            <th>{{ number_format($pi->items->sum('total_price'), 2) }}</th>
                            <th></th>
                            <th></th>
                            <th>{{ number_format($pi->items->sum('total_commission'), 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>


</div>

@endsection
