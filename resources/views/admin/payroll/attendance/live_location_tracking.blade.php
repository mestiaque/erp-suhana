@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Live Location Tracking') }}</title>
@endsection

@push('css')
<style>
    .location-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 15px;
    }
    .employee-location-item {
        display: flex;
        align-items: center;
        padding: 12px;
        border-bottom: 1px solid #eee;
        transition: background 0.2s;
        cursor: pointer;
    }
    .employee-location-item:hover {
        background: #f8f9fa;
    }
    .employee-location-item:last-child {
        border-bottom: none;
    }
    .employee-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: #4ecdc4;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .employee-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    .employee-info {
        flex: 1;
    }
    .employee-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 3px;
    }
    .employee-dept {
        font-size: 12px;
        color: #666;
    }
    .location-info {
        text-align: right;
    }
    .location-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .location-active {
        background: #d4edda;
        color: #28a745;
    }
    .location-inactive {
        background: #f8d7da;
        color: #dc3545;
    }
    .location-time {
        font-size: 11px;
        color: #666;
        margin-top: 3px;
    }
    #map {
        height: 500px;
        width: 100%;
        border-radius: 8px;
    }
    .no-location {
        color: #999;
        font-style: italic;
    }
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .stats-number {
        font-size: 32px;
        font-weight: bold;
    }
    .stats-label {
        font-size: 14px;
        opacity: 0.9;
    }
    .employee-list {
        max-height: 500px;
        overflow-y: auto;
    }
    .selected-employee {
        background: #e3f2fd !important;
        border-left: 3px solid #2196f3;
    }
    @media (max-width: 768px) {
        .employee-location-item {
            flex-direction: column;
            align-items: flex-start;
        }
        .location-info {
            text-align: left;
            margin-top: 10px;
        }
        #map {
            height: 300px;
        }
    }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Live Location Tracking</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Attendance</li>
        <li class="item">Live Location</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="location-card no-print">
        <form action="{{ route('admin.liveLocationTracking') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label>Department</label>
                <select name="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Employee</label>
                <select name="employee_id" class="form-control">
                    <option value="">All Employees</option>
                    @foreach($allEmployees ?? [] as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->employee_id ?? $emp->id }} - {{ $emp->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Filter</button>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="button" onclick="location.reload()" class="btn btn-info w-100">
                    <i class="bx bx-refresh"></i> Refresh
                </button>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="button" onclick="startLiveTracking()" class="btn btn-success w-100">
                    <i class="bx bx-radar"></i> Live Track
                </button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-number">{{ count($employeesWithLocationOnly) }}</div>
                <div class="stats-label">Employees with Location</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stats-number">{{ count($locationData) }}</div>
                <div class="stats-label">Total Employees</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stats-number" id="currentTime">{{ Carbon\Carbon::now()->format('h:i A') }}</div>
                <div class="stats-label">Current Time</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="location-card">
                <h5 class="mb-3">Employee List <small class="text-muted">(Click to view on map)</small></h5>
                <div class="employee-list">
                    @if(count($locationData) > 0)
                        @foreach($locationData as $data)
                        <div class="employee-location-item" onclick="focusOnEmployee({{ $data['employee']->id }}, {{ $data['location']['lat'] ?? 'null' }}, {{ $data['location']['lng'] ?? 'null' }})" id="emp-item-{{ $data['employee']->id }}">
                            <div class="employee-avatar" style="{{ $data['has_location'] ? 'background: #28a745;' : 'background: #6c757d;' }}">
                                @if($data['employee']->photo)
                                    <img src="{{ asset($data['employee']->photo) }}" alt="{{ $data['employee']->name }}">
                                @else
                                    {{ substr($data['employee']->name, 0, 1) }}
                                @endif
                            </div>
                            <div class="employee-info">
                                <div class="employee-name">{{ $data['employee']->name }}</div>
                                <div class="employee-dept">{{ $data['employee']->department->name ?? 'N/A' }}</div>
                            </div>
                            <div class="location-info">
                                @if($data['has_location'])
                                    <span class="location-status location-active">
                                        <i class="bx bx-map"></i> Active
                                    </span>
                                    <div class="location-time">
                                        {{ Carbon\Carbon::parse($data['location_time'])->format('h:i A') }}
                                    </div>
                                @else
                                    <span class="location-status location-inactive">
                                        <i class="bx bx-map-alt"></i> Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-map-alt" style="font-size: 48px; color: #ccc;"></i>
                            <p class="mt-3 text-muted">No employees found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="location-card">
                <h5 class="mb-3">Google Map</h5>
                <div id="map"></div>
            </div>
        </div>
    </div>

    <div class="location-card">
        <h5 class="mb-3">How Location Tracking Works</h5>
        <div class="row">
            <div class="col-md-6">
                <h6>For Employees:</h6>
                <ul>
                    <li>Login to employee portal</li>
                    <li>Click "Share Location" button</li>
                    <li>Allow browser location permission</li>
                    <li>Your location will be shared with admin in real-time</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>For Admin:</h6>
                <ul>
                    <li>View all employees on Google Map</li>
                    <li>Click on employee in list to focus on map</li>
                    <li>Use "Live Track" for auto-refresh every 30 seconds</li>
                    <li>Green marker = Active location, Gray = Inactive</li>
                </ul>
            </div>
        </div>
    </div>

</div>

@push('js')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_maps_key', env('GOOGLE_MAPS_API_KEY', '')) }}&callback=initMap" async defer loading="async"></script>
<script>
    var map;
    var markers = {};
    var employeeLocations = @json($locationData);
    var selectedEmployeeId = null;
    var liveTrackingInterval = null;

    function initMap() {
        // Default center (Bangladesh)
        var center = { lat: 23.8103, lng: 90.4125 };

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: center,
            styles: [
                {
                    "featureType": "poi",
                    "elementType": "labels",
                    "stylers": [{ "visibility": "off" }]
                }
            ]
        });

        // Add markers for all employees
        employeeLocations.forEach(function(data) {
            if (data.location && data.location.lat && data.location.lng) {
                addMarker(data);
            }
        });
    }

    function addMarker(data) {
        var position = { lat: parseFloat(data.location.lat), lng: parseFloat(data.location.lng) };

        var marker = new google.maps.Marker({
            position: position,
            map: map,
            title: data.employee.name,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: '#28a745',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2
            }
        });

        var infoWindow = new google.maps.InfoWindow({
            content: '<div style="padding: 10px;">' +
                     '<strong>' + data.employee.name + '</strong><br>' +
                     '<small>' + (data.employee.department_name || 'N/A') + '</small><br>' +
                     '<small class="text-muted">Last updated: ' + (data.location_time ? formatTime(data.location_time) : 'N/A') + '</small><br>' +
                     '<small>Source: ' + (data.location_source || 'N/A') + '</small>' +
                     '</div>'
        });

        marker.addListener('click', function() {
            infoWindow.open(map, marker);
        });

        markers[data.employee.id] = marker;
    }

    function focusOnEmployee(employeeId, lat, lng) {
        // Remove previous highlight
        document.querySelectorAll('.employee-location-item').forEach(function(el) {
            el.classList.remove('selected-employee');
        });

        // Add highlight to selected
        var item = document.getElementById('emp-item-' + employeeId);
        if (item) {
            item.classList.add('selected-employee');
        }

        if (lat && lng) {
            map.setCenter({ lat: lat, lng: lng });
            map.setZoom(15);

            // Open info window if marker exists
            if (markers[employeeId]) {
                google.maps.event.trigger(markers[employeeId], 'click');
            }
        }
    }

    function startLiveTracking() {
        if (liveTrackingInterval) {
            clearInterval(liveTrackingInterval);
            liveTrackingInterval = null;
            alert('Live tracking stopped');
        } else {
            liveTrackingInterval = setInterval(function() {
                location.reload();
            }, 30000); // Refresh every 30 seconds
            alert('Live tracking started! Page will refresh every 30 seconds.');
        }
    }

    function formatTime(timeStr) {
        var date = new Date(timeStr);
        return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }

    // Update current time every minute
    setInterval(function() {
        var now = new Date();
        document.getElementById('currentTime').innerText = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }, 60000);
</script>
@endpush

@endsection
