@extends('printMaster')

@section('title', 'Yarn Booking – ' . $booking->getBookingNo())

@push('css')
<style>
    /* ── Meta info block (no border, compact) ── */
    .yb-meta table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
        border: none;
    }
    .yb-meta td {
        border: none !important;
        padding: 2px 4px;
        font-size: 12px;
        vertical-align: top;
        background: transparent !important;
    }
    .yb-meta .lbl {
        font-weight: bold;
        white-space: nowrap;
        width: 120px;
    }
    .yb-meta .date-cell {
        text-align: right;
        font-weight: bold;
        white-space: nowrap;
    }

    /* ── Subject line ── */
    .yb-subject {
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        margin: 8px 0 12px 0;
        border-bottom: 1px solid #555;
        padding-bottom: 4px;
    }

    /* ── Override printMaster default table padding for this view ── */
    .yb-table th,
    .yb-table td {
        padding: 5px 6px;
        font-size: 12px;
        vertical-align: middle;
        text-align: center;
    }
    .yb-table td.left-text {
        text-align: left;
    }
    .yb-table tfoot td {
        font-weight: bold;
        background: #f9f9f9 !important;
    }

    /* ── Signature spacing override ── */
    .print-footer {
        margin-top: 70px !important;
        display: flex !important;
        justify-content: space-between !important;
        gap: 58px !important;
        width: 100% !important;
    }
    .print-footer .signature-box {
        flex: 1 1 0;
        width: auto !important;
        padding: 0 10px;
    }
    .print-footer .signature-line {
        margin-top: 35px !important;
    }
</style>
@endpush

@section('contents')

@php
    $pi          = $booking->pi;
    $piNo        = $pi?->pi_no ?? '--';
    $buyerName   = $booking->pi?->buyer_name ?? '--';
    $supplier    = $booking->supplier ?? '--';
    $attnName    = $createdByUser?->name ?? '--';
    $bookingDate = \Carbon\Carbon::parse($booking->created_at)->format('d-M-y');
    $signatures  = ['Knitting & Deying', 'Merchendiser', 'Director', 'Managing Director'];
@endphp

{{-- ════════ META INFO ════════ --}}
<div class="yb-meta">
    <table>
        <tr>
            <td class="lbl">PI NO</td>
            <td>{{ $piNo }}</td>
            <td rowspan="3" class="date-cell">Booking Date :&nbsp; {{ $bookingDate }}</td>
        </tr>
        <tr>
            <td class="lbl">To :</td>
            <td>{{ $supplier }}</td>
        </tr>
        <tr>
            <td class="lbl">Attn.</td>
            <td>{{ $attnName }}</td>
        </tr>
    </table>
</div>

{{-- ════════ SUBJECT ════════ --}}
<div class="yb-subject">
    SUBJECT :&nbsp; YARN REVISE BOOKING FOR THE BUYER {{ strtoupper($buyerName) }}
</div>

{{-- ════════ BUILD PRINT ROWS WITH ROWSPAN ════════ --}}
@php
    $printRows = [];

    foreach ($items as $item) {
        $yarnCounts = json_decode($item->yarn_count, true) ?: [];
        if (empty($yarnCounts)) {
            $yarnCounts = [['count' => '--', 'qty' => floatval($item->required_qty)]];
        }

        $composition = '--';
        $orderItem = $item->getOrderItem();
        if ($orderItem && $orderItem->orderDetail) {
            $composition = $orderItem->orderDetail->items
                ->pluck('composition')
                ->filter()
                ->unique()
                ->implode(', ');

            if ($composition === '') {
                $composition = '--';
            }
        }

        foreach ($yarnCounts as $yarn) {
            $printRows[] = [
                'buyer'      => $item->buyer_name ?? $buyerName,
                'po_no'      => $item->order_no ?? '--',
                'style'      => $item->style ?? '--',
                'fabric'     => $item->fabric_type ?? '--',
                'composition' => $composition,
                'yarn_count' => $yarn['count'] ?? '--',
                'qty'        => floatval($yarn['qty'] ?? 0),
                'remarks'    => $item->remarks ?? '',
            ];
        }
    }

    /* ── Compute rowspans ── */
    $bpKey = fn($r) => $r['buyer'] . '||' . $r['po_no'];
    $stKey = fn($r) => $r['buyer'] . '||' . $r['po_no'] . '||' . $r['style'];
    $fbKey = fn($r) => $r['buyer'] . '||' . $r['po_no'] . '||' . $r['style'] . '||' . $r['fabric'];

    $n       = count($printRows);
    $bpSpan  = array_fill(0, $n, 0);
    $stSpan  = array_fill(0, $n, 0);
    $fbSpan  = array_fill(0, $n, 0);

    // buyer+po spans
    $i = 0;
    while ($i < $n) {
        $j = $i;
        while ($j < $n && $bpKey($printRows[$j]) === $bpKey($printRows[$i])) { $j++; }
        $bpSpan[$i] = $j - $i;
        $i = $j;
    }

    // style spans (within each bp group)
    $i = 0;
    while ($i < $n) {
        $bpEnd = $i;
        while ($bpEnd < $n && $bpKey($printRows[$bpEnd]) === $bpKey($printRows[$i])) { $bpEnd++; }

        $k = $i;
        while ($k < $bpEnd) {
            $m = $k;
            while ($m < $bpEnd && $stKey($printRows[$m]) === $stKey($printRows[$k])) { $m++; }
            $stSpan[$k] = $m - $k;
            $k = $m;
        }
        $i = $bpEnd;
    }

    // fabric spans (within each style group)
    $i = 0;
    while ($i < $n) {
        $stEnd = $i;
        while ($stEnd < $n && $stKey($printRows[$stEnd]) === $stKey($printRows[$i])) { $stEnd++; }

        $k = $i;
        while ($k < $stEnd) {
            $m = $k;
            while ($m < $stEnd && $fbKey($printRows[$m]) === $fbKey($printRows[$k])) { $m++; }
            $fbSpan[$k] = $m - $k;
            $k = $m;
        }
        $i = $stEnd;
    }

    $grandTotal = array_sum(array_column($printRows, 'qty'));
@endphp

<table class="yb-table">
    <thead>
        <tr>
            <th style="width:42px">SI No.</th>
            <th style="width:90px">Buyer</th>
            <th style="width:130px">PO NUMBER</th>
            <th style="width:130px">Style</th>
            <th>Fabrics composition</th>
            <th style="width:130px">Composition</th>
            <th style="width:80px">Yarn count</th>
            <th style="width:105px">Quantity (Kgs)</th>
            <th style="width:80px">REMARKS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($printRows as $idx => $row)
        <tr>
            <td>{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>

            @if($bpSpan[$idx] > 0)
                <td rowspan="{{ $bpSpan[$idx] }}">{{ $row['buyer'] }}</td>
                <td rowspan="{{ $bpSpan[$idx] }}">{{ $row['po_no'] }}</td>
            @endif

            @if($stSpan[$idx] > 0)
                <td rowspan="{{ $stSpan[$idx] }}">{{ $row['style'] }}</td>
            @endif

            @if($fbSpan[$idx] > 0)
                <td class="left-text" rowspan="{{ $fbSpan[$idx] }}">{{ $row['fabric'] }}</td>
            @endif
            <td>{{ $row['composition'] }}</td>
            <td>{{ $row['yarn_count'] }}</td>
            <td>{{ number_format($row['qty'], 2) }} Kgs</td>
            <td class="left-text">{{ $row['remarks'] }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" style="text-align:center; font-weight:bold;">TOTAL</td>
            <td style="text-align:center; font-weight:bold;">{{ number_format($grandTotal, 2) }} KGS</td>
            <td></td>
        </tr>
    </tfoot>
</table>

@endsection
