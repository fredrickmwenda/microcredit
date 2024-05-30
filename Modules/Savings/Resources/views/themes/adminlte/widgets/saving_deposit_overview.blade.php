<div class="grid-stack-item savings_deposit_overview"
     gs-x="{{$config["x"]}}" gs-y="{{$config["y"]}}"
     gs-w="{{$config["width"]}}" gs-h="{{$config["height"]}}" gs-id="SavingsDepositOverview">
    <div class="grid-stack-item-content">
        <div class="card card-bordered card-preview">
            <div class="card-header with-border">
                <h3 class="card-title">Savings {{ trans_choice('loan::general.statistic',2) }}</h3>

                <div class="card-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-card-widget="collapse"><i
                                class="fa fa-minus"></i>
                    </button>
                </div>
                <!-- /.box-tools -->
            </div>
            <div class="card-body">

                <!-- /.box-header -->
                <?php
                $payments_today = $savings_deposits->where('submitted_on', \Illuminate\Support\Carbon::today())->sum('amount');
                $payments_this_week = $savings_deposits->whereBetween('submitted_on', [\Illuminate\Support\Carbon::today()->startOfWeek()->format("Y-m-d"), \Illuminate\Support\Carbon::today()->endOfWeek()->format("Y-m-d")])->sum('amount');
                $payments_this_month = $savings_deposits->sum('amount');
                $target = $savings_deposits->sum('amount');
                if ($target != 0) {
                    $completion = ($payments_this_month / $target) * 100;
                } else {
                    $completion = 0;
                }
                ?>
                <div class="card-body" id="">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="content-group">

                                <h5 class="text-semibold no-margin">
                                    {{number_format($payments_today,2)}}
                                </h5>
                                <span class="text-muted text-size-small">{{ trans_choice('loan::general.today',1) }} </span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="content-group">
                                <h5 class="text-semibold no-margin">
                                    {{number_format($payments_this_week,2)}}
                                </h5>
                                <span class="text-muted text-size-small">{{ trans_choice('loan::general.this',1) }} {{ trans_choice('loan::general.week',1) }}</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="content-group">

                                <h5 class="text-semibold no-margin"> {{number_format($payments_this_month,2)}}</h5>
                                <span class="text-muted text-size-small">{{ trans_choice('loan::general.this',1) }} {{ trans_choice('loan::general.month',1) }}</span>
                            </div>
                        </div>

                    </div>
                    <!--div class="row">
                        <div class="col-md-12">
                            <div class="text-center">
                                <h5 class=" text-semibold">{{ trans_choice('loan::general.monthly',1) }} {{ trans_choice('loan::general.target',1) }}</h5>
                            </div>
                            <div class="progress" data-bs-toggle="tooltip" title=""
                                 data-original-title="{{ trans_choice('loan::general.target',1) }} : {{number_format($target,2)}}">

                                <div class="progress-bar progress-bar-success progress-bar-striped active"
                                     style="width: {{round($completion)}}%">
                                    <span>{{round($completion)}}% {{ trans_choice('loan::general.complete',1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div-->
                    <div class="row">
                        <div class="col-md-12">
                            <div id="collection_statistics_graph">
                                {!! $chart->container() !!}
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
</div>

{!! $chart->script() !!}