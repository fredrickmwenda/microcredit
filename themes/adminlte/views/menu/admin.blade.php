<div class="leftside-menu">
   <a href="{{url('/')}}" class="logo logo-light">
   <span class="logo-lg">
     
        <img src="{{asset('themes/adminlte/img/logo-white.png')}}" alt="{{\Modules\Setting\Entities\Setting::where('setting_key','core.company_name')->first()->setting_value}}" />
</span>
    </a>

    <!-- Brand Logo Light -->
    <!-- <a href="index.html" class="logo logo-light">
        <span class="logo-lg">
            <img src="assets/images/logo.png" alt="logo">
        </span>
        <span class="logo-sm">
            <img src="assets/images/logo-sm.png" alt="small logo">
        </span>
    </a> -->

    <!-- Brand Logo Dark -->
    <a href="index.html" class="logo logo-dark">
    <span class="logo-lg">
    <img src="{{asset('themes/adminlte/img/logo-white.png')}}" alt="{{\Modules\Setting\Entities\Setting::where('setting_key','core.company_name')->first()->setting_value}}" />
    </span>
        <!-- <span class="logo-lg">
            <img src="assets/images/logo-dark.png" alt="dark logo">
        </span>
        <span class="logo-sm">
            <img src="assets/images/logo-sm.png" alt="small logo">
        </span> -->
    </a>
    <!-- Full Sidebar Menu Close Button -->
    <div class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>

    <ul class="side-nav">
    @foreach(\Modules\Core\Entities\Menu::with('children')->where('is_parent', 1)->orderBy('menu_order', 'asc')->get() as $parent)
        @if($parent->children->count() == 0)
            @if(!empty($parent->permissions))
                @can($parent->permissions)
                    <li class="side-nav-item">
                        <a href="{{ url($parent->url) }}" class="side-nav-link @if(Request::is($parent->url)) active @endif">
                            <i class="nav-icon fas {{ $parent->icon }}"></i>
                            <span> {{ $parent->name }} </span>
                        </a>
                    </li>
                @endcan
            @else
                <li class="side-nav-item">
                    <a href="{{ url($parent->url) }}" class="side-nav-link @if(Request::is($parent->url)) active @endif">
                        <i class="nav-icon fas {{ $parent->icon }}"></i>
                        <span> {{ $parent->name }} </span>
                    </a>
                </li>
            @endif
        @else
            @if(!empty($parent->permissions))
                @can($parent->permissions)
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#parentMenu{{ $parent->id }}" aria-expanded="false" aria-controls="parentMenu{{ $parent->id }}" class="side-nav-link @if(Request::is($parent->url.'*')) active @endif">
                            <i class="nav-icon fas {{ $parent->icon }}"></i>
                            <span> {{ $parent->name }} </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse @if(Request::is($parent->url.'*')) show @endif" id="parentMenu{{ $parent->id }}">
                            <ul class="side-nav-second-level">
                                @foreach($parent->children as $child)
                                    @if(!empty($child->permissions))
                                        @can($child->permissions)
                                            <li>
                                                <a href="{{ url($child->url) }}" class="@if(Request::is($child->url)) active @endif">
                                                    <i class="nav-icon fas {{ $child->icon }}"></i>
                                                    {{ $child->name }}
                                                </a>
                                            </li>
                                        @endcan
                                    @else
                                        <li>
                                            <a href="{{ url($child->url) }}" class="@if(Request::is($child->url)) active @endif">
                                                <i class="nav-icon fas {{ $child->icon }}"></i>
                                                {{ $child->name }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                                @if(strtolower($parent->name) === 'loans' || $parent->url === 'loan')
                                    <li>
                                        <a href="{{ url('loan?status=rejected') }}" class="@if(Request::is('loan') && request('status') == 'rejected') active @endif">
                                            <i class="nav-icon fas fa-times-circle"></i>
                                            Rejected Loans
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('loan?status=closed') }}" class="@if(Request::is('loan') && request('status') == 'closed') active @endif">
                                            <i class="nav-icon fas fa-check-circle"></i>
                                            Completed (Closed Loans)
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endcan
            @else
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#parentMenu{{ $parent->id }}" aria-expanded="false" aria-controls="parentMenu{{ $parent->id }}" class="side-nav-link @if(Request::is($parent->url.'*')) active @endif">
                        <i class="nav-icon fas {{ $parent->icon }}"></i>
                        <span> {{ $parent->name }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse @if(Request::is($parent->url.'*')) show @endif" id="parentMenu{{ $parent->id }}">
                        <ul class="side-nav-second-level">
                            @foreach($parent->children as $child)
                                @if(!empty($child->permissions))
                                    @can($child->permissions)
                                        <li>
                                            <a href="{{ url($child->url) }}" class="@if(Request::is($child->url)) active @endif">
                                                <i class="nav-icon fas {{ $child->icon }}"></i>
                                                {{ $child->name }}
                                            </a>
                                        </li>
                                    @endcan
                                @else
                                    <li>
                                        <a href="{{ url($child->url) }}" class="@if(Request::is($child->url)) active @endif">
                                            <i class="nav-icon fas {{ $child->icon }}"></i>
                                            {{ $child->name }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                            @if(strtolower($parent->name) === 'loans' || $parent->url === 'loan')
                                <li>
                                    <a href="{{ url('loan?status=rejected') }}" class="@if(Request::is('loan') && request('status') == 'rejected') active @endif">
                                        <i class="nav-icon fas fa-times-circle"></i>
                                        Rejected Loans
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('loan?status=closed') }}" class="@if(Request::is('loan') && request('status') == 'closed') active @endif">
                                        <i class="nav-icon fas fa-check-circle"></i>
                                        Completed (Closed Loans)
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
        @endif
    @endforeach
</ul>

        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>


