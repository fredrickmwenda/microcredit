<!-- <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
        <b>Version</b> {{$system_version}}
    </div>
    <strong>Copyright &copy; {{ date("Y") }} <a href="{{url('/')}}">{{$company_name}}</a>.</strong> All rights
    reserved.
</footer> -->

<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <script>document.write(new Date().getFullYear())</script> <a href="{{url('/')}}">{{$company_name}}</a>
            </div>
            <div class="col-md-6">
                <div class="text-md-end footer-links d-none d-md-block">
                    <b>Version</b> {{$system_version}}
                </div>
            </div>
        </div>
    </div>
</footer>