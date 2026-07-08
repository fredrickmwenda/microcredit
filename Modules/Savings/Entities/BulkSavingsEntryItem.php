<?php

namespace Modules\Savings\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Client\Entities\Client;

class BulkSavingsEntryItem extends Model
{
    protected $fillable = [
        'bulk_savings_entry_id',
        'savings_id',
        'client_id',
        'transaction_type',
        'amount',
        'notes',
        'savings_transaction_id'
    ];
    
    public $table = "bulk_savings_entry_items";

    /**
     * Relationship: Parent bulk entry
     */
    public function bulk_entry()
    {
        return $this->belongsTo(BulkSavingsEntry::class, 'bulk_savings_entry_id', 'id');
    }

    /**
     * Relationship: The savings account
     */
    public function savings()
    {
        return $this->belongsTo(Savings::class, 'savings_id', 'id');
    }

    /**
     * Relationship: The client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /**
     * Relationship: The created savings transaction
     */
    public function savings_transaction()
    {
        return $this->belongsTo(SavingsTransaction::class, 'savings_transaction_id', 'id');
    }
}
