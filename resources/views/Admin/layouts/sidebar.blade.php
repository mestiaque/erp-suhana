<!-- Start Sidemenu Area -->
<div class="sidemenu-area">
    <div class="sidemenu-header">
        <a href="" class="navbar-brand d-flex align-items-center">
            <img src="{{asset(general()->logo())}}" alt="logo" />
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
            <li class="nav-item-title">
                Main
            </li>

            @foreach(config('sidebar') as $menu)
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link has-arrow" aria-expanded="trueA">
                        <span class="icon"><i class="{{ $menu['icon'] }} {{ $menu['icon_color'] }}"></i></span>
                        <span class="menu-title">{{ $menu['title'] }}</span>
                    </a>
                    @if(isset($menu['children']) && count($menu['children']))
                        <ul class="sidemenu-nav-second-level mm-collapse mm-showA">
                            @foreach($menu['children'] as $child)
                                <li class="nav-item">
                                    <a href="{{ $child['route'] ?? '#' }}" class="nav-link">
                                        <span class="icon"><i class="{{ $child['icon'] }} {{ $child['icon_color'] }}"></i></span>
                                        <span class="menu-title">{{ $child['title'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach

        </ul>
    </div>
</div>
<!-- END: Main Menu-->
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush
@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
@endpush
