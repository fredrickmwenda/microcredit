<?php

namespace Modules\Savings\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class SavingsOfficerChangeAudit extends Model
{
    protected $fillable = [
        'savings_id',
        'client_id',
        'savings_account_number',
        'old_officer_id',
        'new_officer_id',
        'old_officer_name',
        'new_officer_name',
        'changed_by_user_id',
        'changed_by_user_name',
        'reason',
        'ip_address',
    ];

    public $table = "savings_officer_change_audits";

    public function savings()
    {
        return $this->belongsTo(Savings::class, 'savings_id', 'id');
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
