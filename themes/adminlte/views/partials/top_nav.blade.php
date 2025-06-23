<!-- ========== Topbar Start ========== -->
<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-lg-2 gap-1">

            <!-- Topbar Brand Logo -->
            <div class="logo-topbar">
                <!-- Logo light -->
                <a href="{{url('/')}}" class="logo-light">
                    <!-- <span class="logo-lg">
                        <img src="assets/images/logo.png" alt="logo">
                    </span>
                    <span class="logo-sm">
                        <img src="assets/images/logo-sm.png" alt="small logo">
                    </span> -->

                    <img src="{{asset('themes/adminlte/img/logo-white.png')}}" alt="{{\Modules\Setting\Entities\Setting::where('setting_key','core.company_name')->first()->setting_value}}" />

                </a>

                <!-- Logo Dark -->
                <a href="{{url('/')}}" class="logo-dark">
                    <!-- <span class="logo-lg">
                        <img src="assets/images/logo-dark.png" alt="dark logo">
                    </span>
                    <span class="logo-sm">
                        <img src="assets/images/logo-sm.png" alt="small logo">
                    </span> -->
                    <img src="{{asset('themes/adminlte/img/logo-white.png')}}" alt="{{\Modules\Setting\Entities\Setting::where('setting_key','core.company_name')->first()->setting_value}}" />

                </a>
            </div>

            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu">
                <i class="ri-menu-2-fill"></i>
            </button>

            <!-- Topbar Search Form -->
            <div class="app-search dropdown d-none d-lg-block">
                <form>
                    <div class="input-group">
                        <input type="search" class="form-control dropdown-toggle" placeholder="Search..." id="top-search">
                        <span class="ri-search-line search-icon"></span>
                    </div>
                </form>

                <div class="dropdown-menu dropdown-menu-animated dropdown-lg" id="search-dropdown">
                    <!-- item-->
                    <!-- <div class="dropdown-header noti-title">
                        <h5 class="text-overflow mb-1">Found <b class="text-decoration-underline">08</b> results</h5>
                    </div> -->

                    <!-- item-->
                    <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="ri-file-chart-line fs-16 me-1"></i>
                        <span>Analytics Report</span>
                    </a> -->


                    <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="ri-lifebuoy-line fs-16 me-1"></i>
                        <span>How can I help you?</span>
                    </a>

                    
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="ri-user-settings-line fs-16 me-1"></i>
                        <span>User profile settings</span>
                    </a>

                  
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow mt-2 mb-1 text-uppercase">Users</h6>
                    </div>

                    <div class="notification-list">
                      
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <div class="d-flex">
                                <img class="d-flex me-2 rounded-circle" src="assets/images/users/avatar-2.jpg" alt="Generic placeholder image" height="32">
                                <div class="w-100">
                                    <h5 class="m-0 fs-14">Erwin Brown</h5>
                                    <span class="fs-12 mb-0">UI Designer</span>
                                </div>
                            </div>
                        </a>

                       
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <div class="d-flex">
                                <img class="d-flex me-2 rounded-circle" src="assets/images/users/avatar-5.jpg" alt="Generic placeholder image" height="32">
                                <div class="w-100">
                                    <h5 class="m-0 fs-14">Jacob Deo</h5>
                                    <span class="fs-12 mb-0">Developer</span>
                                </div>
                            </div>
                        </a>
                    </div> -->
                </div>
            </div>
        </div>

        <ul class="topbar-menu d-flex align-items-center gap-3">
            <li class="dropdown d-lg-none">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="ri-search-line fs-22"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                    <form class="p-3">
                        <input type="search" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                    </form>
                </div>
            </li>



            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="ri-notification-3-line fs-22"></i>
                    <!-- <span class="badge badge-warning navbar-badge">{{Auth::user()->unreadNotifications()->count()}}</span> -->
                    @if(Auth::user()->unreadNotifications()->count() > 0)
                    <span class="noti-icon-badge">{{ Auth::user()->unreadNotifications()->count()}}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                    <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                        <div class="row align-items-center">
                            <div class="col">


                                <h6 class="m-0 fs-16 fw-semibold"> {{Auth::user()->unreadNotifications()->count()}} {{trans_choice('core::general.notification',2)}}</h6>
                            </div>
                            <div class="col-auto">
                                <a href="javascript: void(0);" class="text-dark text-decoration-underline">
                                    <small>Clear All</small>
                                </a>
                            </div>
                        </div>
                    </div>

                    @php
                    use Carbon\Carbon;

                    $groupedNotifications = Auth::user()->unreadNotifications->groupBy(function($date) {
                    return Carbon::parse($date->created_at)->format('Y-m-d');
                    });
                    @endphp

                    <div style="max-height: 300px;" data-simplebar>
                        @foreach($groupedNotifications as $date => $notifications)
                        <h5 class="text-muted fs-12 fw-bold p-2 text-uppercase mb-0">
                            {{ Carbon::parse($date)->isToday() ? 'Today' : (Carbon::parse($date)->isYesterday() ? 'Yesterday' : Carbon::parse($date)->format('d M Y')) }}
                        </h5>
                        @foreach($notifications as $notification)
                        <a href="@if(!empty($notification->data['link'])) {{url($notification->data['link'])}} @else {{url('user/profile/notification/'.$notification->id.'/show')}} @endif" class="dropdown-item p-0 notify-item {{ $notification->read_at ? 'read-noti' : 'unread-noti' }} card m-0 shadow-none">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="notify-icon bg-info">
                                            <i class="ri-user-add-line fs-18"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 text-truncate ms-2">
                                        @if(!empty($notification->data['message']))
                                        <h3 class="noti-item-title fw-semibold fs-14">
                                            {{$notification->data['message']}} <small class="fw-normal text-muted float-end ms-1">{{ $notification->created_at->diffForHumans() }}</small>
                                        </h3>

                                        @else
                                        <h3 class="noti-item-title fw-semibold fs-14">{{$notification->data['type']}} <small class="fw-normal text-muted float-end ms-1">{{ $notification->created_at->diffForHumans() }}</small></h3>


                                        @endif
                                        <!-- <small class="noti-item-subtitle text-muted"></small> -->
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                        @endforeach
                    </div>



                    <!-- All-->
                    <a href="{{url('user/profile/notification')}}" class="dropdown-item text-center text-primary text-decoration-underline fw-bold notify-item border-top border-light py-2">
                        View All
                    </a>

                </div>
            </li>


            <!-- Theme settings -->
            <li class="d-none d-sm-inline-block">
                <a class="nav-link" data-bs-toggle="offcanvas" href="#theme-settings-offcanvas">
                    <i class="ri-settings-3-line fs-22"></i>
                </a>
            </li>

            <!-- Dark Mode settings -->
            <li class="d-none d-sm-inline-block">
                <div class="nav-link" id="light-dark-mode">
                    <i class="ri-moon-line fs-22"></i>
                </div>
            </li>


            <!-- Full Screen settings -->
            <li class="d-none d-md-inline-block">
                <a class="nav-link" href="" data-bs-toggle="fullscreen">
                    <i class="ri-fullscreen-line fs-22"></i>
                </a>
            </li>

            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="account-user-avatar">
                        @if(!empty(Auth::user()->photo))
                        <img src="{{asset('storage/uploads/'.Auth::user()->photo)}}" alt="user-image" width="32" class="rounded-circle">
                        @else
                        <img src="{{asset('assets/images/users/avatar-1.jpg')}}" alt="user-image" width="32" class="rounded-circle">
                        @endif
                    </span>
                    <span class="d-lg-flex flex-column gap-1 d-none">
                        <h5 class="my-0">{{Auth::user()->full_name}}</h5>
                        <!-- <h6 class="my-0 fw-normal">Founder</h6> -->
                    </span>


                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                    <!-- item-->
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome !</h6>
                    </div>

                    <!-- item-->
                    <a href="{{url('user/profile')}}" class="dropdown-item">
                        <i class="ri-account-circle-line fs-18 align-middle me-1"></i>
                        <span>My Account</span>
                    </a>

                    <!-- item-->
                    <!-- <a href="pages-profile.html" class="dropdown-item">
                        <i class="ri-settings-4-line fs-18 align-middle me-1"></i>
                        <span>Settings</span>
                    </a> -->

                    <!-- item-->
                    <!-- <a href="pages-faq.html" class="dropdown-item">
                        <i class="ri-customer-service-2-line fs-18 align-middle me-1"></i>
                        <span>Support</span>
                    </a> -->

                    <!-- item-->
                    <!-- <a href="auth-lock-screen.html" class="dropdown-item">
                        <i class="ri-lock-password-line fs-18 align-middle me-1"></i>
                        <span>Lock Screen</span>
                    </a> -->

                    <!-- item-->
                    <a href="auth-logout-2.html" class="dropdown-item">
                        <i class="ri-logout-box-line fs-18 align-middle me-1"></i>
                        <span>Logout</span>
                    </a>
                    <a href="#" onclick="logout()" class="dropdown-item">
                        <i class="ri-logout-box-line fs-18 align-middle me-1"></i>
                        <span>{{trans_choice('core::general.logout',1)}}</span>
                    </a>
                    <form action="{{url('logout')}}" method="post" id="logout_form">
                        {{csrf_field()}}
                    </form>
                    <script>
                        function logout() {
                            $("#logout_form").submit();
                        }
                    </script>
                </div>
            </li>
        </ul>
    </div>
</div>
<!-- ========== Topbar End ========== -->