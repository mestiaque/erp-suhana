@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Customer Users')}}</title>
@endsection
@push('css')

@endpush
@section('contents')


@include(adminTheme().'alerts')
<div class="flex-grow-1">
<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Mer List</h3>
         <div class="dropdown">

         </div>
    </div>
    <div class="card-body">

        <form action="{{route('admin.usersAdmin')}}">
            <div class="row">
                <div class="col-md-8">
                     <form action="{{route('admin.usersAdmin')}}">
                        @if(request()->view == 'deleted')
                            <input type="hidden" name="view" value="deleted">
                        @endif
                        <div class="row">
                            <div class="col-md-5 mb-1">
                                <div class="input-group">
                                    <input type="date" name="startDate" value="{{request()->startDate?:''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                                    <input type="date" value="{{request()->endDate?:''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                                </div>
                            </div>
                            <div class="col-md-2 mb-1">
                                <select class="form-control" name="role_id">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{$role->id}}" {{request()->role_id==$role->id?'selected':''}} >{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 mb-1">
                                <div class="input-group">
                                    <input type="text" name="search" value="{{request()->search?:''}}" placeholder="User Name, Email, Mobile" class="form-control {{$errors->has('search')?'error':''}}" />
                                    <button type="submit" class="btn btn-sm rounded-0 ui-act ui-act-filter" title="Search"><i class="fa-solid fa-filter"></i> Search</button>
                                    <a href="{{route('admin.usersAdmin', ['view' => 'deleted'])}}" class="btn-custom ui-act ui-act-reset" style="height: auto;" title="Reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <ul class="statuslist">
                        <li><a href="{{route('admin.usersAdmin')}}" class="{{request()->status?'':'active'}}" >All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.usersAdmin',['status'=>'active'])}}" class="{{request()->status=='active'?'active':''}}" >Active ({{$totals->active}})</a></li>
                        <li><a href="{{route('admin.usersAdmin',['status'=>'inactive'])}}" class="{{request()->status=='inactive'?'active':''}}" >Inactive ({{$totals->inactive}})</a></li>
                        <li><a href="{{route('admin.usersAdmin',['view'=>'deleted'])}}" target="_blank" class="text-danger" >Deleted ({{$totals->deleted}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th style="min-width: 100px; width: 100px;padding-right:0; position: relative;">
                                All
                            </th>
                            <th style="min-width: 70px; width: 70px;">Image</th>
                            <th style="min-width: 200px; width: 200px;">Name</th>
                            <th style="min-width: 100px; width: 100px;">ID Number</th>
                            <th style="min-width: 150px;">Mobile / Email</th>
                            <th style="min-width: 90px;">Join Date</th>
                            <th style="min-width: 90px;">Deleted Time</th>
                            <th style="min-width: 90px;">Deleted By</th>
                            <th style="min-width: 80px; width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i=>$user)
                        <tr>
                            <td style=" position: relative;">

                                <span style="margin:0 5px;">{{$users->currentpage()==1?$i+1:$i+($users->perpage()*($users->currentpage() - 1))+1}}</span>
                            </td>
                            <td style="padding: 0 3px;">
                                <span>
                                    <img src="{{$user->image()}}" style="max-width: 60px; max-height: 50px;" />
                                </span>
                            </td>
                            <td><a href="{{route('admin.usersAdminAction',['view',$user->id])}}" target="_blank" class="invoice-action-view mr-1">{{$user->name}}</a>
                                @if($user->permission)
                                @if($user->permission->color)
                                <br><span class="badge text-dark" style="background:{{$user->permission->color}}">{{$user->permission->name}}</span>
                                @else
                                <br><span class="badge {{$user->permission->id==1?'badge-success':'badge-info'}}">{{$user->permission->name}}</span>
                                @endif
                                @endif
                            </td>
                            <td>{{ $user->employee_id ?? '--' }}</td>
                            <td>{{$user->mobile ?? $user->email ?? '--'}}</td>
                            <td>{{$user->created_at->format('d.m.Y')}}</td>
                            <td>{{ $user->deleted_at?->format('d.m.Y, h:i A') }}</td>

                            <td>
                                {{$user->deletedBy?->name }}</td>
                            <td style="padding: 8px 5px; text-align: center;">
                                @if(can('admin.delete'))
                                    <a href="{{ route('admin.usersAdminAction', ['restore', $user->id]) }}" class="btn-custom ui-act ui-act-restore" onclick="return confirm('Are you sure you want to restore this user?')" title="Restore"><i class="fa-solid fa-trash-arrow-up"></i></a>
                                    <a href="{{ route('admin.usersAdminAction', ['force-delete', $user->id]) }}" class="btn-custom ui-act ui-act-delete" onclick="return confirm('Are you sure you want to permanently delete this user?')" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                @else -- @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
        {{$users->links('pagination')}}
    </div>
</div>
</div>





@endsection
@push('js')
@endpush
