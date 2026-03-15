@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('User Profile')}}</title>
@endsection

@push('css')
<style>
    .fileLoader {
        position: relative;
    }
    .loader {
        position: absolute;
        width: 100%;
        height: 100%;
        text-align: center;
        background: #f6f6f66b;
        z-index: 9;
        display: none;
    }
    .loader img {
        max-height: 100px;
        margin: 15px 0;
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
        <li class="item"><a href="{{route('admin.engineers')}}">Engineers List</a></li>
        <li class="item">Profile</li>
    </ol>
</div>
 
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="row">
        <div class="col-md-12">
            
            <!-- Start -->
            <div class="card mb-30">
                <div class="card-header d-flex justify-content-between align-items-center">
                     <h3>Profile Edit</h3>
                     <!--<a href="{{route('admin.engineersAction',['view',$user->id])}}" target="_blank" class="btn-custom yellow"><i class="bx bx-show"></i> View</a>-->
                </div>
                <div class="card-body">
                    <form action="{{route('admin.engineersAction',['update',$user->id])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="media">
                            <a href="javascript: void(0);">
                                <img src="{{asset($user->image())}}"  class="ProfileImage image_{{$user->id}} rounded mr-75" style="max-height: 100px;" alt="profile image" />
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
                        <h5 style="color:#ff9800">Personal Information</h5>
                        <hr>
                        <div class="row">
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
                                <label for="name">Name* </label>
                                <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" value="{{$user->name?:old('name')}}" required="" />
                                @if ($errors->has('name'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
                                <label for="email">Email* </label>
                                <input type="email" class="form-control {{$errors->has('email')?'error':''}}" name="email" placeholder="Enter Email" value="{{$user->email?:old('email')}}" required="" />
                                @if ($errors->has('email'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
                                <label for="mobile">Mobile* </label>
                                <input type="text" class="form-control {{$errors->has('mobile')?'error':''}}" name="mobile" placeholder="Enter Mobile" value="{{$user->mobile?:old('mobile')}}" required="" />
                                @if ($errors->has('mobile'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mobile') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
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
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
                                <label for="gender">Date Of Birth </label>
                                <input type="date" name="date_of_birth" value="{{$user->dob?Carbon\Carbon::parse($user->dob)->format('Y-m-d'):''}}" class="form-control {{$errors->has('date_of_birth')?'error':''}}">
                                @if ($errors->has('date_of_birth'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('date_of_birth') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
                                <label for="gender">Marital Status </label>
                                <select class="form-control {{$errors->has('marital_status')?'error':''}}" name="marital_status">
                                    <option value="Single" {{$user->marital_status=='Single'?'selected':''}}>Single</option>
                                    <option value="Married" {{$user->marital_status=='Married'?'selected':''}}>Married</option>
                                </select>
                                @if ($errors->has('marital_status'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('marital_status') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                <label for="address">About Profile</label>
                                <textarea class="form-control {{$errors->has('profile')?'error':''}}" name="profile" placeholder="Write About" >{{$user->profile?:old('address')}}</textarea>
                                @if ($errors->has('profile'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('profile') }}</p>
                                @endif
                            </div>
                        </div>
                        <h5 style="color:#e91e63">Address Information</h5>
                        <hr>
                        <div class="row">
                            <div class="form-group col-xl-3 col-lg-3 col-md-12">
                                <label for="division">Division </label>
                                <select id="division" class="form-control {{$errors->has('division')?'error':''}}" name="division">
                                    <option value="">Select Division</option>

                                    @foreach(App\Models\Country::where('type',2)->where('parent_id',1)->get() as $data)
                                    <option value="{{$data->id}}" {{$data->id==$user->division?'selected':''}}>{{$data->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-3 col-lg-3 col-md-12">
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
                            <div class="form-group col-xl-3 col-lg-3 col-md-12">
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
                            
                            <div class="form-group col-xl-3 col-lg-3 col-md-12">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" class="form-control {{$errors->has('postal_code')?'error':''}}" name="postal_code" placeholder="Enter Postal Code" value="{{$user->postal_code?:old('postal_code')}}" />
                                @if ($errors->has('postal_code'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('postal_code') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                <label for="address">Address Line</label>
                                <input type="text" class="form-control {{$errors->has('address')?'error':''}}" name="address" placeholder="Enter Address" value="{{$user->address_line1?:old('address')}}" />
                                @if ($errors->has('address'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('address') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                <label for="present_address">Present Address</label>
                                <input type="text" class="form-control {{$errors->has('present_address')?'error':''}}" name="present_address" placeholder="Enter Address" value="{{$user->address_line2?:old('present_address')}}" />
                                @if ($errors->has('present_address'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('present_address') }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <h5 style="color:#009688">Job Information</h5>
                        <hr>
                        <div class="row">
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
                                <label for="designation">Designation</label>
                                <select class="form-control {{$errors->has('designation')?'error':''}}" name="designation">
                                    <option value="" >Select Designation</option>
                                    @foreach($designations as $dp)
                                    <option value="{{$dp->id}}" {{$user->designation_id==$dp->id?'selected':''}}>{{$dp->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('designation'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('designation') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
                                <label for="department">Department</label>
                                <select class="form-control {{$errors->has('department')?'error':''}}" name="department">
                                    <option value="" >Select Department</option>
                                    @foreach($departments as $dp)
                                    <option value="{{$dp->id}}" {{$user->department_id==$dp->id?'selected':''}}>{{$dp->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('department'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('department') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
                                <label for="employee_id">Employee ID</label>
                                <input type="text" name="employee_id" class="form-control" value="{{$user->employee_id?:old('employee_id')}}" placeholder="Employee ID">
                                @if ($errors->has('employee_id'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('employee_id') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
                                <label for="employment_status">Employment Type*</label>
                                <select class="form-control {{$errors->has('employment_status')?'error':''}}" name="employment_status" required="">
                                    <option value="Probationary Employee" {{$user->employment_status=='Probationary Employee'?'selected':''}}>Probationary Employee</option>
                                    <option value="Permanent Employee" {{$user->employment_status=='Permanent Employee'?'selected':''}}>Permanent Employee</option>
                                    <option value="Contractual Employee" {{$user->employment_status=='Contractual Employee'?'selected':''}}>Contractual Employee</option>
                                </select>
                                @if ($errors->has('employment_status'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('employment_status') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
                                <label for="salary_type">Salary Type</label>
                                <select class="form-control {{$errors->has('salary_type')?'error':''}}" name="salary_type">
                                    <option value="Monthly" {{$user->salary_type=='Monthly'?'selected':''}}>Monthly</option>
                                    <option value="Hourly" {{$user->salary_type=='Hourly'?'selected':''}}>Hourly</option>
                                    <option value="Contract Based" {{$user->salary_type=='Contract Based'?'selected':''}}>Contract Based</option>
                                </select>
                                @if ($errors->has('salary_type'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('salary_type') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
                                <label for="salary_amount">Salary Rate/Amount</label>
                                <input type="number" class="form-control" name="salary_amount" value="{{$user->salary_amount?:old('salary_amount')}}" placeholder="Rate/Amount">
                                @if ($errors->has('salary_amount'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('salary_amount') }}</p>
                                @endif
                            </div>
                            <!--<div class="col-xl-3 col-lg-3 col-md-12">-->
                            <!--    <div class="form-group">-->
                            <!--        <label for="login_status">Login Allow</label>-->
                            <!--        <div class="custom-control custom-checkbox">-->
                            <!--            <input type="checkbox" class="custom-control-input" name="login_status" id="login_status" {{$user->login_status?'checked':''}}/>-->
                            <!--            <label class="custom-control-label" for="login_status">User Active</label>-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="col-xl-3 col-lg-3 col-md-12">
                                <div class="form-group">
                                    <label for="status">User Status</label>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="status" id="status" {{$user->status?'checked':''}}/>
                                        <label class="custom-control-label" for="status">User Active</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-12">
                                <div class="form-group">
                                    <label for="created_at">Joining Date</label>
                                    <input type="date" name="created_at" value="{{$user->created_at?$user->created_at->format('Y-m-d'):old('created_at')}}" class="form-control {{$errors->has('created_at')?'error':''}}">
                                    @if ($errors->has('created_at'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-12">
                                <div class="form-group">
                                    <label for="exited_at">Exit Date</label>
                                    <input type="date" name="exited_at" value="{{$user->exited_at?Carbon\Carbon::parse($user->exited_at)->format('Y-m-d'):old('exited_at')}}" class="form-control {{$errors->has('exited_at')?'error':''}}">
                                    @if ($errors->has('exited_at'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('exited_at') }}</p>
                                    @endif
                                </div>
                            </div>
                            @if(Auth::user()->permission_id==1)
                            <div class="col-xl-3 col-lg-3 col-md-12">
                                <div class="form-group">
                                    <label>User Role</label>
                                    <select name="role" class="form-control {{$errors->has('role')?'error':''}}"
                                    {{Auth::id()==$user->id?'disabled':''}}
                                    >
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
                            @endif
                            <div class="form-group col-xl-4 col-lg-4 col-md-12">
                                <label for="password" style="font-weight:bold;color: #e1000a;">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control password" placeholder="Enter Password" name="password" value="{{$user->password_show?:old('password')}}" required="" style="border: 1px solid #e1000a;" />
                                    <div class="input-group-append">
                                        <span class="input-group-text showPassword" style="background: #e1000a;border-color: #e1000a;color: white;"><i class="bx bx-hide"></i></span>
                                    </div>
                                </div>
                                @if ($errors->has('password'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('password') }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-md rounded-0">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
@push('js')

<script>
    $(document).ready(function(){
        
        $(document).on('click','.AddFile',function(){
            
            if(confirm('Are You Want To Add File')){
                var url =$(this).data('url');
                $.ajax({
                  url:url,
                  dataType: 'json',
                  cache: false,
                  success : function(data){
                    $('.fileAttachment').empty().append(data.view);
                  },error: function () {
                      alert('error');
        
                    }
                });
            }
            
        });
        
        $(document).on('click','.removeData',function(){
            
            if(confirm('Are You Want To Remove Attachment Data')){
                var url =$(this).data('url');
                $.ajax({
                  url:url,
                  dataType: 'json',
                  cache: false,
                  success : function(data){
                    $('.fileAttachment').empty().append(data.view);
                  },error: function () {
                      alert('error');
        
                    }
                });
            }
            
        });
        
        $(document).on('click','.removeFile',function(){
            
            if(confirm('Are You Want To Delete File')){
                var url =$(this).data('url');
                $.ajax({
                  url:url,
                  dataType: 'json',
                  cache: false,
                  success : function(data){
                    $('.fileAttachment').empty().append(data.view);
                  },error: function () {
                      alert('error');
        
                    }
                });
            }
            
        });
        
        
        $(document).on('change','.updateFile',function(){
            var url =$(this).data('url');
            var id =$(this).data('id');
            const file = this.files[0];
            
            var allowedExtensions = /\.(jpg|jpeg|png|gif|pdf|doc|docx)$/i;
            var maxSize = 20 * 1024 * 1024;
            var status = true;
    
            if (status) {
                if (file.size > maxSize) {
                    alert('File size exceeds the maximum limit of 20MB.');
                    status = false;
                }
            }
    
            if (status) {
               if(!allowedExtensions.test(file.name)) {
                    alert('Please upload a valid Image,PDF,Docs file.');
                    status =false;
                    return false;
                }
            }
            
            if (status) {
                var formData = new FormData();
                    formData.append('file', file);
                    formData.append('file_action', 'updateFile');
                    formData.append('file_id', id);
                    $('.loader').show();
                    $.ajax({
                        url: url,
                        type: 'POST', // Use POST method for file uploads
                        data: formData,
                        processData: false,  // Don't process the data
                        contentType: false,  // Don't set content type (let jQuery handle it)
                        success: function (data) {
                            // Handle success
                             $('.fileAttachment').empty().append(data.view);
                             $('.loader').hide();
                        },
                        error: function () {
                            // Handle error
                            alert('Error');
                            $('.loader').hide();
                        }
                    });
                
                
            }
            
            
             
        });
        $(document).on('keyup','.updateData',function(){
            var url =$(this).data('url');
            var title =$(this).val();

            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              data:{title:title},
              success : function(data){
                //$('.fileAttachment').empty().append(data.view);
              },error: function () {
                  alert('error');
    
                }
            });
        });
        
    });
</script>

@endpush