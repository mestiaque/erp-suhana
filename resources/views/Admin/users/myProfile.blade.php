@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('My Profile')}}</title>
@endsection
@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style type="text/css">
    .ProfileImage {
        max-width: 64px;
        max-height: 64px;
    }
</style>
@endpush @section('contents')

<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>My Profile</h1>

    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">Dashboard </li>
        <li class="item">My Profile</li>
    </ol>
</div>


@include(adminTheme().'alerts')
<div class="row">
    <div class="col-md-7">
        <!-- Start -->
        <div class="card mb-30">
            <div class="card-header d-flex justify-content-between align-items-center">
                 <h3>My Profile</h3>
                 <a href="{{route('admin.editProfile')}}" class="btn-custom yellow"><i class="bx bx-edit"></i> Edit</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">

                            <img src="{{asset($user->image())}}" style="max-height:200px;"><br>
                            @if($user->permission)
                               <span style="color: #0829e5;font-weight: bold;">
                                   {{$user->permission->name}}
                               </span>
                           @endif
                        </div>
                        <div class="info">
                            <ul>
                                <li class="d-flex"><i class="bx bx-user mr-2 pt-2"></i> <span><b>ID</b><br>{{$user->employee_id}}</span></li>
                                <li class="d-flex"><i class="bx bx-mobile mr-2 pt-2"></i> <span><b>Mobile</b><br>{{$user->mobile}}</span></li>
                                <li class="d-flex"><i class="bx bx-envelope mr-2 pt-2"></i><span><b>Email</b><br>{{$user->email}}</span></li>

                                @if($user->designation)
                                <li class="d-flex"><i class="bx bx-check-shield mr-2 pt-2"></i><span><span><b>Designation</b><br>{{$user->designation?$user->designation->name:''}}</span></li>
                                @endif

                                @if($user->department)
                                <li class="d-flex"><i class="bx bx-briefcase mr-2 pt-2"></i> <span><span><b>Department</b><br>{{$user->department->name}}</span></li>
                                @endif
                            </ul>
                            <div class="content">
                                {{$user->profile}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h4>Profile Information:</h4>
                        <div class="table-responsive">
                            <table class="table table-borderless profileTable">
                                <tr>
                                    <th style="width: 150px;min-width: 150px;">Name</th>
                                    <th style="width: 20px;min-width: 20px;">:</th>
                                    <td>{{$user->name}}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <th>:</th>
                                    <td>{{$user->fullAddress()}}</td>
                                </tr>
                                <tr>
                                    <th>Present Address</th>
                                    <th>:</th>
                                    <td>{{$user->address_line2}}</td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <th>:</th>
                                    <td>{{$user->gender}}</td>
                                </tr>
                                <tr>
                                    <th>Date Of Birth</th>
                                    <th>:</th>
                                    <td>{{$user->dob?Carbon\Carbon::parse($user->dob)->format('d M Y'):''}}</td>
                                </tr>
                                <tr>
                                    <th>Marital Status</th>
                                    <th>:</th>
                                    <td>{{$user->marital_status}}</td>
                                </tr>
                                <tr>
                                    <th>Employment</th>
                                    <th>:</th>
                                    <td>{{$user->employment_status}}</td>
                                </tr>
                                <tr>
                                    <th>User Status</th>
                                    <th>:</th>
                                    <td>
                                        @if($user->status)
                                        <span style="color: #43d39e;font-size: 20px;line-height: 20px;position:absolute;">
                                            <i class="bx bx-check-circle"></i>
                                        </span>
                                        @else
                                        <span style="color: #FF9800;font-size: 20px;line-height: 20px;position:absolute;">
                                            <i class="bx bx-x"></i>
                                        </span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>Join Date</th>
                                    <th>:</th>
                                    <td>{{$user->created_at->format('d M Y')}}</td>
                                </tr>
                                <tr>
                                    <th>Exit Date</th>
                                    <th>:</th>
                                    <td>{{$user->exited_at?Carbon\Carbon::parse($user->exited_at)->format('d M Y'):''}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <!-- Start -->
        <!--<div class="card mb-30">-->
        <!--    <div class="card-header d-flex justify-content-between align-items-center">-->
        <!--         <h3>Activity Map</h3>-->
        <!--    </div>-->
        <!--    <div class="card-body">-->
        <!--        <div style="text-align: center;min-height: 350px;background: #f2f2f2;padding: 25px;">-->
        <!--            <div class="showMapArea" style="height: 400px; width: 100%;"></div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
    </div>
</div>



@endsection

@push('js')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
$(document).ready(function () {
    let map, marker;
    const mapContainer = $('.showMapArea');

    // $('.startLocation').on('click', function () {
        if (!navigator.geolocation) {
            alert("Your browser does not support Geolocation.");
            return;
        }
        mapContainer.html('');
        map = L.map(mapContainer[0]).setView([23.8103, 90.4125], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        marker = L.marker([23.8103, 90.4125]).addTo(map)
            .bindPopup("Detecting location...")
            .openPopup();

        navigator.geolocation.watchPosition(function (pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            marker.setLatLng([lat, lng]).bindPopup("{{$user->name}} are here!").openPopup();
            map.panTo([lat, lng]);

            // Optional: send to server (uncomment)
            /*
            $.ajax({
                url: '{{route('admin.usersCustomer')}}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    lat: lat,
                    lng: lng
                }
            });
            */
        }, function (err) {
            // alert("Unable to get your location: " + err.message);
            window.realod();
        }, { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 });
    // });
});
</script>

@endpush
