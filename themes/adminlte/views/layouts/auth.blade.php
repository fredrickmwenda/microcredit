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
    <link rel="stylesheet" href="{{ asset('themes/adminlte/css/adminlte.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/adminlte/css/custom.css') }}"/>
    @yield('styles')
    <script src="{{ asset('themes/adminlte/js/adminlte.js') }}"></script>
</head>
<body class="hold-transition login-page">
    
    <div style="position: fixed; top: 0; width: 100%; height: 100%; z-index: -1;">
    <!--<video autoplay muted loop id="video" style="width:100%; height:auto; min-height: 100%;">-->
        
    <!--    <source src="videos/finance_video.mp4" type="video/mp4">-->
    <!--</video>-->
</div>

@yield('content')

<footer>
    &copy; Copyright 2021. Msoft Ghana Ltd. All Rights Reserved.
</footer>

<script src="{{ asset('themes/adminlte/js/custom.js') }}"></script>
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
