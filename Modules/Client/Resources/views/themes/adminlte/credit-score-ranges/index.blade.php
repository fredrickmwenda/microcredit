@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.credit_score_range',2) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.credit_score_range',2) }}
                        <a href="{{ url('client/credit_score_range/create') }}"
                           class="btn btn-info d-none d-sm-inline-flex">
                            <em class="icon ni ni-plus"></em><span>{{ trans_choice('core::general.add',1) }}</span>
                        </a>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a></li>
                        <li class="breadcrumb-item"><a href="{{url('client')}}">{{ trans_choice('client::general.client',2) }}</a></li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.credit_score_range',2) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card card-bordered card-preview">
            <div class="card-body">
                <table class="table table-bordered table-striped" id="credit_score_ranges_table">
                    <thead>
                        <tr>
                            <th>{{ trans_choice('core::general.min',1) }} {{ trans_choice('core::general.score',1) }}</th>
                            <th>{{ trans_choice('core::general.max',1) }} {{ trans_choice('core::general.score',1) }}</th>
                            <th>{{ trans_choice('core::general.label',1) }}</th>
                            <th>{{ trans_choice('core::general.color',1) }}</th>
                            <th>{{ trans_choice('core::general.description',1) }}</th>
                            <th>{{ trans_choice('core::general.order',1) }}</th>
                            <th>{{ trans_choice('core::general.action',2) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ranges as $range)
                        <tr>
                            <td>{{ $range->min_score }}</td>
                            <td>{{ $range->max_score }}</td>
                            <td>{{ $range->rating_label }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $range->color_code }}; color: #fff;">{{ $range->color_code }}</span>
                            </td>
                            <td>{{ $range->description ?? '-' }}</td>
                            <td>{{ $range->sort_order }}</td>
 


                            <td>
                                <div class="btn-group">
                                    <button href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        <i class="ri-settings-3-line"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                       <a href="{{ url('client/credit_score_range/'.$range->id.'/edit') }}" class="dropdown-item">
                                            <i class="ri-edit-fill"></i> {{ trans_choice('core::general.edit', 1) }}

                                        </a>
                                        <a href="{{ url('client/credit_score_range/'.$range->id.'/destroy') }}" class="dropdown-item" onclick="return confirm('Are you sure?')">
                                            <i class="ri-delete-bin-fill"></i>
                                            <span>{{trans_choice('core::general.delete',1)}}</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        $('#credit_score_ranges_table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    </script>
@endsection