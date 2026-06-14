<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hrshadhin\Userstamps\UserstampsTrait;

class Expense extends Model
{
    use SoftDeletes;
    use UserstampsTrait;

    protected $dates = ['expense_date'];

    protected $fillable = [
        'expense_category_id',
        'expense_date',
        'amount',
        'description',
        'reference_no',
        'payment_method',
    ];

    public function category()
    {
        return $this->belongsTo('App\ExpenseCategory', 'expense_category_id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}
