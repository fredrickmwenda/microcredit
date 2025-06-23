<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blacklist extends Model
{
    use SoftDeletes;
    protected $table = 'blacklists';
    protected $fillable = [
        'client_id',
        'reason',
        'status', // active or inactive
        'created_by',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
