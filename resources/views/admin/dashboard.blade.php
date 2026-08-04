@extends('admin.layouts.app')
@section('title')
<title>Admin Dashboard</title>
@endsection


@section('contents')
<div class="flex-grow-1 p-4">

    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-header d-none">

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












            <div class="banner-container">
                <div class="dice-wrapper">

                    <!-- DASHBOARD -->
                    <div class="dice-cube c-red" style="animation-delay: 0.1s;">
                    <div class="face front">D</div><div class="face back">D</div><div class="face left">D</div><div class="face right">D</div><div class="face top">D</div><div class="face bottom">D</div>
                    </div>

                    <div class="dice-cube c-orange" style="animation-delay: 0.3s;">
                    <div class="face front">a</div><div class="face back">a</div><div class="face left">a</div><div class="face right">a</div><div class="face top">a</div><div class="face bottom">a</div>
                    </div>

                    <div class="dice-cube c-yellow" style="animation-delay: 0.5s;">
                    <div class="face front">s</div><div class="face back">s</div><div class="face left">s</div><div class="face right">s</div><div class="face top">s</div><div class="face bottom">s</div>
                    </div>

                    <div class="dice-cube c-green" style="animation-delay: 0.7s;">
                    <div class="face front">h</div><div class="face back">h</div><div class="face left">h</div><div class="face right">h</div><div class="face top">h</div><div class="face bottom">h</div>
                    </div>

                    <div class="dice-cube c-teal" style="animation-delay: 0.9s;">
                    <div class="face front">b</div><div class="face back">b</div><div class="face left">b</div><div class="face right">b</div><div class="face top">b</div><div class="face bottom">b</div>
                    </div>

                    <div class="dice-cube c-blue" style="animation-delay: 1.1s;">
                    <div class="face front">o</div><div class="face back">o</div><div class="face left">o</div><div class="face right">o</div><div class="face top">o</div><div class="face bottom">o</div>
                    </div>

                    <div class="dice-cube c-purple" style="animation-delay: 1.3s;">
                    <div class="face front">a</div><div class="face back">a</div><div class="face left">a</div><div class="face right">a</div><div class="face top">a</div><div class="face bottom">a</div>
                    </div>

                    <div class="dice-cube c-pink" style="animation-delay: 1.5s;">
                    <div class="face front">r</div><div class="face back">r</div><div class="face left">r</div><div class="face right">r</div><div class="face top">r</div><div class="face bottom">r</div>
                    </div>

                    <div class="dice-cube c-dark" style="animation-delay: 1.7s;">
                    <div class="face front">d</div><div class="face back">d</div><div class="face left">d</div><div class="face right">d</div><div class="face top">d</div><div class="face bottom">d</div>
                    </div>

                    <!-- মাঝের খালি স্পেস -->
                    <div class="space"></div>

                    <!-- OVERVIEW -->
                    <div class="dice-cube c-red" style="animation-delay: 1.9s;">
                    <div class="face front">O</div><div class="face back">O</div><div class="face left">O</div><div class="face right">O</div><div class="face top">O</div><div class="face bottom">O</div>
                    </div>

                    <div class="dice-cube c-orange" style="animation-delay: 2.1s;">
                    <div class="face front">v</div><div class="face back">v</div><div class="face left">v</div><div class="face right">v</div><div class="face top">v</div><div class="face bottom">v</div>
                    </div>

                    <div class="dice-cube c-yellow" style="animation-delay: 2.3s;">
                    <div class="face front">e</div><div class="face back">e</div><div class="face left">e</div><div class="face right">e</div><div class="face top">e</div><div class="face bottom">e</div>
                    </div>

                    <div class="dice-cube c-green" style="animation-delay: 2.5s;">
                    <div class="face front">r</div><div class="face back">r</div><div class="face left">r</div><div class="face right">r</div><div class="face top">r</div><div class="face bottom">r</div>
                    </div>

                    <div class="dice-cube c-teal" style="animation-delay: 2.7s;">
                    <div class="face front">v</div><div class="face back">v</div><div class="face left">v</div><div class="face right">v</div><div class="face top">v</div><div class="face bottom">v</div>
                    </div>

                    <div class="dice-cube c-blue" style="animation-delay: 2.9s;">
                    <div class="face front">i</div><div class="face back">i</div><div class="face left">i</div><div class="face right">i</div><div class="face top">i</div><div class="face bottom">i</div>
                    </div>

                    <div class="dice-cube c-purple" style="animation-delay: 3.1s;">
                    <div class="face front">e</div><div class="face back">e</div><div class="face left">e</div><div class="face right">e</div><div class="face top">e</div><div class="face bottom">e</div>
                    </div>

                    <div class="dice-cube c-dark" style="animation-delay: 3.3s;">
                    <div class="face front">w</div><div class="face back">w</div><div class="face left">w</div><div class="face right">w</div><div class="face top">w</div><div class="face bottom">w</div>
                    </div>

                </div>

                <div class="subtitle">Garments ERP Management System</div>
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
<style>


  /* মূল ব্যানার কন্টেইনার */
  .banner-container {
    background: #ffffff;
    padding: 40px 60px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
    text-align: center;
    max-width: 100%;
  }

  /* ডাইসগুলোর জন্য কন্টেইনার ও ৩ডি পারসপেক্টিভ */
  .banner-container .dice-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    justify-content: center;
    perspective: 1200px; /* ৩ডি গভীরতা বাড়ানোর জন্য */
    margin-bottom: 20px;
  }

  /* স্পেস বা গ্যাপের জন্য (Dashboard এবং Overview এর মাঝে) */
  .banner-container .space {
    width: 30px;
  }

  /* প্রতিটা ডাইসের ৩ডি কন্টেইনার */
  .banner-container .dice-cube {
    width: 50px;
    height: 50px;
    position: relative;
    transform-style: preserve-3d;
    cursor: pointer;
    /* পেজ লোডের সময় ডাইসগুলো অনবরত ঘুরতে থাকবে */
    animation: continuous-roll 8s linear infinite;
  }

  /* মাউস হোভার (Hover) করলে ঘোরার স্পীড বেড়ে যাবে */
  .banner-container .dice-cube:hover {
    animation: fast-roll 1.5s ease-in-out infinite;
  }

  /* ডাইসের প্রতিটি সারফেস বা ফেস */
  .banner-container .face {
    position: absolute;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 26px;
    font-weight: 800;
    border-radius: 12px; /* লুডু ডাইসের মতো নিখুঁত রাউন্ড কর্নার */
    border: 2px solid rgba(255, 255, 255, 0.6);
    box-sizing: border-box;
    /* ডাইসে গ্লসি এবং শ্যাডো ইফেক্ট */
    box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.15), 0 5px 10px rgba(0,0,0,0.1);
    backface-visibility: visible;
  }

  /* ৩ডি স্পেসে ৬টি সারফেসের অবস্থান নির্ধারণ */
  .banner-container .front  { transform: rotateY(0deg) translateZ(25px); }
  .banner-container .back   { transform: rotateY(180deg) translateZ(25px); }
  .banner-container .left   { transform: rotateY(-90deg) translateZ(25px); }
  .banner-container .right  { transform: rotateY(90deg) translateZ(25px); }
  .banner-container .top    { transform: rotateX(90deg) translateZ(25px); }
  .banner-container .bottom { transform: rotateX(-90deg) translateZ(25px); }

  /* থিম অনুযায়ী গ্লসি কালার প্যালেট (Background ও Text Color) */
  .c-red .face    { background: #ffebee; color: #e53935; }
  .c-orange .face { background: #fff3e0; color: #fb8c00; }
  .c-yellow .face { background: #fffde7; color: #fdd835; }
  .c-green .face  { background: #e8f5e9; color: #43a047; }
  .c-teal .face   { background: #e0f2f1; color: #00897b; }
  .c-blue .face   { background: #e3f2fd; color: #1e88e5; }
  .c-purple .face { background: #f3e5f5; color: #8e24aa; }
  .c-pink .face   { background: #fce4ec; color: #d81b60; }
  .c-dark .face   { background: #eceff1; color: #37474f; }

  /* অনবরত হালকা ঘোরার অ্যানিমেশন (সব ডাইস একসাথে ঘুরবে) */
  @keyframes continuous-roll {
    0% { transform: rotateX(0deg) rotateY(0deg); }
    100% { transform: rotateX(360deg) rotateY(360deg); }
  }

  /* হোভার করলে দ্রুত এবং স্টাইলিশ ঘোরার অ্যানিমেশন */
  @keyframes fast-roll {
    0% { transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg); }
    100% { transform: rotateX(360deg) rotateY(180deg) rotateZ(360deg); }
  }

  /* সাবটাইটেল টেক্সট */
  .banner-container .subtitle {
    font-size: 13px;
    color: #888888;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-top: 25px;
    font-weight: 500;
  }
</style>
@endpush


