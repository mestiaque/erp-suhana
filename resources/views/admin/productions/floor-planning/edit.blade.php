@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Floor Planning Edit') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>{{ 'Edit Floor Planning' }}</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.floorPlanning') }}">Planning</a></li>
            <li class="item">{{ 'Edit Floor Planning' }}</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Floor Planning</h3>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.floorPlanningAction', ['update', $masterPlan->id]) }}" method="POST">
                @csrf


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
                                                            {{ number_format($plan?->style_qty ?? 0) }} Pcs
                                                        </strong>
                                                        <input type="hidden"
                                                            {{-- name="style_qty" --}}
                                                            value="{{ $plan?->style_qty ?? 0 }}"
                                                            class="style_qty">
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
                                                    <input type="datetime-local"
                                                        class="form-control form-control-sm updateDate sewingStarDate"
                                                        value="{{ $plan->sewing_start ? Carbon\Carbon::parse($plan->sewing_start)->format('Y-m-d\TH:i') : '' }}"
                                                        data-name="sewing_start">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Ending Date</th>
                                                <td>
                                                    <input type="datetime-local"
                                                        readonly
                                                        name="plans[{{ $plan->id }}][sewing_end]"
                                                        class="form-control form-control-sm updateDate sewingEndDate"
                                                        value="{{ $plan->sewing_end ? Carbon\Carbon::parse($plan->sewing_end)->format('Y-m-d\TH:i') : '' }}"
                                                        data-name="sewing_end">
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
                                                                                <input type="checkbox" name="plans[{{ $plan->id }}][floor][]" class="lineCheckbox form-control form-control-sm" style="width: 20px;" value="{{ $line->slug }}"
                                                                                @if(in_array($line->slug, $selectedLines)) checked @endif
                                                                                >&nbsp;&nbsp;

                                                                                <span class="badge badge-info p-1">Line - <b>{{ $line->slug }}</b></span>
                                                                            </div>

                                                                            <!-- Right side: capacity + working hours -->
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <label class="mb-0">Capacity:</label>
                                                                                <input type="number" min="1" name="plans[{{ $plan->id }}][capacity][{{ $line->slug }}]" class="form-control form-control-sm lineCapacity mr-2"
                                                                                    value="{{ $exSew?->capacity_hour ?? $line->capacity ?? 0 }}" style="width:100px;" placeholder="C/H">

                                                                                <label class="mb-0">Working Hour:</label>
                                                                                <input type="number" min="1" name="plans[{{ $plan->id }}][hours][{{ $line->slug }}]" class="form-control form-control-sm lineHours"
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
                                                            <input type="text" class="form-control form-control-sm extraTime" name="plans[{{ $plan->id }}][extra_time]" value="{{$plan->extra_time}}" placeholder="Lose Time (In Minute)">
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
                @endforeach

            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            // Initial calculation for all cards
            $("[id^='planCard_']").each(function () {
                calculateProduction($(this));
            });

            /* ===========================
            Date Update (AJAX)
            ============================ */


            $(document).on("change", ".updateDate", function () {

                let dataName = $(this).data("name");

                // শুধু sewing_start হলে post করবে
                if (dataName !== 'sewing_start') {
                    return;
                }

                let card = $(this).closest("[id^='planCard_']");
                let planId = card.attr("id").replace("planCard_", "");
                let dataValue = $(this).val();

                $.ajax({
                    url: "{{ route('admin.floorPlanningAction', ['date-update', '']) }}/" + planId,
                    data: {
                        dataName: dataName,
                        dataValue: dataValue
                    },
                    dataType: "json",
                    error: function () {
                        alert("Error updating date");
                    }
                });

                calculateProduction(card);
            });


            /* ===========================
            Recalculate on any change
            ============================ */
            $(document).on(
                "change input",
                ".lineCheckbox, .lineCapacity, .lineHours, .extraTime",
                function () {
                    let card = $(this).closest("[id^='planCard_']");
                    calculateProduction(card);
                }
            );

            /* ===========================
            MAIN CALCULATION FUNCTION
            ============================ */
            function calculateProduction(card) {

                let startDate = card.find(".sewingStarDate").val();
                let qty = Number(card.find(".style_qty").val());
                if (!startDate || qty <= 0) return;

                let start = new Date(startDate);
                card.find(".startDate").text(formatDate(start));

                let totalCapacity = 0;
                let totalDailyMinutes = 0;

                // Loop checked lines
                card.find(".lineCheckbox:checked").each(function () {

                    let row = $(this).closest(".lineCheck");

                    let cap = Number(row.find(".lineCapacity").val()) || 0;
                    let hours = Number(row.find(".lineHours").val()) || 0;

                    totalCapacity += cap;
                    totalDailyMinutes += hours * 60;
                });

                if (totalCapacity === 0 || totalDailyMinutes === 0) return;

                card.find(".hourTarget").text(totalCapacity + " pcs");
                card.find(".totalHour").text(Math.floor(totalDailyMinutes / 60) + "h");

                // Total minutes needed
                let totalMinutes = Math.round((qty / totalCapacity) * 60);

                // Lose time
                let loseTime = Number(card.find(".extraTime").val()) || 0;
                totalMinutes += loseTime;

                // Total working hours
                let totalDays = Math.floor(totalMinutes / totalDailyMinutes);
                let remainingMinutes = totalMinutes % totalDailyMinutes;

                let totalHoursNeeded =
                    (totalDays * (totalDailyMinutes / 60)) +
                    (remainingMinutes / 60);

                card.find(".totalTime").text(
                    Math.floor(totalHoursNeeded) +
                    "h - " +
                    Math.round((totalHoursNeeded % 1) * 60) +
                    "m"
                );

                // Calculate End Date
                let end = new Date(start);
                let remaining = totalMinutes;

                while (remaining > 0) {
                    let todayMinutes = Math.min(remaining, totalDailyMinutes);
                    end.setMinutes(end.getMinutes() + todayMinutes);
                    remaining -= todayMinutes;

                    if (remaining > 0) {
                        end.setDate(end.getDate() + 1);
                        end.setHours(start.getHours(), start.getMinutes(), 0);
                    }
                }

                card.find(".EndOfDate").text(formatDate(end));

                card.find(".sewingEndDate").val(
                    end.getFullYear() + "-" +
                    String(end.getMonth() + 1).padStart(2, '0') + "-" +
                    String(end.getDate()).padStart(2, '0') + "T" +
                    String(end.getHours()).padStart(2, '0') + ":" +
                    String(end.getMinutes()).padStart(2, '0')
                );
            }

            /* ===========================
            DATE FORMAT FUNCTION
            ============================ */
            function formatDate(d) {

                let day = String(d.getDate()).padStart(2, '0');
                let month = String(d.getMonth() + 1).padStart(2, '0');
                let year = d.getFullYear();

                let hours = d.getHours();
                let minutes = String(d.getMinutes()).padStart(2, '0');

                let ampm = hours >= 12 ? "PM" : "AM";
                let hour12 = hours % 12;
                if (hour12 === 0) hour12 = 12;

                return `${day}/${month}/${year}, ${hour12}.${minutes} ${ampm}`;
            }

        });
    </script>
@endpush
