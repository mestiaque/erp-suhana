@extends('admin.layouts.app')
@section('title')
<title>Admin Dashboard</title>
@endsection

@push('css')
<style>

</style>
@endpush

@section('contents')
<div class="flex-grow-1 p-4">

    @php
        $hrPackageExists = class_exists(\ME\Hr\Http\Controllers\HrDashboardController::class);
    @endphp

    @if($hrPackageExists && auth()->user()?->can('hr.all'))
        @include('hr::partials.dashboard-widget')
    @else
        <div class="d-flex align-items-center justify-content-center" style="min-height: 60vh;">
            <div class="text-center text-muted">
                <i class="bx bx-home-circle" style="font-size: 64px; opacity: 0.3;"></i>
                <h3 class="mt-3">Welcome to ERP Dashboard</h3>
                <p class="mb-0">Use the sidebar to navigate to your modules.</p>
            </div>
        </div>
    @endif

</div>
@endsection

@push('js')

@endpush
