@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Developer Permissions')}}</title>
@endsection @section('contents')

<div class="flex-grow-1">
    @include(adminTheme().'alerts')

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Developer Permissions</h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Check the module box to hide the whole module from everyone's <strong>Role Update</strong>
                checklist, or check individual actions (List, Create, Edit, ...) to hide just those —
                useful for internal/developer-only capabilities that shouldn't be casually granted or
                revoked while editing a role. This only hides items from the role editor; it does not
                change any role's existing permissions.
            </p>

            <form action="{{route('admin.developerPermissionsUpdate')}}" method="post">
                @csrf

                @php
                    $hidden = $hidden ?? [];
                    function sanitizeDevPermId($string) {
                        return str_replace([' ', '.', '/'], '_', $string);
                    }
                @endphp

                @foreach($modules as $groupKey => $group)
                <div class="card mb-30 shadow">
                    <div class="card-header pb-2">
                        <h3 class="mb-0">{{strtoupper($groupKey)}}</h3>
                    </div>
                    <div class="card-body" style="font-size: 15px !important">
                        @foreach($group as $moduleKey => $moduleDef)
                        @if(is_array($moduleDef) && isset($moduleDef['label']))
                        @php
                            $parentId = 'dev_perm_' . sanitizeDevPermId($moduleKey);
                            $hiddenActions = $hidden[$moduleKey] ?? [];
                            $hasPermissions = isset($moduleDef['permissions']) && is_array($moduleDef['permissions']);
                        @endphp
                        <div class="mb-0 sub-permissions-row px-3 py-1">
                            <div class="sub-permissions" style="display:flex; gap:10px; align-items:flex-start;">
                                <div style="flex: 0 0 20%; display:flex; align-items:center; gap:6px;">
                                    <div class="mr-1">
                                        <input type="checkbox" class="module-cbx inp-cbx" id="{{ $parentId }}" data-target="{{ $parentId }}" name="hidden_modules[{{ $moduleKey }}][_module_]" value="1" {{ array_key_exists('_module_', $hiddenActions) ? 'checked' : '' }} style="display:none;">
                                        <label class="cbx mb-0" for="{{ $parentId }}">
                                            <span>
                                                <svg width="12px" height="10px" viewBox="0 0 12 10">
                                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                </svg>
                                            </span>
                                        </label>
                                    </div>
                                    {{ $moduleDef['label'] }}:
                                </div>

                                <div style="flex: 0 0 80%; display:grid; grid-template-columns: repeat(7, 1fr); gap:10px;">
                                    @if($hasPermissions)
                                    @foreach($moduleDef['permissions'] as $permKey => $permLabel)
                                    @php $childId = $parentId . '_' . $permKey; @endphp
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <div class="mr-1">
                                            <input type="checkbox" class="inp-cbx dev-perm-child" id="{{ $childId }}" data-parent="{{ $parentId }}" name="hidden_modules[{{ $moduleKey }}][{{ $permKey }}]" value="1" {{ array_key_exists($permKey, $hiddenActions) ? 'checked' : '' }} style="display:none;">
                                            <label class="cbx mb-0" for="{{ $childId }}">
                                                <span>
                                                    <svg width="12px" height="10px" viewBox="0 0 12 10">
                                                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                    </svg>
                                                </span>
                                            </label>
                                        </div>
                                        <label for="{{ $childId }}" class="mb-0">{{ $permLabel }}</label>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endforeach

                <button type="submit" class="btn-custom primary" style="position: fixed; z-index: 1; right: 1rem; bottom: 1rem;">Save changes</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('css')
    <style>
        .sub-permissions-row:nth-child(odd) {
            background-color: #f2f2f2;
        }
        .sub-permissions-row:hover {
            background-color: #e6f7ff;
        }
    </style>
@endpush

@push('js')
<script>
    $(function () {
        // Module checkbox toggles all its action checkboxes (convenience, matches Role Update).
        $('.module-cbx').change(function () {
            var checked = $(this).prop('checked');
            var target = $(this).data('target');
            $('.dev-perm-child[data-parent="' + target + '"]').prop('checked', checked);
        });
    });
</script>
@endpush
