@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Production Planning List') }}</title>
@endsection

@push('css')
<style type="text/css">
    @media (max-width: 1400px) {
        table tr td, table tr th {
            font-size: 10px;
            padding: 3px 2px;
        }
        .table thead th {
            font-size: 9px;
            white-space: nowrap;
        }
    }
    .production-cell {
        min-width: 50px;
        text-align: right;
    }
    .production-header {
        text-align: center;
        font-size: 10px;
    }
    .stage-group {
        background-color: #f8f9fa;
    }
    td {
        vertical-align: middle !important;
    }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Production Planning List</h3>
             <div class="dropdown d-flex">
                 <a href="{{ route('admin.productionPlanning') }}?month={{ request('month') }}&pi_no={{ request('pi_no') }}&buyer={{ request('buyer') }}&order_no={{ request('order_no') }}&style_no={{ request('style_no') }}&status={{ request('status') }}&search={{ request('search') }}&print=1" target="_blank" class="btn btn-info btn-sm mr-1" style="padding:5px 15px;">
                     <i class="bx bx-printer"></i> Print
                 </a>
                 @can('production_planning.add')
                  <a href="{{ route('admin.productionPlanningAction','create') }}" class="btn btn-primary btn-sm mr-1" style="padding:5px 15px;">
                      <i class="bx bx-plus"></i> Add Planning
                  </a>
                 @endcan

                  <a href="{{ route('admin.productionPlanning') }}" class="btn btn-warning btn-sm">
                      <i class="bx bx-rotate-left"></i>
                  </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')
            <form action="{{ route('admin.productionPlanning') }}">
                <table>
                    <tbody>
                        <tr>
                            <td>PI</td>
                            <td>Buyer</td>
                            <td>PO</td>
                            <td>Style</td>
                            <td>Status</td>
                            <td>Month</td>
                            <td>Search</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="pi_no" class="form-control form-control-sm">
                                    <option value="">All PI</option>
                                    @foreach($piNos as $pi)
                                        <option value="{{ $pi }}" {{ request()->pi_no == $pi ? 'selected' : '' }}>{{ $pi }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="buyer" class="form-control form-control-sm">
                                    <option value="">All Buyer</option>
                                    @foreach($buyers as $buyer)
                                        <option value="{{ $buyer }}" {{ request()->buyer == $buyer ? 'selected' : '' }}>{{ $buyer }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="order_no" class="form-control form-control-sm">
                                    <option value="">All PO</option>
                                    @foreach($orderNos as $order)
                                        <option value="{{ $order }}" {{ request()->order_no == $order ? 'selected' : '' }}>{{ $order }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="style_no" class="form-control form-control-sm">
                                    <option value="">All Style</option>
                                    @foreach($styleNos as $style)
                                        <option value="{{ $style }}" {{ request()->style_no == $style ? 'selected' : '' }}>{{ $style }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request()->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ request()->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="approved" {{ request()->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="cancelled" {{ request()->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <input type="month" name="month" value="{{ request()->month ? Carbon\Carbon::parse(request()->month)->format('Y-m') : now()->format('Y-m') }}" class="form-control form-control-sm" placeholder="Month" />
                            </td>
                            <td>
                                <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search..." class="form-control form-control-sm" />
                            </td>
                            <td class="d-flex">
                                <button type="submit" class="btn btn-success btn-sm rounded-0 me-1">Search</button>
                                <a href="{{ route('admin.productionPlanning') }}" class="btn btn-warning btn-sm">Reset</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered table-hover">
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
                            <th rowspan="2" style="vertical-align: middle; width: 100px;" class="text-center">Action</th>
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
                            // Track previous master_plan_id for rowspan
                            $prevMasterId = isset($prevMasterId) ? $prevMasterId : null;
                            $currentMasterId = $plan->master_plan_id;
                            $showActions = $prevMasterId != $currentMasterId;
                            $prevMasterId = $currentMasterId;
                        @endphp
                        <tr>
                            <td>{{ $productions->firstItem() + $i }}</td>

                            <!-- Buyer -->
                            <td>{{ $plan->style?->buyer_name ?? '--' }}</td>

                            <!-- Style -->
                            <td>{{ $plan->style_no }}</td>

                            <!-- Color -->
                            <td>
                                {{ $plan->color_name ?? '--' }}
                            </td>

                            <!-- PO No -->
                            <td>{{ $plan->order_no }}</td>

                            <!-- Order Qty -->
                            <td class="font-weight-bold">{{ number_format($plan->color_qty ?? $plan->style_qty) }}</td>

                            @php
                                // Cutting Data
                                // Only show 'today' if today is in the filtered month
                                $filterMonth = request()->month ? \Carbon\Carbon::parse(request()->month)->format('Y-m') : now()->format('Y-m');
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
                                $cuttingTodayOut = $cuttingTodayIn; // Output = Input for now
                                $cuttingTotalOut = $cuttingTotalIn;

                                // Sewing Data
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

                                // Finishing Data
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

                                // Iron Data
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

                                // Poly Data
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

                                // Balance = Total Cutting Input - Total Poly Output
                                $balance = $cuttingTotalIn - $polyTotal;
                            @endphp

                            <!-- Cutting: Today, Total (no output - input = output) -->
                            <td class="production-cell {{ $cuttingTodayIn > 0 ? 'text-success' : '' }}">{{ $cuttingTodayIn > 0 ? number_format($cuttingTodayIn) : '-' }}</td>
                            <td class="production-cell font-weight-bold">{{ number_format($cuttingTotalIn) }}</td>

                            <!-- Sewing -->
                            <td class="production-cell {{ $sewingTodayIn > 0 ? 'text-success' : '' }}">{{ $sewingTodayIn > 0 ? number_format($sewingTodayIn) : '-' }}</td>
                            <td class="production-cell font-weight-bold">{{ number_format($sewingTotalIn) }}</td>
                            <td class="production-cell {{ $sewingTodayOut > 0 ? 'text-success' : '' }}">{{ $sewingTodayOut > 0 ? number_format($sewingTodayOut) : '-' }}</td>
                            <td class="production-cell font-weight-bold">{{ number_format($sewingTotalOut) }}</td>

                            <!-- Finishing -->
                            <td class="production-cell {{ $finishingTodayIn > 0 ? 'text-success' : '' }}">{{ $finishingTodayIn > 0 ? number_format($finishingTodayIn) : '-' }}</td>
                            <td class="production-cell font-weight-bold">{{ number_format($finishingTotalIn) }}</td>
                            <td class="production-cell {{ $finishingTodayOut > 0 ? 'text-success' : '' }}">{{ $finishingTodayOut > 0 ? number_format($finishingTodayOut) : '-' }}</td>
                            <td class="production-cell font-weight-bold">{{ number_format($finishingTotalOut) }}</td>

                            <!-- Iron -->
                            <td class="production-cell {{ $ironToday > 0 ? 'text-success' : '' }}">{{ $ironToday > 0 ? number_format($ironToday) : '-' }}</td>
                            <td class="production-cell font-weight-bold">{{ number_format($ironTotal) }}</td>

                            <!-- Poly -->
                            <td class="production-cell {{ $polyToday > 0 ? 'text-success' : '' }}">{{ $polyToday > 0 ? number_format($polyToday) : '-' }}</td>
                            <td class="production-cell font-weight-bold">{{ number_format($polyTotal) }}</td>

                            <!-- Balance -->
                            <td class="production-cell font-weight-bold {{ $balance < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($balance) }}</td>
                            @if($showActions)
                                <td {{ $showActions ? 'rowspan=' . $plan->masterPlan->productions->count() : '' }} class="align-middle text-center">
                                    <span>{{ $plan->masterPlan->planning_no }}</span><br>
                                    @if($plan->masterPlan->status=='pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($plan->masterPlan->status=='confirmed')
                                        <span class="badge badge-info">Confirmed</span>
                                    @elseif($plan->masterPlan->status=='approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($plan->masterPlan->status=='cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($plan->masterPlan->status) }}</span>
                                    @endif
                                    <br>
                                @php
                                // Ensure planning_month is an array
                                    $planningMonth = $plan->masterPlan->planning_month ?? [];
                                    if (is_string($planningMonth)) {
                                        $planningMonth = json_decode($planningMonth, true) ?: [];
                                    }
                                    if (is_array($planningMonth) && count($planningMonth) > 0) {
                                        $formatted = array_map(function($m) {
                                            return date("M Y", strtotime($m . "-01"));
                                        }, $planningMonth);
                                        echo implode(" <br> ", $formatted);
                                    }
                                @endphp
                                </td>
                            @endif
                            <!-- Action -->
                            @if($showActions)
                            <td {{ $showActions ? 'rowspan=' . $plan->masterPlan->productions->count() : '' }} class="align-middle text-center">
                                @if(can('production_planning.view') || can('production_planning.edit') || can('production_planning.delete') || can('production_planning.approve'))
                                    @if($plan->masterPlan->status !== 'approved')
                                        @can('production_planning.edit')
                                            <a href="{{ route('admin.productionPlanningAction',['edit',$plan->masterPlan->id]) }}" class="btn-custom success mr-1"><i class="bx bx-edit"></i></a>
                                        @endcan
                                        @can('production_planning.approve')
                                            <a href="{{ route('admin.productionPlanningAction',['approve',$plan->masterPlan->id]) }}" onclick="return confirm('Are you sure?')" class="btn-custom success mr-1"><i class="bx bx-check"></i></a>
                                        @endcan
                                        @can('production_planning.delete')
                                            <a href="{{ route('admin.productionPlanningAction',['delete',$plan->masterPlan->id]) }}" onclick="return confirm('Are you sure?')" class="btn-custom danger mr-1"><i class="bx bx-trash"></i></a>
                                        @endcan
                                    @endif
                                @else
                                --
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="22" class="text-center text-muted">No Production Planning Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $productions->links('pagination') }}
            </div>
        </div>
    </div>
</div>
@endsection
