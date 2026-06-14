<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hrshadhin\Userstamps\UserstampsTrait;

class AcademicTerm extends Model
{
    use SoftDeletes;
    use UserstampsTrait;

    protected $dates = ['start_date', 'end_date'];

    protected $fillable = [
        'academic_year_id',
        'name',
        'start_date',
        'end_date',
        'status',
    ];

    public function academicYear()
    {
        return $this->belongsTo('App\AcademicYear', 'academic_year_id');
    }

    public function feeStructures()
    {
        return $this->hasMany('App\FeeStructure', 'term_id');
    }
}
