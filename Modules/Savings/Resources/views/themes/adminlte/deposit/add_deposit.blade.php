@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('savings::general.deposit',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('savings::general.deposit',1) }}
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
                        <li class="breadcrumb-item"><a
                                    href="{{url('savings/show')}}">{{ trans_choice('savings::general.savings',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('savings::general.deposit',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{url('savings/deposit/store_deposit2')}}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client_id"
                                    class="control-label">Select Client</label><br>
                                <select class="form-control select2"  name="client_id"
                                        id="client_id" required>
                                    <option value="" selected>Please Select</option>
                                    @foreach($clients as $key)
                                        <option value="{{$key->id}}">{{$key->first_name . " " .$key->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="saving_id"
                                    class="control-label">Select Savings</label><br>
                                <select class="form-control select2"  name="saving_id"
                                        id="saving_id" required>
                                    <option value="" selected>Please Select</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="savings_detail">
                        <div class="col-sm-6">
                            <div class="box box-solid">
                                <div class="box-body">
                                    <h6><b>Savings Summery</b></h6>
                                    <table class="table table-striped">
                                        <tr style="background-color:cornsilk">
                                            <th>Account Number:</th>
                                            <td align="right">
                                            <span class="account">
                                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                                            </span>
                                            </td>
                                        </tr>
                                        <tr style="background-color:cornsilk">
                                            <th>Balance:</th>
                                            <td align="right">
                                            <span class="balance">
                                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                                            </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="box box-solid">
                                <div class="box-body">
                                </div>
                            </div>
                        </div>
                     </div>
                    
                

                    <div class="row">
                        <div class="form-group col-md-6 col-sm-6">
                            <label class="control-label"
                                for="amount">{{trans_choice('savings::general.amount',1)}}</label>
                            <input type="text" name="amount" id="amount"
                                class="form-control numeric @error('amount') is-invalid @enderror" required>
                            @error('amount')
                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6 col-sm-6">
                            <label class="control-label"
                                for="date"> {{trans_choice('core::general.date',1)}}</label>
                            <flat-pickr
                                    v-model="date"
                                    class="form-control  @error('date') is-invalid @enderror"
                                    name="date" id="date" required>
                            </flat-pickr>
                            @error('date')
                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                            @enderror
                        </div>
                    </div>
                    <div id="payment_details">
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-6">
                                <label class="control-label"
                                    for="payment_type_id">{{trans_choice('core::general.payment',1)}} {{trans_choice('core::general.type',1)}}</label>
                                <select class="form-control select2"  name="payment_type_id"
                                        id="payment_type_id" required>
                                    <option value="" selected>Please Select</option>
                                    @foreach($payment_types as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6 col-sm-6">
                                <label class="control-label"
                                    for="account_number">{{trans_choice('core::general.account',1)}}#</label>
                                <input type="text" name="account_number" id="account_number"
                                    class="form-control @error('account_number') is-invalid @enderror">
                                @error('account_number')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-6">
                                <label class="control-label"
                                    for="cheque_number">{{trans_choice('core::general.cheque',1)}}#</label>
                                <input type="text" name="cheque_number" id="cheque_number"
                                    class="form-control @error('cheque_number') is-invalid @enderror">
                                @error('cheque_number')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6 col-sm-6">
                                <label class="control-label"
                                    for="routing_code">{{trans_choice('core::general.routing_code',1)}}</label>
                                <input type="text" name="routing_code" id="routing_code"
                                    class="form-control  @error('routing_code') is-invalid @enderror">
                                @error('routing_code')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-6">
                                <label class="control-label"
                                    for="receipt">{{trans_choice('core::general.receipt',1)}}#</label>
                                <input type="text" name="receipt" id="receipt"
                                    class="form-control  @error('receipt') is-invalid @enderror">
                                @error('receipt')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6 col-sm-6">
                                <label class="control-label"
                                    for="bank_name">{{trans_choice('core::general.bank',1)}}#</label>
                                <input type="text" name="bank_name" id="bank_name"
                                    class="form-control @error('bank_name') is-invalid @enderror">
                                @error('bank_name')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="description">{{trans_choice('general.description',1)}}</label>
                        <textarea type="text" name="description"
                                  id="description"
                                  class="form-control @error('description') is-invalid @enderror">
                                </textarea>
                        @error('description')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer border-top ">
                    <button type="submit"
                            class="btn btn-primary  float-right">{{trans_choice('core::general.save',1)}}</button>
                </div>
            </div>
        </form>
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                amount: "",
                date: "{{old('date',date('Y-m-d'))}}",
                //payment_type_id: parseInt("{{old('payment_type_id')}}"),
                payment_type_id: "",
                account_number: "",
                cheque_number: "",
                routing_code: "",
                receipt: "",
                bank_name: "",
                description: ``,
                //payment_types: {!! json_encode($payment_types) !!},
                client_id: "",
                saving_id: "",

            },
            created: function () {

            },
            methods: {
                onSubmit() {

                }
            }
        });
       

        $(document).ready(function () {
            $("#client_id").select2({
                width: 'resolve',
                closeOnSelect: true//,
                //theme: "classic"
            });

            $("#saving_id").select2({
                width: 'resolve',
                closeOnSelect: true//,
                //theme: "classic"
            });

            $("#payment_type_id").select2({
                width: 'resolve',
                closeOnSelect: true//,
                //theme: "classic"
            });

           

            $("#client_id").change(function () {
                getSavingsList();
            });

            $("#saving_id").change(function () {
                getSavingsDetail();
            });
            $('#savings_detail').hide();

            function getSavingsList(){
                $('#savings_detail').hide();
                var client_id = $('#client_id').val();
                $.ajax({     // get states/towns from db from controller
                        cache: false,
                        type: "GET",
                        url: "/savings/get_savings_list",
                        dataType: "json",
                        data: { client_id: client_id },
                        success: function (data) {
                            $('#saving_id').html('');
                            $('#saving_id').append(
                                $('<option></option>').val("").html("Please Select")
                            );
                            $.each(data, function (index, item) {
                                $('#saving_id').append(
                                    $('<option></option>').val(item.id).html(item.account_number)
                                );
                            });
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert('Failed to retrieve states.');
                            //statesProgress.hide();
                        }
                    });
                }

            function getSavingsDetail(){
                var saving_id = $('#saving_id').val();
                var data = { saving_id: saving_id};
                if(saving_id == ""){
                    $('#savings_detail').hide();
                    return;
                }
                $('#savings_detail').show();
                var loader = '<i class="fa fa-refresh fa-spin fa-fw"></i>';
                $('.account').html(loader);
                $('.balance').html(loader);

                $.ajax({
                    method: "GET",
                    url: '/savings/get_savings_detail',
                    dataType: "json",
                    data: data,
                    success: function(data){
                        $('.account').html(data.account_number);
                        $('.balance').html(data.balance_derived);
                    }
                });
            }
        

            $(' #start_date, #end_date, #client_id, #branch_id').change( function(){
        		
    		});
        })
    </script>
@endsection
