@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.view',1) }} {{ trans_choice('communication::general.campaign',1) }}
@endsection
@section('content')
    <div class="box box-primary" id="app" style="padding:20px;">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('core::general.view',1) }} {{ trans_choice('communication::general.campaign',1) }}</h3>

            <div class="box-tools">
                <a href="#" onclick="window.history.back()"
                   class="btn btn-info btn-sm">{{ trans_choice('core::general.back',1) }}</a>
            </div>
        </div>
            <div class="box-body">
                {{csrf_field()}}
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
                    <label for="name" class="control-label">{{trans_choice('core::general.name',1)}}</label>
                    <input type="text" name="name" value="{{ old('name') }}" id="name"
                           class="form-control" v-model="name" required readonly>
                </div>
                <div class="form-group">
                    <label class="control-label"
                           for="campaign_type">{{trans_choice('communication::general.campaign',1)}} {{trans_choice('core::general.type',1)}}</label>
                    <select class="form-control" v-model="campaign_type" name="campaign_type" id="campaign_type"
                            required readonly>
                        <option></option>
                        <option value="sms">{{trans_choice('communication::general.sms',1)}}</option>
                        <option value="email">{{trans_choice('communication::general.email',1)}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label"
                           for="trigger_type">{{trans_choice('communication::general.trigger',1)}} {{trans_choice('core::general.type',1)}}</label>
                    <select class="form-control" v-model="trigger_type" name="trigger_type" id="trigger_type"
                            required v-on:click="change_trigger_type" readonly>
                        <option></option>
                        <option value="direct">{{trans_choice('communication::general.direct',1)}}</option>
                        <option value="schedule">{{trans_choice('communication::general.schedule',1)}}</option>
                        <option value="triggered">{{trans_choice('communication::general.triggered',1)}}</option>
                    </select>
                </div>
                <div class="row" v-show="trigger_type=='schedule'">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="scheduled_date"
                                   class="control-label">{{trans_choice('communication::general.schedule',1)}} {{trans_choice('core::general.date',1)}}</label>
                            <input type="text" name="scheduled_date" value="{{ old('scheduled_date') }}"
                                   id="scheduled_date"
                                   class="form-control date-picker" v-model="scheduled_date"
                                   v-bind:required="trigger_type=='schedule'" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="scheduled_time"
                                   class="control-label">{{trans_choice('communication::general.schedule',1)}} {{trans_choice('core::general.time',1)}}</label>
                            <input type="text" name="scheduled_time" value="{{ old('scheduled_time') }}"
                                   id="scheduled_time"
                                   class="form-control time-picker" v-model="scheduled_time"
                                   v-bind:required="trigger_type=='schedule'">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="schedule_frequency"
                                   class="control-label">{{trans_choice('loan::general.schedule',1)}} {{trans_choice('loan::general.frequency',1)}}</label>
                            <input type="number" name="schedule_frequency"
                                   id="schedule_frequency" v-model="schedule_frequency"
                                   class="form-control numeric">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="schedule_frequency_type"
                                   class="control-label">{{trans_choice('loan::general.frequency',1)}} {{trans_choice('core::general.type',1)}}</label>
                            <select class="form-control " name="schedule_frequency_type"
                                    v-model="schedule_frequency_type" id="schedule_frequency_type">
                                <option value="days">{{trans_choice('loan::general.day',2)}}</option>
                                <option value="weeks">{{trans_choice('loan::general.week',2)}}</option>
                                <option value="months">{{trans_choice('loan::general.month',2)}}</option>
                                <option value="years">{{trans_choice('loan::general.year',2)}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group" id="business_rule_msg" v-show="business_rule_description">
                    <div class="alert alert-info">@{{ business_rule_description }}</div>
                </div>
                
                
                <div class="form-group">
                    <label for="description"
                           class="control-label">{{trans_choice('core::general.description',2)}}</label>
                    <textarea type="text" name="description" id="description" class="form-control"
                              required v-model="description" readonly>{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <label class="control-label" for="status">{{trans_choice('core::general.status',1)}}</label>
                    <select class="form-control" name="status" id="status" v-model="status" required readonly>
                        <option></option>
                        <option value="pending">{{trans_choice('core::general.pending',1)}}</option>
                        <option value="active">{{trans_choice('core::general.active',1)}}</option>
                        <option value="inactive">{{trans_choice('core::general.inactive',1)}}</option>
                    </select>
                </div>
            </div>
    </div>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                name: '{{$communication_campaign->name}}',
                subject: '{{$communication_campaign->subject}}',
                trigger_type: '{{$communication_campaign->trigger_type}}',
                campaign_type: '{{$communication_campaign->campaign_type}}',
                communication_campaign_business_rule_id: '{{$communication_campaign->communication_campaign_business_rule_id}}',
                business_rule_description: '',
                sms_gateway_id: '{{$communication_campaign->sms_gateway_id}}',
                communication_campaign_attachment_type_id: '{{$communication_campaign->communication_campaign_attachment_type_id}}',
                branch_id: '{{$communication_campaign->branch_id}}',
                loan_officer_id: '{{$communication_campaign->loan_officer_id}}',
                loan_product_id: '{{$communication_campaign->loan_product_id}}',
                scheduled_date: '{{$communication_campaign->scheduled_date}}',
                scheduled_time: '{{$communication_campaign->scheduled_time}}',
                schedule_frequency: '{{$communication_campaign->schedule_frequency}}',
                schedule_frequency_type: '{{$communication_campaign->schedule_frequency_type}}',
                from_x: '{{$communication_campaign->from_x}}',
                to_y: '{{$communication_campaign->to_y}}',
                cycle_x: '{{$communication_campaign->cycle_x}}',
                cycle_y: '{{$communication_campaign->cycle_y}}',
                overdue_x: '{{$communication_campaign->overdue_x}}',
                overdue_y: '{{$communication_campaign->overdue_y}}',
                status: '{{$communication_campaign->status}}',
                description: '{{$communication_campaign->description}}',
            },
            
        });
    </script>
@endsection