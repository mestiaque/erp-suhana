@extends(adminTheme().'layouts.app')

@section('title')

<title>{{ websiteTitle('Role Update') }}</title>
@endsection

@push('css')

<style>
    .col-md-3 {
        padding: 6px 15px;
    }
    .sub-permissions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
    }
    .sub-permissions label {
        margin: 0;
        display: flex;
        align-items: center;
    }
</style>

@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Role Update</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{ route('admin.userRoles') }}">User Roles</a></li>
        <li class="item">Role Update</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">
    <form action="{{ route('admin.userRoleAction',['update',$role->id]) }}" method="post">
        @csrf
        <div class="card mb-30">
            <div class="card-header d-flex justify-content-between align-items-center">
                 <h3 class="mb-0">Role Update</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Role Name </label>
                    <input type="text" class="form-control" name="name" placeholder="Role name" value="{{ $role->name }}" required />
                    @if ($errors->has('name'))
                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary btn-md rounded-0">Save changes</button>
            </div>
        </div>

    @php
        $permissions = config('permissions.modules');
        $rolePermissions = json_decode($role->permission, true) ?? [];

        function sanitizeId($string) {
            return str_replace([' ', '.', '/'], '_', $string);
        }
    @endphp

    @foreach($permissions as $moduleKey => $module)
        @php
            $collapseId = 'collapse_' . sanitizeId($moduleKey);
        @endphp
        <div class="card mb-30 shadow">
            <div class="card-header pb-2" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="true" aria-controls="{{ $collapseId }}">
                <h3>{{ strtoupper($moduleKey) }}</h3>
            </div>
            <div class="collapse show" id="{{ $collapseId }}">

                <div class="card-body" style="font-size: 15px !important">
                    @foreach($module as $subKey => $subModule)
                        @if(is_array($subModule) && isset($subModule['permissions']))
                            @php
                                $parentId = sanitizeId($moduleKey . '_' . $subKey);
                            @endphp
                            <div class="mb-0 sub-permissions-row px-3 py-1">
                                <div class="sub-permissions" style="display:flex; gap:10px; align-items:flex-start;">
                                    <!-- Parent label part (20%) -->
                                    <div style="flex: 0 0 20%; display:flex; align-items:center; gap:6px;">
                                        <input type="checkbox" class="parent-cbx inp-cbx" id="{{ $parentId }}" style="display:none;">
                                        <label class="cbx" for="{{ $parentId }}">
                                            <span>
                                                <svg width="12px" height="10px" viewBox="0 0 12 10">
                                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                </svg>
                                            </span>
                                        </label>
                                        {{ $subModule['label'] }}:
                                    </div>

                                    <!-- Child checkboxes grid (80%) -->
                                    <div style="flex: 0 0 80%; display:grid; grid-template-columns: repeat(7, 1fr); gap:10px;">
                                        @foreach($subModule['permissions'] as $permKey => $permLabel)
                                            @php
                                                $childId = $parentId . '_' . $permKey;
                                            @endphp
                                            <div style="display:flex; align-items:center; gap:6px;">
                                                <input type="checkbox" class="inp-cbx child-cbx"
                                                    id="{{ $childId }}"
                                                    data-parent="{{ $parentId }}"
                                                    name="permission[{{ $subKey }}][{{ $permKey }}]"
                                                    @isset($rolePermissions[$subKey][$permKey]) checked @endisset
                                                    style="display:none;" />
                                                <label class="cbx" for="{{ $childId }}">
                                                    <span>
                                                        <svg width="12px" height="10px" viewBox="0 0 12 10">
                                                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                        </svg>
                                                    </span>
                                                </label>
                                                <label for="{{ $childId }}">{{ $permLabel }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        @endif
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
            // Initialize parent checkbox based on saved children
            $('.parent-cbx').each(function() {
                var parentId = $(this).attr('id');
                var allChildren = $('.child-cbx[data-parent="' + parentId + '"]');
                var allChecked = allChildren.length === allChildren.filter(':checked').length;
                $(this).prop('checked', allChecked);
            });

            // Parent checkbox toggles children
            $('.parent-cbx').change(function() {
                var parentId = $(this).attr('id');
                var checked = $(this).prop('checked');
                $('.child-cbx[data-parent="' + parentId + '"]').prop('checked', checked);
            });

            // Children update parent checkbox
            $('.child-cbx').change(function() {
                var parentId = $(this).data('parent');
                var allChildren = $('.child-cbx[data-parent="' + parentId + '"]');
                $('#' + parentId).prop('checked', allChildren.length === allChildren.filter(':checked').length);
            });

        });
    </script>
@endpush

@push('css')
    <style>
        .sub-permissions-row:nth-child(odd) {
            background-color: #f2f2f2; /* light grey */
        }
        .sub-permissions-row:hover {
            background-color: #e6f7ff;
        }

    </style>
@endpush
