<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;

class ClientImportModel extends Model
{
    protected $table = 'clients';
    protected $fillable = ['first_name', 'account_number'];
   
}
