@extends('core::layouts.master')
@section('title')
    Verify Bulk Savings Entry
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        Verify Entry #{{ $entry->id }}
                        <a href="#" onclick="window.history.back()"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                        </a>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a></li>
                        <li class="breadcrumb-item"><a href="{{url('entry/savings/bulk_entry')}}">Bulk Entry</a></li>
                        <li class="breadcrumb-item active">Verify</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="card card-bordered">
                    <div class="card-body">
                        <h5>Entry Information</h5>
                        <dl class="small">
                            <dt>ID:</dt>
                            <dd>#{{ $entry->id }}</dd>
                            <dt>Officer:</dt>
                            <dd>{{ $entry->savings_officer->first_name }} {{ $entry->savings_officer->last_name }}</dd>
                            <dt>Submitted By:</dt>
                            <dd>{{ $entry->created_by->first_name }} {{ $entry->created_by->last_name }}</dd>
                            <dt>Submitted At:</dt>
                            <dd>{{ $entry->created_at->format('Y-m-d H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card card-bordered border-success">
                    <div class="card-header bg-success text-white">
                        <h5>Entry Summary for Verification</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-primary">{{ $stats['total_items'] }}</h3>
                                    <p class="text-muted">Total Items</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-success">{{ number_format($stats['total_deposits'], 2) }}</h3>
                                    <p class="text-muted">Deposits ({{ $stats['deposit_count'] }})</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-warning">{{ number_format($stats['total_withdrawals'], 2) }}</h3>
                                    <p class="text-muted">Withdrawals ({{ $stats['withdrawal_count'] }})</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-info">{{ number_format($stats['total_deposits'] - $stats['total_withdrawals'], 2) }}</h3>
                                    <p class="text-muted">Net Amount</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-bordered mt-3">
                    <div class="card-header">
                        <h5>Transactions to Process</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Client</th>
                                        <th>Account</th>
                                        <th>Type</th>
                                        <th class="text-right">Amount</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entry->items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->client->first_name }} {{ $item->client->last_name }}</strong>
                                            </td>
                                            <td>
                                                <code>{{ $item->savings->account_number }}</code>
                                            </td>
                                            <td>
                                                @if($item->transaction_type === 'deposit')
                                                    <span class="badge badge-success">DEPOSIT</span>
                                                @else
                                                    <span class="badge badge-warning">WITHDRAWAL</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <strong>{{ number_format($item->amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                <small>{{ $item->notes ?? '-' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card card-bordered mt-3 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5>Verification Decision</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Important:</strong> Please review all items carefully before proceeding. 
                            Verified entries will be immediately processed into the system.
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <form method="post" action="{{ url('entry/savings/bulk_entry/'.$entry->id.'/verify_entries') }}">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-success btn-block btn-lg" 
                                            onclick="return confirm('Proceed with verification and processing? This action cannot be undone.')">
                                        <i class="fa fa-check-circle"></i> Verify and Process All
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger btn-block btn-lg" data-toggle="modal" data-target="#rejectModal">
                                    <i class="fa fa-times-circle"></i> Reject Entry
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Bulk Entry</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('entry/savings/bulk_entry/'.$entry->id.'/reject_entries') }}">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rejection_reason"><strong>Please provide a reason for rejection:</strong></label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" 
                                      rows="5" placeholder="Enter reason for rejection..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
