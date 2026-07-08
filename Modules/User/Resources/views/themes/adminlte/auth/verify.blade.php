@extends('core::layouts.auth')
@section("title")
    {{trans_choice("user::general.verify_email",1)}}
@endsection
@section('content')
          <div class="classic-card fade-in">
            <div class="logo-section">
                <!-- <div class="logo-text">Logo</div> -->
                <h1 class="form-title">{{\Modules\Setting\Entities\Setting::where('setting_key','core.company_name')->first()->setting_value}}</h1>
                <!-- <p class="form-subtitle">Sign in to your account.</p> -->
            </div>

           <p class="login-box-msg">{{trans_choice("user::general.verify_email",1)}}</p>   @if (session('resent'))
                    <div class="alert alert-success" role="alert">
                        {{trans_choice("user::general.email_link_sent",1)}}
                    </div>
                @endif
                {{trans_choice("user::general.check_verify_link",1)}}
                {{trans_choice("user::general.did_not_receive_email",1)}}, <a
                        href="{{ route('verification.resend') }}">{{trans_choice("user::general.request_another_link",1)}}</a>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
       
@endsection