@extends('core::layouts.auth')
@section("title")
    {{trans_choice("user::general.reset",1)}} {{trans_choice("user::general.password",1)}}
@endsection
@section('content')
    @extends('core::layouts.auth')
@section("title")
    {{trans_choice("user::general.forgot_password",1)}}
@endsection
@section('content')
         <div class="classic-card fade-in">
            <div class="logo-section">
                <!-- <div class="logo-text">Logo</div> -->
                <h1 class="form-title">{{\Modules\Setting\Entities\Setting::where('setting_key','core.company_name')->first()->setting_value}}</h1>
                <!-- <p class="form-subtitle">Sign in to your account.</p> -->
            </div>

            <form method="post" action="{{ route('password.update') }}">
                @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label" for="email">{{trans_choice("user::general.email",1)}}</label>
                        </div>
                        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                               name="email"
                               placeholder="{{trans_choice("user::general.email",1)}}" value="{{ old('email') }}"
                               required
                               autocomplete="email" id="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label"
                                   for="password">{{trans_choice("user::general.password",1)}}</label>
                        </div>
                        <div class="form-control-wrap">
                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch"
                               data-target="password">
                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                            </a>
                            <input type="password" name="password"
                                   class="form-control form-control-lg @error('password') is-invalid @enderror"
                                   placeholder="{{trans_choice("user::general.password",1)}}" required
                                   autocomplete="off" id="password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label"
                                   for="password_confirmation">{{trans_choice("user::general.password_confirmation",1)}}</label>
                        </div>
                        <div class="form-control-wrap">
                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch"
                               data-target="password_confirmation">
                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                            </a>
                            <input type="password" name="password_confirmation"
                                   class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                                   placeholder="{{trans_choice("user::general.password_confirmation",1)}}" required
                                   id="password_confirmation">
                            @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

  

                <button type="submit" class="btn btn-primary">
                    {{trans_choice("user::general.reset",1)}}
                </button>

               
            </form>                               <p class="mb-1">
                    <a href="{{ route('login') }}">{{trans_choice("user::general.back_to_login",1)}}</a>
                </p>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
       


@endsection
