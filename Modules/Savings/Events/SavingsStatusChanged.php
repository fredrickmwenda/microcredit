<?php

namespace Modules\Savings\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Savings\Entities\Savings;

class SavingsStatusChanged
{
    use SerializesModels;
    public $savings;
    public $previous_status;
    public $savings_transaction_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Savings $savings, $previous_status = '',$savings_transaction_id=0)
    {
        $this->savings = $savings;
        $this->previous_status = $previous_status;
        $this->savings_transaction_id = $savings_transaction_id;
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
