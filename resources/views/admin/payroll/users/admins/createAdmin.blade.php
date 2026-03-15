@extends(adminTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Create Admin')}}</title>
@endsection 
@push('css')
<style>
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
    }
    .alert-info {
        background-color: #e7f3ff;
        border-color: #b8d4e8;
        color: #0c5460;
    }
    .table-responsive {
        margin-top: 20px;
    }
</style>
@endpush 
@section('contents')


@include(adminTheme().'alerts')
<div class="flex-grow-1">
<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Create New Admin</h3>
         <div class="dropdown">
            <a href="{{route('admin.usersAdmin')}}" class="btn-custom yellow">
                <i class="bx bx-arrow-back"></i> Back to Admin List
            </a>
         </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="alert alert-info">
                    <strong><i class="bx bx-info-circle"></i> Note:</strong> 
                    The password will be auto-generated and shown after successful creation. 
                    The admin can login using their email and password.
                </div>
                
                <form action="{{route('admin.users.admin.store')}}" method="post">
                    @csrf
                    
                    <div class="form-group">
                        <label for="name">Admin Name *</label>
                        <input type="text" name="name" id="name" class="form-control" 
                               placeholder="Enter admin name" value="{{old('name')}}" required>
                        @if($errors->has('name'))
                        <span style="color: red;">{{$errors->first('name')}}</span>
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" name="email" id="email" class="form-control" 
                               placeholder="Enter email address" value="{{old('email')}}" required>
                        @if($errors->has('email'))
                        <span style="color: red;">{{$errors->first('email')}}</span>
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Create Admin
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-8">
                <h4>Recent Admins</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $admins = \App\Models\User::where('admin', true)->where('super_admin', false)->latest()->take(10)->get(); @endphp
                            @forelse($admins as $key => $admin)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>{{$admin->name}}</td>
                                <td>{{$admin->email}}</td>
                                <td>
                                    @if($admin->status == 1)
                                    <span class="badge badge-success">Active</span>
                                    @else
                                    <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No admins found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


@endsection
