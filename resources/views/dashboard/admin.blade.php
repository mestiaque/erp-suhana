@extends('Admin.layouts.app')
@section('title')
<title>Admin Dashboard</title>
@endsection

@push('css')
<style>
    .stats-card-box {
        padding: 15px;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .stats-card-box .icon-text {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .stats-card-box .icon-box {
        font-size: 30px;
        margin-right: 10px;
        color: #4CAF50;
        margin-left: 12rem;
    }

    .stats-card-box .sub-title {
        font-size: 16px;
        font-weight: 600;
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

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-text">
                    <div class="icon-box"><i class="bx bx-bar-chart"></i></div>
                    <span class="sub-title">Point Orders</span>
                </div>
                <h3>25 <small>(02 Pending)</small></h3>
                <p>Total Sales <a href="#">View</a></p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-text">
                    <div class="icon-box"><i class="bx bx-cloud"></i></div>
                    <span class="sub-title">Point Buy</span>
                </div>
                <h3>40</h3>
                <p>Total Buy <a href="#">View</a></p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-text">
                    <div class="icon-box"><i class="bx bx-user"></i></div>
                    <span class="sub-title">Customers</span>
                </div>
                <h3>120</h3>
                <p>Total Customers <a href="#">View</a></p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-text">
                    <div class="icon-box"><i class="bx bx-money"></i></div>
                    <span class="sub-title">Cash Out</span>
                </div>
                <h3>$3,200</h3>
                <p>Total <a href="#">View</a></p>
            </div>
        </div>
    </div>

    <!-- Tables -->
    <div class="row">
        <div class="col-lg-7 col-md-12">
            <div class="card mb-30">
                <div class="card-header"><h3>Latest Point Orders</h3></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Seller</th>
                                <th>Amount</th>
                                <th>Commission</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John Doe</td>
                                <td>Jane Smith</td>
                                <td>100</td>
                                <td>10</td>
                                <td>2025-11-10</td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>Mary Johnson</td>
                                <td>Tom Brown</td>
                                <td>50</td>
                                <td>5</td>
                                <td>2025-11-12</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-md-12">
            <div class="card mb-30">
                <div class="card-header"><h3>New Registration</h3></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Sarah Connor</td>
                                <td>+123456789</td>
                                <td>2025-11-13</td>
                            </tr>
                            <tr>
                                <td>Michael Scott</td>
                                <td>+987654321</td>
                                <td>2025-11-14</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<!-- Custom JS for admin -->
@endpush
