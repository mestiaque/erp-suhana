@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Production Planning Edit') }}</title>
@endsection


@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>View Floor Planning</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.floorPlanning') }}">Floor Planning</a></li>
            <li class="item">View Floor Planning</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Floor Planning</h3>
             <div class="dropdown">
                <a href="{{ route('admin.floorPlanningAction',['print',$masterPlan->id]) }}" class="btn-custom info mr-1"><i class="fa fa-print"></i></a>
                @can('production_planning.edit')
                 <a href="{{ route('admin.floorPlanningAction',['edit',$masterPlan->id]) }}" class="btn-custom primary" style="padding:5px 15px;">
                    Edit
                 </a>
                 @endcan
                @can('production_planning.view')
                 <a href="{{ route('admin.floorPlanningAction',['view',$masterPlan->id]) }}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
                 @endcan
             </div>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')

            @foreach ($masterPlan->productions as $plan)
                <div class="card p-0 shadow shadow-lg mb-3" id="planCard_{{$plan->id}}">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card shadow-sm h-100 pb-0">
                                <div class="card-header bg-light py-2 mb-0">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="bx bx-tag"></i> Style Information
                                    </h6>
                                </div>

                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <th class="text-muted" style="width:35%">Style</th>
                                                <td>
                                                    <strong class="text-dark">{{ $plan->style_no }}</strong>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th class="text-muted">Buyer</th>
                                                <td>
                                                    <strong class="styleBuyer">
                                                        {{ $plan->style?->buyer_name ?? '--' }}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Order No</th>
                                                <td>{{$plan->order_no}}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Order Quantity</th>
                                                <td>
                                                    <strong class="text-primary styleQty">
                                                        {{ number_format($plan?->color_qty ?? $plan?->style_qty ?? 0) }} Pcs
                                                    </strong>
                                                    <input type="hidden"
                                                        name="style_qty"
                                                        value="{{ $plan?->style_qty ?? 0 }}"
                                                        class="style_qty">
                                                </td>
                                            </tr>

                                            <tr>
                                                <th class="text-muted">Color</th>
                                                <td>
                                                    <strong class="text-dark">
                                                        {{ $plan->color_name ?? 'N/A' }}
                                                    </strong>
                                                </td>
                                            </tr>


                                            <tr>
                                                <th class="text-muted">Merchandiser</th>
                                                <td>
                                                    <strong class="styleMerchant">
                                                        {{ $plan->style?->merchant_name ?? '--' }}
                                                    </strong>
                                                </td>
                                            </tr>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Sewing Date Info -->
                        <div class="col-md-4">
                            <div class="card shadow-sm h-100 pb-0">
                                <div class="card-header bg-light py-2 mb-0">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="bx bx-calendar"></i> Sewing Schedule
                                    </h6>
                                </div>
                                <div class="card-body p-2">
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <th class="text-muted" style="width:45%">Starting Date</th>
                                            <td>
                                                {{$plan->sewing_start?Carbon\Carbon::parse($plan->sewing_start)->format('d.m.Y h:i A'):'--'}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Ending Date</th>
                                            <td>
                                                {{$plan->sewing_end?Carbon\Carbon::parse($plan->sewing_end)->format('d.m.Y h:i A'):'--'}}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card shadow-sm flex-fill">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="padding:5px;    width: 50%;">Floor/Line</th>
                                                <th style="padding:5px;    width: 50%;">Output </th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            @php
                                                                $attributes = App\Models\Attribute::where('type', 4)
                                                                    ->where('status', 'active')
                                                                    ->get()
                                                                    ->groupBy('name');

                                                                $selectedLines = $plan->sewingLines->pluck('line_name')->toArray();
                                                            @endphp

                                                            <table class="table table-sm table-bordered">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Floor</th>
                                                                        <th>Line</th>
                                                                        <th>Capacity / Hour (C/H)</th>
                                                                        <th>Working Hour (WH)</th>
                                                                        <th>Allocation Qty</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php $serial = 1; $hasData = false; @endphp
                                                                    @foreach($attributes as $floorName => $items)
                                                                        @foreach($items as $line)
                                                                            @if(in_array($line->slug, $selectedLines))
                                                                                @php
                                                                                    $exSew = $plan->sewingLines->where('line_name', $line->slug)->first();
                                                                                    $hasData = true;
                                                                                @endphp
                                                                                <tr>
                                                                                    <td>{{ $serial++ }}</td>
                                                                                    <td>{{ $floorName }}</td>
                                                                                    <td>{{ $line->slug }}</td>
                                                                                    <td>{{ $exSew?->capacity_hour ?? $line->capacity ?? 0 }}</td>
                                                                                    <td>{{ $exSew?->working_hours ?? 8 }}</td>
                                                                                    <td>{{ $exSew?->allocation_qty ?? 0 }}</td>
                                                                                </tr>
                                                                            @endif
                                                                        @endforeach
                                                                    @endphp

                                                                    @if(!$hasData)
                                                                        <tr>
                                                                            <td colspan="6" class="text-center text-muted">No Data Found</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <tbody>
                                                                <tr>
                                                                    <th>Production Start</th>
                                                                    <td>{{ $plan->sewing_start ? Carbon\Carbon::parse($plan->sewing_start)->format('d.m.Y h:i A') : '--' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total Hours</th>
                                                                    <td>{{ $plan->total_working_time }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Hourly Target</th>
                                                                    <td>{{ $plan->total_hourly_capacity }} Pcs</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Per Day/Hours</th>
                                                                    <td>{{ $plan->working_hours }}h</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Production End</th>
                                                                    <td>{{ $plan->sewing_end ? Carbon\Carbon::parse($plan->sewing_end)->format('d.m.Y h:i A') : '--' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th><i>Lose Time (In Minite):</i></th>
                                                                    <td><i>{{$plan->extra_time}}</i></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('js')

@endpush
