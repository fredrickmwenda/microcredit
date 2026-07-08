<?php

namespace Modules\Entry\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Client\Entities\Client;
use Modules\User\Entities\User;

class BulkSavingsEntry extends Model
{
    protected $fillable = [
        'savings_officer_id',
        'created_by_user_id',
        'verified_by_user_id',
        'status',
        'rejection_reason',
        'verified_at',
        'rejected_at'
    ];
    
    public $table = "bulk_savings_entries";

    protected $dates = ['verified_at', 'rejected_at', 'created_at', 'updated_at'];

    /**
     * Relationship: The officer whose clients are being assisted
     */
    public function savings_officer()
    {
        return $this->belongsTo(User::class, 'savings_officer_id', 'id');
    }

    /**
     * Relationship: The user assisting with entries
     */
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'id');
    }

    /**
     * Relationship: The Savings Operator who verified
     */
    public function verified_by()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id', 'id');
    }

    /**
     * Relationship: All items in this bulk entry
     */
    public function items()
    {
        return $this->hasMany(BulkSavingsEntryItem::class, 'bulk_savings_entry_id', 'id');
    }

    /**
     * Get summary statistics for this entry
     */
    public function getStats()
    {
        $items = $this->items;
        $totalDeposits = $items->where('transaction_type', 'deposit')->sum('amount');
        $totalWithdrawals = $items->where('transaction_type', 'withdrawal')->sum('amount');
        
        return [
            'total_items' => $items->count(),
            'total_deposits' => $totalDeposits,
            'total_withdrawals' => $totalWithdrawals,
            'deposit_count' => $items->where('transaction_type', 'deposit')->count(),
            'withdrawal_count' => $items->where('transaction_type', 'withdrawal')->count(),
        ];
    }
}
