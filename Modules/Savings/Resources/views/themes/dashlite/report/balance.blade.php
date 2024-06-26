@extends('core::layouts.master')
@section('title')
    {{trans_choice('savings::general.savings',2)}} {{trans_choice('savings::general.balance',2)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">
                {{trans_choice('savings::general.savings',2)}} {{trans_choice('savings::general.balance',2)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h6>

            <div class="box-tools pull-right hidden-print">
                <div class="input-group ">
                    <button type="button" class="btn btn-info btn-sm  dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="true"> {{trans_choice('core::general.action',2)}}
                        <span class="fa fa-caret-down"></span></button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{url('report/savings/balance?download=1&type=csv&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&savings_officer_id='.$savings_officer_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.csv_format',1)}}</a>
                        </li>
                        <li>
                            <a href="{{url('report/savings/balance?download=1&type=excel&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&savings_officer_id='.$savings_officer_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_format',1)}}</a>
                        </li>
                        <li>
                            <a href="{{url('report/savings/balance?download=1&type=excel_2007&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&savings_officer_id='.$savings_officer_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_2007_format',1)}}</a>
                        </li>
                        <li>
                            <a href="{{url('report/savings/balance?download=1&type=pdf&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&savings_officer_id='.$savings_officer_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.pdf_format',1)}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="box-body">
            <form method="get" action="{{Request::url()}}" class="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="branch_id">{{trans_choice('core::general.branch',1)}}/ Team</label>
                            <select class="form-control select2" name="branch_id" id="branch_id">
                                <option value="" disabled selected>{{trans_choice('core::general.select',1)}}</option>
                                @foreach($branches as $key)
                                    <option value="{{$key->id}}"
                                            @if($branch_id==$key->id) selected @endif>{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="start_date">{{trans_choice('core::general.start_date',1)}}</label>
                            <input type="text" name="start_date" class="form-control date-picker"
                                   placeholder=""
                                   value="{{$start_date}}" id="start_date" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="end_date">{{trans_choice('core::general.end_date',1)}}</label>
                            <input type="text" name="end_date" class="form-control date-picker"
                                   placeholder=""
                                   value="{{$end_date}}" id="end_date" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="savings_officer_id">{{trans_choice('savings::general.savings',1)}} {{trans_choice('savings::general.officer',1)}}</label>
                            <select class="form-control select2" name="savings_officer_id" id="savings_officer_id">
                                <option value="" disabled selected>{{trans_choice('core::general.select',1)}}</option>
                                @foreach($users as $key)
                                    <option value="{{$key->id}}"
                                            @if($savings_officer_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="savings_product_id">{{trans_choice('savings::general.savings',1)}} {{trans_choice('savings::general.product',1)}}</label>
                            <select class="form-control select2" name="savings_product_id" id="savings_product_id">
                                <option value="" disabled selected>{{trans_choice('core::general.select',1)}}</option>
                                @foreach($savings_products as $key)
                                    <option value="{{$key->id}}"
                                            @if($savings_product_id==$key->id) selected @endif>{{$key->name}} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <span class="input-group-btn">
                          <button type="submit" class="btn bg-olive btn-flat">{{trans_choice('core::general.filter',1)}}
                          </button>
                        </span>
                        <span class="input-group-btn">
                          <a href="{{Request::url()}}"
                             class="btn bg-purple  btn-flat pull-right">{{trans_choice('general.reset',1)}}!</a>
                        </span>
                    </div>
                </div>
            </form>

        </div>
        <!-- /.box-body -->

    </div>
    <!-- /.box -->
    @if(!empty($start_date))
        <div class="box box-white">
            <div class="box-body table-responsive no-padding">
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                    <tr>
                        <th colspan="4">

                            <h4>  {{trans_choice('core::general.by',1)}}  {{trans_choice('core::general.branch',1)}}/ Team</h4>

                        </th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            @if(!empty($data->first()) && !empty($branch_id))
                                {{trans_choice('core::general.branch',1)}}/ Team:

                                {{$data->first()->branch}}
                            @endif
                        </th>
                        <th colspan="2">
                            @if(!empty($data->first()) && !empty($savings_product_id))
                                {{trans_choice('savings::general.product',1)}}:

                                {{$data->first()->savings_product}}
                            @endif
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            @if(!empty($data->first()) && !empty($savings_officer_id))
                                {{trans_choice('savings::general.officer',1)}}:

                                {{$data->first()->savings_officer}}
                            @endif
                        </th>
                        <th colspan="1">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
                        <th colspan="1">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
                    </tr>
                    <tr style="background-color: #D1F9FF">
                        <th>{{trans_choice('core::general.branch',1)}}</th>
                        <th>{{trans_choice('savings::general.deposit',2)}}</th>
                        <th>{{trans_choice('savings::general.withdrawal',2)}}</th>
                        <th>{{trans_choice('savings::general.total',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $credit_total = 0;
                    $debit_total = 0;
                    ?>
                    @foreach($data->groupBy('branch') as $key=>$value)
                        <?php
                        $credit_total = $credit_total + $value->sum('credit');
                        $debit_total = $debit_total + $value->sum('debit');
                        ?>
                        <tr>
                            <td>{{ $key }}</td>
                            <td>
                                {{ number_format(  $value->sum('credit'),2) }}
                            </td>
                            <td>
                                {{ number_format($value->sum('debit'),2) }}
                            </td>

                            <td>{{ number_format(  $value->sum('credit')+$value->sum('debit'),2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="1"><b>{{trans_choice('core::general.total',1)}}</b></td>

                        <td>{{number_format($credit_total,2)}}</td>
                        <td>{{number_format($debit_total,2)}}</td>
                        <td>{{number_format($debit_total+$credit_total,2)}}</td>
                    </tr>
                    </tfoot>
                </table>
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                    <tr>
                        <th colspan="4">

                            <h4>  {{trans_choice('core::general.by',1)}}  {{trans_choice('savings::general.officer',1)}}</h4>

                        </th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            @if(!empty($data->first()) && !empty($branch_id))
                                {{trans_choice('core::general.branch',1)}}/ Team:

                                {{$data->first()->branch}}
                            @endif
                        </th>
                        <th colspan="2">
                            @if(!empty($data->first()) && !empty($savings_product_id))
                                {{trans_choice('savings::general.product',1)}}:

                                {{$data->first()->savings_product}}
                            @endif
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            @if(!empty($data->first()) && !empty($savings_officer_id))
                                {{trans_choice('savings::general.officer',1)}}:

                                {{$data->first()->savings_officer}}
                            @endif
                        </th>
                        <th colspan="1">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
                        <th colspan="1">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
                    </tr>
                    <tr style="background-color: #D1F9FF">
                        <th>{{trans_choice('savings::general.officer',1)}}</th>
                        <th>{{trans_choice('savings::general.deposit',2)}}</th>
                        <th>{{trans_choice('savings::general.withdrawal',2)}}</th>
                        <th>{{trans_choice('savings::general.total',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $credit_total = 0;
                    $debit_total = 0;
                    ?>
                    @foreach($data->groupBy('savings_officer') as $key=>$value)

                        <?php
                        $credit_total = $credit_total + $value->sum('credit');
                        $debit_total = $debit_total + $value->sum('debit');
                        ?>
                        <tr>
                            <td>{{ $key }}</td>
                            <td>
                                {{ number_format(  $value->sum('credit'),2) }}
                            </td>
                            <td>
                                {{ number_format($value->sum('debit'),2) }}
                            </td>

                            <td>{{ number_format(  $value->sum('credit')+$value->sum('debit'),2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="1"><b>{{trans_choice('core::general.total',1)}}</b></td>

                        <td>{{number_format($credit_total,2)}}</td>
                        <td>{{number_format($debit_total,2)}}</td>
                        <td>{{number_format($debit_total+$credit_total,2)}}</td>
                    </tr>
                    </tfoot>
                </table>
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                    <tr>
                        <th colspan="4">

                            <h4>  {{trans_choice('core::general.by',1)}}  {{trans_choice('core::general.date',1)}}</h4>

                        </th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            @if(!empty($data->first()) && !empty($branch_id))
                                {{trans_choice('core::general.branch',1)}}/ Team:

                                {{$data->first()->branch}}
                            @endif
                        </th>
                        <th colspan="2">
                            @if(!empty($data->first()) && !empty($savings_product_id))
                                {{trans_choice('savings::general.product',1)}}:

                                {{$data->first()->savings_product}}
                            @endif
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            @if(!empty($data->first()) && !empty($savings_officer_id))
                                {{trans_choice('savings::general.officer',1)}}:

                                {{$data->first()->savings_officer}}
                            @endif
                        </th>
                        <th colspan="1">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
                        <th colspan="1">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
                    </tr>
                    <tr style="background-color: #D1F9FF">
                        <th>{{trans_choice('core::general.date',1)}}</th>
                        <th>{{trans_choice('savings::general.deposit',2)}}</th>
                        <th>{{trans_choice('savings::general.withdrawal',2)}}</th>
                        <th>{{trans_choice('savings::general.total',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $credit_total = 0;
                    $debit_total = 0;
                    ?>
                    @foreach($data->groupBy('submitted_on') as $key=>$value)

                        <?php
                        $credit_total = $credit_total + $value->sum('credit');
                        $debit_total = $debit_total + $value->sum('debit');
                        ?>
                        <tr>
                            <td>{{ $key }}</td>
                            <td>
                                {{ number_format(  $value->sum('credit'),2) }}
                            </td>
                            <td>
                                {{ number_format($value->sum('debit'),2) }}
                            </td>

                            <td>{{ number_format(  $value->sum('credit')+$value->sum('debit'),2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="1"><b>{{trans_choice('core::general.total',1)}}</b></td>

                        <td>{{number_format($credit_total,2)}}</td>
                        <td>{{number_format($debit_total,2)}}</td>
                        <td>{{number_format($debit_total+$credit_total,2)}}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection
