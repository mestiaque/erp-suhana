@extends(adminTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle(ucfirst($type).' Setting')}}</title>
@endsection 
@push('css')
<style type="text/css"></style>
@endpush 
@section('contents')
<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>Setting</h1>

    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">Dashboard </li>
        <li class="item">{{ucfirst($type)}} Setting</li>
    </ol>
</div>

@include(adminTheme().'alerts')
<form action="{{route('admin.settingUpdate',$type)}}" method="post" enctype="multipart/form-data">
    @csrf
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Mail Setting</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="mail_from_address">Mail Form Address </label>
                    <input type="text" name="mail_from_address" value="{{ $general->mail_from_address }}" placeholder="Mail From Address" class="form-control  {{$errors->has('mail_from_address')?'error':''}}" />
                    @if ($errors->has('mail_from_address'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mail_from_address') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="mail_from_name">Mail Form Name</label>
                    <input type="text" name="mail_from_name" value="{{ $general->mail_from_name }}" placeholder="Mail From Name" class="form-control  {{$errors->has('mail_from_name')?'error':''}}" />
                    @if ($errors->has('mail_from_name'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mail_from_name') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="mail_driver">Mail Driver</label>
                    <select name="mail_driver" class="form-control  {{$errors->has('mail_driver')?'error':''}}">
                        <option value="smtp" {{$general->mail_driver=='smtp'?'selected':''}}>SMTP</option>
                        <option value="mailgun" {{$general->mail_driver=='mailgun'?'selected':''}}>Mailgun</option>
                        <option value="sendmail" {{$general->mail_driver=='sendmail'?'selected':''}}>Sendmail</option>
                        <option value="mail" {{$general->mail_driver=='mail'?'selected':''}}>Mail</option>
                    </select>
                    @if ($errors->has('mail_driver'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mail_driver') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="mail_host">Mail Host</label>
                    <input type="text" name="mail_host" value="{{ $general->mail_host }}" placeholder="Mail Host" class="form-control  {{$errors->has('mail_host')?'error':''}}" />
                    @if ($errors->has('mail_host'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mail_host') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="mail_port">Mail Port</label>
                    <input type="text" name="mail_port" value="{{ $general->mail_port }}" placeholder="Mail Port" class="form-control  {{$errors->has('mail_port')?'error':''}}" />
                    @if ($errors->has('mail_port'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mail_port') }}</p>
                    @endif
                </div>
    
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="mail_encryption">Mail Encryption</label>
                    <select name="mail_encryption" class="form-control  {{$errors->has('mail_encryption')?'error':''}}">
                        <option value="tls" {{$general->mail_encryption=='tls'?'selected':''}}>TLS</option>
                        <option value="ssl" {{$general->mail_encryption=='ssl'?'selected':''}}>SSL</option>
                        <option value="" {{$general->mail_encryption==null?'selected':''}}>Null</option>
                    </select>
                    @if ($errors->has('mail_encryption'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mail_encryption') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="mail_username">Mail Username</label>
                    <input type="text" name="mail_username" value="{{ $general->mail_username }}" placeholder="Mail Username  " class="form-control  {{$errors->has('mail_username')?'error':''}}" />
                    @if ($errors->has('mail_username'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mail_username') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label for="mail_password">Mail Password</label>
                    <div class="input-group">
                        <input type="password" name="mail_password" value="{{$general->mail_password}}" placeholder="Mail Password" class="form-control  password {{$errors->has('mail_password')?'error':''}}" />
                        <div class="input-group-append">
                            <span class="input-group-text showPassword"><i class='bx bx-hide'></i></span>
                        </div>
                    </div>
                    @if ($errors->has('mail_password'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mail_password') }}</p>
                    @endif
                </div>
                <div class="form-group col-xl-6 col-lg-6 col-md-12">
                    <label>Mail Status</label>
                    
                    <div class="checkbox">
                         <input class="inp-cbx" id="mail_status" name="mail_status" type="checkbox" {{$general->mail_status?'checked':''}} style="display: none;" />
                         <label class="cbx" for="mail_status">
                             <span>
                                 <svg width="12px" height="10px" viewbox="0 0 12 10">
                                     <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                 </svg>
                             </span>
                              Active <small>(Mail System Active)</small>
                         </label>
                     </div>
                </div>
                
            </div>
            <button type="submit" class="btn btn-primary btn-md rounded-0">Save changes</button>
        </div>
    </div>
</form>

@endsection 
@push('js') 
@endpush
