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
         <h3>Employee List</h3>
         <div class="dropdown">

            @can('employee.add')
             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddUser">
                 <i class="bx bx-plus"></i> Employee
             </a>
             @endcan
             <a href="{{route('admin.usersCustomer')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">

        <div class="accordionx-box">
            <div class="accordionx">
                <div class="accordionx-item">
                 <div class="accordionx-content">
                     <form action="{{route('admin.usersCustomer')}}">
                        <div class="row">
                            <div class="col-md-2 mb-1">
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
                            <div class="col-md-2 mb-1">
                                <select class="form-control" name="designation_id">
                                    <option value="">Select Designation</option>
                                    @if(isset($designations))
                                    @foreach($designations as $designation)
                                    <option value="{{$designation->id}}" {{request()->designation_id==$designation->id?'selected':''}} >{{$designation->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2 mb-1">
                                <select class="form-control" name="department_id">
                                    <option value="">Select Department</option>
                                    @if(isset($departments))
                                    @foreach($departments as $department)
                                    <option value="{{$department->id}}" {{request()->department_id==$department->id?'selected':''}} >{{$department->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2 mb-1">
                                <select class="form-control" name="shift_id">
                                    <option value="">Select Shift</option>
                                    @if(isset($shifts))
                                    @foreach($shifts as $shift)
                                    <option value="{{$shift->id}}" {{request()->shift_id==$shift->id?'selected':''}} >{{$shift->name_of_shift}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2 mb-1">
                                <select class="form-control" name="employee_type">
                                    <option value="">Select Employee Type</option>
                                    @if(isset($emp_types))
                                    @foreach($emp_types as $type)
                                    <option value="{{$type->id}}" {{request()->employee_type==$type->id?'selected':''}} >{{$type->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 mb-1">
                                <div class="input-group">
                                    <input type="text" name="search" value="{{request()->search?:''}}" placeholder="User Name, Email, Mobile, Employee ID" class="form-control {{$errors->has('search')?'error':''}}" />
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
        <form action="{{route('admin.usersCustomer')}}">
            <div class="row">
                <div class="col-md-4">
                    @if(auth()->user()->hasPermission('employee.edit')  || auth()->user()->hasPermission('employee.delete'))
                    <div class="input-group mb-1">
                        <select class="form-control form-control-sm rounded-0" name="action" required="">
                            <option value="">Select Action</option>
                            @can('employee.edit')
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                            @endcan
                            @can('employee.delete')
                            <option value="5">Delete</option>
                            @endcan
                        </select>
                        <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                    </div>
                    @endif
                </div>
                <div class="col-md-4">

                </div>
                <div class="col-md-4">
                    <ul class="statuslist">
                        <li><a href="{{route('admin.usersCustomer')}}" class="{{request()->status?'':'active'}}" >All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.usersCustomer',['status'=>'active'])}}" class="{{request()->status=='active'?'active':''}}" >Active ({{$totals->active}})</a></li>
                        <li><a href="{{route('admin.usersCustomer',['status'=>'inactive'])}}" class="{{request()->status=='inactive'?'active':''}}" >Inactive ({{$totals->inactive}})</a></li>
                        @if($totals->deleted > 0)
                        <li><a href="{{route('admin.usersCustomer',['view'=>'deleted'])}}" class="text-danger" >Deleted ({{$totals->deleted}})</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th style="min-width: 100px; width: 100px;padding-right:0; position: relative;">
                                @if(auth()->user()->hasPermission('employee.edit')  || auth()->user()->hasPermission('employee.delete'))
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
                            <th style="min-width: 100px;">Department</th>
                            <th style="min-width: 80px;">Shift</th>
                            <th style="min-width: 90px;">Join Date</th>
                            <th style="min-width: 70px;">Status</th>
                            <th style="min-width: 70px; width: 70px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i=>$user)
                        <tr>
                            <td style=" position: relative;">
                                @if($user->id==Auth::id()) @else
                                @if(auth()->user()->hasPermission('employee.edit')  || auth()->user()->hasPermission('employee.delete'))
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
                            <td><a href="{{route('admin.usersCustomerAction',['view',$user->id])}}" target="_blank" class="invoice-action-view mr-1">{{$user->name}}</a>
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
                            <td>
                                @if($user->department)
                                <span style="color: #009688;">{{$user->department->name}}</span>
                                @else
                                <span style="color: #FF9800;">--</span>
                                @endif
                            </td>
                            <td>
                                @if($user->shift)
                                <span style="color: #2196F3;">{{$user->shift->name_of_shift}}</span>
                                @else
                                <span style="color: #FF9800;">--</span>
                                @endif
                            </td>
                            <td>{{$user->created_at->format('d.m.Y')}}</td>
                            <td>
                                @if($user->status == 1)
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-warning">Inactive</span>
                                @endif
                            </td>
                            <td style="padding: 8px 5px; text-align: center;">
                                @if(can('employee.edit')  || can('employee.delete'))
                                    @can('employee.edit')
                                        <a href="{{route('admin.usersCustomerAction',['edit',$user->id])}}" class="btn-custom success">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                    @endcan
                                    @if($user->id==Auth::id()) @else
                                        @can('employee.delete')
                                            <a href="{{route('admin.usersCustomerAction',['delete',$user->id])}}" onclick="return confirm('Are You Want To Delete')" class="btn-custom danger">
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
        {{-- {{$users->links('pagination')}} --}}
        {{ $users->links('pagination::bootstrap-4') }}
    </div>
</div>
</div>


 <!-- Modal -->
 <div class="modal fade text-left" id="AddUser" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 	<form action="{{route('admin.usersCustomerAction','create')}}" method="post">
	   		@csrf
	   <div class="modal-header">
		 <h4 class="modal-title">Add Employee</h4>
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
		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
		 <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Employee</button>
	   </div>
	   </form>
	 </div>
   </div>
 </div>




@endsection
@push('js')
@endpush
