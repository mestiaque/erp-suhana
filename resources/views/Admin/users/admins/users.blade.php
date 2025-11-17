@extends(adminTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Admin Users')}}</title>
@endsection 
@push('css')
<style>
    .adminProfile img {
        height: 150px;
        max-width: 100%;
        width: unset;
        margin: auto;
    }
    .info ul {
        padding: 0;
        margin: 0;
    }
    .adminProfile {
        margin-bottom: 15px;
    }
</style>
@endpush 
@section('contents')


@include(adminTheme().'alerts')
<div class="flex-grow-1">
<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Admin List</h3>
         <div class="dropdown">

             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddUser">
                 <i class="bx bx-plus"></i> User
             </a>
             <a href="{{route('admin.usersAdmin')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        <div class="accordion-box">
            <div class="accordion">
                <div class="accordion-item">
                 <a class="accordion-title" href="javascript:void(0)">
                     <i class="bx bx-filter-alt"></i>
                    Search click Here..
                 </a>
                 <div class="accordion-content" style="border:1px solid #e1000a;border-top:0;">
                     <form action="{{route('admin.usersAdmin')}}">
                        <div class="row">
                            <div class="col-md-4 mb-1">
                            <select name="role" class="form-control {{$errors->has('role')?'error':''}}">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                <option value="{{$role->id}}" {{request()->role==$role->id?'selected':''}}>{{$role->name}}</option>
                                @endforeach
                            </select>
                            </div>
                            <div class="col-md-8 mb-1">
                                <div class="input-group">
                                    <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="User Name, Email, Mobile" class="form-control {{$errors->has('search')?'error':''}}" />
                                    <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                 </div>
                </div>
            </div>
        </div>
        <br>
        {{--
        <form action="{{route('admin.usersAdmin')}}">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group mb-1">
                        <select class="form-control form-control-sm rounded-0" name="action" required="">
                            <option value="">Select Action</option>
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                            <option value="5">Remove</option>
                        </select>
                        <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                    </div>
                </div>
                <div class="col-md-4">
                    
                </div>
                <div class="col-md-4">
                    <ul class="statuslist">
                        <li><a href="{{route('admin.usersAdmin')}}">All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.usersAdmin',['status'=>'active'])}}">Active ({{$totals->active}})</a></li>
                        <li><a href="{{route('admin.usersAdmin',['status'=>'inactive'])}}">Inactive ({{$totals->inactive}})</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px; width: 100px;padding-right:0;">
                                <div class="checkbox mr-3">
                                     <input class="inp-cbx" id="checkall" type="checkbox" style="display: none;" />
                                     <label class="cbx" for="checkall">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                         All <span class="checkCounter"></span> 
                                     </label>
                                 </div>
                            </th>
                            <th style="min-width: 80px;">Image</th>
                            <th style="min-width: 250px; width: 250px;">Name</th>
                            <th style="min-width: 150px;">Email</th>
                            <th style="min-width: 100px;">Role</th>
                            <th style="min-width: 80px; width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i=>$user)
                        <tr>
                            <td>
                                @if($user->id==Auth::id()) @else
                                <div class="checkbox">
                                     <input class="inp-cbx" id="cbx_{{$user->id}}" type="checkbox" name="checkid[]" value="{{$user->id}}" style="display: none;" />
                                     <label class="cbx" for="cbx_{{$user->id}}">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                     </label>
                                 </div>
                                @endif
                                <span style="margin:0 5px;">{{$users->currentpage()==1?$i+1:$i+($users->perpage()*($users->currentpage() - 1))+1}}</span>
                                @if($user->status)
                                <span style="color: #43d39e;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                @else
                                <span style="color: #FF9800;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-analyse"></i>
                                </span>
                                @endif
                            </td>
                            <td style="padding: 0 3px; text-align: center;">
                                <span>
                                    <img src="{{asset($user->image())}}" style="max-width: 60px; max-height: 50px;" />
                                </span>
                            </td>
                            <td>
                                <a href="{{route('admin.usersAdminAction',['edit',$user->id])}}" class="invoice-action-view mr-1">{{$user->name}} </a>
                            </td>
                            <td>{{$user->email}}</td>
                            <td> 
                                @if($user->permission)
                                <span class="badge {{$user->permission->id==1?'badge-success':'badge-info'}} ">{{$user->permission->name}}</span>
                                @else
                                <span class="badge badge-danger">Un-athorize</span>
                                @endif
                            </td>
                            <td style="padding: 5px 0; text-align: center;">
                                <a href="{{route('admin.usersAdminAction',['edit',$user->id])}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
    

                                @if($user->id==Auth::id()) @else
                                <a href="{{route('admin.usersAdminAction',['delete',$user->id])}}" onclick="return confirm('Are You Want To Delete')" class="btn-custom danger">
                                    <i class="bx bx-trash"></i>
                                </a>
                                @endif 

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
        --}}
    </div>
</div>

<div class="row mb-30" style="margin:0 -10px;">
    
    @foreach($users as $i=>$user)
    <div class="col-md-3" style="padding:0 10px;">
        
        
        <div class="card adminProfile p-0">
            <div class="card-header bg-info d-flex justify-content-between align-items-center" style="margin: 0;padding: 10px;">
                 <h3 style="color:white;">{{$user->permission?ucfirst($user->permission->name):'Unauthorized'}}</h3>
                 <div class="dropdown">
                     <button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:white;">
                         <i class="bx bx-dots-horizontal-rounded"></i>
                     </button>
                     <div class="dropdown-menu">
                         <!--<a class="dropdown-item d-flex align-items-center" href="{{route('admin.usersAdminAction',['view',$user->id])}}">-->
                         <!--    <i class="bx bx-show"></i> View-->
                         <!--</a>-->
                         <a class="dropdown-item d-flex align-items-center" href="{{route('admin.usersAdminAction',['edit',$user->id])}}">
                             <i class="bx bx-edit-alt"></i> Edit
                         </a>
                        @if($user->id==Auth::id()) @else
                         <a class="dropdown-item d-flex align-items-center" href="{{route('admin.usersAdminAction',['delete',$user->id])}}" onclick="return confirm('Are You Want To Delete')">
                             <i class="bx bx-trash"></i> Delete
                         </a>
                        @endif 
                     </div>
                 </div>
            </div>
             <img src="{{asset($user->image())}}" class="card-img-top" alt="{{$user->name}}" />
             <div class="card-body p-4">
                 <h5 class="card-title font-weight-bold">{{$user->name}}</h5>
                 <div class="info">
                    <ul>
                        <li class="d-flex"><i class="bx bx-user mr-2 pt-2"></i> <span><b>Status</b><br>
                            @if($user->status)
                            <span style="color: #43d39e;font-size: 20px;line-height: 20px;">
                                <i class="bx bx-check-circle"></i>
                            </span>
                            @else
                            <span style="color: #FF9800;font-size: 20px;line-height: 20px;">
                                <i class="bx bx-analyse"></i>
                            </span>
                            @endif
                            </span>
                        </li>
                        <li class="d-flex"><i class="bx bx-mobile mr-2 pt-2"></i> <span><b>Mobile</b><br>{{$user->mobile}}</span></li>
                        <li class="d-flex"><i class="bx bx-envelope mr-2 pt-2"></i><span><b>Email</b><br>{{$user->email}}</span></li>
                    </ul>
                </div>
             </div>
         </div>
     </div>
     @endforeach
</div>
<br>
{{$users->links('pagination')}}

</div>

<!-- Modal -->
<div class="modal fade text-left" id="AddUser" tabindex="-1" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('admin.usersAdminAction','create')}}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Add Admin User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times; </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="controls">
                            <input type="text" class="form-control {{$errors->has('username')?'error':''}}" name="username" placeholder="Enter Email/Mobile" value="" required="" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>
 

 @endsection @push('js') @endpush
