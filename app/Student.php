<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'status', 'userId', 'courseId' , 'score',
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
}
