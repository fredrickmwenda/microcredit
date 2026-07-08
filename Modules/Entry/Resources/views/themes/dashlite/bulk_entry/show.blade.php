@extends('core::layouts.master')
@section('title')
    Bulk Savings Entry Details
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        Entry #{{ $entry->id }}
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
                        <li class="breadcrumb-item active">Details</li>
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
                        <dl>
                            <dt>ID:</dt>
                            <dd>#{{ $entry->id }}</dd>
                            <dt>Officer:</dt>
                            <dd>{{ $entry->savings_officer->first_name }} {{ $entry->savings_officer->last_name }}</dd>
                            <dt>Created By:</dt>
                            <dd>{{ $entry->created_by->first_name }} {{ $entry->created_by->last_name }}</dd>
                            <dt>Created At:</dt>
                            <dd>{{ $entry->created_at->format('Y-m-d H:i') }}</dd>
                            <dt>Status:</dt>
                            <dd>
                                @if($entry->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($entry->status === 'verified')
                                    <span class="badge badge-success">Verified</span>
                                @elseif($entry->status === 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @endif
                            </dd>
                        </dl>

                        @if($entry->status === 'verified')
                            <dl>
                                <dt>Verified By:</dt>
                                <dd>{{ $entry->verified_by->first_name }} {{ $entry->verified_by->last_name }}</dd>
                                <dt>Verified At:</dt>
                                <dd>{{ $entry->verified_at->format('Y-m-d H:i') }}</dd>
                            </dl>
                        @elseif($entry->status === 'rejected')
                            <dl>
                                <dt>Rejected By:</dt>
                                <dd>{{ $entry->verified_by->first_name }} {{ $entry->verified_by->last_name }}</dd>
                                <dt>Rejected At:</dt>
                                <dd>{{ $entry->rejected_at->format('Y-m-d H:i') }}</dd>
                                <dt>Reason:</dt>
                                <dd>{{ $entry->rejection_reason }}</dd>
                            </dl>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card card-bordered">
                    <div class="card-header">
                        <h5>Entry Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="alert alert-info">
                                    <strong>Total Items:</strong><br>
                                    {{ $stats['total_items'] }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="alert alert-success">
                                    <strong>Total Deposits:</strong><br>
                                    {{ number_format($stats['total_deposits'], 2) }} ({{ $stats['deposit_count'] }} items)
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="alert alert-warning">
                                    <strong>Total Withdrawals:</strong><br>
                                    {{ number_format($stats['total_withdrawals'], 2) }} ({{ $stats['withdrawal_count'] }} items)
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="alert alert-secondary">
                                    <strong>Net Amount:</strong><br>
                                    {{ number_format($stats['total_deposits'] - $stats['total_withdrawals'], 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-bordered mt-3">
                    <div class="card-header">
                        <h5>Transaction Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Account Number</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Notes</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entry->items as $item)
                                        <tr>
                                            <td>{{ $item->client->first_name }} {{ $item->client->last_name }}</td>
                                            <td>{{ $item->savings->account_number }}</td>
                                            <td>
                                                @if($item->transaction_type === 'deposit')
                                                    <span class="badge badge-success">Deposit</span>
                                                @else
                                                    <span class="badge badge-warning">Withdrawal</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($item->amount, 2) }}</td>
                                            <td>{{ $item->notes ?? '-' }}</td>
                                            <td>
                                                @if($item->savings_transaction_id)
                                                    <span class="badge badge-success">Processed</span>
                                                @else
                                                    <span class="badge badge-secondary">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Verification/Rejection Actions --}}
                @if($entry->status === 'pending' && Auth::user()->hasRole('Savings Operator'))
                    <div class="card card-bordered mt-3 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5>Verification Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="post" action="{{ url('entry/savings/bulk_entry/'.$entry->id.'/verify_entries') }}" style="display: inline;">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-success btn-block" 
                                                onclick="return confirm('Are you sure you want to verify and process all entries?')">
                                            <i class="fa fa-check"></i> Verify and Process
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#rejectModal">
                                        <i class="fa fa-times"></i> Reject Entry
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Entry</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('entry/savings/bulk_entry/'.$entry->id.'/reject_entries') }}">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rejection_reason">Rejection Reason</label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" 
                                      rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
