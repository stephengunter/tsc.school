<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\LessonMember;
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
			'date' => '',
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
	
	public function getMembersByRole($roleName)
	{
        return $this->members()->where('role',$roleName);
	}

	
	
	public function deleteMembersByRole($roleName)
	{
		$ids=$this->getMembersByRole($roleName)->pluck('id')->toArray();
		
		LessonMember::destroy($ids);
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
	public function getMinutes()
	{
		$on=new Carbon('2018-1-31 ' . Helper::toTimeString($this->on));
		$off=new Carbon('2018-1-31 ' . Helper::toTimeString($this->off));

		return $off->diffInMinutes($on);
	}
	

	public function getHours()
	{
		$minutes = $this->getMinutes();
		return $minutes/60;
	}
	
	public function loadViewModel()
    {
		$this->timeString();
		$this->hours=$this->getHours();
		$this->course->fullName();
		$this->studentCount=$this->getStudentCount();
		$this->studentAttended=$this->members()->where('role',Role::studentRoleName())
												->where('absence',true)->count();  
		
	}

	public function getStudentCount()
	{
		return $this->members()->where('role',Role::studentRoleName())->count();
	}

	public function timeString()
	{
		$timeString= Helper::toTimeString($this->on) . '~' . Helper::toTimeString($this->off);
		$this->timeString=$timeString;
		return $timeString;
	}

	public function isNightLesson()
	{
		$config=config('app.wage.night');
		$center=$this->getCenter();

		$nightTime=$config['west'];
		if($center->east)  $nightTime=$config['east'];

		return $this->on >= (int)$nightTime;
	}

	public function isBigLesson()
	{
		$studentCount = $this->getStudentCount();

		$config=config('app.wage.big');
		$center=$this->getCenter();

		$bigCount=$config['west'];
		if($center->east)  $bigCount=$config['east'];

		return $studentCount >= (int)$bigCount;
	}

	public function isHolidayLesson()
	{
		$date=new Carbon($this->date);
		
		if($date->isSaturday()) return true;
		if($date->isSunday()) return true;
		return false;
	}

}
