@extends('admin.layouts.master')
@section('title', 'Monthly Attendance')
@section('main-content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Monthly Attendance</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.employee.portal.dashboard') }}">Employee Portal</a></li>
                            <li class="breadcrumb-item active">Monthly Attendance</li>
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
                                <h3 class="card-title">Attendance Report - {{ \Carbon\Carbon::createFromDate($year, $month)->format('F Y') }}</h3>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('admin.employee.portal.monthly.attendance') }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select name="month" class="form-control">
                                                @for($m = 1; $m <= 12; $m++)
                                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate($year, $m)->format('F') }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="year" class="form-control">
                                                @for($y = 2020; $y <= date('Y'); $y++)
                                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </div>
                                    </div>
                                </form>
                                <br>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Day</th>
                                            <th>In Time</th>
                                            <th>Out Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $startDate = \Carbon\Carbon::createFromDate($year, $month, 1);
                                            $endDate = $startDate->copy()->endOfMonth();
                                            $currentDate = $startDate->copy();
                                        @endphp
                                        @while($currentDate <= $endDate)
                                            @php
                                                $attendance = $attendances->firstWhere('created_at', $currentDate->format('Y-m-d'));
                                                $leave = $leaves->first(function($leave) use ($currentDate) {
                                                    return $currentDate->between($leave->start_date, $leave->end_date);
                                                });
                                            @endphp
                                            <tr>
                                                <td>{{ $currentDate->format('d-m-Y') }}</td>
                                                <td>{{ $currentDate->format('l') }}</td>
                                                <td>{{ $attendance->in_time ?? '--' }}</td>
                                                <td>{{ $attendance->out_time ?? '--' }}</td>
                                                <td>
                                                    @if($currentDate->isSunday())
                                                        <span class="badge bg-success">Weekly Off</span>
                                                    @elseif($leave)
                                                        <span class="badge bg-info">Leave ({{ $leave->leaveType->name ?? 'Approved' }})</span>
                                                    @elseif($attendance)
                                                        <span class="badge bg-success">Present</span>
                                                    @else
                                                        <span class="badge bg-danger">Absent</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php $currentDate->addDay(); @endphp
                                        @endwhile
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
