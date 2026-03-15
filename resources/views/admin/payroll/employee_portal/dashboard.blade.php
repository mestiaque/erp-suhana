@extends('admin.layouts.master')
@section('title', 'Employee Portal Dashboard')
@section('main-content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Employee Portal</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Employee Portal</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $presentDays }}</h3>
                                <p>Present Days</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="{{ route('admin.employee.portal.monthly.attendance') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $absentDays }}</h3>
                                <p>Absent Days</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="{{ route('admin.employee.portal.monthly.attendance') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $pendingLeaves }}</h3>
                                <p>Pending Leaves</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>53<sup style="font-size: 20px">%</sup></h3>
                                <p>Attendance Rate</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Today's Status</h3>
                            </div>
                            <div class="card-body">
                                @if($todayAttendance)
                                    <div class="alert alert-success">
                                        <h5><i class="icon fas fa-check"></i> You are marked present!</h5>
                                        <p>In Time: {{ $todayAttendance->in_time }}</p>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <h5><i class="icon fas fa-exclamation-triangle"></i> You are absent today!</h5>
                                        <p>Please mark your attendance.</p>
                                        <a href="{{ route('admin.employee.portal.attendance') }}" class="btn btn-primary btn-sm">Mark Attendance</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Quick Actions</h3>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('admin.employee.portal.attendance') }}" class="btn btn-app">
                                    <i class="fas fa-clock"></i> Daily Attendance
                                </a>
                                <a href="{{ route('admin.employee.portal.online.attendance') }}" class="btn btn-app">
                                    <i class="fas fa-map-marker-alt"></i> Online Attendance
                                </a>
                                <a href="{{ route('admin.employee.portal.profile') }}" class="btn btn-app">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
                                <a href="{{ route('admin.employee.portal.monthly.attendance') }}" class="btn btn-app">
                                    <i class="fas fa-calendar-alt"></i> Monthly Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($notices) && $notices->count() > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Notices</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Date</th>
                                            <th>Priority</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($notices as $notice)
                                        <tr>
                                            <td><strong>{{ $notice->title }}</strong></td>
                                            <td>{{ $notice->notice_date->format('d M, Y') }}</td>
                                            <td>
                                                @if($notice->priority == 'high')
                                                    <span class="badge badge-danger">High</span>
                                                @elseif($notice->priority == 'medium')
                                                    <span class="badge badge-warning">Medium</span>
                                                @else
                                                    <span class="badge badge-info">Low</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($notice->description, 100) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
