@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Daily Production') }}</title>
@endsection

@push('css')
<style type="text/css">
/* Optional: highlight editable cells on focus */
.data-row:focus {
    outline: 1px solid #4CAF50;
    background-color: #f0fff0;
}
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Daily Production</h3>
             <div class="dropdown">
                 <a href="{{ route('admin.dailyProduction') }}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{ route('admin.dailyProduction') }}">
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

            <!-- Production Table -->
            <div class="">
                <div class="table-responsive data-table">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="deliRport">
                            <tr>
                                <th style="min-width: 130px;">Line</th>
                                <th style="min-width: 125px;">Style</th>
                                <th>Target</th>
                                    @for($h = 8; $h <= 19; $h++)
                                        @php
                                            $start = ($h > 12) ? $h - 12 : $h;
                                            $endHour = $h + 1;
                                            $end = ($endHour > 12) ? $endHour - 12 : $endHour;
                                            $startPeriod = $h < 12 ? 'AM' : 'PM';
                                            $endPeriod = $endHour < 12 ? 'AM' : 'PM';
                                        @endphp
                                        <th>{{ $start }}-{{ $end }} <span>{{ $endPeriod }}</span></th>
                                    @endfor
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($swings as $swing)
                                <tr>
                                    <td class="line-label">
                                        <img src="{{ asset('admin/assets/img/erpline.webp') }}" alt="">
                                        {{ $swing->floor_name }} - {{ $swing->line_name }}
                                    </td>
                                    <td>
                                        @foreach($swing->planning as $plan)
                                            {{ $plan->style_no }}<br>
                                        @endforeach
                                    </td>
                                    <td>{{ $swing->capacity_hour }}</td>

                                    @php
                                        $rowTotal = 0;
                                    @endphp

                                    @for($h = 8; $h <= 19; $h++)
                                        @if($swing->isBreakHour($h))
                                            <td style="color: #e1000a;background: #f9ecef;">Break</td>
                                        @else
                                            @php
                                                $hourValue = $swing->getProductionHour($h);
                                                $rowTotal += $hourValue; // add to total
                                            @endphp
                                            <td contenteditable="true" class="data-row"
                                                data-line="{{ $swing->line_name }}"
                                                data-plan-id="{{ $swing->id }}"
                                                data-hour="{{ $h }}">
                                                {{ $hourValue }}
                                            </td>
                                        @endif
                                    @endfor

                                    <td class="total-column">{{ $rowTotal ?? 0 }}</td> <!-- show total -->
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){

        // ===== Total Update Function =====
        function updateTotal(row){
            let total = 0;

            row.find('.data-row').each(function(){
                let val = parseInt($(this).text().trim());
                if(!isNaN(val)) {
                    total += val;
                }
            });

            row.find('.total-column').text(total);
        }

        // ===== Editable Cell Event =====
        $(document).on('blur', '.data-row', function() {

            let cell  = $(this);
            let row   = cell.closest('tr');
            let planId = cell.data('plan-id');
            let line  = cell.data('line');
            let hour  = cell.data('hour');
            let value = cell.text().trim();

            let date = new Date().toISOString().slice(0, 10); // <-- Today's Date

            if (value === "" || isNaN(value)) {
                cell.text('0');
                value = 0;
            }

            updateTotal(row);

            $.ajax({
                url: '{{ route("admin.dailyProductionAction", ["action" => "update"]) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    plan_id: planId,
                    line: line,
                    hour: hour,
                    value: value,
                    date: date
                },
                success: function(res) {
                    if(res.success){
                        cell.css({"background":"#d4edda"});
                        setTimeout(() => cell.css("background",""), 300);
                    }
                }
            });
        });


    });
</script>
@endpush

@include(adminTheme().'productions.daily.css')


