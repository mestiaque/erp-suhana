@extends('admin.layouts.app')
@section('title')
<title>Admin Dashboard</title>
@endsection


@section('contents')
<div class="flex-grow-1 p-4">

    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-header">

                <!-- Watermark Icons -->
                <i class="fas fa-users bg-icon icon-1"></i>
                <i class="fas fa-dollar-sign bg-icon icon-2"></i>
                <i class="fas fa-industry bg-icon icon-3"></i>
                <i class="fas fa-clipboard-list bg-icon icon-4"></i>
                <i class="fas fa-boxes bg-icon icon-5"></i>
                <i class="fas fa-tshirt bg-icon icon-6"></i>
                <i class="fas fa-pencil-alt bg-icon icon-7"></i>
                <i class="fas fa-chart-line bg-icon icon-8"></i>

                <h2 class="dashboard-title">
                    <span>D</span>
                    <span>a</span>
                    <span>s</span>
                    <span>h</span>
                    <span>b</span>
                    <span>o</span>
                    <span>a</span>
                    <span>r</span>
                    <span>d</span>

                    <span class="mx-2"></span>

                    <span>O</span>
                    <span>v</span>
                    <span>e</span>
                    <span>r</span>
                    <span>v</span>
                    <span>i</span>
                    <span>e</span>
                    <span>w</span>
                </h2>

                <p class="dashboard-subtitle">
                    Garments ERP Management System
                </p>

            </div>
        </div>
    </div>




    @php
        $hrPackageExists = class_exists(\ME\Hr\Http\Controllers\HrDashboardController::class);
        $accSflPackageExists = class_exists(\ME\AccSfl\Http\Controllers\DashboardController::class);
        $sflInventoryPackageExists = class_exists(\ME\SflInventory\Http\Controllers\DashboardController::class);
        $showHrWidget = $hrPackageExists && auth()->user()?->can('hr_dashboard.all');
        $showAccSflWidget = $accSflPackageExists && auth()->user()?->can('ac_dashboard.view');
        $showInventoryWidget = $sflInventoryPackageExists && auth()->user()?->can('inv_dashboard.all');
    @endphp

    @can('user_dashboard.view')
        @include('admin.basic-dashboard')
    @endcan

    @if($showHrWidget)
        @include('hr::partials.dashboard-widget')
    @endif

    @if($showAccSflWidget)
        @include('acc-sfl::admin.partials.dashboard-widget')
    @endif

    @if($showInventoryWidget)
        @include('sfl-inventory::admin.partials.dashboard-widget')
    @endif

    @if(!$showHrWidget && !$showAccSflWidget && !$showInventoryWidget)
        <div class="d-none"></div>
    @endif

</div>
@endsection


@push('css')
<style>
.dashboard-header{
    position:relative;
    background:#fff;
    border-radius:18px;
    padding:45px 20px;
    overflow:hidden;
    text-align:center;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
    border:1px solid #f0f0f0;
}

/* Watermark Icons */

.bg-icon{
    position:absolute;
    font-size:65px;
    color:#0d6efd;
    opacity:.05;
    z-index:1;
    transform:rotate(-20deg);
    pointer-events:none;
}

.icon-1{top:15px;left:35px;}
.icon-2{top:25px;right:60px;}
.icon-3{bottom:20px;left:120px;}
.icon-4{bottom:15px;right:28%;}
.icon-5{top:45%;left:20%;}
.icon-6{top:32%;right:14%; font-size:88px;}
.icon-7{top:18%;left:35%;}
.icon-8{bottom:25%;right:45%;}

/* Content */

.dashboard-title{
    position:relative;
    z-index:2;
    font-size:42px;
    font-weight:800;
    letter-spacing:2px;
}

.dashboard-title span{
    display:inline-block;
    transition:.3s;
}

.dashboard-title span:hover{
    transform:translateY(-6px) scale(1.2);
}

/* Rainbow Letters */

.dashboard-title span:nth-child(1){color:#ff4757;}
.dashboard-title span:nth-child(2){color:#ff6b81;}
.dashboard-title span:nth-child(3){color:#ff9f43;}
.dashboard-title span:nth-child(4){color:#feca57;}
.dashboard-title span:nth-child(5){color:#1dd1a1;}
.dashboard-title span:nth-child(6){color:#10ac84;}
.dashboard-title span:nth-child(7){color:#00d2d3;}
.dashboard-title span:nth-child(8){color:#54a0ff;}
.dashboard-title span:nth-child(9){color:#5f27cd;}
.dashboard-title span:nth-child(11){color:#e84393;}
.dashboard-title span:nth-child(12){color:#fd79a8;}
.dashboard-title span:nth-child(13){color:#00b894;}
.dashboard-title span:nth-child(14){color:#0984e3;}
.dashboard-title span:nth-child(15){color:#6c5ce7;}
.dashboard-title span:nth-child(16){color:#fdcb6e;}
.dashboard-title span:nth-child(17){color:#e17055;}
.dashboard-title span:nth-child(18){color:#2d3436;}

.dashboard-subtitle{
    position:relative;
    z-index:2;
    color:#6c757d;
    font-size:15px;
    margin-top:10px;
    letter-spacing:1px;
    font-weight:500;
}
</style>
@endpush


