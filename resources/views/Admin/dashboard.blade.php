@extends('Admin.layouts.app')
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
        color: #000 !important;
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
                <h3>120 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 56.9%</span></h3>

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
                <h3>150 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 32.1%</span></h3>

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
                <span class="sub-title">Peding order</span>
                <h3>333 <span class="badge badge-red"><i class="bx bx-down-arrow-alt"></i> 45.5%</span></h3>

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
                <span class="sub-title">Cansel order</span>
                <h3>100 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 26.0%</span></h3>

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
                <h3>1000 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 56.9%</span></h3>

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
                <h3>760 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 32.1%</span></h3>

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
                <h3>123<span class="badge badge-red"><i class="bx bx-down-arrow-alt"></i> 45.5%</span></h3>

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
                <h3>856 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 88.0%</span></h3>

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
                <h3>13456780 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 56.9%</span></h3>

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
                <h3>13456780 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 32.1%</span></h3>

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
            <div class="card mb-30">
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
                <div class="card mb-30">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3><i class="fa-regular fa-user"></i> Login Users</h3>

                        <div class="dropdown">
                            <button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-horizontal-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bx bx-show"></i> View
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bx bx-edit-alt"></i> Edit
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bx bx-trash"></i> Delete
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bx bx-printer"></i> Print
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bx bx-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body browser-used-box">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>MD. Nasim Billah</td>
                                        <td>01745354374</td>
                                        <td>11.02.2025 10:14 AM</td>
                                    </tr>
                                    <tr>
                                        <td>Emon Hasan</td>
                                        <td>01475354374</td>
                                        <td>11.02.2025 10:14 AM</td>
                                    </tr>
                                    <tr>
                                        <td>MD. Rabiul Hasan</td>
                                        <td>01475354374</td>
                                        <td>11.02.2025 10:14 AM</td>
                                    </tr>
                                     <tr>
                                        <td>MD. Nasim Billah</td>
                                        <td>01745354374</td>
                                        <td>11.02.2025 10:14 AM</td>
                                    </tr>
                                    <tr>
                                        <td>Emon Hasan</td>
                                        <td>01475354374</td>
                                        <td>11.02.2025 10:14 AM</td>
                                    </tr>
                                    <tr>
                                        <td>MD. Rabiul Hasan</td>
                                        <td>01475354374</td>
                                        <td>11.02.2025 10:14 AM</td>
                                    </tr>
                                      <tr>
                                        <td>Emon Hasan</td>
                                        <td>01475354374</td>
                                        <td>11.02.2025 10:14 AM</td>
                                    </tr>
                                    <tr>
                                        <td>MD. Rabiul Hasan</td>
                                        <td>01475354374</td>
                                        <td>11.02.2025 10:14 AM</td>
                                    </tr>
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

    </div>


    <div>

     <div class="">
        <!-- <div class="header">
            <h1>🏭 Line 14 - Daily Production Report</h1>
            <div class="header-info">
                <div><strong>Date:</strong> November 27, 2025</div>
                <div><strong>Shift:</strong> Day Shift</div>
                <div><strong>Style:</strong> DG-2547 Denim Jacket</div>
                <div><strong>Supervisor:</strong> Sarah Johnson</div>
            </div>
        </div> -->

        <!-- <div class="metrics">
            <div class="metric-card">
                <div class="metric-label">Target Production</div>
                <div class="metric-value">1,680</div>
                <div class="metric-subtext">units/day</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Actual Production</div>
                <div class="metric-value">1,542</div>
                <div class="metric-subtext">units completed</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Efficiency</div>
                <div class="metric-value efficiency">91.8%</div>
                <div class="metric-subtext">achievement rate</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Defect Rate</div>
                <div class="metric-value defect-rate">2.3%</div>
                <div class="metric-subtext">35 defective units</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Operators</div>
                <div class="metric-value">28</div>
                <div class="metric-subtext">active workers</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Downtime</div>
                <div class="metric-value">42</div>
                <div class="metric-subtext">minutes total</div>
            </div>
        </div> -->

        <div class="production-table">
            <h2>📊 Hourly Production Report</h2>
            <table>
                <thead>
                    <tr>
                        <th>Line No.</th>
                        <th>Hour</th>
                        <th>Target</th>
                        <th>Actual</th>
                        <th>Variance</th>
                        <th>Efficiency</th>
                        <th>Defects</th>
                        <th>Downtime</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                         <td>01</td>
                        <td><strong>08:00 - 09:00</strong></td>
                        <td>120</td>
                        <td>115</td>
                        <td>-5</td>
                        <td class="status-good">95.8%</td>
                        <td>2</td>
                        <td>5 min</td>
                        <td class="status-good">✓ Good</td>
                    </tr>
                    <tr>
                         <td>02</td>
                        <td><strong>09:00 - 10:00</strong></td>
                        <td>120</td>
                        <td>118</td>
                        <td>-2</td>
                        <td class="status-good">98.3%</td>
                        <td>3</td>
                        <td>0 min</td>
                        <td class="status-good">✓ Good</td>
                    </tr>
                    <tr>
                         <td>03</td>
                        <td><strong>10:00 - 11:00</strong></td>
                        <td>120</td>
                        <td>122</td>
                        <td>+2</td>
                        <td class="status-good">101.7%</td>
                        <td>2</td>
                        <td>0 min</td>
                        <td class="status-good">✓ Excellent</td>
                    </tr>
                    <tr>
                         <td>04</td>
                        <td><strong>11:00 - 12:00</strong></td>
                        <td>120</td>
                        <td>110</td>
                        <td>-10</td>
                        <td class="status-warning">91.7%</td>
                        <td>4</td>
                        <td>8 min</td>
                        <td class="status-warning">⚠ Below Target</td>
                    </tr>
                    <tr>
                         <td>05</td>
                        <td><strong>12:00 - 13:00</strong></td>
                        <td>120</td>
                        <td>95</td>
                        <td>-25</td>
                        <td class="status-poor">79.2%</td>
                        <td>3</td>
                        <td>15 min</td>
                        <td class="status-poor">✗ Lunch Break</td>
                    </tr>
                    <tr>
                         <td>06</td>
                        <td><strong>13:00 - 14:00</strong></td>
                        <td>120</td>
                        <td>108</td>
                        <td>-12</td>
                        <td class="status-warning">90.0%</td>
                        <td>2</td>
                        <td>6 min</td>
                        <td class="status-warning">⚠ Below Target</td>
                    </tr>
                    <tr>
                         <td>07</td>
                        <td><strong>14:00 - 15:00</strong></td>
                        <td>120</td>
                        <td>117</td>
                        <td>-3</td>
                        <td class="status-good">97.5%</td>
                        <td>3</td>
                        <td>2 min</td>
                        <td class="status-good">✓ Good</td>
                    </tr>
                    <tr>
                         <td>08</td>
                        <td><strong>15:00 - 16:00</strong></td>
                        <td>120</td>
                        <td>119</td>
                        <td>-1</td>
                        <td class="status-good">99.2%</td>
                        <td>2</td>
                        <td>0 min</td>
                        <td class="status-good">✓ Good</td>
                    </tr>
                    <tr>
                         <td>09</td>
                        <td><strong>16:00 - 17:00</strong></td>
                        <td>120</td>
                        <td>114</td>
                        <td>-6</td>
                        <td class="status-good">95.0%</td>
                        <td>3</td>
                        <td>4 min</td>
                        <td class="status-good">✓ Good</td>
                    </tr>
                    <tr>
                         <td>10</td>
                        <td><strong>17:00 - 18:00</strong></td>
                        <td>120</td>
                        <td>112</td>
                        <td>-8</td>
                        <td class="status-warning">93.3%</td>
                        <td>2</td>
                        <td>2 min</td>
                        <td class="status-warning">⚠ Below Target</td>
                    </tr>
                    <tr>
                         <td>11</td>
                        <td><strong>18:00 - 19:00</strong></td>
                        <td>120</td>
                        <td>116</td>
                        <td>-4</td>
                        <td class="status-good">96.7%</td>
                        <td>3</td>
                        <td>0 min</td>
                        <td class="status-good">✓ Good</td>
                    </tr>
                    <tr>
                         <td>12</td>
                        <td><strong>19:00 - 20:00</strong></td>
                        <td>120</td>
                        <td>118</td>
                        <td>-2</td>
                        <td class="status-good">98.3%</td>
                        <td>2</td>
                        <td>0 min</td>
                        <td class="status-good">✓ Good</td>
                    </tr>
                    <tr>
                         <td>13</td>
                        <td><strong>20:00 - 21:00</strong></td>
                        <td>120</td>
                        <td>113</td>
                        <td>-7</td>
                        <td class="status-warning">94.2%</td>
                        <td>2</td>
                        <td>0 min</td>
                        <td class="status-warning">⚠ Below Target</td>
                    </tr>
                    <tr>
                         <td>14</td>
                        <td><strong>21:00 - 22:00</strong></td>
                        <td>120</td>
                        <td>105</td>
                        <td>-15</td>
                        <td class="status-warning">87.5%</td>
                        <td>2</td>
                        <td>0 min</td>
                        <td class="status-warning">⚠ Below Target</td>
                    </tr>
                </tbody>
            </table>
            
            <!-- <div style="margin-top: 20px;">
                <strong>Overall Progress:</strong>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 91.8%"></div>
                </div>
            </div> -->
        </div>

        <!-- <div class="summary">
            <div>
                <span>Total Production</span>
                <strong>1,542 units</strong>
            </div>
            <div>
                <span>Target Achievement</span>
                <strong>91.8%</strong>
            </div>
            <div>
                <span>Quality Rate</span>
                <strong>97.7%</strong>
            </div>
            <div>
                <span>Total Downtime</span>
                <strong>42 minutes</strong>
            </div>
        </div> -->
    </div>

    </div>


</div>
@endsection

@push('js')

<script src="{{asset('admin/assets/js/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{asset('admin/assets/js/apexcharts/apex-custom-line-chart.js')}}"></script>

@endpush