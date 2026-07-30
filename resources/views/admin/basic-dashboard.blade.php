@php
    $widgetId = 'basic_widget_'.uniqid();
@endphp


    {{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg">
        <a href="{{ route('admin.usersAdmin') }}" class="ac-stat-card-link">
        <div class="ac-stat-card">
            <div class="ac-stat-icon" style="background:#ecfdf5;"><i class="fa-solid fa-users" style="color:#10b981;"></i></div>
            <div>
                <div class="ac-stat-val" style="color:#10b981;">{{ $totalUsers }}</div>
                <div class="ac-stat-lbl">Total Users</div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="{{ route('admin.usersAdmin', ['status' => 'active']) }}" class="ac-stat-card-link">
        <div class="ac-stat-card">
            <div class="ac-stat-icon" style="background:#eef2ff;"><i class="fa-solid fa-user-check" style="color:#6366f1;"></i></div>
            <div>
                <div class="ac-stat-val" style="color:#6366f1;">{{ $activeUsers }}</div>
                <div class="ac-stat-lbl">Active Users</div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="{{ route('admin.usersAdmin', ['status' => 'inactive']) }}" class="ac-stat-card-link">
        <div class="ac-stat-card">
            <div class="ac-stat-icon" style="background:#fffbeb;"><i class="fa-solid fa-user-xmark" style="color:#f59e0b;"></i></div>
            <div>
                <div class="ac-stat-val" style="color:#f59e0b;">{{ $inactiveUsers }}</div>
                <div class="ac-stat-lbl">Inactive Users</div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="javascript:void(0)" class="ac-stat-card-link">
        <div class="ac-stat-card">
            <div class="ac-stat-icon" style="background:#fff1f2;"><i class="fa-solid fa-user-shield" style="color:#f43f5e;"></i></div>
            <div>
                <div class="ac-stat-val" style="color:#f43f5e;">{{ $totalRoles }}</div>
                <div class="ac-stat-lbl">Total Roles</div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <a href="{{ route('admin.loginHistory') }}" class="ac-stat-card-link">
        <div class="ac-stat-card">
            <div class="ac-stat-icon" style="background:{{ $todayLogins >= 0 ? '#ecfdf5' : '#fff1f2' }};"><i class="fa-solid fa-right-to-bracket" style="color:{{ $todayLogins >= 0 ? '#10b981' : '#f43f5e' }};"></i></div>
            <div>
                <div class="ac-stat-val" style="color:{{ $todayLogins >= 0 ? '#10b981' : '#f43f5e' }};">{{ $todayLogins }}</div>
                <div class="ac-stat-lbl">Today Logins</div>
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
        .stat-card {
            border: 0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(21, 32, 56, 0.06);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(21, 32, 56, 0.1);
        }
        .stat-card .card-body {
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .stat-icon {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 44px;
            border-radius: 8px;
            font-size: 22px;
            background: #eef7f2;
            color: #18a575;
        }
        .stat-icon.warning {
            background: #fff5db;
            color: #c68300;
        }
        .stat-icon.info {
            background: #eaf3ff;
            color: #1769e0;
        }
        .stat-icon.danger {
            background: #fff0f0;
            color: #d32f2f;
        }
        .stat-icon.primary {
            background: #eaf3ff;
            color: #1769e0;
        }
        .stat-card span {
            display: block;
            color: #667085;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .stat-card strong {
            display: block;
            margin-top: 3px;
            color: #122033;
            font-size: 24px;
            line-height: 1;
        }
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
        .ac-stat-card-link {
            text-decoration: none;
        }
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
