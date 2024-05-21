@extends('core::layouts.master')
@section('title')
    All Jobs on Queue
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>All Jobs On QUEUE</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">All Jobs</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" id="app">
        @can('communication.campaigns.create')
        <span>If Jobs in queue please click the button</span>
            <a href="{{ url('queue-work') }}"
            class="btn btn-info btn-sm" target="_blank">
                <i class="fas fa-plus"></i> Start Queue Work
            </a>

            <a href="{{ url('clear-cache') }}"
            class="btn btn-danger btn-sm" target="_blank">
                <i class="fas fa-align-center"></i> Clear Cache
            </a>
        @endcan
        

        <div class="card">
            <div class="card-header">
                <b>All Jobs Report</b>
            </div>
            <div class="card-body table-responsive p-3">
                <table class="table  table-striped table-hover table-condensed"
                       id="job_table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Queue</th>
                        <th>Payload</th>
                        <th>Attempts</th>
                        <th>Reserved At</th>
                        <th>Available At</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
           
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        function formatDate(date) {
				var d = new Date(date),
					month = '' + (d.getMonth() + 1),
					day = '' + d.getDate(),
					year = d.getFullYear();

				if (month.length < 2) 
					month = '0' + month;
				if (day.length < 2) 
					day = '0' + day;

				return [year, month, day].join('-');
			}

        $(document).ready(function () {
            job_table = $('#job_table').DataTable({
                processing: true,
                serverSide: true,
                //ajax: '{!! url('client/client_deposit_report') !!}',
                ajax: {
					"url": '{!! url('communication/all_jobs') !!}',
					"data": function ( d ) {
						//
					}
				},
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'queue', name: 'queue'},
                    {data: 'payload', name: 'payload'},
                    {data: 'attempts', name: 'attempts'},
                    {data: 'reserved_at', name: 'reserved_at'},
                    {data: 'available_at', name: 'available_at'},
                    {data: 'created_at', name: 'created_at'}
                ],
                "order": [[0, "desc"]],
                "language": {
                    "lengthMenu": "{{ trans('general.lengthMenu') }}",
                    "zeroRecords": "{{ trans('general.zeroRecords') }}",
                    "info": "{{ trans('general.info') }}",
                    "infoEmpty": "{{ trans('general.infoEmpty') }}",
                    "search": "{{ trans('general.search') }}",
                    "infoFiltered": "{{ trans('general.infoFiltered') }}",
                    "paginate": {
                        "first": "{{ trans('general.first') }}",
                        "last": "{{ trans('general.last') }}",
                        "next": "{{ trans('general.next') }}",
                        "previous": "{{ trans('general.previous') }}"
                    }
                },
                responsive: false,
                searching: false,
                "autoWidth": false,
                "drawCallback": function (settings) {
                    $('.confirm').on('click', function (e) {
                        e.preventDefault();
                        var href = $(this).attr('href');
                        swal({
                            title: 'Are you sure?',
                            text: '',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok',
                            cancelButtonText: 'Cancel'
                        }).then(function () {
                            window.location = href;
                        })
                    });
                }
            });
        })
    </script>
@endsection
