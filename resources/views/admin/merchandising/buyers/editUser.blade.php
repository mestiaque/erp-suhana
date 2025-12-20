@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Buyer Profile')}}</title>
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
        <li class="item"><a href="{{route('admin.buyers')}}">Buyer List</a></li>
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
                        <form action="{{route('admin.buyersAction',['update',$user->id])}}" method="post" enctype="multipart/form-data">
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
                                    <label for="name">Buyer Name* </label>
                                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" value="{{$user->name?:old('name')}}" required="" />
                                    @if ($errors->has('name'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="name">Company Name</label>
                                    <input type="text" class="form-control {{$errors->has('company_name')?'error':''}}" name="company_name" placeholder="Enter Company Name" value="{{$user->company_name?:old('company_name')}}" />
                                    @if ($errors->has('company_name'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('company_name') }}</p>
                                    @endif
                                </div>
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="email">Email*</label>
                                    <input type="email" class="form-control {{$errors->has('email')?'error':''}}" name="email" placeholder="Enter Email" value="{{$user->email?:old('email')}}" required />
                                    @if ($errors->has('email'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email') }}</p>
                                    @endif
                                </div>
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="mobile">Mobile </label>
                                    <input type="tel" class="form-control {{$errors->has('mobile')?'error':''}}" name="mobile" minlength="11" maxlength="11" pattern="[0-9]{11}" title="Please enter exactly 11 digits" oninput="this.value = this.value.slice(0, 11);" placeholder="Please enter exactly 11 digits with start 0" value="{{$user->mobile?:old('mobile')}}" required>
                                    @if ($errors->has('mobile'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mobile') }}</p>
                                    @endif
                                </div>
                                <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                    <div class="controls">
                                        <label for="address">Address Line</label>
                                        <input type="text" class="form-control {{$errors->has('address')?'error':''}}" name="address" placeholder="Enter Address" value="{{$user->address_line1?:old('address')}}" />
                                    </div>
                                </div>

                                <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                    <div class="controls">
                                        <label for="country">Country</label>
                                        <select name="country" id="" class="form-control">
                                            <option value="">-- Select Country --</option>
                                            @foreach (geoData(1) as $c)
                                                <option value="{{ $c->name }}" {{ $user->country_text == $c->name ? 'selected':'' }}>{{ $c->name }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                    <label for="status">User Status</label>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="status" id="status" {{$user->status?'checked':''}}/>
                                        <label class="custom-control-label" for="status">User Active</label>
                                    </div>
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

<script type="text/javascript">


</script>


@endpush
