@extends('core::layouts.master')
@section('title')
    {{ trans_choice('communication::general.all_sms',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('communication::general.all_sms',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('communication::general.all_sms',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" id="app">
        @can('communication.campaigns.create')
            <a href="{{ url('communication/send_sms_form') }}"
            class="btn btn-info btn-sm">
                <i class="fas fa-plus"></i> Send SMS
            </a>

            <a href="{{ url('communication/all_jobs') }}"
            class="btn btn-warning btn-sm">
                <i class="fas fa-align-left"></i> Queue Jobs
            </a>

            <a href="{{ url('communication/all_failed_jobs') }}"
            class="btn btn-danger btn-sm">
                <i class="fas fa-align-center"></i> Failed Jobs
            </a>
        @endcan
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="client_id"
                                class="control-label">Select Client</label>
                            <select class="form-control"  @change="onChange($event)" name="client_id"
                                    id="client_id" v-model="client_id" required>
                                <option value="" selected>All</option>
                                @foreach($clients as $key)
                                    <option value="{{$key->id}}">{{$key->first_name . " " .$key->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="start_date"> From Date</label>
                            <flat-pickr
                                    v-model="start_date"
                                    class="form-control"
                                    name="start_date" id="start_date" required>
                            </flat-pickr>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="end_date"> To Date</label>
                            <flat-pickr
                                    v-model="end_date"
                                    class="form-control"
                                    name="end_date" id="end_date" required>
                            </flat-pickr>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <b><span class="selection_date"></span></b>
            </div>
            <div class="card-body table-responsive p-3">
                <table class="table  table-striped table-hover table-condensed"
                       id="deposit_table">
                    <thead>
                    <tr>
                        <th>{{ trans_choice('core::general.id',1) }}</th>
                        <th>{{ trans_choice('communication::general.sms_gateway',1) }}</th>
                        <th>{{ trans_choice('communication::general.client',1) }}</th>
                        <th>{{ trans_choice('communication::general.mobile',1) }}</th>
                        <th>{{ trans_choice('communication::general.text_body',1) }}</th>
                        <th>{{ trans_choice('communication::general.status',1) }}</th>
                        <th>{{ trans_choice('communication::general.created_at',1) }}</th>
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
         var app = new Vue({
            el: "#app",
            data: {
                start_date: "{{old('start_date',date('Y-m-01'))}}",
                end_date: "{{old('end_date',date('Y-m-d'))}}",
                client_id:"",
            }
        })

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
            deposit_table = $('#deposit_table').DataTable({
                processing: true,
                serverSide: true,
                //ajax: '{!! url('client/client_deposit_report') !!}',
                ajax: {
					"url": '{!! url('communication/all_sms') !!}',
					"data": function ( d ) {
						var start = formatDate($("#start_date").val());
						var end = formatDate($("#end_date").val());
						d.start_date = start;
						d.end_date = end;
						d.client_id = $("#client_id").val();
                        $('.selection_date').html($("#start_date").val() + "&nbsp;&nbsp;<< TO >>&nbsp;&nbsp;" + $("#end_date").val());
					}
				},
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'sms_gateway', name: 'sms_gateway'},
                    {data: 'client_name', name: 'client_name'},
                    {data: 'send_to', name: 'send_to'},
                    {data: 'text_body', name: 'text_body'},
                    {data: 'status', name: 'status'},
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

            $(' #start_date, #end_date, #client_id').change( function(){
        		deposit_table.ajax.reload();
    		});
        })
    </script>
@endsection
