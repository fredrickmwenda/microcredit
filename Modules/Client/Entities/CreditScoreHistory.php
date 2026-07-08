<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditScoreHistory extends Model
{
    use HasFactory;
        protected $table = 'credit_score_histories';
    
    protected $fillable = [
        'client_id', 'previous_score', 'new_score', 'status', 'change_date', 'reason'
    ];

    protected $casts = [
        'change_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

 


    // protected static function newFactory()
    // {
    //     return \Modules\Client\Database\factories\CreditScoreHistoryFactory::new();
    // }
}
