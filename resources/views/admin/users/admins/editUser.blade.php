@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('User Profile')}}</title>
@endsection

@push('css')
<style type="text/css">
    .showPassword {
    right: 0 !important;
    cursor: pointer;
    }
    .ProfileImage{
        max-width: 64px;
        max-height: 64px;
    }
</style>
@endpush
@section('contents')
<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>Profile</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item"><a href="{{route('admin.usersAdmin')}}">Admin List</a></li>
        <li class="item">Profile</li>
    </ol>
</div>
 
@include(adminTheme().'alerts')


<div class="flex-grow-1">
    <div class="row">
        <div class="col-md-7">
            <!-- Start -->
            <div class="card mb-30">
                <div class="card-header d-flex justify-content-between align-items-center">
                     <h3>Profile Edit</h3>
                </div>
                <div class="card-body">
                        <form action="{{route('admin.usersAdminAction',['update',$user->id])}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="media">
                                <a href="javascript: void(0);">
                                    <img src="{{asset($user->image())}}"  class="ProfileImage image_{{$user->id}} rounded mr-75" alt="profile image" />
                                </a>
                                <div class="media-body" style="padding: 0 10px;">
                                    <div style="display:flex;">
                                        <label class="btn btn-sm btn-primary cursor-pointer" for="account-upload" >Upload photo </label>
                                        <input type="file" name="image" id="account-upload" class="account-upload" data-imageshow="image_{{$user->id}}" hidden="" />
                                        @if($user->imageFile)
                                        <a href="{{route('admin.mediesDelete',$user->imageFile->id)}}" class="mediaDelete btn btn-sm btn-secondary" style="margin: 0 10px;height:31px;">Reset </a>
                                        @endif
                                    </div>
                                    @if ($errors->has('image'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('image') }}</p>
                                    @endif
                                    <p class="text-muted"><small>Allowed JPG, GIF or PNG. Max size of 2048kB</small></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="name">Name* </label>
                                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" value="{{$user->name?:old('name')}}" required="" />
                                    @if ($errors->has('name'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="email">Email* </label>
                                    <input type="email" class="form-control {{$errors->has('email')?'error':''}}" name="email" placeholder="Enter Email" value="{{$user->email?:old('email')}}" required="" />
                                    @if ($errors->has('email'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email') }}</p>
                                    @endif
                                </div>
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="mobile">Mobile* </label>
                                    <input type="text" class="form-control {{$errors->has('mobile')?'error':''}}" name="mobile" placeholder="Enter Mobile" value="{{$user->mobile?:old('mobile')}}" />
                                    @if ($errors->has('mobile'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mobile') }}</p>
                                    @endif
                                </div>
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="gender">Gender </label>
                                    <select class="form-control {{$errors->has('gender')?'error':''}}" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{$user->gender=='Male'?'selected':''}}>Male</option>
                                        <option value="Female" {{$user->gender=='Female'?'selected':''}}>Female</option>
                                    </select>
                                    @if ($errors->has('gender'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('gender') }}</p>
                                    @endif
                                </div>
                                
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="division">Division </label>
                                    <select id="division" class="form-control {{$errors->has('division')?'error':''}}" name="division">
                                        <option value="">Select Division</option>

                                        @foreach(App\Models\Country::where('type',2)->where('parent_id',1)->get() as $data)
                                        <option value="{{$data->id}}" {{$data->id==$user->division?'selected':''}}>{{$data->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="district">District </label>
                                    <select id="district" class="form-control {{$errors->has('district')?'error':''}}" name="district">
                                        @if($user->division==null)
                                        <option value="">No District</option>
                                        @else
                                        <option value="">Select District</option>
                                        @foreach(App\Models\Country::where('type',3)->where('parent_id',$user->division)->get() as $data)
                                        <option value="{{$data->id}}" {{$data->id==$user->district?'selected':''}}>{{$data->name}}</option>
                                        @endforeach @endif
                                    </select>
                                </div>
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="city">City </label>
                                    <select id="city" class="form-control {{$errors->has('city')?'error':''}}" name="city">
                                        @if($user->district==null)
                                        <option value="">No City</option>
                                        @else
                                        <option value="">Select City</option>
                                        @foreach(App\Models\Country::where('type',4)->where('parent_id',$user->district)->get() as $data)
                                        <option value="{{$data->id}}" {{$data->id==$user->city?'selected':''}}>{{$data->name}}</option>
                                        @endforeach @endif
                                    </select>
                                </div>
                                
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="postal_code">Postal Code</label>
                                    <input type="text" class="form-control {{$errors->has('postal_code')?'error':''}}" name="postal_code" placeholder="Enter Postal Code" value="{{$user->postal_code?:old('postal_code')}}" />
                                </div>
                                <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                    <div class="controls">
                                        <label for="address">Address Line</label>
                                        <input type="text" class="form-control {{$errors->has('address')?'error':''}}" name="address" placeholder="Enter Address" value="{{$user->address_line1?:old('address')}}" />
                                    </div>
                                </div>
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="status">User Status</label>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="status" id="status" {{$user->status?'checked':''}}/>
                                        <label class="custom-control-label" for="status">User Active</label>
                                    </div>
                                </div>

                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label>User Role</label>
                                    <select name="role" class="form-control {{$errors->has('role')?'error':''}}">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                        <option value="{{$role->id}}" {{$user->permission_id==$role->id?'selected':''}}>{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('role'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('role') }}</p>
                                    @endif
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-md rounded-0">Save changes</button>
                        </form>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card mb-30">
                <div class="card-header d-flex justify-content-between align-items-center">
                     <h3>Change Password</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.usersAdminAction',['change-password',$user->id])}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                    <label for="old_password">Old password </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control password" placeholder="Old Password" name="old_password" value="{{$user->password_show?:old('old_password')}}" required="" />
                                        <div class="input-group-append">
                                            <span class="input-group-text showPassword"><i class="bx bx-hide"></i></span>
                                        </div>
                                    </div>
                                    @if ($errors->has('old_password'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('old_password') }}</p>
                                    @endif
                                </div>
                                <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                    <label for="password">New Password </label>
                                    <input type="password" class="form-control password {{$errors->has('password')?'error':''}}" name="password" placeholder="New password" required="" />
                                    @if ($errors->has('password'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('password') }}</p>
                                    @endif
                                </div>
                                <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                    <label for="password_confirmation">Confirmed Password </label>
                                    <input type="password" class="form-control password {{$errors->has('password_confirmation')?'error':''}}" name="password_confirmation" placeholder="Confirmed password" required="" />
                                    @if ($errors->has('password_confirmation'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('password_confirmation') }}</p>
                                    @endif
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger btn-md rounded-0">Change Password</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection
@push('js')

<script type="text/javascript">


</script>


@endpush