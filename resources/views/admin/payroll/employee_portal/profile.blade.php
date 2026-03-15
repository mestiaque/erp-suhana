@extends('admin.layouts.master')
@section('title', 'My Profile')
@section('main-content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">My Profile</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.employee.portal.dashboard') }}">Employee Portal</a></li>
                            <li class="breadcrumb-item active">My Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img class="profile-user-img img-fluid img-circle" src="{{ asset('admin/app-assets/images/portrait/small/avatar-s-19.png') }}" alt="User profile picture">
                                </div>
                                <h3 class="profile-username text-center">{{ $user->name }}</h3>
                                <p class="text-muted text-center">{{ $user->designation->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">About Me</h3>
                            </div>
                            <div class="card-body">
                                <strong><i class="fas fa-id-card mr-1"></i> Employee ID</strong>
                                <p class="text-muted">{{ $user->employee_id }}</p>
                                <hr>
                                <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                                <p class="text-muted">{{ $user->email }}</p>
                                <hr>
                                <strong><i class="fas fa-phone mr-1"></i> Phone</strong>
                                <p class="text-muted">{{ $user->mobile }}</p>
                                <hr>
                                <strong><i class="fas fa-building mr-1"></i> Department</strong>
                                <p class="text-muted">{{ $user->department->name ?? 'N/A' }}</p>
                                <hr>
                                <strong><i class="fas fa-map-marker mr-1"></i> Address</strong>
                                <p class="text-muted">{{ $user->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
