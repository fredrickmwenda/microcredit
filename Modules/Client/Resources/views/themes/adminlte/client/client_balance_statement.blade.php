@extends('core::layouts.master')
@section('title')
    {{ trans_choice('client::general.client_balance_statement',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3>Client Balance Statement</h3>
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
                    <div class="col-md-3">
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

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="client_id"
                                class="control-label">Select Client</label><br>
                            <select class="form-control select2" name="client_id"
                                    id="client_id" required>
                                <option value="" selected>&nbsp;</option>
                                @foreach($clients as $key)
                                    <option value="{{$key->id}}">{{$key->first_name . " " .$key->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3">
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

        <div class="card p-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-body">
                        <h3 id="header_account_summery"><b></b></h3>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="box box-solid">
                                    <div class="box-body">
                                        <h4><b>Saving Balance Summery</b></h4>
                                        <table class="table table-striped">
                                            <tr style="background-color:cornsilk">
                                                <th>Opening Balance at <span class="start_date"></span>:</th>
                                                <td align="right">
                                                <span class="opening_balance">
                                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                                </span>
                                                </td>
                                            </tr>
                                            <tr style="background-color:gainsboro">
                                                <th>Total Debit:</th>
                                                <td align="right">
                                                <span class="total_debit">
                                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                                </span>
                                                </td>
                                            </tr>
                                            <tr style="background-color:lavendar">
                                                <th>Total Credit:</th>
                                                <td align="right">
                                                <span class="total_credit">
                                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                                </span>
                                                </td>
                                            </tr>
                                            <tr style="background-color:khaki">
                                                <th>Closing Balance at <span class="end_date"></span>:</th>
                                                <td align="right">
                                                <span class="closing_balance">
                                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                                </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="box box-solid">
                                    <div class="box-body">
                                    </div>
                                </div>
                            </div>
                         </div>
                        
                    </div>
                </div>
            </div>
        </div>
    
        <div class="card">
            <div class="card-header">
                <b><span class="selection_date_saving"></span></b>
            </div>
            <div class="card-body table-responsive p-3">
                <table class="table  table-striped table-hover table-condensed"
                       id="saving_balance_statement">
                    <thead>
                    <tr>
                        <th>{{ trans_choice('core::general.id',1) }}</th>
                        <th>Saving Products Name</th>
                        <th>Transaction Type Name</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <!--Loan Statement-->
        <div class="card p-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-body">
                        <h3 id="header_account_summery"><b></b></h3>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="box box-solid">
                                    <div class="box-body">
                                        <h4><b>Loan Balance Summery</b></h4>
                                        <table class="table table-striped">
                                            <tr style="background-color:cornsilk">
                                                <th>Opening Balance at <span class="start_date"></span>:</th>
                                                <td align="right">
                                                <span class="loan_opening_balance">
                                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                                </span>
                                                </td>
                                            </tr>
                                            <tr style="background-color:gainsboro">
                                                <th>Total Debit:</th>
                                                <td align="right">
                                                <span class="loan_total_debit">
                                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                                </span>
                                                </td>
                                            </tr>
                                            <tr style="background-color:lavendar">
                                                <th>Total Credit:</th>
                                                <td align="right">
                                                <span class="loan_total_credit">
                                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                                </span>
                                                </td>
                                            </tr>
                                            <tr style="background-color:khaki">
                                                <th>Closing Balance at <span class="end_date"></span>:</th>
                                                <td align="right">
                                                <span class="loan_closing_balance">
                                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                                </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="box box-solid">
                                    <div class="box-body">
                                    </div>
                                </div>
                            </div>
                         </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <b><span class="selection_date_loan"></span></b>
            </div>
            <div class="card-body table-responsive p-3">
                <table class="table  table-striped table-hover table-condensed"
                       id="loan_balance_statement">
                    <thead>
                    <tr>
                        <th>{{ trans_choice('core::general.id',1) }}</th>
                        <th>Loan Products Name</th>
                        <th>Transaction Type Name</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                        <th>Date</th>
                        <th>Action</th>
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
                //client_id:"",
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

            function updateSavingStatement(){

                var start = formatDate($("#start_date").val());
				var end = formatDate($("#end_date").val());
				var client_id = $("#client_id").val();
				var branch_id = $("#branch_id").val();

                var data = { start_date: start, end_date: end, client_id: client_id, branch_id: branch_id};

                var loader = '<i class="fa fa-refresh fa-spin fa-fw"></i>';
                $('.opening_balance').html(loader);
                $('.total_debit').html(loader);
                $('.total_credit').html(loader);
                $('.closing_balance').html(loader);

                $('.start_date').html(start);
                $('.end_date').html(end);

                $.ajax({
                    method: "GET",
                    url: '{!! url('client/client_saving_summery') !!}',
                    dataType: "json",
                    data: data,
                    success: function(data){
                        $('.opening_balance').html(data.openingBalance);
                        $('.total_debit').html(data.totalDebit);
                        $('.total_credit').html(data.totalCredit);
                        $('.closing_balance').html(data.closingBalance);
                    }
                });
                saving_balance_statement.ajax.reload();
            }

            function updateLoanStatement(){

                var start = formatDate($("#start_date").val());
                var end = formatDate($("#end_date").val());
                var client_id = $("#client_id").val();
                var branch_id = $("#branch_id").val();

                var data = { start_date: start, end_date: end, client_id: client_id, branch_id: branch_id};

                var loader = '<i class="fa fa-refresh fa-spin fa-fw"></i>';
                $('.loan_opening_balance').html(loader);
                $('.loan_total_debit').html(loader);
                $('.loan_total_credit').html(loader);
                $('.loan_closing_balance').html(loader);

                $('.start_date').html(start);
                $('.end_date').html(end);

                $.ajax({
                    method: "GET",
                    url: '{!! url('client/client_loan_summery') !!}',
                    dataType: "json",
                    data: data,
                    success: function(data){
                        $('.loan_opening_balance').html(data.openingBalance);
                        $('.loan_total_debit').html(data.totalDebit);
                        $('.loan_total_credit').html(data.totalCredit);
                        $('.loan_closing_balance').html(data.closingBalance);
                    }
                });
                loan_balance_statement.ajax.reload();
            }

            function updateClientInfo(){
                var client_id = $("#client_id").val();

                var data = { client_id: client_id};

                var loader = '<i class="fa fa-refresh fa-spin fa-fw"></i>';
                $('.a').html(loader);

                $.ajax({
                    method: "GET",
                    url: '{!! url('client/client_info') !!}',
                    dataType: "json",
                    data: data,
                    success: function(data){
                        $('.a').html(data);
                    }
                });
            }

        $(document).ready(function () {
            $("#client_id").select2({
                width: 'resolve',
                closeOnSelect: true//,
                //theme: "classic"
            });
            saving_balance_statement = $('#saving_balance_statement').DataTable({
                processing: true,
                serverSide: true,
                //ajax: '{!! url('client/client_deposit_report') !!}',
                ajax: {
					"url": '{!! url('client/client_saving_statement') !!}',
					"data": function ( d ) {
						var start = formatDate($("#start_date").val());
						var end = formatDate($("#end_date").val());
						d.start_date = start;
						d.end_date = end;
						d.client_id = $("#client_id").val();
						d.branch_id = $("#branch_id").val();
                        $('.selection_date_saving').html("Saving Balance Statement: " + $("#start_date").val() + "&nbsp;&nbsp;<< TO >>&nbsp;&nbsp;" + $("#end_date").val());
                        //$('.selection_date').html("&nbsp;&nbsp;<< TILL >>&nbsp;&nbsp;" + $("#end_date").val());
					}
				},
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'savings_product_name', name: 'savings_product_name'},
                    {data: 'savings_transaction_type_name', name: 'savings_transaction_type_name'},
                    {data: 'saving_debit', name: 'saving_debit'},
                    {data: 'saving_credit', name: 'saving_credit'},
                    {data: 'balance', name: 'balance'},
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'action', name: 'action'}
                ],
                "order": [[6, "asc"]],
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

            loan_balance_statement = $('#loan_balance_statement').DataTable({
                processing: true,
                serverSide: true,
                //ajax: '{!! url('client/client_deposit_report') !!}',
                ajax: {
					"url": '{!! url('client/client_loan_statement') !!}',
					"data": function ( d ) {
						var start = formatDate($("#start_date").val());
						var end = formatDate($("#end_date").val());
						d.start_date = start;
						d.end_date = end;
						d.client_id = $("#client_id").val();
						d.branch_id = $("#branch_id").val();
                        $('.selection_date_loan').html("Loan Balance Statement: " + $("#start_date").val() + "&nbsp;&nbsp;<< TO >>&nbsp;&nbsp;" + $("#end_date").val());
                        //$('.selection_date').html("&nbsp;&nbsp;<< TILL >>&nbsp;&nbsp;" + $("#end_date").val());
					}
				},
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'loan_product_name', name: 'loan_product_name'},
                    {data: 'loan_transaction_type_name', name: 'loan_transaction_type_name'},
                    {data: 'loan_debit', name: 'loan_debit'},
                    {data: 'loan_credit', name: 'loan_credit'},
                    {data: 'balance', name: 'balance'},
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'action', name: 'action'}
                ],
                "order": [[6, "asc"]],
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
        		//saving_balance_statement.ajax.reload();
                updateSavingStatement();
                updateLoanStatement();
                //updateClientInfo();
    		});
        })
    </script>
@endsection
