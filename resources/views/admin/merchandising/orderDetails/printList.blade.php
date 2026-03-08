@extends('printMaster')
@section('title', 'Order Details')
@section('contents')
<div class="print-container">

    @if($orderDetails)

    @php
        $grandOrderQty = 0;
        $grandColorQty = 0;
        $totalOrders   = $orderDetails->count();
        $totalColors   = 0;

        foreach ($orderDetails as $order) {
            $grandOrderQty += $order->total_qty ?? 0;
            $totalColors   += $order->items->count();

            foreach ($order->items as $item) {
                $grandColorQty += $item->qty ?? 0;
            }
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Buyer</th>
                <th>Brand/Customer</th>
                <th>Style No</th>
                <th>Order / PO</th>
                <th>Shipment</th>
                <th>Composition</th>
                <th>Fabrication</th>
                <th>GSM</th>
                <th>Color</th>
                <th class="text-right">Color Qnty</th>
                <th class="text-right">Order Qnty</th>
            </tr>
        </thead>

        <tbody>
            @php $sl = 1; @endphp

            @foreach($orderDetails as $order)
                @php
                    $itemCount = $order->items->count();
                @endphp

                @foreach($order->items as $i => $item)
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $order->buyer_name ?? '--' }}</td>
                        <td>{{ $order->company_name ?? '--' }}</td>
                        <td>{{ $order->style_no ?? '--' }}</td>
                        <td>{{ $order->order_no ?? '--' }}</td>
                        <td>{{ $order->shipment_date ? \Carbon\Carbon::parse($order->shipment_date)->format('d.m.Y') : '--' }}</td>
                        <td>{{ $item->composition ?? '--' }}</td>
                        <td>{{ $order->fabrication ?? '--' }}</td>
                        <td>{{ $item->gsm ?? '--' }}</td>
                        <td>{{ $item->color_name ?? '--' }}</td>
                        <td class="text-right">{{ number_format($item->qty) }}</td>

                        {{-- Order Total Qnty (rowspan) --}}
                        @if($i === 0)
                            <td class="text-right" rowspan="{{ $itemCount }}" style="vertical-align: middle; font-weight:600;">
                                {{ number_format($order->total_qty) }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#f8f8f8; font-weight:600;">
                <td colspan="5">Total Orders</td>
                <td colspan="2">{{ $totalOrders }}</td>

                <td colspan="2">Total Colors</td>
                <td colspan="2">{{ $totalColors }}</td>

                <td class="text-right">{{ number_format($grandOrderQty) }}</td>
            </tr>
        </tfoot>

    </table>


    @else
    <span>No Data Found</span>
    @endif



@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{asset('admin/assets/js/inword.js')}}"></script>
<script>

    var amount = Number($('#total_amount_input').val());
    console.log(amount);
    var words = toWords(amount);
    $('#total_amount_word').html(words + ' Taka Only');

</script>
@endpush