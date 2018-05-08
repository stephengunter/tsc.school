<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    protected $primaryKey = 'userId';
    
    protected $fillable = [  'active', 'removed', 
        'joinDate', 'updatedBy', 'time' , 'ps'
    ];

    public static function init()
	{
		return [
			'active' => 0,
			'removed' => 0,
			'joinDate' => '',
            'updatedBy' => '',
            'time' => '',
            'ps' => ''

		];
    }	
    
    public function courses()
    {
        return $this->belongsToMany(Course::class,'course_volunteer','volunteer_id','course_id');
    }

    public function centers()
    {
        return $this->belongsToMany(Center::class,'center_volunteer','volunteer_id','center_id');
    }

    public function weekdays()
    {
        return $this->belongsToMany(Weekday::class,'volunteer_weekday','volunteer_id','weekday_id');
    }
	
	public function user()
    {
        return $this->belongsTo('App\User','userId');
	}
	
	public function addRole()
    {
        $this->user->addRole(Role::volunteerRoleName());
    }

    public function removeRole()
    {
        $this->user->removeRole(Role::volunteerRoleName());
	}
	
	public function  toOption()
    {
        return [ 'text' => $this->user->profile->fullname ,  'value' => $this->userId  ];
    }
}
