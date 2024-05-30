<div class="grid-stack-item loan_statistics" gs-x="{{$config["x"]}}" gs-y="{{$config["y"]}}" gs-w="{{$config["width"]}}" gs-h="{{$config["height"]}}" gs-id="LoanStatistics">
    <div class="grid-stack-item-content">
        <div class="row mb-2">
            <!-- <div class="col-md-3">
                <div class="card">
                    <span class="info-box-icon bg-green elevation-1">
                        <i class="fas fa-money-bill"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ trans_choice('loan::general.loan',2) }} {{ trans_choice('loan::general.disbursed',2) }}</span>
                        <span class="info-box-number">{{number_format(\Modules\Loan\Entities\Loan::where('status','active')->sum('principal'))}}</span>
                    </div>
                    
                </div>
            </div> -->
            <div class="col-md-3 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-4">
                            <span class="info-box-icon bg-green elevation-1">
                                        <i class="ri-money-dollar-circle-fill" style="font-size: 46px;"></i>
                                    </span>
                            </div>
                            <div class="col-8">
                                <!-- <div class="text-end"> -->
                                    <!-- <h3 class="my-1 py-1">{{number_format(\Modules\Loan\Entities\Loan::where('status','active')->sum('principal'))}}</h3> -->
                                    <!-- <div id="deals-chart" data-colors="#e7607b"></div> -->
                                    <h5 class="text-muted fw-normal mt-0 text-truncate" title="Deals">{{ trans_choice('loan::general.loan',2) }} {{ trans_choice('loan::general.disbursed',2) }}</h5>


                                    <!-- <h3 class="my-1 py-1">861</h3> -->
                                    <p class="mb-0 text-muted">
                                    <h3 class="my-1 py-1">{{number_format(\Modules\Loan\Entities\Loan::where('status','active')->sum('principal'))}}</h3>

                                        <!-- <span class="text-success me-2"><i class="ri-arrow-up-line"></i> 4.87%</span> -->
                                    </p>
                                <!-- </div> -->
                            </div>
                        </div> <!-- end row-->
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
            <!-- <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-green elevation-1">
                        <i class="fas fa-dollar-sign"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text"></span>
                        <span class="info-box-number">{{number_format(\Modules\Loan\Entities\LoanTransaction::where('reversed',0)->whereIn('loan_transaction_type_id',[2,5,8])->sum('amount'))}}</span>
                    </div>
                   
                </div>
            </div> -->

            <div class="col-md-3 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-4">
                            <span class="info-box-icon bg-green elevation-1">
                                        <i class="ri-money-cny-circle-fill" style="font-size: 46px;"></i>
                                    </span>
                            </div>
                            <div class="col-8">
                                    <h5 class="text-muted fw-normal mt-0 text-truncate" title="Deals">{{ trans_choice('loan::general.total',1) }} {{ trans_choice('loan::general.repayment',2) }}</h5>
                                    <p class="mb-0 text-muted">
                                    <h3 class="my-1 py-1">{{number_format(\Modules\Loan\Entities\LoanTransaction::where('reversed',0)->whereIn('loan_transaction_type_id',[2,5,8])->sum('amount'))}}</h3>
                                    </p>
                            </div>
                        </div> 
                    </div>
                </div> <!-- end card -->
            </div>             
            <?php
            $total_principal = 0;
            $total_principal_waived = 0;
            $total_principal_paid = 0;
            $total_principal_written_off = 0;
            $total_principal_outstanding = 0;
            $total_principal_overdue = 0;
            $total_interest = 0;
            $total_interest_waived = 0;
            $total_interest_paid = 0;
            $total_interest_written_off = 0;
            $total_interest_outstanding = 0;
            $total_interest_overdue = 0;
            $total_fees = 0;
            $total_fees_waived = 0;
            $total_fees_paid = 0;
            $total_fees_written_off = 0;
            $total_fees_outstanding = 0;
            $total_fees_overdue = 0;
            $total_penalties = 0;
            $total_penalties_waived = 0;
            $total_penalties_paid = 0;
            $total_penalties_written_off = 0;
            $total_penalties_outstanding = 0;
            $total_penalties_overdue = 0;
            $total_arrears_amount = 0;
            foreach ($loans as $loan) {
                $total_principal = $total_principal + $loan->repayment_schedules->sum('principal');
                $total_principal_paid = $total_principal_paid + $loan->repayment_schedules->sum('principal_repaid_derived');
                $total_principal_written_off = $total_principal_written_off + $loan->repayment_schedules->sum('principal_written_off_derived');
                $total_interest = $total_interest + $loan->repayment_schedules->sum('interest');
                $total_interest_waived = $total_interest_waived + $loan->repayment_schedules->sum('interest_waived_derived');
                $total_interest_paid = $total_interest_paid + $loan->repayment_schedules->sum('interest_repaid_derived');
                $total_interest_written_off = $total_interest_written_off + $loan->repayment_schedules->sum('interest_written_off_derived');
                $total_fees = $total_fees + $loan->repayment_schedules->sum('fees') + $loan->disbursement_charges;
                $total_fees_waived = $total_fees_waived + $loan->repayment_schedules->sum('fees_waived_derived');
                $total_fees_paid = $total_fees_paid + $loan->repayment_schedules->sum('fees_repaid_derived') + $loan->disbursement_charges;
                $total_fees_written_off = $total_fees_written_off + $loan->repayment_schedules->sum('fees_written_off_derived');

                $total_penalties = $total_penalties + $loan->repayment_schedules->sum('penalties');
                $total_penalties_waived = $total_penalties_waived + $loan->repayment_schedules->sum('penalties_waived_derived');
                $total_penalties_paid = $total_penalties_paid + $loan->repayment_schedules->sum('penalties_repaid_derived');
                $total_penalties_written_off = $total_penalties_written_off + $loan->repayment_schedules->sum('penalties_written_off_derived');
                //arrears
                $arrears_last_schedule = $loan->repayment_schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->first();
                if (!empty($arrears_last_schedule)) {
                    $overdue_schedules = $loan->repayment_schedules->where('due_date', '<=', $arrears_last_schedule->due_date);
                    $total_principal_overdue = $total_principal_overdue + $overdue_schedules->sum('principal') - $overdue_schedules->sum('principal_written_off_derived') - $overdue_schedules->sum('principal_repaid_derived');
                    $total_interest_overdue = $total_interest_overdue + $overdue_schedules->sum('interest') - $overdue_schedules->sum('interest_written_off_derived') - $overdue_schedules->sum('interest_repaid_derived') - $overdue_schedules->sum('interest_waived_derived');
                    $total_fees_overdue = $total_fees_overdue + $overdue_schedules->sum('fees') - $overdue_schedules->sum('fees_written_off_derived') - $overdue_schedules->sum('fees_repaid_derived') - $overdue_schedules->sum('fees_waived_derived');
                    $total_penalties_overdue = $total_penalties_overdue + $overdue_schedules->sum('penalties') - $overdue_schedules->sum('penalties_written_off_derived') - $overdue_schedules->sum('penalties_repaid_derived') - $overdue_schedules->sum('penalties_waived_derived');
                }
            }
            $total_principal_outstanding = $total_principal - $total_principal_waived - $total_principal_paid - $total_principal_written_off;
            $total_interest_outstanding = $total_interest - $total_interest_waived - $total_interest_paid - $total_interest_written_off;
            $total_fees_outstanding = $total_fees - $total_fees_waived - $total_fees_paid - $total_fees_written_off;
            $total_penalties_outstanding = $total_penalties - $total_penalties_waived - $total_penalties_paid - $total_penalties_written_off;
            $total_balance = $total_principal_outstanding + $total_interest_outstanding + $total_fees_outstanding + $total_penalties_outstanding;
            $total_arrears_amount = $total_principal_overdue + $total_interest_overdue + $total_fees_overdue + $total_penalties_overdue;
            ?>

            <div class="col-md-3 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-4">
                            <span class="info-box-icon bg-green elevation-1">
                                        <i class="ri-money-cny-circle-fill" style="font-size: 46px;"></i>
                                    </span>
                            </div>
                            <div class="col-8">
                                    <h5 class="text-muted fw-normal mt-0 text-truncate" title="Deals">{{ trans_choice('loan::general.total',1) }}  {{ trans_choice('loan::general.outstanding',2) }}</h5>
                                    <p class="mb-0 text-muted">
                                    <h3 class="my-1 py-1">{{number_format($total_balance)}}</h3>
                                    </p>
                            </div>
                        </div> 
                    </div>
                </div> <!-- end card -->
            </div>

            <div class="col-md-3 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-4">
                            <span class="info-box-icon bg-green elevation-1">
                                        <i class="ri-money-cny-circle-fill" style="font-size: 46px;"></i>
                                    </span>
                            </div>
                            <div class="col-8">
                                    <h5 class="text-muted fw-normal mt-0 text-truncate" title="Deals">{{ trans_choice('loan::general.total',1) }}  {{ trans_choice('loan::general.arrears',2) }}</h5>
                                    <p class="mb-0 text-muted">
                                    <h3 class="my-1 py-1">{{number_format($total_arrears_amount)}}</h3>
                                    </p>
                            </div>
                        </div> 
                    </div>
                </div> <!-- end card -->
            </div> 
           
        </div>

    </div>
</div>