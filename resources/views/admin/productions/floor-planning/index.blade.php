@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Floor Planning List') }}</title>
@endsection

@push('css')
<style type="text/css">
    @media (max-width: 1400px) {
        table tr td, table tr th {
            font-size: 9px;
            padding: 2px 1px;
        }
        .table thead th {
            font-size: 8px;
            white-space: nowrap;
        }
    }
    .floor-table {
        width: 100%;
        overflow-x: auto;
        display: block;
    }
    .production-cell {
        text-align: right;
        min-width: 40px;
    }
    .day-column {
        min-width: 35px;
        text-align: center;
    }
    .floor-header {
        font-weight: bold;
    }
    .line-main-row {
        background-color: #f8f9fa;
    }
    .style-sub-row {
    }
    .line-group-header {
        font-weight: bold;
    }
    .merged-row {
        vertical-align: middle;
    }
    .color-badge {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 2px;
        margin-right: 5px;
        vertical-align: middle;
        border: 1px solid #ddd;
    }
    .style-cell {
        font-weight: bold;
        vertical-align: middle;
    }
    .line-cell {
        font-weight: bold;
        vertical-align: middle;
        background-color: #e9ecef;
    }
    .smb-cell, .operator-cell, .helper-cell {
        text-align: center;
    }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Floor Planning - Daily Production Tracking</h3>
             <div class="dropdown d-flex">
                 <a href="{{ route('admin.floorPlanningAction', 'create') }}" class="btn-custom primary mr-1" style="padding:5px 15px;">
                     <i class="bx bx-plus"></i> Add Planning
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')
            <form action="{{ route('admin.floorPlanning') }}">
                <div class="row mb-2">
                    <div class="col-md-2 mb-1">
                        <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month', $month) }}">
                    </div>
                    <div class="col-md-2 mb-1">
                        <select name="line" class="form-control form-control-sm">
                            <option value="">All Lines</option>
                            @foreach($lineOptions as $line)
                                <option value="{{ $line->slug }}" {{ request()->line == $line->slug ? 'selected' : '' }}>Line - {{ $line->slug }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-1">
                        <select name="buyer" class="form-control form-control-sm">
                            <option value="">All Buyers</option>
                            @foreach($buyers as $buyer)
                                <option value="{{ $buyer }}" {{ request()->buyer == $buyer ? 'selected' : '' }}>{{ $buyer }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-1">
                        <select name="style_no" class="form-control form-control-sm">
                            <option value="">All Styles</option>
                            @foreach($styleNos as $styleNo)
                                <option value="{{ $styleNo }}" {{ request()->style_no == $styleNo ? 'selected' : '' }}>{{ $styleNo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-1">
                        <select name="order_no" class="form-control form-control-sm">
                            <option value="">All PO</option>
                            @foreach($orderNos as $orderNo)
                                <option value="{{ $orderNo }}" {{ request()->order_no == $orderNo ? 'selected' : '' }}>{{ $orderNo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search Style, Buyer, PO" class="form-control form-control-sm" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                            <a href="{{ route('admin.floorPlanning') }}" class="btn btn-warning btn-sm">Reset</a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="floor-table">
                <table class="table table-striped table-bordered table-sm">
                    <thead>
                        <tr>
                            <th class="floor-header" style="width: 50px;">LINE</th>
                            <th class="floor-header">BUYER</th>
                            <th class="floor-header">STYLE</th>
                            <th class="floor-header">COLOR</th>
                            <th class="floor-header">PO NO</th>
                            <th class="floor-header">ORDER QTY</th>
                            <th class="floor-header">SMB</th>
                            <th class="floor-header">OPR</th>
                            <th class="floor-header">HELPER</th>
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
                                <th class="day-column">{{ $i }} {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->shortMonthName }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Group productions by line and style
                            $lineGroups = [];
                            $selectedLine = request()->line;

                            // First, organize data: line -> style+color -> items
                            foreach($plans as $plan) {
                                $planColors = $plan->color_name ? [$plan->color_name] : ['N/A'];

                                foreach($planColors as $color) {
                                    foreach($plan->sewingLines as $line) {
                                        // If line filter is active, only show that line
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

                            // Calculate rowspans for merged columns
                            $styleRowspans = [];
                            foreach($lineGroups as $lineName => $styleGroups) {
                                $styleRowspans[$lineName] = [];
                                $currentStyleKey = null;
                                $currentStyleCount = 0;
                                $styleFirstIndices[$lineName] = [];

                                $idx = 0;
                                foreach($styleGroups as $styleKey => $group) {
                                    $count = count($group['items']);
                                    $styleFirstIndices[$lineName][$styleKey] = $idx;
                                    $idx += $count;
                                }
                            }
                        @endphp

                        @forelse($lineGroups as $lineName => $styleGroups)
                            @php
                                $lineTotal = array_sum(array_map(function($g) { return count($g['items']); }, $styleGroups));
                                $lineFirst = true;
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

                                    // Get production data for this style+color
                                    $totalCutting = \App\Models\Cutting::where('pi_no', $items[0]['plan']->pi_no)
                                        ->where('order_no', $items[0]['plan']->order_no)
                                        ->where('style_no', $items[0]['plan']->style_no)
                                        ->where('color_name', $color)
                                        ->sum('cutting_qty');
                                        // dump($color, $totalCutting);

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

                                        // Get daily production
                                        $dailyProduction = [];
                                        for($d = 1; $d <= $daysInMonth; $d++) {
                                            $monthStart = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                                            $date = $monthStart->copy()->day($d);
                                            // Only count as 'today' if $date is today AND $date is in the selected month
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
                                    <tr class="{{ $isStyleFirst && $itemIndex == 0 ? 'line-main-row merged-row' : 'style-sub-row merged-row' }}">
                                        @if($isStyleFirst && $itemIndex == 0)
                                            <td rowspan="{{ $lineTotal }}" class="line-cell">
                                                {{ $lineName }}
                                            </td>
                                        @endif

                                        @if($itemIndex == 0)
                                            <!-- BUYER - rowspan by style+color group (all lines for this style) -->
                                            <td rowspan="{{ $styleTotal }}" class="style-cell">{{ $buyerName }}</td>

                                            <!-- STYLE - rowspan by style+color group -->
                                            <td rowspan="{{ $styleTotal }}" class="style-cell">{{ $styleNo }}</td>

                                            <!-- COLOR - rowspan by style+color group -->
                                            <td rowspan="{{ $styleTotal }}">
                                                {{-- <span class="color-badge" style="background-color: {{ $color != 'N/A' ? $color : '#ccc' }};"></span> --}}
                                                {{ $color }}
                                            </td>

                                            <!-- PO NO - rowspan by style+color group -->
                                            <td rowspan="{{ $styleTotal }}">{{ $orderNo }}</td>

                                            <!-- ORDER QTY - rowspan by style+color group -->
                                            <td rowspan="{{ $styleTotal }}" class="font-weight-bold">{{ number_format($styleQty) }}</td>

                                            <!-- SHIP DATE - rowspan by style+color group -->
                                            <td rowspan="{{ $styleTotal }}">{{ $shipDate ? \Carbon\Carbon::parse($shipDate)->format('d.m.y') : '--' }}</td>
                                            
                                            <!-- SMB -->
                                            <td rowspan="{{ $styleTotal }}" class="smb-cell">{{ $line->smb ?? '--' }}</td>
                                            
                                            <!-- OPERATORS -->
                                            <td rowspan="{{ $styleTotal }}" class="operator-cell">{{ $line->operators ?? '--' }}</td>
                                            
                                            <!-- HELPERS -->
                                            <td rowspan="{{ $styleTotal }}" class="helper-cell">{{ $line->helpers ?? '--' }}</td>

                                            <!-- TOTAL CUTTING - rowspan by style+color group -->
                                            <td rowspan="{{ $styleTotal }}" class="production-cell font-weight-bold">{{ number_format($totalCutting) }}</td>

                                            <!-- TOTAL INPUT - rowspan by style+color group -->
                                            <td rowspan="{{ $styleTotal }}" class="production-cell font-weight-bold">{{ number_format($totalInput) }}</td>
                                        @endif

                                        <!-- TOTAL PROD - per line -->
                                        <td class="production-cell font-weight-bold {{ $totalProd > 0 ? 'text-success' : '' }}">{{ number_format($totalProd) }}</td>

                                        @if($itemIndex == 0)
                                            <!-- TOTAL POLY - rowspan by style+color group -->
                                            <td rowspan="{{ $styleTotal }}" class="production-cell">{{ number_format($totalPoly) }}</td>

                                            <!-- BALANCE - rowspan by style+color group -->
                                            <td rowspan="{{ $styleTotal }}" class="production-cell font-weight-bold {{ $balance < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($balance) }}</td>
                                        @endif

                                        <!-- Daily Columns -->
                                        @for($d = 1; $d <= $daysInMonth; $d++)
                                            <td class="day-column {{ $dailyProduction[$d] > 0 ? 'text-success font-weight-bold' : '' }}">
                                                {{ $dailyProduction[$d] > 0 ? $dailyProduction[$d] : '-' }}
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                                @php $lineRowIndex += $styleTotal; @endphp
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="{{ 15 + $daysInMonth }}" class="text-center text-muted">No Production Data Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
