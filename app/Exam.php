<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hrshadhin\Userstamps\UserstampsTrait;

class Exam extends Model
{
    use SoftDeletes;
    use UserstampsTrait;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'ca_weight',
        'status',
        'open_for_marks_entry'
    ];


    public function classes()
    {
        return $this->belongsToMany('App\IClass', 'exam_iclass', 'exam_id', 'class_id');
    }

    public function scopeForClass($query, $classId)
    {
        if($classId){
            return $query->whereHas('classes', function ($q) use ($classId) {
                $q->where('i_classes.id', $classId);
            });
        }

        return $query;
    }

    public function marks()
    {
        return $this->hasMany('App\Mark', 'exam_id');

    }

    public function result()
    {
        return $this->hasMany('App\Result', 'exam_id');

    }
}
