<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Course;
use App\Student;

class SignupDetail extends Model
{
    protected $table = 'signupDetails';

    protected $fillable = [ 'signupId', 'courseId', 
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


}
