<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('title') </title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;400;600&display=swap" rel="stylesheet">
    <link href="{{asset('plugins/bootstrap/5.3.3/bootstrap.min.css')}}" rel="stylesheet" type="text/css" id="app-style" />
    <link href="{{asset('plugins/datatable/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Theme Config Js -->
    <script src="{{asset('assets/js/config.js')}}"></script>

    <!-- App css -->
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" id="app-style" />

    <link href="{{asset('assets/css/own.css')}}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- <link rel="stylesheet" href="{{ asset('themes/adminlte/css/custom.css') }}" type="text/css" /> -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2-4.0.13.min.css') }}" type="text/css" />

    <!-- Daterangepicker css -->
    <link rel="stylesheet" href="{{asset('assets/vendor/daterangepicker/daterangepicker.css')}}">

    @yield('styles')
    <style>
        .btn {
            background-color: #28a745 !important;
        }

        .card-header>.card-tools {
            float: right;
            margin-right: -.625rem;
        }

        .btn-trigger {
            padding: 6px 15px;
            font-size: 14px;
            font-weight: 600;
            border-radius: .17rem;
            box-shadow: 0px 2px 10px 1px #c3c3c3;
            background-color: #28a745;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffffff;
            border: none;
        }
    </style>


</head>

<body>
    <div class="wrapper">
        @include('core::partials.top_nav')
        @if(Auth::user()->hasRole('client'))
        @include('core::menu.client')
        @else
        @include('core::menu.admin')
        @endif
        <div class="content-page">
            <div class="content">
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

    </div>
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
      <!-- Vendor js -->
  
<script src="{{asset('plugins/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('plugins/bootstrap/5.3.3/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('plugins/select2/select2-4.0.13.min.js') }}"></script>
    <script src="{{ asset('themes/adminlte/js/custom.js') }}"></script>
    <!-- Vendor js -->


    <script src="{{asset('assets/js/app.min.js')}}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var flashModal = document.getElementById('flash-overlay-modal');
            if (flashModal) {
                var modal = new bootstrap.Modal(flashModal);
                modal.show();
            }
                        $('select').select2({
                width: '100%'
            });
            $('.confirm').on('click', function(e) {
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