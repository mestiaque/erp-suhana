@extends(adminTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Items Update')}}</title>
@endsection
@push('css')
<style type="text/css">
    .listmenu ul {
        margin: 0;
        padding: 0;
    }
    .listmenu ul li {
        list-style: none;
        margin: 5px;
        padding: 10px;
        border: 1px solid gray;
    }
    .menumanage {
        float: right;
    }
</style>
@endpush
@section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Items Update</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Items Update</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            @if($item->parent_id)
            <a class="btn btn-outline-primary" href="{{route('admin.menusAction',['edit',$item->parent_id])}}">BACK</a>
            @else
            <a class="btn btn-outline-primary" href="{{route('admin.menus')}}">BACK</a>
            @endif
            <a class="btn btn-outline-primary reloadPage" href="javascript:void(0)">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Basic Elements start -->
    <section class="basic-elements">
        @include(adminTheme().'alerts')

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                        <h4 class="card-title">Items Update</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{route('admin.menusItemsAction',['update',$item->id])}}" method="post" enctype="multipart/form-data">
                                @csrf @if($item->menu_type==1)
                                <h4><b> Name:</b> {{$item->menuName()?:'No Found'}} <span style="color: #d8d8d8;">(Page)</span></h4>
                                @elseif($item->menu_type==2)
                                <h4><b> Name:</b> {{$item->menuName()?:'No Found'}} <span style="color: #d8d8d8;">(Post Category)</span></h4>
                                @else
                                <div class="form-group">
                                    <label>Menu Name*</label>
                                    @if ($errors->has('name'))
                                    <p style="color: red; margin: 0;">{{ $errors->first('name') }}</p>
                                    @endif
                                    <input type="text" name="name" value="{{$item->name}}" class="form-control" placeholder="Enter Menu Name" />
                                </div>
                                <div class="form-group">
                                    <label>Menu Link</label>
                                    @if ($errors->has('link'))
                                    <p style="color: red; margin: 0;">{{ $errors->first('link') }}</p>
                                    @endif
                                    <input type="text" name="link" value="{{$item->slug}}" placeholder="Enter Menu Link" class="form-control" />
                                </div>
                                @endif
                                <div class="form-group">
                                    <label>Menu Icon (Font Icon class)</label>
                                    @if ($errors->has('icon'))
                                    <p style="color: red; margin: 0;">{{ $errors->first('icon') }}</p>
                                    @endif
                                    <input type="text" name="icon" value="{{$item->icon}}" placeholder="Enter Font Icon" class="form-control" />
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-8">
                                        <label>Menu Image (1X1)</label>
                                        @if ($errors->has('image'))
                                        <p style="color: red; margin: 0;">{{ $errors->first('image') }}</p>
                                        @endif
                                        <input type="file" name="image" class="form-control" />
                                    </div>
                                    <div class="form-group col-lg-4" style="position: relative;">
                                        @if($item->imageFile)
                                        
                                        @isset(json_decode(Auth::user()->permission->permission, true)['menus']['add'])
                                        <span style="position: absolute; right: 10px; top: 0px;">
                                            <a href="{{route('admin.mediesDelete',$item->imageFile->id)}}" class="mediaDelete" style="font-size: 25px; color: red;"><i class="fa fa-times-circle"></i></a>
                                        </span>
                                        @endisset

                                        <img src="{{asset($item->image())}}" style="max-width: 50px;" />
                                        @else
                                        <span>No Image</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label>Target New Window</label>
                                        <div class="i-checks">
                                            <label style="cursor: pointer;"> <input name="target" {{$item->target?'checked':''}} type="checkbox" > <i></i> Active</label>
                                        </div>
                                    </div>
                                </div>
                                @isset(json_decode(Auth::user()->permission->permission, true)['menus']['add'])
                                <div class="form-group">
                                    <button type="submit" class="btn btn-md rounded-0 btn-success">Submit</button>
                                </div>
                                @endisset
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
</div>

@endsection 
@push('js') 
@endpush
