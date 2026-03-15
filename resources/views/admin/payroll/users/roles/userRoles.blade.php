@extends(adminTheme().'layouts.app') @section('title')
<title> {{websiteTitle('User Roles')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>User Roles</h3>
             <div class="dropdown">
    
                 <a href="{{route('admin.userRoleAction','create')}}" class="btn-custom primary">
                     <i class="bx bx-plus"></i> Role
                 </a>
                 <a href="{{route('admin.userRoles')}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px; width: 100px;">SL</th>
                            <th style="min-width: 250px; width: 250px;">Name</th>
                            <th style="min-width: 250px;">Users</th>
                            <th style="min-width: 120px; width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $i=>$role)
                        <tr>
                            <td>{{$i+1}}</td>
                            <td>{{$role->name}}</td>
                            <td><a href="{{route('admin.usersCustomer',['role_id'=>$role->id])}}">Users ({{$role->users->count()}})</a></td>
                            <td>
                                <a href="{{route('admin.userRoleAction',['edit',$role->id])}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @if($role->id!=1)
                                <a href="{{route('admin.userRoleAction',['delete',$role->id])}}" onclick="return confirm('Are You Want To Delete')" class="btn-custom danger">
                                    <i class="bx bx-trash"></i>
                                </a>
                                @endif
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
