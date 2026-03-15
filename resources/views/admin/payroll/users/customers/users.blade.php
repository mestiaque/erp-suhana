@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Employee Users')}}</title>
@endsection
@push('css')
<style>
@media print {
    .btn, .pagination, .no-print { display: none !important; }
    .table { font-size: 12px; }
    .table td, .table th { padding: 4px !important; }
    img.rounded-circle {
        width: 30px !important;
        height: 30px !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    div[rounded-circle] {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
@endpush
@section('contents')


@include(adminTheme().'alerts')
<div class="flex-grow-1">
<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Employee List</h3>
         <div class="dropdown">
            <a href="{{ route('admin.usersCustomerExport', request()->all()) }}" class="btn btn-sm btn-secondary mr-2" target="_blank">
                <i class="fa fa-file-excel"></i> Export to Excel
            </a>
            <a href="{{ route('admin.usersCustomerPrint', request()->all()) }}" class="btn btn-sm btn-info mr-2" target="_blank">
                <i class="fa fa-print"></i> Print
            </a>
             <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#AddUser">
                 <i class="bx bx-plus"></i>Add Employee
             </a>
         </div>
    </div>
    <div class="card-body">

        <div class="accordion-content">
            <form action="{{route('admin.usersCustomer')}}">
               <div class="row">
                   <div class="col-md-5 mb-1">
                       <div class="input-group">
                           <input type="date" name="startDate" value="{{request()->startDate?:''}}" class="form-control form-control-sm {{$errors->has('startDate')?'error':''}}" />
                           <input type="date" value="{{request()->endDate?:''}}" name="endDate" class="form-control form-control-sm {{$errors->has('endDate')?'error':''}}" />
                       </div>
                   </div>
                   <div class="col-md-2 mb-1">
                       <select class="form-control form-control-sm" name="role_id">
                           <option value="">Select Role</option>
                           @foreach($roles as $role)
                           <option value="{{$role->id}}" {{request()->role_id==$role->id?'selected':''}} >{{$role->name}}</option>
                           @endforeach
                       </select>
                   </div>
                   <div class="col-md-5 mb-1">
                       <div class="input-group">
                           <input type="text" name="search" value="{{request()->search?:''}}" placeholder="User Name, Email, Mobile" class="form-control form-control-sm {{$errors->has('search')?'error':''}}" />
                           <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                           <a href="{{route('admin.usersCustomer')}}" class="btn btn-danger btn-sm rounded-0 align-middle">Reset</a>
                       </div>
                   </div>
               </div>
           </form>
        </div>

        <form action="{{route('admin.usersCustomer')}}">
            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4">

                </div>
                <div class="col-md-4">
                    <ul class="statuslist mb-0">
                        <li><a href="{{route('admin.usersCustomer')}}" class="{{request()->status?'':'active'}}" >All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.usersCustomer',['status'=>'active'])}}" class="{{request()->status=='active'?'active':''}}" >Active ({{$totals->active}})</a></li>
                        <li><a href="{{route('admin.usersCustomer',['status'=>'inactive'])}}" class="{{request()->status=='inactive'?'active':''}}" >Inactive ({{$totals->inactive}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th style="min-width: 20px;">SL</th>
                            <th style="min-width: 50px;">Photo</th>
                            <th style="min-width: 100px;">Name</th>
                            <th style="min-width: 50px;">Employee ID</th>
                            <th style="min-width: 10px;">Designation</th>
                            <th style="min-width: 10px;">Department</th>
                            <th style="min-width: 10px;">Section</th>
                            <th style="min-width: 50px;">Line</th>
                            <th style="min-width: 100px;">Email / Mobile</th>
                            <th style="min-width: 60px;">Joining Date</th>
                            <th style="min-width: 80px;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $i=>$user)
                        <tr>
                            <td>
                                {{ $users->currentpage()==1 ? $i+1 : $i + ($users->perpage()*($users->currentpage() - 1)) + 1 }}
                            </td>

                            <td>
                                {!! $user->getAvt() !!}
                            </td>
                            <td>
                                <a href="{{route('admin.usersCustomerAction',['view',$user->id])}}" target="_blank" class="invoice-action-view mr-1"> {{ $user->name }} </a>
                            </td>

                            <td>
                                @if($user->employee_id)
                                    {{ $user->employee_id }}
                                @else
                                    <span style="color:#FF9800;">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($user->designation)
                                    {{ $user->designation->name }}
                                @else
                                    <span style="color:#FF9800;">N/A</span>
                                @endif
                            </td>

                            <td>
                                @if($user->department)
                                    {{ $user->department->name }}
                                @else
                                    <span style="color:#FF9800;">N/A</span>
                                @endif
                            </td>

                            <td>
                                @if($user->section)
                                    {{ $user->section->name }}
                                @else
                                    <span style="color:#FF9800;">N/A</span>
                                @endif
                            </td>

                            <td>
                                @if($user->line)
                                    {{ $user->line->name }}
                                @else
                                    <span style="color:#FF9800;">N/A</span>
                                @endif
                            </td>

                            <td>
                                @if($user->email || $user->mobile)
                                    {{ $user->email }} <br>{{ $user->mobile }}
                                @else
                                    <span style="color:#FF9800;">N/A</span>
                                @endif
                            </td>

                            <td>
                                @if($user->joining_date)
                                    {{ \Carbon\Carbon::parse($user->joining_date)->format('d M Y') }}
                                @else
                                    {{ $user->created_at->format('d M Y') }}
                                @endif
                            </td>

                            <td class="">
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-sm btn-custom yellow copyBtn mr-1" type="button" data-id="{{ $user->employee_id ?? $user->email }}" data-password="{{ $user->password_show }}"><i class="bx bx-copy"></i></button>
                                    <a href="{{route('admin.usersCustomerAction',['edit',$user->id])}}" class="btn-custom success mr-1">
                                        <i class="bx bx-edit"></i>
                                    </a>

                                    @if($user->id != Auth::id())
                                    <a href="{{route('admin.usersCustomerAction',['delete',$user->id])}}"
                                       onclick="return confirm('Are You Want To Delete?')"
                                       class="btn-custom danger">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                    @endif
                                </div>
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
                <label for="name">Employee Name *</label>
                <input type="text" name="name" id="name" class="form-control form-control-sm"
                        placeholder="Enter employee name" value="{{old('name')}}" required>
                @if($errors->has('name'))
                <span style="color: red;">{{$errors->first('name')}}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="employee_id">Employee ID *</label>
                <input type="text" name="employee_id" id="employee_id" class="form-control form-control-sm"
                        placeholder="Enter employee ID" value="{{old('employee_id')}}" required>
                @if($errors->has('employee_id'))
                <span style="color: red;">{{$errors->first('employee_id')}}</span>
                @endif
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
<script>
$(document).ready(function() {
    $(".copyBtn").click(function() {

        const email = $(this).data('id');
        const password = $(this).data('password');
        const loginUrl = "{{route('login')}}";
        const finalText = `id: ${email}\npassword: ${password}\nlogin: ${loginUrl}`;

        navigator.clipboard.writeText(finalText).then(() => {
            const $btn = $(this);

            // Apply active class for 0.5s
            $btn.addClass("active");

            // Optional: change text to Copied!
            $btn.attr("disabled", true).html('<i class="bx bx-check"></i>');

            setTimeout(() => {
                $btn.removeClass("active");
                $btn.attr("disabled", false).html('<i class="bx bx-copy"></i>');
            }, 500);
        });
    });
});
</script>
@endpush

