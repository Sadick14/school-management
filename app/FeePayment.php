<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hrshadhin\Userstamps\UserstampsTrait;

class FeePayment extends Model
{
    use SoftDeletes;
    use UserstampsTrait;

    protected $dates = ['payment_date'];

    protected $fillable = [
        'receipt_no',
        'payment_date',
        'academic_year_id',
        'registration_id',
        'student_id',
        'total_amount',
        'payment_method',
        'paid_by',
        'note',
    ];

    public function academicYear()
    {
        return $this->belongsTo('App\AcademicYear', 'academic_year_id');
    }

    public function registration()
    {
        return $this->belongsTo('App\Registration', 'registration_id');
    }

    public function student()
    {
        return $this->belongsTo('App\Student', 'student_id');
    }

    public function items()
    {
        return $this->hasMany('App\FeePaymentItem', 'fee_payment_id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}