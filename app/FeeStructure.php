<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hrshadhin\Userstamps\UserstampsTrait;

class FeeStructure extends Model
{
    use SoftDeletes;
    use UserstampsTrait;

    protected $fillable = [
        'academic_year_id',
        'fee_type_id',
        'class_id',
        'term_id',
        'amount',
        'status',
    ];

    public function academicYear()
    {
        return $this->belongsTo('App\AcademicYear', 'academic_year_id');
    }

    public function feeType()
    {
        return $this->belongsTo('App\FeeType', 'fee_type_id');
    }

    public function class()
    {
        return $this->belongsTo('App\IClass', 'class_id');
    }

    public function term()
    {
        return $this->belongsTo('App\AcademicTerm', 'term_id');
    }
}
