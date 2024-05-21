<?php

namespace Modules\Loan\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Loan\Entities\Loan;

class LoanStatusChanged
{
    use SerializesModels;
    public $loan;
    public $previous_status;
    public $loan_transaction_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Loan $loan, $previous_status = '',$loan_transaction_id=0)
    {
        $this->loan = $loan;
        $this->previous_status = $previous_status;
        $this->loan_transaction_id = $loan_transaction_id;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
