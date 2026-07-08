@extends('core::layouts.master')

@section('title')
    Staff Savings Summary Report
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <style>
        .dt-buttons .btn { margin-right: 5px; }
        #totals-box { font-size: 14px; background: #f8f9fa; padding: 5px 10px; border-radius: 4px; }
        .modal-lg-custom { max-width: 90%; }
        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter {
            margin-bottom: 10px;
        }
    </style>
@stop

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Staff Savings Summary Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('report') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Staff Summary</li>
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
                            <input type="date" class="form-control" name="start_date" id="start_date" value="{{ date('Y-m-d') }}">
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
                <div class="text-right" id="totals-box"></div>
            </div>
            <div class="card-body table-responsive p-3">
                <table class="table table-striped table-hover table-condensed" id="staff-summary-table" width="100%">
                    <thead>
                        <tr>
                            <th>Officer Name</th>
                            <th># Clients</th>
                            <th>Total Deposits</th>
                            <th>Total Withdrawals</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Modal for Deposits and Withdrawals -->
    <div class="modal fade" id="transactionsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg-custom modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transactions for <span id="modal-officer-name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="transactionTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="deposits-tab" data-toggle="tab" href="#deposits" role="tab">Deposits</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="withdrawals-tab" data-toggle="tab" href="#withdrawals" role="tab">Withdrawals</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="deposits" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="deposits-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Client Name</th>
                                            <th>Amount</th>
                                            <th>Savings Account</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="withdrawals" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="withdrawals-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Client Name</th>
                                            <th>Amount</th>
                                            <th>Savings Account</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
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
            // Main summary DataTable
            var summaryTable = $('#staff-summary-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url("report/savings/staff-summary") }}',
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.client_id = $('#client_id').val();
                        d.savings_officer_id = $('#savings_officer_id').val();
                        $('.selection_date').html($('#start_date').val() + " &nbsp;&nbsp;<< TO >>&nbsp;&nbsp; " + $('#end_date').val());
                    }
                },
                columns: [
                    { data: 'officer_name', name: 'officer_name' },
                    { data: 'total_clients', name: 'total_clients' },
                    { data: 'total_deposits', name: 'total_deposits' },
                    { data: 'total_withdrawals', name: 'total_withdrawals' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, "asc"]],
                language: {
                    lengthMenu: "Show _MENU_ entries",
                    zeroRecords: "No records found",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    search: "Search:",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                responsive: true,
                dom: 'Blfrtip',   // now includes length menu
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                buttons: [
                    { extend: 'copy', text: '<i class="fa fa-copy"></i> Copy', className: 'btn btn-sm btn-secondary', exportOptions: { columns: [0,1,2,3], modifier: { page: 'all' } } },
                    { extend: 'excel', text: '<i class="fa fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success', exportOptions: { columns: [0,1,2,3], modifier: { page: 'all' } } },
                    { extend: 'csv', text: '<i class="fa fa-file-csv"></i> CSV', className: 'btn btn-sm btn-info', exportOptions: { columns: [0,1,2,3], modifier: { page: 'all' } } },
                    { extend: 'pdf', text: '<i class="fa fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger', exportOptions: { columns: [0,1,2,3], modifier: { page: 'all' } } },
                    { extend: 'print', text: '<i class="fa fa-print"></i> Print', className: 'btn btn-sm btn-primary', exportOptions: { columns: [0,1,2,3], modifier: { page: 'all' } } },
                    { extend: 'colvis', text: '<i class="fa fa-columns"></i> Columns', className: 'btn btn-sm btn-dark' }
                ]
            });

            // Update totals box
            summaryTable.on('xhr.dt', function (e, settings, json, xhr) {
                if (json && json.totals) {
                    $('#totals-box').html(`
                        <small><strong>Total Deposits (all officers):</strong> ${Number(json.totals.grand_deposits ?? 0).toLocaleString()}</small><br>
                        <small><strong>Total Withdrawals (all officers):</strong> ${Number(json.totals.grand_withdrawals ?? 0).toLocaleString()}</small>
                    `);
                }
            });

            // Reload main table on filter change
            $('#start_date, #end_date, #client_id, #savings_officer_id').on('change', function () {
                summaryTable.ajax.reload();
            });

            // Variables to hold DataTable instances
            var depositsTable = null;
            var withdrawalsTable = null;

            // Function to destroy modal tables if they exist
            function destroyModalTables() {
                if (depositsTable) {
                    depositsTable.destroy();
                    depositsTable = null;
                }
                if (withdrawalsTable) {
                    withdrawalsTable.destroy();
                    withdrawalsTable = null;
                }
            }

            // Function to initialize deposits DataTable
            function initDepositsTable(officerId) {
                if (depositsTable) {
                    depositsTable.destroy();
                }
                depositsTable = $('#deposits-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ url("report/savings/staff-summary") }}',
                        data: {
                            table: 'deposits',
                            officer_id: officerId,
                            start_date: $('#start_date').val(),
                            end_date: $('#end_date').val(),
                            client_id: $('#client_id').val()
                        }
                    },
                    columns: [
                        { data: 'submitted_on', name: 'submitted_on' },
                        { data: 'time', name: 'time' },
                        { data: 'client_name', name: 'client_name' },
                        { data: 'amount_formatted', name: 'amount' },
                        { data: 'account_number', name: 'account_number' }
                    ],
                    order: [[0, 'desc']],
                    language: {
                        lengthMenu: "Show _MENU_ entries",
                        zeroRecords: "No deposits found",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "No deposits",
                        search: "Search:",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    dom: 'Blfrtip',   // now includes length menu
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    buttons: [
                        { extend: 'copy', text: '<i class="fa fa-copy"></i> Copy', className: 'btn btn-sm btn-secondary', exportOptions: { columns: [0,1,2,3,4], modifier: { page: 'all' } } },
                        { extend: 'excel', text: '<i class="fa fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success', exportOptions: { columns: [0,1,2,3,4], modifier: { page: 'all' } } },
                        { extend: 'csv', text: '<i class="fa fa-file-csv"></i> CSV', className: 'btn btn-sm btn-info', exportOptions: { columns: [0,1,2,3,4], modifier: { page: 'all' } } },
                        { extend: 'pdf', text: '<i class="fa fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger', exportOptions: { columns: [0,1,2,3,4], modifier: { page: 'all' } } },
                        { extend: 'print', text: '<i class="fa fa-print"></i> Print', className: 'btn btn-sm btn-primary', exportOptions: { columns: [0,1,2,3,4], modifier: { page: 'all' } } },
                        { extend: 'colvis', text: '<i class="fa fa-columns"></i> Columns', className: 'btn btn-sm btn-dark' }
                    ]
                });
            }

            // Function to initialize withdrawals DataTable
            function initWithdrawalsTable(officerId) {
                if (withdrawalsTable) {
                    withdrawalsTable.destroy();
                }
                withdrawalsTable = $('#withdrawals-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ url("report/savings/staff-summary") }}',
                        data: {
                            table: 'withdrawals',
                            officer_id: officerId,
                            start_date: $('#start_date').val(),
                            end_date: $('#end_date').val(),
                            client_id: $('#client_id').val()
                        }
                    },
                    columns: [
                        { data: 'submitted_on', name: 'submitted_on' },
                        { data: 'time', name: 'time' },
                        { data: 'client_name', name: 'client_name' },
                        { data: 'amount_formatted', name: 'amount' },
                        { data: 'account_number', name: 'account_number' }
                    ],
                    order: [[0, 'desc']],
                    language: {
                        lengthMenu: "Show _MENU_ entries",
                        zeroRecords: "No withdrawals found",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "No withdrawals",
                        search: "Search:",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    dom: 'Blfrtip',   // now includes length menu
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    buttons: [
                        { extend: 'copy', text: '<i class="fa fa-copy"></i> Copy', className: 'btn btn-sm btn-secondary', exportOptions: { columns: [0,1,2,3,4], modifier: { page: 'all' } } },
                        { extend: 'excel', text: '<i class="fa fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success', exportOptions: { columns: [0,1,2,3,4], modifier: { page: 'all' } } },
                        { extend: 'csv', text: '<i class="fa fa-file-csv"></i> CSV', className: 'btn btn-sm btn-info', exportOptions: { columns: [0,1,2,3,4], modifier: { page: 'all' } } },
                        { extend: 'pdf', text: '<i class="fa fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger', exportOptions: { columns: [0,1,2,3,4], modifier: { page: 'all' } } },
                        { extend: 'print', text: '<i class="fa fa-print"></i> Print', className: 'btn btn-sm btn-primary', exportOptions: { columns: [0,1,2,3,4], modifier: { page: 'all' } } },
                        { extend: 'colvis', text: '<i class="fa fa-columns"></i> Columns', className: 'btn btn-sm btn-dark' }
                    ]
                });
            }

            // View More button click: open modal and initialize DataTables
            $(document).on('click', '.view-more-btn', function () {
                var officerId = $(this).data('officer-id');
                var officerName = $(this).data('officer-name');
                $('#modal-officer-name').text(officerName);
                
                // Destroy old tables if any
                destroyModalTables();
                
                // Initialize new tables with current filters
                initDepositsTable(officerId);
                initWithdrawalsTable(officerId);
                
                // Show modal
                $('#transactionsModal').modal('show');
            });
            
            // Optional: reload modal tables if filters change while modal is open
            $('#start_date, #end_date, #client_id').on('change', function () {
                if ($('#transactionsModal').hasClass('show')) {
                    // Reload both tables with new filters
                    if (depositsTable) depositsTable.ajax.reload();
                    if (withdrawalsTable) withdrawalsTable.ajax.reload();
                }
            });
        });
    </script>
@endsection