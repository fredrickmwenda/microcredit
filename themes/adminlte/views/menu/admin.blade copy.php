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



            <li class="side-nav-item">
                <a href="index.html" class="side-nav-link">
                    <i class="ri-dashboard-2-fill"></i>
                    <span> Dashboard </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="apps-calendar.html" class="side-nav-link">
                    <i class="ri-calendar-event-fill"></i>
                    <span> Calendar </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="apps-chat.html" class="side-nav-link">
                    <i class="ri-message-3-fill"></i>
                    <span> Chat </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarEmail" aria-expanded="false" aria-controls="sidebarEmail" class="side-nav-link">
                    <i class="ri-mail-fill"></i>
                    <span> Email </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarEmail">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="apps-email-inbox.html">Inbox</a>
                        </li>
                        <li>
                            <a href="apps-email-read.html">Read Email</a>
                        </li>
                    </ul>
                </div>
            </li>



            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarBaseUI" aria-expanded="false" aria-controls="sidebarBaseUI" class="side-nav-link">
                    <i class="ri-briefcase-fill"></i>
                    <span> Base UI </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarBaseUI">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="ui-accordions.html">Accordions</a>
                        </li>
                        <li>
                            <a href="ui-alerts.html">Alerts</a>
                        </li>
                        <li>
                            <a href="ui-avatars.html">Avatars</a>
                        </li>
                        <li>
                            <a href="ui-badges.html">Badges</a>
                        </li>
                        <li>
                            <a href="ui-breadcrumb.html">Breadcrumb</a>
                        </li>
                        <li>
                            <a href="ui-buttons.html">Buttons</a>
                        </li>
                        <li>
                            <a href="ui-cards.html">Cards</a>
                        </li>
                        <li>
                            <a href="ui-carousel.html">Carousel</a>
                        </li>
                        <li>
                            <a href="ui-collapse.html">Collapse</a>
                        </li>
                        <li>
                            <a href="ui-dropdowns.html">Dropdowns</a>
                        </li>
                        <li>
                            <a href="ui-embed-video.html">Embed Video</a>
                        </li>
                        <li>
                            <a href="ui-grid.html">Grid</a>
                        </li>
                        <li>
                            <a href="ui-links.html">Links</a>
                        </li>
                        <li>
                            <a href="ui-list-group.html">List Group</a>
                        </li>
                        <li>
                            <a href="ui-modals.html">Modals</a>
                        </li>
                        <li>
                            <a href="ui-notifications.html">Notifications</a>
                        </li>
                        <li>
                            <a href="ui-offcanvas.html">Offcanvas</a>
                        </li>
                        <li>
                            <a href="ui-placeholders.html">Placeholders</a>
                        </li>
                        <li>
                            <a href="ui-pagination.html">Pagination</a>
                        </li>
                        <li>
                            <a href="ui-popovers.html">Popovers</a>
                        </li>
                        <li>
                            <a href="ui-progress.html">Progress</a>
                        </li>
                        <li>
                            <a href="ui-spinners.html">Spinners</a>
                        </li>
                        <li>
                            <a href="ui-tabs.html">Tabs</a>
                        </li>
                        <li>
                            <a href="ui-tooltips.html">Tooltips</a>
                        </li>
                        <li>
                            <a href="ui-typography.html">Typography</a>
                        </li>
                        <li>
                            <a href="ui-utilities.html">Utilities</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarExtendedUI" aria-expanded="false" aria-controls="sidebarExtendedUI" class="side-nav-link">
                    <i class="ri-stack-fill"></i>
                    <span> Extended UI </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarExtendedUI">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="extended-dragula.html">Dragula</a>
                        </li>
                        <li>
                            <a href="extended-range-slider.html">Range Slider</a>
                        </li>
                        <li>
                            <a href="extended-ratings.html">Ratings</a>
                        </li>
                        <li>
                            <a href="extended-scrollbar.html">Scrollbar</a>
                        </li>
                        <li>
                            <a href="extended-scrollspy.html">Scrollspy</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarIcons" aria-expanded="false" aria-controls="sidebarIcons" class="side-nav-link">
                    <i class="ri-service-fill"></i>
                    <span> Icons </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarIcons">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="icons-remixicons.html">Remix Icons</a>
                        </li>
                        <li>
                            <a href="icons-bootstrap.html">Bootstrap Icons</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCharts" aria-expanded="false" aria-controls="sidebarCharts" class="side-nav-link">
                    <i class="ri-bar-chart-fill"></i>
                    <span> Apex Charts </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarCharts">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="charts-apex-area.html">Area</a>
                        </li>
                        <li>
                            <a href="charts-apex-bar.html">Bar</a>
                        </li>
                        <li>
                            <a href="charts-apex-bubble.html">Bubble</a>
                        </li>
                        <li>
                            <a href="charts-apex-candlestick.html">Candlestick</a>
                        </li>
                        <li>
                            <a href="charts-apex-column.html">Column</a>
                        </li>
                        <li>
                            <a href="charts-apex-heatmap.html">Heatmap</a>
                        </li>
                        <li>
                            <a href="charts-apex-line.html">Line</a>
                        </li>
                        <li>
                            <a href="charts-apex-mixed.html">Mixed</a>
                        </li>
                        <li>
                            <a href="charts-apex-timeline.html">Timeline</a>
                        </li>
                        <li>
                            <a href="charts-apex-boxplot.html">Boxplot</a>
                        </li>
                        <li>
                            <a href="charts-apex-treemap.html">Treemap</a>
                        </li>
                        <li>
                            <a href="charts-apex-pie.html">Pie</a>
                        </li>
                        <li>
                            <a href="charts-apex-radar.html">Radar</a>
                        </li>
                        <li>
                            <a href="charts-apex-radialbar.html">RadialBar</a>
                        </li>
                        <li>
                            <a href="charts-apex-scatter.html">Scatter</a>
                        </li>
                        <li>
                            <a href="charts-apex-polar-area.html">Polar Area</a>
                        </li>
                        <li>
                            <a href="charts-apex-sparklines.html">Sparklines</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarForms" aria-expanded="false" aria-controls="sidebarForms" class="side-nav-link">
                    <i class="ri-survey-fill"></i>
                    <span> Forms </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarForms">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="form-elements.html">Basic Elements</a>
                        </li>
                        <li>
                            <a href="form-advanced.html">Form Advanced</a>
                        </li>
                        <li>
                            <a href="form-validation.html">Validation</a>
                        </li>
                        <li>
                            <a href="form-wizard.html">Wizard</a>
                        </li>
                        <li>
                            <a href="form-fileuploads.html">File Uploads</a>
                        </li>
                        <li>
                            <a href="form-editors.html">Editors</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarTables" aria-expanded="false" aria-controls="sidebarTables" class="side-nav-link">
                    <i class="ri-table-fill"></i>
                    <span> Tables </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarTables">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="tables-basic.html">Basic Tables</a>
                        </li>
                        <li>
                            <a href="tables-datatable.html">Data Tables</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarMaps" aria-expanded="false" aria-controls="sidebarMaps" class="side-nav-link">
                    <i class="ri-treasure-map-fill"></i>
                    <span> Maps </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarMaps">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="maps-google.html">Google Maps</a>
                        </li>
                        <li>
                            <a href="maps-vector.html">Vector Maps</a>
                        </li>
                    </ul>
                </div>
            </li>

    










        </ul>
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div> 

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
        <!--div class="user-panel mt-4 mb-3 d-flex">
            <div class="image">
                @if(!empty(Auth::user()->photo))
                    <img
                            class="img-circle"
                            src="{{asset('storage/uploads/'.Auth::user()->photo)}}"
                            alt="User Image">
                @else
                    <img class="img-circle"
                         src="{{asset('themes/adminlte/img/user.png')}}"
                         alt="User profile picture">
                @endif
            </div>
            <div class="info">
                <a href="#" class="d-block">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</a>
            </div>
        </div-->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true" style="margin-top: 23% !important;">
                @foreach(\Modules\Core\Entities\Menu::with('children')->where('is_parent',1)->orderBy('menu_order','asc')->get() as $parent)
                    @if($parent->children->count()==0)
                        @if(!empty($parent->permissions))
                            @can($parent->permissions)
                                <li class="nav-item">
                                    <a href="{{url($parent->url)}}" class="nav-link @if(Request::is($parent->url)) active @endif">
                                        <i class="nav-icon fas {{$parent->icon}}"></i>
                                        <p>
                                            {{$parent->name}}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        @else
                            <li class="nav-item">
                                <a href="{{url($parent->url)}}" class="nav-link @if(Request::is($parent->url)) active @endif">
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
                                    <a href="#" class="nav-link @if(Request::is($parent->url.'*')) active @endif">
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
                                                    <a href="{{url($child->url)}}" class="nav-link @if(Request::is($child->url)) active @endif">
                                                        <i class="nav-icon fas {{$child->icon}}"></i>
                                                        <p>
                                                            {{$child->name}}
                                                        </p>
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                            @endcan
                        @else
                            <li class="nav-item has-treeview @if(Request::is($parent->url.'*')) menu-open @endif">
                                <a href="#" class="nav-link @if(Request::is($parent->url.'*')) active @endif">
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
                                                <a  href="{{url($child->url)}}" class="nav-link @if(Request::is($child->url)) active @endif">
                                                    <i class="nav-icon fas {{$child->icon}}"></i>
                                                    <p>
                                                        {{$child->name}}
                                                    </p>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
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
