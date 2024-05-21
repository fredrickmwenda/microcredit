@extends('core::layouts.master')
@section('title')
    {{ trans_choice('client::general.client_balance',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Client Balance Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('client::general.dormant_clients',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" id="app">

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="branch_id"
                                class="control-label">Select Branch</label><br>
                            <select class="form-control select2" name="branch_id"
                                    id="branch_id" v-model="branch_id" required>
                                <option value="" selected>All</option>
                                @foreach($branches as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="client_id"
                                class="control-label">Select Client</label><br>
                            <select class="form-control select2"  @change="onChange($event)" name="client_id"
                                    id="client_id" v-model="client_id" required>
                                <option value="" selected>All</option>
                                @foreach($clients as $key)
                                    <option value="{{$key->id}}">{{$key->first_name . " " .$key->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!--div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="start_date"> From Date</label>
                            <flat-pickr
                                    v-model="start_date"
                                    class="form-control"
                                    name="start_date" id="start_date" required>
                            </flat-pickr>
                        </div>
                    </div-->
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
                       id="balance_table">
                    <thead>
                    <tr>
                        <th>{{ trans_choice('core::general.id',1) }}</th>
                        <th>{{ trans_choice('core::general.name',1) }}</th>
                        <th>{{ trans_choice('core::general.mobile',1) }}</th>
                        <th>{{ trans_choice('core::general.branch',1) }}/ Team</th>
                        <th>{{ trans_choice('core::general.status',1) }}</th>
                        <th>Saving Balance</th>
                        <th>Loan Balance</th>
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
                //start_date: "{{old('start_date',date('Y-m-01'))}}",
                end_date: "{{old('end_date',date('Y-m-d'))}}",
                client_id:"",
                branch_id:"",
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
            $("#client_id").select2({
                width: 'resolve',
                closeOnSelect: true//,
                //theme: "classic"
            });
            balance_table = $('#balance_table').DataTable({
                processing: true,
                serverSide: true,
                //ajax: '{!! url('client/client_deposit_report') !!}',
                ajax: {
					"url": '{!! url('client/client_balance_report') !!}',
					"data": function ( d ) {
						//var start = formatDate($("#start_date").val());
						var end = formatDate($("#end_date").val());
						//d.start_date = start;
						d.end_date = end;
						d.client_id = $("#client_id").val();
						d.branch_id = $("#branch_id").val();
                        //$('.selection_date').html($("#start_date").val() + "&nbsp;&nbsp;<< TO >>&nbsp;&nbsp;" + $("#end_date").val());
                        $('.selection_date').html("&nbsp;&nbsp;<< TILL >>&nbsp;&nbsp;" + $("#end_date").val());
					}
				},
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'mobile', name: 'mobile'},
                    {data: 'branch', name: 'branch'},
                    {data: 'status', name: 'status'},
                    {data: 'saving_balance', name: 'saving_balance'},
                    {data: 'loan_balance', name: 'loan_balance'}
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

            $(' #start_date, #end_date, #client_id, #branch_id').change( function(){
        		balance_table.ajax.reload();
    		});
        })
    </script>
@endsection
