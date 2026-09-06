<!-- Top Navbar Area -->
<nav class="navbar top-navbar navbar-expand">
    <div class="collapse navbar-collapse" id="navbarSupportContent">
        <div class="responsive-burger-menu d-block d-lg-none">
            <span class="top-bar"></span>
            <span class="middle-bar"></span>
            <span class="bottom-bar"></span>
        </div>

        <ul class="navbar-nav left-nav align-items-center">
            <li class="nav-item">
                <a href="#" class="nav-link" data-toggle="tooltip" data-placement="bottom" title="Employee">
                    <i class="bx bx-group"></i>
                </a>
            </li>
        </ul>

        <form class="nav-search-form d-none ml-auto d-md-block" autocomplete="off" onsubmit="return false;">
            <label style="color: green"><i class="bx bx-search"></i></label>
            <input type="text" id="menuSearchInput" class="form-control" placeholder="Search here..." />
            <div id="menuSearchResults" class="menu-search-results" style="display:none;"></div>
        </form>

        <ul class="navbar-nav right-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link bx-fullscreen-btn" id="fullscreen-button">
                    <i class="bx bx-fullscreen"></i>
                </a>
            </li>
            <li class="nav-item dropdown profile-nav-item">
                <a href="#" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="menu-profile">
                        <span class="name">{{ Auth::user()->name }}</span>
                        <img src="{{asset(Auth::user()->image())}}" class="rounded-circle" alt="Admin" />
                    </div>
                </a>

                <div class="dropdown-menu">
                    <div class="dropdown-header d-flex flex-column align-items-center">
                        <div class="figure mb-3">
                            <img src="{{asset(Auth::user()->image())}}" class="rounded-circle" alt="image" />
                        </div>

                        <div class="info text-center">
                            <span class="name">{{ Auth::user()->name }}</span>
                            <p class="mb-3 email">{{ Auth::user()->permission?->name ?? '' }}</p>
                        </div>
                    </div>

                    <div class="dropdown-body">
                        <ul class="profile-nav p-0 pt-3">

                            <li class="nav-item">
                                @php
                                    $user = Auth::user();
                                    $currentRoute = \Request::route()->getName();
                                @endphp

                                {{-- যদি user দুই role এর মধ্যে থাকে এবং এখন admin dashboard এ থাকে --}}
                                @if($user->staff && $user->admin && \Str::contains($currentRoute, 'admin.'))
                                    <a href="{{ route('staff.dashboard') }}" class="nav-link">
                                        <i class="bx bx-home"></i>
                                        <span>Staff Dashboard</span>
                                    </a>
                                {{-- যদি user দুই role এর মধ্যে থাকে এবং এখন staff dashboard এ থাকে --}}
                                @elseif($user->staff && $user->admin && \Str::contains($currentRoute, 'staff.'))
                                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                                        <i class="bx bx-user"></i>
                                        <span>Admin Dashboard</span>
                                    </a>
                                @endif
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.myProfile') }}" class="nav-link"> <i class="bx bx-user"></i> <span>Profile </span></a>
                            </li>
                            <li class="nav-item">
                                <a href="" class="nav-link"> <i class="bx bx-bell"></i> <span>Reminder List <span style="background: #f80e5d;color: white;padding: 2px 10px;border-radius: 5px;margin-left: 5px;">0</span></span></a>
                            </li>
                        </ul>
                    </div>

                    <div class="dropdown-footer">
                        <ul class="profile-nav">
                            <li class="nav-item">
                                <a href="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link"> <i class="bx bx-log-out"></i> <span>Logout </span> </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- End Top Navbar Area -->

{{--
    Deliberately an inline <style> here, NOT @push('css') — app.blade.php's
    @stack('css') lives in <head> (rendered before the body ever reaches
    this @include'd partial), so anything pushed from here would register
    too late to ever be flushed into the page. An inline tag has no such
    ordering dependency.
--}}
<style>
    .nav-search-form { position: relative; }
    .menu-search-results {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #eef0f4;
        border-radius: 12px;
        box-shadow: 0 12px 30px rgba(20, 30, 60, 0.14);
        max-height: 380px;
        overflow-y: auto;
        z-index: 1060;
        padding: 6px;
        animation: menuSearchFadeIn .12s ease-out;
    }
    @keyframes menuSearchFadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .menu-search-results a.menu-search-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 9px 10px;
        color: #333;
        text-decoration: none;
        border-radius: 8px;
        transition: background-color .12s ease;
    }
    .menu-search-results a.menu-search-item + a.menu-search-item {
        margin-top: 2px;
    }
    .menu-search-results a.menu-search-item:hover,
    .menu-search-results a.menu-search-item.active {
        background: #eef3ff;
        color: #0047ab;
    }
    .menu-search-results .menu-search-icon {
        flex: 0 0 32px;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #eef3ff;
        color: #0047ab;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }
    .menu-search-results a.menu-search-item:hover .menu-search-icon,
    .menu-search-results a.menu-search-item.active .menu-search-icon {
        background: #0047ab;
        color: #fff;
    }
    .menu-search-results .menu-search-text {
        min-width: 0;
        overflow: hidden;
    }
    .menu-search-results .menu-search-title {
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .menu-search-results .menu-search-breadcrumb {
        font-size: 11px;
        color: #939aad;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .menu-search-results .menu-search-empty {
        padding: 14px 10px;
        font-size: 12px;
        color: #939aad;
        text-align: center;
    }
</style>

@push('js')
<script>
(function () {
    var $input = $('#menuSearchInput');
    var $results = $('#menuSearchResults');
    var searchUrl = '{{ route('admin.menuSearch') }}';
    var debounceTimer = null;
    var activeIndex = -1;

    function hideResults() {
        $results.hide().empty();
        activeIndex = -1;
    }

    function renderResults(items) {
        $results.empty();
        activeIndex = -1;

        if (!items.length) {
            $results.append('<div class="menu-search-empty">No matching menu found.</div>');
            $results.show();
            return;
        }

        items.forEach(function (item) {
            var $a = $('<a>', {
                'class': 'menu-search-item',
                href: item.route,
            });
            var $icon = $('<span>', { 'class': 'menu-search-icon' }).append('<i class="' + item.icon + '"></i>');
            var $text = $('<span>', { 'class': 'menu-search-text' });
            $text.append($('<span>', { 'class': 'menu-search-title', text: item.title }));
            if (item.breadcrumb) {
                $text.append($('<span>', { 'class': 'menu-search-breadcrumb', text: item.breadcrumb }));
            }
            $a.append($icon, $text);
            $results.append($a);
        });

        $results.show();
    }

    $input.on('input', function () {
        var term = $(this).val().trim();

        clearTimeout(debounceTimer);

        if (term.length < 2) {
            hideResults();
            return;
        }

        debounceTimer = setTimeout(function () {
            $.get(searchUrl, { q: term }).done(function (items) {
                renderResults(items);
            });
        }, 250);
    });

    $input.on('keydown', function (e) {
        var $items = $results.find('.menu-search-item');
        if (!$items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = Math.min(activeIndex + 1, $items.length - 1);
            $items.removeClass('active').eq(activeIndex).addClass('active');
            $items.get(activeIndex).scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
            $items.removeClass('active').eq(activeIndex).addClass('active');
            $items.get(activeIndex).scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            e.preventDefault();
            var $target = activeIndex >= 0 ? $items.eq(activeIndex) : $items.first();
            window.location.href = $target.attr('href');
        } else if (e.key === 'Escape') {
            hideResults();
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.nav-search-form').length) {
            hideResults();
        }
    });
})();
</script>
@endpush
