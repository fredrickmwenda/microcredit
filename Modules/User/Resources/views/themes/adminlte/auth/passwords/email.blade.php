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

            <form method="post" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                     <label class="form-label" for="email">{{trans_choice("user::general.email",1)}}</label>
                    <input type="email" id="email" class="form-control" placeholder="Email Address" name="email" required>
                                        @error('email')
                    <div class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
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
