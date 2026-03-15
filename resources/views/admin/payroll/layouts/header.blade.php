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
                <a href="{{route('admin.usersCustomer')}}" class="nav-link" data-toggle="tooltip" data-placement="bottom" title="Employee">
                    <i class="bx bx-group"></i>
                </a>
            </li>


            @isset(json_decode(Auth::user()->permission->permission, true)['pi']['view'])
            <li class="nav-item">
                <a href="{{route('admin.sales')}}" class="nav-link" data-toggle="tooltip" data-placement="bottom" title="Sales">
                    <i class="bx bxs-file-export"></i>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{route('admin.piInvoices')}}" class="nav-link" data-toggle="tooltip" data-placement="bottom" title="Invoices">
                    <i class="bx bx-file"></i>
                </a>
            </li>
            @endisset




        </ul>

        <!--<form class="nav-search-form d-none ml-auto d-md-block">-->
        <!--    <label><i class="bx bx-search"></i></label>-->
        <!--    <input type="text" class="form-control" placeholder="Search here..." />-->
        <!--</form>-->

        <ul class="navbar-nav right-nav ml-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link bx-fullscreen-btn" id="fullscreen-button">
                    <i class="bx bx-fullscreen"></i>
                </a>
            </li>
            <li class="nav-item dropdown profile-nav-item">
                <a href="#" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="menu-profile">
                        <span class="name">Hi! {{Str::limit(Auth::user()->name,10)}} </span>
                        <img src="{{asset(Auth::user()->image())}}" class="rounded-circle" alt="{{Auth::user()->name}}" />
                    </div>
                </a>

                <div class="dropdown-menu">
                    <div class="dropdown-header d-flex flex-column align-items-center">
                        <div class="figure mb-3">
                            <img src="{{asset(Auth::user()->image())}}" class="rounded-circle" alt="image" />
                        </div>

                        <div class="info text-center">
                            <span class="name">{{Auth::user()->name}}</span>
                            <p class="mb-3 email">{{Auth::user()->permission?Auth::user()->permission->name:'Authorized'}}</p>
                        </div>
                    </div>

                    <div class="dropdown-body">
                        <ul class="profile-nav p-0 pt-3">
                            @if(Auth::user()->customer)
                            <li class="nav-item">
                                <a href="{{route('customer.dashboard')}}" class="nav-link"> <i class="bx bxs-dashboard"></i> <span>Employee Dashboard</span></a>
                            </li>
                            @endif
                            <li class="nav-item">
                                <a href="{{route('admin.myProfile')}}" class="nav-link"> <i class="bx bx-user"></i> <span>My Profile </span></a>
                            </li>

                        </ul>
                    </div>

                    <div class="dropdown-footer">
                        <ul class="profile-nav">
                            <li class="nav-item">
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link"> <i class="bx bx-log-out"></i> <span>Logout </span> </a>
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

 <!-- BEGIN: Header-->
 {{--<nav class="header-navbar navbar-expand-lg navbar navbar-with-menu fixed-top navbar-semi-dark navbar-shadow">
   <div class="navbar-wrapper">
     <div class="navbar-header">
       <ul class="nav navbar-nav flex-row">
         <li class="nav-item mobile-menu d-lg-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#">
          <!-- <i class="fa-2x fas fa-bars"></i> -->
          <i class="fa-2x fas fa-arrow-alt-circle-right"></i>
          </a>
          </li>
          <li class="nav-item mr-auto">
           <a class="navbar-brand" href="{{route('admin.dashboard')}}">
            <img class="brand-logo" src="{{asset(general()->favicon())}}" style="max-height:30px;" />
            <h2 class="brand-text">NIT-B</h2>
           </a>
          </li>
         <li class="nav-item d-none d-lg-block nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse" style="    color: white;"><i class="fa-2x fas fa-bars"></i></a></li>
         <!-- <li class="nav-item d-lg-none"><a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="fa fa-ellipsis-v"></i></a></li> -->
       </ul>
     </div>
     <div class="navbar-container content">
       <div class="collapse navbar-collapse" id="navbar-mobile">
         <ul class="nav navbar-nav mr-auto float-left">

           <li class="nav-item d-none d-md-block">
            <a class="nav-link nav-link-expand" href="#">
              <i class="fas fa-compress"></i>
            </a>
          </li>
          <li class="nav-item d-none d-md-block">
            <a href="{{route('index')}}" class="nav-link">Visit Website</a>
          </li>
         </ul>
         <ul class="nav navbar-nav float-right">

           <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="javascript:void(0)" data-toggle="dropdown">
               <div class="avatar avatar-online">
                <img src="{{route('imageView2',['profile',Auth::user()->imageName(),'w'=>60,'h'=>60])}}" alt="avatar" /><i></i>
              </div>
               <span class="user-name">{{Str::limit(Auth::user()->name,15)}}</span></a>
               <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="{{route('customer.dashboard')}}" style="min-width: 220px"><i class="fas fa-th-large"></i> My Dashboard </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{route('admin.myProfile')}}" style="min-width: 220px"><i class="fa fa-user"></i> My Profile </a>

                 <div class="dropdown-divider"></div>

                 <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  <i class="fas fa-power-off"></i> Logout
                  </a>



               </div>
           </li>
         </ul>
       </div>
     </div>
   </div>
 </nav>--}}
 <!-- END: Header-->
