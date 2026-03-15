@extends(adminTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Menu Config')}}</title>
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
    .select2-container--default .select2-search--inline .select2-search__field {
        width: 100% !important;
    }
</style>
@endpush 
@section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Menu Config</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Menu Config</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            @if($menu->parent_id)
            <a class="btn btn-outline-primary" href="{{route('admin.menusAction',['edit',$menu->parent_id])}}">BACK</a>
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
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                        <h4 class="card-title">Add Items</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div id="accordionWrapa1" role="tablist" aria-multiselectable="true">
                                <div class="card accordion">

                                    <!--Custom menus Items -->
                                    @include(adminTheme().'menus.includes.customLink')
                                    

                                    <!--Page menus Items -->
                                    @include(adminTheme().'menus.includes.pagesList')


                                    <!--Post Category Items -->
                                    @include(adminTheme().'menus.includes.postCategoryList')
                                    

                                    <!--Service Category Items -->
                                    @include(adminTheme().'menus.includes.serviceCategoryList')
                                    

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                        <h4 class="card-title">Menu Config</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{route('admin.menusAction',['update',$menu->id])}}" method="post" >
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Menu Name(*) </label>
                                            <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Menu Name" value="{{$parent->name?:old('name')}}" required="" />
                                            @if ($errors->has('name'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fetured">Menu Location</label>
                                            <div class="input-group">
                                                <select class="form-control" name="location">
                                                    <option value="">Select Location</option>
                                                    <option value="Header Menus" {{$parent->location=='Header Menus'?'selected':''}}>Header Menus</option>
                                                    <option value="Footer Two" {{$parent->location=='Footer Two'?'selected':''}}>Footer Two</option>
                                                    <option value="Footer Three" {{$parent->location=='Footer Three'?'selected':''}}>Footer Three</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <hr/>
                                
                                <p>
                                <b> @if($menu->parent_id) {{$menu->menuName()?:'No Found'}} @else Primary @endif : </b>
                                    Label

                                    <span style="float: right;">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you Want To Delete?')">Delete</button>
                                        <label style="cursor: pointer; margin-bottom: 0;"> <input class="checkbox" type="checkbox" class="form-control" id="checkall" /> All <span class="checkCounter"></span> </label>
                                    </span>
                                </p>
                                <div class="listmenu">
                                    <ul  class="sortable">
                                        @foreach($menu->subMenus as $menuli)
                                        <li class="ui-sortable-handle" @if(!$menuli->
                                            menuName()) style="border: 1px solid red;" @endif >
                                            <span style="cursor: move;">
                                                <input type="hidden" name="menuids[]" value="{{$menuli->id}}" />
                                                @if($menuli->icon)
                                                <span>{!!$menuli->icon!!}</span>
                                                @elseif($menuli->imageFile)
                                                <img src="{{asset($menuli->image())}}" width="25px" />
                                                @endif {{$menuli->menuName()?:'No Found'}}

                                                <span style="color: #d8d8d8;">
                                                    ( @if($menuli->menu_type==1) Page @elseif($menuli->menu_type==2) Post Category @elseif($menuli->menu_type==3) Service Category @elseif($menuli->menu_type==0) Custom @endif )
                                                </span>
                                                <strong>Sub: {{$menuli->subMenus->count()}}</strong>
                                            </span>
                                            <span class="menumanage">
                                                <a href="{{route('admin.menusItemsAction',['edit',$menuli->id])}}" style="margin: 0 10px; color: #7bdc00;"><i class="fa fa-edit"></i></a>
                                                <a href="{{route('admin.menusAction',['edit',$menuli->id])}}" style="margin: 0 10px;"><i class="fa fa-plus"></i></a>
                                                
                                                @isset(json_decode(Auth::user()->permission->permission, true)['menus']['delete'])
                                                <label><i class="fa fa-trash text-danger"></i> <input class="checkbox" type="checkbox" name="deleteItems[]" value="{{$menuli->id}}"></label>
                                                @endisset
                                                <span> </span>
                                            </span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="form-group col-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="status" name="status" {{$parent->status=='active'?'checked':''}}/>
                                            <label class="custom-control-label" for="status">Active</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                    	@isset(json_decode(Auth::user()->permission->permission, true)['menus']['add'])
                                        <button type="submit" class="btn btn-primary btn-md rounded-0 mr-sm-1 mb-1 mb-sm-0">Save changes</button>
                                      @endisset
                                    </div>
                                </div>
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
<<script>
    $(document).ready(function(){
        $('.checkbox').click('');
    });
</script>
@endpush
