<?php

namespace App;

use App\Http\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hrshadhin\Userstamps\UserstampsTrait;
use Illuminate\Support\Arr;


class Subject extends Model
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
        'code',
        'type',
        'status',
        'order',
        'exclude_in_result'
    ];


    public function teachers()
    {
        return $this->belongsToMany('App\Employee','teacher_subjects', 'subject_id', 'teacher_id')
            ->select('employees.id','employees.name');
    }
    public function classes()
    {
        return $this->belongsToMany('App\IClass', 'class_subjects', 'subject_id', 'class_id');
    }

    public function marks()
    {
        return $this->hasMany('App\Mark', 'subject_id');
    }

    public function getTypeAttribute($value)
    {
        return Arr::get(AppHelper::SUBJECT_TYPE, $value);
    }

    public function scopeIclass($query, $classId)
    {
        if($classId){
            return $query->whereHas('classes', function ($q) use ($classId) {
                $q->where('i_classes.id', $classId);
            });
        }

        return $query;
    }

    public function scopeSType($query, $subjectType)
    {
        if($subjectType && is_array($subjectType)){
            return $query->whereIn('type', $subjectType);
        }
        if($subjectType){
            return $query->where('type', $subjectType);
        }

        return $query;
    }

    public function students()
    {
        return $this->belongsToMany('App\Registration','student_subjects', 'subject_id', 'registration_id');
    }


}
