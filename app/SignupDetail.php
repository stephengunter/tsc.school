<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Course;
use App\Student;
use App\QuitDetail;
use App\Tran;

class SignupDetail extends Model
{
    protected $table = 'signupDetails';

    protected $fillable = [ 'signupId', 'courseId', 'canceled',
    'tuition' , 'cost' , 'updatedBy'  ];

    public static function init(Course $course)
	{
        $course->fullName();
        $course->loadClassTimes();
		return [
          
            'courseId' => $course->id,
			'tuition' => $course->tuition,
            'cost' => $course->cost,
            
            'course' => $course
		];
	}	
   

    public function signup() 
	{
		return $this->hasOne('App\Signup', 'id' ,'signupId');
    }

    public function course() 
	{
		return $this->hasOne('App\Course', 'id' ,'courseId');
    }

    public function total() 
	{
        $total= $this->tuition + $this->cost;
        $this->total=$total;
        return $total;
    }

    public function actualTuition() 
	{
       
        if(!$this->signup->hasDiscount()) return $this->tuition;

        $points=(int)$this->signup->points;

        return $this->tuition * $points /100 ;
    }

    public function getStudent()
    {
        return Student::where('userId',$this->signup->userId)
                ->where('courseId',$this->courseId)->first();
    }

    public function getTranRecord()
    {
        return Tran::where('signupDetailId',$this->id)->first();

    }

    public function updateStatus()
    {
        if($this->hasQuit()) $this->canceled=true;
        else if($this->hasTran()) $this->canceled=true;
        else $this->canceled=false;

        $this->save();
    }

    public function hasTran()
    {
        $tranRecord=$this->getTranRecord();
        if($tranRecord) return true;
        return false;
    }

    public function getQuitDetail()
    {
        return QuitDetail::where('signupDetailId',$this->id)->first();
    }

    public function hasQuit()
    {
        $quitDetail=$this->getQuitDetail();
        if($quitDetail) return true;
        return false;
    }

    public function canQuit()
    {
        $tran=$this->getTranRecord();
        if($tran) return false;

        $hasQuit=$this->hasQuit();
        if($hasQuit) return false;

        return true;
    }

    public function loadViewModel()
    {
        $this->course->fullName();
        
        $tran=$this->getTranRecord();

        if($tran){
            $this->ps ='(' . $tran->date . '辦理轉班' . ') ' ;

            return;
        }

        $quitDetail=$this->getQuitDetail();

        if($quitDetail){
            $this->ps ='(' . $quitDetail->quit->date . '辦理退費' . ') ' ;

            return;
        }

    }


}
