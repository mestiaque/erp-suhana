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

</div>
@endsection

@push('js')
<!-- Custom JS for admin -->
@endpush