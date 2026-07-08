<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Client\Entities\Client;
use Modules\User\Entities\User;


class LoanApplicationProcess extends Model
{
    use HasFactory;

    protected $table = 'loan_application_processes';

    protected $fillable = [
        'first_name','last_name', 'dob', 'gender', 'country_id', 'ghana_card_number', 'client_id',
        'phone_number', 'email', 'residential_address', 'digital_address',
        'employment_status', 'employer_business_name', 'occupation', 'monthly_net_income',
        'work_address', 'length_of_employment', 'loan_amount_requested', 'purpose_of_loan', 'loan_product_id',
        'repayment_period', 'preferred_repayment_method', 'income_stability_score',
        'debt_to_income_score', 'credit_history_score', 'employment_length_score',
        'guarantor_strength_score', 'total_score', 'risk_rating', 'level1_status',
        'recommended_amount', 'loan_officer_id', 'level1_decision_at', 'level2_status',
        'approved_amount', 'manager_id', 'level2_decision_at', 'reference_number',
        'overall_status', 'submitted_at'
    ];

    protected $casts = [
        'dob' => 'date',
        'monthly_net_income' => 'decimal:2',
        'loan_amount_requested' => 'decimal:2',
        'recommended_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'level1_decision_at' => 'datetime',
        'level2_decision_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function loanOfficer()
    {
        return $this->belongsTo(User::class, 'loan_officer_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }


    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($application) {
            $application->reference_number = 'LOAN-' . strtoupper(uniqid());
        });
    }
}
