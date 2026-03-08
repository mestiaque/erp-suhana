
@extends('printMaster')
@section('title', 'Floor Planning - ' . \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y'))
@section('contents')
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
@endsection
