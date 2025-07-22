@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('communication::general.gateway',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('communication::general.gateway',1) }}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <form method="post" action="{{url('communication/sms_gateway/'.$sms_gateway->id.'/update')}}" class="form">
            {{csrf_field()}}
            <div class="box-body">
                @if (count($errors) > 0)
                    <div class="form-group has-feedback">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <label for="key" class="control-label">API Key</label>
                    <input type="text" name="key" value="{{ $sms_gateway->key }}" id="key" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="sender" class="control-label">Sender Name</label>
                    <input type="text" name="sender" value="{{ $sms_gateway->sender }}" id="sender" class="form-control" required>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
            </div>
        </form>
    </div>
@endsection
@section('scripts')

@endsection