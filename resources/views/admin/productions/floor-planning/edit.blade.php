@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Floor Planning') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>{{ 'Floor Planning' }}</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.floorPlanning') }}">Planning</a></li>
            <li class="item">{{ 'Scrollable View' }}</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header">
            <h3>Floor Planning Table</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.floorPlanningAction', ['update', $masterPlan->id]) }}" method="POST">
                @csrf
                <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Style</th>
                                <th>Buyer</th>
                                <th>Order No</th>
                                <th>Qty</th>
                                <th>Merchandiser</th>
                                @php
                                    $allLines = App\Models\Attribute::where('type',4)->where('status','active')->get();
                                @endphp

                                @foreach($allLines as $line)
                                    <th>{{ $line->name }} - {{ $line->slug }}</th>
                                @endforeach
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Total Time</th>
                                <th>Hourly Target</th>
                                <th>Extra Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($masterPlan->productions as $plan)
                                <tr data-planid="{{ $plan->id }}">
                                    <td>{{ $plan->style_no }}</td>
                                    <td>{{ $plan->style?->buyer_name ?? '--' }}</td>
                                    <td>{{ $plan->order_no }}</td>
                                    <td><input type="hidden" class="style_qty" value="{{ $plan->style_qty ?? 0 }}">{{ number_format($plan->style_qty ?? 0) }}</td>
                                    <td>{{ $plan->style?->merchant_name ?? '--' }}</td>

                                    @foreach($allLines as $line)
                                        @php
                                            $exSew = $plan->sewingLines->where('line_name',$line->slug)->first();
                                        @endphp
                                        <td>
                                            <div class="d-flex flex-column">
                                                <label>
                                                    <input type="checkbox" name="plans[{{ $plan->id }}][floor][]" value="{{ $line->slug }}" class="lineCheckbox form-control form-control-sm"
                                                    @if($exSew) checked @endif>
                                                </label>
                                                <label style="font-size:0.8rem;" class="mb-0">Capacity</label>
                                                <input type="number" name="plans[{{ $plan->id }}][capacity][{{ $line->slug }}]" value="{{ $exSew->capacity_hour ?? $line->capacity ?? 0 }}" class="lineCapacity mb-2 form-control form-control-sm">
                                                <label style="font-size:0.8rem;" class="mb-0">Hours</label>
                                                <input type="number" name="plans[{{ $plan->id }}][hours][{{ $line->slug }}]" value="{{ $exSew->working_hours ?? 8 }}" class="lineHours form-control form-control-sm">
                                            </div>
                                        </td>
                                    @endforeach

                                    <td>
                                        <input type="datetime-local"
                                                        class="form-control form-control-sm updateDate sewingStarDate"
                                                        value="{{ $plan->sewing_start ? Carbon\Carbon::parse($plan->sewing_start)->format('Y-m-d\TH:i') : '' }}"
                                                        data-name="sewing_start">
                                        {{-- <input type="datetime-local"  class="startDate form-control form-control-sm" value="{{ $plan->sewing_start ? \Carbon\Carbon::parse($plan->sewing_start)->format('Y-m-d\TH:i') : '' }}"> --}}
                                    </td>
                                    <td>
                                        <input type="datetime-local"
                                                        readonly
                                                        name="plans[{{ $plan->id }}][sewing_end]"
                                                        class="form-control form-control-sm updateDate sewingEndDate"
                                                        value="{{ $plan->sewing_end ? Carbon\Carbon::parse($plan->sewing_end)->format('Y-m-d\TH:i') : '' }}"
                                                        data-name="sewing_end">
                                    </td>
                                    <td class="totalTime"></td>
                                    <td class="hourTarget"></td>
                                    <td>
                                        <input type="number" name="plans[{{ $plan->id }}][extra_time]" class="extraTime form-control form-control-sm" value="{{ $plan->extra_time ?? 0 }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-right">
                    <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Update Plans</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function(){

    function calculateRow(row){
        // ক্লাসের নাম .sewingStarDate (আপনার ব্লেড অনুযায়ী)
        let startDateVal = row.find('.sewingStarDate').val();
        let qty = Number(row.find('.style_qty').val());

        if(!startDateVal || qty <= 0) return;

        let start = new Date(startDateVal);
        let totalCapacity = 0;
        let totalDailyMinutes = 0;

        row.find('td').each(function(){
            let td = $(this);
            let checkbox = td.find('.lineCheckbox');
            if(checkbox.length && checkbox.is(':checked')){
                let cap = Number(td.find('.lineCapacity').val()) || 0;
                let hours = Number(td.find('.lineHours').val()) || 0;
                totalCapacity += cap;
                totalDailyMinutes += hours * 60;
            }
        });

        if(totalCapacity === 0 || totalDailyMinutes === 0) return;

        row.find('.hourTarget').text(totalCapacity + '');

        let extraTime = Number(row.find('.extraTime').val()) || 0;
        // Total Minutes needed based on Qty and Capacity
        let totalMinutes = Math.round((qty / totalCapacity) * 60) + extraTime;

        let totalHoursNeeded = totalMinutes / 60;
        row.find('.totalTime').text(Math.floor(totalHoursNeeded) + 'h - ' + Math.round((totalHoursNeeded % 1) * 60) + 'm');

        // End Date Calculation
        let end = new Date(start.getTime());
        let remaining = totalMinutes;

        while(remaining > 0){
            let currentDayStart = new Date(end);
            // আজ আর কতক্ষণ কাজ করা যাবে (ধরি ৮ ঘণ্টা শিফট)
            let todayMaxMinutes = totalDailyMinutes;
            let minutesCanDoToday = Math.min(remaining, todayMaxMinutes);

            end.setMinutes(end.getMinutes() + minutesCanDoToday);
            remaining -= minutesCanDoToday;

            if(remaining > 0){
                end.setDate(end.getDate() + 1);
                end.setHours(start.getHours(), start.getMinutes(), 0);
            }
        }

        // ISO format conversion for datetime-local input
        let year = end.getFullYear();
        let month = String(end.getMonth() + 1).padStart(2, '0');
        let day = String(end.getDate()).padStart(2, '0');
        let hours = String(end.getHours()).padStart(2, '0');
        let mins = String(end.getMinutes()).padStart(2, '0');

        let formattedEndDate = `${year}-${month}-${day}T${hours}:${mins}`;
        row.find('.sewingEndDate').val(formattedEndDate);
    }

    // টেবিল বডি লুপ (আইডি ছাড়া সরাসরি tbody দিয়ে)
    $('table tbody tr').each(function(){
        calculateRow($(this));
    });

    // ইভেন্ট লিসেনার (সঠিক ক্লাস নেম .sewingStarDate ব্যবহার করা হয়েছে)
    $(document).on('change input', '.lineCheckbox, .lineCapacity, .lineHours, .extraTime, .sewingStarDate', function(){
        let row = $(this).closest('tr');
        calculateRow(row);
    });

    $(document).on("change", ".updateDate", function () {
        let dataName = $(this).data("name");
        let planId = $(this).closest('tr').data('planid');

        if (dataName !== 'sewing_start') return;

        let dataValue = $(this).val();

        $.ajax({
            url: "{{ route('admin.floorPlanningAction', ['date-update', '']) }}/" + planId,
            method: "GET", // বা POST আপনার রাউট অনুযায়ী
            data: {
                dataName: dataName,
                dataValue: dataValue
            },
            dataType: "json",
            success: function(res) {
                console.log("Updated");
            },
            error: function () {
                console.error("Error updating date");
            }
        });
    });
});
</script>
@endpush

@push('css')
<style>
    table th{
        white-space: nowrap !important;
        min-width: 100px;
    }
    .lineCheckbox{
        height: 1.5rem !important;
    }
</style>
@endpush


