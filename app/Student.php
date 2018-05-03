<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Student extends Model
{
    protected $fillable = [
        'status', 'userId', 'courseId' , 'score',
        'joinDate' , 'quitDate',
        'updatedBy','ps'
    ];

    public function getCenter()
    {
        return $this->course->center;
    }

    public function user() 
	{
		return $this->hasOne('App\User', 'id' ,'userId');
    }

    public function course() 
	{
		return $this->hasOne('App\Course', 'id' ,'courseId');
    }

    public function  loadViewModel()
    {
        $this->course->fullName();
        $this->user->loadContactInfo();
    }

    public function  mustInLesson(Carbon $lessonDate)
    {
        if($this->quitDate){
            //已退出
            $quitDate=new Carbon($this->quitDate);
            return $lessonDate->lt($quitDate);
        }else{
            $joinDate=new Carbon($this->joinDate);
            return $joinDate->lte($lessonDate);
        }
    }

    public function absenceRecords()
	{
        $lessons=$this->course->lessons;
        $lessonIds=$lessons->pluck('id')->toArray();
       
        $studentLessonRecords=LessonMember::whereIn('lessonId',$lessonIds)
                                            ->where('userId',$this->userId)
                                            ->where('role',Role::studentRoleName());


		return $studentLessonRecords->where('absence',true);

    }
    
    public function  hasQuit()
    {
        return $this->status < 0;
    }
}
