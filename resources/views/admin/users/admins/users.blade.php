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

                @can('admin.add')
                <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddUser">
                    <i class="bx bx-plus"></i> User
                </a>
                @endcan
                <a href="{{route('admin.usersAdmin')}}" class="btn-custom yellow">
                    <i class="bx bx-rotate-left"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
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
            <br>

            <form action="{{route('admin.usersAdmin')}}">

                <div class="row">
                    <div class="col-md-4">
                        @if(auth()->user()->hasPermission('admin.edit')  || auth()->user()->hasPermission('admin.delete'))
                        <div class="input-group mb-1">
                            <select class="form-control form-control-sm rounded-0" name="action" required="">
                                <option value="">Select Action</option>
                                @can('admin.edit')
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>
                                @endcan
                                @can('admin.delete')
                                <option value="5">Delete</option>
                                @endcan
                            </select>
                            <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                        </div>
                        @endif
                    </div>

                    <div class="col-md-4"></div>

                    <div class="col-md-4">
                        <ul class="statuslist">
                            <li><a href="{{route('admin.usersAdmin')}}" class="{{request()->status?'':'active'}}">All ({{$totals->total}})</a></li>
                            <li><a href="{{route('admin.usersAdmin',['status'=>'active'])}}" class="{{request()->status=='active'?'active':''}}">Active ({{$totals->active}})</a></li>
                            <li><a href="{{route('admin.usersAdmin',['status'=>'inactive'])}}" class="{{request()->status=='inactive'?'active':''}}">Inactive ({{$totals->inactive}})</a></li>
                            @if($totals->deleted > 0)
                                <li><a href="{{route('admin.usersAdmin',['view'=>'deleted'])}}" class="text-danger" >Deleted ({{$totals->deleted}})</a></li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Staff Table --}}

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="min-width: 100px; width: 100px;padding-right:0; position: relative;">
                                    @if(auth()->user()->hasPermission('admin.edit')  || auth()->user()->hasPermission('admin.delete'))
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
                                    @else -- @endif
                                </th>
                                <th style="min-width: 70px; width: 70px;">Image</th>
                                <th style="min-width: 200px; width: 200px;">Name</th>
                                <th style="min-width: 100px; width: 100px;">ID Number</th>
                                <th style="min-width: 150px;">Mobile / Email</th>
                                <th style="min-width: 100px;">Designation</th>
                                <th style="min-width: 90px;">Join Date</th>
                                <th style="min-width: 80px; width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $i=>$user)
                            <tr>
                                <td style=" position: relative;">
                                    @if($user->id==Auth::id()) @else
                                    @if(auth()->user()->hasPermission('admin.edit')  || auth()->user()->hasPermission('admin.delete'))
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
                                <td style="padding: 0 3px;">
                                    <span>
                                        <img src="{{asset($user->image())}}" style="max-width: 60px; max-height: 50px;" />
                                    </span>
                                </td>
                                <td><a href="{{route('admin.usersAdminAction',['view',$user->id])}}" target="_blank" class="invoice-action-view mr-1">{{$user->name}}</a>
                                    @if($user->permission)
                                    <br><span class="badge {{$user->permission->id==1?'badge-success':'badge-info'}}">{{$user->permission->name}}</span>
                                    @endif
                                </td>
                                <td>{{ $user->employee_id ?? '--' }}</td>
                                <td>{{$user->mobile ?? $user->email ?? '--'}}</td>
                                <td>
                                    @if($user->designation)
                                    <span style="color: #009688;font-weight: bold;">{{$user->designation->name}}</span>
                                    @else
                                    <span style="color: #FF9800;">No Designation</span>
                                    @endif
                                </td>
                                <td>{{ $user?->addedby_at?->format('d.m.Y') ?? '--' }}</td>
                                <td style="padding: 8px 5px; text-align: center;">
                                     @if(auth()->user()->hasPermission('admin.edit')  || auth()->user()->hasPermission('admin.delete'))
                                        @can('admin.edit')
                                            <a href="{{route('admin.usersAdminAction',['edit',$user->id])}}" class="btn-custom success">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                        @endcan
                                        @if($user->id==Auth::id()) @else
                                            @can('admin.delete')
                                                <a href="{{route('admin.usersAdminAction',['delete',$user->id])}}" onclick="return confirm('Are You Want To Delete')" class="btn-custom danger">
                                                    <i class="bx bx-trash"></i>
                                                </a>
                                            @endcan
                                        @endif
                                    @else -- @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

        </div>
    </div>
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
                        <label for="name">Name* </label>
                        <div class="controls">
                            <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" required="">
                            @if ($errors->has('name'))
                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                            @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name">Mobile* </label>
                            <div class="controls">
                                {{-- <input type="mobile" class="form-control {{$errors->has('mobile')?'error':''}}" name="mobile" maxlength="11" oninput="this.value = this.value.slice(0, 11);" placeholder="Enter Mobile" required> --}}
                                    <input type="tel" class="form-control {{$errors->has('mobile')?'error':''}}" name="mobile" minlength="11" maxlength="11" pattern="[0-9]{11}" title="Please enter exactly 11 digits" oninput="this.value = this.value.slice(0, 11);" placeholder="Please enter exactly 11 digits with start 0" required>

                                @if ($errors->has('mobile'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mobile') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name">Email </label>
                            <div class="controls">
                                <input type="email" class="form-control {{$errors->has('email')?'error':''}}" name="email" placeholder="Enter Email">
                                @if ($errors->has('email'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email') }}</p>
                                @endif
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
