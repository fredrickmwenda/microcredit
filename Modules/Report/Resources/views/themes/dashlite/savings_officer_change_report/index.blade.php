@extends('core::master')

@section('title', 'Savings Officer Change Report')

@section('content')
<div class="nk-content">
    <div class="container-xl wide-xl">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h4 class="nk-block-title">Savings Officer Change Audit Report</h4>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-inner">
                            <form method="GET" action="{{ url('report/savings-officer-changes') }}" class="row gy-3">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">From Date</label>
                                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">To Date</label>
                                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Previous Officer</label>
                                        <select name="old_officer_id" class="form-control">
                                            <option value="">All</option>
                                            @foreach($officers as $officer)
                                                <option value="{{ $officer->id }}" @if(request('old_officer_id') == $officer->id) selected @endif>
                                                    {{ $officer->first_name }} {{ $officer->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">New Officer</label>
                                        <select name="new_officer_id" class="form-control">
                                            <option value="">All</option>
                                            @foreach($officers as $officer)
                                                <option value="{{ $officer->id }}" @if(request('new_officer_id') == $officer->id) selected @endif>
                                                    {{ $officer->first_name }} {{ $officer->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Changed By</label>
                                        <select name="changed_by_user_id" class="form-control">
                                            <option value="">All</option>
                                            @foreach($changedByUsers as $user)
                                                <option value="{{ $user->id }}" @if(request('changed_by_user_id') == $user->id) selected @endif>
                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="btn-group w-100" role="group">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                            <a href="{{ url('report/savings-officer-changes') }}" class="btn btn-light">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-inner">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>Savings Account</th>
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
                                                    @if($audit->savings)
                                                        <a href="{{ url('savings/' . $audit->savings_id) }}">
                                                            {{ $audit->savings_account_number ?? 'N/A' }}
                                                        </a>
                                                    @else
                                                        {{ $audit->savings_account_number ?? 'N/A' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($audit->savings && $audit->savings->client)
                                                        <a href="{{ url('client/' . $audit->client_id) }}">
                                                            {{ $audit->savings->client->first_name }} {{ $audit->savings->client->last_name }}
                                                        </a>
                                                    @else
                                                        {{ $audit->savings->client->first_name ?? 'N/A' }} {{ $audit->savings->client->last_name ?? '' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger">{{ $audit->old_officer_name }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">{{ $audit->new_officer_name }}</span>
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
                        </div>
                        <div class="card-footer">
                            {{ $audits->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
