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

        <form class="nav-search-form d-none ml-auto d-md-block">
            <label><i class="bx bx-search"></i></label>
            <input type="text" class="form-control" placeholder="Search here..." />
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
                            <p class="mb-3 email">super Admin</p>
                        </div>
                    </div>

                    <div class="dropdown-body">
                        <ul class="profile-nav p-0 pt-3">
                            <li class="nav-item">
                                <a href="" class="nav-link"> <i class="bx bx-user"></i> <span>User Dashboard </span></a>
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
