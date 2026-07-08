@extends('core::layouts.master')

@section('title', 'Loan Officer Change Report')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Loan Officer Change Audit Report</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url('report/loan-officer-changes') }}" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="from_date" class="mr-2">From Date:</label>
                            <input type="date" id="from_date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>

                        <div class="form-group mr-2">
                            <label for="to_date" class="mr-2">To Date:</label>
                            <input type="date" id="to_date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>

                        <div class="form-group mr-2">
                            <label for="old_officer_id" class="mr-2">Previous Officer:</label>
                            <select id="old_officer_id" name="old_officer_id" class="form-control">
                                <option value="">All</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}" @if(request('old_officer_id') == $officer->id) selected @endif>
                                        {{ $officer->first_name }} {{ $officer->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mr-2">
                            <label for="new_officer_id" class="mr-2">New Officer:</label>
                            <select id="new_officer_id" name="new_officer_id" class="form-control">
                                <option value="">All</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}" @if(request('new_officer_id') == $officer->id) selected @endif>
                                        {{ $officer->first_name }} {{ $officer->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mr-2">
                            <label for="changed_by_user_id" class="mr-2">Changed By:</label>
                            <select id="changed_by_user_id" name="changed_by_user_id" class="form-control">
                                <option value="">All</option>
                                @foreach($changedByUsers as $user)
                                    <option value="{{ $user->id }}" @if(request('changed_by_user_id') == $user->id) selected @endif>
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Filter</button>
                        <a href="{{ url('report/loan-officer-changes') }}" class="btn btn-secondary">Reset</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Changes History</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Loan Account</th>
                                <th>Client</th>
                                <th>Previous Officer</th>
                                <th>New Officer</th>
                                <th>Changed By</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($audits as $audit)
                                <tr>
                                    <td>{{ $audit->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        @if($audit->loan)
                                            <a href="{{ url('loan/' . $audit->loan_id) }}">
                                                {{ $audit->loan_account_number ?? 'N/A' }}
                                            </a>
                                        @else
                                            {{ $audit->loan_account_number ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($audit->loan && $audit->loan->client)
                                            <a href="{{ url('client/' . $audit->client_id) }}">
                                                {{ $audit->loan->client->first_name }} {{ $audit->loan->client->last_name }}
                                            </a>
                                        @else
                                            {{ $audit->loan->client->first_name ?? 'N/A' }} {{ $audit->loan->client->last_name ?? '' }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-danger">{{ $audit->old_officer_name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ $audit->new_officer_name }}</span>
                                    </td>
                                    <td>{{ $audit->changed_by_user_name }}</td>
                                    <td>
                                        <small>{{ $audit->ip_address }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No changes found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $audits->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
