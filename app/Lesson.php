<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Role;
use Carbon\Carbon;
use App\Core\Helper;

class Lesson extends Model
{
	public static $snakeAttributes = false;
	
    protected $fillable = [  'courseId', 'status', 
        'date','title', 'content','materials',
		'on','off', 'location', 'ps' , 'updatedBy',
		'reviewed','reviewedBy',
    ];

    public static function init($year)
	{
		return [
			'courseId' => 0,
			'status' => 0,
			'on' => 0,
            'off' => 0,
            'content' => '',
			'location' => '',
			'updatedBy' => '',

		];
    }	
    
    public function course() 
	{
		return $this->hasOne('App\Course', 'id' ,'courseId');
    }
    
    public function members() 
	{
		return $this->hasMany('App\LessonMember','lessonId');
	}

	public function getCenter()
	{
        return $this->course->center;
    }

	public function getTeacherIds()
	{
		return $this->members()->where('role',Role::teacherRoleName())
									->pluck('userId')->toArray();

	}
	
	public function getVolunteerIds()
	{
		return $this->members()->where('role',Role::volunteerRoleName())
									->pluck('userId')->toArray();

	}

	public function getStudentIds()
	{
		return $this->members()->where('role',Role::studentRoleName())
									->pluck('userId')->toArray();

	}

	public function getHours()
	{
		$on=new \Carbon\Carbon('2018-1-31 ' . Helper::toTimeString($this->on));
		$off=new \Carbon\Carbon('2018-1-31 ' . Helper::toTimeString($this->off));

		$minutes = $off->diffInMinutes($on);
		return $minutes/60;
	}
	
	public function loadViewModel()
    {
		$this->timeString();
		$this->hours=$this->getHours();
		$this->course->fullName();
		$this->studentCount=$this->members()->where('role',Role::studentRoleName())->count();
		$this->studentAttended=$this->members()->where('role',Role::studentRoleName())
												->where('absence',true)->count();  

		
		
		
	}

	public function timeString()
	{
		$timeString= Helper::toTimeString($this->on) . '~' . Helper::toTimeString($this->off);
		$this->timeString=$timeString;
		return $timeString;
	}

	

}
