<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{url('/')}}" class="brand-link">
        @if(!empty($logo=\Modules\Setting\Entities\Setting::where('setting_key','core.company_logo')->first()->setting_value))
            <!--<img class="brand-image img-circle elevation-3" src="{{asset('storage/uploads/'.$logo)}}"-->
            <!--     srcset="{{asset('storage/uploads/'.$logo)}} 2x"-->
            <!--     alt="logo">-->
        @else
            <!--<span class="brand-text font-weight-light">{{\Modules\Setting\Entities\Setting::where('setting_key','core.company_name')->first()->setting_value}}</span>-->
        @endif
        <img src="{{asset('themes/adminlte/img/logo-white.png')}}" alt="{{\Modules\Setting\Entities\Setting::where('setting_key','core.company_name')->first()->setting_value}}" />
    </a>
    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true" style="margin-top: 23% !important;">
                @foreach(\Modules\Core\Entities\Menu::with('children')->where('is_parent',1)->orderBy('menu_order','asc')->get() as $parent)
                    @if($parent->children->count()==0)
                        @if(!empty($parent->permissions))
                            @can($parent->permissions)
                                <li class="nav-item">
                                    <a href="{{url($parent->url)}}" class="nav-link navigate @if(Request::is($parent->url)) active @endif">
                                        <i class="nav-icon fas {{$parent->icon}}"></i>
                                        <p>
                                            {{$parent->name}}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        @else
                            <li class="nav-item">
                                <a href="{{url($parent->url)}}" class="nav-link navigate @if(Request::is($parent->url)) active @endif">
                                    <i class="nav-icon fas {{$parent->icon}}"></i>
                                    <p>
                                        {{$parent->name}}
                                    </p>
                                </a>
                            </li>
                        @endif
                    @else
                        @if(!empty($parent->permissions))
                            @can($parent->permissions)
                                <li class="nav-item has-treeview @if(Request::is($parent->url.'*')) menu-open @endif">
                                    <a href="#" class="nav-link navigate @if(Request::is($parent->url.'*')) active @endif">
                                        <i class="nav-icon fas {{$parent->icon}}"></i>
                                        <p>
                                            {{$parent->name}}
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @foreach($parent->children as $child)
                                            @if(!empty($child->permissions))
                                                @can($child->permissions)
                                                    <li class="nav-item">
                                                        <a href="{{url($child->url)}}" class="nav-link @if(Request::is($child->url)) active @endif">
                                                            <i class="nav-icon fas {{$child->icon}}"></i>
                                                            <p>
                                                                {{$child->name}}
                                                            </p>
                                                        </a>
                                                    </li>
                                                @endcan
                                            @else
                                                <li class="nav-item">
                                                    <a href="{{url($child->url)}}" class="nav-link navigate @if(Request::is($child->url)) active @endif">
                                                        <i class="nav-icon fas {{$child->icon}}"></i>
                                                        <p>
                                                            {{$child->name}}
                                                        </p>
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach

                                        {{-- Custom children for Loans --}}
                                        @if(strtolower($parent->name) === 'loans' || $parent->url === 'loan')
                                            <li class="nav-item">
                                                <a href="{{ url('loan?status=rejected') }}" class="nav-link @if(Request::is('loan') && request('status') == 'rejected') active @endif">
                                                    <i class="nav-icon fas fa-times-circle"></i>
                                                    <p>Rejected Loans</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ url('loan?status=closed') }}" class="nav-link navigate @if(Request::is('loan') && request('status') == 'closed') active @endif">
                                                    <i class="nav-icon fas fa-check-circle"></i>
                                                    <p>Completed (Closed Loans)</p>
                                                </a>
                                            </li>
                                        @endif
                                        {{-- Custom children for Savings --}}
                                        @if(strtolower($parent->name) === 'savings' || $parent->url === 'saving')
                                           
                                                <li class="nav-item">
                                                    <a href="{{ url('bulk_entry') }}" class="nav-link navigate @if(Request::is('bulk_entry*')) active @endif">
                                                        <i class="nav-icon fas fa-list"></i>
                                                        <p>Entry Savings</p>
                                                    </a>
                                                </li>
                                            
                                            <li class="nav-item">
                                                <a href="{{ url('savings?status=closed') }}" class="nav-link navigate @if(Request::is('saving') && request('status') == 'closed') active @endif">
                                                    <i class="nav-icon fas fa-check-circle"></i>
                                                    <p>Closed Savings</p>
                                                </a>
                                            </li>
                                        @endif
                                        
                                    </ul>
                                </li>
                            @endcan
                        @else
                            <li class="nav-item has-treeview @if(Request::is($parent->url.'*')) menu-open @endif">
                                <a href="#" class="nav-link navigate @if(Request::is($parent->url.'*')) active @endif">
                                    <i class="nav-icon fas {{$parent->icon}}"></i>
                                    <p>
                                        {{$parent->name}}
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @foreach($parent->children as $child)
                                        @if(!empty($child->permissions))
                                            @can($child->permissions)
                                                <li class="nav-item">
                                                    <a href="{{url($child->url)}}" class="nav-link navigate @if(Request::is($child->url)) active @endif">
                                                        <i class="nav-icon fas {{$child->icon}}"></i>
                                                        <p>
                                                            {{$child->name}}
                                                        </p>
                                                    </a>
                                                </li>
                                            @endcan
                                        @else
                                            <li class="nav-item">
                                                <a  href="{{url($child->url)}}" class="nav-link navigate @if(Request::is($child->url)) active @endif">
                                                    <i class="nav-icon fas {{$child->icon}}"></i>
                                                    <p>
                                                        {{$child->name}}
                                                    </p>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Custom children for Loans --}}
                                    @if(strtolower($parent->name) === 'loans' || $parent->url === 'loan')
                                        <li class="nav-item">
                                            <a href="{{ url('loan?status=rejected') }}" class="nav-link navigate @if(Request::is('loan') && request('status') == 'rejected') active @endif">
                                                <i class="nav-icon fas fa-times-circle"></i>
                                                <p>Rejected Loans</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('loan?status=closed') }}" class="nav-link navigate @if(Request::is('loan') && request('status') == 'closed') active @endif">
                                                <i class="nav-icon fas fa-check-circle"></i>
                                                <p>Completed (Closed Loans)</p>
                                            </a>
                                        </li>
                                    @endif
                                    {{-- Custom children for Savings --}}
                                        @if(strtolower($parent->name) === 'savings' || $parent->url === 'saving')
                                            @can('savings.bulk_entry.index')
                                                <li class="nav-item">
                                                    <a href="{{ url('bulk_entry') }}" class="nav-link navigate @if(Request::is('bulk_entry*')) active @endif">
                                                        <i class="nav-icon fas fa-list"></i>
                                                        <p>Entry Savings</p>
                                                    </a>
                                                </li>
                                            @endcan
                                            <li class="nav-item">
                                                <a href="{{ url('savings?status=closed') }}" class="nav-link navigate @if(Request::is('saving') && request('status') == 'closed') active @endif">
                                                    <i class="nav-icon fas fa-check-circle"></i>
                                                    <p>Closed Savings</p>
                                                </a>
                                            </li>
                                        @endif
                                </ul>
                            </li>
                        @endif
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>