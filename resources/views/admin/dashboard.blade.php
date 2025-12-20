@extends('admin.layouts.app')
@section('title')
<title>Admin Dashboard</title>
@endsection

@push('css')
<style>


.stats-card-box .icon-box {
    display: flex;
    align-items: center;
    justify-content: center
}

.stats-card-box .sub-title {
    color: #000;
}


 .animated-list{
    list-style:none;
    padding:0;
    margin:0;
    display: flex;
    margin-left: 10px;
  }

  .animated-list li{
    display:flex;
    align-items:center;
    gap:12px;
    margin-left: 10px;
    background:#fff;
    padding:10px 12px;
    border-radius:10px;
    margin-bottom:10px;
    box-shadow:0 6px 14px rgba(14,18,36,0.06);
    transform:translateY(10px);
    opacity:0;
    animation: slideIn .45s ease forwards;
  }

  /* stagger */
  .animated-list li:nth-child(1){ animation-delay:.08s; }
  .animated-list li:nth-child(2){ animation-delay:.18s; }
  .animated-list li:nth-child(3){ animation-delay:.28s; }

  .bullet{
    width:14px;
    height:14px;
    border-radius:50%;
    flex-shrink:0;
    position:relative;
    box-shadow:0 2px 6px rgba(0,0,0,0.12);
    display:inline-block;
  }

  .label{
    font-weight:600;
  }
  .sub{ font-size:13px; color:var(--muted); margin-left:4px; font-weight:500; }

  /* pulsing ring */
  .bullet::after{
    content:"";
    position:absolute;
    left:50%; top:50%;
    transform:translate(-50%,-50%);
    width:100%;
    height:100%;
    border-radius:50%;
    opacity:.25;
    animation: pulse 1.6s infinite ease-out;
  }

  /* colors */
  .b1{ background:green; }
  .b1::after{ background: green; }
  .b2{ background: red; }
  .b2::after{ background: red; }
  .b3{ background: blue; }
  .b3::after{ background: blue; }

  @keyframes pulse{
    0%{ transform:translate(-50%,-50%) scale(.9); opacity:.28; }
    70%{ transform:translate(-50%,-50%) scale(2.2); opacity:0; }
    100%{ opacity:0; }
  }

  @keyframes slideIn{
    to{ transform:none; opacity:1; }
  }


.card-header {
    display: flex;
    align-items: center;
}
h4{
    font-size: 20px;
}

.browser-used-box table thead th{
        color: #fff !important;
}

.value-tag {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: bold;
}

/* Colors */
.low-performance {
    background: #ea3a3b;  /* Red */
    color: #fff;
}
.medium-performance {
    background: #c8ffcd; /* Light Green */
    color: #000;
}
.high-performance {
    background: #00994d; /* Deep Green */
    color: #fff;
}


/* production report css */

   .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header-info {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .header-info div {
            background: rgba(255,255,255,0.1);
            padding: 10px 20px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 30px;
            background: #f8f9fa;
        }

        .metric-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .metric-label {
            color: #7f8c8d;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .metric-value {
            font-size: 2.5em;
            font-weight: bold;
            color: #2c3e50;
        }

        .metric-subtext {
            color: #95a5a6;
            font-size: 0.85em;
            margin-top: 8px;
        }

        .efficiency {
            color: #27ae60;
        }

        .defect-rate {
            color: #e74c3c;
        }


        .production-table h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th, td {
            padding: 15px;
            text-align: center;
        }

        th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 1px;
        }

        tbody tr {
            border-bottom: 1px solid #ecf0f1;
            transition: background 0.2s;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        .status-good {
            color: #27ae60;
            font-weight: bold;
        }

        .status-warning {
            color: #f39c12;
            font-weight: bold;
        }

        .status-poor {
            color: #e74c3c;
            font-weight: bold;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #ecf0f1;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }

        .summary {
            background: #2c3e50;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .summary div {
            flex: 1;
            min-width: 150px;
        }

        .summary strong {
            display: block;
            font-size: 1.8em;
            margin-top: 5px;
        }
        .stats-card-box h3 {
            font-size: 22px;
            font-weight: 600;
        }






.header-info {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .data-table {
            background: white;
            border-radius: 8px;
            overflow-x: auto;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table {
            font-size: 0.85rem;
        }
        .table .deliRport th {
            background-color: #6c757d;
            color: white;
            font-weight: 600;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            text-align: center;
        }
        .table tbody td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }
        .line-label {
            font-weight: 600;
            background-color: #e9ecef;
            text-align: left;
        }
        .data-row {
            font-family: monospace;
            font-size: 0.75rem;
        }
        .total-column {
            background-color: #effff6c7;
            font-weight: 600;
        }
        .date-header {
            font-size: 0.75rem;
            padding: 5px;
        }
        .crossed-out {
            text-decoration: line-through;
            color: #6c757d;
        }
        .notes-section {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }


        .line-label img {
            width: 35px;
            display: block;
            background: #19ff5b;
        }

        @media (max-width: 1400px) {
            .production-table h2 {
                font-size: 18px;
                font-weight: 600;
            }
            .breadcrumb-area h1 {
                font-size: 19px;
                font-weight: 500;
            }
            .card .card-header h3 {
                font-size: 16px;
                font-weight: 500;
            }
            .label {
                font-weight: 600;
                font-size: 12px;
            }
            h4 {
                font-size: 18px;
                font-weight: 500;
            }
            .stats-card-box h3 {
                font-size: 14px;
                font-weight: 600;
            }
            .stats-card-box {
                margin-bottom: 25px;
                padding: 15px 10px 10px 70px;
            }
            .stats-card-box .icon-box {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }
            .stats-card-box .sub-title {
                font-size: 14px;
            }


        }

        @media (max-width: 1240px) {
            .stats-card-box .sub-title {
                font-size: 12px;
            }
                .stats-card-box h3 {
                font-size: 12px;
            }
            .stats-card-box h3 .badge {
                font-size: 9px;
                        }
        }


        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8em;
            }

            .metrics {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 0.85em;
            }

            th, td {
                padding: 10px 5px;
            }
        }



</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <!-- Breadcrumb -->
    <div class="breadcrumb-area">
        <h1>Admin Dashboard</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="#"><i class="bx bx-home-alt"></i></a></li>
            <li class="item">Dashboard</li>
        </ol>
    </div>

    <h4 class="mb-3"><i class="fa fa-clipboard"></i> Order Summary</h4>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-check-circle"></i>
                </div>
                <span class="sub-title">Order total</span>
                <h3>{{number_format($reports['total_order'])}} <span class="badge"><i class="bx bx-up-arrow-alt"></i> 56.9%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="56.9" style="width: 56.9%;"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-check-circle"></i>
                </div>
                <span class="sub-title">Confirm order</span>
                <h3>{{number_format($reports['total_order_confirmed'])}} <span class="badge"><i class="bx bx-up-arrow-alt"></i> 32.1%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="32.1" style="width: 32.1%;"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-check-circle"></i>
                </div>
                <span class="sub-title">Pending order</span>
                <h3>{{number_format($reports['total_order_pending'])}} <span class="badge badge-red"><i class="bx bx-down-arrow-alt"></i> 45.5%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="45.5" style="width: 45.5%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-times-circle"></i>
                </div>
                <span class="sub-title">Cancelled order</span>
                <h3>{{number_format($reports['total_order_cancelled'])}} <span class="badge"><i class="bx bx-up-arrow-alt"></i> 26.0%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="26.0" style="width: 26%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-3"><i class="fa fa-users"></i> Office Staff</h4>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box" style="background-color: #d81be9;">
                    <i class="fa fa-user-friends"></i>
                </div>
                <span class="sub-title">Total Staff</span>
                <h3>{{number_format($reports['total_staff'])}} <span class="badge"><i class="bx bx-up-arrow-alt"></i> 56.9%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="56.9" style="width: 56.9%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box" style="background-color: #185806;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <span class="sub-title">Present Staff</span>
                <h3>{{number_format($reports['total_staff_present'])}} <span class="badge"><i class="bx bx-up-arrow-alt"></i> 32.1%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="32.1" style="width: 32.1%;"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box" style="background-color: #b20c0c;">
                    <i class="fa fa-times-circle"></i>
                </div>
                <span class="sub-title">Absent Staff</span>
                <h3>{{number_format($reports['total_staff_absent'])}} <span class="badge badge-red"><i class="bx bx-down-arrow-alt"></i> 45.5%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="45.5" style="width: 45.5%;"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box" style="background-color: #ea043a;">
                    <i class="fa fa-briefcase"></i>
                </div>
                <span class="sub-title">Worker Present Staff</span>
                <h3>{{number_format($reports['total_staff_worked'])}} <span class="badge"><i class="bx bx-up-arrow-alt"></i> 88.0%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="26.0" style="width: 26%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-3"><i class="fa-solid fa-money-bill"></i> Accounts</h4>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-line-chart"></i>
                </div>
                <span class="sub-title">Total Sale Amount</span>
                <h3>{{priceFullFormat($reports['total_sale'])}} <span class="badge"><i class="bx bx-up-arrow-alt"></i> 56.9%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="56.9" style="width: 56.9%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-file-text"></i>
                </div>
                <span class="sub-title">Order Amount</span>
                <h3>{{priceFullFormat($reports['total_order_amount'])}} <span class="badge"><i class="bx bx-up-arrow-alt"></i> 32.1%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="32.1" style="width: 32.1%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-credit-card"></i>
                </div>
                <span class="sub-title">Total Expense Amount</span>
                <h3>{{priceFullFormat($reports['total_expenses'])}}<span class="badge badge-red"><i class="bx bx-down-arrow-alt"></i> 45.5%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="45.5" style="width: 45.5%;"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-credit-card"></i>
                </div>
                <span class="sub-title">Total I.O.U</span>
                <h3>{{priceFullFormat($reports['total_IOU'])}}
                    <!-- <span class="badge"><i class="bx bx-up-arrow-alt"></i> 88.0%</span> -->
                </h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="26.0" style="width: 26%;"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-30" style="min-height: 430px;">
                <div class="card-header">
                    <h3><i class="fa fa-line-chart"></i> Yearly Charts</h3>
                    <ul class="animated-list" aria-label="Status list">
                        <li>
                            <span class="bullet b1" aria-hidden="true"></span>
                            <div>
                            <div class="label">80% Sent <span class="sub">• Sent</span></div>
                            </div>
                        </li>
                        <li>
                            <span class="bullet b2" aria-hidden="true"></span>
                            <div>
                            <div class="label">75% Read <span class="sub">• Read</span></div>
                            </div>
                        </li>
                        <li>
                            <span class="bullet b3" aria-hidden="true"></span>
                            <div>
                            <div class="label">33% Unread <span class="sub">• Unread</span></div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="card-body" style="position: relative;">
                    <div id="website-analytics-chart" class="extra-margin"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
                <div class="card mb-30" style="min-height: 430px;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3><i class="fa-regular fa-user"></i> Login Users</h3>
                    </div>

                    <div class="card-body browser-used-box">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($userActivity as $ua)
                                        <tr>
                                            <td>{{ $ua['name'] }}</td>
                                            <td>{{ $ua['mobile'] }}</td>
                                            <td>{{ $ua['login_at'] }}</td>
                                            <td>
                                                @if($ua['active_status'])
                                                    <span class="badge bg-success text-light">Active Now</span>
                                                @else
                                                    <span class="badge bg-secondary text-light">{{ $ua['last_active_ago'] ?? '--' }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty

                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

    </div>


    <div>


        @php
            $now = now(); // current timestamp
            $nextHour = $now->copy()->addHour(); // now + 1 hour
            $swings = App\Models\ProductionSewing::with(['planning', 'planning.style', 'outputs'])
                            ->whereHas('planning', function ($q) use ($now, $nextHour) {
                                // Filter by exact datetime instead of date only
                                $q->where('status', 'confirmed')
                                ->where('sewing_start', '<=', $now)
                                ->where('sewing_end', '>=', $nextHour);
                            })
                            ->get();
            function badgeClass($value, $target){
                if($target==0) return 'value-tag low-performance';
                $percentage = ($value/$target)*100;
                if($percentage >= 100) return 'value-tag high-performance';
                elseif($percentage >= 95) return 'value-tag medium-performance';
                else return 'value-tag low-performance';
            }
        @endphp

        <div class="">
            <div class="table-responsive data-table">
                @php
                    $maxWorkingTime = $swings->pluck('working_hours')->max() ?? 9; // maximum across all lines
                    $startHour = 8;
                    $endHour = $startHour + $maxWorkingTime;
                    $today_date = request('startDate') ?? date('Y-m-d');
                @endphp

                <table class="table table-bordered table-striped mb-0">
                    <thead class="deliRport">
                        <tr>
                            <th>Line</th>
                            <th>Style</th>
                            <th>Order</th>
                            {{-- <th>Buyer</th> --}}
                            <th>Target</th>

                            @for($h = $startHour; $h < $endHour; $h++)
                                @php
                                    $start = ($h > 12) ? $h - 12 : $h;
                                    $endHourNext = $h + 1;
                                    $end = ($endHourNext > 12) ? $endHourNext - 12 : $endHourNext;
                                    $endPeriod = $endHourNext < 12 ? 'AM' : 'PM';
                                @endphp
                                <th style="white-space: nowrap;">{{ $start }}-{{ $end }} {{ $endPeriod }}</th>
                            @endfor

                            <th>Today Total</th>
                            <th>Previous</th>
                            <th>Grand Total</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        $sum_target = $sum_today = $sum_previous = $sum_grand = 0;
                        $unique_orders = $unique_buyers = $unique_styles = [];
                        $hourly_sums = [];
                        for($h=$startHour; $h<$endHour; $h++) $hourly_sums[$h] = 0;
                        $styleBalances = [];
                    @endphp

                    @forelse($swings as $swing)
                        @php
                            $style_no = $swing->planning->style_no;
                            $unique_styles[] = $style_no;
                            $unique_buyers[] = $swing?->planning?->style?->buyer_name;
                            $unique_orders[] = $swing?->planning?->style?->order_no;

                            $today_total = 0;
                            $hour_values = [];

                            for($h=$startHour; $h<$endHour; $h++){
                                if($h >= $startHour + $swing->working_hours){
                                    $hour_values[$h] = null; // disabled beyond this line's working hours
                                } elseif($swing->isBreakHour($h)){
                                    $hour_values[$h] = 'Break';
                                } else {
                                    $val = $swing->getProductionHour($h,$today_date);
                                    $hour_values[$h] = $val;
                                    $today_total += $val;
                                    $hourly_sums[$h] += $val;
                                }
                            }

                            $previous_total = $swing->outputs()->where('date','<',$today_date)->sum('production');
                            $grand_total = $today_total + $previous_total;
                            $style_qty = $swing->planning->style_qty;

                            // per-style balance
                            if(!isset($styleBalances[$style_no])){
                                $styleBalances[$style_no] = ['style_qty'=>$style_qty, 'grand'=>$grand_total];
                            } else {
                                $styleBalances[$style_no]['grand'] += $grand_total;
                            }

                            $balance = $style_qty - $styleBalances[$style_no]['grand'];

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
                            <td>{{ $swing->capacity_hour }}</td>

                            @for($h=$startHour; $h<$endHour; $h++)
                                @if(is_null($hour_values[$h]))
                                    <td class="text-disabled">--</td>
                                @elseif($hour_values[$h] === 'Break')
                                    <td class="text-danger" style="background:#f9ecef">Break</td>
                                @else
                                    <td>
                                        <span class="{{ badgeClass(intval($hour_values[$h]), intval($swing->capacity_hour)) }}">
                                            {{ $hour_values[$h] }}
                                        </span>
                                    </td>
                                @endif
                            @endfor

                            <td>{{ $today_total }}</td>
                            <td>{{ $previous_total }}</td>
                            <td>{{ $grand_total }}</td>
                            <td style="color:#ff0000b5">{{ $balance }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="{{4 + $maxWorkingTime + 4}}" class="text-center text-muted"><i>No data found.</i></td></tr>
                    @endforelse

                    @php
                        $sum_balance = 0;
                        foreach($styleBalances as $style){
                            $sum_balance += $style['style_qty'] - $style['grand'];
                        }
                    @endphp

                    @if(count($swings) > 0)
                        <tr style="font-weight:bold;background:#eef3ff">
                            <td colspan="">Lines: {{ count($swings) }}</td>
                            <td colspan="">Style: {{ count(array_unique($unique_styles)) }}</td>
                            <td>Orders: {{ count(array_unique($unique_orders)) }}</td>
                            {{-- <td>Buyers: {{ count(array_unique($unique_buyers)) }}</td> --}}
                            <td>{{ $sum_target }}</td>
                            @for($h=$startHour; $h<$endHour; $h++)
                                <td style="background:#f2f5fbcf">{{ $hourly_sums[$h] }}</td>
                            @endfor
                            <td>{{ $sum_today }}</td>
                            <td>{{ $sum_previous }}</td>
                            <td>{{ $sum_grand }}</td>
                            <td style="color:#ff0000c5">{{ $sum_balance }}</td>
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


<script>
   $(document).ready(function () {
    $("table tbody tr").each(function () {
        let target = parseInt($(this).find("td").eq(2).text());

        $(this).find("td.data-row").each(function () {
            let val = parseInt($(this).text());

            if (!isNaN(val)) {
                let percent = (val / target) * 100;

                // wrap value with tag
                $(this).html('<span class="value-tag">'+val+'</span>');

                let tag = $(this).find("span");

                if (percent <= 80) {
                    tag.addClass("low-performance");
                }
                else if (percent <= 90) {
                    tag.addClass("medium-performance");
                }
                else {
                    tag.addClass("low-performance");
                }
            }
        });
    });
});
</script>

<script src="{{asset('admin/assets/js/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{asset('admin/assets/js/apexcharts/apex-custom-line-chart.js')}}"></script>

@endpush
