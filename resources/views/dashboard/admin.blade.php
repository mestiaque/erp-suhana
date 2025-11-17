@extends('Admin.layouts.app')
@section('title')
<title>Admin Dashboard</title>
@endsection

@push('css')
<style>
    /* .stats-card-box {
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
    } */



    /* dashboard ui css start */

    /* .dashboardInfoContainer svg {
            margin-right: 5px;
        }

        .dashboardInfoContainer h2 {
            font-size: 24px;
        }

    .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .order-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 5px solid;
        }
        
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .order-card.electronics {
            border-left-color: #667eea;
                display: flex;
                align-items: center;
                justify-content: space-between;
        }
        
        .order-card.furniture {
            border-left-color: #f093fb;
                display: flex;
                align-items: center;
                justify-content: space-between;
        }
        .order-card.furniture h3 {
            margin: 0;
        }
        .order-card.clothing {
            border-left-color: #4facfe;
        }
        
        .order-card.books {
            border-left-color: #43e97b;
        }
        
        .order-card.office {
            border-left-color: #fa709a;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fc;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        
        .badge-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-total {
            background: #d4edda;
            color: #155724;
        }
        
        .category-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .order-card.electronics h3 {
    margin: 0;
}
        h3 {
            font-size: 1.1rem;
            font-weight: 700;
        }
        
        .order-detail {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 5px 0;
        }
        
        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
            margin-top: 10px;
        } */

    /* dashboard ui css end */






.stats-card-box .icon-box {
    display: flex;
    align-items: center;
    justify-content: center
}

.stats-card-box .sub-title {
    color: #000;
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
    <!-- <div class="row">
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
    </div> -->

    <!-- Tables -->
    <!-- <div class="row">
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
    </div> -->

        <h4 class="mb-4"><i class="fa fa-clipboard"></i> Order Summary</h4>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card-box">
                    <div class="icon-box">
                        <i class="fa fa-money"></i>
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
                        <i class="fa fa-clock-o"></i>
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

        <h4 class="mb-4"><i class="fa fa-users"></i> Office Staff</h4>
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
                    <span class="sub-title">Working Staff</span>
                    <h3>856 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 88.0%</span></h3>

                    <div class="progress-list">
                        <div class="bar-inner">
                            <div class="bar progress-line" data-width="26.0" style="width: 26%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="mb-4"><i class="fa fa-money"></i>Accounts</h4>
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
                    <h3>12300<span class="badge badge-red"><i class="bx bx-down-arrow-alt"></i> 45.5%</span></h3>

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
                    <span class="sub-title">Net Profit / Balance</span>
                    <h3>13456780<span class="badge"><i class="bx bx-up-arrow-alt"></i> 88.0%</span></h3>

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
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3><i class="fa fa-line-chart"></i> Website Analytics</h3>

                        <div class="dropdown">
                            <button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-horizontal-rounded"></i>
                            </button>
                            <div class="dropdown-menu" style="">
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
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-30">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3><i class="fa-regular fa-user"></i> Login User</h3>

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
                                        <th>Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>MD. Nasim Billah</td>
                                        <td>01864748523</td>
                                    </tr>
                                    <tr>
                                        <td>MD. Rabiul Islam</td>
                                        <td>01564748523</td>
                                    </tr>
                                    <tr>
                                        <td>MD. Emon Hasan</td>
                                        <td>01464748523</td>
                                    </tr>
                                    
                            
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>



<div class="dashboardInfoContainer">


 <!-- <h2 class="mb-4"><i class="fa-brands fa-first-order"></i>Order Summary</h2>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="order-card electronics">
                    <div class="icon-box" style="background: #e8eaf6;">
                        <i class="fas fa-laptop" style="color: #667eea;"></i>
                    </div>
                    <h3>Order Total</h3>
                    <div class="price">100</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card electronics">
                    <div class="icon-box" style="background: #e3f2fd;">
                        <i class="fas fa-mobile-alt" style="color: #2196F3;"></i>
                    </div>
                 
                    <h3>Cansel Order</h3>
                    <div class="price">40</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card electronics">
                    <div class="icon-box" style="background: #fff3e0;">
                        <i class="fas fa-headphones" style="color: #FF9800;"></i>
                    </div>
                    <h3>Pending Order</h3>
                    <div class="price">140</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card electronics">
                    <div class="icon-box" style="background: #e8f5e9;">
                        <i class="fas fa-tv" style="color: #4CAF50;"></i>
                    </div>
                    <h3>Confirm Order</h3>
                    <div class="price">180</div>
                </div>
            </div>
        </div> -->
        
      
        <!-- <h2 class="mb-3 mt-3"><i class="fa-solid fa-user"></i>Office Stap</h2>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="order-card furniture">
                    <div class="icon-box" style="background: #fce4ec;">
                        <i class="fa-solid fa-user" style="color: #E91E63;"></i>
                    </div>
                    <h3>Total Stap</h3>
                    <div class="price">1000</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card furniture">
                    <div class="icon-box" style="background: #f3e5f5;">
                        <i class="fa-solid fa-user" style="color: #9C27B0;"></i>
                    </div>
                  
                    <h3>Present Stap</h3>
                    <div class="price">740</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card furniture">
                    <div class="icon-box" style="background: #ede7f6;">
                        <i class="fa-solid fa-user" style="color: #673AB7;"></i>
                    </div>
                    <h3>Appsent Stap</h3>
                    <div class="price">45</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card furniture">
                    <div class="icon-box" style="background: #fff9c4;">
                        <i class="fa-solid fa-user" style="color: #FBC02D;"></i>
                    </div>
                    <h3>Working Stap</h3>
                    <div class="price">850</div>
                </div>
            </div>
        </div> -->
        
       
        <!-- <h2 class="mb-3 mt-"><i class="fa-solid fa-dollar-sign"></i></i>Accounts</h2>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="order-card clothing">
                    <div class="icon-box" style="background: #e1f5fe;">
                        <i class="fas fa-tshirt" style="color: #03A9F4;"></i>
                    </div>
                    <span class="category-badge" style="background: #e1f5fe; color: #0277BD;">Clothing</span>
                    <h3>Premium Cotton T-Shirts</h3>
                    <p class="order-detail"><i class="fas fa-box me-2"></i>Order #CL-30567</p>
                    <p class="order-detail"><i class="fas fa-calendar me-2"></i>Nov 15, 2025</p>
                    <span class="badge-status badge-pending"><i class="fas fa-clock me-2"></i>Pending</span>
                    <div class="price">$89.99</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card clothing">
                    <div class="icon-box" style="background: #b3e5fc;">
                        <i class="fas fa-user-tie" style="color: #0288D1;"></i>
                    </div>
                    <span class="category-badge" style="background: #b3e5fc; color: #01579B;">Clothing</span>
                    <h3>Business Suit Premium</h3>
                    <p class="order-detail"><i class="fas fa-box me-2"></i>Order #CL-30568</p>
                    <p class="order-detail"><i class="fas fa-calendar me-2"></i>Nov 16, 2025</p>
                    <span class="badge-status badge-confirmed"><i class="fas fa-check-circle me-2"></i>Confirmed</span>
                    <div class="price">$599.99</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card clothing">
                    <div class="icon-box" style="background: #e0f2f1;">
                        <i class="fas fa-shoe-prints" style="color: #00796B;"></i>
                    </div>
                    <span class="category-badge" style="background: #e0f2f1; color: #004D40;">Clothing</span>
                    <h3>Running Shoes Sport</h3>
                    <p class="order-detail"><i class="fas fa-box me-2"></i>Order #CL-30569</p>
                    <p class="order-detail"><i class="fas fa-calendar me-2"></i>Nov 14, 2025</p>
                    <span class="badge-status badge-cancelled"><i class="fas fa-times-circle me-2"></i>Cancelled</span>
                    <div class="price">$129.99</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card clothing">
                    <div class="icon-box" style="background: #f1f8e9;">
                        <i class="fas fa-hat-cowboy" style="color: #689F38;"></i>
                    </div>
                    <span class="category-badge" style="background: #f1f8e9; color: #33691E;">Clothing</span>
                    <h3>Winter Jacket Collection</h3>
                    <p class="order-detail"><i class="fas fa-box me-2"></i>Order #CL-30570</p>
                    <p class="order-detail"><i class="fas fa-calendar me-2"></i>Nov 17, 2025</p>
                    <span class="badge-status badge-total"><i class="fas fa-dollar-sign me-2"></i>Total Order</span>
                    <div class="price">$249.99</div>
                </div>
            </div>
        </div>
         -->
        <!-- Books Row -->
        <!-- <h2 class="mb-4 mt-5"><i class="fas fa-book me-2"></i>Books & Media</h2>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="order-card books">
                    <div class="icon-box" style="background: #e8f5e9;">
                        <i class="fas fa-book" style="color: #66BB6A;"></i>
                    </div>
                    <span class="category-badge" style="background: #e8f5e9; color: #2E7D32;">Books</span>
                    <h3>Programming Books Set</h3>
                    <p class="order-detail"><i class="fas fa-box me-2"></i>Order #BK-40782</p>
                    <p class="order-detail"><i class="fas fa-calendar me-2"></i>Nov 15, 2025</p>
                    <span class="badge-status badge-pending"><i class="fas fa-clock me-2"></i>Pending</span>
                    <div class="price">$149.99</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card books">
                    <div class="icon-box" style="background: #c8e6c9;">
                        <i class="fas fa-book-open" style="color: #43A047;"></i>
                    </div>
                    <span class="category-badge" style="background: #c8e6c9; color: #1B5E20;">Books</span>
                    <h3>Best Sellers Collection</h3>
                    <p class="order-detail"><i class="fas fa-box me-2"></i>Order #BK-40783</p>
                    <p class="order-detail"><i class="fas fa-calendar me-2"></i>Nov 16, 2025</p>
                    <span class="badge-status badge-confirmed"><i class="fas fa-check-circle me-2"></i>Confirmed</span>
                    <div class="price">$79.99</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card books">
                    <div class="icon-box" style="background: #dcedc8;">
                        <i class="fas fa-bookmark" style="color: #7CB342;"></i>
                    </div>
                    <span class="category-badge" style="background: #dcedc8; color: #33691E;">Books</span>
                    <h3>Magazine Subscription</h3>
                    <p class="order-detail"><i class="fas fa-box me-2"></i>Order #BK-40784</p>
                    <p class="order-detail"><i class="fas fa-calendar me-2"></i>Nov 14, 2025</p>
                    <span class="badge-status badge-cancelled"><i class="fas fa-times-circle me-2"></i>Cancelled</span>
                    <div class="price">$39.99</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card books">
                    <div class="icon-box" style="background: #f9fbe7;">
                        <i class="fas fa-newspaper" style="color: #9E9D24;"></i>
                    </div>
                    <span class="category-badge" style="background: #f9fbe7; color: #827717;">Books</span>
                    <h3>Educational Bundle</h3>
                    <p class="order-detail"><i class="fas fa-box me-2"></i>Order #BK-40785</p>
                    <p class="order-detail"><i class="fas fa-calendar me-2"></i>Nov 17, 2025</p>
                    <span class="badge-status badge-total"><i class="fas fa-dollar-sign me-2"></i>Total Order</span>
                    <div class="price">$199.99</div>
                </div>
            </div>
        </div> -->
        
        <!-- Office Supplies Row -->
        <!-- <h2 class="mb-4 mt-5"><i class="fas fa-briefcase me-2"></i>Office Supplies</h2>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="order-card office">
                    <div class="icon-box" style="background: #fce4ec;">
                        <i class="fas fa-pen" style="color: #EC407A;"></i>
                    </div>
                    <span class="category-badge" style="background: #fce4ec; color: #C2185B;">Office</span>
                    <h3>Premium Pen Set</h3>
                    <p class="order-detail"><i class="fas fa-box me-2"></i>Order #OF-50923</p>
                    <p class="order-detail"><i class="fas fa-calendar me-2"></i>Nov 15, 2025</p>
                    <span class="badge-status badge-pending"><i class="fas fa-clock me-2"></i>Pending</span>
                    <div class="price">$45.99</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card office">
                    <div class="icon-box" style="background: #f8bbd0;">
                        <i class="fas fa-print" style="color: #D81B60;"></i>
                    </div>
                    <span class="category-badge" style="background: #f8bbd0; color: #880E4F;">Office</span>
                    <h3>Printer Paper 500 Sheets</h3>
                    <p class="order-detail"><i class="fas fa-box me-2"></i>Order #OF-50924</p>
                    <p class="order-detail"><i class="fas fa-calendar me-2"></i>Nov 16, 2025</p>
                    <span class="badge-status badge-confirmed"><i class="fas fa-check-circle me-2"></i>Confirmed</span>
                    <div class="price">$29.99</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card office">
                    <div class="icon-box" style="background: #fce4ec;">
                        <i class="fas fa-folder" style="color: #E91E63;"></i>
                    </div>
                    <span class="category-badge" style="background: #fce4ec; color: #AD1457;">Office</span>
                    <h3>File Organizer Set</h3>
                    <p class="order-detail"><i class="fas fa-box me-2"></i>Order #OF-50925</p>
                    <p class="order-detail"><i class="fas fa-calendar me-2"></i>Nov 14, 2025</p>
                    <span class="badge-status badge-cancelled"><i class="fas fa-times-circle me-2"></i>Cancelled</span>
                    <div class="price">$34.99</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="order-card office">
                    <div class="icon-box" style="background: #f48fb1;">
                        <i class="fas fa-calculator" style="color: #C2185B;"></i>
                    </div>
                    <span class="category-badge" style="background: #f48fb1; color: #880E4F;">Office</span>
                    <h3>Desk Accessories Bundle</h3>
                    <p class="order-detail"><i class="fas fa-box me-2"></i>Order #OF-50926</p>
                    <p class="order-detail"><i class="fas fa-calendar me-2"></i>Nov 17, 2025</p>
                    <span class="badge-status badge-total"><i class="fas fa-dollar-sign me-2"></i>Total Order</span>
                    <div class="price">$89.99</div>
                </div>
            </div>
        </div> -->
    </div>



</div>






</div>
@endsection

@push('js')
<!-- Custom JS for admin -->
@endpush
