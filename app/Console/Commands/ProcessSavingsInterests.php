<?php

namespace App\Console\Commands;

use App\Helpers\GeneralHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Savings\Entities\Savings;
use Modules\Savings\Entities\SavingsTransaction;

class ProcessSavingsInterests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savings:interest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process savings interest';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        // 1️⃣ Calculate Interest
        $savingsList = DB::table('savings')
            ->join('savings_products', 'savings_products.id', 'savings.savings_product_id')
            ->select(
                'savings.*',
                'savings_products.interest_compounding_period',
                'savings_products.interest_calculation_type',
                'savings_products.minimum_balance',
                'savings_products.interest_rate'
            )
            ->where('savings.next_interest_calculation_date', $today)
            ->get();

        foreach ($savingsList as $savings) {
            [$startDate, $nextCalcDate] = $this->getPeriodDates($savings->interest_compounding_period, $savings->start_interest_calculation_date);

            if ($savings->interest_calculation_type === 'daily') {
                $this->calculateDailyInterest($savings, $startDate, $nextCalcDate);
            } elseif ($savings->interest_calculation_type === 'average') {
                $this->calculateAverageInterest($savings, $startDate, $nextCalcDate);
            }
        }

        // 2️⃣ Post Interest
        $postingList = DB::table('savings')
            ->join('savings_products', 'savings_products.id', 'savings.savings_product_id')
            ->select(
                'savings.*',
                'savings_products.chart_reference_id',
                'savings_products.accounting_rule',
                'savings_products.chart_expense_interest_id',
                'savings_products.interest_rate',
                'savings_products.interest_posting_period'
            )
            ->where('savings.next_interest_posting_date', $today)
            ->get();

        foreach ($postingList as $record) {
            $this->postInterest($record);
        }

        $this->info("Savings interest calculated and posted successfully");
    }

    /**
     * Get start date & next calculation date based on compounding period
     */
    private function getPeriodDates($period, $startInterestDate)
    {
        $periodMonths = [
            'monthly'   => 1,
            'quarterly' => 3,
            'biannual'  => 6,
            'annually'  => 12
        ];

        $months = $periodMonths[$period] ?? 1;

        $firstDay   = Carbon::today()->subMonths($months - 1)->firstOfMonth();
        $startDate  = Carbon::parse($startInterestDate)->gt($firstDay) ? $startInterestDate : $firstDay->format('Y-m-d');
        $nextDate   = Carbon::today()->addMonthsNoOverflow($months)->endOfMonth()->format('Y-m-d');

        return [$startDate, $nextDate];
    }

    /**
     * Calculate daily interest
     */
    private function calculateDailyInterest($savings, $startDate, $nextCalcDate)
    {
        if ($savings->interest_compounding_period === 'daily') {
            // Start date check
            if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . date("M")))) {
                $startDate = $savings->start_interest_calculation_date;
            } else {
                $startDate = Carbon::parse("first day of " . date("M"))->format("Y-m-d");
            }

            $totalBalance = GeneralHelper::savings_account_balance($savings->id, $startDate) + $savings->interest_earned;

            $todayBalance = SavingsTransaction::selectRaw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance')
                ->where('savings_id', $savings->id)
                ->where('reversed', 0)
                ->where('date', Carbon::today()->format("Y-m-d"))
                ->first();

            if (!empty($todayBalance)) {
                $totalBalance += $todayBalance->balance;
            }

            $savingsModel = Savings::find($savings->id);
            if ($totalBalance >= $savings->minimum_balance) {
                $interest = $totalBalance * ($savings->interest_rate / (100 * 365));
                $savingsModel->interest_earned += $interest;
            }

            $savingsModel->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
            $savingsModel->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
            $savingsModel->save();
        } else {
            // Non-daily compounding — calculate over a range
            $transactions = SavingsTransaction::selectRaw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance, date')
                ->where('savings_id', $savings->id)
                ->where('reversed', 0)
                ->whereBetween('date', [$startDate, Carbon::today()->format("Y-m-d")])
                ->groupBy('date')
                ->get();

            $balance = GeneralHelper::savings_account_balance($savings->id, $startDate) + $savings->interest_earned;
            $interest = 0;
            $totalDays = 0;

            if ($transactions->isEmpty()) {
                if ($balance >= $savings->minimum_balance) {
                    $days = Carbon::parse($startDate)->diffInDays(Carbon::today()) + 1;
                    $interest += ($balance * $days * $savings->interest_rate / (100 * 365));
                }
            } else {
                foreach ($transactions as $transaction) {
                    $days = Carbon::parse($startDate)->eq(Carbon::parse($transaction->date))
                        ? 1
                        : Carbon::parse($startDate)->diffInDays($transaction->date);

                    if ($balance >= $savings->minimum_balance) {
                        $interest += ($balance * $days * $savings->interest_rate / (100 * 365));
                    }

                    $startDate = Carbon::parse($startDate)->addDays($days)->format("Y-m-d");
                    $balance += $transaction->balance;
                    $totalDays += $days;
                }

                if (Carbon::parse($startDate)->notEqualTo(Carbon::today())) {
                    $days = Carbon::parse($startDate)->diffInDays(Carbon::today()) + 1;
                    if ($balance >= $savings->minimum_balance) {
                        $interest += ($balance * $days * $savings->interest_rate / (100 * 365));
                    }
                    $totalDays += $days;
                } else {
                    if ($balance >= $savings->minimum_balance) {
                        $interest += ($balance * $savings->interest_rate / (100 * 365));
                    }
                    $totalDays++;
                }
            }

            $savingsModel = Savings::find($savings->id);
            $savingsModel->interest_earned += $interest;
            $savingsModel->next_interest_calculation_date = $nextCalcDate;
            $savingsModel->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
            $savingsModel->save();
        }
    }

    /**
     * Calculate average interest
     */
    private function calculateAverageInterest($savings, $startDate, $nextCalcDate)
    {
        if ($savings->interest_compounding_period === 'daily') {
            if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . date("M")))) {
                $startDate = $savings->start_interest_calculation_date;
            } else {
                $startDate = Carbon::parse("first day of " . date("M"))->format("Y-m-d");
            }

            $totalBalance = GeneralHelper::savings_account_balance($savings->id, $startDate) + $savings->interest_earned;
            $todayBalance = SavingsTransaction::selectRaw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance')
                ->where('savings_id', $savings->id)
                ->where('reversed', 0)
                ->where('date', Carbon::today()->format("Y-m-d"))
                ->first();

            if (!empty($todayBalance)) {
                $totalBalance += $todayBalance->balance;
            }

            $savingsModel = Savings::find($savings->id);
            if ($totalBalance >= $savings->minimum_balance) {
                $interest = $totalBalance * ($savings->interest_rate / (100 * 365));
                $savingsModel->interest_earned += $interest;
            }

            $savingsModel->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
            $savingsModel->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
            $savingsModel->save();
        } else {
            $transactions = SavingsTransaction::selectRaw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance, date')
                ->where('savings_id', $savings->id)
                ->where('reversed', 0)
                ->whereBetween('date', [$startDate, Carbon::today()->format("Y-m-d")])
                ->groupBy('date')
                ->get();

            $balance = GeneralHelper::savings_account_balance($savings->id, $startDate);
            $interest = 0;
            $totalDays = 0;
            $averageBalance = 0;

            if ($transactions->isEmpty()) {
                if ($balance >= $savings->minimum_balance) {
                    $days = Carbon::parse($startDate)->diffInDays(Carbon::today()) + 1;
                    $interest += ($balance * $days * $savings->interest_rate / (100 * 365));
                }
            } else {
                foreach ($transactions as $transaction) {
                    $days = Carbon::parse($startDate)->eq(Carbon::parse($transaction->date))
                        ? 1
                        : Carbon::parse($startDate)->diffInDays($transaction->date);

                    $interest += ($balance * $days * $savings->interest_rate / (100 * 365));
                    $averageBalance += ($balance * $days);
                    $startDate = Carbon::parse($startDate)->addDays($days)->format("Y-m-d");
                    $balance += $transaction->balance;
                    $totalDays += $days;
                }

                if (Carbon::parse($startDate)->notEqualTo(Carbon::today())) {
                    $days = Carbon::parse($startDate)->diffInDays(Carbon::today()) + 1;
                    $averageBalance += ($balance * $days);
                    if ($balance >= $savings->minimum_balance) {
                        $interest += ($balance * $days * $savings->interest_rate / (100 * 365));
                    }
                    $totalDays += $days;
                } else {
                    $averageBalance += ($balance * 1);
                    if ($balance >= $savings->minimum_balance) {
                        $interest += ($balance * $savings->interest_rate / (100 * 365));
                    }
                    $totalDays++;
                }

                $averageBalance = $averageBalance / $totalDays;
                if ($averageBalance > $savings->minimum_balance) {
                    $interest += ($averageBalance * $totalDays * $savings->interest_rate / (100 * 365));
                }
            }

            $savingsModel = Savings::find($savings->id);
            $savingsModel->interest_earned += $interest;
            $savingsModel->next_interest_calculation_date = $nextCalcDate;
            $savingsModel->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
            $savingsModel->save();
        }
    }


    private function postInterest($record)
    {
        if ($record->interest_earned <= 0) return;

        // Create savings transaction
        $transaction = new SavingsTransaction([
            'borrower_id'     => $record->borrower_id,
            'branch_id'       => $record->branch_id,
            'savings_id'      => $record->id,
            'system_interest' => 1,
            'type'            => 'interest',
            'reversible'      => 1,
            'date'            => now()->format('Y-m-d'),
            'time'            => now()->format('H:i'),
            'year'            => now()->year,
            'month'           => now()->month,
            'credit'          => $record->interest_earned,
            'notes'           => "{$record->interest_rate} Per Annum Interest calculated",
        ]);
        $transaction->save();

        // Accounting entries
        if ($record->accounting_rule === 'cash_based') {
            $this->createJournalEntry($record->chart_reference_id, $transaction, $record->interest_earned, 'credit');
            $this->createJournalEntry($record->chart_expense_interest_id, $transaction, $record->interest_earned, 'debit');
        }

        // Update savings next posting date
        $months = [
            'monthly'   => 1,
            'quarterly' => 3,
            'biannual'  => 6,
            'annually'  => 12
        ][$record->interest_posting_period] ?? 1;

        Savings::where('id', $record->id)->update([
            'next_interest_posting_date' => Carbon::today()->addMonthsNoOverflow($months)->endOfMonth()->format('Y-m-d'),
            'interest_earned'            => 0,
            'last_interest_posting_date' => Carbon::today()->format('Y-m-d')
        ]);
    }

    private function createJournalEntry($accountId, $transaction, $amount, $type)
    {
        JournalEntry::create([
            'account_id'        => $accountId,
            'branch_id'         => $transaction->branch_id,
            'date'              => now()->format('Y-m-d'),
            'year'              => now()->year,
            'month'             => now()->month,
            'borrower_id'       => $transaction->borrower_id,
            'transaction_type'  => 'interest',
            'name'              => 'Savings Interest',
            'savings_id'        => $transaction->savings_id,
            $type               => $amount,
            'reference'         => $transaction->id
        ]);
    }

    // public function handle()
    // {
    //     //Calculate savings interest
    //     foreach (DB::table('savings')->join('savings_products', 'savings_products.id', 'savings.savings_product_id')->selectRaw("savings.*,savings_products.interest_compounding_period,savings_products.interest_calculation_type,savings_products.minimum_balance,savings_products.interest_rate")->where('savings.next_interest_calculation_date', Carbon::today()->format("Y-m-d"))->get() as $savings) {
    //         if ($savings->interest_compounding_period == "monthly") {
    //             if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . date("M")))) {
    //                 $start_date = $savings->start_interest_calculation_date;
    //             } else {
    //                 $start_date = Carbon::parse("first day of " . date("M"))->format("Y-m-d");
    //             }
    //             $next_interest_calculation_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(1)->format("M"))->format("Y-m-d");
    //         }
    //         if ($savings->interest_compounding_period == "quarterly") {
    //             if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . Carbon::today()->subMonths(2)->format("M")))) {
    //                 $start_date = $savings->start_interest_calculation_date;
    //             } else {
    //                 $start_date = Carbon::parse("first day of " . Carbon::today()->subMonths(2))->format("Y-m-d");
    //             }
    //             $next_interest_calculation_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(3)->format("M"))->format("Y-m-d");
    //         }
    //         if ($savings->interest_compounding_period == "biannual") {
    //             if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . Carbon::today()->subMonths(5)->format("M")))) {
    //                 $start_date = $savings->start_interest_calculation_date;
    //             } else {
    //                 $start_date = Carbon::parse("first day of " . Carbon::today()->subMonths(5))->format("Y-m-d");
    //             }
    //             $next_interest_calculation_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(6)->format("M"))->format("Y-m-d");

    //         }
    //         if ($savings->interest_compounding_period == "annually") {
    //             if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . Carbon::today()->subMonths(11)->format("M")))) {
    //                 $start_date = $savings->start_interest_calculation_date;
    //             } else {
    //                 $start_date = Carbon::parse("first day of " . Carbon::today()->subMonths(11))->format("Y-m-d");
    //             }
    //             $next_interest_calculation_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(12)->format("M"))->format("Y-m-d");
    //         }
    //         //calculate interest using daily balance
    //         if ($savings->interest_calculation_type == "daily") {
    //             if ($savings->interest_compounding_period == "daily") {
    //                 if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . date("M")))) {
    //                     $start_date = $savings->start_interest_calculation_date;
    //                 } else {
    //                     $start_date = Carbon::parse("first day of " . date("M"))->format("Y-m-d");
    //                 }
    //                 $total_balance = GeneralHelper::savings_account_balance($savings->id, $start_date) + $savings->interest_earned;
    //                 $today_balance = SavingsTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance'))->where('savings_id', $savings->id)->where('reversed', 0)->where('date', Carbon::today()->format("Y-m-d"))->first();
    //                 if (!empty($today_balance)) {
    //                     $total_balance = $today_balance->balance + $total_balance;
    //                 }
    //                 if ($total_balance >= $savings->minimum_balance) {
    //                     //calculate interest
    //                     $interest = $total_balance * ($savings->interest_rate / (100 * 365));
    //                     $savings_to_save = Savings::find($savings->id);
    //                     $savings_to_save->interest_earned = $savings->interest_earned + $interest;
    //                     $savings_to_save->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
    //                     $savings_to_save->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
    //                     $savings_to_save->save();
    //                 } else {
    //                     $savings_to_save = Savings::find($savings->id);
    //                     $savings_to_save->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
    //                     $savings_to_save->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
    //                     $savings_to_save->save();
    //                 }
    //             } else {
    //                 $transactions = SavingsTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance, date'))->where('savings_id', $savings->id)->where('reversed', 0)->whereBetween('date', [$start_date, Carbon::today()->format("Y-m-d")])->groupBy('date')->get();
    //                 $balance = GeneralHelper::savings_account_balance($savings->id, $start_date) + $savings->interest_earned;
    //                 $interest = 0;
    //                 $total_days = 0;
    //                 if (empty($transactions)) {
    //                     if ($balance >= $savings->minimum_balance) {
    //                         $days = Carbon::parse($start_date)->diffInDays(Carbon::today()->format("Y-m-d")) + 1;
    //                         $interest = $interest + ($balance * $days * $savings->interest_rate / (100 * 365));
    //                     }
    //                 } else {
    //                     foreach ($transactions as $transaction) {
    //                         if (Carbon::parse($start_date)->eq(Carbon::parse($transaction->date))) {
    //                             $days = 1;
    //                         } else {
    //                             $days = Carbon::parse($start_date)->diffInDays($transaction->date);
    //                         }
    //                         if ($balance >= $savings->minimum_balance) {
    //                             $interest = $interest + ($balance * $days * $savings->interest_rate / (100 * 365));
    //                         }
    //                         $start_date = Carbon::parse($start_date)->addDays($days)->format("Y-m-d");
    //                         $balance = $balance + $transaction->balance;
    //                         $total_days = $total_days + $days;
    //                     }
    //                     if (Carbon::parse($start_date)->notEqualTo(Carbon::today())) {
    //                         $days = Carbon::parse($start_date)->diffInDays(Carbon::today()) + 1;
    //                         if ($balance >= $savings->minimum_balance) {
    //                             $interest = $interest + ($balance * $days * $savings->interest_rate / (100 * 365));
    //                         }
    //                         $total_days = $total_days + $days;
    //                     } else {
    //                         if ($balance >= $savings->minimum_balance) {
    //                             $interest = $interest + ($balance * $savings->interest_rate / (100 * 365));
    //                         }
    //                         $total_days = $total_days + 1;
    //                     }
    //                 }
    //                 $savings_to_save = Savings::find($savings->id);
    //                 $savings_to_save->interest_earned = $savings->interest_earned + $interest;
    //                 $savings_to_save->next_interest_calculation_date = $next_interest_calculation_date;
    //                 $savings_to_save->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
    //                 $savings_to_save->save();
    //             }

    //         }
    //         //calculate interest using average balance
    //         if ($savings->interest_calculation_type == "average") {
    //             if ($savings->interest_compounding_period == "daily") {
    //                 if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . date("M")))) {
    //                     $start_date = $savings->start_interest_calculation_date;
    //                 } else {
    //                     $start_date = Carbon::parse("first day of " . date("M"))->format("Y-m-d");
    //                 }
    //                 $total_balance = GeneralHelper::savings_account_balance($savings->id, $start_date) + $savings->interest_earned;
    //                 $today_balance = SavingsTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance'))->where('savings_id', $savings->id)->where('reversed', 0)->where('date', Carbon::today()->format("Y-m-d"))->first();
    //                 if (!empty($today_balance)) {
    //                     $total_balance = $today_balance->balance + $total_balance;
    //                 }
    //                 if ($total_balance >= $savings->minimum_balance) {
    //                     //calculate interest
    //                     $savings_to_save = Savings::find($savings->id);
    //                     $interest = $total_balance * ($savings->interest_rate / (100 * 365));
    //                     $savings_to_save->interest_earned = $savings->interest_earned + $interest;
    //                     $savings_to_save->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
    //                     $savings_to_save->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
    //                     $savings_to_save->save();
    //                 } else {
    //                     $savings_to_save = Savings::find($savings->id);
    //                     $savings_to_save->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
    //                     $savings_to_save->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
    //                     $savings_to_save->save();
    //                 }
    //             } else {
    //                 $transactions = SavingsTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance, date'))->where('savings_id', $savings->id)->where('reversed', 0)->whereBetween('date', [$start_date, Carbon::today()->format("Y-m-d")])->groupBy('date')->get();
    //                 $balance = GeneralHelper::savings_account_balance($savings->id, $start_date);
    //                 $interest = 0;
    //                 $total_days = 0;
    //                 if (empty($transactions)) {
    //                     if ($balance >= $savings->minimum_balance) {
    //                         $days = Carbon::parse($start_date)->diffInDays(Carbon::today()->format("Y-m-d")) + 1;
    //                         $interest = $interest + ($balance * $days * $savings->interest_rate / (100 * 365));
    //                     }
    //                 } else {
    //                     $average_balance = 0;
    //                     foreach ($transactions as $transaction) {
    //                         if (Carbon::parse($start_date)->eq(Carbon::parse($transaction->date))) {
    //                             $days = 1;
    //                         } else {
    //                             $days = Carbon::parse($start_date)->diffInDays($transaction->date);
    //                         }
    //                         $interest = $interest + ($balance * $days * $savings->interest_rate / (100 * 365));
    //                         $average_balance = $average_balance + ($balance * $days);
    //                         $start_date = Carbon::parse($start_date)->addDays($days)->format("Y-m-d");
    //                         $balance = $balance + $transaction->balance;
    //                         $total_days = $total_days + $days;
    //                     }
    //                     if (Carbon::parse($start_date)->notEqualTo(Carbon::today())) {
    //                         $days = Carbon::parse($start_date)->diffInDays(Carbon::today()) + 1;
    //                         $average_balance = $average_balance + ($balance * $days);
    //                         if ($balance >= $savings->minimum_balance) {
    //                             $interest = $interest + ($balance * $days * $savings->interest_rate / (100 * 365));
    //                         }
    //                         $total_days = $total_days + $days;
    //                     } else {
    //                         $average_balance = $average_balance + ($balance * 1);
    //                         if ($balance >= $savings->minimum_balance) {
    //                             $interest = $interest + ($balance * $savings->interest_rate / (100 * 365));
    //                         }
    //                         $total_days = $total_days + 1;
    //                     }
    //                     $average_balance = $average_balance / $total_days;
    //                     if ($average_balance > $savings->minimum_balance) {
    //                         $interest = $interest + ($average_balance * $total_days * $savings->interest_rate / (100 * 365));
    //                     }
    //                 }
    //                 $savings_to_save = Savings::find($savings->id);
    //                 $savings_to_save->interest_earned = $savings->interest_earned + $interest;
    //                 $savings_to_save->next_interest_calculation_date = $next_interest_calculation_date;
    //                 $savings_to_save->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
    //                 $savings_to_save->save();
    //             }
    //         }


    //     }
    //     //post interest
    //     foreach (DB::table('savings')->join('savings_products', 'savings_products.id', 'savings.savings_product_id')->selectRaw("savings.*,savings_products.chart_reference_id,savings_products.accounting_rule,savings_products.chart_expense_interest_id,savings_products.interest_rate,savings_products.interest_posting_period")->where('savings.next_interest_posting_date', Carbon::today()->format("Y-m-d"))->get() as $key) {
    //         if ($key->interest_earned > 0) {
    //             $date = date("Y-m-d");
    //             $savings_transaction = new SavingsTransaction();
    //             $savings_transaction->borrower_id = $key->borrower_id;
    //             $savings_transaction->branch_id = $key->branch_id;
    //             $savings_transaction->savings_id = $key->id;
    //             $savings_transaction->system_interest = 1;
    //             $savings_transaction->type = "interest";
    //             $savings_transaction->reversible = 1;
    //             $savings_transaction->date = date("Y-m-d");
    //             $savings_transaction->time = date("H:i");
    //             $date = explode('-', date("Y-m-d"));
    //             $savings_transaction->year = $date[0];
    //             $savings_transaction->month = $date[1];
    //             $savings_transaction->credit = $key->interest_earned;
    //             $savings_transaction->notes = $key->interest_rate . " Per Annum Interest calculated";
    //             $savings_transaction->save();
    //             if ($key->accounting_rule == 'cash_based') {
    //                 $journal = new JournalEntry();
    //                 $journal->account_id = $key->chart_reference_id;
    //                 $journal->branch_id = $savings_transaction->branch_id;
    //                 $journal->date = date("Y-m-d");
    //                 $journal->year = $date[0];
    //                 $journal->month = $date[1];
    //                 $journal->borrower_id = $savings_transaction->borrower_id;
    //                 $journal->transaction_type = 'interest';
    //                 $journal->name = "Savings Interest";
    //                 $journal->savings_id = $key->id;
    //                 $journal->credit = $key->interest_earned;
    //                 $journal->reference = $savings_transaction->id;
    //                 $journal->save();

    //                 $journal = new JournalEntry();
    //                 $journal->account_id = $key->chart_expense_interest_id;
    //                 $journal->branch_id = $savings_transaction->branch_id;
    //                 $journal->date = date("Y-m-d");
    //                 $journal->year = $date[0];
    //                 $journal->month = $date[1];
    //                 $journal->borrower_id = $savings_transaction->borrower_id;
    //                 $journal->transaction_type = 'interest';
    //                 $journal->name = "Savings Interest";
    //                 $journal->savings_id = $key->id;
    //                 $journal->debit = $key->interest_earned;
    //                 $journal->reference = $savings_transaction->id;
    //                 $journal->save();
    //             }
    //         }
    //         $savings = Savings::find($key->id);
    //         if ($key->interest_posting_period == "monthly") {
    //             $savings->next_interest_posting_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(1)->format("M"))->format("Y-m-d");
    //         }
    //         if ($key->interest_posting_period == "quarterly") {
    //             $savings->next_interest_posting_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(3)->format("M"))->format("Y-m-d");
    //         }
    //         if ($key->interest_posting_period == "biannual") {
    //             $savings->next_interest_posting_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(6)->format("M"))->format("Y-m-d");
    //         }
    //         if ($key->interest_posting_period == "annually") {
    //             $savings->next_interest_posting_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(12)->format("M"))->format("Y-m-d");
    //         }
    //         $savings->interest_earned = 0;
    //         $savings->last_interest_posting_date = Carbon::today()->format("Y-m-d");
    //         $savings->save();
    //     }

    //     $this->info("Savings interest calculated and posted successfully");
    // }
}
