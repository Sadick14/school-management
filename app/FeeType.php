<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hrshadhin\Userstamps\UserstampsTrait;

class FeeType extends Model
{
    use SoftDeletes;
    use UserstampsTrait;

    protected $fillable = [
        'code',
        'name',
        'billing_cycle',
        'applies_to',
        'is_optional',
        'status',
    ];

    public function feeStructures()
    {
        return $this->hasMany('App\FeeStructure', 'fee_type_id');
    }

    public function ledgers()
    {
        return $this->hasMany('App\StudentLedger', 'fee_type_id');
    }
}
