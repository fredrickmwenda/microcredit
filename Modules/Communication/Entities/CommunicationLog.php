<?php

namespace Modules\Communication\Entities;

use Illuminate\Database\Eloquent\Model;

class CommunicationLog extends Model
{
    protected $fillable = ['client_id','sms_gateway_id','text_body','send_to'];
}
