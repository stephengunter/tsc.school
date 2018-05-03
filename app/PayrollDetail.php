<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Lesson;
use Carbon\Carbon;
use App\Core\Helper;

class PayrollDetail extends Model
{
    protected $table = 'payrollDetails';

    protected $fillable = [ 
        'payrollId', 'lessonId', 
        'date' , 'on' , 'off' ,'minutes'  ,
        'studentCount','wageMoney'
    ];

    

    public function payroll() 
	{
		return $this->hasOne('App\Payroll', 'id' ,'payrollId');
    }

    public function getMoney()
    {
        return $this->wageMoney * $this->minutes /60;
    }

    public function getLesson()
    {
        return Lesson::find($this->lessonId);
    }

    public function timeString()
	{
		$timeString= Helper::toTimeString($this->on) . '~' . Helper::toTimeString($this->off);
		$this->timeString=$timeString;
		return $timeString;
    }
    
    public function hours()
	{
        $hours=$this->minutes/60;
		$this->hours=$hours;
		return $hours;
	}

    public function loadViewModel()
    {
        $lesson=$this->getLesson();
        $lesson->loadViewModel();

        $this->lesson=$lesson;

        $this->timeString();
        $this->hours();
        $this->money=$this->getMoney();
		
	}
}
