@extends('printMaster')
@section('title', 'Production Planning')
@section('contents')
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle; width: 40px;">SL</th>
                <th colspan="5" class="text-center">Style Details</th>
                <th colspan="2" class="production-header">Cutting</th>
                <th colspan="4" class="production-header">Sewing</th>
                <th colspan="4" class="production-header">Finishing Receive</th>
                <th colspan="2" class="production-header">Iron</th>
                <th colspan="2" class="production-header">Poly</th>
                <th rowspan="2" style="vertical-align: middle;">Balance</th>
                <th rowspan="2" style="vertical-align: middle; " class="text-center">Status</th>
            </tr>
            <tr>
                <th style="vertical-align: middle;">Buyer</th>
                <th style="vertical-align: middle;">Style</th>
                <th style="vertical-align: middle;">Color</th>
                <th style="vertical-align: middle;">PO No</th>
                <th style="vertical-align: middle;">Order Qty</th>
                <!-- Cutting: Today In, Total In (no output - input = output) -->
                <th>Today</th>
                <th>Total</th>
                <!-- Sewing -->
                <th>Today In</th>
                <th>Total In</th>
                <th>Today Out</th>
                <th>Total Out</th>
                <!-- Finishing -->
                <th>Today In</th>
                <th>Total In</th>
                <th>Today Out</th>
                <th>Total Out</th>
                <!-- Iron -->
                <th>Today</th>
                <th>Total</th>
                <!-- Poly -->
                <th>Today</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productions as $i => $plan)
            @php
                $filterMonth = $month;
                $todayMonth = now()->format('Y-m');
                $showToday = $filterMonth === $todayMonth;

                $cuttingTodayIn = 0;
                if ($showToday) {
                    $cuttingTodayIn = \App\Models\Cutting::where('pi_no', $plan->pi_no)
                        ->where('order_no', $plan->order_no)
                        ->where('style_no', $plan->style_no)
                        ->where('color_name', $plan->color_name)
                        ->whereDate('cutting_date', today())
                        ->sum('cutting_qty');
                }
                $cuttingTotalIn = \App\Models\Cutting::where('pi_no', $plan->pi_no)
                    ->where('order_no', $plan->order_no)
                    ->where('style_no', $plan->style_no)
                    ->where('color_name', $plan->color_name)
                    ->sum('cutting_qty');

                $sewingTodayIn = 0;
                if ($showToday) {
                    $sewingTodayIn = \App\Models\SewingOutput::where('planning_id', $plan->id)
                        ->whereDate('created_at', today())
                        ->sum('production');
                }
                $sewingTotalIn = \App\Models\SewingOutput::where('planning_id', $plan->id)
                    ->sum('production');
                $sewingTodayOut = $sewingTodayIn;
                $sewingTotalOut = $sewingTotalIn;

                $finishingTodayIn = 0;
                if ($showToday) {
                    $finishingTodayIn = \App\Models\Finishing::where('pi_no', $plan->pi_no)
                        ->where('order_no', $plan->order_no)
                        ->where('style_no', $plan->style_no)
                        ->where('color_name', $plan->color_name)
                        ->whereDate('finishing_date', today())
                        ->sum('finishing_qty');
                }
                $finishingTotalIn = \App\Models\Finishing::where('pi_no', $plan->pi_no)
                    ->where('order_no', $plan->order_no)
                    ->where('style_no', $plan->style_no)
                    ->where('color_name', $plan->color_name)
                    ->sum('finishing_qty');
                $finishingTodayOut = $finishingTodayIn;
                $finishingTotalOut = $finishingTotalIn;

                $ironToday = 0;
                if ($showToday) {
                    $ironToday = \App\Models\Iron::where('pi_no', $plan->pi_no)
                        ->where('order_no', $plan->order_no)
                        ->where('style_no', $plan->style_no)
                        ->where('color_name', $plan->color_name)
                        ->whereDate('iron_date', today())
                        ->sum('iron_qty');
                }
                $ironTotal = \App\Models\Iron::where('pi_no', $plan->pi_no)
                    ->where('order_no', $plan->order_no)
                    ->where('style_no', $plan->style_no)
                    ->where('color_name', $plan->color_name)
                    ->sum('iron_qty');

                $polyToday = 0;
                if ($showToday) {
                    $polyToday = \App\Models\Poly::where('pi_no', $plan->pi_no)
                        ->where('order_no', $plan->order_no)
                        ->where('style_no', $plan->style_no)
                        ->where('color_name', $plan->color_name)
                        ->whereDate('poly_date', today())
                        ->sum('poly_qty');
                }
                $polyTotal = \App\Models\Poly::where('pi_no', $plan->pi_no)
                    ->where('order_no', $plan->order_no)
                    ->where('style_no', $plan->style_no)
                    ->where('color_name', $plan->color_name)
                    ->sum('poly_qty');

                $balance = $cuttingTotalIn - $polyTotal;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $plan->style?->buyer_name ?? '--' }}</td>
                <td>{{ $plan->style_no }}</td>
                <td>{{ $plan->color_name ?? '--' }}</td>
                <td>{{ $plan->order_no }}</td>
                <td class="font-bold">{{ number_format($plan->color_qty ?? $plan->style_qty) }}</td>

                <!-- Cutting -->
                <td class="production-cell">{{ $cuttingTodayIn > 0 ? number_format($cuttingTodayIn) : '-' }}</td>
                <td class="production-cell font-bold">{{ number_format($cuttingTotalIn) }}</td>

                <!-- Sewing -->
                <td class="production-cell">{{ $sewingTodayIn > 0 ? number_format($sewingTodayIn) : '-' }}</td>
                <td class="production-cell font-bold">{{ number_format($sewingTotalIn) }}</td>
                <td class="production-cell">{{ $sewingTodayOut > 0 ? number_format($sewingTodayOut) : '-' }}</td>
                <td class="production-cell font-bold">{{ number_format($sewingTotalOut) }}</td>

                <!-- Finishing -->
                <td class="production-cell">{{ $finishingTodayIn > 0 ? number_format($finishingTodayIn) : '-' }}</td>
                <td class="production-cell font-bold">{{ number_format($finishingTotalIn) }}</td>
                <td class="production-cell">{{ $finishingTodayOut > 0 ? number_format($finishingTodayOut) : '-' }}</td>
                <td class="production-cell font-bold">{{ number_format($finishingTotalOut) }}</td>

                <!-- Iron -->
                <td class="production-cell">{{ $ironToday > 0 ? number_format($ironToday) : '-' }}</td>
                <td class="production-cell font-bold">{{ number_format($ironTotal) }}</td>

                <!-- Poly -->
                <td class="production-cell">{{ $polyToday > 0 ? number_format($polyToday) : '-' }}</td>
                <td class="production-cell font-bold">{{ number_format($polyTotal) }}</td>

                <!-- Balance -->
                <td class="production-cell font-bold {{ $balance < 0 ? 'text-danger' : '' }}">{{ number_format($balance) }}</td>

                <!-- Status -->
                <td>
                    @if($plan->masterPlan->status == 'pending')
                        <span class="badge badge-warning">Pending</span>
                    @elseif($plan->masterPlan->status == 'confirmed')
                        <span class="badge badge-info">Confirmed</span>
                    @elseif($plan->masterPlan->status == 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif($plan->masterPlan->status == 'cancelled')
                        <span class="badge badge-danger">Cancelled</span>
                    @else
                        <span class="badge badge-secondary">{{ ucfirst($plan->masterPlan->status) }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="22" class="text-center">No Production Planning Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endsection
