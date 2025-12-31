@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Booking Form') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Booking Form</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item">Bookings</li>
            <li class="item">Add Booking</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')

            <link href="cdn.jsdelivr.net" rel="stylesheet">

            <div class="table-responsive">
                <table class="table table-bordered table-sm text-center" style="font-size: 0.75rem;">
                    <thead class="thead-light">
                        <tr>
                            <th rowspan="2" style="width: 3%">SL</th>
                            <th rowspan="2" style="width: 8%">BUYER</th>
                            <th rowspan="2" style="width: 8%">PI NO</th>
                            <th rowspan="2" style="width: 10%">FABRICATION</th>
                            <th rowspan="2" style="width: 5%">COLOR</th>
                            <th rowspan="2" style="width: 6%">YARN COUNT</th>
                            <th rowspan="2" colspan="2" style="width: 8%">YARN BOOKING</th>
                            <th rowspan="2" style="width: 7%">INHOUSE YARN</th>
                            <th rowspan="2" style="width: 8%">KNITTING FACTORY</th>
                            <th colspan="3">GREY RECEIVE FOR DYEING</th>
                            <th rowspan="2" style="width: 7%">DYEING FACTORY</th>
                            <th colspan="4">DYEING TO FINISHED FAB I/H</th>

                            <th rowspan="2" style="width: 10%">REMARKS</th>
                        </tr>
                        <tr>
                            <th style="width: 6%">TO YARN DELI</th>
                            <th style="width: 5%">KNITTING PROCESS LOSS (%)</th>
                            <th style="width: 6%">GREY RECEIVE</th>
                            <th style="width: 6%">TOTAL DYEING</th>
                            <th style="width: 5%">DYEING BALANCE</th>
                            <th style="width: 5%">GREY FINISH</th>
                            <th colspan="" rowspan="">DYE PROCESS LOSS (N)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>PRITY FASHION</td>
                            <td>ANR/PI/02</td>
                            <td>100% ORG. CTN S/J, 200 GSM</td>
                            <td>06 COLORS</td>
                            <td>22/1</td>
                            <td>5230</td>
                            <td>5230</td>
                            <td>MAHERIN MEHJBN</td>
                            <td>A&M</td>
                            <td>2430</td>
                            <td>-29</td>
                            <td>1.19</td>
                            <td>2401</td>
                            <td>0</td>
                            <td>2</td>
                            <td>1</td>
                            <td>1</td>
                            <td>KNIT SU 11 TIK 11 LIRB NE TIK WITH LYCRA</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>MAHERIN MEHJBN</td>
                            <td>2800</td>
                            <td>-30</td>
                            <td>1.07</td>
                            <td>2770</td>
                            <td>0</td>
                            <td>2</td>
                            <td></td>
                            <td></td>
                            <td>YARN PURCHASE 500 KG. ANOTHER 230 KO AN BUYER</td>
                        </tr>
                        <tr class="font-weight-bold">
                            <td colspan="3" class="text-right">TOTAL</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>5230</td>
                            <td>5171</td>
                            <td>-59</td>
                            <td>5171</td>
                            <td>0</td>
                            <td>2</td>
                            <td>2</td>
                            <td>1</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>


<div class="table-responsive">
    <table class="table table-bordered table-sm text-center" style="font-size: 0.75rem;">
        <thead class="thead-light">
            <!-- আপনার দেওয়া সেইম হেডার -->
            <tr>
                <th rowspan="2">SL</th>
                <th rowspan="2">BUYER</th>
                <th rowspan="2">PI NO</th>
                <th rowspan="2">FABRICATION</th>
                <th rowspan="2">COLOR</th>
                <th rowspan="2">YARN COUNT</th>
                <th rowspan="2" colspan="2">YARN BOOKING</th>
                <th rowspan="2">INHOUSE YARN</th>
                <th rowspan="2">KNITTING FACTORY</th>
                <th colspan="3">GREY RECEIVE FOR DYEING</th>
                <th rowspan="2">DYEING FACTORY</th>
                <th colspan="4">DYEING TO FINISHED FAB I/H</th>
                <th rowspan="2">REMARKS</th>
            </tr>
            <tr>
                <th>TO YARN DELI</th>
                <th>PROCESS LOSS (%)</th>
                <th>GREY RECEIVE</th>
                <th>TOTAL DYEING</th>
                <th>DYEING BALANCE</th>
                <th>GREY FINISH</th>
                <th>DYE LOSS</th>
            </tr>
        </thead>
        <tbody>
            @php
                // ১. পিআই লেভেল ক্যালকুলেশন
                $fabrication = $pi->items->unique('fabrication')->map(function($i){
                    return $i->fabrication . ' ' . $i->gsm . ' GSM';
                })->implode(', ');

                $colors = $pi->items->pluck('color_name')->unique();

                // ২. ইয়ান ডেটা
                $total_yarn_req = $pi->yarnBookings->sum('required_qty');
                $total_yarn_recv = \DB::table('yarn_receives')->where('pi_id', $pi->id)->sum('receive_qty');

                $unique_counts = collect();
                foreach ($pi->yarnBookings as $yb) {
                    $counts = json_decode($yb->yarn_count, true);
                    if(is_array($counts)) foreach($counts as $c) $unique_counts->push($c['count']);
                }
                $yarn_counts_string = $unique_counts->unique()->implode(', ');

                // ৩. নিটিং ডেটা
                $knitting_bookings = \DB::table('knitting_bookings')->where('pi_id', $pi->id)->get();
                $total_knit_qty = $knitting_bookings->sum('booking_qty');
                $total_grey_recv = \DB::table('knitting_receives')->where('pi_id', $pi->id)->sum('weight');

                // ৪. ডাইং ডেটা
                $dyeing_bookings = $pi->dyeingBookings;
                $total_dyeing_req = $dyeing_bookings->sum('required_qty');
                $total_dyeing_recv = \DB::table('dyeing_receives')->where('pi_id', $pi->id)->sum('receive_qty');
            @endphp

            <tr>
                <td>1</td>
                <td>{{ $pi->buyer->buyer_name ?? $pi->buyer_name }}</td>
                <td>{{ $pi->pi_no }}</td>
                <td>{{ $fabrication }}</td>
                <td>{{ $colors->count() }} COLORS</td>
                <td>{{ $yarn_counts_string }}</td>

                {{-- YARN BOOKING --}}
                <td>{{ number_format($total_yarn_req, 2) }}</td>
                <td>{{ number_format($total_yarn_req, 2) }}</td>

                {{-- INHOUSE YARN (Total Received) --}}
                <td>{{ number_format($total_yarn_recv, 2) }}</td>

                {{-- KNITTING FACTORY --}}
                <td>
                    @foreach($knitting_bookings->pluck('knitting_unit')->unique() as $unit)
                        {{ $unit }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </td>

                {{-- GREY RECEIVE FOR DYEING --}}
                <td>{{ number_format($total_knit_qty, 2) }}</td>
                <td>
                    @php
                        $k_loss = $total_knit_qty > 0 ? (($total_knit_qty - $total_grey_recv) / $total_knit_qty) * 100 : 0;
                    @endphp
                    {{ number_format($k_loss, 2) }}%
                </td>
                <td>{{ number_format($total_grey_recv, 2) }}</td>

                {{-- DYEING FACTORY --}}
                <td>
                    @foreach($dyeing_bookings->pluck('remarks')->unique() as $d_unit)
                        {{ $d_unit ?? 'N/A' }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </td>

                {{-- DYEING TO FINISHED --}}
                <td>{{ number_format($total_dyeing_req, 2) }}</td>
                <td>{{ number_format($total_dyeing_req - $total_dyeing_recv, 2) }}</td>
                <td>{{ number_format($total_dyeing_recv, 2) }}</td>
                <td>{{ number_format($total_grey_recv - $total_dyeing_recv, 2) }}</td>

                <td>{{ $pi->remarks }}</td>
            </tr>

            <!-- যদি আপনি ব্রেকডাউন দেখাতে চান তবে এখানে আরও রো অ্যাড করা যাবে -->
        </tbody>

        <tfoot class="font-weight-bold bg-light">
            <tr>
                <td colspan="6" class="text-right">TOTAL</td>
                <td>{{ number_format($total_yarn_req, 2) }}</td>
                <td>{{ number_format($total_yarn_req, 2) }}</td>
                <td>{{ number_format($total_yarn_recv, 2) }}</td>
                <td></td>
                <td>{{ number_format($total_knit_qty, 2) }}</td>
                <td></td>
                <td>{{ number_format($total_grey_recv, 2) }}</td>
                <td></td>
                <td>{{ number_format($total_dyeing_req, 2) }}</td>
                <td>{{ number_format($total_dyeing_req - $total_dyeing_recv, 2) }}</td>
                <td>{{ number_format($total_dyeing_recv, 2) }}</td>
                <td>{{ number_format($total_grey_recv - $total_dyeing_recv, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>



            <script src="cdn.jsdelivr.net"></script>
            <script src="cdn.jsdelivr.net"></script>

        </div>
    </div>
</div>
@endsection

@push('js')
@endpush
@push('css')
@endpush
