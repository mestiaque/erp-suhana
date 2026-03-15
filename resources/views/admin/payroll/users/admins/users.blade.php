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
                 <div class="accordion-content" >
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
                         <a class="dropdown-item d-flex align-items-center" href="{{route('admin.usersAdminAction',['edit',$user->id])}}">
                             <i class="bx bx-edit-alt"></i> Edit
                         </a>
                         <a class="dropdown-item d-flex align-items-center copyBtn" href="javascript:void(0)" data-id="{{ $user->employee_id ?? $user->email }}" data-password="{{ $user->password_show }}">
                             <i class="bx bx-key"></i> Login Info
                         </a>

                        {{-- @if($user->id==Auth::id()) @else
                         <a class="dropdown-item d-flex align-items-center" href="{{route('admin.usersAdminAction',['delete',$user->id])}}" onclick="return confirm('Are You Want To Delete')">
                             <i class="bx bx-trash"></i> Delete
                         </a>
                        @endif --}}
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




 <div class="modal fade text-left" id="AddUser" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 	<form action="{{route('admin.usersAdminAction','create')}}" method="post">
	   		@csrf
	   <div class="modal-header">
		 <h4 class="modal-title">Add Admin</h4>
		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		   <span aria-hidden="true">&times; </span>
		 </button>
	   </div>
	   <div class="modal-body">
            <div class="form-group">
                <label for="name">Admin Name *</label>
                <input type="text" name="name" id="name" class="form-control form-control-sm"
                        placeholder="Enter admin name" value="{{old('name')}}" required>
                @if($errors->has('name'))
                <span style="color: red;">{{$errors->first('name')}}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" name="email" id="email" class="form-control form-control-sm"
                        placeholder="Enter email" value="{{old('email')}}" required>
                @if($errors->has('email'))
                <span style="color: red;">{{$errors->first('email')}}</span>
                @endif
            </div>
            <div class="form-group">
                <table class="">
                    <tbody>
                        <tr>
                            <td style="width: 25px;">
                                <input type="checkbox" name="is_employee" id="is_employee" class="form-control " value="1" {{ old('is_employee') ? 'checked' : '' }}>
                            </td>
                            <td style="vertical-align: middle !important">
                                <label for="is_employee" class="mb-0 ml-2">Mark as Employee</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
	   </div>
	   <div class="modal-footer">
		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
		 <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Admin</button>
	   </div>
	   </form>
	 </div>
   </div>
 </div>


 @endsection


@push('js')
<script>
$(document).ready(function() {
    $(".copyBtn").click(function() {
        console.log('clicked');
        const email = $(this).data('id');
        const password = $(this).data('password');
        const loginUrl = "{{route('login')}}";
        const finalText = `id: ${email}\npassword: ${password}\nlogin: ${loginUrl}`;

        navigator.clipboard.writeText(finalText).then(() => {
            const $btn = $(this);

            // Apply active class for 0.5s
            $btn.addClass("active");

        });
    });
});
</script>
@endpush
