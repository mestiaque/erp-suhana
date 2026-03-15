@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle(ucfirst($type).' Setting')}}</title>
@endsection 
@push('css')
<style type="text/css">
    .note-editable p {
        font-size: 10px;
        font-family: times new romance;
    }
</style>
@endpush 
@section('contents')

<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>Setting</h1>

    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>

        <li class="item">{{ucfirst($type)}} Setting</li>
    </ol>
</div>
<!-- End Breadcrumb Area -->
<div class="flex-grow-1">
@include(adminTheme().'alerts')
<form action="{{route('admin.settingUpdate',$type)}}" method="post" enctype="multipart/form-data">
    @csrf
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>General Info </h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="title">Website Title </label>
                    <input type="text" name="title" value="{{ $general->title }}" placeholder="Website Title" class="form-control {{$errors->has('title')?'error':''}}" />
                    @if ($errors->has('title'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('title') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="subtitle">Website Subtitle</label>
                    <input type="text" name="subtitle" value="{{ $general->subtitle }}" placeholder="Website subtitle" class="form-control {{$errors->has('subtitle')?'error':''}}" />
                    @if ($errors->has('subtitle'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('subtitle') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="mobile">Mobile Number</label>
                    <input type="text" name="mobile" value="{{ $general->mobile }}" placeholder="Website mobile" class="form-control {{$errors->has('mobile')?'error':''}}" />
                    @if ($errors->has('mobile'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mobile') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="mobile2">Mobile Number 2</label>
                    <input type="text" name="mobile2" value="{{ $general->mobile2 }}" placeholder="Website mobile 2" class="form-control {{$errors->has('mobile2')?'error':''}}" />
                    @if ($errors->has('mobile2'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mobile2') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="email">Email Address</label>
                    <input type="text" name="email" value="{{ $general->email }}" placeholder="Website email" class="form-control {{$errors->has('email')?'error':''}}" />
                    @if ($errors->has('email'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="email2">Email Address 2</label>
                    <input type="text" name="email2" value="{{ $general->email2 }}" placeholder="Website email 2" class="form-control {{$errors->has('email2')?'error':''}}" />
                    @if ($errors->has('email2'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email2') }}</p>
                    @endif
                </div>
    
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="address_one">Address line 1</label>
                    <textarea name="address_one" placeholder="Address Line 1" rows="5" class="form-control  {{$errors->has('address_one')?'error':''}}">{{ $general->address_one}}</textarea>
                    @if ($errors->has('address_one'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('address_one') }}</p>
                    @endif
                </div>
    
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="address_two">Address line 2</label>
                    <textarea name="address_two" placeholder="Address Line 1" rows="5" class="form-control {{$errors->has('address_two')?'error':''}}">{{ $general->address_two}}</textarea>
                    @if ($errors->has('address_two'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('address_two') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="favicon">Favicon</label>
                    <div class="custom-file">
                         <input type="file" name="favicon" class="custom-file-input {{$errors->has('favicon')?'error':''}}">
                         <label class="custom-file-label">Choose file... </label>
                    </div>
                    @if ($errors->has('favicon'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('favicon') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <img src="{{asset($general->favicon())}}" style="max-width: 60px;" />
                    @if($general->favicon)
                    <a href="{{route('admin.setting','favicon')}}" style="color: red;" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="helpInputTop">Logo</label>
                    <div class="custom-file">
                         <input type="file" name="logo" class="custom-file-input {{$errors->has('logo')?'error':''}}">
                         <label class="custom-file-label">Choose file... </label>
                    </div>
                    @if ($errors->has('logo'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('logo') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <img src="{{asset($general->logo())}}" style="max-width: 150px;" />
                    @if($general->logo)
                    <a href="{{route('admin.setting','logo')}}" style="color: red;" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                    @endif
                </div>
    
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="website">Website Url</label>
                    <input type="text" name="website" value="{{ $general->website }}" placeholder="Website website" class="form-control {{$errors->has('website')?'error':''}}" />
                    @if ($errors->has('website'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('website') }}</p>
                    @endif
                </div>
                
                <div class="form-group col-xl-12 col-lg-12 col-md-12">
                    <label for="website">Terms And Conditions</label>
                    <textarea class="form-control summernote" name="pi_terms_condition" placeholder="Write Pi Terms and Condition">{{ $general->pi_terms_condition }}</textarea>
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="signature">Signature Image</label>
                    <div class="custom-file">
                         <input type="file" name="signature" class="custom-file-input {{$errors->has('signature')?'error':''}}">
                         <label class="custom-file-label">Choose file... </label>
                    </div>
                    @if ($errors->has('signature'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('signature') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <img src="{{asset($general->signature())}}" style="max-width: 150px;" />
                    @if($general->signature)
                    <a href="{{route('admin.setting','signature')}}" style="color: red;" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                    @endif
                </div>
                
            </div>
            <button type="submit" class="btn btn-primary btn-md rounded-0">Save changes</button>
        </div>
    </div>
</form>
</div>

@endsection 

@push('js')
<script>
    $(".summernote").summernote({
        placeholder: "Write Terms and Conditions",
        tabsize: 2,
        height: 300,
        toolbar: [
            ["font", ["bold", "underline"]],
            ["para", ["ul", "ol", "paragraph"]],
        ],
    });
</script>

@endpush
