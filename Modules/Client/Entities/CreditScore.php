<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditScore extends Model
{
    use HasFactory;
    protected $table = 'credit_scores';
    
    protected $fillable = [
        'client_id', 'score', 'range_id', 'rating_label', 'assessed_at', 'notes'
    ];

    protected $casts = [
        'assessed_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function range()
    {
        return $this->belongsTo(CreditScoreRange::class, 'range_id');
    }
    // protected static function newFactory()
    // {
    //     return \Modules\Client\Database\factories\CreditScoreFactory::new();
    // }
}
