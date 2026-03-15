@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Post Edit')}}</title>
@endsection 

@push('css')

<style type="text/css">
    .catagorydiv {
        max-height: 300px;
        overflow: auto;
    }
    .catagorydiv ul {
        padding-left: 20px;
    }
    .catagorydiv ul li {
        list-style: none;
    }
    .slugEditData{
        display:none;
    }
</style>
@endpush 
@section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Post Edit</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Post Edit</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-outline-primary" href="{{route('admin.posts')}}">BACK</a>
            @isset(json_decode(Auth::user()->permission->permission, true)['posts']['add'])
            <a class="btn btn-outline-primary" href="{{route('admin.postsAction',['create'])}}">Add Post</a>
            @endisset
            <a class="btn btn-outline-primary" href="{{route('admin.postsAction',['edit',$post->id])}}">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Basic Elements start -->
    <section class="basic-elements">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.postsAction',['update',$post->id])}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Post Edit</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Post Name </label>
                                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Post Name" value="{{$post->name?:old('name')}}" required="" />
                                    @if ($errors->has('name'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="slugEdit" for="slug" style="color: #3F51B5;cursor: pointer;">Post Slug <i class="fa fa-edit"></i></label>
                                    <input type="text" class="slugEditData form-control {{$errors->has('slug')?'error':''}}" name="slug" placeholder="Post Slug" value="{{$post->slug?:old('slug')}}" />
                                </div>
                                <div class="form-group">
                                    <label for="short_description">Short Description </label>
                                    <textarea name="short_description" class="form-control {{$errors->has('short_description')?'error':''}}" placeholder="Enter Short Description">{!!$post->short_description!!}</textarea>
                                    @if ($errors->has('short_description'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('short_description') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="description">Description </label>
                                    <textarea name="description" class="{{$errors->has('description')?'error':''}} summernote" placeholder="Enter Description">{!!$post->description!!}</textarea>
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
                                    <input type="text" class="form-control {{$errors->has('seo_title')?'error':''}}" name="seo_title" placeholder="Enter SEO Meta Title" value="{{$post->seo_title?:old('seo_title')}}" />
                                    @if ($errors->has('seo_title'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('seo_title') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="seo_description">SEO Meta Description </label>
                                    <textarea name="seo_description" class="form-control {{$errors->has('seo_description')?'error':''}}" placeholder="Enter SEO Meta Description">{!!$post->seo_description!!}</textarea>
                                    @if ($errors->has('seo_description'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('seo_description') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="seo_keyword">SEO Meta Keyword </label>
                                    <textarea name="seo_keyword" class="form-control {{$errors->has('seo_keyword')?'error':''}}" placeholder="Enter SEO Meta Keyword">{!!$post->seo_keyword!!}</textarea>
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
                            <h4 class="card-title">Post Images</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="image">Post Image</label>
                                    <input type="file" name="image" class="form-control {{$errors->has('image')?'error':''}}" />
                                    @if ($errors->has('image'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('image') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <img src="{{asset($post->image())}}" style="max-width: 100px;" />
                                    @isset(json_decode(Auth::user()->permission->permission, true)['posts']['add'])
                                    @if($post->imageFile)
                                    <a href="{{route('admin.mediesDelete',$post->imageFile->id)}}" class="mediaDelete" style="color: red;"><i class="fa fa-trash"></i></a>
                                    @endif
                                    @endisset
                                </div>
                                <div class="form-group">
                                    <label for="banner">Post Banner</label>
                                    <input type="file" name="banner" class="form-control {{$errors->has('banner')?'error':''}}" />
                                    @if ($errors->has('banner'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('banner') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <img src="{{asset($post->banner())}}" style="max-width: 200px;" />
                                    @isset(json_decode(Auth::user()->permission->permission, true)['posts']['add'])
                                    @if($post->bannerFile)
                                    <a href="{{route('admin.mediesDelete',$post->bannerFile->id)}}" class="mediaDelete" style="color: red;"><i class="fa fa-trash"></i></a>
                                    @endif
                                    @endisset
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Post Category</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @if ($errors->has('categoryid*'))
                                <p style="color: red; margin: 0; font-size: 10px;">The Category Must Be a Number</p>
                                @endif
                                <div class="catagorydiv">
                                    <ul>
                                        @foreach($categories as $ctg)
                                        <li>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" value="{{$ctg->id}}" id="category_{{$ctg->id}}" name="categoryid[]" @foreach($post->postCtgs as $postctg)
                                                {{$postctg->reff_id==$ctg->id?'checked':''}} @endforeach/>
                                                <label class="custom-control-label" for="category_{{$ctg->id}}">{{$ctg->name}}</label>
                                            </div>
                                            @if($ctg->subCtgs->count() >0) @include(adminTheme().'posts.includes.postsEditSubctg',['subcategories' => $ctg->subCtgs,'i'=>1]) @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Post Tags</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <textarea id="hero-demo" name="tagskey">{!!$post->tags?:old('tagskey')!!}</textarea>
                                
                                
                                <!--@if ($errors->has('tags*'))-->
                                <!--<p style="color: red; margin: 0; font-size: 10px;">The Tags Must Be a Number</p>-->
                                <!--@endif-->
                                <!--<select data-placeholder="Select Tags..." name="tags[]" class="select2 form-control" multiple="multiple">-->
                                <!--    @foreach($tags as $i=>$tag)-->
                                <!--    <option value="{{$tag->id}}" @foreach($post->postTags as $posttag) {{$posttag->reff_id==$tag->id?'selected':''}} @endforeach>{{$tag->name}}</option>-->
                                <!--    @endforeach-->
                                <!--</select>-->
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Post Action</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="status">Post Status</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="status" name="status" {{$post->status=='active'?'checked':''}}/>
                                            <label class="custom-control-label" for="status">Active</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="fetured">Post Fetured</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="fetured" name="fetured" {{$post->fetured?'checked':''}}/>
                                            <label class="custom-control-label" for="fetured">Active</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Published Date</label>
                                    <input type="date" class="form-control form-control-sm" name="created_at" value="{{$post->created_at->format('Y-m-d')}}">
                                    @if ($errors->has('created_at'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
                                    @endif
                                </div>
                                @isset(json_decode(Auth::user()->permission->permission, true)['posts']['add'])
                                <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save changes</button>
                                @endisset
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <!-- Basic Inputs end -->
</div>

@endsection @push('js')
    <script>
        $(function() {
            $('#hero-demo').tagEditor({
                placeholder: 'Enter tags ...',
            });
        });
    </script>
<script>
    $(document).ready(function(){
        $('.slugEdit').click(function(){
            $('.slugEditData').toggle();
        });
    });
    $(".summernote").summernote({
        placeholder: "Hello stand alone ui",
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
