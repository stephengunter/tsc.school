<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $primaryKey = 'userId';

    protected $fillable = [ 
        'experiences','education','certificate','specialty',
        'job','jobtitle','description','reviewed','reviewedBy',
        'wageId' , 'pay' , 
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
            
            'accountNumber' => '',

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

    public function wage() 
	{
        return $this->hasOne('App\Wage', 'id' ,'wageId');
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

    public function getName()
    {
        return $this->user->profile->fullname;
    }

    public function getSummary()
    {
        return $this->jobtitle;
    }

    public function getAccount()
    {
        $account= $this->user->accounts->first();
        return $account;
    }

    public function setAccount($values)
    { 
        $account= $this->getAccount();
        if($account) $account->update($values);
        else $this->user->accounts()->save(new Account($values));

        
    }

    public function  toOption()
    {
        return [ 'text' => $this->user->profile->fullname ,  'value' => $this->userId , 'group' => false ];
    }

}
