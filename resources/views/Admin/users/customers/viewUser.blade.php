@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('User Profile View')}}</title>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    .profileTable tr th{
        padding:5px 8px;
    }
    .profileTable tr td{
        padding:5px 8px;
    }
    .info ul {
        list-style: none;
        padding: 0;
        margin-top: 15px;
    }
    .info ul li{
        margin:10px 0;
    }
    .info ul li span b {
        font-size: 14px;
    }
    
    .info ul li i {
        font-size: 20px;
    }
    
    .info ul li span {
        line-height: 18px;
    }
    .fileTable tr td {
        padding: 5px;
    }
    
    .fileTable tr th {
        padding: 5px;
    }
    .nav-tabs .nav-link {
        font-size: 15px;
        font-weight: bold;
    }
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #e9ecef;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    
    
    
    .status a {
        background: #f2f2f2;
        padding: 3px 15px;
        display: inline-block;
        margin-bottom: 5px;
        border-radius: 5px;
        color: #4c4a4a;
        font-weight: bold;
    }
    .summery .info {
        width: 200px;
        display: inline-block;
        border: 1px solid #dfdbdb;
        margin: 5px;
        padding: 5px 15px;
        border-radius: 5px;
        background: #f8f8fa;
    }
    
    .summery .info h3 {
        font-size: 18px;
        margin: 0;
    }
    
    .summery .info span {
        font-size: 26px;
        font-weight: bold;
    }
    
    .dropdown-toggle::after{
        display:none;
    }
    
    @media only screen and (max-width: 678px) {
        .nav-tabs .nav-item {
            width: 50%;
            text-align: center;
            border: 1px solid #e7e7e7;
        }
        .summery .info {
            width: 100%;
        }
    }
</style>

@endpush
@section('contents')
<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>Profile View</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item"><a href="{{route('admin.usersCustomer')}}">Employee List</a></li>
        <li class="item">Profile View</li>
    </ol>
</div>
 
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="row">
        <div class="col-md-12">
             <!-- Start -->
            <div class="card mb-30">
                <div class="card-header d-flex justify-content-between align-items-center">
                     <h3>{{$user->name}} - Profile View</h3>
                     <a href="{{route('admin.usersCustomerAction',['edit',$user->id])}}"  class="btn-custom yellow"><i class="bx bx-edit"></i> Edit</a>
                </div>
                <div class="card-body">
                    
                    <ul class="nav nav-tabs">
                      <li class="nav-item">
                        <a class="nav-link {{$action=='view'?'active':''}}" href="{{route('admin.usersCustomerAction',['view',$user->id])}}">Information</a>
                      </li>
                      
                    </ul>
                    <br>

                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{asset($user->image())}}" style="max-height:200px;"><br>
                            <div class="info">
                                <ul>
                                    <li class="d-flex"><i class="bx bx-user mr-2 pt-2"></i> <span><b>ID</b><br>{{$user->employee_id}}</span></li>
                                    <li class="d-flex"><i class="bx bx-mobile mr-2 pt-2"></i> <span><b>Mobile</b><br>{{$user->mobile}}</span></li>
                                    <li class="d-flex"><i class="bx bx-envelope mr-2 pt-2"></i><span><b>Email</b><br>{{$user->email}}</span></li>
                                    <li class="d-flex"><i class="bx bx-check-shield mr-2 pt-2"></i><span><span><b>Designation</b><br>{{$user->designation?$user->designation->name:''}}</span></li>
                                    <li class="d-flex"><i class="bx bx-briefcase mr-2 pt-2"></i> <span><span><b>Department</b><br>{{$user->department?$user->department->name:''}}</span></li>
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
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <br>
                            <h4>Attach Document:</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered fileTable">
                                    <thead>
                                        <tr>
                                           <th style="min-width: 60px;width: 60px;">SL</th> 
                                           <th style="min-width: 150px;width: 150px;">Attachment</th> 
                                           <th style="min-width: 200px;" >Title</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($user->galleryFiles->count() > 0)
                                        @foreach($user->galleryFiles as $i=>$file)
                                        <tr>
                                            <td>{{$i+1}}</td>
                                            <td>
                                                @if($file->file_url)
                                                <a href="{{asset($file->file_url)}}" title="{{$file->file_name}}" download="">Download File</a>
                                                @else
                                                <span>No Attachment</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{$file->file_name}}
                                            </td>
                                            
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td style="text-align:center;" colspan="3">No Attachment File</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    

                </div>
            </div>

            <div class="card mb-30">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>{{$user->name}} - Last Live Location</h3>
                </div>
                <div class="card-body">
                    <!--<p>-->
                    <!--    Your Are in <b>Lat:</b> <span class="latValue"></span> , <b>Lng:</b> <span class="lngValue"></span>-->
                    <!--</p>-->
                    <!--<div style="text-align: center;min-height: 350px;background: #f2f2f2;padding: 25px;">-->
                    <!--    <div class="showMapArea" style="height: 400px; width: 100%;"></div>-->
                    <!--</div>-->
                    <p>Google Map</p>
                    <div class="mapContainer" style="width: 100%; height: 500px;">
                        <p id="mapMessage" style="font-size: 20px;"></p>
                        <iframe id="googleMap" width="100%" height="500" style="border:0;" loading="lazy" allowfullscreen src=""></iframe>
                    </div>
                </div>
            </div>

        
        
        </div>
    </div>
</div>



@endsection
@push('js')


@endpush