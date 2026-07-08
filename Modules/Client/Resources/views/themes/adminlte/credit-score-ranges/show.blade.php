@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.credit_score_range',1) }} {{ $range->rating_label }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.credit_score_range',1) }}
                        <a href="{{ url('client/credit_score_range') }}"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                        </a>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a></li>
                        <li class="breadcrumb-item"><a href="{{url('client/credit_score_range')}}">{{ trans_choice('core::general.credit_score_range',2) }}</a></li>
                        <li class="breadcrumb-item active">{{ $range->rating_label }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card card-bordered card-preview">
            <div class="card-body">
                <div class="row gy-4">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>{{ trans_choice('core::general.min',1) }} {{ trans_choice('core::general.score',1) }}</th>
                                <td>{{ $range->min_score }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans_choice('core::general.max',1) }} {{ trans_choice('core::general.score',1) }}</th>
                                <td>{{ $range->max_score }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans_choice('core::general.label',1) }}</th>
                                <td>{{ $range->rating_label }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>{{ trans_choice('core::general.color',1) }}</th>
                                <td>
                                    <span class="badge" style="background-color: {{ $range->color_code }}; color: #fff;">{{ $range->color_code }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans_choice('core::general.description',1) }}</th>
                                <td>{{ $range->description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans_choice('core::general.order',1) }}</th>
                                <td>{{ $range->sort_order }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <a href="{{ url('client/credit_score_range/'.$range->id.'/edit') }}" class="btn btn-info">{{ trans_choice('core::general.edit',1) }}</a>
                    <a href="{{ url('client/credit_score_range/'.$range->id.'/destroy') }}" class="btn btn-danger" onclick="return confirm('Are you sure?')">{{ trans_choice('core::general.delete',1) }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection