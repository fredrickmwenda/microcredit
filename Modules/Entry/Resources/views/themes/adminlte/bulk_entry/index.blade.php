@extends('core::layouts.master')
@section('title')
    Savings Bulk Entry
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        Savings Bulk Entry
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
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card card-bordered card-preview">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title">Bulk Entry List</h5>
                    </div>
                    <div class="col-md-4 text-right">
                        @can('entry.savings_bulk_entry.create')
                            <a href="{{ url('entry/savings/bulk_entry/create') }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Create Bulk Entry
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-8">
                        <form method="get" action="{{ route('bulk_entry.index') }}" class="form-inline">
                            <div class="form-group mb-2 mr-2">
                                <label for="date" class="mr-2">Date:</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ request('date', now()->toDateString()) }}">
                            </div>
                            <button type="submit" class="btn btn-info mb-2 mr-2">Filter</button>
                            <a href="{{ route('bulk_entry.index') }}" class="btn btn-secondary mb-2 mr-2">Reset</a>
                        </form>
                    </div>
                    <div class="col-md-4 text-right">
                        <form method="get" action="{{ route('bulk_entry.export') }}" class="d-inline">
                            <input type="hidden" name="date" value="{{ request('date', now()->toDateString()) }}">
                            <button type="submit" name="format" value="xlsx" class="btn btn-success mb-2 mr-2"><i class="fa fa-file-excel-o"></i> Export Excel</button>
                            <button type="submit" name="format" value="csv" class="btn btn-outline-success mb-2"><i class="fa fa-file-text-o"></i> Export CSV</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Officer</th>
                                        <th>Created By</th>
                                        <th>Items</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $entry)
                                        <tr>
                                            <td>#{{ $entry->id }}</td>
                                            <td>{{ $entry->savings_officer->first_name }} {{ $entry->savings_officer->last_name }}</td>
                                            <td>{{ $entry->created_by->first_name }} {{ $entry->created_by->last_name }}</td>
                                            <td>{{ $entry->items->count() }}</td>
                                            <td>
                                                @if($entry->status === 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($entry->status === 'verified')
                                                    <span class="badge badge-success">Verified</span>
                                                @elseif($entry->status === 'rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>{{ $entry->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ url('entry/savings/bulk_entry/'.$entry->id.'/show') }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No entries found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if(method_exists($data, 'render'))
                    <div class="row mt-3">
                        <div class="col-md-12">
                            {{ $data->render() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
