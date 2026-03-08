@extends('printMaster')
@section('title', 'Daily Production Report - ' . \Carbon\Carbon::parse($startDate)->format('d-m-Y'))
@section('contents')
    @php
        $maxWorkingTime = $swings->count()
            ? $swings->flatten()->pluck('working_hours')->max()
            : 9;
        $startHour = 8;
        $endHour = $startHour + $maxWorkingTime;
        
        $sum_target = 0;
        $sum_today = 0;
        $sum_previous = 0;
        $sum_grand = 0;
        $sum_working_min = 0;
        $sum_prod_min = 0;
    @endphp
    
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 40px;">SL</th>
                <th rowspan="2">Line</th>
                <th rowspan="2">Buyer</th>
                <th rowspan="2">Order</th>
                <th rowspan="2">Order Qty</th>
                <th rowspan="2">Style</th>
                <th rowspan="2">Color</th>
                <th rowspan="2">Color Qty</th>
                <th rowspan="2">Alloc Qty</th>
                <th rowspan="2">Target</th>
                <th rowspan="2">Hour</th>
                <th rowspan="2">SMV</th>
                <th rowspan="2">Operator</th>
                <th rowspan="2">Helper</th>
                <th rowspan="2">Manpower</th>
                
                @for($h=$startHour; $h<$endHour; $h++)
                    @php
                        $start = ($h > 12) ? $h - 12 : $h;
                        $endH = $h + 1;
                        $end = ($endH > 12) ? $endH - 12 : $endH;
                        $endPeriod = $endH < 12 ? 'AM' : 'PM';
                    @endphp
                    <th>{{ $start }}-{{ $end }} {{ $endPeriod }}</th>
                @endfor
                
                <th rowspan="2">Today</th>
                <th rowspan="2">Previous</th>
                <th rowspan="2">Grand</th>
                <th rowspan="2">Balance</th>
                <th rowspan="2">Work Min</th>
                <th rowspan="2">Prod Min</th>
                <th rowspan="2">Efficiency</th>
            </tr>
        </thead>
        <tbody>
            @php $serial = 1; @endphp
            
            @forelse($floorLines as $line)
                @php
                    $lineKey = $line['floor'].' - '.$line['line'];
                    $lineSwings = $swings[$lineKey] ?? collect();
                @endphp

                @if($lineSwings->count())
                    @foreach($lineSwings as $swing)
                        @php
                            $buyerName = $swing?->planning?->style?->buyer_name ?? '--';
                            $styleNo = $swing->style_no ?? '--';
                            $orderNo = $swing?->planning?->order_no ?? '--';
                            $orderQty = $swing?->planning?->style_qty ?? 0;
                            $colorName = $swing->color_name ?? '--';
                            $colorQty = $swing?->planning?->color_qty ?? 0;
                            $allocQty = $swing->allocation_qty ?? 0;
                            $target = $swing->capacity_hour ?? 0;
                            $hours = $swing->working_hours ?? 0;
                            $smv = $swing->smb ?? 0;
                            $operators = $swing->operators ?? 0;
                            $helpers = $swing->helpers ?? 0;
                            $manpower = $operators + $helpers;
                            
                            $today_total = 0;
                            for($h=$startHour; $h<$startHour + $swing->working_hours; $h++){
                                if(!$swing->isBreakHour($h)){
                                    $today_total += $swing->getProductionHour($h, $today_date);
                                }
                            }
                            
                            $previous_total = $swing->outputs()
                                ->where('date','<',$today_date)
                                ->sum('production');
                            
                            $grand_total = $today_total + $previous_total;
                            
                            $sum_target += $target;
                            $sum_today += $today_total;
                            $sum_previous += $previous_total;
                            $sum_grand += $grand_total;
                            
                            $totalWorkingMinutes = $manpower * $hours * 60;
                            $totalProductionMinutes = $today_total * $smv;
                            $efficiency = $totalWorkingMinutes > 0 
                                ? round(($totalProductionMinutes / $totalWorkingMinutes) * 100, 1) 
                                : 0;
                                
                            $sum_working_min += $totalWorkingMinutes;
                            $sum_prod_min += $totalProductionMinutes;
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ $serial++ }}</td>
                            <td>{{ $lineKey }}</td>
                            <td>{{ $buyerName }}</td>
                            <td>{{ $orderNo }}</td>
                            <td style="text-align: right;">{{ number_format($orderQty) }}</td>
                            <td>{{ $styleNo }}</td>
                            <td>{{ $colorName }}</td>
                            <td style="text-align: right;">{{ number_format($colorQty) }}</td>
                            <td style="text-align: right;">{{ number_format($allocQty) }}</td>
                            <td style="text-align: right;">{{ number_format($target) }}</td>
                            <td style="text-align: center;">{{ $hours }}</td>
                            <td style="text-align: right;">{{ $smv }}</td>
                            <td style="text-align: center;">{{ $operators }}</td>
                            <td style="text-align: center;">{{ $helpers }}</td>
                            <td style="text-align: center;">{{ $manpower }}</td>
                            
                            @for($h=$startHour; $h<$endHour; $h++)
                                @if($h > $startHour + $swing->working_hours - 1)
                                    <td style="background: #f0f0f0; text-align: center;">-</td>
                                @elseif($swing->isBreakHour($h))
                                    <td style="background: #ffebee; text-align: center; color: red;">B</td>
                                @else
                                    @php
                                        $value = $swing->getProductionHour($h, $today_date);
                                    @endphp
                                    <td style="text-align: center;">{{ $value > 0 ? $value : '-' }}</td>
                                @endif
                            @endfor
                            
                            <td style="text-align: right; font-weight: bold;">{{ number_format($today_total) }}</td>
                            <td style="text-align: right;">{{ number_format($previous_total) }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($grand_total) }}</td>
                            <td style="text-align: right; color: red;">{{ number_format($orderQty - $grand_total) }}</td>
                            <td style="text-align: right;">{{ number_format($totalWorkingMinutes) }}</td>
                            <td style="text-align: right;">{{ number_format($totalProductionMinutes) }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $efficiency }}%</td>
                        </tr>
                    @endforeach
                @else
                    <tr style="background: #f8f9fa;">
                        <td>-</td>
                        <td>{{ $lineKey }}</td>
                        <td colspan="{{ 10 + $maxWorkingTime + 8 }}" class="text-center text-muted">No Production Running</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="{{ 15 + $maxWorkingTime + 8 }}" class="text-center">No Production Data Found</td>
                </tr>
            @endforelse
            
            @php
                $sum_efficiency = $sum_working_min > 0 ? round(($sum_prod_min / $sum_working_min) * 100, 1) : 0;
            @endphp
            
            @if($serial > 1)
            <tr style="background-color: #e8e8e8; font-weight: bold;">
                <td colspan="2">Total</td>
                <td>Lines: {{ $swings->count() }}</td>
                <td colspan="2"></td>
                <td style="text-align: right;">{{ number_format($sum_target) }}</td>
                <td colspan="3"></td>
                <td colspan="{{ $maxWorkingTime + 6 }}"></td>
                <td style="text-align: right;">{{ number_format($sum_today) }}</td>
                <td style="text-align: right;">{{ number_format($sum_previous) }}</td>
                <td style="text-align: right;">{{ number_format($sum_grand) }}</td>
                <td style="text-align: right;">-</td>
                <td style="text-align: right;">{{ number_format($sum_working_min) }}</td>
                <td style="text-align: right;">{{ number_format($sum_prod_min) }}</td>
                <td style="text-align: center;">{{ $sum_efficiency }}%</td>
            </tr>
            @endif
        </tbody>
    </table>
@endsection
