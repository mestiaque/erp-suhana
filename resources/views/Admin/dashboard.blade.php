@extends('Admin.layouts.app')
@section('title')
<title>Dashboard</title>
@endsection

@push('css')

<style type="text/css">
    #eventModal {
      display: none;
      position: fixed;
      top: 20%;
      left: 50%;
      transform: translateX(-50%);
      padding: 20px;
      z-index: 1000;
      max-width: 600px;
      width: 100%;
    }
    
    #eventModal .body{
        background: white;
        padding: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }

    #modalOverlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
    }

    #eventModal h3 {
      margin-top: 0;
      font-size: 24px;
    }

    #eventLink {
      color: black;
      display: inline-block;
      margin-top: 10px;
    }

    .close-btn {
      background-color: #f44336;
      color: white;
      border: none;
      padding: 5px 10px;
      cursor: pointer;
      border-radius: 5px;
      float: right;
    }
    .bx-coin:before {
        content: "\f2db"
    }
</style>
@endpush
@section('contents')

<div class="flex-grow-1">
    


<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>Dashboard</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="#"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">Dashboard</li>
    </ol>
</div>
<!-- End Breadcrumb Area -->

<!-- Start -->
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="stats-card-box">
            <div class="icon-box">
                <i class="bx bx-bar-chart"></i>
            </div>
            <span class="sub-title">Point Orders</span>
            <h3>
                0 <small style="font-size: 16px;font-weight: bold;">(02)</small>
            </h3>

            <div class="progress-list">
                <!--<div class="bar-inner">-->
                <!--    <div class="bar progress-line" data-width="56.9"></div>-->
                <!--</div>-->
                <p>Total Sales <a href="#">View</a> </p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card-box">
            <div class="icon-box">
                <i class="bx bx-cloud"></i>
            </div>
            <span class="sub-title">Point Buy</span>
            <h3>
                0
                <br> <span class="badge"><i class="bx bx-phone"></i> 0 </span>
            </h3>
            <div class="progress-list">
                <!--<div class="bar-inner">-->
                <!--    <div class="bar progress-line" data-width="82"></div>-->
                <!--</div>-->

                <p>Total Buy <a href="">View</a> </p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card-box">
            <div class="icon-box">
                <i class="bx bx-bar-chart-alt"></i>
            </div>
            <span class="sub-title">Customers</span>
            <h3>
                0
                <br><span class="badge"><i class="bx bx-up-arrow-alt"></i> Total</span>
            </h3>
            <div class="progress-list">
                <!--<div class="bar-inner">-->
                <!--    <div class="bar progress-line" data-width="80"></div>-->
                <!--</div>-->

                <p>Total Customers <a href="#">View</a> </p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card-box">
            <div class="icon-box">
                <i class="bx bx-money"></i>
            </div>
            <span class="sub-title">Cash Out</span>
            <h3>
                0
                <br><span class="badge"><i class="bx bx-up-arrow-alt"></i> Total</span>
            </h3>
            <div class="progress-list">
                <p>Total  <a href="#">View</a> </p>
            </div>
        </div>
    </div>
</div>
<!-- End -->

<!-- Start -->
<div class="row">
    <div class="col-lg-7 col-md-12">
        <div class="card mb-30">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Latest Point Orders</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-20">
                        <thead>
                            <tr class="table__title">
                                <th>Customer</th>
                                <th>Seller</th>
                                <th>Amount</th>
                                <th>Commission</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table__body">
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 col-md-12">
        <div class="card mb-30">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>New Registration</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-20">
                        <thead>
                            <tr class="table__title">
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="table__body">
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End -->
</div>
@endsection

@push('js')



@endpush