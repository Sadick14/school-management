<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hrshadhin\Userstamps\UserstampsTrait;

class ExpenseCategory extends Model
{
    use SoftDeletes;
    use UserstampsTrait;

    protected $fillable = [
        'name',
        'status',
    ];

    public function expenses()
    {
        return $this->hasMany('App\Expense', 'expense_category_id');
    }
}
