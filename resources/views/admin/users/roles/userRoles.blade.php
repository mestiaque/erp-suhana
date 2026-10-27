@extends(adminTheme().'layouts.app') @section('title')
<title> {{websiteTitle('User Roles')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    @include(adminTheme().'alerts')
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>User Roles</h3>
             <div class="dropdown">
                @can('roles.add')
                 <a href="{{route('admin.userRoleAction','create')}}" class="btn-custom ui-act ui-act-create" title="Role"><i class="fa-solid fa-plus"></i> Role</a>
                 @endcan
                 <a href="{{route('admin.userRoles')}}" class="btn-custom ui-act ui-act-reset" title="Reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
             </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="min-width: 100px; width: 100px;">SL</th>
                            <th style="min-width: 250px; width: 250px;">Name</th>
                            <th style="min-width: 250px;">Users</th>
                            <th style="min-width: 150px;">Permissions</th>
                            <th style="min-width: 120px; width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $i=>$role)
                        <tr>
                            <td>{{$i+1}}</td>
                            <td>
                                <span style="display:inline-block; width:16px; height:16px; border-radius:4px; margin-right:6px; vertical-align:middle; border:1px solid rgba(0,0,0,.2); background:{{ $role->color ?: 'transparent' }};" title="{{ $role->color ?: 'Default' }}"></span>
                                {{$role->name}}
                            </td>
                            <td><a href="{{route('admin.usersAdmin',['role_id'=>$role->id])}}">Users ({{$role->users->count()}})</a></td>
                            <td>Permissions (
                                @if($role->id== 999)
                                &divonx;
                                @else
                                {{count(json_decode($role->permission ?? '{}', true))}}
                                @endif
                                )</td>
                            <td class="text-center">
                                 @if(auth()->user()->hasPermission('roles.edit')  || auth()->user()->hasPermission('roles.delete'))
                                    @can('roles.edit')
                                        <a href="{{route('admin.userRoleAction',['edit',$role->id])}}" class="btn-custom ui-act ui-act-edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                    @endcan
                                    @can('roles.delete')
                                        @if($role->id!=1)
                                            <a href="{{route('admin.userRoleAction',['delete',$role->id])}}" onclick="return confirm('Are You Want To Delete')" class="btn-custom ui-act ui-act-delete" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                        @endif
                                    @endcan
                                @else -- @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$roles->links()}}
            </div>
        </div>
    </div>
</div>

@endsection @push('js') @endpush
