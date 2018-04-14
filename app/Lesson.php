<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [  'courseId', 'status', 
        'date','title', 'content','materials',
        'on','off', 'classroom', 'ps' , 'updatedBy'
    ];

    public static function init($year)
	{
		return [
			'courseId' => 0,
			'status' => 0,
			'on' => 0,
            'off' => 0,
          
			'classroom' => '',
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
}
