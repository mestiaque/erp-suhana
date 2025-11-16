@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle(ucfirst($type).' Setting')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')
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

@include(adminTheme().'alerts')
<form action="{{route('admin.settingUpdate',$type)}}" method="post" enctype="multipart/form-data">
    @csrf
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>SMS Setting</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="sms_type">SMS Type</label>
                    <select name="sms_type" class="form-control {{$errors->has('sms_type')?'error':''}}">
                        <option value="smtp" {{$general->sms_type=='Non Masking'?'selected':''}}>Non-Masking</option>
                        <option value="mailgun" {{$general->sms_type=='Masking'?'selected':''}}>Masking</option>
                    </select>
                    @if ($errors->has('sms_type'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sms_type') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="sms_senderid">SMS Sender ID</label>
                    <input type="text" name="sms_senderid" value="{{ $general->sms_senderid }}" placeholder="SMS Sender ID" class="form-control {{$errors->has('sms_senderid')?'error':''}}" />
                    @if ($errors->has('sms_senderid'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sms_senderid') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="sms_url_nonmasking">SMS Url Non-Masking</label>
                    <input type="text" name="sms_url_nonmasking" value="{{ $general->sms_url_nonmasking }}" placeholder="SMS Url Non-Masking" class="form-control {{$errors->has('sms_url_nonmasking')?'error':''}}" />
                    @if ($errors->has('sms_url_nonmasking'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sms_url_nonmasking') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="sms_url_masking">SMS Url Masking</label>
                    <input type="text" name="sms_url_masking" value="{{ $general->sms_url_masking }}" placeholder="SMS Url Masking" class="form-control {{$errors->has('sms_url_masking')?'error':''}}" />
                    @if ($errors->has('sms_url_masking'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sms_url_masking') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="sms_username">SMS Username</label>
                    <input type="text" name="sms_username" value="{{ $general->sms_username }}" placeholder="SMS Username  " class="form-control {{$errors->has('sms_username')?'error':''}}" />
                    @if ($errors->has('sms_username'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sms_username') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="sms_password">SMS Password</label>
                    <div class="input-group">
                        <input type="password" name="sms_password" value="{{$general->sms_password}}" placeholder="SMS Password" class="form-control password {{$errors->has('sms_password')?'error':''}}" />
                        <div class="input-group-append">
                            <span class="input-group-text showPassword"><i class='bx bx-hide'></i></span>
                        </div>
                    </div>
                    @if ($errors->has('sms_password'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sms_password') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label>SMS Status</label>
                    <div class="checkbox">
                         <input class="inp-cbx" id="sms_status" name="sms_status" type="checkbox" {{$general->sms_status?'checked':''}} style="display: none;" />
                         <label class="cbx" for="sms_status">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                              Active <small>(SMS System Active)</small>
                         </label>
                     </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-md rounded-0">Save changes</button>
        </div>
    </div>
</form>

@endsection @push('js') @endpush
