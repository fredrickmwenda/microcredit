<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class LoanOfficerChangeAudit extends Model
{
    protected $fillable = [
        'loan_id',
        'client_id',
        'loan_account_number',
        'old_officer_id',
        'new_officer_id',
        'old_officer_name',
        'new_officer_name',
        'changed_by_user_id',
        'changed_by_user_name',
        'reason',
        'ip_address',
    ];

    public $table = "loan_officer_change_audits";

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'id');
    }

    public function old_officer()
    {
        return $this->belongsTo(User::class, 'old_officer_id', 'id');
    }

    public function new_officer()
    {
        return $this->belongsTo(User::class, 'new_officer_id', 'id');
    }

    public function changed_by_user()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id', 'id');
    }
}
