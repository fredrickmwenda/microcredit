<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Loan\Entities\Loan;

class LoanTransaction extends Model
{
     protected $fillable = [
        'loan_id',
        'loan_schedule_id',
        'charge_id',
        'branch_id',
        'transaction_type',
        'date',
        'year',
        'month',
        'debit',
        'credit',
        'reversible',
        'reversed'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function schedule()
    {
        return $this->belongsTo(LoanSchedule::class, 'loan_schedule_id');
    }
}
