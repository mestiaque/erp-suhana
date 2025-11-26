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
        color: white !important;
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
</div>
@endsection

@push('js')

<script src="{{asset('admin/assets/js/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{asset('admin/assets/js/apexcharts/apex-custom-line-chart.js')}}"></script>

@endpush