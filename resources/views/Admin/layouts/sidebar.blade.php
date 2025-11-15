<!-- Start Sidemenu Area -->
<div class="sidemenu-area">
    <div class="sidemenu-header">
        <a href="" class="navbar-brand d-flex align-items-center">
            <img src="" alt="logo" />
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
            <li class="nav-item {{Request::is('admin/dashboard')? 'mm-active' : ''}}">
                <a href="#" class="nav-link">
                    <span class="icon"><i class='bx bxs-dashboard'></i></span>
                    <span class="menu-title">Dashboard </span>
                </a>
            </li>
            <li class="nav-item {{Request::is('admin/my-profile')? 'mm-active' : ''}}">
                <a href="#" class="nav-link">
                    <span class="icon"><i class="bx bx-user"></i></span>
                    <span class="menu-title">My Profile </span>
                </a>
            </li>

        </ul>
    </div>
</div>
   <!-- END: Main Menu-->