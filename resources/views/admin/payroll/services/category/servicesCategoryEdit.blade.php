@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Category Edit')}}</title>
@endsection @push('css')

<style type="text/css"></style>
@endpush @section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Category Edit</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Category Edit</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-outline-primary" href="{{route('admin.servicesCategories')}}">BACK</a>
            <a class="btn btn-outline-primary" href="{{route('admin.servicesCategoriesAction','create')}}">Add Category</a>
            <a class="btn btn-outline-primary" href="{{route('admin.servicesCategoriesAction',['edit',$category->id])}}">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Basic Elements start -->
    <section class="basic-elements">
        @include('admin.alerts')
        <form action="{{route('admin.servicesCategoriesAction',['update',$category->id])}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Category Edit</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Category Name(*) </label>
                                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Category Name" value="{{$category->name?:old('name')}}" required="" />
                                    @if ($errors->has('name'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="parent_id">Parent Category</label>
                                    <select name="parent_id" class="form-control">
                                        <option value="">Select Category</option>

                                        @foreach($parents as $parent) @if($parent->id==$category->id) @else
                                        <option value="{{$parent->id}}" {{$parent->id==$category->parent_id?'selected':''}}>{{$parent->name}}</option>
                                        @if($parent->subctgs->count() > 0) @include('admin.services.includes.editSubcategory',['subcategories' =>$parent->subctgs, 'i'=>1]) @endif @endif @endforeach
                                    </select>
                                    @if ($errors->has('parent_id'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('parent_id') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="description">Description </label>
                                    <textarea name="description" class="{{$errors->has('description')?'error':''}} summernote" placeholder="Enter Description">{!!$category->description!!}</textarea>
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
                                    <input type="text" class="form-control {{$errors->has('seo_title')?'error':''}}" name="seo_title" placeholder="Enter SEO Meta Title" value="{{$category->seo_title?:old('seo_title')}}" />
                                    @if ($errors->has('seo_title'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('seo_title') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="seo_description">SEO Meta Description </label>
                                    <textarea name="seo_description" class="form-control {{$errors->has('seo_description')?'error':''}}" placeholder="Enter SEO Meta Description">{!!$category->seo_description!!}</textarea>
                                    @if ($errors->has('seo_description'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('seo_description') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="seo_keyword">SEO Meta Keyword </label>
                                    <textarea name="seo_keyword" class="form-control {{$errors->has('seo_keyword')?'error':''}}" placeholder="Enter SEO Meta Keyword">{!!$category->seo_keyword!!}</textarea>
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
                            <h4 class="card-title">Category Images</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="image">Category Image</label>
                                    <input type="file" name="image" class="form-control {{$errors->has('image')?'error':''}}" />
                                    @if ($errors->has('image'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('image') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <img src="{{asset($category->image())}}" style="max-width: 100px;" />
                                    @if($category->imageFile)
                                    <a href="{{route('admin.mediesDelete',$category->imageFile->id)}}" class="mediaDelete" style="color: red;"><i class="fa fa-trash"></i></a>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="banner">Category Banner</label>
                                    <input type="file" name="banner" class="form-control {{$errors->has('banner')?'error':''}}" />
                                    @if ($errors->has('banner'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('banner') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <img src="{{asset($category->banner())}}" style="max-width: 200px;" />
                                    @if($category->bannerFile)
                                    <a href="{{route('admin.mediesDelete',$category->bannerFile->id)}}" class="mediaDelete" style="color: red;"><i class="fa fa-trash"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Category Action</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="status">Category Status</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="status" name="status" {{$category->status=='active'?'checked':''}}/>
                                            <label class="custom-control-label" for="status">Active</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="Featured">Category Featured</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="Featured" name="featured" {{$category->featured?'checked':''}}/>
                                            <label class="custom-control-label" for="Featured">Active</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save changes</button>
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
                    @include(adminTheme().'menus.includes.menuSetting',['item_id'=>$category->id,'menu_type'=>3])
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
</div>

@endsection @push('js')

<script>
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
