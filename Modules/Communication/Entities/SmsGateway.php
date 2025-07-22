<?php

namespace Modules\Communication\Entities;

use Illuminate\Database\Eloquent\Model;

class SmsGateway extends Model
{
    protected $fillable = ['key', 'sender', 'active'];
    public $table = "sms_gateways";
    protected $casts = [
        'active' => 'boolean',
    ];
}