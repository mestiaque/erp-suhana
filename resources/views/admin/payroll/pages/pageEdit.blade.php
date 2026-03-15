@extends(adminTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Page Edit')}}</title>
@endsection 
@push('css')

<style type="text/css">

</style>
@endpush 
@section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Page Edit</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Page Edit</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-outline-primary" href="{{route('admin.pages')}}">BACK</a>
            <!--<a class="btn btn-outline-info MenuSetting" href="javascript:void(0)" data-id="{{$page->id}}">Add Menus</a>-->
            @isset(json_decode(Auth::user()->permission->permission, true)['pages']['add'])
            <a class="btn btn-outline-primary" href="{{route('admin.pagesAction','create')}}" onclick="return confirm('Are You Want To New page?')">Add Page</a>
            @endisset
            <a class="btn btn-outline-primary" href="{{route('admin.pagesAction',['edit',$page->id])}}">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Basic Elements start -->
    <section class="basic-elements">
    @include(adminTheme().'alerts')
        <form action="{{route('admin.pagesAction',['update',$page->id])}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Page Edit 
                            	@if($page->slug)
                            	<a href="{{route('pageView',$page->slug)}}" class="badge badge-success float-right" target="_blank">View</a>
                            	@endif
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Page Name 
                                        @if($page->template)
                                    	<span style="color: #ccc;">({{$page->template}})</span>
                                    	@endif
                                    </label>
                                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Page Name" value="{{$page->name?:old('name')}}" required="" />
                                    @if ($errors->has('name'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="short_description">Short Description </label>
                                    <textarea name="short_description" class="form-control {{$errors->has('short_description')?'error':''}}" placeholder="Enter Short Description">{!!$page->short_description!!}</textarea>
                                    @if ($errors->has('short_description'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('short_description') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="description">Description </label>
                                    <textarea name="description" class="{{$errors->has('description')?'error':''}} summernote" placeholder="Enter Description">{!!$page->description!!}</textarea>
                                    @if ($errors->has('description'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">SEO Optimize</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="seo_title">SEO Meta Title</label>
                                    <input type="text" class="form-control {{$errors->has('seo_title')?'error':''}}" name="seo_title" placeholder="Enter SEO Meta Title" value="{{$page->seo_title?:old('seo_title')}}" />
                                    @if ($errors->has('seo_title'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('seo_title') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="seo_description">SEO Meta Description </label>
                                    <textarea name="seo_description" class="form-control {{$errors->has('seo_description')?'error':''}}" placeholder="Enter SEO Meta Description">{!!$page->seo_description!!}</textarea>
                                    @if ($errors->has('seo_description'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('seo_description') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="seo_keyword">SEO Meta Keyword </label>
                                    <textarea name="seo_keyword" class="form-control {{$errors->has('seo_keyword')?'error':''}}" placeholder="Enter SEO Meta Keyword">{!!$page->seo_keyword!!}</textarea>
                                    @if ($errors->has('seo_keyword'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('seo_keyword') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Page Images</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="image">Page Image</label>
                                    <input type="file" name="image" class="form-control {{$errors->has('image')?'error':''}}" />
                                    @if ($errors->has('image'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('image') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <img src="{{asset($page->image())}}" style="max-width: 100px;" />
                                    @isset(json_decode(Auth::user()->permission->permission, true)['pages']['add'])
                                    @if($page->imageFile)
                                    <a href="{{route('admin.mediesDelete',$page->imageFile->id)}}" class="mediaDelete" style="color: red;"><i class="fa fa-trash"></i></a>
                                    @endif
                                    @endisset
                                </div>
                                <div class="form-group">
                                    <label for="banner">Page Banner</label>
                                    <input type="file" name="banner" class="form-control {{$errors->has('banner')?'error':''}}" />
                                    @if ($errors->has('banner'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('banner') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <img src="{{asset($page->banner())}}" style="max-width: 200px;" />
                                    @isset(json_decode(Auth::user()->permission->permission, true)['pages']['add'])
                                    @if($page->bannerFile)
                                    <a href="{{route('admin.mediesDelete',$page->bannerFile->id)}}" class="mediaDelete" style="color: red;"><i class="fa fa-trash"></i></a>
                                    @endif
                                    @endisset
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Galleries</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @if ($errors->has('galleries*'))
                                <p style="color: red; margin: 0; font-size: 10px;">The Galleries Must Be a Number</p>
                                @endif
                                <select data-placeholder="Select Gallery..." name="galleries[]" class="select2 form-control" multiple="multiple">
                                    @foreach($galleries as $i=>$gallery)
                                    <option value="{{$gallery->id}}" @foreach($page->postTags as $posttag) {{$posttag->reff_id==$gallery->id?'selected':''}} @endforeach>{{$gallery->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Page Action</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="template">Page Template</label>
                                    <select class="form-control" name="template">
                                        <option value="">Default Template</option>
                                        <option value="Front Page" {{$page->template=='Front Page'?'selected':''}}>Front Page</option>
                                        <option value="Privacy Policy" {{$page->template=='Privacy Policy'?'selected':''}}>Privacy Policy</option>
                                        <option value="Latest Blog" {{$page->template=='Latest Blog'?'selected':''}}>Latest Blog</option>
                                        <option value="Latest Services" {{$page->template=='Latest Services'?'selected':''}}>Latest Services</option>
                                        <option value="About Us" {{$page->template=='About Us'?'selected':''}}>About Us</option>
                                        <option value="Contact Us" {{$page->template=='Contact Us'?'selected':''}}>Contact Us</option>
                                        <option value="Galleries" {{$page->template=='Galleries'?'selected':''}}>Galleries</option>
                                        <option value="All Brands" {{$page->template=='All Brands'?'selected':''}}>All Brands</option>
                                        <option value="All Clients" {{$page->template=='All Clients'?'selected':''}}>All Clients</option>
                                    </select>
                                    @if ($errors->has('template'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('template') }}</p>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="status">Page Status</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="status" name="status" {{$page->status=='active'?'checked':''}}/>
                                            <label class="custom-control-label" for="status">Active</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="fetured">Page Featured</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="fetured" name="fetured" {{$page->fetured?'checked':''}}/>
                                            <label class="custom-control-label" for="fetured">Active</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Published Date</label>
                                    <input type="date" class="form-control form-control-sm" name="created_at" value="{{$page->created_at->format('Y-m-d')}}">
                                    @if ($errors->has('created_at'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
                                    @endif
                                </div>
                                @isset(json_decode(Auth::user()->permission->permission, true)['pages']['add'])
                                <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save changes</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
        
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                <h4 class="card-title">Add Menu</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    @include(adminTheme().'menus.includes.menuSetting',['item_id'=>$page->id,'menu_type'=>1])
                </div>
            </div>
        </div>
        
    </section>
    <!-- Basic Inputs end -->
</div>

@endsection @push('js')
<script>
    $(".summernote").summernote({
        placeholder: "Write Your Contents",
        tabsize: 2,
        height: 120,
        toolbar: [
            ["style", ["style"]],
            ["font", ["bold", "underline"]],
            ["color", ["color"]],
            ["para", ["ul", "ol", "paragraph"]],
            ["table", ["table"]],
            ["insert", ["link", "picture"]],
            ["view", ["fullscreen", "codeview"]],
        ],
    });
</script>

@endpush
