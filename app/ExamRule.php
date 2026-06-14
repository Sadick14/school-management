<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hrshadhin\Userstamps\UserstampsTrait;

class ExamRule extends Model
{
    use SoftDeletes;
    use UserstampsTrait;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class_id',
        'subject_id',
        'exam_id',
        'grade_id',
        'ca_total_marks',
        'exam_total_marks',
        'pass_mark',
    ];

    public function class()
    {
        return $this->belongsTo('App\IClass', 'class_id');
    }
    public function exam()
    {
        return $this->belongsTo('App\Exam', 'exam_id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subject', 'subject_id');
    }

    public function grade()
    {
        return $this->belongsTo('App\Grade', 'grade_id');
    }
}
