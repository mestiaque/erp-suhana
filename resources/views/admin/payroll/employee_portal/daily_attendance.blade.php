@extends('admin.layouts.master')
@section('title', 'Daily Attendance')
@section('main-content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Daily Attendance</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.employee.portal.dashboard') }}">Employee Portal</a></li>
                            <li class="breadcrumb-item active">Daily Attendance</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Mark Daily Attendance</h3>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                @if($attendance)
                                    <div class="alert alert-info">
                                        <h4>Attendance Already Marked!</h4>
                                        <p>Date: {{ $attendance->created_at->format('d-m-Y') }}</p>
                                        <p>In Time: {{ $attendance->in_time }}</p>
                                    </div>
                                @else
                                    <form action="{{ route('admin.employee.portal.attendance.mark') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="date">Select Date</label>
                                            <input type="date" name="date" class="form-control" value="{{ $date }}" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Mark Attendance</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
