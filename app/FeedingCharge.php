<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedingCharge extends Model
{
    protected $dates = ['charge_date'];

    protected $fillable = [
        'registration_id',
        'student_ledger_id',
        'charge_date',
        'amount',
    ];

    public function ledger()
    {
        return $this->belongsTo('App\StudentLedger', 'student_ledger_id');
    }

    public function registration()
    {
        return $this->belongsTo('App\Registration', 'registration_id');
    }
}
