<?php

namespace Modules\Loan\Console;

use Illuminate\Console\Command;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanRepaymentSchedule;
use Illuminate\Support\Facades\DB;

class CloseZeroBalanceLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:close-zero-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close all loans with 0 balance and active status.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $loans = Loan::where('status', 'active')->get();
        $count = 0;
        foreach ($loans as $loan) {
            $total_remaining = LoanRepaymentSchedule::where('loan_id', $loan->id)
                ->sum(DB::raw('principal + interest + fees + penalties - principal_repaid_derived - interest_repaid_derived - fees_repaid_derived - penalties_repaid_derived'));
            if ($total_remaining <= 0) {
                $loan->status = 'closed';
                $loan->save();
                $count++;
                $this->info("Closed loan ID: {$loan->id}");
            }
        }
        $this->info("Total loans closed: $count");
        return 0;
    }
}
