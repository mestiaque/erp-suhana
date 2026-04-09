@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('User Profile Edit')}}</title>
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

    .inforGrid{
        background:#ffffff;
        border-radius:14px;
        padding:16px;
        box-shadow:0 6px 18px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    .inforGrid table tr td{
        padding:2px;
        border-top:none;
    }
    .inforGrid table tr th{
        padding:5px;
        width: 160px;
        border-top:none;
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
        <li class="item"><a href="{{route('admin.usersCustomer')}}">Employee List</a></li>
        <li class="item">Profile</li>
    </ol>
</div>

@include(adminTheme().'alerts')
<div class="flex-grow-1">
    @php
        $user = $user ?? new \App\Models\User();
        $isCreateMode = !$user || !$user->exists;
        $profileImageClass = 'image_' . ($user->id ?? 'new');
    @endphp
    <div class="row">
        <div class="col-md-12">

            <!-- Start -->
            <div class="card mb-30">
                <div class="card-header d-flex justify-content-between align-items-center">
                     <h3>{{ $isCreateMode ? 'Create Employee' : 'Profile Edit' }} <button class="btn btn-sm btn-light copyBtn ms-2" id="copyBtn" @if($isCreateMode) disabled @endif>Copy Login</button></h3>
                     <div class="d-none">
                        <p>name: <span id="username">{{ $user?->name }}</span></p>
                        <p>email: <span id="email">{{ $user?->email }}</span></p>
                        <p>password: <span id="password">{{ $user?->password_show }}</span></p>
                    </div>
                    {{-- @if(!$isCreateMode)
                        <a href="{{route('admin.usersCustomerAction',['view',$user->id])}}" class="btn-custom yellow"><i class="bx bx-show"></i> View</a>
                    @endif --}}
                </div>
                <div class="card-body">
                    @php
                        if(!$isCreateMode) {
                            $action = route('admin.usersCustomerAction',['update',$user->id]);
                        }else{
                            $action = route('admin.usersCustomerAction',['employee-create']);
                        }
                    @endphp
                    <form action="{{$action}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="media">
                            <a href="javascript: void(0);">
                                <img src="{{ asset($user->image()) }}"  class="ProfileImage {{ $profileImageClass }} rounded mr-75" style="max-height: 100px;" alt="profile image" />
                            </a>
                            <div class="media-body" style="padding: 0 10px;">
                                <div style="display:flex;">
                                    <label class="btn btn-sm btn-primary cursor-pointer" for="account-upload" >Upload photo </label>
                                    <input type="file" name="image" id="account-upload" class="account-upload" data-imageshow="{{ $profileImageClass }}" hidden="" />
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
                            <div class="col-md-6">
                                <div class="inforGrid card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h3>Basic Information</h3>
                                    </div>
                                    <table class="table">
                                        <tr>
                                            <th>Employee ID*</th>
                                            <td style="padding:2px;">
                                                <input type="text" name="employee_id" class="form-control form-control-sm  {{$errors->has('employee_id')?'error':''}}" value="{{ old('employee_id', $user->employee_id) }}" placeholder="Employee ID"  minlength="3" maxlength="50" >
                                                @if ($errors->has('employee_id'))
                                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('employee_id') }}</p>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Employee Name*</th>
                                            <td style="padding:2px;">
                                                <input type="text" class="form-control form-control-sm " placeholder="Enter name" value="{{ old('name', $user->name) }}" name="name" required="required" minlength="2" maxlength="255">
                                                @if ($errors->has('name'))
                                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Name (Bangla)</th>
                                            <td style="padding:2px;">
                                                <input type="text" class="form-control form-control-sm " placeholder="Enter name (Bangla)" value="{{$user->bn_name?:old('bn_name')}}" name="bn_name"  >
                                                @if ($errors->has('bn_name'))
                                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('bn_name') }}</p>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Mobile Number</th>
                                            <td style="padding:2px;">
                                                <input type="text" class="form-control form-control-sm "
                                                       value="{{ old('mobile', $user->mobile) }}"
                                                       name="mobile" pattern="[0-9+\-\s()]{10,20}" minlength="10" maxlength="20">
                                                @if ($errors->has('mobile'))
                                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('mobile') }}</p>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Email Address</th>
                                            <td style="padding:2px;">
                                                <input type="email" class="form-control form-control-sm "
                                                       value="{{ $user->email ?? old('email') }}"
                                                       name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                                @if ($errors->has('email'))
                                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('email') }}</p>
                                                @endif
                                            </td>
                                        </tr>
                                                                                <tr>
                                        <th>Login Password</th>
                                            <td style="padding:2px;">
                                                <div class="input-group">
                                                    <input type="password" class="form-control form-control-sm  password" placeholder="Enter Password" name="password" value="{{$user->password_show?:old('password')}}" required="required" minlength="6" style="border: 1px solid #e1000a;" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text showPassword" style="background: #e1000a;border-color: #e1000a;color: white;"><i class="bx bx-hide"></i></span>
                                                    </div>
                                                </div>
                                                @if ($errors->has('password'))
                                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('password') }}</p>
                                                @endif
                                            </td>
                                        </tr>



                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if(!$isCreateMode)
                                    <h5 style="color:#e91e63">Attach Document</h5>
                                    <hr>
                                    <div class="table-responsive fileLoader">
                                        <div class="loader">
                                        <img src="{{asset('public/medies/loading.gif')}}">
                                        </div>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 250px;width: 250px;">File <span style="font-size:14px;color: gray;">(Allow Image, Docs,PDf)</span></th>
                                                    <th style="min-width: 250px;">Title</th>
                                                    <th style="min-width: 100px;width: 100px;padding: 8px 15px">
                                                        <a href="javascript:void(0)" class="btn-custom btn-sm success AddFile" data-url="{{route('admin.usersCustomerAction',['user-document',$user->id,'file_action'=>'addfile'])}}"><i class="bx bx-plus"></i> Add</a>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="fileAttachment">
                                                @include(adminTheme().'users.customers.includes.userFiles')
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                        </div>


                        <button type="submit" class="btn btn-primary btn-md rounded-0 float-right">{{ $isCreateMode ? 'Create Employee' : 'Save changes' }}</button>
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

<script>
$(document).ready(function() {
    $("#copyBtn").click(function() {
        const name = $('#username').text();
        const email = $('#email').text();
        const password = $('#password').text();
        const appUrl = "erp.anrfashion.com";

        const finalText = `Login Info : ${name}\nUrl : ${appUrl}\nEmail : ${email}\nPassword : ${password}`;

        navigator.clipboard.writeText(finalText).then(() => {
            const $btn = $(this);
            $btn.addClass("active");
            const originalText = $btn.text();
            $btn.text("Copied!");
            setTimeout(() => {
                $btn.removeClass("active");
                $btn.text(originalText);
            }, 500);
        });
    });
});
</script>

@endpush




