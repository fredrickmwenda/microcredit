@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('communication::general.send_sms_form',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('communication::general.send_sms_form',1) }}
                        <a href="#" onclick="window.history.back()"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                        </a>
                    </h1>

                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item">{{ trans_choice('communication::general.communication',2) }}</li>
                        <li class="breadcrumb-item active">{{ trans_choice('communication::general.send_sms_form',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('communication/process_sms_send') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client_id"
                                    class="control-label">Select Client</label>
                                <select class="form-control"  @change="onChange($event)" name="client_id"
                                        id="client_id" v-model="client_id" required>
                                    <option value="" selected>Please Select...</option>
                                    @foreach($clients as $key)
                                        <option value="{{$key->id}}">{{$key->first_name . " " .$key->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sms_gateway_id"
                                    class="control-label">Select Sms Gateway</label>
                                <select class="form-control"  @change="onChange($event)" name="sms_gateway_id"
                                        id="sms_gateway_id" v-model="sms_gateway_id" required>
                                    <option value="" selected>Please Select...</option>
                                    @foreach($smsGateways as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sms"
                                       class="control-label">{{trans_choice('communication::general.sms',2)}}</label>
                                <textarea type="text" name="sms" id="sms"
                                          class="form-control @error('sms') is-invalid @enderror"
                                          required v-model="sms"></textarea>
                                @error('sms')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                </div>
                <div class="card-footer border-top ">
                    <button type="submit"
                            class="btn btn-primary  float-right">{{trans_choice('communication::general.send',1)}}</button>
                </div>
            </div><!-- .card-preview -->
        </form>
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                sms: "{{ old('sms') }}",
                client_id: '{{ old("client_id") }}',
                sms_gateway_id: '{{ old("sms_gateway_id") }}',
                /*branches: branches,
                users: users,
                business_rules: business_rules,
                attachment_types: attachment_types,
                loan_products: loan_products,
                sms_gateways: sms_gateways*/

            },
        });
    </script>
@endsection