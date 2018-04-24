<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherGroup extends Model
{
	protected $table = 'teacherGroups';
	
	protected $fillable = [ 
        'centerId','name','description',
        'removed','active','updatedBy','ps'
    
    ];
       
    public static function init()
	{
		return [
           
            'name' =>'',
            'description' =>'',

			'active' => 1,
			'removed' => 0,
			
			'ps' => ''

		];
	}  
	


    public function center() 
	{
		return $this->hasOne('App\Center', 'id' ,'centerId');
	}

	public function teachers()
    {
        return $this->belongsToMany(Teacher::class,'group_teacher','group_id','teacher_id');
	}

	public function courses() 
	{
		return $this->hasMany('App\Course','teacherGroupId');
	}

	public function getName()
    {
        return $this->name;
    }
	
	public function getTeacherNames()
	{
		$teacherNames='';
		if(count($this->teachers)){
			
			$teacherNames=join(',',$this->teachers->pluck('user.profile.fullname')->toArray() );
		}

		$this->teacherNames=$teacherNames;
		return $teacherNames;

		
	}

	public function getSummary()
    {
        return '';
    }

	public function addTeachers(array $teacherIds)
	{
		$this->teachers()->attach($teacherIds);
	}

	public function removeTeachers(array $teacherIds)
	{
		
		$this->teachers()->detach($teacherIds);
	}

	


    public function  toOption()
    {
        return [ 'text' => $this->name ,  'value' => $this->id , 'group' => true ];
    }


}
