<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Course;

class SignupDetail extends Model
{
    protected $table = 'signupDetails';

    protected $fillable = [ 'signupId', 'courseId', 
    'tuition' , 'cost' , 'updatedBy'  ];

    public static function init(Course $course)
	{
		return [
          
            'courseId' => $course->id,
			'tuition' => $course->tuition,
            'cost' => $course->cost,
            
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
}
