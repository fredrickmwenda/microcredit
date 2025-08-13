<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollMeta extends Model
{
    // Table name will be payroll_metas unless changed
    protected $table = 'payroll_metas';

    // Allow mass assignment for these fields
    protected $fillable = [
        'payroll_id',
        'payroll_template_meta_id',
        'value',
        'position',
    ];

    /**
     * Relationship: A payroll meta belongs to a payroll record.
     */
    public function payroll()
    {
        return $this->belongsTo(\Modules\Payroll\Entities\Payroll::class);
    }
}
