@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Role Update')}}</title>
@endsection @push('css')
<style type="text/css">
    .col-md-3{
        padding: 6px 15px;
    }
</style>
@endpush 
@section('contents')
<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>Role Update</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item"><a href="{{route('admin.userRoles')}}">User Roles</a></li>
        <li class="item">Role Update</li>
    </ol>
</div>

@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <!-- Start -->
    <form action="{{route('admin.userRoleAction',['update',$role->id])}}" method="post">
    @csrf
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Role Update</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>Role Name </label>
                <input type="text" class="form-control" name="name" placeholder="Role name" value="{{$role->name}}" required="" />
                @if ($errors->has('name'))
                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                @endif
            </div>
            <button type="submit" class="btn btn-primary btn-md rounded-0">Save changes</button>
        </div>
    </div>
    
    <!--======Start Customer Management Permission======-->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
             <h3>Customers Management
             <div class="checkbox">
                 <input class="inp-cbx selectAll" data-type="companyAll" id="company_all" type="checkbox"  style="display: none;"
                 
                 @if(
                    isset(json_decode($role->permission, true)['company']['add']) &&  
                    isset(json_decode($role->permission, true)['company']['view']) &&  
                    isset(json_decode($role->permission, true)['company']['delete']) &&  
                    isset(json_decode($role->permission, true)['company']['export']) &&  
                    isset(json_decode($role->permission, true)['company']['all']) 
                    )
                 
                 checked
                 
                 @endif
                 />
                 <label class="cbx" for="company_all">
                     <span>
                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                         </svg>
                     </span>
                 </label>
             </div>
             <label for="company_all">All</label>
             </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx companyAll" id="company_add" type="checkbox" name="permission[company][add]" @isset(json_decode($role->permission, true)['company']['add']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="company_add">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="company_add">Create/Update</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx companyAll" id="company_view" type="checkbox" name="permission[company][view]" @isset(json_decode($role->permission, true)['company']['view']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="company_view">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="company_view">View</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx companyAll" id="company_delete" type="checkbox" name="permission[company][delete]" @isset(json_decode($role->permission, true)['company']['delete']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="company_delete">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="company_delete">Delete</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx companyAll" id="company_export" type="checkbox" name="permission[company][export]" @isset(json_decode($role->permission, true)['company']['export']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="company_export">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="company_export" >Export</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx companyAll" id="company_sale" type="checkbox" name="permission[company][sales]" @isset(json_decode($role->permission, true)['company']['sales']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="company_sale">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="company_sale">Sales</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx companyAll" id="company_duecollect" type="checkbox" name="permission[company][duecollect]" @isset(json_decode($role->permission, true)['company']['duecollect']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="company_duecollect">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="company_duecollect">Due Collect</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx companyAll" id="company_service" type="checkbox" name="permission[company][service]" @isset(json_decode($role->permission, true)['company']['service']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="company_service">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="company_service">Services</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx companyAll" id="company_list" type="checkbox" name="permission[company][all]" @isset(json_decode($role->permission, true)['company']['all']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="company_list">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="company_list" >All</label>
                </div>
            </div>
        </div>
    </div>
    <!--======End Customer Management Permission======-->
    <!--======Start Engineer Management Permission======-->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
             <h3>Engineers Management
             <div class="checkbox">
                 <input class="inp-cbx selectAll" data-type="engineerAll" id="engineer_all" type="checkbox"  style="display: none;"
                 
                 @if(
                    isset(json_decode($role->permission, true)['engineers']['add']) &&  
                    isset(json_decode($role->permission, true)['engineers']['view']) &&  
                    isset(json_decode($role->permission, true)['engineers']['delete']) &&  
                    isset(json_decode($role->permission, true)['engineers']['export']) &&  
                    isset(json_decode($role->permission, true)['engineers']['all']) 
                    )
                 
                 checked
                 
                 @endif
                 />
                 <label class="cbx" for="engineer_all">
                     <span>
                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                         </svg>
                     </span>
                 </label>
             </div>
             <label for="engineer_all">All</label>
             </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx engineerAll" id="engineer_add" type="checkbox" name="permission[engineers][add]" @isset(json_decode($role->permission, true)['engineers']['add']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="engineer_add">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="engineer_add">Create/Update</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx engineerAll" id="engineer_view" type="checkbox" name="permission[engineers][view]" @isset(json_decode($role->permission, true)['engineers']['view']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="engineer_view">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="engineer_view">View</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx engineerAll" id="engineer_delete" type="checkbox" name="permission[engineers][delete]" @isset(json_decode($role->permission, true)['engineers']['delete']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="engineer_delete">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="engineer_delete">Delete</label>
                </div>
                
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx engineerAll" id="engineer_export" type="checkbox" name="permission[engineers][export]" @isset(json_decode($role->permission, true)['engineers']['export']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="engineer_export">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="engineer_export">Export</label>
                </div>
                
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx engineerAll" id="engineer_list" type="checkbox" name="permission[engineers][all]" @isset(json_decode($role->permission, true)['engineers']['all']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="engineer_list">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="engineer_list" >All</label>
                </div>
            </div>
        </div>
    </div>
    <!--======End Engineer Management Permission======-->
    
    <!--======Start Lead Management Permission======-->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
             <h3>Leads Management
             <div class="checkbox">
                 <input class="inp-cbx selectAll" data-type="leadAll" id="lead_all" type="checkbox"  style="display: none;"
                 
                 @if(
                    isset(json_decode($role->permission, true)['leads']['add']) &&  
                    isset(json_decode($role->permission, true)['leads']['view']) &&  
                    isset(json_decode($role->permission, true)['leads']['delete']) &&  
                    isset(json_decode($role->permission, true)['leads']['all']) 
                    )
                 
                 checked
                 
                 @endif
                 />
                 <label class="cbx" for="lead_all">
                     <span>
                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                         </svg>
                     </span>
                 </label>
             </div>
             <label for="lead_all">All</label>
             </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx leadAll" id="lead_add" type="checkbox" name="permission[leads][add]" @isset(json_decode($role->permission, true)['leads']['add']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="lead_add">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="lead_add">Create/Update</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx leadAll" id="lead_view" type="checkbox" name="permission[leads][view]" @isset(json_decode($role->permission, true)['leads']['view']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="lead_view">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="lead_view">View</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx leadAll" id="lead_delete" type="checkbox" name="permission[leads][delete]" @isset(json_decode($role->permission, true)['leads']['delete']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="lead_delete">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="lead_delete">Delete</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx leadAll" id="lead_list" type="checkbox" name="permission[leads][all]" @isset(json_decode($role->permission, true)['leads']['all']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="lead_list">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="lead_list" >All</label>
                </div>
            </div>
        </div>
    </div>
    <!--======End Lead Management Permission======-->
    <!--======Start Task Management Permission======-->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
             <h3>Task Management
             <div class="checkbox">
                 <input class="inp-cbx selectAll" data-type="taskAll" id="task_all" type="checkbox"  style="display: none;"
                 
                 @if(
                    isset(json_decode($role->permission, true)['tasks']['add']) &&  
                    isset(json_decode($role->permission, true)['tasks']['view']) &&  
                    isset(json_decode($role->permission, true)['tasks']['delete']) &&  
                    isset(json_decode($role->permission, true)['tasks']['all']) 
                    )
                 
                 checked
                 
                 @endif
                 />
                 <label class="cbx" for="task_all">
                     <span>
                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                         </svg>
                     </span>
                 </label>
             </div>
             <label for="task_all">All</label>
             </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx taskAll" id="task_add" type="checkbox" name="permission[tasks][add]" @isset(json_decode($role->permission, true)['tasks']['add']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="task_add">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="task_add">Create/Update</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx taskAll" id="task_view" type="checkbox" name="permission[tasks][view]" @isset(json_decode($role->permission, true)['tasks']['view']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="task_view">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="task_view">View</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx taskAll" id="task_delete" type="checkbox" name="permission[tasks][delete]" @isset(json_decode($role->permission, true)['tasks']['delete']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="task_delete">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="task_delete">Delete</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx taskAll" id="task_list" type="checkbox" name="permission[tasks][all]" @isset(json_decode($role->permission, true)['tasks']['all']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="task_list">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="task_list" >All</label>
                </div>
            </div>
        </div>
    </div>
    <!--======End Task Management Permission======-->
    <!--======Start Meeting Management Permission======-->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
             <h3>Meeting Management
             <div class="checkbox">
                 <input class="inp-cbx selectAll" data-type="meetingAll" id="meeting_all" type="checkbox"  style="display: none;"
                 
                 @if(
                    isset(json_decode($role->permission, true)['meetings']['add']) &&  
                    isset(json_decode($role->permission, true)['meetings']['view']) &&  
                    isset(json_decode($role->permission, true)['meetings']['delete']) &&  
                    isset(json_decode($role->permission, true)['meetings']['all']) 
                    )
                 
                 checked
                 
                 @endif
                 />
                 <label class="cbx" for="meeting_all">
                     <span>
                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                         </svg>
                     </span>
                 </label>
             </div>
             <label for="meeting_all">All</label>
             </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx meetingAll" id="meeting_add" type="checkbox" name="permission[meetings][add]" @isset(json_decode($role->permission, true)['meetings']['add']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="meeting_add">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="meeting_add">Create/Update</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx meetingAll" id="meeting_view" type="checkbox" name="permission[meetings][view]" @isset(json_decode($role->permission, true)['meetings']['view']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="meeting_view">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="meeting_view">View</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx meetingAll" id="meeting_delete" type="checkbox" name="permission[meetings][delete]" @isset(json_decode($role->permission, true)['meetings']['delete']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="meeting_delete">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="meeting_delete">Delete</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx meetingAll" id="meeting_list" type="checkbox" name="permission[meetings][all]" @isset(json_decode($role->permission, true)['meetings']['all']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="meeting_list">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="meeting_list" >All</label>
                </div>
            </div>
        </div>
    </div>
    <!--======End Meeting Management Permission======-->
    <!--======Start Visit Management Permission======-->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
             <h3>Visit Management
             <div class="checkbox">
                 <input class="inp-cbx selectAll" data-type="visitAll" id="visit_all" type="checkbox"  style="display: none;"
                 
                 @if(
                    isset(json_decode($role->permission, true)['visits']['add']) &&  
                    isset(json_decode($role->permission, true)['visits']['view']) &&  
                    isset(json_decode($role->permission, true)['visits']['delete']) &&  
                    isset(json_decode($role->permission, true)['visits']['all']) 
                    )
                 
                 checked
                 
                 @endif
                 />
                 <label class="cbx" for="visit_all">
                     <span>
                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                         </svg>
                     </span>
                 </label>
             </div>
             <label for="visit_all">All</label>
             </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx visitAll" id="visit_add" type="checkbox" name="permission[visits][add]" @isset(json_decode($role->permission, true)['visits']['add']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="visit_add">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="visit_add">Create/Update</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx visitAll" id="visit_view" type="checkbox" name="permission[visits][view]" @isset(json_decode($role->permission, true)['visits']['view']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="visit_view">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="visit_view">View</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx visitAll" id="visit_delete" type="checkbox" name="permission[visits][delete]" @isset(json_decode($role->permission, true)['visits']['delete']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="visit_delete">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="visit_delete">Delete</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx visitAll" id="visit_list" type="checkbox" name="permission[visits][all]" @isset(json_decode($role->permission, true)['visits']['all']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="visit_list">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="visit_list" >All</label>
                </div>
            </div>
        </div>
    </div>
    <!--======End Visit Management Permission======-->
    
    <!--======Start Pi Management Permission======-->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
             <h3>Sales Invoice
             <div class="checkbox">
                 <input class="inp-cbx selectAll" data-type="piAll" id="pi_all" type="checkbox"  style="display: none;"
                 
                 @if(
                    isset(json_decode($role->permission, true)['sales']['add']) &&  
                    isset(json_decode($role->permission, true)['sales']['view']) &&  
                    isset(json_decode($role->permission, true)['sales']['delete']) &&  
                    isset(json_decode($role->permission, true)['sales']['report']) 
                    )
                 
                 checked
                 
                 @endif
                 />
                 <label class="cbx" for="pi_all">
                     <span>
                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                         </svg>
                     </span>
                 </label>
             </div>
             <label for="pi_all">All</label>
             </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx piAll" id="pi_add" type="checkbox" name="permission[sales][add]" @isset(json_decode($role->permission, true)['sales']['add']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="pi_add">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="pi_add">Create/Update</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx piAll" id="pi_view" type="checkbox" name="permission[sales][view]" @isset(json_decode($role->permission, true)['sales']['view']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="pi_view">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="pi_view">View</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx piAll" id="pi_delete" type="checkbox" name="permission[sales][delete]" @isset(json_decode($role->permission, true)['sales']['delete']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="pi_delete">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="pi_delete">Delete</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx piAll" id="pi_report" type="checkbox" name="permission[sales][all]" @isset(json_decode($role->permission, true)['sales']['all']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="pi_report">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="pi_report" >All</label>
                </div>
                
            </div>
        </div>
    </div>
    <!--======End Pi Management Permission======-->

    <!--======Start LC Management Permission======-->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
             <h3>Quotation
             <div class="checkbox">
                 <input class="inp-cbx selectAll" data-type="lcAll" id="lc_all" type="checkbox"  style="display: none;"
                 
                 @if(
                    isset(json_decode($role->permission, true)['quotation']['add']) &&  
                    isset(json_decode($role->permission, true)['quotation']['view']) &&  
                    isset(json_decode($role->permission, true)['quotation']['delete']) &&  
                    isset(json_decode($role->permission, true)['quotation']['all']) 
                    )
                 
                 checked
                 
                 @endif
                 
                 />
                 <label class="cbx" for="lc_all">
                     <span>
                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                         </svg>
                     </span>
                 </label>
             </div>
             <label for="lc_all">All</label>
             </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx lcAll" id="lc_add" type="checkbox" name="permission[quotation][add]" @isset(json_decode($role->permission, true)['quotation']['add']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="lc_add">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="lc_add">Create/Update</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx lcAll" id="lc_view" type="checkbox" name="permission[quotation][view]" @isset(json_decode($role->permission, true)['quotation']['view']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="lc_view">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="lc_view" >View</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx lcAll" id="lc_delete" type="checkbox" name="permission[quotation][delete]" @isset(json_decode($role->permission, true)['quotation']['delete']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="lc_delete">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="lc_delete" >Delete</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx lcAll" id="lc_report" type="checkbox" name="permission[quotation][all]" @isset(json_decode($role->permission, true)['quotation']['all']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="lc_report">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="lc_report">All</label>
                </div>
            </div>
        </div>
    </div>
    
    <!--======End LC Management Permission======-->
   {{--
    <!--======Start Payrooll Management Permission======-->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
             <h3>Payroll Management
             
             <div class="checkbox">
                 <input class="inp-cbx selectAll" data-type="payroll" id="payroll" type="checkbox"  style="display: none;"
                 
                 @if(
                    isset(json_decode($role->permission, true)['expenses']['add']) &&  
                    isset(json_decode($role->permission, true)['expenses']['delete']) &&  
                    isset(json_decode($role->permission, true)['expenses']['type']) &&  
                    isset(json_decode($role->permission, true)['expenses']['report']) 
                    )
                 
                 checked
                 
                 @endif
                 
                 />
                 <label class="cbx" for="payroll">
                     <span>
                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                         </svg>
                     </span>
                 </label>
             </div>
             <label for="payroll">All</label>
             </h3>
        </div>
        <div class="card-body">
            <h5>Expenses:</h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx payroll" id="expenses_list" type="checkbox" name="permission[expenses][add]" @isset(json_decode($role->permission, true)['expenses']['add']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="expenses_list">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="expenses_list">Expenses Create/Update</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx payroll" id="expenses_delete" type="checkbox" name="permission[expenses][delete]" @isset(json_decode($role->permission, true)['expenses']['delete']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="expenses_delete">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="expenses_delete" >Expenses Delete</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx payroll" id="expenses_type" type="checkbox" name="permission[expenses][type]" @isset(json_decode($role->permission, true)['expenses']['type']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="expenses_type">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="expenses_type">Expenses Type</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx payroll" id="expenses_report" type="checkbox" name="permission[expenses][report]" @isset(json_decode($role->permission, true)['expenses']['report']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="expenses_report">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="expenses_report">Expenses Reports</label>
                </div>
            </div>
            <br>
            <h5>Salary Sheet:</h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx payroll" id="salarySheet_add" type="checkbox" name="permission[salarySheet][add]" @isset(json_decode($role->permission, true)['salarySheet']['add']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="salarySheet_add">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="salarySheet_add" >Create/Update/Delete</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx payroll" id="salarySheet_view" type="checkbox" name="permission[salarySheet][view]" @isset(json_decode($role->permission, true)['salarySheet']['view']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="salarySheet_view">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="salarySheet_view">Salary Sheet View</label>
                </div>
            </div>
            
            
            
        </div>
    </div>
    --}}
    <!--======End LC Management Permission======-->
    
    <!--======Account Management Permission======-->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
             <h3>Account Management
             <div class="checkbox">
                 <input class="inp-cbx selectAll" data-type="accountM" id="accountM" type="checkbox"  style="display: none;"
                 @if(
                    isset(json_decode($role->permission, true)['paymentMethod']['list']) &&  
                    isset(json_decode($role->permission, true)['accounts']['list']) &&  
                    isset(json_decode($role->permission, true)['deposit']['list']) &&  
                    isset(json_decode($role->permission, true)['withdrawal']['list']) 
                    )
                 
                 checked
                 
                 @endif
                 />
                 <label class="cbx" for="accountM">
                     <span>
                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                         </svg>
                     </span>
                 </label>
             </div>
             <label for="accountM">All</label>
             </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx accountM" id="paymentMethod" type="checkbox" name="permission[paymentMethod][list]" @isset(json_decode($role->permission, true)['paymentMethod']['list']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="paymentMethod">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="paymentMethod">Payment Methods</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx accountM" id="accounts" type="checkbox" name="permission[accounts][list]" @isset(json_decode($role->permission, true)['accounts']['list']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="accounts">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="accounts">Account List</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx accountM" id="deposit" type="checkbox" name="permission[deposit][list]" @isset(json_decode($role->permission, true)['deposit']['list']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="deposit">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="deposit" >Deposit</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx accountM" id="withdrawal" type="checkbox" name="permission[withdrawal][list]" @isset(json_decode($role->permission, true)['withdrawal']['list']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="withdrawal">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="withdrawal">Withdrawal</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx accountM" id="billCollectin" type="checkbox" name="permission[billCollectin][list]" @isset(json_decode($role->permission, true)['billCollectin']['list']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="billCollectin">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="billCollectin">Bill Collection</label>
                </div>
            </div>
        </div>
    </div>
    <!--======End Account Management Permission======-->
    
   
    <!--======HR Management Permission======-->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
             <h3>HR Management
             <div class="checkbox">
                 <input class="inp-cbx selectAll" data-type="HR_m" id="HR_m" type="checkbox"  style="display: none;"
                 
                 @if(
                    isset(json_decode($role->permission, true)['departments']['list']) &&  
                    isset(json_decode($role->permission, true)['designations']['list']) &&  
                    isset(json_decode($role->permission, true)['product']['list']) &&  
                    isset(json_decode($role->permission, true)['productUnit']['list']) &&  
                    isset(json_decode($role->permission, true)['employees']['list']) &&  
                    isset(json_decode($role->permission, true)['adminUsers']['list']) &&  
                    isset(json_decode($role->permission, true)['adminRoles']['list']) 
                    )
                 
                 checked
                 
                 @endif
                 
                 />
                 <label class="cbx" for="HR_m">
                     <span>
                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                         </svg>
                     </span>
                 </label>
             </div>
             <label for="HR_m">All</label>
             </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx HR_m" id="departments" type="checkbox" name="permission[departments][list]" @isset(json_decode($role->permission, true)['departments']['list']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="departments">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="departments" >Departments</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx HR_m" id="designations" type="checkbox" name="permission[designations][list]" @isset(json_decode($role->permission, true)['designations']['list']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="designations">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="designations">Designations</label>
                </div>
                <!--<div class="col-md-3">-->
                <!--    <div class="checkbox">-->
                <!--         <input class="inp-cbx HR_m" id="suppiers" type="checkbox" name="permission[suppiers][list]" @isset(json_decode($role->permission, true)['suppiers']['list']) checked @endisset  style="display: none;" />-->
                <!--         <label class="cbx" for="suppiers">-->
                <!--             <span>-->
                <!--                 <svg width="12px" height="10px" viewbox="0 0 12 10">-->
                <!--                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>-->
                <!--                 </svg>-->
                <!--             </span>-->
                <!--         </label>-->
                <!--     </div>-->
                <!--     <label style="margin:0 5px;" for="suppiers">Suppliers</label>-->
                <!--</div>-->
                <!--<div class="col-md-3">-->
                <!--    <div class="checkbox">-->
                <!--         <input class="inp-cbx HR_m" id="ReffTitle" type="checkbox" name="permission[ReffTitle][list]" @isset(json_decode($role->permission, true)['ReffTitle']['list']) checked @endisset  style="display: none;" />-->
                <!--         <label class="cbx" for="ReffTitle">-->
                <!--             <span>-->
                <!--                 <svg width="12px" height="10px" viewbox="0 0 12 10">-->
                <!--                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>-->
                <!--                 </svg>-->
                <!--             </span>-->
                <!--         </label>-->
                <!--     </div>-->
                <!--     <label style="margin:0 5px;" for="ReffTitle">Reff/Title List</label>-->
                <!--</div>-->
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx HR_m" id="productList" type="checkbox" name="permission[product][list]" @isset(json_decode($role->permission, true)['product']['list']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="productList">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="productList" >Product List</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx HR_m" id="productUnit" type="checkbox" name="permission[productUnit][list]" @isset(json_decode($role->permission, true)['productUnit']['list']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="productUnit">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="productUnit">Product Unit</label>
                </div>
                
                <!--<div class="col-md-3">-->
                <!--    <div class="checkbox">-->
                <!--         <input class="inp-cbx HR_m" id="companies" type="checkbox" name="permission[companies][list]" @isset(json_decode($role->permission, true)['companies']['list']) checked @endisset  style="display: none;" />-->
                <!--         <label class="cbx" for="companies">-->
                <!--             <span>-->
                <!--                 <svg width="12px" height="10px" viewbox="0 0 12 10">-->
                <!--                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>-->
                <!--                 </svg>-->
                <!--             </span>-->
                <!--         </label>-->
                <!--     </div>-->
                <!--     <label style="margin:0 5px;" for="companies" >Companies</label>-->
                <!--</div>-->
                <!--<div class="col-md-3">-->
                <!--    <div class="checkbox">-->
                <!--         <input class="inp-cbx HR_m" id="merchandisers" type="checkbox" name="permission[merchandisers][list]" @isset(json_decode($role->permission, true)['merchandisers']['list']) checked @endisset  style="display: none;" />-->
                <!--         <label class="cbx" for="merchandisers">-->
                <!--             <span>-->
                <!--                 <svg width="12px" height="10px" viewbox="0 0 12 10">-->
                <!--                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>-->
                <!--                 </svg>-->
                <!--             </span>-->
                <!--         </label>-->
                <!--     </div>-->
                <!--     <label style="margin:0 5px;" for="merchandisers" >Merchandisers</label>-->
                <!--</div>-->
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx HR_m" id="employees" type="checkbox" name="permission[employees][list]" @isset(json_decode($role->permission, true)['employees']['list']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="employees">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="employees" >Employee Users</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx HR_m" id="adminUsers" type="checkbox" name="permission[adminUsers][list]" @isset(json_decode($role->permission, true)['adminUsers']['list']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="adminUsers">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="adminUsers" >Admin Users</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx HR_m" id="adminRoles" type="checkbox" name="permission[adminRoles][list]" @isset(json_decode($role->permission, true)['adminRoles']['list']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="adminRoles">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="adminRoles" >Roles Setup</label>
                </div>
            </div>
        </div>
    </div>
    <!--======End HR Management Permission======-->
  
    <!--======Software Setting Permission======-->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
             <h3>Software Setting
             
             <div class="checkbox">
                 <input class="inp-cbx selectAll" data-type="soft_setting" id="soft_setting" type="checkbox"  style="display: none;"
                 @if(
                    isset(json_decode($role->permission, true)['appsSetting']['general']) &&  
                    isset(json_decode($role->permission, true)['appsSetting']['mail']) &&  
                    isset(json_decode($role->permission, true)['appsSetting']['sms']) 
                    )
                 
                 checked
                 
                 @endif
                 />
                 <label class="cbx" for="soft_setting">
                     <span>
                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                         </svg>
                     </span>
                 </label>
             </div>
             <label for="soft_setting">All</label>
             </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx soft_setting" id="appsSetting_general" type="checkbox" name="permission[appsSetting][general]" @isset(json_decode($role->permission, true)['appsSetting']['general']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="appsSetting_general">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="appsSetting_general">General Setting</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx soft_setting" id="appsSetting_mail" type="checkbox" name="permission[appsSetting][mail]" @isset(json_decode($role->permission, true)['appsSetting']['mail']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="appsSetting_mail">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label style="margin:0 5px;" for="appsSetting_mail" >Mail Setting</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx soft_setting" id="appsSetting_sms" type="checkbox" name="permission[appsSetting][sms]" @isset(json_decode($role->permission, true)['appsSetting']['sms']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="appsSetting_sms">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label for="appsSetting_sms" style="margin:0 5px;">SMS Setting</label>
                </div>
                <div class="col-md-3">
                    <div class="checkbox">
                         <input class="inp-cbx soft_setting" id="appsSetting_report" type="checkbox" name="permission[appsSetting][report]" @isset(json_decode($role->permission, true)['appsSetting']['report']) checked @endisset  style="display: none;" />
                         <label class="cbx" for="appsSetting_report">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                         </label>
                     </div>
                     <label for="appsSetting_report" style="margin:0 5px;">Reports</label>
                </div>
            </div>
        </div>
    </div>
    
    
    <!--======End Software Setting Permission======-->
    </form>
    
    
</div>


@endsection @push('js')

<script type="text/javascript">
    $(document).ready(function () {
        $(".selectAll").change(function () {
            var dataClass =$(this).data('type');
            var checked = $(this).prop("checked");
            $('.'+dataClass).prop("checked", checked);
        });
    });
</script>

@endpush
