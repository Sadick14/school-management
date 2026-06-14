<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeePaymentItem extends Model
{
    protected $fillable = [
        'fee_payment_id',
        'student_ledger_id',
        'amount_applied',
    ];

    public function payment()
    {
        return $this->belongsTo('App\FeePayment', 'fee_payment_id');
    }

    public function ledger()
    {
        return $this->belongsTo('App\StudentLedger', 'student_ledger_id');
    }
}
