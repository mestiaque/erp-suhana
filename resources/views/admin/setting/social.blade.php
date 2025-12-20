@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle(ucfirst($type).' Setting')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">{{ucfirst($type)}} Setting</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Setting</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-outline-primary reloadPage" href="javascript:void(0)">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    @include(adminTheme().'alerts')
    <form action="{{route('admin.settingUpdate',$type)}}" method="post" enctype="multipart/form-data">
        @csrf
        <!-- Basic Elements start -->
        <section class="basic-elements">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Social link</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="facebook_link">Facebook Link </label>
                                            <input type="text" name="facebook_link" value="{{ $general->facebook_link }}" placeholder="Facebook Link" class="form-control {{$errors->has('facebook_link')?'error':''}}" />
                                            @if ($errors->has('facebook_link'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('facebook_link') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="twitter_link">Twitter Link</label>
                                            <input type="text" name="twitter_link" value="{{ $general->twitter_link }}" placeholder="Twitter Link" class="form-control {{$errors->has('twitter_link')?'error':''}}" />
                                            @if ($errors->has('twitter_link'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('twitter_link') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="instagram_link">Instagram Link</label>
                                            <input type="text" name="instagram_link" value="{{ $general->instagram_link }}" placeholder="Instagram Link" class="form-control {{$errors->has('instagram_link')?'error':''}}" />
                                            @if ($errors->has('instagram_link'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('instagram_link') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="linkedin_link">Linkedin Link</label>
                                            <input type="text" name="linkedin_link" value="{{ $general->linkedin_link }}" placeholder="Linkedin Link" class="form-control {{$errors->has('linkedin_link')?'error':''}}" />
                                            @if ($errors->has('linkedin_link'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('linkedin_link') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="youtube_link">Youtube Link</label>
                                            <input type="text" name="youtube_link" value="{{ $general->youtube_link }}" placeholder="Youtube Link" class="form-control {{$errors->has('youtube_link')?'error':''}}" />
                                            @if ($errors->has('youtube_link'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('youtube_link') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="pinterest_link">Pinterest Link</label>
                                            <input type="text" name="pinterest_link" value="{{ $general->pinterest_link }}" placeholder="Pinterest Link" class="form-control {{$errors->has('pinterest_link')?'error':''}}" />
                                            @if ($errors->has('pinterest_link'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('pinterest_link') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Social Ingreation</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-md-12">
                                        <div class="form-group">
                                            <label for="fb_app_id">Facebook App ID </label>
                                            <input type="text" name="fb_app_id" value="{{ $general->fb_app_id }}" placeholder="Facebook App ID" class="form-control {{$errors->has('fb_app_id')?'error':''}}" />
                                            @if ($errors->has('fb_app_id'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('fb_app_id') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-12">
                                        <div class="form-group">
                                            <label for="fb_app_secret">Facebook App Secret</label>
                                            <input type="text" name="fb_app_secret" value="{{ $general->fb_app_secret }}" placeholder="Facebook App Secret" class="form-control {{$errors->has('fb_app_secret')?'error':''}}" />
                                            @if ($errors->has('fb_app_secret'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('fb_app_secret') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-12">
                                        <div class="form-group">
                                            <label for="fb_app_redirect_url">Facebook Redirect Url</label>
                                            <input
                                                type="text"
                                                name="fb_app_redirect_url"
                                                value="{{ $general->fb_app_redirect_url }}"
                                                placeholder="Facebook Redirect Url"
                                                class="form-control {{$errors->has('fb_app_redirect_url')?'error':''}}"
                                            />
                                            @if ($errors->has('fb_app_redirect_url'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('fb_app_redirect_url') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-12">
                                        <div class="form-group">
                                            <label for="google_client_id">Google App ID </label>
                                            <input type="text" name="google_client_id" value="{{ $general->google_client_id }}" placeholder="Google App ID" class="form-control {{$errors->has('google_client_id')?'error':''}}" />
                                            @if ($errors->has('google_client_id'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('google_client_id') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-12">
                                        <div class="form-group">
                                            <label for="google_client_secret">Google App Secret</label>
                                            <input
                                                type="text"
                                                name="google_client_secret"
                                                value="{{ $general->google_client_secret }}"
                                                placeholder="Google App Secret"
                                                class="form-control {{$errors->has('google_client_secret')?'error':''}}"
                                            />
                                            @if ($errors->has('google_client_secret'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('google_client_secret') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-12">
                                        <div class="form-group">
                                            <label for="google_client_redirect_url">Google Redirect Url</label>
                                            <input
                                                type="text"
                                                name="google_client_redirect_url"
                                                value="{{ $general->google_client_redirect_url }}"
                                                placeholder="Google Redirect Url"
                                                class="form-control {{$errors->has('google_client_redirect_url')?'error':''}}"
                                            />
                                            @if ($errors->has('google_client_redirect_url'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('google_client_redirect_url') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-12">
                                        <div class="form-group">
                                            <label for="tw_app_id">Twitter App ID </label>
                                            <input type="text" name="tw_app_id" value="{{ $general->tw_app_id }}" placeholder="Twitter App ID" class="form-control {{$errors->has('tw_app_id')?'error':''}}" />
                                            @if ($errors->has('tw_app_id'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('tw_app_id') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-12">
                                        <div class="form-group">
                                            <label for="tw_app_secret">Twitter App Secret</label>
                                            <input type="text" name="tw_app_secret" value="{{ $general->tw_app_secret }}" placeholder="Twitter App Secret" class="form-control {{$errors->has('tw_app_secret')?'error':''}}" />
                                            @if ($errors->has('tw_app_secret'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('tw_app_secret') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-12">
                                        <div class="form-group">
                                            <label for="tw_app_redirect_url">Twitter Redirect Url</label>
                                            <input
                                                type="text"
                                                name="tw_app_redirect_url"
                                                value="{{ $general->tw_app_redirect_url }}"
                                                placeholder="Twitter Redirect Url"
                                                class="form-control {{$errors->has('tw_app_redirect_url')?'error':''}}"
                                            />
                                            @if ($errors->has('tw_app_redirect_url'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('tw_app_redirect_url') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                                        <button type="submit" class="btn btn-primary btn-md rounded-0 mr-sm-1 mb-1 mb-sm-0">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Basic Inputs end -->
    </form>
</div>

@endsection @push('js') @endpush
