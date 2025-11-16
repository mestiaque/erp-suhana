@extends('Admin.layouts.app')
@section('title')
<title>Business Dashboard</title>
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
        color: #2196F3;
        margin-left: 20rem;
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
        <h1>Staff Dashboard</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="#"><i class="bx bx-home-alt"></i></a></li>
            <li class="item">Dashboard</li>
        </ol>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="stats-card-box">
                <div class="icon-text">
                    <div class="icon-box"><i class="bx bx-cart-alt"></i></div>
                    <span class="sub-title">Orders</span>
                </div>
                <h3>15</h3>
                <p>Recent Orders <a href="#">View</a></p>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="stats-card-box">
                <div class="icon-text">
                    <div class="icon-box"><i class="bx bx-money"></i></div>
                    <span class="sub-title">Revenue</span>
                </div>
                <h3>$1,500</h3>
                <p>Total Revenue <a href="#">View</a></p>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="stats-card-box">
                <div class="icon-text">
                    <div class="icon-box"><i class="bx bx-user"></i></div>
                    <span class="sub-title">Customers</span>
                </div>
                <h3>50</h3>
                <p>Total Customers <a href="#">View</a></p>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-30">
                <div class="card-header"><h3>Recent Orders</h3></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#1001</td>
                                <td>John Doe</td>
                                <td>$100</td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td>2025-11-10</td>
                            </tr>
                            <tr>
                                <td>#1002</td>
                                <td>Mary Johnson</td>
                                <td>$75</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>2025-11-12</td>
                            </tr>
                            <tr>
                                <td>#1003</td>
                                <td>Michael Scott</td>
                                <td>$50</td>
                                <td><span class="badge bg-danger">Cancelled</span></td>
                                <td>2025-11-13</td>
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
<!-- Custom JS for business -->
@endpush
