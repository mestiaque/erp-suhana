@extends('admin.layouts.app')
@section('title')
<title>Admin Dashboard</title>
@endsection


@section('contents')
<div class="flex-grow-1 p-4">

    <div class="row mb-3">
        <div class="col-md-12">
            <h4 class="mb-3">Dashboard Overview</h4>
        </div>
    </div>



    @php
        $hrPackageExists = class_exists(\ME\Hr\Http\Controllers\HrDashboardController::class);
        $accSflPackageExists = class_exists(\ME\AccSfl\Http\Controllers\DashboardController::class);
        $sflInventoryPackageExists = class_exists(\ME\SflInventory\Http\Controllers\DashboardController::class);
        $showHrWidget = $hrPackageExists && auth()->user()?->can('hr.all');
        $showAccSflWidget = $accSflPackageExists && auth()->user()?->can('ac_dashboard.view');
        $showInventoryWidget = $sflInventoryPackageExists && auth()->user()?->can('inv_dashboard.all');
    @endphp

    @include('admin.basic-dashboard')

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


