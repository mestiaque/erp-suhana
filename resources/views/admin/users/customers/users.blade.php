@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('User List')}}</title>
@endsection
@push('css')
<style>
    .table td, .table th { vertical-align: middle; }
</style>
@endpush
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">User List</h3>
        <div>
            @can('employee.add')
            <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddUser">
                <i class="bx bx-plus"></i> User
            </a>
            @endcan
            <a href="{{route('admin.usersCustomer')}}" class="btn-custom yellow ml-1">
                <i class="bx bx-rotate-left"></i>
            </a>
        </div>
    </div>
    <div class="card-body">

        {{-- Search / Filter --}}
        <form action="{{route('admin.usersCustomer')}}" class="mb-3">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="mb-0 small">Role</label>
                    <select class="form-control form-control-sm" name="role_id">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                        <option value="{{$role->id}}" {{request()->role_id == $role->id ? 'selected' : ''}}>{{$role->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="mb-0 small">Search</label>
                    <div class="d-flex">
                        <input type="text" name="search" value="{{request()->search}}" placeholder="Name, Email, Phone, Employee ID" class="form-control form-control-sm">
                        <button type="submit" class="btn btn-success btn-sm rounded-0 ml-1">Search</button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Bulk Actions + Status Tabs --}}
        <form action="{{route('admin.usersCustomer')}}">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                @if(auth()->user()->hasPermission('employee.edit') || auth()->user()->hasPermission('employee.delete'))
                <div class="d-flex">
                    <select class="form-control form-control-sm rounded-0 mr-1" name="action" required>
                        <option value="">Select Action</option>
                        @can('employee.edit')
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
                        @endcan
                        @can('employee.delete')
                        <option value="5">Delete</option>
                        @endcan
                    </select>
                    <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Confirm action?')">Go</button>
                </div>
                @else <div></div>
                @endif
                <ul class="statuslist mb-0">
                    <li><a href="{{route('admin.usersCustomer')}}" class="{{!request()->status ? 'active' : ''}}">All ({{$totals->total}})</a></li>
                    <li><a href="{{route('admin.usersCustomer',['status'=>'active'])}}" class="{{request()->status=='active'?'active':''}}">Active ({{$totals->active}})</a></li>
                    <li><a href="{{route('admin.usersCustomer',['status'=>'inactive'])}}" class="{{request()->status=='inactive'?'active':''}}">Inactive ({{$totals->inactive}})</a></li>
                    @if($totals->deleted > 0)
                    <li><a href="{{route('admin.usersCustomer',['view'=>'deleted'])}}" class="text-danger">Deleted ({{$totals->deleted}})</a></li>
                    @endif
                </ul>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm">
                    <thead>
                        <tr>
                            <th style="width:80px;">
                                @if(auth()->user()->hasPermission('employee.edit') || auth()->user()->hasPermission('employee.delete'))
                                <div class="checkbox d-inline-block mr-1">
                                    <input class="inp-cbx" id="checkall" type="checkbox" style="display:none;" />
                                    <label class="cbx mb-0" for="checkall">
                                        <span><svg width="12px" height="10px" viewBox="0 0 12 10"><polyline points="1.5 6 4.5 9 10.5 1"></polyline></svg></span>
                                    </label>
                                </div>
                                @endif
                                SL
                            </th>
                            <th style="width:60px;">Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Employee ID</th>
                            <th>Added Date</th>
                            <th>Added By</th>
                            <th style="width:80px;">Status</th>
                            <th style="width:90px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $i => $user)
                        <tr>
                            <td>
                                @if($user->id != Auth::id() && (auth()->user()->hasPermission('employee.edit') || auth()->user()->hasPermission('employee.delete')))
                                <div class="checkbox d-inline-block mr-1">
                                    <input class="inp-cbx" id="cbx_{{$user->id}}" type="checkbox" name="checkid[]" value="{{$user->id}}" style="display:none;" />
                                    <label class="cbx mb-0" for="cbx_{{$user->id}}">
                                        <span><svg width="12px" height="10px" viewBox="0 0 12 10"><polyline points="1.5 6 4.5 9 10.5 1"></polyline></svg></span>
                                    </label>
                                </div>
                                @endif
                                {{$users->firstItem() + $i}}
                            </td>
                            <td style="padding:3px 5px;">
                                <img src="{{asset($user->image())}}" style="max-width:48px;max-height:40px;border-radius:4px;" />
                            </td>
                            <td>
                                <span class="font-weight-bold">{{$user->name}}</span>
                                @if($user->permission)
                                <br><span class="badge {{$user->permission->id==1?'badge-success':'badge-info'}} badge-sm">{{$user->permission->name}}</span>
                                @endif
                            </td>
                            <td>{{$user->email ?? '--'}}</td>
                            <td>{{$user->mobile ?? '--'}}</td>
                            <td>{{$user->employee_id ?? '--'}}</td>
                            <td>{{$user->created_at ? $user->created_at->format('d.m.Y') : '--'}}</td>
                            <td>{{$user->addedBy->name ?? '--'}}</td>
                            <td>
                                @if($user->status)
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-warning">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    @can('employee.edit')
                                    <a href="{{route('admin.usersCustomerAction',['edit',$user->id])}}" class="btn-custom success mr-1" title="Edit"><i class="bx bx-edit"></i></a>
                                    @endcan
                                    @if($user->id != Auth::id())
                                    @can('employee.delete')
                                    <a href="{{route('admin.usersCustomerAction',['delete',$user->id])}}" onclick="return confirm('Delete this user?')" class="btn-custom danger" title="Delete"><i class="bx bx-trash"></i></a>
                                    @endcan
                                    @endif
                                    @can('employee.role')
                                    <a href="javascript:void(0)" class="btn-custom info ml-1 open-role-modal"
                                       data-toggle="modal" data-target="#AssignRoleModal"
                                       data-user-id="{{$user->id}}"
                                       data-user-name="{{$user->name}}"
                                       data-role-id="{{$user->permission_id}}"
                                       title="Assign Role">
                                        <i class="bx bx-shield-quarter"></i>
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

{{-- Add User Modal --}}
@can('employee.add')
<div class="modal fade" id="AddUser" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('admin.usersCustomerAction','create')}}" method="post" id="AddUserForm">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Add User</h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm {{$errors->has('name')?'is-invalid':''}}" name="name" placeholder="Enter Name" required>
                        @error('name')<div class="invalid-feedback">{{$message}}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" class="form-control form-control-sm {{$errors->has('mobile')?'is-invalid':''}}" name="mobile" id="addUserMobile" minlength="11" maxlength="11" pattern="[0-9]{11}" title="11 digits starting with 0" placeholder="01XXXXXXXXX">
                        @error('mobile')<div class="invalid-feedback">{{$message}}</div>@enderror
                    </div>
                    <div class="form-group mb-0">
                        <label>Email</label>
                        <input type="email" class="form-control form-control-sm {{$errors->has('email')?'is-invalid':''}}" name="email" id="addUserEmail" placeholder="Enter Email">
                        @error('email')<div class="invalid-feedback">{{$message}}</div>@enderror
                        <small class="text-muted">Phone বা Email যেকোনো একটা দিলেই হবে — password auto-generate হবে।</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Assign Role Modal --}}
@can('employee.role')
<div class="modal fade" id="AssignRoleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" id="assignRoleForm" action="">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Assign Role</h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">User: <strong id="assignRoleUserName">-</strong></p>
                    <div class="form-group mb-0">
                        <label>Role</label>
                        <select name="role" id="assignRoleSelect" class="form-control">
                            <option value="">No Role</option>
                            @foreach($roles as $role)
                            <option value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@endsection
@push('js')
@can('employee.role')
<script>
$(document).on('click', '.open-role-modal', function () {
    const userId   = $(this).data('user-id');
    const userName = $(this).data('user-name') || '-';
    const roleId   = $(this).data('role-id');
    const tpl      = "{{ route('admin.usersCustomerAction', ['role', '__ID__']) }}";
    $('#assignRoleUserName').text(userName);
    $('#assignRoleSelect').val(roleId || '');
    $('#assignRoleForm').attr('action', tpl.replace('__ID__', userId));
});
</script>
@endcan
<script>
$(document).on('submit', '#AddUserForm', function (e) {
    const mobile = ($('#addUserMobile').val() || '').trim();
    const email  = ($('#addUserEmail').val() || '').trim();
    if (!mobile && !email) {
        e.preventDefault();
        alert('Phone বা Email যেকোনো একটি দিতে হবে।');
    }
});
</script>
@endpush
