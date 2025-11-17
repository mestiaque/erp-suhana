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
        <ul class="sidemenu-nav metisMenu h-100" id="sidemenu-nav" data-simplebar="">

        @foreach(config('sidebar') as $group)
            @php
                $groupHasVisibleMenu = false;

                // Check each menu in group
                foreach ($group as $key => $menu) {
                    if ($key === 'group_title') continue; // skip group title

                    if (!isset($menu['title'])) continue;

                    // Single item (no children)
                    if (!isset($menu['children'])) {
                        if ($menu['permission'] === '' || hasParentPermission($menu['permission'])) {
                            $groupHasVisibleMenu = true;
                            break;
                        }
                        continue;
                    }

                    // Menu with children
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

                {{-- Single item --}}
                @if(!isset($menu['children']))
                    @php
                        $show = $menu['permission'] === '' || hasParentPermission($menu['permission']);
                    @endphp
                    @if($show)
                        <li class="nav-item">
                            <a href="{{ !empty($menu['route']) ? route($menu['route']) : '#' }}" class="nav-link">
                                <span class="icon"><i class="{{ $menu['icon'] }}"></i></span>
                                <span class="menu-title">{{ $menu['title'] }}</span>
                            </a>
                        </li>
                    @endif
                    @continue
                @endif

                {{-- Menu with children --}}
                @php
                    $showParent = $menu['permission'] === '' || hasParentPermission($menu['permission']);
                    $visibleChildren = [];
                    foreach ($menu['children'] as $child) {
                        $childPermission = $child['permission'] ?? '';
                        if ($childPermission === '' || hasChildPermission($menu['permission'] ?? '', $childPermission)) {
                            $visibleChildren[] = $child;
                        }
                    }
                @endphp

                @if($showParent && count($visibleChildren) > 0)
                    <li class="nav-item">
                        <a href="#" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="{{ $menu['icon'] }}"></i></span>
                            <span class="menu-title">{{ $menu['title'] }}</span>
                        </a>
                        <ul class="sidemenu-nav-second-level mm-collapse">
                            @foreach($visibleChildren as $child)
                                <li class="nav-item">
                                    <a href="{{ !empty($child['route']) ? route($child['route']) : '#' }}" class="nav-link">
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
