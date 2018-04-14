<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LessonMember extends Model
{
    protected $table = 'lessonMembers';

    protected $fillable = [  'lessonId', 'userId', 
        'role', 'absence' , 'ps', 'updatedBy'
    ];

    public static function init()
	{
		return [
			'lessonId' => 0,
			'userId' => 0,
			'role' => '',
			'absence' => 0,
			'ps' => '',
			'updatedBy' => '',

		];
    }	
    
    public function lesson() 
	{
		return $this->hasOne('App\Lesson', 'id' ,'lessonId');
	}
}
