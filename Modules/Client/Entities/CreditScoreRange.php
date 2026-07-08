<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditScoreRange extends Model
{
    use HasFactory;

   protected $table ='credit_score_ranges';


    protected $fillable = ['min_score', 'max_score', 'rating_label', 'color_code', 'description', 'sort_order'];

    public static function getRatingForScore(int $score): ?self
    {
        return static::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();
    }
}
