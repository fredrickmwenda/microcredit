<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('title') </title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700">
    <!-- <link rel="stylesheet" href="{{ asset('themes/adminlte/css/adminlte.css') }}"> -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('themes/adminlte/css/theme.css') }}"/>
    @yield('styles')
    <!-- <script src="{{ asset('themes/adminlte/js/adminlte.js') }}"></script> -->
            <style>
            body {
                background: url('/assets/images/bg/login-bg.png') no-repeat center center fixed;
                background-size: cover;
                min-height: 100vh;
                

                padding: 2rem;
            }
            .brod  {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .classic-card {
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                padding: 3rem;
                width: 100%;
                max-width: 480px;
                border: 1px solid var(--border-light);
            }

            .logo-section {
                text-align: center;
                margin-bottom: 2rem;
            }

            .logo-text {
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--primary);
                margin-bottom: 0.5rem;
            }

            .form-title {
                font-size: 2rem;
                font-weight: 700;
                color: var(--black);
                margin-bottom: 0.5rem;
            }

            .form-subtitle {
                color: var(--text-light);
                font-size: 1rem;
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-label {
                display: block;
                font-weight: 500;
                margin-bottom: 0.5rem;
                color: var(--text);
            }

            .form-control {
                width: 100%;
                padding: 1rem;
                border: 2px solid var(--border);
                border-radius: 8px;
                font-size: 1rem;
                transition: all 0.3s ease;
                background: white;
            }

            .form-control:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 4px rgba(59, 91, 255, 0.1);
            }

            .form-control::placeholder {
                color: var(--text-light);
            }

            .btn-primary {
                background: var(--primary);
                color: white;
                border: none;
                padding: 1rem 2rem;
                border-radius: 8px;
                font-size: 1rem;
                font-weight: 600;
                width: 100%;
                transition: all 0.3s ease;
                margin-bottom: 1.5rem;
            }

            .btn-primary:hover {
                background: var(--primary-dark);
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(59, 91, 255, 0.3);
            }

            .form-check {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                margin-bottom: 1rem;
            }

            .form-check-input {
                width: 18px;
                height: 18px;
                border: 2px solid var(--border);
                border-radius: 4px;
                cursor: pointer;
            }

            .form-check-input:checked {
                background-color: var(--primary);
                border-color: var(--primary);
            }

            .form-check-label {
                font-size: 0.9rem;
                color: var(--text);
                cursor: pointer;
            }

            .forgot-password {
                text-align: right;
                margin-bottom: 1.5rem;
            }

            .forgot-password a {
                color: var(--primary);
                text-decoration: none;
                font-size: 0.9rem;
            }

            .forgot-password a:hover {
                text-decoration: underline;
            }

            .signup-link {
                text-align: center;
                margin-top: 2rem;
                color: var(--text-light);
            }

            .signup-link a {
                color: var(--primary);
                text-decoration: none;
                font-weight: 600;
            }

            .signup-link a:hover {
                text-decoration: underline;
            }

            @media (max-width: 768px) {
                body {
                    padding: 1rem;
                }

                .classic-card {
                    padding: 2rem;
                    margin: 1rem;
                }

                .form-title {
                    font-size: 1.75rem;
                }
            }
            .login-page footer {
    text-align: center;
    font-size: 14px;
    color: #000000ff;
    padding: 15px 10px;
    width: 100%;
    align-self: end;
}
.text-primary{
    color: #6C1E54!important;
}a{
    color: #6C1E54;
}
a:hover
 {
    color: #b48520ff!important;
    text-decoration: none;
}
        </style>
</head>
<body class="hold-transition login-page">
    
    <!-- <div style="position: fixed; top: 0; width: 100%; height: 100%; z-index: -1;"> -->

 <div class="brod">                     
                    <img class="logo-light logo-img logo-img-lg" src="{{asset('themes/adminlte/img/logo-white.png')}}"
                         
                    
                         alt="logo">
                 </div>
  <div class="brod">
@yield('content')
</div>
<footer>
    
        
    &copy; Copyright <script>document.write(new Date().getFullYear())</script>. <a href="https://www.msoftghana.com" target="_blank" class="text-decoration-none text-primary">Msoft Ghana Ltd</a>. All Rights Reserved.
    
</footer> 

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        $('#flash-overlay-modal').modal();
        $('.confirm').on('click', function (e) {
            e.preventDefault();
            var href = $(this).attr('href');
            Swal.fire({
                title: '{{trans_choice('core::general.are_you_sure',1)}}',
                text: '',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{trans_choice('core::general.ok',1)}}",
                cancelButtonText: "{{trans_choice('core::general.cancel',1)}}"
            }).then(function (result) {
                if (result.value) {
                    window.location = href;
                }

            })
        });

    })
</script>
@yield('scripts')
</body>
</html>
