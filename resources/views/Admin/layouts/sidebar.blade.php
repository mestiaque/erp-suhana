<div class="sidemenu-area">
    <div class="sidemenu-header">
        <a href="" class="navbar-brand d-flex align-items-center">
            <img src="{{ asset(general()->logo()) }}" alt="logo" />
        </a>

        <div class="burger-menu d-none d-lg-block">
            <span class="top-bar"></span>
            <span class="middle-bar"></span>
            <span class="bottom-bar"></span>
        </div>

        <div class="responsive-burger-menu d-block d-lg-none">
            <span class="top-bar"></span>
            <span class="middle-bar"></span>
            <span class="bottom-bar"></span>
        </div>
    </div>

    <div class="sidemenu-body">
        <ul class="sidemenu-nav metismenu h-100" id="sidemenu-nav" data-simplebar="">

        @foreach(config('sidebar') as $group)
            @php
                $groupHasVisibleMenu = false;

                foreach ($group as $key => $menu) {

                    if ($key === 'group_title') continue;
                    if (!isset($menu['title'])) continue;

                    // Single item
                    if (!isset($menu['children'])) {
                        if ($menu['permission'] === '' || hasParentPermission($menu['permission'])) {
                            $groupHasVisibleMenu = true;
                            break;
                        }
                        continue;
                    }

                    // Item with children
                    foreach ($menu['children'] as $child) {
                        $childPermission = $child['permission'] ?? '';
                        if ($childPermission === '' || hasChildPermission($menu['permission'] ?? '', $childPermission)) {
                            $groupHasVisibleMenu = true;
                            break 2;
                        }
                    }
                }
            @endphp

            @if(!$groupHasVisibleMenu) @continue @endif

            {{-- Group Title --}}
            <li class="nav-item-title">
                {{ $group['group_title'] ?? '' }}
            </li>

            @foreach($group as $key => $menu)
                @if($key === 'group_title') @continue @endif
                @if(!isset($menu['title'])) @continue @endif

                {{-- ================== SINGLE ITEM ================== --}}
                @if(!isset($menu['children']))
                    @php
                        $show = $menu['permission'] === '' || hasParentPermission($menu['permission']);
                        $route = $menu['route'] ?? '';
                        $pattern = trim($route, '/');
                        $prefix  = $pattern . '/*';
                        $active = !empty($route) && (request()->is($pattern) || request()->is($prefix));
                    @endphp

                    @if($show)
                        <li class="nav-item {{ $active ? 'mm-active' : '' }}">
                            <a href="{{ !empty($menu['route']) ? url($menu['route']) : 'javascript:void(0)' }}" class="nav-link">
                                <span class="icon"><i class="{{ $menu['icon'] }}"></i></span>
                                <span class="menu-title">{{ $menu['title'] }}</span>
                            </a>
                        </li>
                    @endif

                    @continue
                @endif

                {{-- ================== PARENT WITH CHILDREN ================== --}}
                @php
                    $showParent = $menu['permission'] === '' || hasParentPermission($menu['permission']);
                    $visibleChildren = [];
                    $parentActive = false;

                    foreach ($menu['children'] as $child) {
                        $childPermission = $child['permission'] ?? '';
                        $route = $child['route'] ?? '';

                        if ($childPermission === '' || hasChildPermission($menu['permission'] ?? '', $childPermission)) {
                            $visibleChildren[] = $child;

                            if (!empty($route)) {
                                $pattern = trim($route, '/');
                                $prefix  = $pattern . '/*';

                                if (request()->is($pattern) || request()->is($prefix)) {
                                    $parentActive = true;
                                }
                            }
                        }
                    }
                @endphp

                @if($showParent && count($visibleChildren) > 0)
                    <li class="nav-item {{ $parentActive ? 'mm-active' : '' }}">
                        <a href="javascript:void(0)" class="collapsed-nav-link nav-link" aria-expanded="{{ $parentActive ? 'true' : 'false' }}">
                            <span class="icon"><i class="{{ $menu['icon'] }}"></i></span>
                            <span class="menu-title">{{ $menu['title'] }}</span>
                        </a>

                        <ul class="sidemenu-nav-second-level mm-collapse {{ $parentActive ? 'mm-show' : '' }}">
                            @foreach($visibleChildren as $child)
                                @php
                                    $route = $child['route'] ?? '';
                                    $pattern = trim($route, '/');
                                    $prefix  = $pattern . '/*';
                                    $childActive = !empty($route) && (request()->is($pattern) || request()->is($prefix));
                                @endphp

                                <li class="nav-item {{ $childActive ? 'mm-active' : '' }}">
                                    <a href="{{ !empty($child['route']) ? url($child['route']) : 'javascript:void(0)' }}" class="nav-link">
                                        <span class="icon"><i class="{{ $child['icon'] }}"></i></span>
                                        <span class="menu-title">{{ $child['title'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif

            @endforeach
        @endforeach

        </ul>
    </div>
</div>
