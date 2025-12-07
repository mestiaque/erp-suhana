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

            <form>
                <div class="row mb-2">
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate ? Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') : '' }}" class="form-control" />
                            <input type="date" name="endDate" value="{{ request()->endDate ? Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') : '' }}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search Buyer, Style" class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>


            <div class="table-responsive data-table">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="deliRport">
                        <tr>
                            <th style="width:10rem">Line</th>
                            <th style="width:10rem">Style</th>
                            <th style="width:10rem">Order</th>
                            <th style="width:10rem">Buyer</th>
                            <th style="width:10rem">Target</th>
                            @for($h = 8; $h <= 19; $h++)
                                @php
                                    $start = ($h > 12) ? $h - 12 : $h;
                                    $endHour = $h + 1;
                                    $end = ($endHour > 12) ? $endHour - 12 : $endHour;
                                    $endPeriod = $endHour < 12 ? 'AM' : 'PM';
                                @endphp
                                <th style="width:20rem; white-space: nowrap;">{{ $start }}-{{ $end }} <span>{{ $endPeriod }}</span></th>
                            @endfor
                            <th style="width:10rem; white-space: nowrap;">Today Total</th>
                            <th style="width:10rem; white-space: nowrap;">Previous</th>
                            <th style="width:10rem; white-space: nowrap;">Grand Total</th>
                            <th style="width:10rem; white-space: nowrap;">Balance</th>
                        </tr>
                    </thead>
                    <tbody>

                    @php
                        $sum_target = 0;
                        $sum_today = 0;
                        $sum_previous = 0;
                        $sum_grand = 0;
                        $sum_balance = 0;
                        $unique_orders = [];
                        $unique_buyers = [];
                        $unique_styles = [];
                    @endphp

                    @forelse($swings as $swing)
                        @php
                            $style_no = $swing->planning->style_no;
                            $unique_styles[] = $style_no;
                            $unique_buyers[] = $swing->planning->style->buyer_name;
                            $unique_orders[] = $swing->planning->style->order_no;

                            $today_date = request('startDate') ?? date('Y-m-d');
                            $today_total = 0;
                            for($h=8;$h<=19;$h++){
                                if(!$swing->isBreakHour($h)){
                                    $today_total += $swing->getProductionHour($h,$today_date);
                                }
                            }
                            $previous_total = $swing->outputs()->where('date','<',$today_date)->sum('production');
                            $grand_total = $today_total + $previous_total;
                            $style_qty = $swing->planning->sum('style_qty');
                            $balance = $style_qty - $grand_total;

                            $sum_target += $swing->capacity_hour;
                            $sum_today += $today_total;
                            $sum_previous += $previous_total;
                            $sum_grand += $grand_total;
                            // sum_balance will be calculated per style in JS
                        @endphp

                        <tr data-style-qty="{{ $style_qty }}" data-style="{{ $style_no }}">
                            <td class="line-label" style="white-space: nowrap;">{{ $swing->floor_name }} - {{ $swing->line_name }}</td>
                            <td style="white-space: nowrap;">{{ $style_no }}</td>
                            <td style="white-space: nowrap;">{{ $swing->planning->style->order_no }}</td>
                            <td style="white-space: nowrap;">{{ $swing->planning->style->buyer_name }}</td>
                            <td class="target">{{ $swing->capacity_hour }}</td>

                            @for($h=8;$h<=19;$h++)
                                @if($swing->isBreakHour($h))
                                    <td class="text-danger" style="background:#f9ecef">Break</td>
                                @else
                                    <td contenteditable="true" class="data-row"
                                        data-plan="{{ $swing->id }}"
                                        data-hour="{{ $h }}"
                                        data-terget="{{ $swing->capacity_hour }}"
                                        data-date="{{ $today_date }}">

                                         @php
                                            $value = $swing->getProductionHour($h, $today_date);
                                            $target = $swing->capacity_hour;
                                            $percentage = $target > 0 ? ($value / $target) * 100 : 0;

                                            if ($percentage >= 100) $badgeClass = 'value-tag high-performance';
                                            elseif ($percentage >= 95) $badgeClass = 'value-tag medium-performance';
                                            else $badgeClass = 'value-tag low-performance';
                                        @endphp
                                        <span class="{{ $badgeClass }}">{{ $swing->getProductionHour($h,$today_date) }}</span>

                                    </td>
                                @endif
                            @endfor

                            <td class="today badge-cell total-column" data-target="{{ $swing->capacity_hour }}">{{ $today_total }}</td>
                            <td class="previous total-column">{{ $previous_total }}</td>
                            <td class="grand total-column">{{ $grand_total }}</td>
                            <td class="balance total-column" style="color: #ff0000b5">{{ $balance }}</td>
                        </tr>
                    @empty
                    <tr><td colspan="21" class="text-cenetr text-muted"><i>No data found.</i></td></tr>
                    @endforelse


                    @if(count($swings) > 0)
                    <!-- SUMMARY ROW -->
                    <tr style="font-weight:bold;background:#eef3ff">
                        <td colspan="" style="white-space: nowrap;">Lines: {{ count($swings) }}</td>
                        <td colspan="" style="white-space: nowrap;">Style: {{ count(array_unique($unique_styles)) }}</td>
                        <td style="white-space: nowrap;">Orders: {{ count(array_unique($unique_orders)) }}</td>
                        <td style="white-space: nowrap;">Buyers: {{ count(array_unique($unique_buyers)) }}</td>
                        <td>{{ $sum_target }}</td>
                        @for($h=8;$h<=19;$h++)
                            <td style="background:#f2f5fbcf !important"></td>
                        @endfor
                        <td id="sum-today">{{ $sum_today }}</td>
                        <td id="sum-prev">{{ $sum_previous }}</td>
                        <td id="sum-grand">{{ $sum_grand }}</td>
                        <td id="sum-balance" style="color:#ff0000c5"></td>
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
    function getBadgeClass(value, target) {
        if (target == 0) return 'value-tag low-performance'; // avoid division by zero

        let percentage = (value / target) * 100;

        if (percentage >= 100) return 'value-tag high-performance';        // >=100%
        if (percentage >= 95 && percentage < 100) return 'value-tag medium-performance'; // 95% - 99.999%
        return 'value-tag low-performance';                                // <95%
    }

    $('.data-row').on('focus', function(){
        // remove badge when editing
        let cell = $(this);
        cell.removeClass('value-tag high-performance medium-performance low-performance');
    });

    function setBadge(cell, value){
        let target = parseInt(cell.data('terget')) || 0;
        let badgeClass = getBadgeClass(value, target);
        // replace cell content with span and value
        cell.html('<span class="'+badgeClass+'">'+value+'</span>');
    }


    function updateRow(row){
        let total = 0;
        let style_qty = parseInt(row.data('style-qty')) || 0;

        row.find('.data-row').each(function(){
            total += parseInt($(this).text()) || 0; //badge
        });

        let previous = parseInt(row.find('.previous').text()) || 0;
        let grand = total + previous;
        let balance = style_qty - grand;

        row.find('.today').text(total);
        row.find('.grand').text(grand);
        row.find('.balance').text(balance);

        updateSummary();
    }

    function updateSummary(){
        let sum_today = 0, sum_prev = 0, sum_grand = 0, sum_balance = 0;
        let hourly_sums = {};
        let styleBalances = {};

        for(let h=8; h<=19; h++) hourly_sums[h] = 0;

        $('tbody tr').each(function(){
            let row = $(this);

            if(row.find('.today').length){
                sum_today += parseInt(row.find('.today').text()) || 0;
                sum_prev += parseInt(row.find('.previous').text()) || 0;
                sum_grand += parseInt(row.find('.grand').text()) || 0;
            }

            row.find('.data-row').each(function(){
                let h = $(this).data('hour');
                hourly_sums[h] += parseInt($(this).text()) || 0; //badge
            });

            let style = row.data('style');
            let style_qty = parseInt(row.data('style-qty')) || 0;
            let grand = parseInt(row.find('.grand').text()) || 0;

            if(!styleBalances[style]){
                styleBalances[style] = { style_qty: style_qty, grand: grand };
            } else {
                styleBalances[style].grand += grand;
            }
        });

        sum_balance = 0;
        for(let s in styleBalances){
            sum_balance += styleBalances[s].style_qty - styleBalances[s].grand;
        }

        // update hourly sums
        let summaryRow = $('tbody tr').last();
        let hourColIndex = 5;
        for(let h=8; h<=19; h++){
            summaryRow.find('td').eq(hourColIndex).text(hourly_sums[h]); //summary
            hourColIndex++;
        }

        $('#sum-today').text(sum_today);
        $('#sum-prev').text(sum_prev);
        $('#sum-grand').text(sum_grand);
        $('#sum-balance').text(sum_balance);
    }

    $('.data-row').on('blur', function(){
        let cell = $(this);
        let row = cell.closest('tr');

        let plan = cell.data('plan');
        let hour = cell.data('hour');
        let date = cell.data('date');
        let value = cell.text();


        setBadge(cell, value);

        updateRow(row);

        $.post('{{ route("admin.dailyProductionAction", ["action"=>"update"]) }}', {
            _token: '{{ csrf_token() }}',
            plan_id: plan,
            hour: hour,
            date: date,
            value: value
        });
    });

    $('.data-row').on('focus', function(){
        let cell = $(this);
        // get current number
        let value = cell.find('span').text() || cell.text();
        cell.text(value); // remove badge span
    });


    $('tbody tr').each(function(){
        if($(this).find('.today').length){
            updateRow($(this));
        }
    });

});
</script>
@endpush
@include(adminTheme().'productions.daily.css')
