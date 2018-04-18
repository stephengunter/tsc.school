<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $primaryKey = 'userId';

    protected $fillable = [ 
        'experiences','education','certificate','specialty',
        'job','jobtitle','description','reviewed','reviewedBy',
       
        'active', 'removed', 'updatedBy', 'joinDate','ps'
    
    ];
       
    public static function init()
	{
		return [
            'group' => 0,
            'experiences' =>'',
            'education' =>'',
            'certificate' =>'',
            'specialty' =>'',

            'job' =>'',
            'jobtitle' =>'',
            'description' =>'',
            'reviewed' =>0,
            'reviewedBy' =>'',

            'joinDate' =>'1900-1-1',


			'active' => 1,
            'removed' => 0,

            'ps' => '',
            
            'wage' => 0,
            'account' => ''

		];
    }  

    public function setexperiencesAttribute($value) 
	{

		$this->attributes['experiences'] = str_replace("\n",'<br>',$value);
	}
    
    public function user()
    {
        return $this->belongsTo('App\User','userId');
    }

    public function centers()
    {
        return $this->belongsToMany(Center::class,'center_teacher','teacher_id','center_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class,'course_teacher','teacher_id','course_id');
    }

    public function groups()
    {
        return $this->belongsToMany(TeacherGroup::class,'group_teacher','teacher_id','group_id');
    }

    public function addRole()
    {
        $this->user->addRole(Role::teacherRoleName());
    }

    public function removeRole()
    {
        $this->user->removeRole(Role::teacherRoleName());
    }

    public function getWage()
    {
        $wage= $this->user->wages->first();
        return $wage;
    }

    public function setWage($values)
    { 
        $wage= $this->getWage();
        if($wage) $wage->update($values);
        else $this->user->wages()->save(new Wage($values));

        
    }

    public function  toOption()
    {
        return [ 'text' => $this->user->profile->fullname ,  'value' => $this->userId , 'group' => false ];
    }

}
