@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Floor Planning - Create') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>{{ 'Floor Planning - Create' }}</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.floorPlanning') }}">Planning</a></li>
            <li class="item">{{ 'Create' }}</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header">
            <h3>Floor Planning Table</h3>
        </div>

        <div class="card-body">
            <!-- Master Plan Selection -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label>Select Master Plan *</label>
                    <select id="masterPlanSelect" class="form-control">
                        <option value="">-- Select Master Plan --</option>
                        @foreach($masterPlans as $mp)
                            <option value="{{ $mp->id }}">
                                {{ $mp->planning_no }} |
                                @php $pis = $mp->productions->pluck('pi_no')->unique()->implode(', ') @endphp
                                ({{ $pis }}) |
                                {{ $mp->productions->count() }} Styles |
                                Qty: {{ number_format($mp->productions->sum('style_qty')) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <form action="{{ route('admin.floorPlanningAction', ['update', '']) }}" method="POST" id="floorPlanForm">
                @csrf
                <input type="hidden" name="master_plan_id" id="masterPlanId">
                <input type="hidden" name="planning_month" id="hidden_planning_month" value="{{ request('planning_month', now()->format('Y-m')) }}">

                <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
                    <table class="table table-bordered table-striped table-sm" id="planningTable">
                        <thead>
                            <tr>
                                <th>Style</th>
                                <th>Buyer</th>
                                <th>Order No</th>
                                <th>Color</th>
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
                        <tbody id="plansTableBody">
                            <tr>
                                <td colspan="{{ 10 + count($allLines) }}" class="text-center text-muted">
                                    Please select a Master Plan above
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-right">
                    <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Save Plans</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function(){
    // Sync planning_month input to hidden field on change
    $('#planning_month').on('change', function(){
        $('#hidden_planning_month').val($(this).val());
    });
    // Load productions when master plan is selected
    $('#masterPlanSelect').on('change', function(){
        var masterPlanId = $(this).val();
        if(masterPlanId) {
            $('#masterPlanId').val(masterPlanId);
            $('#floorPlanForm').attr('action', '{{ route("admin.floorPlanningAction", ["update", ""]) }}/' + masterPlanId);

            // Load productions via AJAX
            $.get('{{ route("admin.floorPlanningAction", ["get-productions", ""]) }}/' + masterPlanId, function(data) {
                $('#plansTableBody').html(data);

                // Initialize calculations for new rows
                $('#planningTable tbody tr').each(function(){
                    calculateRow($(this));
                });
            });
        } else {
            $('#plansTableBody').html('<tr><td colspan="{{ 10 + count($allLines) }}" class="text-center text-muted">Please select a Master Plan above</td></tr>');
        }
    });

    function calculateRow(row){
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
        let totalMinutes = Math.round((qty / totalCapacity) * 60) + extraTime;

        let totalHoursNeeded = totalMinutes / 60;
        row.find('.totalTime').text(Math.floor(totalHoursNeeded) + 'h - ' + Math.round((totalHoursNeeded % 1) * 60) + 'm');

        // End Date Calculation
        let end = new Date(start.getTime());
        let remaining = totalMinutes;

        while(remaining > 0){
            let currentDayStart = new Date(end);
            let todayMaxMinutes = totalDailyMinutes;
            let minutesCanDoToday = Math.min(remaining, todayMaxMinutes);

            end.setMinutes(end.getMinutes() + minutesCanDoToday);
            remaining -= minutesCanDoToday;

            if(remaining > 0){
                end.setDate(end.getDate() + 1);
                end.setHours(start.getHours(), start.getMinutes(), 0);
            }
        }

        let year = end.getFullYear();
        let month = String(end.getMonth() + 1).padStart(2, '0');
        let day = String(end.getDate()).padStart(2, '0');
        let hours = String(end.getHours()).padStart(2, '0');
        let mins = String(end.getMinutes()).padStart(2, '0');

        let formattedEndDate = `${year}-${month}-${day}T${hours}:${mins}`;
        row.find('.sewingEndDate').val(formattedEndDate);
    }

    // Event listeners
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
            method: "GET",
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
