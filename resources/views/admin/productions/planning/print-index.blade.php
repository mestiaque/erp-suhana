<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Planning Print - {{ $month }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px;
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
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 2px 3px;
            text-align: center;
            font-size: 9px;
        }
        th {
            background: #f0f0f0 !important;
            font-weight: bold;
        }
        .production-cell {
            text-align: right;
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
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
        }
        .badge-warning { background: #ffc107; }
        .badge-info { background: #17a2b8; color: white; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-secondary { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="no-print" style="position:fixed; top:10px; right:10px; z-index:9999;">
        <button onclick="window.location.href='{{ route('admin.productionPlanning') }}'" style="padding:8px 15px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px; margin-right:5px;">
            ← Back
        </button>
        <button onclick="window.print()" style="padding:8px 15px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px;">
            🖨️ Print
        </button>
    </div>

    <div class="print-header">
        <h2>{{ general()->title ?? 'Garments Factory' }}</h2>
        <h3>Production Planning List</h3>
        <p>Month: {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</p>
        @if(request()->pi_no)
        <p><strong>PI:</strong> {{ request()->pi_no }}</p>
        @endif
        @if(request()->buyer)
        <p><strong>Buyer:</strong> {{ request()->buyer }}</p>
        @endif
        @if(request()->order_no)
        <p><strong>PO:</strong> {{ request()->order_no }}</p>
        @endif
        @if(request()->style_no)
        <p><strong>Style:</strong> {{ request()->style_no }}</p>
        @endif
        @if(request()->status)
        <p><strong>Status:</strong> {{ request()->status }}</p>
        @endif
        @if(request()->search)
        <p><strong>Search:</strong> {{ request()->search }}</p>
        @endif
        <p><strong>Print Date:</strong> {{ now()->format('d-m-Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle; width: 40px;">SL</th>
                <th colspan="5" class="text-center">Style Details</th>
                <th colspan="2" class="production-header">Cutting</th>
                <th colspan="4" class="production-header">Sewing</th>
                <th colspan="4" class="production-header">Finishing</th>
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
                <th>Today Out</th>
                <th>Total In</th>
                <th>Total Out</th>
                <!-- Finishing -->
                <th>Today In</th>
                <th>Today Out</th>
                <th>Total In</th>
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
                <td class="production-cell">{{ $sewingTodayOut > 0 ? number_format($sewingTodayOut) : '-' }}</td>
                <td class="production-cell font-bold">{{ number_format($sewingTotalIn) }}</td>
                <td class="production-cell font-bold">{{ number_format($sewingTotalOut) }}</td>

                <!-- Finishing -->
                <td class="production-cell">{{ $finishingTodayIn > 0 ? number_format($finishingTodayIn) : '-' }}</td>
                <td class="production-cell">{{ $finishingTodayOut > 0 ? number_format($finishingTodayOut) : '-' }}</td>
                <td class="production-cell font-bold">{{ number_format($finishingTotalIn) }}</td>
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
</body>
</html>
