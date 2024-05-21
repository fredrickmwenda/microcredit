<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 8/2/19
 * Time: 8:50 PM
 */

namespace Modules\Savings\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Savings\Charts\SavingsDepositOverviewBarChart;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanRepaymentSchedule;
use Modules\Savings\Entities\SavingsTransaction;

class SavingsDepositOverview extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $repayment_schedules = LoanRepaymentSchedule::join("loans", "loans.id", "loan_repayment_schedules.loan_id")
            ->selectRaw('loan_repayment_schedules.*')
            ->whereBetween('due_date', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])
            ->where('loans.status', 'active')
            ->get();
        $savings_deposits = SavingsTransaction::join("savings", "savings.id", "savings_transactions.savings_id")
            ->selectRaw('savings_transactions.*')
            ->whereBetween('submitted_on', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])
            ->where('savings.status', 'active')
            ->get();
        $chart = new SavingsDepositOverviewBarChart();
        $labels = [];
        $start_date = Carbon::today()->subYearsNoOverflow(1);
        $expected = [];
        $actual = [];

        for ($i = 0; $i < 13; $i++) {
            $data = SavingsTransaction::join("savings", "savings.id", "savings_transactions.savings_id")
                ->whereBetween('submitted_on', [$start_date->startOfMonth()->format("Y-m-d"), $start_date->endOfMonth()->format("Y-m-d")])
                ->where('savings.status', 'active')
                ->selectRaw('savings_transactions.*')
                ->get();
            $payments = 0 + $data->sum('amount');
            $target = 0 + $data->sum('amount');
            array_push($expected, round($target, 2));
            array_push($actual, round($payments, 2));
            array_push($labels, $start_date->format("M") . " " . $start_date->format("Y"));
            $start_date = $start_date->addMonthsNoOverflow(1);
        }

        /*for ($i = 0; $i < 13; $i++) {
            $data = LoanRepaymentSchedule::join("loans", "loans.id", "loan_repayment_schedules.loan_id")
                ->whereBetween('due_date', [$start_date->startOfMonth()->format("Y-m-d"), $start_date->endOfMonth()->format("Y-m-d")])
                ->where('loans.status', 'active')
                ->selectRaw('loan_repayment_schedules.*')
                ->get();
            $payments = 0 + $data->sum('principal_repaid_derived') + $data->sum('interest_repaid_derived') + $data->sum('fees_repaid_derived') + $data->sum('penalties_repaid_derived');
            $target = 0 + $data->sum('principal') + $data->sum('interest') + $data->sum('fees') + $data->sum('penalties');
            array_push($expected, round($target, 2));
            array_push($actual, round($payments, 2));
            array_push($labels, $start_date->format("M") . " " . $start_date->format("Y"));
            $start_date = $start_date->addMonthsNoOverflow(1);
        }*/
        $chart->labels(array_values($labels))->labelsRotation(-30);
        $chart->title(trans_choice('loan::general.collection', 1) . ' ' . trans_choice('loan::general.overview', 1));
        $chart->label(trans_choice('loan::general.amount', 1));
        $chart->dataset(trans_choice('loan::general.expected', 1), 'line', array_values($expected))->color('red');
        $chart->dataset(trans_choice('loan::general.actual', 1), 'line', array_values($actual))->color('green');

        return theme_view('savings::widgets.saving_deposit_overview', [
            'config' => $this->config,
            'savings_deposits' => $savings_deposits,
            'chart' => $chart
        ]);
    }
}