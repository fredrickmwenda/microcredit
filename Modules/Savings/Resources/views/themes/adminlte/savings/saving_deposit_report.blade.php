@extends('core::layouts.master')
@section('title')
    {{ trans_choice('savings::general.deposit_report',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Saving Deposit Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('savings::general.savings',2) }}</li>
                        <li class="breadcrumb-item active">{{ trans_choice('savings::general.deposit_report',2) }}</li>
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
                        <th>{{ trans_choice('core::general.name',1) }}</th>
                        <th>Type</th>
                        <th>Deposit Amount</th>
                        <th>Submitted On</th>
                        <th>{{ trans_choice('core::general.status',1) }}</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
           
        </div>
    </section>

    <div class="modal fade" id="approve_reject_savings_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title approve_reject_header"></h4>
                    <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">×</span></button>

                </div>
                <form method="post"
                      action="{{ url('savings/approve_reject_savings_deposit') }}">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="id" id="savings_transaction_id">
                            <input type="hidden" name="status" id="savings_transaction_status">
                            <label for="remarks"
                                   class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                            <textarea name="remarks"
                                      class="form-control"
                                      id="remarks"
                                      rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-default pull-left"
                                data-bs-dismiss="modal">
                            {{ trans_choice('core::general.close',1) }}
                        </button>
                        <button type="submit"
                                class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
					"url": '{!! url('savings/deposit_report') !!}',
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
                    {data: 'client_name', name: 'client_name'},
                    {data: 'trx_name', name: 'trx_name'},
                    {data: 'deposit', name: 'deposit'},
                    {data: 'submitted_on', name: 'submitted_on'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action'}
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

            $(document).on("click", ".approval", function () {
                var id = $(this).data('id');
                var status = $(this).data('status');
                $('#savings_transaction_id').val( id );
                $('#savings_transaction_status').val( status );
                if(status == "approved"){
                    $('.approve_reject_header').html("Approve Deposit");
                }
                if(status == "declined"){
                    $('.approve_reject_header').html("Decline Deposit");
                }
                $('#approve_reject_savings_modal').modal('toggle');

            });



            $(' #start_date, #end_date, #client_id').change( function(){
        		deposit_table.ajax.reload();
    		});
        })
    </script>
@endsection
