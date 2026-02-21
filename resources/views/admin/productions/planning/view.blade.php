@foreach($masterPlans as $plan)
<div class="modal fade" id="masterPlanModal_{{ $plan->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

<<<<<<< HEAD
@section('title')
<title>{{ websiteTitle('Production Planning Edit') }}</title>
@endsection

@push('css')
<style>
    .search-result-box{position:absolute;z-index:9;width:100%;background:#fff;border:1px solid #ddd;display:none;}
    .search-result-box li{padding:6px 10px;cursor:pointer;}
    .searchlist ul {list-style:none;margin:0;padding:0;}
    .searchlist ul li{border-top:1px solid #dbd6d6;padding:5px 10px;cursor:pointer;}
    .searchlist ul li:hover{background:#f2f2f2;}
    .searchGrid {position:relative;}
    .itemSearch {height:200px;overflow:auto;position:absolute;width:100%;background:white;border:1px solid #dfdfdf;border-top:0;display:none;}
    .table-striped tr th{padding:3px;}
    .table-striped tr td{padding:3px;}
    .lineCheck {
        border: 1px solid #bebebe;
        padding: 0px 10px;
        border-radius: 3px;
        cursor: pointer;
        margin: 3px 1px;
        display: inline-block;
    }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>View Planning</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.productionPlanning') }}">Planning</a></li>
            <li class="item">View Planning</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Production Planning</h3>
             <div class="dropdown">
                @if($plan->status!='temp')
                <a href="{{ route('admin.productionPlanningAction',['print',$plan->id]) }}" class="btn-custom info mr-1"><i class="fa fa-print"></i></a>
                @can('production_planning.edit')
                 <a href="{{ route('admin.productionPlanningAction',['edit',$plan->id]) }}" class="btn-custom primary" style="padding:5px 15px;">
                    Edit
                 </a>
                 @endcan
                @endif
                @can('production_planning.view')
                 <a href="{{ route('admin.productionPlanningAction',['view',$plan->id]) }}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
                 @endcan
             </div>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm mb-3 flex-fill">
                        <div class="card-body">
                            <span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;display:inline-block;margin-bottom:2px;">1.Cutting Section</span>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="padding:5px;width: 200px;min-width: 200px;">Starting Date</th>
                                        <td style="padding:5px;min-width: 250px;">
                                            {{$plan->cutting_start?Carbon\Carbon::parse($plan->cutting_start)->format('d.m.Y h:i A'):''}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="padding:5px;">Ending Date</th>
                                        <td style="padding:5px;">
                                            {{$plan->cutting_end?Carbon\Carbon::parse($plan->cutting_end)->format('d.m.Y h:i A'):''}}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;display:inline-block;margin-bottom:2px;">2.Sewing Section</span>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="padding:5px;width: 200px;min-width: 200px;">Starting Date</th>
                                        <td style="padding:5px;min-width: 250px;">
                                            {{$plan->sewing_start?Carbon\Carbon::parse($plan->sewing_start)->format('d.m.Y h:i A'):''}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="padding:5px;">Ending Date</th>
                                        <td style="padding:5px;">
                                            {{$plan->sewing_end?Carbon\Carbon::parse($plan->sewing_end)->format('d.m.Y h:i A'):''}}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;display:inline-block;margin-bottom:2px;">3.Packing Section</span>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="padding:5px;width: 200px;min-width: 200px;">Starting Date</th>
                                        <td style="padding:5px;min-width: 250px;">
                                            {{$plan->packing_start?Carbon\Carbon::parse($plan->packing_start)->format('d.m.Y h:i A'):''}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="padding:5px;">Ending Date</th>
                                        <td style="padding:5px;">
                                            {{$plan->packing_end?Carbon\Carbon::parse($plan->packing_end)->format('d.m.Y h:i A'):''}}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm mb-3 flex-fill">
                        <div class="card-body">
                            <span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;display:inline-block;margin-bottom:2px;">5. Sewing Production Planning</span>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="padding:5px;min-width:300px;">Style No</th>
                                        <th style="padding:5px;min-width:250px;width:250px;">Output </th>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px;">
                                            <b>Style No:</b> {{$plan->style_no}}
                                            <p>
                                                Order Qnty :<b>{{number_format($plan->style_qty ?? 0)}} Pcs</b> <br>
                                                Buyer :<b>{{$plan->getPiStyle()?->buyer_name}}</b> <br>
                                                Merchandiser :<b>{{$plan->getPiStyle()?->merchant_name}}</b> <br>
                                            </p>

                                                @php
                                                    $lines = $plan->floorLines()->groupBy('name');
                                                @endphp

                                                @foreach($lines as $name => $items)
                                                <p>
                                                    <b>{{ $name }}</b> : <br>
                                                    @foreach($items as $line)
                                                    @php
                                                        $exSew = App\Models\ProductionSewing::where('planning_id', $plan->id)->where('line_name', $line->slug)->first();
                                                    @endphp

                                                    <span class="lineCheck">
                                                        <span class="p-1 text-white mr-2 badge bg-primary">Line: {{ $line->slug }}</span>
                                                        <span class="p-1 text-white mr-2 badge bg-success">C/H: {{ $exSew?->capacity_hour ?? $line->capacity ?? 0 }}</span>
                                                        <span class="p-1 text-white mr-2 badge bg-info">WH: {{ $exSew?->working_hours ?? 8 }}</span>
                                                    </span>
                                                    @endforeach
                                                </p>
                                                @endforeach

                                            <b>Lose Time (In Minite):</b> {{$plan->extra_time}}
                                        </td>
                                        <td>
                                            <p>
                                                P. Start:<b>{{$plan->sewing_start?Carbon\Carbon::parse($plan->sewing_start)->format('d.m.Y h:i A'):''}}</b> <br>
                                                Total Hours:<b>{{$plan->total_working_time}}</b> <br>
                                                Hourly Target :<b>{{$plan->total_hourly_capacity}} Pcs</b> <br>
                                                Per Day/Hours :<b>{{$plan->working_hours}}h</b> <br>
                                                P. End:<b>{{$plan->sewing_end?Carbon\Carbon::parse($plan->sewing_end)->format('d.m.Y h:i A'):''}}</b> <br>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
=======
            <!-- Header -->
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title text-white">
                    Master Plan Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
>>>>>>> master
            </div>

            <!-- Body -->
            <div class="modal-body">

                <!-- Summary -->
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Created By</small><br>
                                <strong>{{ $plan->creator?->name ?? '--' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Status</small><br>
                                <strong>{{ ucfirst($plan->status) }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Created At</small><br>
                                <strong>{{ $plan->created_at->format('d.m.Y') }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Total Styles</small><br>
                                <strong>{{ $plan->productions->count() }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-secondary text-white text-center">
                            <tr>
                                <th>SL</th>
                                <th>Style</th>
                                <th>Buyer</th>
                                <th>Order No</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalQty = 0; @endphp
                            @foreach($plan->productions as $i => $p)
                                @php
                                    $qty = $p->style?->total_qty ?? 0;
                                    $totalQty += $qty;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $i+1 }}</td>
                                    <td>{{ $p->style_no }}</td>
                                    <td>{{ $p->orderDetailItems->buyer_name ?? '--' }}</td>
                                    <td>{{ $p->order_no }}</td>
                                    <td class="text-right">{{ number_format($qty) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="font-weight-bold bg-light">
                            <tr>
                                <td colspan="4" class="text-right">Total</td>
                                <td class="text-right">{{ number_format($totalQty) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
@endforeach
