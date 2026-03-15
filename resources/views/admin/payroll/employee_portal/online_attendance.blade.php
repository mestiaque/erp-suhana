@extends('admin.layouts.master')
@section('title', 'Online Attendance')
@section('main-content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Online Attendance (GPS)</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.employee.portal.dashboard') }}">Employee Portal</a></li>
                            <li class="breadcrumb-item active">Online Attendance</li>
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
                                <h3 class="card-title">Mark Attendance with Location</h3>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <div id="map" style="height: 400px; width: 100%; margin-bottom: 20px;"></div>

                                <form action="{{ route('admin.employee.portal.online.attendance.mark') }}" method="POST" id="attendanceForm">
                                    @csrf
                                    <input type="hidden" name="latitude" id="latitude">
                                    <input type="hidden" name="longitude" id="longitude">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="markBtn" disabled>Mark Attendance</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
    <script>
        function initMap() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;

                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    document.getElementById('markBtn').disabled = false;

                    var map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 15,
                        center: {lat: lat, lng: lng}
                    });

                    var marker = new google.maps.Marker({
                        position: {lat: lat, lng: lng},
                        map: map,
                        title: 'Your Location'
                    });
                }, function() {
                    alert('Error: The Geolocation service failed.');
                });
            } else {
                alert('Error: Your browser doesn\'t support geolocation.');
            }
        }
    </script>
@endsection
