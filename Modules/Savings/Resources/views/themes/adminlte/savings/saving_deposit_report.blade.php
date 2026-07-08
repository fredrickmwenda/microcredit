@extends('core::layouts.master')

@section('title')
    {{ trans_choice('savings::general.deposit_report',2) }}
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <style>
        .dt-buttons .btn { margin-right: 5px; }
        #totals-box { font-size: 14px; background: #f8f9fa; padding: 5px 10px; border-radius: 4px; }
    </style>
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
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a></li>
                        <li class="breadcrumb-item active">{{ trans_choice('savings::general.savings',2) }}</li>
                        <li class="breadcrumb-item active">{{ trans_choice('savings::general.deposit_report',2) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="client_id" class="control-label">Select Client</label>
                            <select class="form-control" name="client_id" id="client_id">
                                <option value="" selected>All</option>
                                @foreach($clients as $key)
                                    <option value="{{$key->id}}">{{$key->first_name . " " .$key->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="savings_officer_id">Savings Officer</label>
                            <select name="savings_officer_id" id="savings_officer_id" class="form-control">
                                <option value="">-- All Officers --</option>
                                @foreach($savings_officers as $officer)
                                    <option value="{{ $officer->id }}">{{ $officer->first_name }} {{ $officer->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="start_date">From Date</label>
                            <input type="date" class="form-control" name="start_date" id="start_date" value="{{ date('Y-m-01') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="end_date">To Date</label>
                            <input type="date" class="form-control" name="end_date" id="end_date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <b><span class="selection_date"></span></b>
                <div class="text-right" id="totals-box">
                    <!-- totals will be inserted here by JS -->
                </div>
            </div>
            <div class="card-body table-responsive p-3">
                <table class="table table-striped table-hover table-condensed" id="deposit_table" width="100%">
                    <thead>
                        <tr>
                            <th>{{ trans_choice('core::general.id',1) }}</th>
                            <th>{{ trans_choice('core::general.name',1) }}</th>
                            <th>Savings Officer</th>
                            <th>Type</th>
                            <th>Deposit Amount</th>
                            <th>Submitted On</th>
                            <th>{{ trans_choice('core::general.status',1) }}</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Approve/Reject Modal -->
    <div class="modal fade" id="approve_reject_savings_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title approve_reject_header"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form method="post" action="{{ url('savings/approve_reject_savings_deposit') }}">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="id" id="savings_transaction_id">
                            <input type="hidden" name="status" id="savings_transaction_status">
                            <label for="remarks" class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                            <textarea name="remarks" class="form-control" id="remarks" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans_choice('core::general.close',1) }}</button>
                        <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- DataTables and export extensions --}}
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <script>
        $(document).ready(function () {
            var deposit_table = $('#deposit_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! url('savings/deposit_report') !!}',
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.client_id = $('#client_id').val();
                        d.savings_officer_id = $('#savings_officer_id').val();
                        $('.selection_date').html($('#start_date').val() + " &nbsp;&nbsp;<< TO >>&nbsp;&nbsp; " + $('#end_date').val());
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'client_name', name: 'client_name' },
                    { data: 'savings_officer_name', name: 'savings_officer_name' },
                    { data: 'trx_name', name: 'trx_name' },
                    { data: 'deposit', name: 'deposit' },
                    { data: 'submitted_on', name: 'submitted_on' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, "desc"]],
                language: {
                    lengthMenu: "{{ trans('general.lengthMenu') }}",
                    zeroRecords: "{{ trans('general.zeroRecords') }}",
                    info: "{{ trans('general.info') }}",
                    infoEmpty: "{{ trans('general.infoEmpty') }}",
                    search: "{{ trans('general.search') }}",
                    infoFiltered: "{{ trans('general.infoFiltered') }}",
                    paginate: {
                        first: "{{ trans('general.first') }}",
                        last: "{{ trans('general.last') }}",
                        next: "{{ trans('general.next') }}",
                        previous: "{{ trans('general.previous') }}"
                    }
                },
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    { 
                        extend: 'copy', 
                        text: '<i class="fa fa-copy"></i> Copy', 
                        className: 'btn btn-sm btn-secondary',
                        exportOptions: { columns: [0,1,2,3,4,5,6] } // exclude action column (index 7)
                    },
                    { 
                        extend: 'excel', 
                        text: '<i class="fa fa-file-excel"></i> Excel', 
                        className: 'btn btn-sm btn-success',
                        exportOptions: { columns: [0,1,2,3,4,5,6] }
                    },
                    { 
                        extend: 'csv', 
                        text: '<i class="fa fa-file-csv"></i> CSV', 
                        className: 'btn btn-sm btn-info',
                        exportOptions: { columns: [0,1,2,3,4,5,6] }
                    },
                    { 
                        extend: 'pdf', 
                        text: '<i class="fa fa-file-pdf"></i> PDF', 
                        className: 'btn btn-sm btn-danger',
                        exportOptions: { columns: [0,1,2,3,4,5,6] }
                    },
                    { 
                        extend: 'print', 
                        text: '<i class="fa fa-print"></i> Print', 
                        className: 'btn btn-sm btn-primary',
                        exportOptions: { columns: [0,1,2,3,4,5,6] }
                    },
                    { 
                        extend: 'colvis', 
                        text: '<i class="fa fa-columns"></i> Columns', 
                        className: 'btn btn-sm btn-dark' 
                    }
                ],
                drawCallback: function (settings) {
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
                        });
                    });
                }
            });

            // Update totals from server response
            deposit_table.on('xhr.dt', function (e, settings, json, xhr) {
                if (json && json.totals) {
                    $('#totals-box').html(`
                        <small><strong>Total Deposits:</strong> ${Number(json.totals.total_deposit ?? 0).toLocaleString()}</small><br>
                        <small><strong>Total Withdrawals:</strong> ${Number(json.totals.total_withdrawal ?? 0).toLocaleString()}</small>
                    `);
                }
            });

            // Approve/Reject modal trigger
            $(document).on("click", ".approval", function () {
                var id = $(this).data('id');
                var status = $(this).data('status');
                $('#savings_transaction_id').val(id);
                $('#savings_transaction_status').val(status);
                if (status == "approved") {
                    $('.approve_reject_header').html("Approve Deposit");
                }
                if (status == "declined") {
                    $('.approve_reject_header').html("Decline Deposit");
                }
                $('#approve_reject_savings_modal').modal('toggle');
            });

            // Reload table when any filter changes
            $('#start_date, #end_date, #client_id, #savings_officer_id').on('change', function () {
                deposit_table.ajax.reload();
            });
        });
    </script>
@endsection
