@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Edit Profile')}}</title>
@endsection @push('css')

<style type="text/css">
    .ProfileImage {
        max-width: 64px;
        max-height: 64px;
    }
</style>
@endpush @section('contents')

<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>My Profile</h1>

    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">Dashboard </li>
        <li class="item">Edit Profile</li>
    </ol>
</div>


@include(adminTheme().'alerts')
    <form action="{{route('admin.editProfile',['update'])}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row mb-4">
            <div class="col-md-6">

                <div class="inforGrid card basic mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Basic Information</h3>
                    </div>
                    <div class="media profileHeader">
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
                    <table class="table">
                        <tr>
                            <th>Employee ID*</th>
                            <td style="padding:0px;">
                                <input type="text" name="employee_id" class="form-control form-control-sm" value="{{$user->employee_id?:old('employee_id')}}" placeholder="Employee ID" disabled>
                                @if ($errors->has('employee_id'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('employee_id') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Employee Name*</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm" placeholder="Enter name" value="{{$user->name?:old('name')}}" name="name" required="" >
                                @if ($errors->has('name'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Name (Bangla)</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm" placeholder="Enter name (Bangla)" value="{{$user->bn_name?:old('bn_name')}}" name="bn_name"  >
                                @if ($errors->has('bn_name'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('bn_name') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Designation</th>
                            <td style="padding:0px;">
                                <div class="form-control form-control-sm">{{$user?->designation?->name ?? 'N/A  '}}</div>
                                @if ($errors->has('designation_id'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('designation_id') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Division</th>
                            <td style="padding:0px;">
                                <select disabled class="form-control form-control-sm {{$errors->has('division_id')?'error':''}}" name="division_id">
                                    <option value="" >Select Division</option>
                                    @foreach($divisions as $dp)
                                    <option value="{{$dp->id}}" {{$user->division==$dp->id?'selected':''}}>{{$dp->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('division_id'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('division_id') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td style="padding:0px;">
                                <select disabled class="form-control form-control-sm {{$errors->has('department_id')?'error':''}}" name="department_id">
                                    <option value="" >Select Department</option>
                                    @foreach($departments as $dp)
                                    <option value="{{$dp->id}}" {{$user->department_id==$dp->id?'selected':''}}>{{$dp->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('department_id'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('department_id') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Section</th>
                            <td style="padding:0px;">
                                <select disabled class="form-control form-control-sm {{$errors->has('section_id')?'error':''}}" name="section_id">
                                    <option value="" >Select Section</option>
                                    @foreach($sections as $dp)
                                    <option value="{{$dp->id}}" {{$user->section_id==$dp->id?'selected':''}}>{{$dp->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('section_id'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('section_id') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Line Number</th>
                            <td style="padding:0px;">
                                <select disabled class="form-control form-control-sm {{$errors->has('line_number')?'error':''}}" name="line_number">
                                    <option value="" >Select Line Number</option>
                                    @foreach($lines as $dp)
                                    <option value="{{$dp->id}}" {{$user->line_number==$dp->id?'selected':''}}>{{$dp->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('line_number'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('line_number') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>

                            <th>Shift</th>
                            <td style="padding:0px;">
                                <select disabled class="form-control form-control-sm {{$errors->has('shift_id')?'error':''}}" name="shift_id">
                                    <option value="" >Select Shift</option>
                                    @foreach($shifts as $dp)

                                    <option value="{{$dp->id}}" {{$user->shift_id==$dp->id?'selected':''}}>{{$dp->name_of_shift}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('shift_id'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('shift_id') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Employee Type</th>
                            <td style="padding:0px;">
                                <select disabled class="form-control form-control-sm {{$errors->has('employee_type')?'error':''}}" name="employee_type">
                                    <option value="" >Select Shift</option>
                                    @foreach($emp_types as $dp)
                                    <option value="{{$dp->id}}" {{$user->employee_type==$dp->id?'selected':''}}>{{$dp->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('employee_type'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('employee_type') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Placement/Location</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm" placeholder="Enter location" value="{{$user->location?:old('location')}}" name="location" >
                                @if ($errors->has('location'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('location') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Joining Date</th>
                            <td style="padding:0px;">
                                <input disabled type="date" name="created_at" value="{{$user->created_at?$user->created_at->format('Y-m-d'):old('created_at')}}" class="form-control form-control-sm {{$errors->has('created_at')?'error':''}}">
                                @if ($errors->has('created_at'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Date Of Birth</th>
                            <td style="padding:0px;">
                                <input type="date" name="date_of_birth" value="{{$user->dob?Carbon\Carbon::parse($user->dob)->format('Y-m-d'):''}}" class="form-control form-control-sm {{$errors->has('date_of_birth')?'error':''}}">
                                @if ($errors->has('date_of_birth'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('date_of_birth') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Gender</th>
                            <td style="padding:0px;">
                                <select class="form-control form-control-sm {{$errors->has('gender')?'error':''}}" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{$user->gender=='Male'?'selected':''}}>Male</option>
                                    <option value="Female" {{$user->gender=='Female'?'selected':''}}>Female</option>
                                </select>
                                @if ($errors->has('gender'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('gender') }}</p>
                                @endif
                            </td>
                        </tr>
                         <tr>
                            <th>Report To</th>
                            <td style="padding:0px;">
                                <input type="text" name="report_to" value="{{$user->report_to}}" class="form-control form-control-sm {{$errors->has('report_to')?'error':''}}" placeholder="Report To Name">
                                @if ($errors->has('report_to'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('report_to') }}</p>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="inforGrid card contactInfo">
                    <div class="card-header d-flex justify-content-between align-items-center mb-0">
                        <h3>Contact Information</h3>
                    </div>
                    <table class="table">
                        <tr>
                            <th>Home District</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->home_district ?? old('home_district') }}"
                                        name="home_district">
                                @if ($errors->has('home_district'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('home_district') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Nationality</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->nationality ?? old('nationality') }}"
                                        name="nationality">
                                @if ($errors->has('nationality'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('nationality') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Emergency Mobile</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->emergency_mobile ?? old('emergency_mobile') }}"
                                        name="emergency_mobile">
                                @if ($errors->has('emergency_mobile'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('emergency_mobile') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Emergency Contact Relation</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->emergency_relation ?? old('emergency_relation') }}"
                                        name="emergency_relation">
                                @if ($errors->has('emergency_relation'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('emergency_relation') }}</p>
                                @endif
                            </td>
                        </tr>



                        <tr>
                            <th>Other Information</th>
                            <td style="padding:0px;">
                                <textarea class="form-control form-control-sm form-control form-control-sm-sm" name="other_information">{{ $user->other_information ?? old('other_information') }}</textarea>
                                @if ($errors->has('other_information'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('other_information') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Reference - 1</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->reference_1 ?? old('reference_1') }}"
                                        name="reference_1">
                                @if ($errors->has('reference_1'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('reference_1') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Reference - 2</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->reference_2 ?? old('reference_2') }}"
                                        name="reference_2">
                                @if ($errors->has('reference_2'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('reference_2') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Nominee Information</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->nominee ?? old('nominee') }}"
                                        name="nominee">
                                @if ($errors->has('nominee'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('nominee') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Nominee Information (Bangla)</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->nominee_bn ?? old('nominee_bn') }}"
                                        name="nominee_bn">
                                @if ($errors->has('nominee_bn'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('nominee_bn') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Relation</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->nominee_relation ?? old('nominee_relation') }}"
                                        name="nominee_relation">
                                @if ($errors->has('nominee_relation'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('nominee_relation') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Age</th>
                            <td style="padding:0px;">
                                <input type="number" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->nominee_age ?? old('nominee_age') }}"
                                        name="nominee_age">
                                @if ($errors->has('nominee_age'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('nominee_age') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Present Address</th>
                            <td style="padding:0px;">
                                <textarea class="form-control form-control-sm form-control form-control-sm-sm" name="present_address">{{ $user->present_address ?? old('present_address') }}</textarea>
                                @if ($errors->has('present_address'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('present_address') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Present Address (Bangla)</th>
                            <td style="padding:0px;">
                                <textarea class="form-control form-control-sm form-control form-control-sm-sm" name="present_address_bn">{{ $user->present_address_bn ?? old('present_address_bn') }}</textarea>
                                @if ($errors->has('present_address_bn'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('present_address_bn') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Permanent Address</th>
                            <td style="padding:0px;">
                                <textarea class="form-control form-control-sm form-control form-control-sm-sm" name="permanent_address">{{ $user->permanent_address ?? old('permanent_address') }}</textarea>
                                @if ($errors->has('permanent_address'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('permanent_address') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Permanent Address (Bangla)</th>
                            <td style="padding:0px;">
                                <textarea class="form-control form-control-sm form-control form-control-sm-sm" name="permanent_address_bn">{{ $user->permanent_address_bn ?? old('permanent_address_bn') }}</textarea>
                                @if ($errors->has('permanent_address_bn'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('permanent_address_bn') }}</p>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
            <div class="col-md-6">
                <div class="inforGrid card personal mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center mb-0">
                        <h3>Personal Information</h3>
                    </div>
                    <table class="table">
                        <tr>
                            <th>Father Name*</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        placeholder="Enter father name"
                                        value="{{ $user->father_name ?? old('father_name') }}"
                                        name="father_name">
                                @if ($errors->has('father_name'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('father_name') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Father Name (Bangla)</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        placeholder="বাংলায় পিতার নাম"
                                        value="{{ $user->father_name_bn ?? old('father_name_bn') }}"
                                        name="father_name_bn">
                                @if ($errors->has('father_name_bn'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('father_name_bn') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Mother Name</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        placeholder="Enter mother name"
                                        value="{{ $user->mother_name ?? old('mother_name') }}"
                                        name="mother_name">
                                @if ($errors->has('mother_name'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('mother_name') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Mother Name (Bangla)</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        placeholder="বাংলায় মাতার নাম"
                                        value="{{ $user->mother_name_bn ?? old('mother_name_bn') }}"
                                        name="mother_name_bn">
                                @if ($errors->has('mother_name_bn'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('mother_name_bn') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Marital Status</th>
                            <td style="padding:0px;">
                                <select disabled name="marital_status" class="form-control form-control-sm form-control form-control-sm-sm">
                                    <option value="">Select</option>
                                    <option value="single" {{ old('marital_status', $user->marital_status ?? '')=='single'?'selected':'' }}>Single</option>
                                    <option value="married" {{ old('marital_status', $user->marital_status ?? '')=='married'?'selected':'' }}>Married</option>
                                </select>
                                @if ($errors->has('marital_status'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('marital_status') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Spouse Name</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->spouse_name ?? old('spouse_name') }}"
                                        name="spouse_name">
                                @if ($errors->has('spouse_name'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('spouse_name') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Spouse Name (Bangla)</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->spouse_name_bn ?? old('spouse_name_bn') }}"
                                        name="spouse_name_bn">
                                @if ($errors->has('spouse_name_bn'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('spouse_name_bn') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>No of Boys</th>
                            <td style="padding:0px;">
                                <input type="number" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->boys ?? old('boys') }}"
                                        name="boys">
                                @if ($errors->has('boys'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('boys') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>No of Girls</th>
                            <td style="padding:0px;">
                                <input type="number" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->girls ?? old('girls') }}"
                                        name="girls">
                                @if ($errors->has('girls'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('girls') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Blood Group</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->blood_group ?? old('blood_group') }}"
                                        name="blood_group">
                                @if ($errors->has('blood_group'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('blood_group') }}</p>
                                @endif
                            </td>
                        </tr>


                        <tr>
                            <th>Religion</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->religion ?? old('religion') }}"
                                        name="religion">
                                @if ($errors->has('religion'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('religion') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Mobile Number*</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->mobile ?? old('mobile') }}"
                                        name="mobile" required="">
                                @if ($errors->has('mobile'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('mobile') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Email Address</th>
                            <td style="padding:0px;">
                                <input type="email" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->email ?? old('email') }}"
                                        name="email">
                                @if ($errors->has('email'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('email') }}</p>
                                @endif
                            </td>
                        </tr>



                        <tr>
                            <th>Education</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->education ?? old('education') }}"
                                        name="education">
                                @if ($errors->has('education'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('education') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Type of Work</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->work_type ?? old('work_type') }}"
                                        name="work_type">
                                @if ($errors->has('work_type'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('work_type') }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>National ID Card</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->nid_number ?? old('nid_number') }}"
                                        name="nid_number">
                                @if ($errors->has('nid_number'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('nid_number') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Birth Registration No</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->birth_registration ?? old('birth_registration') }}"
                                        name="birth_registration">
                                @if ($errors->has('birth_registration'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('birth_registration') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Passport No</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->passport_no ?? old('passport_no') }}"
                                        name="passport_no">
                                @if ($errors->has('passport_no'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('passport_no') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Driving License</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->driving_license ?? old('driving_license') }}"
                                        name="driving_license">
                                @if ($errors->has('driving_license'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('driving_license') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>e-TIN</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->etin ?? old('etin') }}"
                                        name="etin">
                                @if ($errors->has('etin'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('etin') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Distinguished Mark</th>
                            <td style="padding:0px;">
                                <input type="text" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->distinguished_mark ?? old('distinguished_mark') }}"
                                        name="distinguished_mark">
                                @if ($errors->has('distinguished_mark'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('distinguished_mark') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Height (Cm)</th>
                            <td style="padding:0px;">
                                <input type="number" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->height ?? old('height') }}"
                                        name="height">
                                @if ($errors->has('height'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('height') }}</p>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Weight (KG)</th>
                            <td style="padding:0px;">
                                <input type="number" class="form-control form-control-sm form-control form-control-sm-sm"
                                        value="{{ $user->weight ?? old('weight') }}"
                                        name="weight">
                                @if ($errors->has('weight'))
                                    <p style="color:red;margin:0;font-size:10px;">{{ $errors->first('weight') }}</p>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>


                <div class="inforGrid card changePassword">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Change Password</h3>
                    </div>
                    <div class="card-body">
                        <input type="hidden" value="change-password" name="actionType">
                        <div class="row">
                            <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                <label for="old_password">Old password </label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-sm password" placeholder="Old Password" name="old_password" value="{{ $user->password_show }}" required="" />
                                    <div class="input-group-append">
                                        <span class="input-group-text showPassword"><i class='bx bx-hide'></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                <label for="password">New Password </label>
                                <input type="password" class="form-control form-control-sm password {{$errors->has('password')?'error':''}}" name="password" placeholder="New password"  />
                                @if ($errors->has('password'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('password') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                <label for="password_confirmation">Confirmed Password </label>
                                <input type="password" class="form-control form-control-sm password {{$errors->has('password_confirmation')?'error':''}}" name="password_confirmation" placeholder="Confirmed password"  />
                                @if ($errors->has('password_confirmation'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('password_confirmation') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <button type="submit" class="btn btn-danger float-right">Save Changes</button>
                        {{-- <button type="submit" class="btn btn-danger">Save Changes</button> --}}
                    </div>
                </div>


            </div>
        </div>
    </form>


    <div class="row">
        <div class="col-md-12">
                <div class="inforGrid card documentInfo">
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
                                        <a href="javascript:void(0)" class="btn-custom success AddFile" data-url="{{route('admin.usersCustomerAction',['user-document',$user->id,'file_action'=>'addfile'])}}"><i class="bx bx-plus"></i> Add</a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="fileAttachment">
                                @include(adminTheme().'users.customers.includes.userFiles')
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
    </div>



@endsection

@push('css')
<style>
    .table td, .table th {
        padding: .25rem;
        vertical-align: middle !important;
        border-top: 1px solid #dee2e6;
    }
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
        let gradeData = {};

        function updateSalaries() {
            let gross = parseFloat($('input[name="gross_salary"]').val());
            if (isNaN(gross) || !gradeData.basic_salary) return;

            // ১. ফিক্সড ভ্যালুগুলো আগে বের করে নিচ্ছি (MTF + ATS)
            let medical = parseFloat(gradeData.medical_allowance || 0);
            let transport = parseFloat(gradeData.transport_allowance || 0);
            let food = parseFloat(gradeData.food_allowance || 0);

            let attendance = parseFloat(gradeData.attendance_bonus || 0);
            let other = parseFloat(gradeData.other_allowance || 0);
            let stamp = parseFloat(gradeData.stamp_charge || 0);

            let MTF = medical + transport + food; // ২টো যোগফল
            let ATS = attendance + other + stamp;

            // ২. অবশিষ্ট টাকা বের করা (Gross - (MTF + ATS))
            let remainder = gross - (MTF + ATS);

            // ৩. পার্সেন্টেজ অনুযায়ী Basic এবং House Rent ক্যালকুলেশন
            let basicSalary = (remainder * parseFloat(gradeData.basic_salary) / 100);
            let houseRent = (remainder * parseFloat(gradeData.house_rent) / 100);

            // ৪. ফিল্ডগুলোতে ভ্যালু বসানো
            $('input[data-key="basic_salary"]').val(basicSalary.toFixed(2));
            $('input[data-key="house_rent"]').val(houseRent.toFixed(2));

            // ফিক্সড অ্যামাউন্টগুলো সরাসরি বসবে
            $('input[data-key="medical_allowance"]').val(medical.toFixed(2));
            $('input[data-key="transport_allowance"]').val(transport.toFixed(2));
            $('input[data-key="food_allowance"]').val(food.toFixed(2));
            $('input[data-key="attendance_bonus"]').val(attendance.toFixed(2));
            $('input[data-key="other_allowance"]').val(other.toFixed(2));
            $('input[data-key="stamp_charge"]').val(stamp.toFixed(2));
        }

        // Grade change
        $('select[name="grade_lavel"]').on('change', function() {
            let selected = $(this).find('option:selected');
            let jsonStr = selected.data('des');

            if (jsonStr) {
                try {
                    // ডাটা যতক্ষণ স্ট্রিং থাকবে ততক্ষণ পার্স করবে
                    let data = jsonStr;
                    while (typeof data === 'string') {
                        data = JSON.parse(data);
                    }

                    // যদি ডাটাটি একটি অ্যারের ভেতরে থাকে, তবে প্রথমটি নিবে
                    gradeData = Array.isArray(data) ? data[0] : data;

                    console.log("Type of gradeData:", typeof gradeData); // এটি 'object' হওয়া উচিত
                    updateSalaries();
                } catch(e) {
                    console.error("JSON parsing error:", e);
                }
            }
        });


        // Gross salary change
        $('input[name="gross_salary"]').on('input', function() {
            updateSalaries();
        });

        // Auto run on load
        $('select[name="grade_lavel"]').trigger('change');
    });
</script>


<script>
$(document).ready(function() {
    $("#copyBtn").click(function() {
        const email = $("#email").text();
        const password = $("#password").text();
        const finalText = `email: ${email}\npassword: ${password}`;

        navigator.clipboard.writeText(finalText).then(() => {
            const $btn = $(this);

            // Apply active class for 0.5s
            $btn.addClass("active");

            // Optional: change text to Copied!
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
