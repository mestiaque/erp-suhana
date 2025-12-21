@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Daily Production') }}</title>
@endsection

@push('css')
<style>
.data-row:focus {
    outline: 2px solid #4CAF50;
    background: #f0fff0;
}
.value-tag {
    display: inline-block;
    padding: 2px 5px;
    border-radius: 3px;
    color: #fff;
}
.high-performance { background: #4CAF50; }
.medium-performance { background: #FF9800; }
.low-performance { background: #F44336; }
.badge-cell { text-align: center; }
.total-column { font-weight: bold; }
.text-disabled { background:#f0f0f0; text-align:center; color:#999; }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Daily Production</h3>
            <a href="{{ route('admin.dailyProduction') }}" class="btn-custom yellow">
                <i class="bx bx-rotate-left"></i>
            </a>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            @php
                $maxWorkingTime = $swings->count() ? $swings->pluck('working_hours')->max() : 9;
                $startHour = 8;
                $endHour = $startHour + $maxWorkingTime;
                $today_date = request('startDate') ?? date('Y-m-d');
            @endphp

            <div class="table-responsive data-table">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="deliRport">
                        <tr>
                            <th>Line</th>
                            <th>Style</th>
                            <th>Order</th>
                            {{-- <th>Buyer</th> --}}
                            <th>Target</th>
                            @for($h=$startHour; $h<$endHour; $h++)
                                @php
                                    $start = ($h > 12) ? $h - 12 : $h;
                                    $endH = $h + 1;
                                    $end = ($endH > 12) ? $endH - 12 : $endH;
                                    $endPeriod = $endH < 12 ? 'AM' : 'PM';
                                @endphp
                                <th style="white-space: nowrap;">{{ $start }}-{{ $end }} <span>{{ $endPeriod }}</span></th>
                            @endfor
                            <th>Today Total</th>
                            <th>Previous</th>
                            <th>Grand Total</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        $sum_target = 0;
                        $sum_today = 0;
                        $sum_previous = 0;
                        $sum_grand = 0;
                        $unique_styles = [];
                        $unique_orders = [];
                        $unique_buyers = [];
                    @endphp

                    @forelse($swings as $swing)
                        @php
                            $style_no = $swing->planning->style_no;
                            $unique_styles[] = $style_no;
                            $unique_buyers[] = $swing?->planning?->style?->buyer_name;
                            $unique_orders[] = $swing?->planning?->style?->order_no;

                            $today_total = 0;
                            for($h=$startHour; $h<$startHour + $swing->working_hours; $h++){
                                if(!$swing->isBreakHour($h)){
                                    $today_total += $swing->getProductionHour($h, $today_date);
                                }
                            }
                            $previous_total = $swing->outputs()->where('date','<',$today_date)->sum('production');
                            $grand_total = $today_total + $previous_total;
                            $style_qty = $swing->planning->style_qty;

                            $sum_target += $swing->capacity_hour;
                            $sum_today += $today_total;
                            $sum_previous += $previous_total;
                            $sum_grand += $grand_total;
                        @endphp

                        <tr data-style-qty="{{ $style_qty }}" data-style="{{ $style_no }}">
                            <td>{{ $swing->floor_name }} - {{ $swing->line_name }}</td>
                            <td>{{ $style_no }}</td>
                            <td>{{ $swing?->planning?->style?->order_no ?? '--' }}</td>
                            {{-- <td>{{ $swing?->planning?->style?->buyer_name ?? '--' }}</td> --}}
                            <td class="target">{{ $swing->capacity_hour }}</td>

                            @for($h=$startHour; $h<$endHour; $h++)
                                @if($h > $startHour + $swing->working_hours - 1)
                                    <td class="text-disabled">--</td>
                                @elseif($swing->isBreakHour($h))
                                    <td class="text-danger" style="background:#f9ecef">Break</td>
                                @else
                                    @php
                                        $value = $swing->getProductionHour($h, $today_date);
                                        $target = $swing->capacity_hour;
                                        $percentage = $target > 0 ? ($value / $target) * 100 : 0;
                                        if ($percentage >= 100) $badgeClass = 'value-tag high-performance';
                                        elseif ($percentage >= 95) $badgeClass = 'value-tag medium-performance';
                                        else $badgeClass = 'value-tag low-performance';
                                    @endphp
                                    <td contenteditable="true" class="data-row"
                                        data-plan="{{ $swing->id }}"
                                        data-hour="{{ $h }}"
                                        data-terget="{{ $swing->capacity_hour }}"
                                        data-date="{{ $today_date }}">
                                        <span class="{{ $badgeClass }}">{{ $value }}</span>
                                    </td>
                                @endif
                            @endfor

                            <td class="today badge-cell total-column">{{ $today_total }}</td>
                            <td class="previous total-column">{{ $previous_total }}</td>
                            <td class="grand total-column">{{ $grand_total }}</td>
                            <td class="balance total-column" style="color:#ff0000b5"></td>
                            <td>
                                <a href="{{route('admin.dailyProductionAction',['status-update','s_id' => $swing->id])}}" class="btn-custom success" onclick="return confirm('Are You Want To Complete This Line?')"><i class="bx bx-check"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{4 + $maxWorkingTime + 5}}" class="text-center text-muted"><i>No data found.</i></td>
                        </tr>
                    @endforelse

                    @if($swings->count() > 0)
                        <tr class="summary-hourly"  style="font-weight:bold;background:#eef3ff">
                            <td colspan="">Lines: {{ count($swings) }}</td>
                            <td colspan="">Style: {{ count(array_unique($unique_styles)) }}</td>
                            <td>Orders: {{ count(array_unique($unique_orders)) }}</td>
                            {{-- <td>Buyers: {{ count(array_unique($unique_buyers)) }}</td> --}}
                            <td>{{ $sum_target }}</td>
                            @php
                                $hourly_sums = [];
                                for($h = $startHour; $h < $endHour; $h++) {
                                    $hourly_sums[$h] = 0;

                                    foreach($swings as $swing) {
                                        // শুধু valid production hour-এ হিসাব নাও
                                        if($h <= $startHour + $swing->working_hours - 1 && !$swing->isBreakHour($h)) {
                                            $hourly_sums[$h] += $swing->getProductionHour($h, $today_date);
                                        }
                                    }
                                }
                            @endphp

                            @for($h=$startHour; $h<$endHour; $h++)
                                <td style="background:#f2f5fbcf !important" class="hourly-sum" data-hour="{{ $h }}">{{ $hourly_sums[$h] }}</td>
                            @endfor
                            <td id="sum-today">{{ $sum_today }}</td>
                            <td id="sum-prev">{{ $sum_previous }}</td>
                            <td id="sum-grand">{{ $sum_grand }}</td>
                            <td id="sum-balance" style="color:#ff0000c5"></td>
                            <td></td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    let startHour = {{ $startHour }};
    let endHour = {{ $endHour }};

    function getBadgeClass(value, target) {
        if (target == 0) return 'value-tag low-performance';
        let percentage = (value / target) * 100;
        if (percentage >= 100) return 'value-tag high-performance';
        if (percentage >= 95) return 'value-tag medium-performance';
        return 'value-tag low-performance';
    }

    function setBadge(cell, value){
        let target = parseInt(cell.data('terget')) || 0;
        let badgeClass = getBadgeClass(value, target);
        cell.html('<span class="'+badgeClass+'">'+value+'</span>');
    }

    function updateRowsAndSummary(){
        let styleBalances = {};

        $('tbody tr').each(function(){
            let row = $(this);
            if(!row.find('.today').length) return;

            let style = row.data('style');
            let style_qty = parseInt(row.data('style-qty')) || 0;
            let today = 0;

            row.find('.data-row').each(function(){
                today += parseInt($(this).text()) || 0;
            });

            let previous = parseInt(row.find('.previous').text()) || 0;
            let grand = today + previous;

            // accumulate per style
            if(!styleBalances[style]) styleBalances[style] = { style_qty: style_qty, grand: 0 };
            styleBalances[style].grand += grand;

            row.find('.today').text(today);
            row.find('.grand').text(grand);
        });

        // Update balance per row
        $('tbody tr').each(function(){
            let row = $(this);
            if(!row.find('.balance').length) return;

            let style = row.data('style');
            let style_qty = parseInt(row.data('style-qty')) || 0;
            let balance = style_qty - styleBalances[style].grand;
            row.find('.balance').text(balance);
        });

        // Update summary
        let sum_today = 0, sum_prev = 0, sum_grand = 0, sum_balance = 0;
        $('tbody tr').each(function(){
            let row = $(this);
            if(!row.find('.today').length) return;
            sum_today += parseInt(row.find('.today').text()) || 0;
            sum_prev += parseInt(row.find('.previous').text()) || 0;
            sum_grand += parseInt(row.find('.grand').text()) || 0;
        });
        for(let s in styleBalances){
            sum_balance += styleBalances[s].style_qty - styleBalances[s].grand;
        }

        $('#sum-today').text(sum_today);
        $('#sum-prev').text(sum_prev);
        $('#sum-grand').text(sum_grand);
        $('#sum-balance').text(sum_balance);
    }

    function hourlySummary() {
        let startHour = {{ $startHour }};
        let endHour = {{ $endHour }};

        // hourly sums initialize
        let hourly_sums = {};
        for(let h=startHour; h<endHour; h++) hourly_sums[h] = 0;

        // tbody row iterate করে hourly sums হিসাব করা
        $('tbody tr').each(function(){
            let row = $(this);
            row.find('.data-row').each(function(){
                let cell = $(this);
                let val = parseInt(cell.text()) || 0;
                let hour = parseInt(cell.data('hour'));
                if(hourly_sums[hour] !== undefined){
                    hourly_sums[hour] += val;
                }
            });
        });

        // update summary row td
        $('tr.summary-hourly td.hourly-sum').each(function(){
            let td = $(this);
            let h = parseInt(td.data('hour'));
            td.text(hourly_sums[h] || 0);
        });
    }


    $('.data-row').on('focus', function(){
        let cell = $(this);
        let value = cell.find('span').text() || cell.text();
        cell.text(value);
    });

    $('.data-row').on('blur', function(){
        let cell = $(this);
        let row = cell.closest('tr');
        let plan = cell.data('plan');
        let hour = cell.data('hour');
        let date = cell.data('date');
        let value = cell.text();

        setBadge(cell, value);
        updateRowsAndSummary();
        hourlySummary();

        if(!isNaN(value) && value !== ''){
            $.post('{{ route("admin.dailyProductionAction", ["action"=>"update"]) }}', {
                _token: '{{ csrf_token() }}',
                plan_id: plan,
                hour: hour,
                date: date,
                value: value
            });
        }
    });

    updateRowsAndSummary();
    // hourlySummary();

});
</script>
@endpush
@include(adminTheme().'productions.daily.css')
