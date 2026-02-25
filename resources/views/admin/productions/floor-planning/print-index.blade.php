<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floor Planning Print - {{ $month }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            padding: 10px;
            background: white;
        }
        @media print {
            body {
                padding: 5px;
            }
            .no-print {
                display: none !important;
            }
        }
        .print-header {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-bottom: 2px solid #333;
        }
        .print-header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .print-header h3 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .print-header p {
            margin: 3px 0;
            font-size: 11px;
            color: #666;
        }
        .btn-back {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 8px 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-back:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 3px 5px;
            text-align: center;
        }
        th {
            background: #f0f0f0 !important;
            font-weight: bold;
            font-size: 9px;
        }
        .floor-header {
            background: #e0e0e0 !important;
            font-weight: bold;
        }
        .line-cell {
            background: #e0e0e0 !important;
            font-weight: bold;
        }
        .style-cell {
            font-weight: bold;
        }
        .production-cell {
            text-align: right;
        }
        .day-column {
            min-width: 20px;
            font-size: 8px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-success {
            color: green;
        }
        .text-danger {
            color: red;
        }
        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="btn-back no-print" style="position:fixed; top:10px; right:10px; z-index:9999;">
        <button onclick="window.location.href='{{ route('admin.floorPlanning') }}'" style="padding:8px 15px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px; margin-right:5px;">
            ← Back
        </button>
        <button onclick="window.print()" style="padding:8px 15px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px;">
            🖨️ Print
        </button>
    </div>

    <div class="print-header">
        <h2>{{ general()->title ?? 'Garments Factory' }}</h2>
        <h3>Floor Planning - Daily Production Tracking</h3>
        <p>Month: {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</p>
        @if(request()->line)
        <p><strong>Line:</strong> {{ request()->line }}</p>
        @endif
        @if(request()->buyer)
        <p><strong>Buyer:</strong> {{ request()->buyer }}</p>
        @endif
        @if(request()->style_no)
        <p><strong>Style:</strong> {{ request()->style_no }}</p>
        @endif
        @if(request()->order_no)
        <p><strong>PO:</strong> {{ request()->order_no }}</p>
        @endif
        @if(request()->search)
        <p><strong>Search:</strong> {{ request()->search }}</p>
        @endif
        <p><strong>Print Date:</strong> {{ now()->format('d-m-Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="floor-header" style="width: 40px;">LINE</th>
                <th class="floor-header">BUYER</th>
                <th class="floor-header">STYLE</th>
                <th class="floor-header">COLOR</th>
                <th class="floor-header">PO NO</th>
                <th class="floor-header">ORDER QTY</th>
                <th class="floor-header">ALLOC QTY</th>
                <th class="floor-header">SHIP DATE</th>
                <th class="floor-header">TOTAL CUTTING</th>
                <th class="floor-header">TOTAL INPUT</th>
                <th class="floor-header">TOTAL PROD</th>
                <th class="floor-header">TOTAL POLY</th>
                <th class="floor-header">BALANCE</th>
                @php
                    $daysInMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->daysInMonth;
                @endphp
                @for($i = 1; $i <= $daysInMonth; $i++)
                    <th class="day-column">{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @php
                $lineGroups = [];
                $selectedLine = request()->line;

                foreach($plans as $plan) {
                    $planColors = $plan->color_name ? [$plan->color_name] : ['N/A'];

                    foreach($planColors as $color) {
                        foreach($plan->sewingLines as $line) {
                            if($selectedLine && $line->line_name != $selectedLine) {
                                continue;
                            }
                            $lineName = $line->line_name;
                            $styleKey = $plan->style_no . '|' . $color;

                            if(!isset($lineGroups[$lineName])) {
                                $lineGroups[$lineName] = [];
                            }
                            if(!isset($lineGroups[$lineName][$styleKey])) {
                                $lineGroups[$lineName][$styleKey] = [
                                    'style_no' => $plan->style_no,
                                    'color' => $color,
                                    'buyer_name' => $plan->style?->buyer_name ?? '--',
                                    'order_no' => $plan->order_no,
                                    'style_qty' => $plan->style_qty,
                                    'ship_date' => $plan->style?->ship_date,
                                    'items' => []
                                ];
                            }
                            $lineGroups[$lineName][$styleKey]['items'][] = [
                                'plan' => $plan,
                                'line' => $line
                            ];
                        }
                    }
                }
                ksort($lineGroups);
            @endphp

            @forelse($lineGroups as $lineName => $styleGroups)
                @php
                    $lineTotal = array_sum(array_map(function($g) { return count($g['items']); }, $styleGroups));
                    $lineRowIndex = 0;
                @endphp
                @foreach($styleGroups as $styleKey => $group)
                    @php
                        $styleNo = $group['style_no'];
                        $color = $group['color'];
                        $buyerName = $group['buyer_name'];
                        $orderNo = $group['order_no'];
                        $styleQty = $group['style_qty'];
                        $shipDate = $group['ship_date'];
                        $items = $group['items'];

                        $styleTotal = count($items);
                        $isStyleFirst = ($lineRowIndex === 0);

                        $totalCutting = \App\Models\Cutting::where('pi_no', $items[0]['plan']->pi_no)
                            ->where('order_no', $items[0]['plan']->order_no)
                            ->where('style_no', $items[0]['plan']->style_no)
                            ->where('color_name', $color)
                            ->sum('cutting_qty');

                        $totalInput = $totalCutting;
                        $totalPoly = \App\Models\Poly::where('pi_no', $items[0]['plan']->pi_no)
                            ->where('order_no', $items[0]['plan']->order_no)
                            ->where('style_no', $items[0]['plan']->style_no)
                            ->when($color != 'N/A', function($q) use ($color) {
                                return $q->where('color_name', $color);
                            })
                            ->sum('poly_qty');
                        $balance = $totalCutting - $totalPoly;
                    @endphp

                    @foreach($items as $itemIndex => $item)
                        @php
                            $plan = $item['plan'];
                            $line = $item['line'];

                            $totalProd = \App\Models\SewingOutput::where('planning_id', $plan->id)
                                ->where('line_name', $line->line_name)
                                ->when($color != 'N/A', function($q) use ($color) {
                                    return $q->where('color_name', $color);
                                })
                                ->sum('production');

                            $dailyProduction = [];
                            for($d = 1; $d <= $daysInMonth; $d++) {
                                $monthStart = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                                $date = $monthStart->copy()->day($d);
                                $qty = 0;
                                if ($date->format('Y-m') === $month) {
                                    $qty = \App\Models\SewingOutput::where('planning_id', $plan->id)
                                        ->where('line_name', $line->line_name)
                                        ->when($color != 'N/A', function($q) use ($color) {
                                            return $q->where('color_name', $color);
                                        })
                                        ->whereDate('created_at', $date)
                                        ->sum('production');
                                }
                                $dailyProduction[$d] = $qty;
                            }
                        @endphp
                        <tr>
                            @if($isStyleFirst && $itemIndex == 0)
                                <td rowspan="{{ $lineTotal }}" class="line-cell">
                                    {{ $lineName }}
                                </td>
                            @endif

                            @if($itemIndex == 0)
                                <td rowspan="{{ $styleTotal }}" class="style-cell">{{ $buyerName }}</td>
                                <td rowspan="{{ $styleTotal }}" class="style-cell">{{ $styleNo }}</td>
                                <td rowspan="{{ $styleTotal }}">{{ $color }}</td>
                                <td rowspan="{{ $styleTotal }}">{{ $orderNo }}</td>
                                <td rowspan="{{ $styleTotal }}" class="font-bold">{{ number_format($styleQty) }}</td>
                                <td rowspan="{{ $styleTotal }}" class="production-cell font-bold">{{ number_format($line->allocation_qty ?? 0) }}</td>
                                <td rowspan="{{ $styleTotal }}">{{ $shipDate ? \Carbon\Carbon::parse($shipDate)->format('d.m.y') : '--' }}</td>
                                <td rowspan="{{ $styleTotal }}" class="production-cell font-bold">{{ number_format($totalCutting) }}</td>
                                <td rowspan="{{ $styleTotal }}" class="production-cell font-bold">{{ number_format($totalInput) }}</td>
                            @endif

                            <td class="production-cell font-bold">{{ number_format($totalProd) }}</td>

                            @if($itemIndex == 0)
                                <td rowspan="{{ $styleTotal }}" class="production-cell">{{ number_format($totalPoly) }}</td>
                                <td rowspan="{{ $styleTotal }}" class="production-cell font-bold {{ $balance < 0 ? 'text-danger' : '' }}">{{ number_format($balance) }}</td>
                            @endif

                            @for($d = 1; $d <= $daysInMonth; $d++)
                                <td class="day-column">
                                    {{ $dailyProduction[$d] > 0 ? $dailyProduction[$d] : '-' }}
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                    @php $lineRowIndex += $styleTotal; @endphp
                @endforeach
            @empty
                <tr>
                    <td colspan="{{ 14 + $daysInMonth }}" class="text-center">No Production Data Found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        // Auto-trigger print dialog on page load
        window.onload = function() {
            // Uncomment below line to auto-print
            // window.print();
        };
    </script>
</body>
</html>
