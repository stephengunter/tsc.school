<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherGroup extends Model
{
    protected $table = 'teacherGroups';

    public function center() 
	{
		return $this->hasOne('App\Center', 'id' ,'centerId');
	}

	public function teachers()
    {
        return $this->belongsToMany(Teacher::class,'group_teacher','group_id','teacher_id');
	}
}
