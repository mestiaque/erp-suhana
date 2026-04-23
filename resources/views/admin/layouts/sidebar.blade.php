<div class="sidemenu-area">
    <div class="sidemenu-header">
        <a href="{{ route('admin.dashboard') }}" class="navbar-brand d-flex align-items-center">
            <!-- Site logo -->
            <img src="{{ asset(general()->logo()) }}" alt="logo" style="max-height: 50px;" />
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
            @php
                /**
                 * Sidebar menu rendering - ONLY leaf menu permission checked
                 * Parent menu permission key is ALWAYS IGNORED
                 */
                function renderMenu($menu) {
                        $hasVisibleChild = false;
                        $isActive = false;
                        $childrenHtml = '';

                        // Child recursive render
                        if (isset($menu['children']) && is_array($menu['children'])) {
                            foreach ($menu['children'] as $child) {
                                $result = renderMenu($child);
                                if ($result['show']) {
                                    $hasVisibleChild = true;
                                    if ($result['active']) $isActive = true;
                                    $childrenHtml .= $result['html'];
                                }
                            }
                        }

                        // --------- এই অংশটা কপি করুন! --------
                        // শুধু LEAF node-এ permission check হবে
                        if (isset($menu['children'])) {
                            $show = $hasVisibleChild;
                        } else {
                            $permission = $menu['permission'] ?? '';
                            $show = !empty($permission) ? hasChildPermission($permission) : true;
                        }

                    // Active route check
                    $route = $menu['route'] ?? '';
                    $pattern = trim($route, '/');
                    $prefix = $pattern . '/*';
                    $selfActive = !empty($route) && (request()->is($pattern) || request()->is($prefix));
                    if ($selfActive) $isActive = true;

                    // Only show HTML if show = true
                    $html = '';
                    if ($show) {
                        $hasChildren = isset($menu['children']) && $hasVisibleChild;
                        $activeClass = $isActive ? 'mm-active' : '';
                        $collapseClass = ($hasChildren && $isActive) ? 'mm-show' : '';
                        $linkClass = $hasChildren ? 'collapsed-nav-link nav-link' : 'nav-link';
                        $href = !empty($menu['route']) ? url($menu['route']) : 'javascript:void(0)';

                        $html .= '<li class="nav-item ' . $activeClass . '">';
                        $html .= '<a href="' . $href . '" class="' . $linkClass . '">';
                        $html .= '<span class="icon"><i class="' . ($menu['icon'] ?? '') . '"></i></span>';
                        $html .= '<span class="menu-title">' . e($menu['title'] ?? '') . '</span>';
                        $html .= '</a>';

                        if ($hasChildren) {
                            $html .= '<ul class="sidemenu-nav-second-level mm-collapse ' . $collapseClass . '">';
                            $html .= $childrenHtml;
                            $html .= '</ul>';
                        }
                        $html .= '</li>';
                    }
                    return [
                        'show'   => $show,
                        'active' => $isActive,
                        'html'   => $html
                    ];
                }
            @endphp

            {{-- Sidebar configuration loading and sorting --}}
            @php
                $sidebarGroups = config('sidebar', []);
                usort($sidebarGroups, function ($a, $b) {
                    $minA = PHP_INT_MAX;
                    $minB = PHP_INT_MAX;
                    foreach ($a as $k => $item) {
                        if ($k !== 'group_title' && is_array($item)) {
                            $minA = min($minA, $item['order'] ?? PHP_INT_MAX);
                        }
                    }
                    foreach ($b as $k => $item) {
                        if ($k !== 'group_title' && is_array($item)) {
                            $minB = min($minB, $item['order'] ?? PHP_INT_MAX);
                        }
                    }
                    return $minA <=> $minB;
                });
            @endphp


            @foreach($sidebarGroups as $group)
                @php
                    $hasVisibleParent = false;
                    foreach($group as $key => $menu) {
                        if($key === 'group_title') continue;
                        $result = renderMenu($menu);
                        if($result['show']) {
                            $hasVisibleParent = true;
                            break;
                        }
                    }
                @endphp

                @if($hasVisibleParent)
                    @if(isset($group['group_title']) && $group['group_title'])
                        <li class="nav-item-title">{{ $group['group_title'] }}</li>
                    @endif
                    @foreach($group as $key => $menu)
                        @if($key === 'group_title') @continue @endif
                        @php $result = renderMenu($menu); @endphp
                        {!! $result['html'] !!}
                    @endforeach
                @endif
            @endforeach
        </ul>
    </div>
</div>

<style>
    /* Sidebar CSS: color for child/active/normal state */
    .sidemenu-area .sidemenu-body .sidemenu-nav
        .nav-item .sidemenu-nav-second-level .nav-item .nav-link {
        color: #7e7e7e !important;
        background-color: transparent !important;
    }
    .sidemenu-area .sidemenu-body .sidemenu-nav
        .nav-item .sidemenu-nav-second-level .nav-item .nav-link svg {
        color: #7e7e7e !important;
        background-color: transparent !important;
    }
    .sidemenu-area .sidemenu-body .sidemenu-nav
        .nav-item .sidemenu-nav-second-level .nav-item.mm-active > .nav-link {
        color: #e1000a !important;
    }
    .sidemenu-area .sidemenu-body .sidemenu-nav
        .nav-item .sidemenu-nav-second-level .nav-item.mm-active > .nav-link svg {
        color: #e1000a !important;
    }
</style>

<script>
    (function () {
        function scrollActiveSidebarItemIntoView() {
            var sidebarNav = document.getElementById('sidemenu-nav');
            if (!sidebarNav) return;
            var activeCandidates = sidebarNav.querySelectorAll('.nav-item.mm-active > .nav-link, .nav-item.mm-active');
            if (!activeCandidates.length) return;
            var activeElement = activeCandidates[activeCandidates.length - 1];
            var simplebarScroller = sidebarNav.querySelector('.simplebar-content-wrapper');
            var fallbackScroller = sidebarNav.closest('.sidemenu-body');
            var scroller = simplebarScroller || fallbackScroller;
            if (!scroller) return;
            var targetRect = activeElement.getBoundingClientRect();
            var scrollerRect = scroller.getBoundingClientRect();
            var isOutOfView = targetRect.top < scrollerRect.top || targetRect.bottom > scrollerRect.bottom;
            if (!isOutOfView) return;
            var targetTop = targetRect.top - scrollerRect.top + scroller.scrollTop;
            var centeredScrollTop = targetTop - (scroller.clientHeight / 2) + (activeElement.offsetHeight / 2);
            scroller.scrollTop = Math.max(0, centeredScrollTop);
        }
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(scrollActiveSidebarItemIntoView, 200);
            setTimeout(scrollActiveSidebarItemIntoView, 600);
        });
    })();
</script>
