<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tran;
use Carbon\Carbon;

class Student extends Model
{
    protected $fillable = [
        'status', 'userId', 'courseId' , 'score',
        'joinDate' , 'quitDate', 'tran_id_from', 'tran_id_to',
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
        
        $tranFrom=$this->getTranFrom();
        if($tranFrom){
            $this->ps ='(' . $tranFrom->date . '轉班加入' . ') ' .$this->ps;
        }

        $tranTo=$this->getTranTo();
        if($tranTo){
            $this->ps ='(' . $tranTo->date . '轉班退出' . ') ' .$this->ps;
        }

        $this->course->fullName();
        $this->user->loadContactInfo();
    }

    public function  getTranFrom()
    {
        if(!$this->tran_id_from) return null;

        return Tran::find($this->tran_id_from);
    }

    public function  getTranTo()
    {
        if(!$this->tran_id_to) return null;

        return Tran::find($this->tran_id_to);
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
