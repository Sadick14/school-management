<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hrshadhin\Userstamps\UserstampsTrait;

class StudentLedger extends Model
{
    use SoftDeletes;
    use UserstampsTrait;

    protected $dates = ['billing_date'];

    protected $fillable = [
        'registration_id',
        'student_id',
        'academic_year_id',
        'fee_type_id',
        'term_id',
        'billing_date',
        'description',
        'amount',
        'amount_paid',
        'balance',
        'source',
        'status',
    ];

    public function registration()
    {
        return $this->belongsTo('App\Registration', 'registration_id');
    }

    public function student()
    {
        return $this->belongsTo('App\Student', 'student_id');
    }

    public function academicYear()
    {
        return $this->belongsTo('App\AcademicYear', 'academic_year_id');
    }

    public function feeType()
    {
        return $this->belongsTo('App\FeeType', 'fee_type_id');
    }

    public function term()
    {
        return $this->belongsTo('App\AcademicTerm', 'term_id');
    }

    public function paymentItems()
    {
        return $this->hasMany('App\FeePaymentItem', 'student_ledger_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', '1')->where('balance', '!=', 0);
    }
}
