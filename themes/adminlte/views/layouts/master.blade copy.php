<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('title') </title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700">-->
    <!--<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;600&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;400;600&display=swap" rel="stylesheet">
    <!--<link href="https://fonts.googleapis.com/css2?family=Questrial&display=swap" rel="stylesheet"> -->
    <!--<link href="https://fonts.googleapis.com/css2?family=Didact+Gothic&display=swap" rel="stylesheet"> -->

    <!-- <link rel="stylesheet" href="{{ asset('themes/adminlte/css/adminlte.css') }}" type="text/css"> -->
    <!-- Theme Config Js -->
    <script src="{{asset('assets/js/config.js')}}"></script>

    <!-- App css -->
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{ asset('themes/adminlte/css/custom.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2-4.0.13.min.css') }}" type="text/css" />

    <!-- Daterangepicker css -->
    <link rel="stylesheet" href="{{asset('assets/vendor/daterangepicker/daterangepicker.css')}}">

    <!-- Vector Map css -->
    <link rel="stylesheet" href="{{asset('assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css')}}">
    @yield('styles')
    <!-- <script src="{{ asset('themes/adminlte/js/adminlte.js') }}"></script> -->
    <script src="{{ asset('plugins/select2/select2-4.0.13.min.js') }}"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        @include('core::partials.top_nav')
        @if(Auth::user()->hasRole('client'))
        @include('core::menu.client')
        @else
        @include('core::menu.admin')
        @endif
        <div class="content-wrapper">
            <div class="w3k-content-fixed">
                <section class="content pt-2">
                    <div class="row">
                        <div class="col-md-12">
                            @include('core::partials.flash.message')
                        </div>
                    </div>
                </section>
                @yield('content')
            </div>
        </div>
        @include('core::partials.footer')
    </div>
    <script src="{{ asset('themes/adminlte/js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#flash-overlay-modal').modal();
            $('.confirm').on('click', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                Swal.fire({
                    title: '{{trans_choice('
                    core::general.are_you_sure ',1)}}',
                    text: '',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{trans_choice('core::general.ok',1)}}",
                    cancelButtonText: "{{trans_choice('core::general.cancel',1)}}"
                }).then(function(result) {
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