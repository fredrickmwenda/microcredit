
        <div class="card text-center">
            <div class="card-body">
            @if(!empty($user->photo))
                <a href="{{asset('uploads/user/'.$user->photo)}}"
                   class="fancybox">
                        <img src="{{asset('uploads/user/'.$user->photo)}}" class="rounded-circle avatar-lg img-thumbnail"
                        alt="profile-image">
                </a>
            @else

                     <img src="{{asset('themes/adminlte/img/user.png')}}" class="rounded-circle avatar-lg img-thumbnail"
                     alt="profile-image">
            @endif
               

                <h4 class="mb-1 mt-2">{{ $user->first_name }} {{ $user->last_name }}</h4>
 

           
                <a href="{{url('user/'.$user->id.'/edit')}}"
               class="btn btn-info confirm  btn-sm mr-1 mb-2">{{trans_choice('core::general.edit',1)}}</a>
            <a href="{{url('user/'.$user->id.'/destroy')}}"
               class="btn btn-danger confirm btn-sm mr-1 mb-2">{{trans_choice('core::general.delete',1)}}</a>

                <div class="text-start mt-3">
                    <h4 class="fs-13 text-uppercase">About Me :</h4>
                    <p class="text-muted mb-2">
                        <strong>Gender :</strong> 
                        <span class="ms-2">
                        @if($user->gender='male')
                            {{trans_choice("user::general.male",1)}}
                        @elseif($user->gender='female')
                            {{trans_choice("user::general.female",1)}}
                        @else
                            {{__('general.Unspecified')}}
                            {{trans_choice("user::general.unspecified",1)}}
                        @endif
                        </span>
                    </p>
                    <p class="text-muted mb-3">
                        @foreach($user->roles as $key)
                            <span class="badge badge-light">{{$key->name}}</span>
                        @endforeach
                    </p>
                    <!-- <p class="text-muted mb-2"><strong>Full Name :</strong> <span class="ms-2">Tosha K. Minner</span></p> -->

                    <!-- <p class="text-muted mb-2"><strong>Mobile :</strong><span class="ms-2">(123)
                            123 1234</span></p> -->

                    <p class="text-muted mb-2"><strong>Email :</strong> <span class="ms-2 ">{{$user->email}}</span></p>

                    <!-- <p class="text-muted mb-1"><strong>Location :</strong> <span class="ms-2">USA</span></p> -->
                </div>


            </div> <!-- end card-body -->


         <div class="list-group list-group-unbordered mb-3">
            <a class="list-group-item @if(Request::segment(3)=='') active @endif" href="{{url('user/profile')}}">
                <i class="fas fa-user-edit"></i>
                <span>{{ __('user::general.Account Information') }}</span>
            </a>
            <a href="{{url('user/profile/change_password')}}"
               class="list-group-item @if(Request::segment(3)=='change_password') active @endif">
                <em class="fas fa-user-shield"></em>
                <span> {{ __('user::general.Change Password') }}</span>
            </a>
            <a href="{{url('user/profile/notification')}}"
               class="list-group-item @if(Request::segment(3)=='notification') active @endif">
                <i class="fas fa-bell"></i>
                <span>{{ __('user::general.Notifications') }}</span>
            </a>
            <a href="{{url('user/profile/activity_log')}}"
               class="list-group-item @if(Request::segment(3)=='activity_log') active @endif">
                <i class="fas fa-database"></i>
                <span>{{ __('user::general.Activity Logs') }}</span>
            </a>
            <a href="{{url('user/profile/two_factor')}}"
               class="list-group-item @if(Request::segment(3)=='two_factor') active @endif">
                <i class="fas fa-lock"></i>
                <span>{{ __('user::general.Two Factor Authentication') }}</span>
            </a>
            <a href="{{url('user/profile/note')}}"
               class="list-group-item @if(Request::segment(3)=='note') active @endif">
                <i class="fas fa-bookmark"></i>
                <span>{{ __('user::general.Notes') }}</span>
            </a>
            <a href="{{url('user/profile/api')}}" class="list-group-item @if(Request::segment(3)=='api') active @endif">
                <i class="fas fa-code"></i>
                <span>{{ __('user::general.API Keys') }}</span>
            </a>
        </div> 
        </div> <!-- end card -->

    

         <!-- <div class="list-group list-group-unbordered mb-3">
            <a class="list-group-item @if(Request::segment(3)=='') active @endif" href="{{url('user/profile')}}">
                <i class="fas fa-user-edit"></i>
                <span>{{ __('user::general.Account Information') }}</span>
            </a>
            <a href="{{url('user/profile/change_password')}}"
               class="list-group-item @if(Request::segment(3)=='change_password') active @endif">
                <em class="fas fa-user-shield"></em>
                <span> {{ __('user::general.Change Password') }}</span>
            </a>
            <a href="{{url('user/profile/notification')}}"
               class="list-group-item @if(Request::segment(3)=='notification') active @endif">
                <i class="fas fa-bell"></i>
                <span>{{ __('user::general.Notifications') }}</span>
            </a>
            <a href="{{url('user/profile/activity_log')}}"
               class="list-group-item @if(Request::segment(3)=='activity_log') active @endif">
                <i class="fas fa-database"></i>
                <span>{{ __('user::general.Activity Logs') }}</span>
            </a>
            <a href="{{url('user/profile/two_factor')}}"
               class="list-group-item @if(Request::segment(3)=='two_factor') active @endif">
                <i class="fas fa-lock"></i>
                <span>{{ __('user::general.Two Factor Authentication') }}</span>
            </a>
            <a href="{{url('user/profile/note')}}"
               class="list-group-item @if(Request::segment(3)=='note') active @endif">
                <i class="fas fa-bookmark"></i>
                <span>{{ __('user::general.Notes') }}</span>
            </a>
            <a href="{{url('user/profile/api')}}" class="list-group-item @if(Request::segment(3)=='api') active @endif">
                <i class="fas fa-code"></i>
                <span>{{ __('user::general.API Keys') }}</span>
            </a>
        </div> -->




