@php
    $widgetId = 'basic_widget_'.uniqid();
@endphp
<div class="d-flex align-items-center justify-content-between mb-3 mt-1">
    <h4 class="mb-0" style="font-size:17px;font-weight:700;">
        <i class="fa-solid fa-boxes-stacked me-2" style="color:#1671f9;"></i> User & Role Overview
    </h4>
</div>

    {{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg">
        <a href="{{ route('admin.usersAdmin') }}" class="basic-stat-card-link">
        <div class="basic-stat-card">
            <div class="basic-stat-icon" style="background:#ecf0fd;"><i class="fa-solid fa-users" style="color:#1671f9;"></i></div>
            <div>
                <div class="basic-stat-val" style="color:#1671f9;">{{ $totalUsers }}</div>
                <div class="basic-stat-lbl">Total Users</div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="{{ route('admin.usersAdmin', ['status' => 'active']) }}" class="basic-stat-card-link">
        <div class="basic-stat-card">
            <div class="basic-stat-icon" style="background:#eef2ff;"><i class="fa-solid fa-user-check" style="color:#10b981;"></i></div>
            <div>
                <div class="basic-stat-val" style="color:#10b981;">{{ $activeUsers }}</div>
                <div class="basic-stat-lbl">Active Users</div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="{{ route('admin.usersAdmin', ['status' => 'inactive']) }}" class="basic-stat-card-link">
        <div class="basic-stat-card">
            <div class="basic-stat-icon" style="background:#fffbeb;"><i class="fa-solid fa-user-xmark" style="color:#f59e0b;"></i></div>
            <div>
                <div class="basic-stat-val" style="color:#f59e0b;">{{ $inactiveUsers }}</div>
                <div class="basic-stat-lbl">Inactive Users</div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="javascript:void(0)" class="basic-stat-card-link">
        <div class="basic-stat-card">
            <div class="basic-stat-icon" style="background:#fff1f2;"><i class="fa-solid fa-user-shield" style="color:#f43f5e;"></i></div>
            <div>
                <div class="basic-stat-val" style="color:#f43f5e;">{{ $totalRoles }}</div>
                <div class="basic-stat-lbl">Total Roles</div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="{{ route('admin.loginHistory') }}" class="basic-stat-card-link">
        <div class="basic-stat-card">
            <div class="basic-stat-icon" style="background:{{ $todayLogins >= 0 ? '#ecfdf5' : '#fff1f2' }};"><i class="fa-solid fa-right-to-bracket" style="color:{{ $todayLogins >= 0 ? '#10b981' : '#f43f5e' }};"></i></div>
            <div>
                <div class="basic-stat-val" style="color:{{ $todayLogins >= 0 ? '#10b981' : '#f43f5e' }};">{{ $todayLogins }}</div>
                <div class="basic-stat-lbl">Today Logins</div>
            </div>
        </div>
        </a>
    </div>
</div>

{{-- ── Charts Row 1 ── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="ac-chart-card">
            <div class="ac-section-title">Login Flow – Last 30 Days</div>
            <div id="{{ $widgetId }}_flow" style="height:220px;"></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ac-chart-card text-center">
            <div class="ac-section-title">Today Logins</div>
            <div id="{{ $widgetId }}_donut" style="height:220px;"></div>
        </div>
    </div>
</div>


@push('css')
    <style>
        .chart-card {
            border: 0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(21, 32, 56, 0.06);
        }
        .chart-card .card-body {
            padding: 18px;
        }
        #dashboardStatusChart {
            min-height: 320px;
        }

    </style>
    <style>
        .basic-stat-card-link { display: block; text-decoration: none; color: inherit; height: 100%; }
        .basic-stat-card { background: #fff; border-radius: 12px; padding: 20px 18px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); border: none; transition: transform .2s, box-shadow .2s; height: 100%; }
        .basic-stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.11); }
        .basic-stat-icon { width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .basic-stat-val { font-size: 24px; font-weight: 700; line-height: 1; margin-bottom: 3px; }
        .basic-stat-lbl { font-size: 12px; color: #888; font-weight: 500; text-transform: uppercase; letter-spacing: .5px; }
        .basic-section-title { font-size: 13px; font-weight: 700; color: #444; text-transform: uppercase; letter-spacing: 1px; border-left: 3px solid #f97316; padding-left: 10px; margin-bottom: 16px; }
        .basic-chart-card { background: #fff; border-radius: 12px; padding: 18px 20px; box-shadow: 0 2px 12px rgba(0,0,0,.07); height: 100%; }
        .basic-quick-btn { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 10px; background: #fff7ed; border: 1px solid #fde8d0; color: #444; font-size: 13px; font-weight: 500; text-decoration: none; transition: all .2s; }
        .basic-quick-btn:hover { background: #f97316; color: #fff; border-color: #f97316; }
        .basic-quick-btn i { width: 20px; text-align: center; }
        .basic-recent-table td { font-size: 13px; vertical-align: middle; padding: 8px 10px; }
        .basic-badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    </style>
@endpush


@push('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    (function() {
        var flowEl = document.getElementById('{{ $widgetId }}_flow');
        var donutEl = document.getElementById('{{ $widgetId }}_donut');

        var data = @json($chartData);
        var flowCategories = data.map(function(item) { return item.date; });
        var flowActive = data.map(function(item) { return item.active; });
        var flowInactive = data.map(function(item) { return item.inactive; });

        if (flowEl) {
            var flowOptions = {
                series: [{
                    name: 'Active',
                    data: flowActive
                }, {
                    name: 'Inactive',
                    data: flowInactive
                }],
                chart: {
                    type: 'area',
                    height: 220,
                    toolbar: { show: false },
                    fontFamily: 'Montserrat, Open Sans, sans-serif',
                },
                colors: ['#18a575', '#f43f5e'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: flowCategories,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#6b778c', fontSize: '12px' } }
                },
                yaxis: {
                    labels: { style: { colors: '#6b778c', fontSize: '12px' } }
                },
                grid: {
                    borderColor: '#edf1f6',
                    strokeDashArray: 4,
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    markers: { radius: 12 },
                    labels: { colors: '#17233c' }
                },
                tooltip: {
                    theme: 'light',
                    x: { show: true }
                }
            };

            var flowChart = new ApexCharts(flowEl, flowOptions);
            flowChart.render();
        }

        if (donutEl) {
            var nonLoginCount = Math.max(0, {{ $totalUsers }} - {{ $todayLogins }});
            var donutOptions = {
                series: [{{ $todayLogins }}, nonLoginCount],
                chart: {
                    type: 'donut',
                    height: 220,
                    fontFamily: 'Montserrat, Open Sans, sans-serif',
                },
                labels: ['Today Logins', 'Not Logged In'],
                colors: ['#10b981', '#e5e7eb'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Users',
                                    color: '#17233c',
                                    fontSize: '14px',
                                    fontWeight: 600,
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false },
                legend: {
                    position: 'bottom',
                    labels: { colors: '#17233c' }
                },
                tooltip: {
                    theme: 'light',
                }
            };

            var donutChart = new ApexCharts(donutEl, donutOptions);
            donutChart.render();
        }
    })();
</script>
@endpush
