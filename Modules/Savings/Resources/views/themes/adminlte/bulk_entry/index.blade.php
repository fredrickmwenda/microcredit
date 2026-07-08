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
                        <li class="breadcrumb-item"><a href="{{url('bulk_entry')}}">Bulk Entry</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card card-bordered card-preview">
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
                                                <a href="{{ url('bulk_entry/'.$entry->id.'/show') }}" class="btn btn-sm btn-info">
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
