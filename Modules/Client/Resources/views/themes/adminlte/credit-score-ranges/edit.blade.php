@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('core::general.credit_score_range',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.edit',1) }} {{ trans_choice('core::general.credit_score_range',1) }}
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
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.edit',1) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <form method="post" action="{{ url('client/credit_score_range/'.$range->id.'/update') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="min_score" class="control-label">{{ trans_choice('core::general.min',1) }} {{ trans_choice('core::general.score',1) }}</label>
                                <input type="number" name="min_score" id="min_score" class="form-control numeric" value="{{ $range->min_score }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="max_score" class="control-label">{{ trans_choice('core::general.max',1) }} {{ trans_choice('core::general.score',1) }}</label>
                                <input type="number" name="max_score" id="max_score" class="form-control numeric" value="{{ $range->max_score }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sort_order" class="control-label">{{ trans_choice('core::general.order',1) }}</label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control numeric" value="{{ $range->sort_order }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rating_label" class="control-label">{{ trans_choice('core::general.label',1) }}</label>
                                <input type="text" name="rating_label" id="rating_label" class="form-control" value="{{ $range->rating_label }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="color_code" class="control-label">{{ trans_choice('core::general.color',1) }}</label>
                                <input type="color" name="color_code" id="color_code" class="form-control" value="{{ $range->color_code }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description" class="control-label">{{ trans_choice('core::general.description',1) }}</label>
                                <textarea name="description" id="description" rows="2" class="form-control">{{ $range->description }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-top">
                    <button type="submit" class="btn btn-primary float-right">{{ trans_choice('core::general.save',1) }}</button>
                </div>
            </div>
        </form>
    </section>
@endsection