<div class="sidemenu-area">
    <div class="sidemenu-header">
        <a href="" class="navbar-brand d-flex align-items-center">
            <!-- সাইটের লোগো দেখানোর জন্য -->
            <img src="{{ asset(general()->logo()) }}" alt="logo" style="max-height: 50px;" />
        </a>

        <!-- বড় স্ক্রিনে দেখানোর জন্য বার্গার মেনু -->
        <div class="burger-menu d-none d-lg-block">
            <span class="top-bar"></span>
            <span class="middle-bar"></span>
            <span class="bottom-bar"></span>
        </div>

        <!-- মোবাইল স্ক্রিনে দেখানোর জন্য responsive বার্গার মেনু -->
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
                 * নিচের renderMenu ফাংশনটি recursiveভাবে কাজ করে:
                 * - $level 0 হলে সেটাকে (PARENT) হিসেবে ধরা হবে
                 * - $level 1 হলে সেটাকে (CHILD) হিসেবে ধরা হবে
                 * - $level >=2 হলে (GRANDCHILD) হিসেবে ধরা হবে
                 */

                function renderMenu($menu, $level = 0)
                {
                    // --------------------------------------
                    // 1) ভেরিয়েবল ডিফাইন
                    // --------------------------------------
                    $hasVisibleChild = false; // ভেতরের child দেখা যাবে কিনা
                    $isActive = false;        // active ক্লাস লাগবে কি না
                    $childrenHtml = '';       // child HTML এখানে জমা হবে

                    // --------------------------------------
                    // 2) যদি children থাকে → recursive render
                    // --------------------------------------
                    if (isset($menu['children'])) {
                        foreach ($menu['children'] as $child) {

                            // recursive call (level এক বাড়িয়ে)
                            $result = renderMenu($child, $level + 1);

                            if ($result['show']) {
                                $hasVisibleChild = true;   // অন্তত একটি child দৃশ্যমান
                                if ($result['active']) {
                                    $isActive = true;      // child active হলে parent active
                                }
                                $childrenHtml .= $result['html'];  // child HTML যোগ
                            }
                        }
                    }

                    // --------------------------------------
                    // ৩) menu permissions
                    // --------------------------------------
                    $permission = $menu['permission'] ?? '';

                    /**
                     * 🔥 FIXED SHOW LOGIC 🔥
                     *
                     * RULE:
                     *  - PARENT → permission না থাকলেও show হবে
                     *  - CHILD/GRANDCHILD → permission না থাকলে show হবে না যদি কোনো visible child না থাকে
                     */

                    // if ($permission !== '') {
                    //     $show = hasChildPermission($permission) || $hasVisibleChild;
                    // } else {
                    //     if ($level == 0) {
                    //         // (PARENT) → শুধুমাত্র show হবে যদি child visible থাকে
                    //         $show = $hasVisibleChild;
                    //     } elseif ($level == 1) {
                    //         // (CHILD) → permission='' হলে দেখাবে শুধুমাত্র যখন তার কোন visible child আছে
                    //         $show = $hasVisibleChild;
                    //     } else {
                    //         // (GRANDCHILD / level>=2) → permission='' হলেও show হবে (NEW RULE)
                    //         $show = true;
                    //     }
                    // }

                    if ($permission !== '') {

                        // If permission IS defined → MUST CHECK PERMISSION
                        $show = hasChildPermission($permission);

                    } else {

                        // NO PERMISSION SET
                        if ($level == 0) {

                            // PARENT LEVEL

                            if (isset($menu['children'])) {
                                // Parent WITH children → show only if any child is visible
                                $show = $hasVisibleChild;
                            } else {
                                // Single parent menu → always show
                                $show = true;
                            }

                        } elseif ($level == 1) {

                            // CHILD LEVEL (must have children to show)
                            $show = $hasVisibleChild;

                        } else {

                            // GRANDCHILD (level >= 2) always show if no permission
                            $show = true;
                        }
                    }





                    // --------------------------------------
                    // ৪) Active route check
                    // --------------------------------------
                    $route = $menu['route'] ?? '';
                    $pattern = trim($route, '/');
                    $prefix = $pattern . '/*';


                    $selfActive = !empty($route) && (request()->is($pattern) || request()->is($prefix));

                    if ($selfActive) {
                        $isActive = true;
                    }

                    // --------------------------------------
                    // ৬) এখন HTML তৈরি
                    // --------------------------------------
                    $html = '';

                    // যদি show = true হয় তবেই HTML বানানো হবে
                    if ($show) {

                        $hasChildren = isset($menu['children']) && $hasVisibleChild;

                        $activeClass = $isActive ? 'mm-active' : '';
                        $collapseClass = ($hasChildren && $isActive) ? 'mm-show' : '';
                        $linkClass = $hasChildren ? 'collapsed-nav-link nav-link' : 'nav-link';

                        $href = !empty($menu['route']) ? url($menu['route']) : 'javascript:void(0)';

                        // li start
                        $html .= '<li class="nav-item ' . $activeClass . '">';

                        // <a> link
                        $html .= '<a href="' . $href . '" class="' . $linkClass . '">';
                        $html .= '<span class="icon"><i class="' . ($menu['icon'] ?? '') . '"></i></span>';

                        // menu title + label
                        $html .= '<span class="menu-title">' . ($menu['title'] ?? '').'</span>';

                        $html .= '</a>';

                        // যদি চাইল্ড থাকে → inner <ul>
                        if ($hasChildren) {
                            $html .= '<ul class="sidemenu-nav-second-level mm-collapse ' . $collapseClass . '">';
                            $html .= $childrenHtml; // recursive child HTML
                            $html .= '</ul>';
                        }

                        $html .= '</li>'; // li end
                    }

                    // --------------------------------------
                    // ৭) recursive return
                    // --------------------------------------
                    return [
                        'show'   => $show,
                        'active' => $isActive,
                        'html'   => $html
                    ];
                }
            @endphp


            {{-- গ্রুপ অনুযায়ী মেনু রেন্ডার --}}
            @foreach(config('sidebar') as $group)

                @php
                    // check if any parent in this group will be visible
                    $hasVisibleParent = false;
                    foreach($group as $key => $menu) {
                        if($key === 'group_title') continue;
                        $result = renderMenu($menu, 0);
                        if($result['show']) {
                            $hasVisibleParent = true;
                            break;
                        }
                    }
                @endphp

                {{-- যদি কোনো parent visible থাকে --}}
                @if($hasVisibleParent)

                    {{-- group title দেখানো --}}
                    @if(isset($group['group_title']) && $group['group_title'])
                        <li class="nav-item-title">{{ $group['group_title'] }}</li>
                    @endif

                    {{-- visible parent গুলো render করা --}}
                    @foreach($group as $key => $menu)
                        @if($key === 'group_title') @continue @endif
                        @php $result = renderMenu($menu, 0); @endphp
                        {!! $result['html'] !!}
                    @endforeach

                @endif

            @endforeach


        </ul>
    </div>
</div>

<style>
    /* If parent is active, child links normal color */
.sidemenu-area .sidemenu-body .sidemenu-nav
    .nav-item .sidemenu-nav-second-level .nav-item .nav-link {
    color: #7e7e7e !important;
    background-color: transparent !important;
}
.sidemenu-area .sidemenu-body .sidemenu-nav
    .nav-item .sidemenu-nav-second-level .nav-item .nav-link svg{
    color: #7e7e7e !important;
    background-color: transparent !important;
}

/* Child links active state */
.sidemenu-area .sidemenu-body .sidemenu-nav
    .nav-item .sidemenu-nav-second-level .nav-item.mm-active > .nav-link {
    color: #e1000a !important; /* only when child itself active */
}
.sidemenu-area .sidemenu-body .sidemenu-nav
    .nav-item .sidemenu-nav-second-level .nav-item.mm-active > .nav-link svg{
    color: #e1000a !important; /* only when child itself active */
}

</style>
