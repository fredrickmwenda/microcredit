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
    
    <!--<link rel="stylesheet" href="{{ asset('themes/adminlte/css/adminlte.css') }}" type="text/css">-->
    <link rel="stylesheet" href="{{ asset('themes/adminlte/css/custom.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2-4.0.13.min.css') }}" type="text/css"/>
    @yield('styles')
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gridstack@9.4.0/dist/gridstack.min.css" />-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">)
    <!--<script src="{{ asset('themes/adminlte/js/adminlte.js') }}"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/vue@2.7.16/dist/vue.js"></script>

<!-- vue-select -->
<link rel="stylesheet" href="https://unpkg.com/vue-select@3.20.2/dist/vue-select.css">
<script src="https://unpkg.com/vue-select@3.20.2"></script>

<!-- flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://unpkg.com/vue-flatpickr-component@8"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> -->


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
[class*="sidebar-dark-"] {
    background: #923E5A!important;
}
.layout-navbar-fixed .wrapper .sidebar-dark-primary .brand-link:not([class*="navbar"]) {
    background-color: #6C1E54!important;
}
.navigate{
    color:white!important;
}
.sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active{
    background-color:#fd7e14!important;
}
.nav-link.active{
    color:black!important;
}
.brand-link img {
    max-height: 70px; /* keeps it within header */
    width: auto;      /* prevents stretching */
    object-fit: contain;
}
.breadcrumb-item.active{
    color:black!important;
}
.btn-primary {
    color: #fff;
    background-color: #6C1E54;
    border-color: #6C1E54;
    box-shadow: none;
}
.btn-sec{
        color: #fff;
    background-color: #6C1E54;
    border-color: #6C1E54;
    box-shadow: none;
}

.btn-sec:hover{
        color: #fff;
    background-color: #6C1E54;
    border-color: #6C1E54;
    box-shadow: none;
}

.alert-info {
    color: #fff;
    background-color: #6C1E54!important;
    border-color: #6C1E54 !important;
}


.btn-info{
    color: #fff;
    background-color: #6C1E54 !important;
    border-color: #6C1E54 !important;
    box-shadow: none;
}

.btn-primary:hover {
    color: #fff;
    background-color: #923E5A;
    border-color: #923E5A;
    box-shadow: none;
}

.dropdown-item.active, .dropdown-item:active {
    background-color: #6C1E54;
}
a{
    color: #6C1E54;
}
a:hover
 {
    color: #b48520ff!important;
    text-decoration: none;
}a.text-primary:hover

 {
    color: #6C1E54 !important;
}
.modal.show {
  display: block !important;
  z-index: 1050 !important;
}
.text-primary {
    color: #d39a1eff!important;
} 
.bg-brown {
  background-color: #9D536A !important
}
.bg-brown-dark {
  background-color: #6C1E54 !important
}
</style>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Select2 -->
<script src="http://admin.studytrustgh.com/plugins/select2/select2-4.0.13.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- GridStack (compatible with AdminLTE v3 + Bootstrap 4) -->

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/gridstack.js/0.6.4/gridstack.all.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('plugins/select2/select2-4.0.13.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
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
