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
                      <li class="nav-item">
                        <a class="nav-link {{$action=='leads'?'active':''}}" href="{{route('admin.usersCustomerAction',['leads',$user->id])}}">Leads ({{$leads->total()}})</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link {{$action=='visits'?'active':''}}" href="{{route('admin.usersCustomerAction',['visits',$user->id])}}">Re-Visits ({{$visits->total()}})</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link {{$action=='meeting'?'active':''}}" href="{{route('admin.usersCustomerAction',['meeting',$user->id])}}">Meeting ({{$meetings->total()}})</a>
                      </li>
                      
                      <li class="nav-item">
                        <a class="nav-link {{$action=='notes'?'active':''}}" href="{{route('admin.usersCustomerAction',['notes',$user->id])}}">Notes ({{$notes->total()}})</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link {{$action=='tasks'?'active':''}}" href="{{route('admin.usersCustomerAction',['tasks',$user->id])}}">Tasks ({{$tasks->total()}})</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link {{$action=='companies'?'active':''}}" href="{{route('admin.usersCustomerAction',['companies',$user->id])}}">Companies ({{$companies->total()}})</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link {{$action=='reports'?'active':''}}" href="{{route('admin.usersCustomerAction',['reports',$user->id])}}">Reports</a>
                      </li>
                    </ul>
                    <br>
                    @if($action=='leads')
                        <form action="{{route('admin.usersCustomerAction',['leads',$user->id])}}">
                            <div class="row">
                                <div class="col-md-5 mb-1">
                                    <div class="input-group">
                                        <input type="date" name="startDate" value="{{request()->startDate}}" class="form-control form-control-sm {{$errors->has('startDate')?'error':''}}" />
                                        <input type="date" value="{{request()->endDate}}" name="endDate" class="form-control form-control-sm {{$errors->has('endDate')?'error':''}}" />
                                    </div>
                                </div>
                                <div class="col-md-3 mb-0">
                                    <select class="form-control form-control-sm" name="customer_status">
                                        <option value="">Select Customer Status</option>
                                        <option value="Not Potential">Not Potential</option>
                                        <option value="Potential">Potential</option>
                                        <option value="Very Potential">Very Potential</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-0">
                                    <div class="input-group">
                                        <input type="text" name="search" value="{{request()->search}}" placeholder="Search Company, Owner name" class="form-control form-control-sm {{$errors->has('search')?'error':''}}" />
                                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <br>
                        <!--<div class="status">-->
                        <!--    <a href="{{route('admin.usersCustomerAction',['leads',$user->id,'status'=>'New'])}}" style="background: #2c66cb;color: white;">New (0)</a>-->
                        <!--    <a href="{{route('admin.usersCustomerAction',['leads',$user->id,'status'=>'Contacted'])}}" style="background: #ff108c;color: white;">Contacted (0)</a>-->
                        <!--    <a href="{{route('admin.usersCustomerAction',['leads',$user->id,'status'=>'Interested'])}}" style="background: #4788ff;color: white;">Interested (0)</a>-->
                        <!--    <a href="{{route('admin.usersCustomerAction',['leads',$user->id,'status'=>'Follow-up Sheduled'])}}" style="background: #d5ab05;color: white;">Follow-up Sheduled (0)</a>-->
                        <!--    <a href="{{route('admin.usersCustomerAction',['leads',$user->id,'status'=>'Meeting Done'])}}" style="background: #13c238;color: white;">Meeting Done (0)</a>-->
                        <!--    <a href="{{route('admin.usersCustomerAction',['leads',$user->id,'status'=>'Proposal Sent'])}}" style="background: #0b9e97;color: white;">Proposal Sent (0)</a>-->
                        <!--    <a href="{{route('admin.usersCustomerAction',['leads',$user->id,'status'=>'Win'])}}" style="background: #670bc1;color: white;">Win (0)</a>-->
                        <!--    <a href="{{route('admin.usersCustomerAction',['leads',$user->id,'status'=>'Cancelled'])}}" style="background: #ff2e37;color: white;">Cancelled (0)</a>-->
                        <!--</div>-->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="min-width: 150px;">Company Name</th>
                                    <th style="min-width: 200px;width:200px;">Owner Name</th>
                                    <th style="min-width: 230px;width:230px;">Sister Concern</th>
                                    <th style="min-width: 150px;width:150px">Customer Status</th>
                                    <th style="min-width: 150px;width:150px">Company Status</th>
                                    <th style="min-width: 110px;width: 110px;">Date</th>
                                    <th style="min-width:50px;width:50px;">Visits</th>
                                    <th style="min-width:50px;width:50px;">Meeting</th>
                                    <th style="min-width:50px;width:50px;">Task</th>
                                    <th style="min-width:50px;width:50px;">Note</th>
                                </tr>
                                </thead>
                                @foreach($leads as $lead)
                                <tr>
                                    <td>
                                        <a href="{{route('admin.leadsAction',['view',$lead->id])}}" >
                                        {{$lead->factory_name}}
                                        </a>
                                    </td>
                                    <td>{{$lead->name}}</td>
                                    <td>{{$lead->concern}}</td>
                                    <td>
                                        @if($lead->customer_status=='Not Potential')
                                        <span class="badge" style="background: #9baaff;font-size: 14px;color: white;" >Not Potential</span>
                                        @elseif($lead->customer_status=='Potential')
                                        <span class="badge" style="background: #5970f3;font-size: 14px;color: white;" >Potential</span>
                                        @elseif($lead->customer_status=='Very Potential')
                                        <span class="badge" style="background: #0829e5;font-size: 14px;color: white;" >Very Potential</span>
                                        @endif
                                    </td>
                                    <td>{{$lead->company_status}}</td>
                                    <td>{{$lead->created_at->format('d-m-Y')}}</td>
                                    <!--<td>-->
                                        <!--@if($lead->status=='Contacted')-->
                                        <!--<span class="badge" style="background: #ff108c;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>-->
                                        <!--@elseif($lead->status=='Interested')-->
                                        <!--<span class="badge" style="background: #4788ff;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>-->
                                        <!--@elseif($lead->status=='Follow-up Sheduled')-->
                                        <!--<span class="badge" style="background: #d5ab05;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>-->
                                        <!--@elseif($lead->status=='Meeting Done')-->
                                        <!--<span class="badge" style="background: #13c238;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>-->
                                        <!--@elseif($lead->status=='Proposal Sent')-->
                                        <!--<span class="badge" style="background: #0b9e97;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>-->
                                        <!--@elseif($lead->status=='Win')-->
                                        <!--<span class="badge" style="background: #670bc1;font-size: 14px;color: white;" >{{ucfirst($lead->status)}} (Convert Customer)</span>-->
                                        <!--@elseif($lead->status=='Cancelled')-->
                                        <!--<span class="badge" style="background: #ff2e37;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>-->
                                        <!--@else-->
                                        <!--<span class="badge" style="background: #2c66cb;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>-->
                                        <!--@endif-->
                                    <!--</td>-->
                                    <td>{{$lead->visits()->count()}}</td>
                                    <td>{{$lead->meetings()->count()}}</td>
                                    <td>{{$lead->tasks()->count()}}</td>
                                    <td>{{$lead->notes()->count()}}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                        <div>
                        {{$leads->links('pagination')}}
                        </div>
                        
                    @elseif($action=='meeting')
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="min-width: 150px;">Meeting Title</th>
                                        <th style="min-width: 200px;">Participants</th>
                                        <th style="min-width: 120px;">Date & Time</th>
                                        <th style="min-width: 120px;">Type</th>
                                        <th style="min-width: 120px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($meetings as $i=>$meeting)
                                    <tr>
                                        <td>{{$meeting->name}}</td>
                                        <td>
                                            @foreach($meeting->participantsUsers()->get() as $user)
                                            @if($meeting->type==1)
                                            <span>{{$user->name}} - {{$user->email}}</span>
                                            @else
                                            <span>{{$user->factory_name}} - {{$user->owner_name}}</span>
                                            @endif
                                            @endforeach
                                        </td>
                                        <td>{{$meeting->created_at->format('d-m-Y h:i A')}}</td>
                                        <td>{{ucfirst($meeting->meeting_type)}}</td>
                                        <td>
                                           
                                            @if($meeting->status=='In progress')
                                            <span class="badge" style="background: #ff108c;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                            @elseif($meeting->status=='Completed')
                                            <span class="badge" style="background: #13c238;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                            @elseif($meeting->status=='Canceled')
                                            <span class="badge" style="background: #ff2e37;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                            @elseif($meeting->status=='Rescheduled')
                                            <span class="badge" style="background: #f326eb;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                            @else
                                            <span class="badge" style="background: #2c66cb;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$meetings->links('pagination')}}
                        </div>
                    
                    
                    
                    @elseif($action=='visits')
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="min-width: 200px;">Visit Date</th>
                                        <th style="min-width: 100px;">Location</th>
                                        <th style="min-width: 100px;">Company/Lead</th>
                                        <th style="min-width: 100px;">Description</th>
                                        <th style="min-width: 100px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($visits as $i=>$visit)
                                    <tr>
                                        <td>
                                            {{$visit->visit_date?Carbon\Carbon::parse($visit->visit_date)->format('d-m-Y'):''}}
                                            @if($visit->imageFile)
                                            <a href="{{asset($visit->image())}}" download="" style="margin-left: 5px;color: #e1000a;"><i class="bx bx-file"></i></a>
                                            @endif
                                        </td>
                                        <td>{{$visit->location}}</td>
                                        <td>
                                            @if($visit->type==1)
                                            <span>{{$visit->company?$visit->company->name:''}}</span>
                                            @else
                                            {{$visit->company?$visit->company->factory_name:''}}
                                            @endif
                                        </td>
                                        <td>{{$visit->description}}</td>
                                        <td>
                                             @if($visit->status=='Not Potential')
                                            <span class="badge" style="background: #9baaff;font-size: 14px;color: white;" >Not Potential</span>
                                            @elseif($visit->status=='Potential')
                                            <span class="badge" style="background: #5970f3;font-size: 14px;color: white;" >Potential</span>
                                            @elseif($visit->status=='Very Potential')
                                            <span class="badge" style="background: #0829e5;font-size: 14px;color: white;" >Very Potential</span>
                                            @endif
                                            
                                            <!--@if($visit->status=='In progress')-->
                                            <!--<span class="badge" style="background: #ff108c;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                            <!--@elseif($visit->status=='Completed')-->
                                            <!--<span class="badge" style="background: #13c238;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                            <!--@elseif($visit->status=='Canceled')-->
                                            <!--<span class="badge" style="background: #ff2e37;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                            <!--@elseif($visit->status=='Rescheduled')-->
                                            <!--<span class="badge" style="background: #f326eb;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                            <!--@else-->
                                            <!--<span class="badge" style="background: #2c66cb;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                            <!--@endif-->
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$visits->links('pagination')}}
                        </div>
                        
                        
                    @elseif($action=='notes')
                    
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="min-width: 200px;">Note</th>
                                        <th style="min-width: 120px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notes as $i=>$note)
                                    <tr>
                                        <td>
                                           {!!nl2br(e($note->description))!!}
                                        </td>
                                        <td>
                                            {{$note->created_at->format('d-m-Y')}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$notes->links('pagination')}}
                        </div>
                        
                    @elseif($action=='tasks')
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="min-width: 200px;">Task Name</th>
                                        <th style="min-width: 100px;">Company</th>
                                        <th style="min-width: 100px;">Priority</th>
                                        <th style="min-width: 100px;">Status</th>
                                        <th style="min-width: 120px;">Assinee Date</th>
                                        <th style="min-width: 120px;">Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $i=>$task)
                                    <tr>
                                        <td>{{$task->name}}</td>
                                        <td>
                                            @if($task->type==1)
                                            <span>{{$task->company?$task->company->name:''}}</span>
                                            @else
                                            {{$task->company?$task->company->factory_name:''}}
                                            @endif
                                        </td>
                                        <td>{{ucfirst($task->priority)}}</td>
                                        <td>
                                            @if($task->status=='in progress')
                                            <span class="badge" style="background: #ff108c;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                            @elseif($task->status=='review')
                                            <span class="badge" style="background: #d5ab05;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                            @elseif($task->status=='completed')
                                            <span class="badge" style="background: #13c238;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                            @elseif($task->status=='canceled')
                                            <span class="badge" style="background: #ff2e37;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                            @elseif($task->status=='on hold')
                                            <span class="badge" style="background: #f326eb;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                            @else
                                            <span class="badge" style="background: #2c66cb;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                            @endif
                                        </td>
                                        <td>{{$task->created_at->format('d-m-Y')}}</td>
                                        <td>{{$task->due_date?Carbon\Carbon::parse($task->due_date)->format('d-m-Y'):''}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$tasks->links('pagination')}}
                        </div>
                        
                    @elseif($action=='companies')
                    
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="min-width: 200px;">Company Name</th>
                                        <th style="min-width: 200px;">Owner Name</th>
                                        <th style="min-width: 150px;">Owner Mobile</th>
                                        <th style="min-width: 250px;">Sister Concern</th>
                                        <th style="min-width: 400px;">Address</th>
                                        <th style="min-width: 120px;">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($companies as $i=>$company)
                                    <tr>
                                        <td>
                                            <a href="{{route('admin.companiesAction',['view',$company->id])}}" >
                                            {{$company->factory_name}}
                                            </a>
                                        </td>
                                        <td>{{$company->owner_name}}</td>
                                        <td>{{$company->owner_mobile}}</td>
                                        <td>{{$company->concern}}</td>
                                        <td>{{$company->fullAddress()}}</td>
                                        <td>{{$company->created_at->format('d-m-Y')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$companies->links('pagination')}}
                        </div>
                    
                    @elseif($action=='reports')
                    
                    <div>
                        <form action="{{route('admin.usersCustomerAction',['reports',$user->id])}}">
                            <div class="row">
                                <div class="col-md-4 mb-1">
                                    
                                    <div class="input-group">
                                        <div class="dropdown">
                                            <button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border: none;background: #ebebec;height: 100%;">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.usersCustomerAction',['reports',$user->id,'startDate'=>Carbon\Carbon::now()->format('Y-m-d'),'endDate'=>Carbon\Carbon::now()->format('Y-m-d')])}}">
                                                     Today
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.usersCustomerAction',['reports',$user->id,'startDate'=>Carbon\Carbon::now()->subDays(30)->format('Y-m-d'),'endDate'=>Carbon\Carbon::now()->format('Y-m-d')])}}">
                                                    Last 30 Days
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.usersCustomerAction',['reports',$user->id,'startDate'=>Carbon\Carbon::now()->startOfYear()->format('Y-m-d'),'endDate'=>Carbon\Carbon::now()->format('Y-m-d')])}}">
                                                    This Years
                                                </a>
                                            </div>
                                        </div>
                                        <input type="date" name="startDate" value="{{$startDate->format('Y-m-d')}}" class="form-control form-control-sm" />
                                        <input type="date" name="endDate"  value="{{$endDate->format('Y-m-d')}}" class="form-control form-control-sm" />
                                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div class="summery">
                            <div class="info">
                                <h3>Leads</h3>
                                <span>{{$summeryReport['Leads']}}</span>
                            </div>
                            <div class="info">
                                <h3>Customer</h3>
                                <span>{{$summeryReport['Companies']}}</span>
                            </div>
                            <div class="info">
                                <h3>Meating</h3>
                                <span>{{$summeryReport['Meeting']}}</span>
                            </div>
                            <div class="info">
                                <h3>Visits</h3>
                                <span>{{$summeryReport['Visits']}}</span>
                            </div>
                            <div class="info">
                                <h3>Sales</h3>
                                <span>{{$summeryReport['Companies']}}</span>
                            </div>
                            <div class="info">
                                <h3>Sales Due</h3>
                                <span>{{$summeryReport['Companies']}}</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="{{route('admin.reports',['startDate'=>$startDate->format('Y-m-d'),'endDate'=>$endDate->format('Y-m-d'),'employee_id'=>$user->id])}}" class="btn btn-sm btn-success" target="_blank">View Details <i class="bx bx-link"></i></a>
                        </div>
                        
                    </div>
                    
                    @else
                    
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
                    
                    @endif
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
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

@if($user->lastLocation)

<script>
$(document).ready(function(){

    let userName = "{{ $user->name }}";

    function updateMap(){
        $.get("{{ route('admin.usersCustomerAction',['location',$user->id]) }}", function(res){

            if(res.latitude && res.longitude){
                let lat2 = parseFloat(res.latitude);
                let lng2 = parseFloat(res.longitude);
                let time =res.time;

                let gmapLink = `https://www.google.com/maps?q=${lat2},${lng2}`;
                let iframeSrc = `https://www.google.com/maps?q=${lat2},${lng2}&output=embed`;
                // Update iframe src
                $('#googleMap').attr('src', iframeSrc);

                // Update message with link
                $('#mapMessage').html(`${userName} is last! ${time}`);

            }else{
                $('#mapMessage').html('User Not Active');
                $('#googleMap').attr('src', '');
            }
        });
    }

    // Initial call
    updateMap();

    // Update every 1 minute
    setInterval(updateMap, 60000);

});
</script>


<script>
$(document).ready(function() {
    let mapContainer = $('.showMapArea');
    mapContainer.html('');

    let map = L.map(mapContainer[0]).setView([23.8103, 90.4125], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = null;

    function updateLocation(){
        $.get("{{ route('admin.usersCustomerAction',['location',$user->id]) }}", function(res){
            if(res.latitude && res.longitude){
                let lat = parseFloat(res.latitude);
                let lng = parseFloat(res.longitude);
                let time =res.time;
                let gmapLink = `https://www.google.com/maps?q=${lat},${lng}`;
                let popupContent = `{{ $user->name }} is last! ${time} <br><a href="${gmapLink}" target="_blank">View on Map</a>`;
                if(marker){
                    marker.setLatLng([lat, lng])
                        .bindPopup(popupContent)
                        .openPopup();
                }else{
                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup(popupContent)
                        .openPopup();
                }
                map.panTo([lat, lng]);
            }else{
                mapContainer.html('<span style="font-size: 60px;color: gainsboro;padding-top: 100px;display: block;">User Not Active</span>');
            }
            
        });
    }

    updateLocation();
    setInterval(updateLocation, 6000);

});
</script>
@else
<script>
$(document).ready(function(){
    $('.showMapArea').html('<span style="font-size: 60px;color: gainsboro;padding-top: 100px;display: block;">User Not Active</span>');
});
</script>
@endif

@endpush