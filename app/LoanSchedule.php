<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Loan\Entities\Loan;

class LoanSchedule extends Model
{
    // If table name is different from 'loan_schedules', uncomment and edit:
    // protected $table = 'loan_schedules';

    // Allow mass assignment for specific columns (optional)
    protected $fillable = [
        'loan_id',
        'due_date',
        'principal',
        'interest',
        'fees',
        'penalty',
        'principal_paid',
        'interest_paid',
        'fees_paid',
        'penalty_paid',
        'principal_waived',
        'interest_waived',
        'fees_waived',
        'penalty_waived',
        'missed_penalty_applied',
    ];

    // Relationships
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
