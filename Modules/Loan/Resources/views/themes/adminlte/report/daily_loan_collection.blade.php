@extends('core::layouts.master')

@section('title', 'Daily Loan Issuance,  Collection & Rebursement Report')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <style>
        .dt-buttons .btn { margin-right: 5px; }
        #totals-box { font-size: 14px; background: #f8f9fa; padding: 5px 10px; border-radius: 4px; }
        .modal-lg-custom { max-width: 90%; }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Daily Loan Issuance,  Collection & Rebursement Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('report') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Daily Loan  Collection & Rebursement Report</li>
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
                        <label>Start Date</label>
                        <input type="date" id="start_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label>End Date</label>
                        <input type="date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Branch</label>
                        <select id="branch_id" class="form-control">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Loan Officer</label>
                        <select id="loan_officer_id" class="form-control">
                            <option value="">All Officers</option>
                            @foreach($loan_officers as $officer)
                                <option value="{{ $officer->id }}">{{ $officer->first_name }} {{ $officer->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mt-3">
                        <label>Loan Product</label>
                        <select id="loan_product_id" class="form-control">
                            <option value="">All Products</option>
                            @foreach($loan_products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
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
                <table class="table table-striped table-hover table-condensed" id="daily_collection_table" width="100%">
                    <thead>
                        <tr>
                            <th>Loan Officer</th>
                            <th># Loans Issued</th>
                            <th>Total Disbursed</th>
                            <th># Collections</th>
                            <th>Total Collected</th>
                            <th>Net Collection</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th><strong>Totals</strong></th>
                            <th id="total_loans_issued">0</th>
                            <th id="total_disbursed">0.00</th>
                            <th id="total_collection_count">0</th>
                            <th id="total_collected">0.00</th>
                            <th id="net_collection_total">0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <!-- Modal for Disbursements and Collections -->
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
                            <a class="nav-link active" id="disbursements-tab" data-toggle="tab" href="#disbursements" role="tab">Disbursements</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="collections-tab" data-toggle="tab" href="#collections" role="tab">Collections</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="disbursements" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="disbursements-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Client</th>
                                            <th>Product</th>
                                            <th>Principal</th>
                                            <th>Loan ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td colspan="5" class="text-center">Select an officer to view disbursements</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="collections" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="collections-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Client</th>
                                            <th>Product</th>
                                            <th>Amount</th>
                                            <th>Loan ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td colspan="5" class="text-center">Select an officer to view collections</td></tr>
                                    </tbody>
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
        $(document).ready(function() {
            var table = $('#daily_collection_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url("report/loan/daily_collection") }}',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.branch_id = $('#branch_id').val();
                        d.loan_officer_id = $('#loan_officer_id').val();
                        d.loan_product_id = $('#loan_product_id').val();
                        $('.selection_date').html($('#start_date').val() + ' to ' + $('#end_date').val());
                    },
                    dataSrc: function(json) {
                        if (json.totals) {
                            $('#total_loans_issued').text(json.totals.total_loans_issued);
                            $('#total_disbursed').text(parseFloat(json.totals.total_disbursed).toFixed(2));
                            $('#total_collection_count').text(json.totals.total_collection_count);
                            $('#total_collected').text(parseFloat(json.totals.total_collected).toFixed(2));
                            $('#net_collection_total').text(parseFloat(json.totals.net_collection_total).toFixed(2));
                            $('#totals-box').html(`
                                <small><strong>Total Disbursed:</strong> ${parseFloat(json.totals.total_disbursed).toFixed(2)}</small><br>
                                <small><strong>Total Collected:</strong> ${parseFloat(json.totals.total_collected).toFixed(2)}</small><br>
                                <small><strong>Net:</strong> ${parseFloat(json.totals.net_collection_total).toFixed(2)}</small>
                            `);
                        }
                        return json.data;
                    }
                },
                columns: [
                    { data: 'officer_name', name: 'officer_name' },
                    { data: 'loans_issued', name: 'loans_issued' },
                    { data: 'total_disbursed', name: 'total_disbursed' },
                    { data: 'collection_count', name: 'collection_count' },
                    { data: 'total_collected', name: 'total_collected' },
                    { data: 'net_collection', name: 'net_collection' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'asc']],
                responsive: true,
                dom: 'Blfrtip',   // now includes length menu
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                buttons: [
                    { extend: 'copy', text: '<i class="fas fa-copy"></i> Copy', className: 'btn btn-sm btn-secondary', exportOptions: { columns: [0,1,2,3,4,5], modifier: { page: 'all' } } },
                    { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-sm btn-success', exportOptions: { columns: [0,1,2,3,4,5], modifier: { page: 'all' } } },
                    { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-sm btn-info', exportOptions: { columns: [0,1,2,3,4,5], modifier: { page: 'all' } } },
                    { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-sm btn-danger', exportOptions: { columns: [0,1,2,3,4,5], modifier: { page: 'all' } } },
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-sm btn-primary', exportOptions: { columns: [0,1,2,3,4,5] } },
                    { extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-sm btn-dark' }
                ]
            });

            // Reload table on filter change
            $('#start_date, #end_date, #branch_id, #loan_officer_id, #loan_product_id').on('change', function() {
                table.ajax.reload();
            });

            // View More button click: load modal data
            $(document).on('click', '.view-more-btn', function() {
                var officerId = $(this).data('officer-id');
                var officerName = $(this).data('officer-name');
                $('#modal-officer-name').text(officerName);

                // Clear previous data and show loading
                $('#disbursements-table tbody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
                $('#collections-table tbody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');

                $.ajax({
                    url: '{{ url("report/loan/daily_collection") }}',
                    type: 'GET',
                    data: {
                        officer_id: officerId,
                        start_date: $('#start_date').val(),
                        end_date: $('#end_date').val(),
                        branch_id: $('#branch_id').val(),
                        loan_product_id: $('#loan_product_id').val(),
                        ajax: 1
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Populate disbursements table
                        if (response.disbursements && response.disbursements.length > 0) {
                            var disbHtml = '';
                            $.each(response.disbursements, function(i, loan) {
                                disbHtml += '<tr>' +
                                    '<td>' + loan.disbursed_on_date + '</td>' +
                                    '<td>' + (loan.client ? loan.client.first_name + ' ' + loan.client.last_name : 'N/A') + '</td>' +
                                    '<td>' + (loan.loan_product ? loan.loan_product.name : 'N/A') + '</td>' +
                                    '<td>' + parseFloat(loan.principal).toFixed(2) + '</td>' +
                                    '<td><a href="{{ url("loan") }}/' + loan.id + '/show">' + loan.id + '</a></td>' +
                                '</tr>';
                            });
                            $('#disbursements-table tbody').html(disbHtml);
                        } else {
                            $('#disbursements-table tbody').html('<tr><td colspan="5" class="text-center">No disbursements found</td></tr>');
                        }

                        // Populate collections table
                        if (response.collections && response.collections.length > 0) {
                            var collHtml = '';
                            $.each(response.collections, function(i, trans) {
                                collHtml += '<tr>' +
                                    '<td>' + trans.submitted_on + '</td>' +
                                    '<td>' + (trans.loan && trans.loan.client ? trans.loan.client.first_name + ' ' + trans.loan.client.last_name : 'N/A') + '</td>' +
                                    '<td>' + (trans.loan && trans.loan.loan_product ? trans.loan.loan_product.name : 'N/A') + '</td>' +
                                    '<td>' + parseFloat(trans.amount).toFixed(2) + '</td>' +
                                    '<td><a href="{{ url("loan") }}/' + trans.loan_id + '/show">' + trans.loan_id + '</a></td>' +
                                '</tr>';
                            });
                            $('#collections-table tbody').html(collHtml);
                        } else {
                            $('#collections-table tbody').html('<tr><td colspan="5" class="text-center">No collections found</td></tr>');
                        }
                    },
                    error: function() {
                        $('#disbursements-table tbody').html('<tr><td colspan="5" class="text-center">Error loading data</td></tr>');
                        $('#collections-table tbody').html('<tr><td colspan="5" class="text-center">Error loading data</td></tr>');
                    }
                });

                $('#transactionsModal').modal('show');
            });
        });
    </script>
@endsection