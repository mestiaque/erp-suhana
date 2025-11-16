@extends(adminTheme().'layouts.app') 

@section('title')
<title>{{ websiteTitle('Role Update') }}</title>
@endsection 

@push('css')
<style type="text/css">
    .col-md-3 {
        padding: 6px 15px;
    }
</style>
@endpush 

@section('contents')
<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>Role Update</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item"><a href="{{ route('admin.userRoles') }}">User Roles</a></li>
        <li class="item">Role Update</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">
    <!-- Start Form -->
    <form action="{{ route('admin.userRoleAction',['update',$role->id]) }}" method="post">
        @csrf
        <div class="card mb-30">
            <div class="card-header d-flex justify-content-between align-items-center">
                 <h3>Role Update</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Role Name </label>
                    <input type="text" class="form-control" name="name" placeholder="Role name" value="{{ $role->name }}" required="" />
                    @if ($errors->has('name'))
                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary btn-md rounded-0">Save changes</button>
            </div>
        </div>

        @php
            $permissions = config('permission.modules');
            $rolePermissions = json_decode($role->permission, true) ?? [];
        @endphp

        @foreach($permissions as $moduleKey => $module)
            <div class="card mb-30">
                <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 10px;">
                    <h3>{{ $module['label'] }}
                        <div class="checkbox">
                            @php
                                $allChecked = collect(array_keys($module['permissions']))->every(function($permKey) use ($rolePermissions, $moduleKey) {
                                    return isset($rolePermissions[$moduleKey][$permKey]);
                                });
                            @endphp
                            <input class="inp-cbx selectAll" data-type="{{ $moduleKey }}All" id="{{ $moduleKey }}_all" type="checkbox" style="display: none;"
                                @if($allChecked) checked @endif />
                            <label class="cbx" for="{{ $moduleKey }}_all">
                                <span>
                                    <svg width="12px" height="10px" viewbox="0 0 12 10">
                                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                    </svg>
                                </span>
                            </label>
                        </div>
                        <label for="{{ $moduleKey }}_all">All</label>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($module['permissions'] as $permKey => $permLabel)
                            <div class="col-md-3">
                                <div class="checkbox">
                                    <input class="inp-cbx {{ $moduleKey }}All" id="{{ $moduleKey }}_{{ $permKey }}" type="checkbox" 
                                        name="permission[{{ $moduleKey }}][{{ $permKey }}]"
                                        @isset($rolePermissions[$moduleKey][$permKey]) checked @endisset
                                        style="display: none;" />
                                    <label class="cbx" for="{{ $moduleKey }}_{{ $permKey }}">
                                        <span>
                                            <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                                <label style="margin:0 5px;" for="{{ $moduleKey }}_{{ $permKey }}">{{ $permLabel }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

    </form>
</div>
@endsection 

@push('js')
<script type="text/javascript">
    $(document).ready(function () {
        $(".selectAll").change(function () {
            var dataClass = $(this).data('type');
            var checked = $(this).prop("checked");
            $('.' + dataClass).prop("checked", checked);
        });
    });
</script>
@endpush
