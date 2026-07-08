@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.loan_application',1) }} {{ $application->reference_number }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('loan::general.loan_application',1) }}
                        <a href="#" onclick="window.history.back()"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                        </a>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a></li>
                        <li class="breadcrumb-item"><a href="{{url('loan/application')}}">{{ trans_choice('loan::general.loan_application',2) }}</a></li>
                        <li class="breadcrumb-item active">{{ $application->reference_number }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content" id="app">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-bordered card-preview">
                    <div class="card-header">
                        <h5 class="card-title">{{ $application->full_name }} ({{ $application->reference_number }})</h5>
                        <div class="card-tools">
                            <div class="float-right btn-group">
                                @if($application->level1_status === 'Pending')
                                    <a href="#" data-toggle="modal" data-target="#level1_review_modal" class="btn btn-primary">
                                        <i class="ri-user-star-line"></i> {{ trans_choice('loan::general.level1_review',1) }}
                                    </a>
                                    <a href="#" data-toggle="modal" data-target="#reject_modal" class="btn btn-danger">
                                        <i class="ri-close-circle-line"></i> {{ trans_choice('core::general.reject',1) }}
                                    </a>
                                @endif
                                @if($application->level1_status === 'Approved' && $application->level2_status === 'Pending')
                                    <a href="#" data-toggle="modal" data-target="#level2_approve_modal" class="btn btn-success">
                                        <i class="ri-shield-user-line"></i> {{ trans_choice('loan::general.level2_approve',1) }}
                                    </a>
                                    <a href="#" data-toggle="modal" data-target="#reject_modal" class="btn btn-danger">
                                        <i class="ri-close-circle-line"></i> {{ trans_choice('core::general.reject',1) }}
                                    </a>
                                    <a href="#" data-toggle="modal" data-target="#defer_modal" class="btn btn-warning">
                                        <i class="ri-time-line"></i> {{ trans_choice('core::general.defer',1) }}
                                    </a>
                                @endif
                                @if($application->overall_status === 'Converted' && $application->level2_status === 'Approved')
                                    @if($application->loan_id)
                                             

                                        @can('loan.loans.ceo_approval')
                                                    @if($application->$loan->status=='submitted' ||$application->$loan->status=='pending' || $application->$loan->status=='pending_ceo_approval')
                                                @can('loan.loans.approve_loan')
                                                <a href="#" data-toggle="modal" data-target="#approve_loan_modal" class="btn btn-primary"><i class="fas fa-check"></i>
                                                    {{ trans_choice('loan::general.approve',1) }}
                                                </a>
                                                <a href="#" data-toggle="modal" data-target="#reject_loan_modal" class="btn btn-primary"><i class="fas fa-times"></i>
                                                    {{ trans_choice('loan::general.reject',1) }}
                                                </a>
                                                <a href="#" data-toggle="modal" data-target="#withdraw_loan_modal" class="btn btn-primary"><i class="fas fa-times"></i>
                                                    {{ trans_choice('loan::general.withdraw',1) }}
                                                </a>
                                                @endcan
                                                @can('loan.loans.edit')
                                                <a href="{{url('loan/'.$application->$loan->id.'/edit')}}" class="btn btn-primary">
                                                    <i class="ri-edit-fill"></i>
                                                    {{ trans_choice('core::general.edit',1) }}
                                                </a>
                                                @endcan
                                                @endif
                                            
                                            
                                            
                                            @else

                                            @if($application->$loan->status=='submitted' ||$application->$loan->status=='pending')
                                                @can('loan.loans.approve_loan')
                                                <a href="#" data-toggle="modal" data-target="#approve_loan_modal" class="btn btn-primary"><i class="fas fa-check"></i>
                                                    {{ trans_choice('loan::general.approve',1) }}
                                                </a>
                                                <a href="#" data-toggle="modal" data-target="#reject_loan_modal" class="btn btn-primary"><i class="fas fa-times"></i>
                                                    {{ trans_choice('loan::general.reject',1) }}
                                                </a>
                                                <a href="#" data-toggle="modal" data-target="#withdraw_loan_modal" class="btn btn-primary"><i class="fas fa-times"></i>
                                                    {{ trans_choice('loan::general.withdraw',1) }}
                                                </a>
                                                @endcan
                                                @can('loan.loans.edit')
                                                <a href="{{url('loan/'.$application->$loan->id.'/edit')}}" class="btn btn-primary">
                                                    <i class="ri-edit-fill"></i>
                                                    {{ trans_choice('core::general.edit',1) }}
                                                </a>
                                                @endcan
                                            @endif
                                            
                                        @endcan
                    
                    
                                        @can('loan.loans.approve_loan')
                                            <div class="modal fade" id="approve_loan_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('loan::general.approve',1) }} {{ trans_choice('loan::general.loan',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <form method="post" action="{{ url('loan/'.$application->$loan->id.'/approve_loan') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="approved_on_date" class="control-label">{{ trans_choice('core::general.date',1) }}</label>
                                                                    <flat-pickr class="form-control  @error('approved_on_date') is-invalid @enderror" name="approved_on_date" value="{{date("Y-m-d")}}" id="approved_on_date" required>
                                                                    </flat-pickr>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="approved_amount" class="control-label">{{ trans_choice('core::general.amount',1) }}</label>
                                                                    <input type="number" name="approved_amount" class="form-control numeric" value="{{$application->$loan->applied_amount}}" required="" id="approved_amount">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="approved_notes" class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="approved_notes" class="form-control" id="approved_notes" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default float-left" data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="reject_loan_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('loan::general.reject',1) }} {{ trans_choice('loan::general.loan',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post" action="{{ url('loan/'.$application->$loan->id.'/reject_loan') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="rejected_notes" class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="rejected_notes" class="form-control" id="rejected_notes" rows="3" required=""></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="withdraw_loan_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('loan::general.withdraw',1) }} {{ trans_choice('loan::general.loan',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post" action="{{ url('loan/'.$application->$loan->id.'/withdraw_loan') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="withdrawn_notes" class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="withdrawn_notes" class="form-control" id="withdrawn_notes" rows="3" required=""></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan


                                        @if($application->$loan->status=='active')
                                            @can('loan.loans.transactions.create')
                                            <a href="{{url('loan/'.$application->$loan->id.'/repayment/create')}}" class="btn btn-primary"><i class="fas fa-dollar-sign"></i>
                                                {{ trans_choice('loan::general.make',1) }} {{ trans_choice('loan::general.repayment',1) }}
                                            </a>
                                            @endcan

                                            @can('loan.loans.disburse_loan')
                                            <a href="{{url('loan/'.$application->$loan->id.'/undo_disbursement')}}" class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('loan::general.undo',1) }} {{ trans_choice('loan::general.disbursement',1) }}
                                            </a>
                                            @endcan

                                            @can('loan.loans.edit')
                                            <a href="#" data-toggle="modal" data-target="#change_loan_officer_modal" class="btn btn-primary">
                                                {{ trans_choice('loan::general.change',1) }} {{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.officer',1) }}
                                            </a>
                                            @endcan

                                            @can('loan.loans.charges.create')
                                            <a href="{{url('loan/'.$application->$loan->id.'/charge/create')}}" class="btn btn-primary"><i class="fa fa-plus"></i>
                                                {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.charge',1) }}
                                            </a>
                                            @endcan

                                            @can('loan.loans.transactions.edit')
                                            <a href="#" data-toggle="modal" data-target="#waive_interest_modal" class="btn btn-primary">
                                                {{ trans_choice('loan::general.waive',1) }} {{ trans_choice('loan::general.interest',1) }}
                                            </a>
                                            @endcan

                                            @can('loan.loans.write_off_loan')
                                            <a href="#" data-toggle="modal" data-target="#write_off_loan_modal" class="btn btn-primary">
                                                {{ trans_choice('loan::general.write_off',1) }} {{ trans_choice('loan::general.loan',1) }}
                                            </a>
                                            @endcan

                                            @can('loan.loans.reschedule_loan')
                                            <a href="#" data-toggle="modal" data-target="#reschedule_loan_modal" class="btn btn-primary">
                                                {{ trans_choice('loan::general.reschedule',1) }} {{ trans_choice('loan::general.loan',1) }}
                                            </a>
                                            @endcan

                                            @can('loan.loans.edit')
                                            <div class="modal fade" id="change_loan_officer_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('loan::general.change',1) }} {{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.officer',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post" action="{{ url('loan/'.$application->$loan->id.'/change_loan_officer') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="loan_officer_id" class="control-label">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</label>
                                                                    <select class="form-control select2" name="loan_officer_id" id="loan_officer_id" v-model="loan_officer_id" required>
                                                                        <option value=""></option>
                                                                        @foreach($users as $key)
                                                                        <option value="{{$key->id}}" @if($key->id==$application->$loan->loan_officer_id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endcan

                                            @can('loan.loans.transactions.edit')
                                            <div class="modal fade" id="waive_interest_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('loan::general.waive',1) }} {{ trans_choice('loan::general.interest',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post" action="{{ url('loan/'.$application->$loan->id.'/waive_interest') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="date" class="control-label">{{ trans_choice('core::general.date',1) }}</label>
                                                                    <flat-pickr class="form-control  @error('date') is-invalid @enderror" name="date" value="{{date("Y-m-d")}}" id="date" required>
                                                                    </flat-pickr>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="interest_waived_amount" class="control-label">{{ trans_choice('core::general.amount',1) }}</label>
                                                                    <input type="text" name="interest_waived_amount" class="form-control numeric" value="" required="" id="interest_waived_amount">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="description" class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="description" class="form-control" id="description" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endcan

                                            @can('loan.loans.write_off_loan')
                                            <div class="modal fade" id="write_off_loan_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('loan::general.write_off',1) }} {{ trans_choice('loan::general.loan',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post" action="{{ url('loan/'.$application->$loan->id.'/write_off_loan') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="written_off_on_date" class="control-label">{{ trans_choice('core::general.date',1) }}</label>
                                                                    <flat-pickr class="form-control  @error('written_off_on_date') is-invalid @enderror" name="written_off_on_date" value="{{date("Y-m-d")}}" id="written_off_on_date" required>
                                                                    </flat-pickr>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="written_off_notes" class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="written_off_notes" class="form-control" id="written_off_notes" rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endcan

                                            @can('loan.loans.reschedule_loan')
                                            <div class="modal fade" id="reschedule_loan_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('loan::general.reschedule',1) }} {{ trans_choice('loan::general.loan',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post" action="{{ url('loan/'.$application->$loan->id.'/reschedule_loan') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="rescheduled_from_date" class="control-label">
                                                                        {{ trans_choice('loan::general.reschedule_from_installment_on',1) }}
                                                                    </label>
                                                                    <flat-pickr v-model="rescheduled_from_date" name="rescheduled_from_date" class="form-control @error('rescheduled_from_date') is-invalid @enderror" :required="true" id="rescheduled_from_date"></flat-pickr>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="rescheduled_on_date" class="control-label">
                                                                        {{ trans_choice('core::general.submitted_on',1) }}
                                                                    </label>
                                                                    <flat-pickr v-model="rescheduled_on_date" name="rescheduled_on_date" class="form-control @error('rescheduled_on_date') is-invalid @enderror" required id="rescheduled_on_date"></flat-pickr>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="reschedule_first_payment_date" class="">
                                                                        <input type="checkbox" id="reschedule_first_payment_date" name="rescheduled_first_payment_date" v-model="reschedule_first_payment_date" class="" />

                                                                        {{trans_choice('loan::general.change_repayment_date',1)}}
                                                                    </label>
                                                                </div>
                                                                <div class="form-group" v-if="reschedule_first_payment_date">
                                                                    <label for="rescheduled_first_payment_date" class="control-label">
                                                                        {{ trans_choice('loan::general.adjusted_due_date',1) }}
                                                                    </label>
                                                                    <flat-pickr v-model="rescheduled_first_payment_date" name="rescheduled_first_payment_date" class="form-control @error('rescheduled_first_payment_date') is-invalid @enderror" required></flat-pickr>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="reschedule_adjust_loan_interest_rate" class="">
                                                                        <input type="checkbox" id="reschedule_adjust_loan_interest_rate" name="reschedule_adjust_loan_interest_rate" v-model="reschedule_adjust_loan_interest_rate" class="" />

                                                                        {{trans_choice('loan::general.adjust_loan_interest_rate',1)}}
                                                                    </label>
                                                                </div>
                                                                <div class="form-group" v-if="reschedule_adjust_loan_interest_rate">
                                                                    <label for="reschedule_interest_rate" class="control-label">
                                                                        {{ trans_choice('loan::general.interest',1) }} {{ trans_choice('loan::general.rate',1) }}
                                                                    </label>
                                                                    <input type="text" id="reschedule_interest_rate" name="reschedule_interest_rate" v-model="reschedule_interest_rate" class="form-control" required />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="reschedule_add_extra_installments" class="">
                                                                        <input type="checkbox" id="reschedule_add_extra_installments" name="reschedule_add_extra_installments" v-model="reschedule_add_extra_installments" class="" />

                                                                        {{trans_choice('loan::general.add_extra_installments',1)}}
                                                                    </label>
                                                                </div>
                                                                <div class="form-group" v-if="reschedule_add_extra_installments">
                                                                    <label for="reschedule_extra_installments" class="control-label">
                                                                        {{ trans_choice('loan::general.extra_installment',2) }}
                                                                    </label>
                                                                    <input type="text" id="reschedule_extra_installments" name="reschedule_extra_installments" v-model="reschedule_extra_installments" class="form-control numeric" required />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="reschedule_enable_grace_periods" class="">
                                                                        <input type="checkbox" id="reschedule_enable_grace_periods" name="reschedule_enable_grace_periods" v-model="reschedule_enable_grace_periods" class="" />

                                                                        {{trans_choice('loan::general.introduce_grace_periods',1)}}
                                                                    </label>
                                                                </div>
                                                                <div class="form-group" v-if="reschedule_enable_grace_periods">
                                                                    <label for="reschedule_grace_on_principal_paid" class="control-label">
                                                                        {{ trans_choice('loan::general.grace_on_principal_paid',1) }}
                                                                    </label>
                                                                    <input type="text" id="reschedule_grace_on_principal_paid" name="reschedule_grace_on_principal_paid" v-model="reschedule_grace_on_principal_paid" class="form-control numeric" />
                                                                </div>
                                                                <div class="form-group" v-if="reschedule_enable_grace_periods">
                                                                    <label for="reschedule_grace_on_interest_paid" class="control-label">
                                                                        {{ trans_choice('loan::general.grace_on_interest_paid',1) }}
                                                                    </label>
                                                                    <input type="text" id="reschedule_grace_on_interest_paid" name="reschedule_grace_on_interest_paid" v-model="reschedule_grace_on_interest_paid" class="form-control numeric" />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="rescheduled_notes" class="control-label">{{ trans_choice('core::general.note',2) }}</label>
                                                                    <textarea name="rescheduled_notes" v-model="rescheduled_notes" class="form-control" id="rescheduled_notes" rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endcan

                                        @endif


                                        @if($application->$loan->status=='written_off')
                                            <a href="{{url('loan/'.$application->$loan->id.'/repayment/create')}}" class="btn btn-primary"><i class="fas fa-dollar-sign"></i>
                                                {{ trans_choice('loan::general.recovery',1) }} {{ trans_choice('loan::general.payment',1) }}
                                            </a>
                                            <a href="{{url('loan/'.$application->$loan->id.'/undo_write_off')}}" class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('loan::general.undo',1) }} {{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.write_off',1) }}
                                            </a>
                                        @endif


                                        @if($application->$loan->status=='approved')
                                            @can('loan.loans.disburse_loan')
                                            <a href="#" data-toggle="modal" data-target="#disburse_loan_modal" class="btn btn-primary"><i class="fas fa-flag"></i>
                                                {{ trans_choice('loan::general.disburse',1) }}
                                            </a>
                                            @endcan

                                            @can('loan.loans.edit')
                                            <a href="#" data-toggle="modal" data-target="#change_loan_officer_modal" class="btn btn-primary">
                                                {{ trans_choice('loan::general.change',1) }} {{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.officer',1) }}
                                            </a>
                                            @endcan

                                            @can('loan.loans.approve_loan')
                                            <a href="{{url('loan/'.$application->$loan->id.'/undo_approval')}}" class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('loan::general.undo',1) }} {{ trans_choice('loan::general.approval',1) }}
                                            </a>
                                            @endcan

                                            @can('loan.loans.edit')
                                            <div class="modal fade" id="change_loan_officer_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('loan::general.change',1) }} {{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.officer',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post" action="{{ url('loan/'.$application->$loan->id.'/change_loan_officer') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="loan_officer_id" class="control-label">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</label>
                                                                    <select class="form-control select2" name="loan_officer_id" id="loan_officer_id" v-model="loan_officer_id" required>
                                                                        <option value=""></option>
                                                                        @foreach($users as $key)
                                                                        <option value="{{$key->id}}" @if($key->id==$application->$loan->loan_officer_id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endcan

                                            @can('loan.loans.disburse_loan')
                                            <div class="modal fade in" id="disburse_loan_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ trans_choice('loan::general.disburse',1) }} {{ trans_choice('loan::general.loan',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>×</span></button>
                                                        </div>
                                                        <form method="post" action="{{ url('loan/'.$application->$loan->id.'/disburse_loan') }}" class="form-horizontal">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="disbursed_on_date" class="control-label">{{ trans_choice('loan::general.actual',1) }} {{ trans_choice('loan::general.disbursement',1) }} {{ trans_choice('core::general.date',1) }}</label>

                                                                    <flat-pickr name="disbursed_on_date" value="{{$application->$loan->expected_disbursement_date}}" class="form-control @error('disbursed_on_date') is-invalid @enderror" :required="true" id="rescheduled_from_date"></flat-pickr>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="first_payment_date" class="control-label">{{ trans_choice('core::general.first',1) }} {{ trans_choice('loan::general.repayment',1) }} {{ trans_choice('core::general.date',1) }}</label>

                                                                    <flat-pickr name="first_payment_date" value="{{$application->$loan->expected_first_payment_date}}" class="form-control @error('first_payment_date') is-invalid @enderror" :required="true" id="first_payment_date"></flat-pickr>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="payment_type_id" class="control-label">{{ trans_choice('loan::general.payment',1) }}
                                                                        {{ trans_choice('core::general.type',1) }}
                                                                    </label>
                                                                    <select class="form-control select2" name="payment_type_id" id="payment_type_id" v-model="payment_type_id" required>
                                                                        <option value=""></option>
                                                                        @foreach($payment_types as $key)
                                                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="approved_amount" class="control-label">{{ trans_choice('core::general.show',1) }}
                                                                        {{ trans_choice('loan::general.payment',1) }} {{ trans_choice('core::general.detail',2) }}</label>
                                                                    <button type="button" class="btn btn-primary collapsed" data-toggle="collapse" data-target="#show_payment_details" aria-expanded="false">
                                                                        <i class="fa fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                                <div id="show_payment_details" class="collapse">
                                                                    <div class="form-group">
                                                                        <label for="account_number" class="control-label">{{ trans_choice('core::general.account',1) }}
                                                                            #</label>

                                                                        <input type="text" name="account_number" class="form-control" value="" id="account_number">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="cheque_number" class="control-label">{{ trans_choice('core::general.cheque',1) }}
                                                                            #</label>
                                                                        <input type="text" name="cheque_number" class="form-control" value="" id="cheque_number">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="routing_code" class="control-label">{{ trans_choice('core::general.routing_code',1) }}</label>
                                                                        <input type="text" name="routing_code" class="form-control" value="" id="routing_code">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="receipt_number" class="control-label">{{ trans_choice('core::general.receipt',1) }}
                                                                            #</label>
                                                                        <input type="text" name="receipt_number" class="form-control" value="" id="receipt_number">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="bank" class="control-label">{{ trans_choice('core::general.bank',1) }}
                                                                            #</label>
                                                                        <input type="text" name="bank" class="form-control" value="" id="bank">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="disbursed_notes" class="control-label">{{ trans_choice('core::general.note',2) }}</label>

                                                                    <textarea name="disbursed_notes" class="form-control" id="disbursed_notes" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                                                                    {{ trans_choice('core::general.close',1) }}
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endcan

                                        @endif


                                        @if($application->$loan->status=='rejected')
                                            @can('loan.loans.approve_loan')
                                            <a href="{{url('loan/'.$application->$loan->id.'/undo_rejection')}}" class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('loan::general.undo',1) }} {{ trans_choice('loan::general.rejection',1) }}
                                            </a>
                                            @endcan

                                        @endif


                                        @if($application->$loan->status=='withdrawn')
                                            @can('loan.loans.approve_loan')
                                            <a href="{{url('loan/'.$application->$loan->id.'/undo_withdrawn')}}" class="btn btn-primary confirm"><i class="fa fa-undo"></i>
                                                {{ trans_choice('loan::general.undo',1) }} {{ trans_choice('loan::general.withdrawn',1) }}
                                            </a>
                                            @endcan
                                        @endif
                                         
                                    @endif
                                     <?php
                                        $loan = \Modules\Loan\Entities\Loan::where('loan_pre_application_id', $application->id)->first();
                                        $clientId = $loan ? $loan->client_id : null;
                                    ?>
                                    @if($clientId)
                                    <a href="{{url('client/'.$clientId.'/show')}}" class="btn btn-info">
                                        <i class="ri-user-line"></i> {{ trans_choice('client::general.view_client',1) }}
                                    </a>
                                    <a href="{{url('client/'.$clientId.'/credit_score')}}" class="btn btn-success">
                                        <i class="ri-bar-chart-box-line"></i> {{ trans_choice('client::general.credit_score',1) }}
                                    </a>
                                    @endif

                                @endif                           
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Level 1 Review Modal -->
                        @if($application->level1_status === 'Pending')
                        <div class="modal fade" id="level1_review_modal">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">{{ trans_choice('loan::general.level1_loan_officer_review',1) }}</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form method="post" action="{{ url('loan/application/'.$application->id.'/level1') }}"> 
                                        {{csrf_field()}}
                                        <div class="modal-body">
                                            <div class="row gy-4">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="income_stability_score" class="control-label">{{ trans_choice('core::general.income_stability',1) }} (0-100)</label>
                                                        <input type="number" name="income_stability_score" id="income_stability_score" min="0" max="100" class="form-control numeric" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="debt_to_income_score" class="control-label">{{ trans_choice('core::general.debt_to_income',1) }} (0-100)</label>
                                                        <input type="number" name="debt_to_income_score" id="debt_to_income_score" min="0" max="100" class="form-control numeric" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="credit_history_score" class="control-label">{{ trans_choice('core::general.credit_history',1) }} (0-100)</label>
                                                        <input type="number" name="credit_history_score" id="credit_history_score" min="0" max="100" class="form-control numeric" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row gy-4">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="employment_length_score" class="control-label">{{ trans_choice('core::general.employment_length',1) }} (0-100)</label>
                                                        <input type="number" name="employment_length_score" id="employment_length_score" min="0" max="100" class="form-control numeric" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="guarantor_strength_score" class="control-label">{{ trans_choice('core::general.guarantor_strength',1) }} (0-100)</label>
                                                        <input type="number" name="guarantor_strength_score" id="guarantor_strength_score" min="0" max="100" class="form-control numeric" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="level1_status" class="control-label">{{ trans_choice('core::general.decision',1) }}</label>
                                                        <select class="form-control" name="level1_status" id="level1_status" required>
                                                            <option value="Approved">{{ trans_choice('core::general.approve',1) }}</option>
                                                            <option value="Declined">{{ trans_choice('core::general.decline',1) }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row gy-4">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="recommended_amount" class="control-label">{{ trans_choice('loan::general.recommended_amount',1) }} (GHS)</label>
                                                        <input type="number" step="0.01" name="recommended_amount" id="recommended_amount" class="form-control numeric" required>
                                                    </div>
                                                </div>
                                                <!-- Notes for Level 1 -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="level1_notes" class="control-label">{{ trans_choice('loan::general.level1_notes',1) }}</label>
                                                        <textarea class="form-control" name="level1_notes" id="level1_notes" rows="3"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default float-left" data-dismiss="modal">
                                                {{ trans_choice('core::general.close',1) }}
                                            </button>
                                            <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Level 2 Approve Modal -->
                        @if($application->level1_status === 'Approved' && $application->level2_status === 'Pending')
                            <div class="modal fade" id="level2_approve_modal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">{{ trans_choice('loan::general.level2_manager_approval',1) }}</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <form method="post" action="{{ url('loan/application/'.$application->id.'/level2') }}">
                                            {{csrf_field()}}
                                            <div class="modal-body">
                                                <div class="alert alert-info">
                                                    <p><strong>{{ trans_choice('loan::general.total_score',1) }}:</strong> {{ $application->total_score }}/500</p>
                                                    <p><strong>{{ trans_choice('loan::general.risk_rating',1) }}:</strong> {{ $application->risk_rating }}</p>
                                                    <p><strong>{{ trans_choice('loan::general.recommended',1) }}:</strong> GHS {{ number_format($application->recommended_amount, 2) }}</p>
                                                </div>
                                                <div class="form-group">
                                                    <label for="level2_status" class="control-label">{{ trans_choice('core::general.decision',1) }}</label>
                                                    <select class="form-control" name="level2_status" id="level2_status" required>
                                                        <option value="Approved">{{ trans_choice('core::general.approve',1) }}</option>
                                                        <option value="Declined">{{ trans_choice('core::general.decline',1) }}</option>
                                                        <option value="Deferred">{{ trans_choice('core::general.defer',1) }}</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="approved_amount" class="control-label">{{ trans_choice('loan::general.approved_amount',1) }} (GHS)</label>
                                                    <input type="number" step="0.01" name="approved_amount" id="approved_amount" class="form-control numeric" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="level2_notes" class="control-label">{{ trans_choice('loan::general.level2_notes',1) }}</label>
                                                    <textarea class="form-control" name="level2_notes" id="level2_notes" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default float-left" data-dismiss="modal">
                                                    {{ trans_choice('core::general.close',1) }}
                                                </button>
                                                <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="defer_modal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">{{ trans_choice('core::general.defer',1) }} {{ trans_choice('loan::general.application',1) }}</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <form method="post" action="{{ url('loan/application/'.$application->id.'/level2') }}">
                                            {{csrf_field()}}
                                            <input type="hidden" name="level2_status" value="Deferred">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label class="control-label">{{ trans_choice('core::general.reason',1) }}</label>
                                                    <textarea name="reason" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default float-left" data-dismiss="modal">
                                                    {{ trans_choice('core::general.close',1) }}
                                                </button>
                                                <button type="submit" class="btn btn-warning">{{ trans_choice('core::general.defer',1) }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Reject Modal -->
                        <div class="modal fade" id="reject_modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">{{ trans_choice('core::general.reject',1) }} {{ trans_choice('loan::general.application',1) }}</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form method="post" action="{{ url('loan/application/'.$application->id.'/level1') }}">
                                        {{csrf_field()}}
                                        <input type="hidden" name="level1_status" value="Declined">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="control-label">{{ trans_choice('core::general.reason',1) }}</label>
                                                <textarea name="rejected_notes" class="form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default float-left" data-dismiss="modal">
                                                {{ trans_choice('core::general.close',1) }}
                                            </button>
                                            <button type="submit" class="btn btn-danger">{{ trans_choice('core::general.reject',1) }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-7 col-md-7 p-10">
                                @if($application->level1_status !== 'Pending')
                                <h4 class="">
                                    {{ trans_choice('loan::general.total_score',1) }}
                                    : <b>{{$application->total_score}}/500</b>
                                </h4>
                                <h4 class="">
                                    {{ trans_choice('loan::general.risk_rating',1) }}
                                    : <b class="@if($application->risk_rating=='High') text-danger @elseif($application->risk_rating=='Medium') text-warning @else text-success @endif">{{$application->risk_rating ?? 'N/A'}}</b>
                                </h4>
                                @if($application->recommended_amount)
                                <h4 class="">
                                    {{ trans_choice('loan::general.recommended_amount',1) }}
                                    : <b>GHS {{number_format($application->recommended_amount,2)}}</b>
                                </h4>
                                @endif
                                @if($application->approved_amount)
                                <h4 class="">
                                    {{ trans_choice('loan::general.approved_amount',1) }}
                                    : <b>GHS {{number_format($application->approved_amount,2)}}</b>
                                </h4>
                                @endif
                                @endif

                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ trans_choice('core::general.criteria',1) }}</th>
                                            <th>{{ trans_choice('loan::general.score',1) }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th>{{ trans_choice('core::general.income_stability',1) }}</th>
                                            <td>{{$application->income_stability_score ?? 0}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans_choice('core::general.debt_to_income',1) }}</th>
                                            <td>{{$application->debt_to_income_score ?? 0}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans_choice('core::general.credit_history',1) }}</th>
                                            <td>{{$application->credit_history_score ?? 0}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans_choice('core::general.employment_length',1) }}</th>
                                            <td>{{$application->employment_length_score ?? 0}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans_choice('core::general.guarantor_strength',1) }}</th>
                                            <td>{{$application->guarantor_strength_score ?? 0}}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>{{ trans_choice('loan::general.total',1) }}</th>
                                            <th>{{$application->total_score ?? 0}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-sm-5 col-md-5">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('core::general.status',1) }}</th>
                                            <td>
                                                <span class="badge badge-{{ $application->overall_status == 'Converted' ? 'success' : ($application->overall_status == 'Declined' ? 'danger' : ($application->overall_status == 'Under Review' ? 'warning' : 'info')) }}">
                                                    {{ $application->overall_status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('loan::general.level1_status',1) }}</th>
                                            <td>
                                                <span class="badge badge-{{ $application->level1_status == 'Approved' ? 'success' : ($application->level1_status == 'Declined' ? 'danger' : 'warning') }}">
                                                    {{ $application->level1_status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('loan::general.level2_review',1) }}</th>
                                            <td>
                                                <span class="badge badge-{{ $application->level2_status == 'Approved' ? 'success' : ($application->level2_status == 'Declined' ? 'danger' : ($application->level2_status == 'Deferred' ? 'info' : 'warning')) }}">
                                                    {{ $application->level2_status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('core::general.name',1) }}</th>
                                            <td>{{$application->first_name}}-{{$application->last_name}}</td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('core::general.ghana_card_number',1) }}</th>
                                            <td>{{$application->ghana_card_number}}</td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('core::general.phone',1) }}</th>
                                            <td>{{$application->phone_number}}</td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('core::general.email',1) }}</th>
                                            <td>{{$application->email}}</td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('loan::general.loan_amount_requested',1) }}</th>
                                            <td>GHS {{number_format($application->loan_amount_requested,2)}}</td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('loan::general.purpose_of_loan',1) }}</th>
                                            @if($application->purpose_of_loan)
                                                <td>{{ $application->purpose_of_loan }}</td>
                                            @else
                                            <?php
                                                $purpose = \Modules\Loan\Entities\LoanProduct::find($application->loan_product_id);
                                            ?>
                                                <td>{{ $purpose ? $purpose->name : 'N/A' }}</td>
                                            @endif
                                            
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('loan::general.repayment_period',1) }}</th>
                                            <td>{{$application->repayment_period}} {{ trans_choice('loan::general.month',2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('loan::general.preferred_repayment_method',1) }}</th>
                                            <td>{{$application->preferred_repayment_method}}</td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('core::general.employment_status',1) }}</th>
                                            <td>{{$application->employment_status}}</td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('core::general.monthly_net_income',1) }}</th>
                                            <td>GHS {{number_format($application->monthly_net_income ?? 0,2)}}</td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('core::general.submitted_on',1) }}</th>
                                            <td>{{$application->submitted_at?->format('Y-m-d')}}</td>
                                        </tr>
                                        @if($application->level1_decision_at)
                                            <tr>
                                                <th class="table-bold-loan">{{ trans_choice('loan::general.level1_decision_at',1) }}</th>
                                                <td>
                                                    {{ $application->level1_decision_at->format('Y-m-d h:i A') }}
                                                    {{ trans_choice('core::general.by',1) }}
                                                    {{ $application->loanOfficer?->name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                        @endif

                                        @if($application->level2_decision_at)
                                            <tr>
                                                <th class="table-bold-loan">{{ trans_choice('loan::general.level2_decision_at',1) }}</th>
                                                <td>
                                                    {{ $application->level2_decision_at->format('Y-m-d h:i A') }}
                                                    {{ trans_choice('core::general.by',1) }}
                                                    {{ $application->manager?->name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($application->loan_id)
        <div class="row my-4">
            <div class="col-md-12">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a href="#account_details" class="nav-link active"
                                   data-toggle="tab">
                                    {{ trans_choice('loan::general.account',1) }} {{ trans_choice('core::general.detail',2) }}
                                </a>
                            </li>
                            @if($application->$loan->status=='active' ||$application->$loan->status=='closed'||$application->$loan->status=='written_off'||$application->$loan->status=='overpaid'||$application->$loan->status=='rescheduled')
                                <li class="nav-item">
                                    <a href="#repayment_schedule" class="nav-link"
                                       data-toggle="tab">
                                        {{ trans_choice('loan::general.repayment',1) }} {{ trans_choice('loan::general.schedule',1) }}
                                    </a>
                                </li>
                                @can('loan.loans.transactions.index')
                                    <li class="nav-item">
                                        <a href="#loan_transactions" class="nav-link"
                                           data-toggle="tab">
                                            {{ trans_choice('loan::general.transaction',2) }}
                                        </a>
                                    </li>
                                @endcan
                            @endif
                            @can('loan.loans.charges.index')
                                <li class="nav-item">
                                    <a href="#loan_charges" class="nav-link"
                                       data-toggle="tab">
                                        {{ trans_choice('loan::general.charge',2) }}
                                    </a>
                                </li>
                            @endcan
                            @can('loan.loans.files.index')
                                <li class="nav-item">
                                    <a href="#loan_files" class="nav-link"
                                       data-toggle="tab">
                                        {{ trans_choice('loan::general.file',2) }}
                                    </a>
                                </li>
                            @endcan
                            @can('loan.loans.collateral.index')
                                <li class="nav-item">
                                    <a href="#loan_collateral" class="nav-link"
                                       data-toggle="tab">
                                        {{ trans_choice('loan::general.collateral',2) }}
                                    </a>
                                </li>
                            @endcan
                            @can('loan.loans.guarantors.index')
                                <li class="nav-item">
                                    <a href="#loan_guarantors" class="nav-link"
                                       data-toggle="tab">
                                        {{ trans_choice('loan::general.guarantor',2) }}
                                    </a>
                                </li>
                            @endcan
                            @can('loan.loans.notes.index')
                                <li class="nav-item">
                                    <a href="#loan_notes" class="nav-link"
                                       data-toggle="tab">
                                        {{ trans_choice('core::general.note',2) }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="account_details">
                                <table class="table table-striped table-hover">
                                    <tbody>
                                    <tr>
                                        <td>{{trans_choice('loan::general.loan_transaction_processing_strategy',1)}}</td>
                                        <td>
                                            @if(!empty($application->$loan->loan_transaction_processing_strategy))
                                                {{$application->$loan->loan_transaction_processing_strategy->translated_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.term',1)}}</td>
                                        <td>
                                            {{$application->$loan->loan_term}}
                                            @if($application->$loan->repayment_frequency_type=='days')
                                                {{trans_choice('loan::general.day',2)}}
                                            @endif
                                            @if($application->$loan->repayment_frequency_type=='weeks')
                                                {{trans_choice('loan::general.week',2)}}
                                            @endif
                                            @if($application->$loan->repayment_frequency_type=='months')
                                                {{trans_choice('loan::general.month',2)}}
                                            @endif
                                            @if($application->$loan->repayment_frequency_type=='years')
                                                {{trans_choice('loan::general.year',2)}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('loan::general.repayment',2)}}</td>
                                        <td>
                                            {{trans_choice('loan::general.every',1)}} {{$application->$loan->repayment_frequency}}
                                            @if($application->$loan->repayment_frequency_type=='days')
                                                {{trans_choice('loan::general.day',2)}}
                                            @endif
                                            @if($application->$loan->repayment_frequency_type=='weeks')
                                                {{trans_choice('loan::general.week',2)}}
                                            @endif
                                            @if($application->$loan->repayment_frequency_type=='months')
                                                {{trans_choice('loan::general.month',2)}}
                                            @endif
                                            @if($application->$loan->repayment_frequency_type=='years')
                                                {{trans_choice('loan::general.year',2)}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('loan::general.interest_methodology',1)}}</td>
                                        <td>
                                            @if($application->$loan->interest_methodology=='flat')
                                                {{trans_choice('loan::general.flat',1)}}
                                            @endif
                                            @if($application->$loan->interest_methodology=='declining_balance')
                                                {{trans_choice('loan::general.declining_balance',1)}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('loan::general.interest',1)}}</td>
                                        <td>
                                            {{number_format($application->$loan->interest_rate,2)}} %
                                            {{trans_choice('loan::general.per',1)}}
                                            @if($application->$loan->interest_rate_type=='month')
                                                {{trans_choice('loan::general.month',1)}}
                                            @endif
                                            @if($application->$loan->interest_rate_type=='year')
                                                {{trans_choice('loan::general.year',1)}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('loan::general.grace_on_principal_paid',1)}}</td>
                                        <td>
                                            {{$application->$loan->grace_on_principal_paid}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('loan::general.grace_on_interest_paid',1)}}</td>
                                        <td>
                                            {{$application->$loan->grace_on_interest_paid}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('loan::general.grace_on_interest_charged',1)}}</td>
                                        <td>
                                            {{$application->$loan->grace_on_interest_charged}}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>{{trans_choice('core::general.submitted_on',1)}}</td>
                                        <td>
                                            {{$application->$loan->submitted_on_date}}
                                            @if(!empty($application->$loan->submitted_by))
                                                {{trans_choice('core::general.by',1)}}
                                                {{$application->$loan->submitted_by->first_name}} {{$application->$loan->submitted_by->last_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('loan::general.approved',1)}} {{trans_choice('core::general.on',1)}}</td>
                                        <td>
                                            {{$application->$loan->approved_on_date}}
                                            @if(!empty($application->$loan->approved_by))
                                                {{trans_choice('core::general.by',1)}}
                                                {{$application->$loan->approved_by->first_name}} {{$application->$loan->approved_by->last_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{trans_choice('loan::general.disbursed',1)}} {{trans_choice('core::general.on',1)}}</td>
                                        <td>
                                            {{$application->$loan->disbursed_on_date}}
                                            @if(!empty($application->$loan->disbursed_by))
                                                {{trans_choice('core::general.by',1)}}
                                                {{$application->$loan->disbursed_by->first_name}} {{$application->$loan->disbursed_by->last_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    @foreach($custom_fields as $custom_field)
                                        <?php
                                        $field = custom_field_build_form_field($custom_field, $application->$loan->id);
                                        ?>
                                        <tr>
                                            <td>{{$field['label']}}</td>
                                            <td>
                                                @if($custom_field->type=='checkbox')
                                                    @foreach(explode(',',$field['current'] ) as $key)
                                                        {{$key}}<br>
                                                    @endforeach
                                                @else
                                                    {{$field['current'] }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($application->$loan->status=='active' ||$application->$loan->status=='closed'||$application->$loan->status=='written_off'||$application->$loan->status=='overpaid'||$application->$loan->status=='rescheduled')
                                <div class="tab-pane" id="repayment_schedule">
                                    <div class="m-4">
                                        <div class="btn-group">
                                            <button href="#" class="btn btn-info dropdown-toggle"
                                                    data-toggle="dropdown">
                                                {{trans_choice('core::general.action',1)}}
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-left">
                                                <a href="{{url('loan/'.$application->$loan->id.'/schedule/email')}}"
                                                   class="dropdown-item confirm">
                                                    <i class="fas fa-envelope"></i>
                                                    {{trans_choice('core::general.email',1)}} {{trans_choice('loan::general.schedule',1)}}
                                                </a>

                                                <a href="{{url('loan/'.$application->$loan->id.'/schedule/print')}}"
                                                   class="dropdown-item"
                                                   target="_blank">
                                                    <i class="fas fa-print"></i>
                                                    {{trans_choice('core::general.print',1)}} {{trans_choice('loan::general.schedule',1)}}
                                                </a>

                                                <a href="{{url('loan/'.$application->$loan->id.'/schedule/pdf')}}"
                                                   class="dropdown-item"
                                                   target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                    {{trans_choice('core::general.download',1)}} {{trans_choice('core::general.pdf',1)}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="pretty displayschedule" id="repaymentschedule"
                                           style="margin-top: 20px;">
                                        <colgroup span="3"></colgroup>
                                        <colgroup span="3">
                                            <col class="lefthighlightcol">
                                            <col>
                                            <col class="righthighlightcol">
                                        </colgroup>
                                        <colgroup span="3">
                                            <col class="lefthighlightcol">
                                            <col>
                                            <col class="righthighlightcol">
                                        </colgroup>
                                        <colgroup span="3"></colgroup>
                                        <thead>
                                        <tr>
                                            <th class="empty" scope="colgroup" colspan="5">&nbsp;</th>
                                            <th class="highlightcol" scope="colgroup"
                                                colspan="3">{{trans_choice('loan::general.loan_amount_and_balance',1)}}</th>
                                            <th class="highlightcol" scope="colgroup"
                                                colspan="3">{{trans_choice('loan::general.total_cost_of_loan',1)}}</th>
                                            <th class="empty" scope="colgroup" colspan="1">&nbsp;</th>
                                        </tr>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">{{trans_choice('core::general.date',1)}}</th>
                                            <th scope="col"># {{trans_choice('loan::general.day',2)}}</th>
                                            <th scope="col">{{trans_choice('loan::general.paid',1)}} {{trans_choice('core::general.by',1)}}</th>
                                            <th scope="col"></th>
                                            <th class="lefthighlightcolheader"
                                                scope="col">{{trans_choice('loan::general.disbursement',1)}}</th>
                                            <th scope="col">{{trans_choice('loan::general.principal',1)}} {{trans_choice('loan::general.due',1)}}</th>
                                            <th class="righthighlightcolheader"
                                                scope="col">{{trans_choice('loan::general.principal',1)}} {{trans_choice('loan::general.balance',1)}}</th>

                                            <th class="lefthighlightcolheader"
                                                scope="col">{{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.due',1)}}</th>
                                            <th scope="col">{{trans_choice('loan::general.fee',2)}}</th>
                                            <th class="righthighlightcolheader"
                                                scope="col">{{trans_choice('loan::general.penalty',2)}}

                                            </th>
                                            <th scope="col">{{trans_choice('loan::general.total',1)}} {{trans_choice('loan::general.due',1)}}</th>
                                            <th scope="col">{{trans_choice('loan::general.total',1)}} {{trans_choice('loan::general.paid',1)}}</th>
                                            <th scope="col">{{trans_choice('loan::general.total',1)}} {{trans_choice('loan::general.outstanding',1)}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <tr>
                                            <td scope="row"></td>
                                            <td>{{$application->$loan->disbursed_on_date}}</td>
                                            <td></td>
                                            <td><span style="color: #eb2442;"></span></td>
                                            <td>&nbsp;</td>
                                            <td class="lefthighlightcolheader">{{number_format($application->$loan->principal,$application->$loan->decimals)}}</td>
                                            <td></td>
                                            <td class="righthighlightcolheader">{{number_format($application->$loan->principal,$application->$loan->decimals)}}</td>
                                            <td class="lefthighlightcolheader"></td>
                                            <td>{{number_format($application->$loan->disbursement_charges,$application->$loan->decimals)}}</td>
                                            <td class="righthighlightcolheader"></td>
                                            <td>{{number_format($application->$loan->disbursement_charges,$application->$loan->decimals)}}</td>
                                            <td>{{number_format($application->$loan->disbursement_charges,$application->$loan->decimals)}}</td>
                                            <td></td>
                                        </tr>
                                        <?php
                                        $count = 1;
                                        $total_days = 0;
                                        $total_principal = 0;
                                        $total_interest = 0;
                                        $total_fees = 0 + $application->$loan->disbursement_charges;
                                        $total_penalties = 0;
                                        $total_due = 0;
                                        $total_paid = 0 + $application->$loan->disbursement_charges;
                                        $total_outstanding = 0;
                                        $balance = $application->$loan->principal
                                        ?>
                                        @foreach($application->$loan->repayment_schedules as $key)
                                            <?php
                                            $days = \Carbon\Carbon::parse($key->due_date)->diffInDays(\Illuminate\Support\Carbon::parse($key->from_date));
                                            $total_days = $total_days + $days;
                                            $balance = $balance - $key->principal;
                                            $principal = $key->principal - $key->principal_waived_derived - $key->principal_written_off_derived;
                                            $interest = $key->interest - $key->interest_waived_derived - $key->interest_written_off_derived;
                                            $fees = $key->fees - $key->fees_waived_derived - $key->fees_written_off_derived;
                                            $penalties = $key->penalties - $key->penalties_waived_derived - $key->penalties_written_off_derived;
                                            $due = $principal + $interest + $fees + $penalties;
                                            $paid = $key->principal_repaid_derived + $key->interest_repaid_derived + $key->fees_repaid_derived + $key->penalties_repaid_derived;
                                            $outstanding = $due - $paid;
                                            $total_principal = $total_principal + $principal;
                                            $total_interest = $total_interest + $interest;
                                            $total_fees = $total_fees + $fees;
                                            $total_penalties = $total_penalties + $penalties;
                                            $total_due = $total_due + $due;
                                            $total_paid = $total_paid + $paid;
                                            $total_outstanding = $total_outstanding + $outstanding;

                                            ?>
                                            <tr>
                                                <td scope="row">{{$count}}</td>
                                                <td>{{$key->due_date}}</td>
                                                <td>{{$days}}</td>
                                                <td>
                                                    @if($outstanding<=0)
                                                        <span style="@if(\Illuminate\Support\Carbon::parse($key->paid_by_date)->greaterThan(\Illuminate\Support\Carbon::parse($key->due_date)))color: #eb2442; @endif">{{$key->paid_by_date}}</span>
                                                    @elseif($outstanding>0 && \Illuminate\Support\Carbon::now()->greaterThan(\Illuminate\Support\Carbon::parse($key->due_date)))
                                                        <span style="color: #eb2442;">{{trans_choice('loan::general.overdue',1)}}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($outstanding<=0)
                                                        @if(\Illuminate\Support\Carbon::parse($key->paid_by_date)->greaterThan(\Illuminate\Support\Carbon::parse($key->due_date)))
                                                            <i class="fa fa-question-circle"></i>
                                                        @else
                                                            <i class="fa fa-check-circle"></i>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="lefthighlightcolheader"></td>
                                                <td>{{number_format($principal,$application->$loan->decimals)}}</td>
                                                <td class="righthighlightcolheader">{{number_format($balance,$application->$loan->decimals)}}</td>
                                                <td class="lefthighlightcolheader">
                                                    {{number_format($interest,$application->$loan->decimals)}}
                                                </td>
                                                <td>{{number_format($fees,$application->$loan->decimals)}}</td>
                                                <td class="righthighlightcolheader">{{number_format($penalties,$application->$loan->decimals)}}</td>
                                                <td>{{number_format($due,$application->$loan->decimals)}}</td>
                                                <td>{{number_format($paid,$application->$loan->decimals)}}</td>
                                                <td>{{number_format($outstanding,$application->$loan->decimals)}}</td>
                                            </tr>
                                            <?php
                                            $count++;
                                            ?>
                                        @endforeach
                                        </tbody>
                                        <tfoot class="ui-widget-header">
                                        <tr>
                                            <th colspan="2">{{trans_choice('loan::general.total',1)}}</th>
                                            <th>{{$total_days}}</th>
                                            <th></th>
                                            <th></th>
                                            <th class="lefthighlightcolheader">{{number_format($application->$loan->principal,$application->$loan->decimals)}}</th>
                                            <th>{{number_format($total_principal,$application->$loan->decimals)}}</th>
                                            <th class="righthighlightcolheader">&nbsp;</th>
                                            <th class="lefthighlightcolheader">{{number_format($total_interest,$application->$loan->decimals)}}</th>
                                            <th>{{number_format($total_fees,$application->$loan->decimals)}}</th>
                                            <th class="righthighlightcolheader">{{number_format($total_penalties,$application->$loan->decimals)}}</th>
                                            <th>{{number_format($total_due,$application->$loan->decimals)}}</th>
                                            <th>{{number_format($total_paid,$application->$loan->decimals)}}</th>
                                            <th>{{number_format($total_outstanding,$application->$loan->decimals)}}</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                @can('loan.loans.transactions.index')
                                    <div class="tab-pane" id="loan_transactions">
                                        <table class="table table-striped table-hover" id="loan_transactions_table">
                                            <thead>
                                            <tr>
                                                <th>{{trans_choice('core::general.date',1)}}</th>
                                                <th>{{trans_choice('core::general.submitted_on',1)}}</th>
                                                <th>{{trans_choice('loan::general.transaction',1)}} {{trans_choice('core::general.type',1)}}</th>
                                                <th>{{trans_choice('loan::general.transaction',1)}} {{trans_choice('core::general.id',1)}}</th>
                                                <th>{{trans_choice('accounting::general.debit',1)}}</th>
                                                <th>{{trans_choice('accounting::general.credit',1)}}</th>
                                                <th>{{trans_choice('loan::general.balance',1)}}</th>
                                                <th>{{trans_choice('core::general.action',1)}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $balance = $application->$loan->principal;
                                            ?>
                                            @foreach($application->$loan->transactions as $key)
                                                <?php
                                                if ($key->loan_transaction_type_id == 10 || $key->loan_transaction_type_id == 11) {
                                                    $balance = $balance + $key->amount;
                                                }
                                                if ($key->loan_transaction_type_id == 2 || $key->loan_transaction_type_id == 4 || $key->loan_transaction_type_id == 8 || $key->loan_transaction_type_id == 9 || $key->loan_transaction_type_id == 6) {
                                                    $balance = $balance - $key->amount;
                                                }
                                                ?>
                                                <tr>
                                                    <td>{{$key->created_on}}</td>
                                                    <td>{{$key->submitted_on}}</td>
                                                    <td>
                                                        @if($key->loan_transaction_type_id == 1)
                                                            {{trans_choice('loan::general.disbursement',1)}}
                                                        @endif
                                                        @if($key->loan_transaction_type_id == 2)
                                                            {{trans_choice('loan::general.repayment',1)}}
                                                        @endif
                                                        @if($key->loan_transaction_type_id == 3)
                                                            {{trans_choice('loan::general.contra',1)}}
                                                        @endif
                                                        @if($key->loan_transaction_type_id == 4)
                                                            {{trans_choice('loan::general.waive',1)}} {{trans_choice('loan::general.interest',1)}}
                                                        @endif
                                                        @if($key->loan_transaction_type_id == 5)
                                                            {{trans_choice('loan::general.repayment',1)}} {{trans_choice('core::general.at',1)}} {{trans_choice('loan::general.disbursement',1)}}
                                                        @endif
                                                        @if($key->loan_transaction_type_id == 6)
                                                            {{trans_choice('loan::general.write_off',1)}}
                                                        @endif
                                                        @if($key->loan_transaction_type_id == 7)
                                                            {{trans_choice('loan::general.marked_for_rescheduling',1)}}
                                                        @endif
                                                        @if($key->loan_transaction_type_id == 8)
                                                            {{trans_choice('loan::general.recovery',1)}} {{trans_choice('loan::general.repayment',1)}}
                                                        @endif
                                                        @if($key->loan_transaction_type_id == 9)
                                                            {{trans_choice('loan::general.waive',1)}} {{trans_choice('loan::general.charge',2)}}
                                                        @endif
                                                        @if($key->loan_transaction_type_id == 10)
                                                            {{trans_choice('loan::general.fee',1)}} {{trans_choice('loan::general.applied',1)}}
                                                        @endif
                                                        @if($key->loan_transaction_type_id == 11)
                                                            {{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.applied',1)}}
                                                        @endif
                                                    </td>
                                                    <td>{{$key->id}}</td>
                                                    <td>{{number_format($key->debit,$application->$loan->decimals)}}</td>
                                                    <td>{{number_format($key->credit,$application->$loan->decimals)}}</td>
                                                    <td>{{number_format($balance,$application->$loan->decimals)}}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button href="#" class="btn btn-default dropdown-toggle"
                                                                    data-toggle="dropdown">
                                                                <i class="ri-settings-3-line"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a href="{{url('loan/transaction/' . $key->id . '/show') }}"
                                                                   class="dropdown-item"><i
                                                                            class="ri-eye-fill"></i> {{ trans_choice('core::general.view', 2) }}
                                                                </a>
                                                                @if($key->loan_transaction_type_id == 2 && $key->reversed==0)
                                                                    <a href="{{url('loan/transaction/' . $key->id . '/pdf') }}"
                                                                       target="_blank" class="dropdown-item"><i
                                                                                class="fas fa-file-pdf"></i> {{ trans_choice('core::general.receipt', 1) }}
                                                                    </a>
                                                                    <a href="{{url('loan/transaction/' . $key->id . '/print') }}"
                                                                       target="_blank" class="dropdown-item"><i
                                                                                class="fas fa-print"></i> {{ trans_choice('core::general.print', 1) }}
                                                                    </a>
                                                                    @can('loan.loans.transactions.edit')
                                                                        <a href="{{url('loan/repayment/' . $key->id . '/edit') }}"
                                                                           class="dropdown-item">
                                                                           <i class="ri-edit-fill"></i> {{ trans_choice('core::general.edit', 1) }}
                                                                        </a>

                                                                    @endcan
                                                                    @can('loan.loans.transactions.edit')

                                                                        <a href="{{url('loan/repayment/' . $key->id . '/reverse') }}"
                                                                           class="dropdown-item confirm"><i
                                                                                    class="fas fa-undo"></i> {{ trans_choice('loan::general.reverse', 1) }}
                                                                        </a>

                                                                    @endcan
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endcan
                            @endif
                            @can('loan.loans.charges.index')
                                <div class="tab-pane" id="loan_charges">
                                    @can('loan.loans.charges.create')
                                        <a href="{{url('loan/'.$application->$loan->id.'/charge/create')}}"
                                           class="btn btn-info float-right m-2">{{trans_choice('core::general.add',1)}} {{trans_choice('loan::general.charge',1)}}</a>
                                    @endcan
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('core::general.name',1) }}</th>
                                            <th>{{ trans_choice('loan::general.charge',1) }} {{ trans_choice('core::general.type',1) }}</th>
                                            <th>{{ trans_choice('core::general.amount',1) }}</th>
                                            <th>{{ trans_choice('loan::general.collected_on',1) }}</th>
                                            <th>{{ trans_choice('core::general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($application->$loan->charges as $key)
                                            <tr>
                                                <td>{{$key->name}}</td>
                                                <td>
                                                    @if($key->loan_charge_option_id==1)
                                                        {{number_format($key->amount,2)}} {{ trans_choice('loan::general.flat',1) }}
                                                    @endif
                                                    @if($key->loan_charge_option_id==2)
                                                        {{number_format($key->amount,2)}}
                                                        % {{ trans_choice('loan::general.principal_due_on_installment',1) }}
                                                    @endif
                                                    @if($key->loan_charge_option_id==3)
                                                        {{number_format($key->amount,2)}}
                                                        %  {{ trans_choice('loan::general.principal_interest_due_on_installment',1) }}
                                                    @endif
                                                    @if($key->loan_charge_option_id==4)
                                                        {{number_format($key->amount,2)}}
                                                        % {{ trans_choice('loan::general.interest_due_on_installment',1) }}
                                                    @endif
                                                    @if($key->loan_charge_option_id==5)
                                                        {{number_format($key->amount,2)}}
                                                        %  {{ trans_choice('loan::general.total_outstanding_loan_principal',1) }}
                                                    @endif
                                                    @if($key->loan_charge_option_id==6)
                                                        {{number_format($key->amount,2)}}
                                                        % {{ trans_choice('loan::general.percentage_of_original_loan_principal_per_installment',1) }}
                                                    @endif
                                                    @if($key->loan_charge_option_id==7)
                                                        {{number_format($key->amount,2)}}
                                                        % {{ trans_choice('loan::general.original_loan_principal',1) }}
                                                    @endif
                                                </td>
                                                <td>{{number_format($key->calculated_amount,$application->$loan->decimals)}}</td>
                                                <td>
                                                    @if($key->loan_charge_type_id==1)
                                                        {{ trans_choice('loan::general.disbursement',1) }}
                                                    @endif
                                                    @if($key->loan_charge_type_id==2)
                                                        {{ trans_choice('loan::general.specified_due_date',1) }}
                                                    @endif
                                                    @if($key->loan_charge_type_id==3)
                                                        {{ trans_choice('loan::general.installment',1) }} {{ trans_choice('loan::general.fee',2) }}
                                                    @endif
                                                    @if($key->loan_charge_type_id==4)
                                                        {{ trans_choice('loan::general.overdue',1) }} {{ trans_choice('loan::general.installment',1) }} {{ trans_choice('loan::general.fee',1) }}
                                                    @endif
                                                    @if($key->loan_charge_type_id==5)
                                                        {{ trans_choice('loan::general.disbursement_paid_with_repayment',1) }}
                                                    @endif
                                                    @if($key->loan_charge_type_id==6)
                                                        {{ trans_choice('loan::general.loan_rescheduling_fee',1) }}
                                                    @endif
                                                    @if($key->loan_charge_type_id==7)
                                                        {{ trans_choice('loan::general.overdue_on_loan_maturity',1) }}
                                                    @endif
                                                    @if($key->loan_charge_type_id==8)
                                                        {{ trans_choice('loan::general.last_installment_fee',1) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($key->loan_charge_type_id==1 ||$key->loan_charge_type_id==5)
                                                        {{ trans_choice('loan::general.charge',1) }} {{ trans_choice('loan::general.paid',1) }}
                                                    @else
                                                        @if($key->waived==1)
                                                            {{ trans_choice('loan::general.charge',1) }} {{ trans_choice('loan::general.waived',1) }}
                                                        @else
                                                            @can('loan.loans.transactions.edit')
                                                                <a href="{{url('loan/charge/'.$key->id.'/waive')}}"
                                                                   class="btn btn-danger confirm">
                                                                    {{ trans_choice('loan::general.waive',1) }} {{ trans_choice('loan::general.charge',1) }}</a>
                                                            @endcan
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endcan
                            @can('loan.loans.files.index')
                                <div class="tab-pane" id="loan_files">
                                    @can('loan.loans.files.create')
                                        <a href="{{url('loan/'.$application->$loan->id.'/file/create')}}"
                                           class="btn btn-info float-right m-2">{{trans_choice('core::general.add',1)}} {{trans_choice('loan::general.file',1)}}</a>
                                    @endcan
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('core::general.name',1) }}</th>
                                            <th>{{ trans_choice('core::general.description',1) }}</th>
                                            <th>{{ trans_choice('core::general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($application->$loan->files as $key)
                                            <tr>
                                                <td>{{$key->name}}</td>
                                                <td>{{$key->description}}</td>
                                                <td>
                                                    <a href="{{asset('storage/uploads/loans/'.$key->link)}}"
                                                       target="_blank"><i class="ri-download-line"></i> </a>
                                                    @can('loan.loans.files.edit')
                                                        <a href="{{url('loan/file/'.$key->id.'/edit')}}"><i
                                                                    class="ri-edit-fill"></i> </a>
                                                    @endcan
                                                    @can('loan.loans.files.destroy')
                                                        <a href="{{url('loan/file/'.$key->id.'/destroy')}}"
                                                           class="confirm"><i class="ri-delete-bin-fill"></i> </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endcan
                            @can('loan.loans.collateral.index')
                                <div class="tab-pane" id="loan_collateral">
                                    @can('loan.loans.collateral.create')
                                        <a href="{{url('loan/'.$application->$loan->id.'/collateral/create')}}"
                                           class="btn btn-info float-right m-2">{{trans_choice('core::general.add',1)}} {{trans_choice('loan::general.collateral',1)}}</a>
                                    @endcan
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('loan::general.type',1) }}</th>
                                            <th>{{ trans_choice('loan::general.value',1) }}</th>
                                            <th>{{ trans_choice('core::general.description',1) }}</th>
                                            <th>{{ trans_choice('core::general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($application->$loan->collateral as $key)
                                            <tr>
                                                <td>
                                                    @if(!empty($key->collateral_type))
                                                        {{$key->collateral_type->name}}
                                                    @endif
                                                </td>
                                                <td>{{number_format($key->value,$application->$loan->decimals)}}</td>
                                                <td>{{$key->description}}</td>
                                                <td>
                                                    <a href="{{asset('storage/uploads/loans/'.$key->link)}}"
                                                       target="_blank"><i class="ri-download-line"></i> </a>
                                                    @can('loan.loans.collateral.edit')
                                                        <a href="{{url('loan/collateral/'.$key->id.'/edit')}}"><i
                                                                    class="ri-edit-fill"></i> </a>
                                                    @endcan
                                                    @can('loan.loans.collateral.destroy')
                                                        <a href="{{url('loan/collateral/'.$key->id.'/destroy')}}"
                                                           class="confirm"><em class="ri-delete-bin-fill"></em> </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endcan
                            @can('loan.loans.guarantors.index')
                                <div class="tab-pane" id="loan_guarantors">
                                    @can('loan.loans.guarantors.create')
                                        <a href="{{url('loan/'.$application->$loan->id.'/guarantor/create')}}"
                                           class="btn btn-info float-right m-2">{{trans_choice('core::general.add',1)}} {{trans_choice('loan::general.guarantor',1)}}</a>
                                    @endcan
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('core::general.name',1) }}</th>
                                            <th>{{ trans_choice('core::general.amount',1) }}</th>
                                            <th>{{ trans_choice('core::general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($application->$loan->guarantors as $key)
                                            <tr>
                                                <td>
                                                    @if($key->is_client==1)
                                                        @if(!empty($key->client))
                                                            <a href="{{url('client/'.$key->client_id.'/show')}}">
                                                                {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                                                            </a>
                                                        @endif
                                                    @else
                                                        {{$key->first_name}} {{$key->middle_name}} {{$key->last_name}}
                                                    @endif
                                                </td>
                                                <td>{{number_format($key->guaranteed_amount,$application->$loan->decimals)}}</td>
                                                <td>
                                                    @if($key->is_client==1)
                                                        <a href="{{url('client/'.$key->client_id.'/show')}}"><i
                                                                    class="ri-eye-fill"></i> </a>
                                                    @else
                                                        <a href="{{url('loan/guarantor/'.$key->id.'/show')}}"><i
                                                                    class="ri-eye-fill"></i> </a>
                                                    @endif
                                                    @can('loan.loans.guarantors.edit')
                                                        <a href="{{url('loan/guarantor/'.$key->id.'/edit')}}"><i
                                                                    class="ri-edit-fill"></i> </a>
                                                    @endcan
                                                    @can('loan.loans.guarantors.destroy')
                                                        <a href="{{url('loan/guarantor/'.$key->id.'/destroy')}}"
                                                           class="confirm"><i class="ri-edit-fill"></i> </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endcan
                            @can('loan.loans.notes.index')
                                <div class="tab-pane" id="loan_notes">
                                    @can('loan.loans.notes.create')
                                        <a href="{{url('loan/'.$application->$loan->id.'/note/create')}}"
                                           class="btn btn-info float-right m-2">{{trans_choice('core::general.add',1)}} {{trans_choice('core::general.note',1)}}</a>
                                    @endcan

                                    <div class="comments-list clearfix">
                                        @foreach($application->$loan->notes as $key)
                                            <div class="media pt-2">
                                                <div class="media-body">
                                                    <h4 class="media-heading user_name">
                                                        @if(!empty($key->created_by))
                                                            <a>{{$key->created_by->first_name}} {{$key->created_by->last_name}}</a>
                                                        @endif
                                                        <small>{{trans_choice('core::general.on',1)}} {{$key->created_at}}</small>
                                                    </h4>
                                                    <p>{{$key->description}}</p>
                                                    <p>
                                                        @can('loan.loans.notes.edit')
                                                            <a href="{{url('loan/note/'.$key->id.'/edit')}}"
                                                               class="btn btn-xs btn-tool"><i
                                                                        class="ri-edit-fill"></i> </a>
                                                        @endcan
                                                        @can('loan.loans.notes.destroy')
                                                            <a href="{{url('loan/note/'.$key->id.'/destroy')}}"
                                                               class="btn btn-xs btn-tool link-danger confirm"><i
                                                                        class="ri-delete-bin-fill"></i> </a>
                                                        @endcan
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            @endcan
                        </div>
                        <!-- /.tab-content -->
                    </div>
                </div>
            </div>
        </div>
        @endif
    </section>
@endsection