@extends(adminTheme().'layouts.app')

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
        padding: 5px 10px;
        border-radius: 3px;
        margin: 0;
        cursor: pointer;
        margin: 3px 1px;
    }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Edit Planning</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.productionPlanning') }}">Planning</a></li>
            <li class="item">Edit Planning</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Production Planning</h3>
             <div class="dropdown">
                @if($plan->status!='temp')
                @can('samples.add')
                 <a href="{{ route('admin.productionPlanningAction',['view',$plan->id]) }}" class="btn-custom primary" style="padding:5px 15px;">
                    View
                 </a>
                @endcan
                @endif
                 <a href="{{ route('admin.productionPlanning') }}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')

            {{-- @dd($plan->style_no) --}}
            <form action="{{ route('admin.productionPlanningAction', ['update', $plan->id]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <div class="style-info">
                            <select class="form-control form-control-sm mb-2 styleSelect" name="{{ $plan->style_no ? '': 'style_no' }}" {{ $plan->style_no ? 'disabled':'' }}>
                                <option value="">Select</option>
                                @foreach($styles as $style)
                                <option value="{{$style->style_no}}" {{$style->style_no==$plan->style_no?'selected':''}} data-buyer="{{$style->buyer_name}}"  data-merchandiser="{{$style->merchant_name}}"  data-qty="{{$style->total_qty}}" >{{$style->style_no}}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="{{ $plan->style_no ? 'style_no': '' }}" value="{{ $plan->style_no ? $plan->style_no : '' }}">
                            <p>
                                <input type="hidden" name="style_qty" value="{{$plan->style?->total_qty ?? 0}}" class="style_qty">
                                Order Qty :<b class="styleQty">{{number_format($plan->style?->total_qty ?? 0)}} Pcs</b> <br>
                                Buyer :<b class="styleBuyer">{{$plan->style?->buyer_name}}</b> <br>
                                Merchandiser :<b class="styleMerchant">{{$plan->style?->merchant_name}}</b> <br>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card shadow-sm mb-3 flex-fill">
                                    <div class="card-header">
                                        <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">1.Cutting Section</span></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th style="padding:5px;">Starting Date</th>
                                                    <td style="padding:1px;">
                                                        <input type="datetime-local" class="form-control form-control-sm updateDate" value="{{$plan->cutting_start?Carbon\Carbon::parse($plan->cutting_start)->format('Y-m-d\TH:i'):''}}" data-name="cutting_start">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="padding:5px;">Ending Date</th>
                                                    <td style="padding:1px;">
                                                        <input type="datetime-local" class="form-control form-control-sm updateDate" value="{{$plan->cutting_end?Carbon\Carbon::parse($plan->cutting_end)->format('Y-m-d\TH:i'):''}}" data-name="cutting_end">
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card shadow-sm mb-3 flex-fill">
                                    <div class="card-header">
                                        <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">2.Sewing Section</span></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th style="padding:5px;">Starting Date</th>
                                                    <td style="padding:1px;">
                                                        <input type="datetime-local" class="form-control form-control-sm updateDate sewingStarDate" value="{{$plan->sewing_start?Carbon\Carbon::parse($plan->sewing_start)->format('Y-m-d\TH:i'):''}}" data-name="sewing_start">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="padding:5px;">Ending Date</th>
                                                    <td style="padding:1px;">
                                                        <input type="datetime-local" readonly="" class="form-control form-control-sm updateDate sewingEndDate" name="sewing_end" value="{{$plan->sewing_end?Carbon\Carbon::parse($plan->sewing_end)->format('Y-m-d\TH:i'):''}}" data-name="sewing_end">
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card shadow-sm mb-3 flex-fill">
                                    <div class="card-header">
                                        <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">3.Packing Section</span></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th style="padding:5px;">Starting Date</th>
                                                    <td style="padding:1px;">
                                                        <input type="datetime-local" class="form-control form-control-sm updateDate" value="{{$plan->packing_start?Carbon\Carbon::parse($plan->packing_start)->format('Y-m-d\TH:i'):''}}" data-name="packing_start">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="padding:5px;">Ending Date</th>
                                                    <td style="padding:1px;">
                                                        <input type="datetime-local" class="form-control form-control-sm updateDate"  value="{{$plan->packing_end?Carbon\Carbon::parse($plan->packing_end)->format('Y-m-d\TH:i'):''}}" data-name="packing_end">
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card shadow-sm mb-3 flex-fill">
                                    <div class="card-header">
                                        <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">4.Shipment Section</span></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th style="padding:5px;">Starting Date</th>
                                                    <td style="padding:1px;">
                                                        <input type="datetime-local" class="form-control form-control-sm updateDate" value="{{$plan->shippment_start?Carbon\Carbon::parse($plan->shippment_start)->format('Y-m-d\TH:i'):''}}" data-name="shippment_start">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="padding:5px;">Ending Date</th>
                                                    <td style="padding:1px;">
                                                        <input type="datetime-local" class="form-control form-control-sm updateDate" value="{{$plan->shippment_end?Carbon\Carbon::parse($plan->shippment_end)->format('Y-m-d\TH:i'):''}}" data-name="shippment_end">
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="card shadow-sm mb-3 flex-fill">
                                    <div class="card-header">
                                        <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">5. Sewing Production Planning </span></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th style="padding:5px;min-width:400px;">Floor/Line</th>
                                                    <th style="padding:5px;min-width:250px;width:250px;">Output </th>
                                                    <th style="padding:5px;min-width:250px;width:250px;">
                                                        Setup
                                                    </th>
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

                                                                @foreach($attributes as $name => $items)
                                                                    <b>{{ $name }}</b>
                                                                    <br>
                                                                    @foreach($items as $line)
                                                                        @php
                                                                            $exSew = App\Models\ProductionSewing::where('planning_id', $plan->id)->where('line_name', $line->slug)->first();
                                                                        @endphp
                                                                        <div class="lineCheck d-flex justify-content-between align-items-center mb-2">
                                                                            <!-- Left side: checkbox + line name -->
                                                                            <div class="d-flex align-items-center">
                                                                                <input type="checkbox" name="floor[]" class="lineCheckbox form-control form-control-sm" style="width: 20px;" value="{{ $line->slug }}"
                                                                                @if(in_array($line->slug, $selectedLines)) checked @endif
                                                                                >&nbsp;&nbsp;

                                                                                <span class="badge badge-info p-1">Line - <b>{{ $line->slug }}</b></span>
                                                                            </div>

                                                                            <!-- Right side: capacity + working hours -->
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <label class="mb-0">Capacity:</label>
                                                                                <input type="number" min="1" name="capacity[{{ $line->slug }}]" class="form-control form-control-sm lineCapacity mr-2"
                                                                                    value="{{ $exSew?->capacity_hour ?? $line->capacity ?? 0 }}" style="width:100px;" placeholder="C/H">

                                                                                <label class="mb-0">Working Hour:</label>
                                                                                <input type="number" min="1" name="hours[{{ $line->slug }}]" class="form-control form-control-sm lineHours"
                                                                                    value="{{ $exSew?->working_hours ?? 8 }}" style="width:100px;" placeholder="Hours">
                                                                            </div>
                                                                        </div>

                                                                    @endforeach
                                                                    <hr>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p>
                                                            P. Start:<b class="startDate"></b> <br>
                                                            Total Hours:<b class="totalTime"></b> <br>
                                                            Hourly Target :<b class="hourTarget"></b> <br>
                                                            Per Day/Hours :<b class="totalHour" data-hour="10">10h</b> <br>
                                                            P. End:<b class="EndOfDate"></b> <br>
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-3">
                                                            <label>Lose Time (In Minute)</label>
                                                            <input type="text" class="form-control form-control-sm extraTime" name="extra_time" value="{{$plan->extra_time}}" placeholder="Lose Time (In Minute)">
                                                        </div>
                                                        <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Update Plan</button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        calculateProduction();

        // AJAX for date updates
        $('.updateDate').change(function(){
            var url = "{{ route('admin.productionPlanningAction', ['date-update', $plan->id]) }}";
            var dataName = $(this).data('name');
            var dataValue = $(this).val();

            $.ajax({
                url: url,
                data: {'dataName': dataName,'dataValue':dataValue},
                dataType: 'json',
                success: function(res){},
                error: function(){ alert('Error updating item.'); }
            });
        });

        // On style selection
        $(document).on("change", ".styleSelect", function () {
            let qty = Number($(this).find(":selected").data("qty"));
            let buyer = $(this).find(":selected").data("buyer");
            let merch = $(this).find(":selected").data("merchandiser");

            $('.style_qty').val(qty);
            $(".styleQty").text(qty.toLocaleString() + " pcs");
            $(".styleBuyer").text(buyer);
            $(".styleMerchant").text(merch);

            calculateProduction();
        });

        // Trigger recalculation on inputs
        $(document).on("change input", "input[name='floor[]'], .lineCapacity, .lineHours, .extraTime, .sewingStarDate", calculateProduction);

        function calculateProduction() {
            // Start date and quantity
            let startDate = $(".sewingStarDate").val();
            let qty = Number($(".styleSelect option:selected").data("qty"));
            if (!startDate || !qty) return;

            let start = new Date(startDate);
            $(".startDate").text(formatDate(start));

            // Calculate total capacity and daily working minutes from checked lines
            let totalCapacity = 0;
            let totalDailyMinutes = 0;

            $("input[name='floor[]']:checked").each(function(){
                let cap = Number($(this).closest(".lineCheck").find(".lineCapacity").val()) || 0;
                let hours = Number($(this).closest(".lineCheck").find(".lineHours").val()) || 0;

                totalCapacity += cap;
                totalDailyMinutes += hours * 60; // working minutes per day
            });

            if(totalCapacity === 0 || totalDailyMinutes === 0) return;

            $(".hourTarget").text(totalCapacity + " pcs");
            $(".totalHour").text(Math.floor(totalDailyMinutes/60) + "h");

            // Total minutes needed to finish the order
            let totalMinutes = Math.round((qty / totalCapacity) * 60);

            // Add lose time
            let loseTime = Number($(".extraTime").val()) || 0;
            totalMinutes += loseTime;

            // Calculate Total Hours (real work hours needed)
            let totalDays = Math.floor(totalMinutes / totalDailyMinutes);
            let remainingMinutes = totalMinutes % totalDailyMinutes;
            let totalHoursNeeded = (totalDays * (totalDailyMinutes / 60)) + (remainingMinutes / 60);

            $(".totalTime").text(Math.floor(totalHoursNeeded) + "h - " + Math.round((totalHoursNeeded % 1) * 60) + "m");

            // Calculate End Date based on daily working minutes
            let end = new Date(start);
            let remainingTotalMinutes = totalMinutes;

            while(remainingTotalMinutes > 0) {
                let minutesThisDay = Math.min(remainingTotalMinutes, totalDailyMinutes);
                end.setMinutes(end.getMinutes() + minutesThisDay);
                remainingTotalMinutes -= minutesThisDay;

                if(remainingTotalMinutes > 0) {
                    // Move to next day same start time
                    end.setDate(end.getDate() + 1);
                    end.setHours(start.getHours(), start.getMinutes(), 0);
                }
            }

            $(".EndOfDate").text(formatDate(end));
            $(".sewingEndDate").val(
                end.getFullYear() + "-" +
                String(end.getMonth()+1).padStart(2,'0') + "-" +
                String(end.getDate()).padStart(2,'0') + "T" +
                String(end.getHours()).padStart(2,'0') + ":" +
                String(end.getMinutes()).padStart(2,'0')
            );
        }

        // Format date helper
        function formatDate(d) {
            let day = String(d.getDate()).padStart(2,'0');
            let month = String(d.getMonth()+1).padStart(2,'0');
            let year = d.getFullYear();

            let hours = d.getHours();
            let minutes = String(d.getMinutes()).padStart(2,'0');

            let ampm = hours >= 12 ? "PM" : "AM";
            let hour12 = hours % 12;
            if(hour12 === 0) hour12 = 12;
            hour12 = String(hour12).padStart(2,'0');

            return `${day}/${month}/${year}, ${hour12}.${minutes} ${ampm}`;
        }
    });


    $(document).ready(function(){
        var $styleDiv = $('.style-info');
        var divTop = $styleDiv.offset().top;

        $(window).scroll(function(){
            if($(window).scrollTop() >= divTop){
                $styleDiv.addClass('sticky');
            } else {
                $styleDiv.removeClass('sticky');
            }
        });
    });
</script>
@endpush

@push('css')
<style>
.style-info {
    position: relative; /* default অবস্থায় relative থাকে */
    transition: all 0.3s ease; /* smooth transition */
}
.style-info.sticky {
    position: fixed;
    top: 10rem;
    width: 250px;
    background: #fff;
    padding: 10px;
    border: 1px solid #4caf50;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    z-index: 1000;
}
</style>
@endpush
